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
     * ملاحظة: تم إزالة تسقيط العمولة الفردي — العمولة الآن تُسقط على مستوى الرحلة (PassengerTrip)
     */
    public function updated(Passengers $passenger): void
    {
        // العمولة تُسقط الآن من PassengerTripController عند إنشاء/تعديل الرحلة
    }
}