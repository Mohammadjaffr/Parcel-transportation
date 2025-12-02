<?php 

namespace App\Repositories;

use App\Interfaces\RepositoriesInterface;
use App\Models\UserDevice;

class UserDeviceRepository implements RepositoriesInterface
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
        return UserDevice::paginate(10);
    }

    public function getById($id): UserDevice
    {
        return UserDevice::findOrFail($id);
    }

    public function store(array $data): UserDevice
    {
        return UserDevice::create($data);
    }

    public function update(array $data, $id): UserDevice
    {
        $UserDevice = UserDevice::findOrFail($id);
        $UserDevice->update($data);
        return $UserDevice;
    }
    public function delete($id): bool
    {
        return UserDevice::where('id', $id)->delete() > 0;
    }

    public function deleteByTokenID($token_id): bool
    {
        return UserDevice::where('token_id', $token_id)->delete() > 0;
    }

    public function updateOrCreate($user_id,$device_token,$token_id)
    {
        return UserDevice::updateOrCreate(
        [
            'user_id' => $user_id,
            'device_token' => $device_token
        ],
        [
            'token_id' => $token_id,
        ]);
    }
    

}