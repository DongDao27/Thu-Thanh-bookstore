<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\PaymentReceipt;
use App\Services\StorefrontSnapshot;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StorefrontObserver
{
    public function saved(Model $model): void
    {
        // Bestseller aggregates depend on payment status and receipt allocation,
        // not recipient details, fulfillment updates or receipt verification times.
        if ($model instanceof Order && ! $model->isDirty('payment_status')) {
            return;
        }
        if ($model instanceof PaymentReceipt && ! $model->isDirty(['allocation', 'order_id'])) {
            return;
        }
        $this->invalidate();
    }

    private function invalidate(): void
    {
        if (DB::transactionLevel() > 0) {
            DB::afterCommit(fn () => StorefrontSnapshot::invalidate());
        } else {
            StorefrontSnapshot::invalidate();
        }
    }

    public function deleted(Model $model): void
    {
        $this->invalidate();
    }
}
