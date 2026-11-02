<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Console\Command;

class CancelExpiredPayments extends Command
{
    protected $signature = 'iyma:cancel-expired-payments';

    protected $description = 'Cancel orders past payment timeout without receipt';

    public function handle(OrderService $orders): int
    {
        $count = 0;
        Order::query()
            ->where('status', OrderStatus::PendingPayment)
            ->whereNotNull('payment_due_at')
            ->where('payment_due_at', '<', now())
            ->each(function (Order $order) use ($orders, &$count) {
                $orders->changeStatus($order, OrderStatus::Cancelled, 'system', null, 'انتهت مهلة الدفع');
                $count++;
            });

        $this->info("Cancelled {$count} orders");

        return self::SUCCESS;
    }
}
