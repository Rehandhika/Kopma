<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

/**
 * POS Transaction Template Export
 * 
 * Creates an Excel template for bulk transaction import.
 * Columns: Tanggal, Kode Produk, Nama Produk, Qty, Metode Bayar
 * 
 * Requirements: 6.1
 */
class PosTransactionTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new PosTransactionDataSheet(),
            new PosTransactionInstructionsSheet(),
        ];
    }
}

/**
 * Data sheet with template columns and sample data
 */
class PosTransactionDataSheet implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    public function title(): string
    {
        return 'Data Transaksi';
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Kode Produk',
            'Nama Produk',
            'Qty',
            'Metode Bayar',
        ];
    }

    public function array(): array
    {
        // Sample data rows to guide users
        return [
            ['2025-12-23', 'PRD001', 'Contoh Produk 1', 2, 'cash'],
            ['2025-12-23', 'PRD002', 'Contoh Produk 2', 1, 'transfer'],
            ['2025-12-22', 'PRD003', 'Contoh Produk 3', 3, 'ewallet'],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,  // Tanggal
            'B' => 15,  // Kode Produk
            'C' => 30,  // Nama Produk
            'D' => 10,  // Qty
            'E' => 15,  // Metode Bayar
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'], // Indigo color
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            // Sample data rows styling (light background)
            '2:4' => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F3F4F6'], // Light gray
                ],
                'font' => [
                    'italic' => true,
                    'color' => ['rgb' => '6B7280'], // Gray text for sample
                ],
            ],
        ];
    }
}

/**
 * Instructions sheet with usage guide
 */
class PosTransactionInstructionsSheet implements FromArray, WithStyles, WithColumnWidths, WithTitle
{
    public function title(): string
    {
        return 'Petunjuk';
    }

    public function array(): array
    {
        return [
            ['PETUNJUK PENGGUNAAN TEMPLATE IMPOR TRANSAKSI POS'],
            [''],
            ['1. KOLOM YANG WAJIB DIISI:'],
            ['   - Tanggal: Format YYYY-MM-DD (contoh: 2025-12-23)'],
            ['   - Kode Produk: SKU produk yang terdaftar di sistem'],
            ['   - Nama Produk: Nama produk (untuk referensi, tidak divalidasi)'],
            ['   - Qty: Jumlah item (angka bulat positif)'],
            ['   - Metode Bayar: cash, transfer, atau ewallet'],
            [''],
            ['2. ATURAN VALIDASI:'],
            ['   - Tanggal tidak boleh lebih dari hari ini'],
            ['   - Kode Produk harus terdaftar di sistem'],
            ['   - Qty harus lebih dari 0'],
            ['   - Metode Bayar harus salah satu dari: cash, transfer, ewallet'],
            [''],
            ['3. CATATAN PENTING:'],
            ['   - Hapus baris contoh (baris 2-4) sebelum mengimpor'],
            ['   - Pastikan tidak ada baris kosong di antara data'],
            ['   - Stok produk akan otomatis berkurang sesuai Qty'],
            ['   - Jika ada error, semua transaksi akan dibatalkan'],
            [''],
            ['4. METODE PEMBAYARAN YANG VALID:'],
            ['   - cash    : Pembayaran tunai'],
            ['   - transfer: Pembayaran via transfer bank'],
            ['   - ewallet : Pembayaran via e-wallet (OVO, GoPay, dll)'],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 80,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Title row
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 14,
                    'color' => ['rgb' => '4F46E5'],
                ],
            ],
            // Section headers
            3 => ['font' => ['bold' => true]],
            10 => ['font' => ['bold' => true]],
            16 => ['font' => ['bold' => true]],
            22 => ['font' => ['bold' => true]],
        ];
    }
}
