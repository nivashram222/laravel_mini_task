<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LowStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_products_below_default_threshold(): void
    {
        Product::factory()->create(['name' => 'Low A', 'stock_on_hand' => 3]);
        Product::factory()->create(['name' => 'Low B', 'stock_on_hand' => 0]);
        Product::factory()->create(['name' => 'OK', 'stock_on_hand' => 50]);

        $response = $this->getJson('/api/products/low-stock');

        $response->assertStatus(200)
                 ->assertJson(['success' => true, 'threshold' => 10])
                 ->assertJsonCount(2, 'data');
    }

    public function test_respects_custom_threshold_parameter(): void
    {
        Product::factory()->create(['stock_on_hand' => 3]);
        Product::factory()->create(['stock_on_hand' => 15]);
        Product::factory()->create(['stock_on_hand' => 25]);

        // Threshold = 5 → only first product
        $response = $this->getJson('/api/products/low-stock?threshold=5');
        $response->assertJsonCount(1, 'data');

        // Threshold = 20 → first two products
        $response2 = $this->getJson('/api/products/low-stock?threshold=20');
        $response2->assertJsonCount(2, 'data');
    }

    public function test_returns_empty_when_all_stocked(): void
    {
        Product::factory()->create(['stock_on_hand' => 100]);
        Product::factory()->create(['stock_on_hand' => 200]);

        $response = $this->getJson('/api/products/low-stock?threshold=5');

        $response->assertStatus(200)
                 ->assertJsonCount(0, 'data');
    }

    public function test_includes_out_of_stock_products(): void
    {
        Product::factory()->create(['name' => 'OOS', 'stock_on_hand' => 0]);

        $response = $this->getJson('/api/products/low-stock?threshold=5');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonPath('data.0.name', 'OOS');
    }
}
