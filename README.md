# Store Order & Inventory Mini-System

A Laravel application that lets a retail counter record customer orders against a product catalog and keep stock in sync.

## Tech Stack

- **Framework:** Laravel 13.x (PHP 8.3)
- **Database:** MySQL
- **Architecture:** Repository Pattern (Interfaces → Eloquent implementations, bound via ServiceProvider)
- **Queue:** Database driver (with `OrderConfirmationJob`)
- **Frontend:** Blade + Vanilla JS + CSS (dark-themed UI)

## Setup Instructions

### Prerequisites
- PHP 8.3+ with `pdo_mysql` extension
- Composer 2.x
- MySQL 8.x
- Node.js (optional, only if modifying frontend assets)

### Installation

```bash
# Clone the repository
git clone <repo-url> && cd <repo-name>

# Install dependencies
composer install

# Copy environment config
cp .env.example .env

# Set your database credentials in .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=ramnivash_task
# DB_USERNAME=root
# DB_PASSWORD=<your-password>

# Generate application key
php artisan key:generate

# Create the database (if not already created)
mysql -u root -p -e "CREATE DATABASE ramnivash_task;"

# Run migrations and seed
php artisan migrate --seed

# Start the server
php artisan serve
```

Visit **http://localhost:8000/billing** to use the billing interface.

## Architecture & Design Decisions

### Repository Pattern
All database operations go through repository interfaces, making it easy to swap out implementations (e.g., from Eloquent to an API-based repository).

```
app/Repositories/
├── Interfaces/
│   ├── ProductRepositoryInterface.php
│   ├── CustomerRepositoryInterface.php
│   └── OrderRepositoryInterface.php
└── Eloquent/
    ├── EloquentProductRepository.php
    ├── EloquentCustomerRepository.php
    └── EloquentOrderRepository.php
```

Bindings are registered in `App\Providers\RepositoryServiceProvider`.

### Concurrency-Safe Stock Deduction
The `OrderService::createOrder()` method uses a **DB transaction with pessimistic locking** (`lockForUpdate()`) to prevent overselling under concurrent requests. If two orders arrive simultaneously for the last unit, exactly one succeeds and the other receives a 422 error.

### Queued Job
`OrderConfirmationJob` is dispatched asynchronously on order creation. It logs a simulated confirmation email to `storage/logs/laravel.log`. No real SMTP is configured.

To process queued jobs:
```bash
php artisan queue:work
```

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/api/orders` | Create a new order |
| `GET` | `/api/customers/{email}/orders` | Fetch order history by email |
| `GET` | `/api/products` | List all products |
| `GET` | `/api/products/low-stock?threshold=10` | Products below stock threshold |

### Create Order — Request Body
```json
{
  "customer_name": "John Doe",
  "customer_email": "john@example.com",
  "items": [
    { "product_id": 1, "quantity": 2 },
    { "product_id": 3, "quantity": 1 }
  ]
}
```

### Create Order — Response (201)
```json
{
  "success": true,
  "message": "Order created successfully.",
  "data": {
    "id": 1,
    "customer_id": 1,
    "subtotal": "370.00",
    "tax": "58.10",
    "grand_total": "428.10",
    "items": [...],
    "customer": {...}
  }
}
```

## Running Tests

```bash
# Create test database
mysql -u root -p -e "CREATE DATABASE ramnivash_task_testing;"

# Run all tests
php artisan test

# Run specific test file
php artisan test --filter=OrderCreationTest
php artisan test --filter=OrderHistoryTest
php artisan test --filter=LowStockTest
```

### Test Coverage
- **OrderCreationTest** — Happy path, existing customer reuse, insufficient stock, zero stock, missing fields, invalid product, zero quantity, concurrent overselling prevention
- **OrderHistoryTest** — Single order, empty results, multiple orders, product detail inclusion
- **LowStockTest** — Default threshold, custom threshold, all-stocked, out-of-stock inclusion

## Assumptions
1. **No authentication** — The API and billing page are publicly accessible (no Sanctum token required) since this is a retail counter application.
2. **Customer lookup by email** — Email is the unique identifier. If a customer with the same email already exists, the existing record is reused (name is not updated).
3. **Tax computation** — Tax is computed per product line: `(unit_price × quantity) × (tax_percentage / 100)`. Each product has its own tax rate.
4. **Stock threshold** — Default low-stock threshold is 10 units, configurable via `?threshold=` query parameter or `LOW_STOCK_THRESHOLD` env variable.
5. **Currency** — All monetary values are in Indian Rupees (₹) for display purposes; stored as decimal values in the database.
6. **Order confirmation** — Uses `Log::info()` instead of real email delivery. Check `storage/logs/laravel.log` for confirmation entries.

## Project Structure (Key Files)

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   │   ├── OrderController.php
│   │   │   └── ProductController.php
│   │   └── BillingController.php
│   └── Requests/
│       └── StoreOrderRequest.php
├── Jobs/
│   └── OrderConfirmationJob.php
├── Models/
│   ├── Product.php
│   ├── Customer.php
│   ├── Order.php
│   └── OrderItem.php
├── Providers/
│   └── RepositoryServiceProvider.php
├── Repositories/
│   ├── Interfaces/
│   └── Eloquent/
└── Services/
    └── OrderService.php

database/
├── migrations/
├── factories/
└── seeders/

tests/Feature/
├── OrderCreationTest.php
├── OrderHistoryTest.php
└── LowStockTest.php

resources/views/billing/
└── index.blade.php
```
