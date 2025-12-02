<?php 

namespace App\Repositories;

use App\Interfaces\RepositoriesInterface;
use App\Models\Request;

class RequestRepository implements RepositoriesInterface
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
        return Request::paginate(10);
    }

    public function getById($id): Request
    {
        return Request::findOrFail($id);
    }

    public function store(array $data): Request
    {
        return Request::create($data);
    }

    public function update(array $data, $id): Request
    {
        $Request = Request::findOrFail($id);
        $Request->update($data);
        return $Request;
    }
    public function delete($id): bool
    {
        return Request::where('id', $id)->delete() > 0;
    }

}