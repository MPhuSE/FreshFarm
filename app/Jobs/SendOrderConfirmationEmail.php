<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendOrderConfirmationEmail implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order)
    {
    }

    public function handle(): void
    {
        // Simulate sending email to Queue
        Log::info('Da gui email xac nhan don hang qua Queue.', [
            'order_id' => $this->order->id,
            'order_code' => $this->order->order_code,
            'email' => $this->order->email ?? 'no-email@example.com'
        ]);
        
        // Thêm sleep để mô phỏng tác vụ nặng
        sleep(2);
    }
}
