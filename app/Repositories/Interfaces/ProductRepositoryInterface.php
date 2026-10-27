<?php

namespace App\Repositories\Interfaces;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    public function all(): Collection;

    public function find(int $id): ?Product;

    public function findByCode(string $code): ?Product;

    public function getLowStock(int $threshold): Collection;

    /**
     * Find a product by ID with a pessimistic lock for update.
     */
    public function findForUpdate(int $id): ?Product;

    public function decrementStock(Product $product, int $quantity): void;
}
