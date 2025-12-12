<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ActivationCodesExport implements FromArray, WithHeadings, WithMapping
{
    protected array $codes;

    public function __construct(array $codes)
    {
        $this->codes = $codes;
    }

    public function array(): array
    {
        return $this->codes;
    }

    public function map($row): array
    {
        return [
            $row['code'],
            $row['status'] instanceof \BackedEnum ? $row['status']->value : $row['status'],
            $row['expired_at'] instanceof \DateTimeInterface ? $row['expired_at']->format('Y-m-d H:i:s') : $row['expired_at'],
        ];
    }

    public function headings(): array
    {
        return [
            'Code',
            'Status',
            'Expired At',
        ];
    }
}
