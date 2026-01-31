<?php

namespace App\Exports;

use App\Models\Sale;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesExport implements FromQuery, ShouldAutoSize, WithChunkReading, WithHeadings, WithMapping, WithStyles
{
    public function __construct(
        private string $dateFrom,
        private string $dateTo
    ) {}

    public function query()
    {
        return Sale::query()
            ->with(['cashier:id,name', 'items:id,sale_id,product_name,quantity,price,subtotal'])
            ->whereBetween('date', [$this->dateFrom, $this->dateTo])
            ->whereNull('deleted_at')
            ->orderByDesc('created_at');
    }

    public function headings(): array
    {
        return [
            'No',
            'Invoice',
            'Tanggal',
            'Kasir',
            'Metode Pembayaran',
            'Total',
            'Bayar',
            'Kembalian',
            'Jumlah Item',
            'Daftar Item',
        ];
    }

    public function map($sale): array
    {
        static $no = 0;
        $no++;

        $paymentMethodMap = [
            'cash' => 'Tunai',
            'transfer' => 'Transfer',
            'qris' => 'QRIS',
        ];

        // Format items list
        $itemsList = $sale->items->map(function ($item) {
            return $item->product_name . ' (' . $item->quantity . 'x @ Rp ' . number_format($item->price, 0, ',', '.') . ')';
        })->implode('; ');

        return [
            $no,
            $sale->invoice_number,
            $sale->created_at->format('d/m/Y H:i'),
            $sale->cashier?->name ?? '-',
            $paymentMethodMap[$sale->payment_method] ?? $sale->payment_method,
            $sale->total_amount,
            $sale->payment_amount,
            $sale->change_amount,
            $sale->items->sum('quantity'),
            $itemsList,
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
