<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OrderConfirmationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Order $order
    ) {}

    public function handle(): void
    {
        $this->order->load('customer');

        Log::info('Order Confirmation Email Sent', [
            'order_id' => $this->order->id,
            'customer_name' => $this->order->customer->name,
            'customer_email' => $this->order->customer->email,
            'grand_total' => $this->order->grand_total,
            'message' => "Dear {$this->order->customer->name}, your order #{$this->order->id} "
                . "totalling \${$this->order->grand_total} has been confirmed. Thank you for shopping with us!",
        ]);
    }
}
