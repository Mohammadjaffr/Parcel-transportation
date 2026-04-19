<?php

namespace App\Interfaces;

interface ReceiptStrategyInterface
{
    public function fetchData(string $referenceId): array;
    public function getTemplatePath(): string;
    public function getFileName(array $data): string;
    public function sizepage(): string | array;
}