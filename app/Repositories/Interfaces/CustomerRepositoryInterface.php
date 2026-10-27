<?php

namespace App\Repositories\Interfaces;

use App\Models\Customer;

interface CustomerRepositoryInterface
{
    public function findByEmail(string $email): ?Customer;

    public function firstOrCreate(array $attributes, array $values = []): Customer;
}
