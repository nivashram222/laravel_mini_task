<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function all(): Collection
    {
        return Product::orderBy('name')->get();
    }

    public function find(int $id): ?Product
    {
        return Product::find($id);
    }

    public function findByCode(string $code): ?Product
    {
        return Product::where('code', $code)->first();
    }

    public function getLowStock(int $threshold): Collection
    {
        return Product::where('stock_on_hand', '<', $threshold)
            ->orderBy('stock_on_hand')
            ->get();
    }

    /**
     * Find a product with a pessimistic lock (SELECT ... FOR UPDATE).
     * Must be called within a DB transaction.
     */
    public function findForUpdate(int $id): ?Product
    {
        return Product::lockForUpdate()->find($id);
    }

    public function decrementStock(Product $product, int $quantity): void
    {
        $product->decrement('stock_on_hand', $quantity);
    }
}
