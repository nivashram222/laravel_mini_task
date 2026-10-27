<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_order_history_for_customer(): void
    {
        $customer = Customer::factory()->create(['email' => 'history@example.com']);
        $product = Product::factory()->create();

        $order = Order::create([
            'customer_id' => $customer->id,
            'subtotal' => 100.00,
            'tax' => 18.00,
            'grand_total' => 118.00,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 50.00,
            'tax_percentage' => 18.00,
            'line_total' => 118.00,
        ]);

        $response = $this->getJson('/api/customers/history@example.com/orders');

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJsonCount(1, 'data')
                 ->assertJsonPath('data.0.grand_total', '118.00');
    }

    public function test_returns_empty_for_unknown_email(): void
    {
        $response = $this->getJson('/api/customers/unknown@example.com/orders');

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJsonCount(0, 'data');
    }

    public function test_returns_multiple_orders_for_customer(): void
    {
        $customer = Customer::factory()->create(['email' => 'multi@example.com']);
        $product = Product::factory()->create();

        for ($i = 0; $i < 3; $i++) {
            $order = Order::create([
                'customer_id' => $customer->id,
                'subtotal' => 50.00,
                'tax' => 5.00,
                'grand_total' => 55.00,
            ]);
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => 1,
                'unit_price' => 50.00,
                'tax_percentage' => 10.00,
                'line_total' => 55.00,
            ]);
        }

        $response = $this->getJson('/api/customers/multi@example.com/orders');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_order_history_includes_product_details(): void
    {
        $customer = Customer::factory()->create(['email' => 'detail@example.com']);
        $product = Product::factory()->create(['name' => 'Test Widget', 'code' => 'TW-001']);

        $order = Order::create([
            'customer_id' => $customer->id,
            'subtotal' => 100.00,
            'tax' => 10.00,
            'grand_total' => 110.00,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 50.00,
            'tax_percentage' => 10.00,
            'line_total' => 110.00,
        ]);

        $response = $this->getJson('/api/customers/detail@example.com/orders');

        $response->assertStatus(200)
                 ->assertJsonPath('data.0.items.0.product.name', 'Test Widget')
                 ->assertJsonPath('data.0.items.0.product.code', 'TW-001');
    }
}
