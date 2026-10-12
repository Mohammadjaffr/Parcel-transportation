<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PassengerTrip extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'app_id',
        'branch_id',
        'created_by',
        'driver_id',
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function passengers()
    {
        return $this->hasMany(Passengers::class, 'trip_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getDriverPdfLinkAttribute()
    {
        return \App\Services\WhatsApp\WhatsAppLinkService::generate($this, 'tripDriver');
    }

    public function getOfficeCommissionAttribute()
    {
        return $this->passengers->sum('office_commission');
    }

    public function getOtherOfficeCommissionAttribute()
    {
        return $this->passengers->sum('other_office_commission');
    }

    public function getTotalCommissionAttribute()
    {
        return $this->passengers->sum(function ($passenger) {
            return ($passenger->office_commission ?? 0) + ($passenger->other_office_commission ?? 0);
        });
    }
}