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
    // 2. 🟢 كم له؟ (إجمالي الأرصدة المستحقة له من الـ COD)
    public function getTotalCreditAttribute()
    {
        return $this->transactions()->where('type', 'credit')->sum('amount');
    }

    // 3. 🔴 كم عليه؟ (إجمالي الديون من الشحن الآجل)
    public function getTotalDebitAttribute()
    {
        return $this->transactions()->where('type', 'debit')->sum('amount');
    }
    // 4. ⚖️ الرصيد الصافي (إذا كان موجب = أنتم مدينون له / سالب = هو مدين لكم)
    public function getBalanceAttribute()
    {
        return $this->total_credit - $this->total_debit;
    }
    
    public function users()
    {
        return $this->hasMany(User::class);
    }
    
}
