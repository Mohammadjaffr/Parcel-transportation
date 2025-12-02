<?php 

namespace App\Repositories;

use App\Interfaces\RepositoriesInterface;
use App\Models\Rating;

class RatingRepository implements RepositoriesInterface
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
        return Rating::paginate(10);
    }

    public function getById($id): Rating
    {
        return Rating::findOrFail($id);
    }

    public function store(array $data): Rating
    {
        return Rating::create($data);
    }

    public function update(array $data, $id): Rating
    {
        $Rating = Rating::findOrFail($id);
        $Rating->update($data);
        return $Rating;
    }
    public function delete($id): bool
    {
        return Rating::where('id', $id)->delete() > 0;
    }

    public function getByUserIdAndDriverId($userId, $driverId): ?Rating
    {
        return Rating::where('user_id', $userId)
            ->where('driver_id', $driverId)
            ->first();
    }

}