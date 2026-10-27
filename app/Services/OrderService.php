<?php

namespace App\Services;

use App\Jobs\OrderConfirmationJob;
use App\Models\Order;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        protected CustomerRepositoryInterface $customerRepository,
        protected OrderRepositoryInterface $orderRepository,
    ) {}

    public function createOrder(string $customerName, string $customerEmail, array $items): Order
    {
        return DB::transaction(function () use ($customerName, $customerEmail, $items) {
            $customer = $this->customerRepository->firstOrCreate(
                ['email' => $customerEmail],
                ['name' => $customerName]
            );

            $subtotal = 0;
            $totalTax = 0;
            $orderLines = [];

            foreach ($items as $item) {
                $product = $this->productRepository->findForUpdate($item['product_id']);

                if (!$product) {
                    throw ValidationException::withMessages([
                        'items' => ["Product with ID {$item['product_id']} not found."],
                    ]);
                }

                if ($product->stock_on_hand < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => [
                            "Insufficient stock for \"{$product->name}\" (Code: {$product->code}). " .
                            "Requested: {$item['quantity']}, Available: {$product->stock_on_hand}."
                        ],
                    ]);
                }

                $lineSubtotal = $product->price_per_unit * $item['quantity'];
                $lineTax = round($lineSubtotal * ($product->tax_percentage / 100), 2);
                $lineTotal = round($lineSubtotal + $lineTax, 2);

                $subtotal += $lineSubtotal;
                $totalTax += $lineTax;

                $orderLines[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price_per_unit,
                    'tax_percentage' => $product->tax_percentage,
                    'line_total' => $lineTotal,
                ];
            }

            $grandTotal = round($subtotal + $totalTax, 2);

            $order = $this->orderRepository->create([
                'customer_id' => $customer->id,
                'subtotal' => round($subtotal, 2),
                'tax' => round($totalTax, 2),
                'grand_total' => $grandTotal,
            ]);

            foreach ($orderLines as $line) {
                $this->orderRepository->createItem($order, [
                    'product_id' => $line['product']->id,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'tax_percentage' => $line['tax_percentage'],
                    'line_total' => $line['line_total'],
                ]);

                $this->productRepository->decrementStock($line['product'], $line['quantity']);
            }

            $order->load(['items.product', 'customer']);

            OrderConfirmationJob::dispatch($order);

            return $order;
        });
    }
}
