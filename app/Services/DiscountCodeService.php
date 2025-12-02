<?php

namespace App\Services;

use App\Models\DiscountCode;
use App\Repositories\DiscountCodeRepository;

class DiscountCodeService{
    public function __construct(private DiscountCodeRepository $discountCodeRepository)
    {
        //
    }

    
}