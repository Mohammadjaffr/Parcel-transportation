<?php 

namespace App\Repositories;

use App\Interfaces\RepositoriesInterface;
use App\Models\Service;

class ServiceRepository implements RepositoriesInterface
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
        return Service::paginate(10);
    }

    public function getById($id): Service
    {
        return Service::findOrFail($id);
    }

    public function store(array $data): Service
    {
        return Service::create($data);
    }

    public function update(array $data, $id): Service
    {
        $Service = Service::findOrFail($id);
        $Service->update($data);
        return $Service;
    }
    public function delete($id): bool
    {
        return Service::where('id', $id)->delete() > 0;
    }

}