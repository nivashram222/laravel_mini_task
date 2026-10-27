<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Models\Customer;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function createItem(Order $order, array $data): void
    {
        $order->items()->create($data);
    }

    public function getByCustomerEmail(string $email): Collection
    {
        $customer = Customer::where('email', $email)->first();

        if (!$customer) {
            return new Collection();
        }

        return $customer->orders()
            ->with(['items.product:id,name,code'])
            ->orderByDesc('created_at')
            ->get();
    }
}
