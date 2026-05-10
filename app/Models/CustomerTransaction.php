<?php
// معتمد
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Passengers;

class CustomerTransaction extends Model
{
    protected $fillable = [
        'customer_id',
        'shipment_id',
        'amount',
        'type',
        'description',
        'passenger_id'
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
}
