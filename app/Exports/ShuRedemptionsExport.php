<?php

namespace App\Exports;

use App\Models\ShuPointTransaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ShuRedemptionsExport implements FromQuery, ShouldAutoSize, WithChunkReading, WithHeadings, WithMapping, WithStyles
{
    public function __construct(
        private string $search = '',
        private string $dateFrom = '',
        private string $dateTo = ''
    ) {}

    public function query()
    {
        return ShuPointTransaction::query()
            ->with(['student:id,nim,full_name', 'creator:id,name'])
            ->where('type', 'redeem')
            ->when($this->search, function ($q) {
                $q->whereHas('student', function ($sq) {
                    $sq->where('nim', 'like', "%{$this->search}%")
                        ->orWhere('full_name', 'like', "%{$this->search}%");
                });
            })
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderByDesc('created_at');
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'NIM',
            'Nama',
            'Poin Dicairkan',
            'Nominal Pencairan',
            'Catatan',
            'Dicatat Oleh',
        ];
    }

    public function map($trx): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $trx->created_at?->format('d/m/Y H:i') ?? '-',
            $trx->student?->nim ?? '-',
            $trx->student?->full_name ?? '-',
            abs((int) $trx->points),
            $trx->cash_amount ? (int) $trx->cash_amount : '',
            $trx->notes ?? '',
            $trx->creator?->name ?? '',
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E5E7EB'],
                ],
            ],
        ];
    }
}

