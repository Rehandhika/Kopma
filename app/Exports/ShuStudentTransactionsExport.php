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

class ShuStudentTransactionsExport implements FromQuery, ShouldAutoSize, WithChunkReading, WithHeadings, WithMapping, WithStyles
{
    public function __construct(
        private int $studentId,
        private string $type = '',
        private string $dateFrom = '',
        private string $dateTo = '',
        private string $search = ''
    ) {}

    public function query()
    {
        return ShuPointTransaction::query()
            ->with(['sale:id,invoice_number,total_amount', 'creator:id,name', 'student:id,nim,full_name'])
            ->where('student_id', $this->studentId)
            ->when($this->type, fn ($q) => $q->where('type', $this->type))
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('notes', 'like', "%{$this->search}%")
                        ->orWhereHas('sale', fn ($sq) => $sq->where('invoice_number', 'like', "%{$this->search}%"));
                });
            })
            ->orderByDesc('created_at');
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'NIM',
            'Nama',
            'Tipe',
            'Poin',
            'Nominal Pembelian',
            'Persentase (%)',
            'Nominal Pencairan',
            'Invoice',
            'Catatan',
            'Dicatat Oleh',
        ];
    }

    public function map($trx): array
    {
        static $no = 0;
        $no++;

        $typeMap = [
            'earn' => 'Masuk (Pembelian)',
            'redeem' => 'Keluar (Pencairan)',
            'adjust' => 'Penyesuaian',
        ];

        $percentage = $trx->percentage_bps ? number_format($trx->percentage_bps / 100, 2, '.', '') : '';

        return [
            $no,
            $trx->created_at?->format('d/m/Y H:i') ?? '-',
            $trx->student?->nim ?? '-',
            $trx->student?->full_name ?? '-',
            $typeMap[$trx->type] ?? $trx->type,
            (int) $trx->points,
            $trx->amount ? (int) $trx->amount : '',
            $percentage,
            $trx->cash_amount ? (int) $trx->cash_amount : '',
            $trx->sale?->invoice_number ?? '',
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

