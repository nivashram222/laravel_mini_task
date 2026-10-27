<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Specific well-known products for a realistic retail catalog
        $products = [
            ['name' => 'Classic Sunglasses', 'code' => 'PRD-0001', 'price_per_unit' => 120.00, 'tax_percentage' => 18.00, 'stock_on_hand' => 50],
            ['name' => 'Leather Wallet', 'code' => 'PRD-0002', 'price_per_unit' => 85.00, 'tax_percentage' => 12.00, 'stock_on_hand' => 35],
            ['name' => 'Wireless Earbuds', 'code' => 'PRD-0003', 'price_per_unit' => 250.00, 'tax_percentage' => 18.00, 'stock_on_hand' => 3],
            ['name' => 'Cotton T-Shirt', 'code' => 'PRD-0004', 'price_per_unit' => 45.00, 'tax_percentage' => 5.00, 'stock_on_hand' => 100],
            ['name' => 'Sports Watch', 'code' => 'PRD-0005', 'price_per_unit' => 350.00, 'tax_percentage' => 18.00, 'stock_on_hand' => 2],
            ['name' => 'Notebook Set', 'code' => 'PRD-0006', 'price_per_unit' => 25.00, 'tax_percentage' => 12.00, 'stock_on_hand' => 80],
            ['name' => 'Canvas Backpack', 'code' => 'PRD-0007', 'price_per_unit' => 180.00, 'tax_percentage' => 18.00, 'stock_on_hand' => 4],
            ['name' => 'Ceramic Mug', 'code' => 'PRD-0008', 'price_per_unit' => 15.00, 'tax_percentage' => 12.00, 'stock_on_hand' => 200],
            ['name' => 'USB-C Hub', 'code' => 'PRD-0009', 'price_per_unit' => 75.00, 'tax_percentage' => 18.00, 'stock_on_hand' => 1],
            ['name' => 'Desk Lamp', 'code' => 'PRD-0010', 'price_per_unit' => 95.00, 'tax_percentage' => 18.00, 'stock_on_hand' => 25],
            ['name' => 'Power Bank 10000mAh', 'code' => 'PRD-0011', 'price_per_unit' => 60.00, 'tax_percentage' => 18.00, 'stock_on_hand' => 0],
            ['name' => 'Yoga Mat', 'code' => 'PRD-0012', 'price_per_unit' => 40.00, 'tax_percentage' => 5.00, 'stock_on_hand' => 15],
            ['name' => 'Bluetooth Speaker', 'code' => 'PRD-0013', 'price_per_unit' => 199.00, 'tax_percentage' => 18.00, 'stock_on_hand' => 3],
            ['name' => 'Stainless Steel Bottle', 'code' => 'PRD-0014', 'price_per_unit' => 30.00, 'tax_percentage' => 12.00, 'stock_on_hand' => 60],
            ['name' => 'Phone Case Premium', 'code' => 'PRD-0015', 'price_per_unit' => 35.00, 'tax_percentage' => 18.00, 'stock_on_hand' => 90],
            ['name' => 'Mechanical Keyboard', 'code' => 'PRD-0016', 'price_per_unit' => 320.00, 'tax_percentage' => 18.00, 'stock_on_hand' => 10],
            ['name' => 'Running Shoes', 'code' => 'PRD-0017', 'price_per_unit' => 280.00, 'tax_percentage' => 5.00, 'stock_on_hand' => 2],
            ['name' => 'Scented Candle Set', 'code' => 'PRD-0018', 'price_per_unit' => 55.00, 'tax_percentage' => 28.00, 'stock_on_hand' => 40],
            ['name' => 'Portable Charger', 'code' => 'PRD-0019', 'price_per_unit' => 45.00, 'tax_percentage' => 18.00, 'stock_on_hand' => 0],
            ['name' => 'Graphic Novel Collection', 'code' => 'PRD-0020', 'price_per_unit' => 150.00, 'tax_percentage' => 0.00, 'stock_on_hand' => 12],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
