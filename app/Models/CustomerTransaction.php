<?php
// معتمد
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Passengers;
use App\Models\User;

class CustomerTransaction extends Model
{
    protected $fillable = [
        'customer_id',
        'shipment_id',
        'amount',
        'type',
        'description',
        'passenger_id',
        'created_by'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
    public function passenger()
    {
        return $this->belongsTo(Passengers::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
