<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository,
    ) {}

    /**
     * Get all products.
     * GET /api/products
     */
    public function index(): JsonResponse
    {
        $products = $this->productRepository->all();

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Get products below the low-stock threshold.
     * GET /api/products/low-stock?threshold=10
     */
    public function lowStock(Request $request): JsonResponse
    {
        $threshold = (int) $request->query('threshold', config('inventory.low_stock_threshold', 10));

        $products = $this->productRepository->getLowStock($threshold);

        return response()->json([
            'success' => true,
            'threshold' => $threshold,
            'data' => $products,
        ]);
    }
}
