<?php 

namespace App\Repositories;

use App\Interfaces\RepositoriesInterface;
use App\Models\DiscountCode;

class DiscountCodeRepository implements RepositoriesInterface
{
/**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function index(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return DiscountCode::paginate(10);
    }

    public function getById($id): DiscountCode
    {
        return DiscountCode::findOrFail($id);
    }

    public function store(array $data): DiscountCode
    {
        return DiscountCode::create($data);
    }

    public function update(array $data, $id): DiscountCode
    {
        $DiscountCode = DiscountCode::findOrFail($id);
        $DiscountCode->update($data);
        return $DiscountCode;
    }
    public function delete($id): bool
    {
        return DiscountCode::where('id', $id)->delete() > 0;
    }
    public function getDiscountCodeByCode(string $code)
    {
        return DiscountCode::where('code', $code)->first();
    }
    public function isCouponActive(DiscountCode $coupon): bool
    {
        return $coupon->is_active;
    }
    public function hasUsageLimitAvailable(DiscountCode $coupon): bool
    {
        return $coupon->current_uses < $coupon->max_uses;
    }
    public function incrementUsage(DiscountCode $coupon): void
    {
        $coupon->increment('current_uses');
    }

}