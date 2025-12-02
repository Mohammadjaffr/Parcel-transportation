<?php 

namespace App\Repositories;

use App\Interfaces\RepositoriesInterface;
use App\Models\Driver;

class DriverRepository implements RepositoriesInterface
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
        return Driver::paginate(10);
    }

    public function getById($id): Driver
    {
        return Driver::findOrFail($id);
    }

    public function store(array $data): Driver
    {
        return Driver::create($data);
    }

    public function update(array $data, $id): Driver
    {
        $Driver = Driver::findOrFail($id);
        $Driver->update($data);
        return $Driver;
    }
    public function delete($id): bool
    {
        return Driver::where('id', $id)->delete() > 0;
    }

    public function findByPhone($phone)
    {
        return Driver::where('phone', $phone)->first();
    }

}