<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Passengers extends Model 
{
    use HasFactory ;
    protected $fillable = [
        'date',
        'broker_id',
        'passenger_number',
        'pickup_location',
        'destination',
        'count',
        'office_commission',
        'other_office_commission',
        'branch_id',
        'driver_id',
        'note',
        'status'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }
    public function getTotalCommissionAttribute()
    {  
        return $this->office_commission + $this->other_office_commission;
    }

    public function getDriverPdfLinkAttribute()
    {
        return route('receipt.generate', ['type' => 'passenger', 'id' => $this->id]);
    }

    public function getDriverWhatsappLinkAttribute()
    {
        if (!$this->driver || !$this->driver->phone) {
            return null;
        }

        $driverName = $this->driver->name ?? 'السائق';
        $pNum = $this->passenger_number ?? '---';
        $pLoc = $this->location ?? '---';
        $pCnt = $this->count ?? 0;
        $pNote = $this->note ? $this->note : '---';
        $pdfLink = $this->driver_pdf_link;

        $msg = "السلام عليكم ورحمة الله وبركاته\n";
        $msg .= "الأخ الكابتن / *{$driverName}* المحترم،\n\n";
        $msg .= "تم تكليفك بنقل الراكب التالي:\n\n";
        $msg .= "*تفاصيل الراكب:*\n";
        $msg .= "------------------------------------------\n";
        $msg .= "الراكب: {$pNum}\n";
        $msg .= "📍 المكان: {$pLoc}\n";
        $msg .= "👥 العدد: {$pCnt} راكب\n";
        if ($this->note) {
            $msg .= "📝 ملاحظات: {$pNote}\n";
        }
        $msg .= "------------------------------------------\n\n";
        $msg .= "📄 رابط كشف الـ PDF للراكب:\n{$pdfLink}\n\n";
        $msg .= "رافقتكم السلامة. 🚚";

        $encodedMessage = urlencode($msg);
        $cleanPhone = preg_replace('/[^\d\+]/', '', $this->driver->phone);
        $cleanPhone = ltrim($cleanPhone, '+');

        if (str_starts_with($cleanPhone, '00')) {
            $cleanPhone = substr($cleanPhone, 2);
        }
        if (str_starts_with($cleanPhone, '967967')) {
            $cleanPhone = substr($cleanPhone, 3);
        }
        if (str_starts_with($cleanPhone, '966966')) {
            $cleanPhone = substr($cleanPhone, 3);
        }
        if (preg_match('/^0(7[0-9]\d{7})$/', $cleanPhone, $matches)) {
            $cleanPhone = '967' . $matches[1];
        }
        if (strlen($cleanPhone) === 9 && (str_starts_with($cleanPhone, '77') || str_starts_with($cleanPhone, '73') || str_starts_with($cleanPhone, '71') || str_starts_with($cleanPhone, '70'))) {
            $cleanPhone = '967' . $cleanPhone;
        }

        return "https://wa.me/{$cleanPhone}?text={$encodedMessage}";
    }
}
