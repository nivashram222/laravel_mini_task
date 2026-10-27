<?php

namespace App\Repositories\Eloquent;

use App\Models\Customer;
use App\Repositories\Interfaces\CustomerRepositoryInterface;

class EloquentCustomerRepository implements CustomerRepositoryInterface
{
    public function findByEmail(string $email): ?Customer
    {
        return Customer::where('email', $email)->first();
    }

    public function firstOrCreate(array $attributes, array $values = []): Customer
    {
        return Customer::firstOrCreate($attributes, $values);
    }
}
