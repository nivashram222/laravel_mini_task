<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['name' => 'John Doe', 'email' => 'john.doe@example.com'],
            ['name' => 'Jane Smith', 'email' => 'jane.smith@example.com'],
            ['name' => 'Alice Johnson', 'email' => 'alice.johnson@example.com'],
            ['name' => 'Bob Williams', 'email' => 'bob.williams@example.com'],
            ['name' => 'Charlie Brown', 'email' => 'charlie.brown@example.com'],
            ['name' => 'Diana Ross', 'email' => 'diana.ross@example.com'],
            ['name' => 'Edward King', 'email' => 'edward.king@example.com'],
            ['name' => 'Fiona Davis', 'email' => 'fiona.davis@example.com'],
            ['name' => 'George Miller', 'email' => 'george.miller@example.com'],
            ['name' => 'Hannah Wilson', 'email' => 'hannah.wilson@example.com'],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
