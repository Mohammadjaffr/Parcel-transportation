<?php

namespace App\Repositories;
use App\Interfaces\RepositoriesInterface;
use App\Models\Otp;
use Illuminate\Support\Facades\DB;

class OtpRepository implements RepositoriesInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Retrieve all Otps with pagination.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Otp::paginate(10);
    }

    /**
     * Retrieve a Otp by ID.
     */
    public function getById($id): Otp
    {
        return Otp::findOrFail($id);
    }

    /**
     * Store a new Otp.
     */
    public function store(array $data): Otp
    {
        return Otp::create($data);
    }

    /**
     * Update an existing Otp.
     */
    public function update(array $data, $id): Otp
    {
        $Otp = Otp::findOrFail($id);
        $Otp->update($data);
        return $Otp;
    }

    /**
     * Delete a Otp by ID.
     */
    public function delete($id): bool
    {
        return Otp::where('id', $id)->delete() > 0;
    }
    public function findByPhone($phone)
    {
        return Otp::where('phone', $phone)->first();
    }
    public function verifyOTP($phone, $code)  {
        return Otp::where('phone', $phone)
        ->where('code', $code)
        ->where('is_used', false)
        ->where('expires_at', '>', now())
        ->first();
    }
    
}
