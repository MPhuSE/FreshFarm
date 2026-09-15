<?php

namespace App\Jobs;

use App\Services\CheckoutService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CancelUnpaidOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    public function handle(CheckoutService $checkoutService)
    {
        //gọi Service để thực hiện giao dịch hủy đơn và hoàn kho an toàn
        $checkoutService->cancelOrder($this->orderId);
    }
}