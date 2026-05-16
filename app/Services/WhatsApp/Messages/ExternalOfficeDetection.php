<?php

namespace App\Services\WhatsApp\Messages;

use App\Interfaces\WhatsAppMessageInterface;
use App\Models\ShipmentPackage;
use Illuminate\Database\Eloquent\Model;

class ExternalOfficeDetection implements WhatsAppMessageInterface
{
    public function getPhoneNumber(Model $entity): ?string
    {
        if (!$entity instanceof ShipmentPackage || empty($entity->external_office_branch)) {
            return null;
        }

        $officeBranch = $entity->external_office_branch;
        return $officeBranch->phone ?? $officeBranch->office->phone ?? null;
    }

    public function getReceiptType(): ?string
    {
        return null; // Generate URL manually inside getMessageBody to include query parameters
    }

    public function getMessageBody(Model $entity, ?string $receiptUrl): string
    {
        if (!$entity instanceof ShipmentPackage || empty($entity->external_office_branch)) {
            return "بيانات الوجهة غير صالحة.";
        }

        $officeBranch = $entity->external_office_branch;
        $officeName = $officeBranch->office->name ?? 'المكتب';
        $branchName = $officeBranch->name ?? '';
        $fullName = $branchName ? "{$officeName} - {$branchName}" : $officeName;

        $trackingNo = $entity->tracking_number ?? 'غير متوفر';
        
        $customReceiptUrl = route('receipt.generate', [
            'type' => 'ExternalOfficeDetection',
            'id' => $entity->uuid ?? $entity->id,
            'office_branch_id' => $officeBranch->id
        ]);

        $msg  = "السلام عليكم ورحمة الله وبركاته\n\n";
        $msg .= "الأخوة في / *{$fullName}* المحترمين،\n\n";

        $msg .= "يسعدنا إشعاركم بأنه تم إرسال إرسالية جديدة تتضمن طروداً موجهة لمكتبكم.\n\n";

        $msg .= "📦 رقم الإرسالية : *{$trackingNo}*\n";
        if ($entity->senderBranch) {
            $msg .= "🏢 الانطلاق من : *" . $entity->senderBranch->name . "*\n";
        }

        $msg .= "\n📄 كشف الإرسالية المخصص لمكتبكم:\n{$customReceiptUrl}\n";

        $msg .= "\nنأمل منكم التكرم بالاطلاع على الكشف لاستلام الطرود عند وصولها.\n";
        $msg .= "نشكر تعاونكم المستمر. 🌹";

        return $msg;
    }
}
