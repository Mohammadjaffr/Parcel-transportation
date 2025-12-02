<?php 

namespace App\Repositories;

use App\Interfaces\RepositoriesInterface;
use App\Models\User;

class UserRepository implements RepositoriesInterface
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
        return User::paginate(10);
    }

    public function getById($id): User
    {
        return User::findOrFail($id);
    }

    public function store(array $data): User
    {
        return User::create($data);
    }

    public function update(array $data, $id): User
    {
        $user = User::findOrFail($id);
        $user->update($data);
        return $user;
    }
    public function delete($id): bool
    {
        return User::where('id', $id)->delete() > 0;
    }

    public function findByPhone($phone)
    {
        return User::where('phone', $phone)->first();
    }

}