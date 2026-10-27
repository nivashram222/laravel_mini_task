<?php

namespace App\Repositories\Interfaces;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryInterface
{
    public function create(array $data): Order;

    public function createItem(Order $order, array $data): void;

    public function getByCustomerEmail(string $email): Collection;
}
