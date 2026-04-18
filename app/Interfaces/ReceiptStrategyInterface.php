<?php

namespace App\Interfaces;

interface ReceiptStrategyInterface
{
    public function fetchData(int $referenceId): array;
    public function getTemplatePath(): string;
    public function getFileName(array $data): string;
    public function sizepage(): string | array;
}