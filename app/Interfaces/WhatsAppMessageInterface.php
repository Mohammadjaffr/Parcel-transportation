<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface WhatsAppMessageInterface
{
    public function getPhoneNumber(Model $entity): ?string;
    public function getReceiptType(): ?string;
    public function getMessageBody(Model $entity, ?string $receiptUrl): string;
}