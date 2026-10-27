<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Jobs\OrderConfirmationJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OrderCreationTest extends TestCase
{
    use RefreshDatabase;

    // ──────────────────────────────────────────────
    //  Happy Path
    // ──────────────────────────────────────────────

    public function test_order_created_successfully_with_valid_data(): void
    {
        Queue::fake();

        $product1 = Product::factory()->create([
            'name' => 'Widget A',
            'code' => 'WA-001',
            'price_per_unit' => 100.00,
            'tax_percentage' => 10.00,
            'stock_on_hand' => 50,
        ]);

        $product2 = Product::factory()->create([
            'name' => 'Widget B',
            'code' => 'WB-001',
            'price_per_unit' => 200.00,
            'tax_percentage' => 18.00,
            'stock_on_hand' => 30,
        ]);

        $response = $this->postJson('/api/orders', [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'items' => [
                ['product_id' => $product1->id, 'quantity' => 2],
                ['product_id' => $product2->id, 'quantity' => 3],
            ],
        ]);

        $response->assertStatus(201)
                 ->assertJson(['success' => true]);

        // Verify customer created
        $this->assertDatabaseHas('customers', [
            'email' => 'john@example.com',
            'name' => 'John Doe',
        ]);

        // Verify order created with correct totals
        // Product 1: 100 * 2 = 200 (subtotal), 200 * 10% = 20 (tax)
        // Product 2: 200 * 3 = 600 (subtotal), 600 * 18% = 108 (tax)
        // Total subtotal = 800, total tax = 128, grand total = 928
        $this->assertDatabaseHas('orders', [
            'subtotal' => 800.00,
            'tax' => 128.00,
            'grand_total' => 928.00,
        ]);

        // Verify stock deducted
        $this->assertEquals(48, $product1->fresh()->stock_on_hand);
        $this->assertEquals(27, $product2->fresh()->stock_on_hand);

        // Verify order items
        $this->assertDatabaseCount('order_items', 2);

        // Verify queued job dispatched
        Queue::assertPushed(OrderConfirmationJob::class);
    }

    public function test_existing_customer_is_reused_by_email(): void
    {
        Queue::fake();

        $customer = Customer::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
        ]);

        $product = Product::factory()->create(['stock_on_hand' => 10]);

        $this->postJson('/api/orders', [
            'customer_name' => 'Jane Updated',
            'customer_email' => 'jane@example.com',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ]);

        // Customer should not be duplicated
        $this->assertDatabaseCount('customers', 1);

        // Original name should be preserved (firstOrCreate)
        $this->assertEquals('Jane Smith', $customer->fresh()->name);
    }

    // ──────────────────────────────────────────────
    //  Validation Edge Cases
    // ──────────────────────────────────────────────

    public function test_order_fails_with_insufficient_stock(): void
    {
        $product = Product::factory()->create([
            'name' => 'Rare Item',
            'code' => 'RI-001',
            'stock_on_hand' => 2,
        ]);

        $response = $this->postJson('/api/orders', [
            'customer_name' => 'Buyer',
            'customer_email' => 'buyer@example.com',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 5],
            ],
        ]);

        $response->assertStatus(422);

        // Stock should remain unchanged
        $this->assertEquals(2, $product->fresh()->stock_on_hand);

        // No order should be created
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_order_fails_with_zero_stock(): void
    {
        $product = Product::factory()->outOfStock()->create();

        $response = $this->postJson('/api/orders', [
            'customer_name' => 'Buyer',
            'customer_email' => 'buyer@example.com',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_order_fails_without_required_fields(): void
    {
        $response = $this->postJson('/api/orders', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['customer_name', 'customer_email', 'items']);
    }

    public function test_order_fails_with_invalid_product_id(): void
    {
        $response = $this->postJson('/api/orders', [
            'customer_name' => 'Test',
            'customer_email' => 'test@example.com',
            'items' => [
                ['product_id' => 99999, 'quantity' => 1],
            ],
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['items.0.product_id']);
    }

    public function test_order_fails_with_zero_quantity(): void
    {
        $product = Product::factory()->create(['stock_on_hand' => 10]);

        $response = $this->postJson('/api/orders', [
            'customer_name' => 'Test',
            'customer_email' => 'test@example.com',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 0],
            ],
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['items.0.quantity']);
    }

    // ──────────────────────────────────────────────
    //  Concurrency — No Overselling
    // ──────────────────────────────────────────────

    public function test_concurrent_orders_prevent_overselling(): void
    {
        Queue::fake();

        // Only 1 unit available
        $product = Product::factory()->create([
            'name' => 'Last Item',
            'code' => 'LI-001',
            'stock_on_hand' => 1,
        ]);

        $payload = [
            'customer_name' => 'Racer',
            'customer_email' => 'racer@example.com',
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ];

        // First order should succeed
        $response1 = $this->postJson('/api/orders', $payload);

        // Second order should fail — stock is now 0
        $response2 = $this->postJson('/api/orders', $payload);

        // Exactly one should succeed
        $this->assertTrue(
            ($response1->status() === 201 && $response2->status() === 422) ||
            ($response1->status() === 422 && $response2->status() === 201),
            'Exactly one of the two orders should succeed.'
        );

        // Stock should be zero
        $this->assertEquals(0, $product->fresh()->stock_on_hand);

        // Only one order should exist
        $this->assertDatabaseCount('orders', 1);
    }
}
