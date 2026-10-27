<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
        protected OrderRepositoryInterface $orderRepository,
    ) {}

    /**
     * Create a new order.
     * POST /api/orders
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            $order = $this->orderService->createOrder(
                customerName: $request->validated('customer_name'),
                customerEmail: $request->validated('customer_email'),
                items: $request->validated('items'),
            );

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully.',
                'data' => $order,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order could not be created.',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Get a customer's order history by email.
     * GET /api/customers/{email}/orders
     */
    public function customerHistory(string $email): JsonResponse
    {
        $orders = $this->orderRepository->getByCustomerEmail($email);

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }
}
