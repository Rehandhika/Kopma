<?php

namespace App\Exports;

use App\Models\SaleItem;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SaleItemsExport implements FromQuery, ShouldAutoSize, WithChunkReading, WithHeadings, WithMapping, WithStyles
{
    public function __construct(
        private string $dateFrom,
        private string $dateTo
    ) {}

    public function query()
    {
        return SaleItem::query()
            ->with(['sale:id,invoice_number,date,created_at,cashier_id,payment_method', 'sale.cashier:id,name'])
            ->whereHas('sale', function ($q) {
                $q->whereBetween('date', [$this->dateFrom, $this->dateTo])
                  ->whereNull('deleted_at');
            })
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
            'Nama Produk',
            'Jumlah',
            'Harga Satuan',
            'Subtotal',
        ];
    }

    public function map($item): array
    {
        static $no = 0;
        $no++;

        $paymentMethodMap = [
            'cash' => 'Tunai',
            'transfer' => 'Transfer',
            'qris' => 'QRIS',
        ];

        return [
            $no,
            $item->sale?->invoice_number ?? '-',
            $item->sale?->created_at?->format('d/m/Y H:i') ?? '-',
            $item->sale?->cashier?->name ?? '-',
            $paymentMethodMap[$item->sale?->payment_method] ?? ($item->sale?->payment_method ?? '-'),
            $item->product_name,
            $item->quantity,
            $item->price,
            $item->subtotal,
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
