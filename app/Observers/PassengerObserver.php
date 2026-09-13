<?php

namespace App\Observers;

use App\Models\Passengers;
use App\Services\CashTransactionService;

class PassengerObserver
{
    public function __construct(
        protected CashTransactionService $cashTransactionService
    ) {}
    /**
     * معالجة حدث تحديث الراكب
     */
    public function updated(Passengers $passenger): void
    {
        if ($passenger->isDirty('status') && $passenger->status === 'completed' && !empty($passenger->trip_id)) {
            $this->cashTransactionService->recordPassengerCommission($passenger);
        }
    }

   
}