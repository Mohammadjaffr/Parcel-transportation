<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
      use HasFactory;

    protected $table = 'branches';

    protected $fillable = [
        'name',
        'region',
        'phone',
        'code',
    ];
    

    public function users()
{
    return $this->hasMany(User::class);
}

}