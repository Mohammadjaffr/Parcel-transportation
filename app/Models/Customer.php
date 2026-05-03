<?php


// معتمد
namespace App\Models;

use App\Traits\BelongsToApp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Customer extends Model
{
    use HasFactory,BelongsToApp,HasUuid;
    protected $fillable = [
        'name',
        'phone',
        'app_id',
        'branch_id',
        'created_by',
        'uuid',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class,'branch_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sentShipments()
    {
        return $this->hasMany(Shipment::class, 'sender_customer_id');
    }

    public function receivedShipments()
    {
        return $this->hasMany(Shipment::class, 'receiver_customer_id');
    }

    public function transactions()
    {
        return $this->hasMany(CustomerTransaction::class);
    }

    public function cashTransactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
    
}
