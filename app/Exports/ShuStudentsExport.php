<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ShuStudentsExport implements FromQuery, ShouldAutoSize, WithChunkReading, WithHeadings, WithMapping, WithStyles
{
    public function __construct(
        private string $search = ''
    ) {}

    public function query()
    {
        return Student::query()
            ->select(['id', 'nim', 'full_name', 'points_balance', 'created_at'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('nim', 'like', "%{$this->search}%")
                        ->orWhere('full_name', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('nim');
    }

    public function headings(): array
    {
        return [
            'No',
            'NIM',
            'Nama Lengkap',
            'Saldo Poin',
            'Dibuat Pada',
        ];
    }

    public function map($student): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $student->nim,
            $student->full_name,
            (int) $student->points_balance,
            $student->created_at?->format('d/m/Y H:i') ?? '-',
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
