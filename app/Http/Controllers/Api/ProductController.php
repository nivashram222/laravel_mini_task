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

    public function index(): JsonResponse
    {
        $products = $this->productRepository->all();

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

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
