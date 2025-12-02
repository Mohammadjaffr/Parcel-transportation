<?php 

namespace App\Repositories;

use App\Interfaces\RepositoriesInterface;
use App\Models\SpecialOrder;

class SpecialOrderRepository implements RepositoriesInterface
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
        return SpecialOrder::paginate(10);
    }

    public function getById($id): SpecialOrder
    {
        return SpecialOrder::findOrFail($id);
    }

    public function store(array $data): SpecialOrder
    {
        return SpecialOrder::create($data);
    }

    public function update(array $data, $id): SpecialOrder
    {
        $SpecialOrder = SpecialOrder::findOrFail($id);
        $SpecialOrder->update($data);
        return $SpecialOrder;
    }
    public function delete($id): bool
    {
        return SpecialOrder::where('id', $id)->delete() > 0;
    }

}