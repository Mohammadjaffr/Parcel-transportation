<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface RepositoriesInterface
{
//    public function index();
    public function getById($id ) : ?Model;
    public function store(array $data):Model;
    public function update(array $data,$id):?Model;
    public function delete($id): bool;
}
