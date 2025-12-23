<?php

namespace App\Imports;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

/**
 * POS Transaction Import
 * 
 * Handles Excel import for bulk transaction entry.
 * Validates all rows before import and maps Excel columns to transaction data.
 * 
 * Requirements: 6.2
 */
class PosTransactionImport implements ToCollection, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    /**
     * Parsed and validated rows ready for import
     */
    protected Collection $validRows;

    /**
     * Validation errors per row
     */
    protected array $rowErrors = [];

    /**
     * Product cache for validation
     */
    protected Collection $products;

    /**
     * Valid payment methods
     */
    protected array $validPaymentMethods = ['cash', 'transfer', 'ewallet'];

    public function __construct()
    {
        $this->validRows = collect();
        $this->products = Product::select('id', 'sku', 'name', 'price', 'stock')
            ->where('status', 'active')
            ->get()
            ->keyBy('sku');
    }

    /**
     * Process the collection of rows from Excel
     * 
     * @param Collection $rows
     */
    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 because index starts at 0 and we skip header row
            
            // Skip completely empty rows
            if ($this->isEmptyRow($row)) {
                continue;
            }

            $errors = $this->validateRow($row, $rowNumber);
            
            if (empty($errors)) {
                $this->validRows->push($this->mapRowToTransaction($row));
            } else {
                $this->rowErrors[$rowNumber] = $errors;
            }
        }
    }

    /**
     * Check if a row is completely empty
     */
    protected function isEmptyRow($row): bool
    {
        $values = $row->toArray();
        foreach ($values as $value) {
            if (!empty($value) && $value !== null) {
                return false;
            }
        }
        return true;
    }

    /**
     * Validate a single row
     * 
     * @param mixed $row
     * @param int $rowNumber
     * @return array Validation errors
     */
    protected function validateRow($row, int $rowNumber): array
    {
        $errors = [];
        
        // Get values with flexible column name matching
        $tanggal = $this->getColumnValue($row, ['tanggal', 'date', 'tgl']);
        $kodeProduk = $this->getColumnValue($row, ['kode_produk', 'kode produk', 'sku', 'product_code']);
        $qty = $this->getColumnValue($row, ['qty', 'quantity', 'jumlah']);
        $metodeBayar = $this->getColumnValue($row, ['metode_bayar', 'metode bayar', 'payment_method', 'payment']);

        // Validate date
        if (empty($tanggal)) {
            $errors[] = 'Tanggal wajib diisi';
        } else {
            try {
                $parsedDate = Carbon::parse($tanggal);
                if ($parsedDate->isAfter(Carbon::today())) {
                    $errors[] = 'Tanggal tidak boleh lebih dari hari ini';
                }
            } catch (\Exception $e) {
                $errors[] = 'Format tanggal tidak valid (gunakan YYYY-MM-DD)';
            }
        }

        // Validate product code
        if (empty($kodeProduk)) {
            $errors[] = 'Kode Produk wajib diisi';
        } elseif (!$this->products->has($kodeProduk)) {
            $errors[] = "Produk dengan kode '{$kodeProduk}' tidak ditemukan";
        }

        // Validate quantity
        if (empty($qty)) {
            $errors[] = 'Qty wajib diisi';
        } elseif (!is_numeric($qty) || (int) $qty < 1) {
            $errors[] = 'Qty harus berupa angka lebih dari 0';
        }

        // Validate payment method
        if (empty($metodeBayar)) {
            $errors[] = 'Metode Bayar wajib diisi';
        } else {
            $normalizedPayment = strtolower(trim($metodeBayar));
            if (!in_array($normalizedPayment, $this->validPaymentMethods)) {
                $errors[] = "Metode Bayar tidak valid (gunakan: cash, transfer, atau ewallet)";
            }
        }

        return $errors;
    }

    /**
     * Get column value with flexible column name matching
     */
    protected function getColumnValue($row, array $possibleNames)
    {
        foreach ($possibleNames as $name) {
            // Try exact match
            if (isset($row[$name])) {
                return $row[$name];
            }
            // Try with underscores replaced by spaces
            $withSpaces = str_replace('_', ' ', $name);
            if (isset($row[$withSpaces])) {
                return $row[$withSpaces];
            }
        }
        return null;
    }

    /**
     * Map Excel row to transaction data array
     */
    protected function mapRowToTransaction($row): array
    {
        $tanggal = $this->getColumnValue($row, ['tanggal', 'date', 'tgl']);
        $kodeProduk = $this->getColumnValue($row, ['kode_produk', 'kode produk', 'sku', 'product_code']);
        $qty = $this->getColumnValue($row, ['qty', 'quantity', 'jumlah']);
        $metodeBayar = $this->getColumnValue($row, ['metode_bayar', 'metode bayar', 'payment_method', 'payment']);

        $product = $this->products->get($kodeProduk);

        return [
            'date' => Carbon::parse($tanggal)->format('Y-m-d'),
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'qty' => (int) $qty,
            'unit_price' => (float) $product->price,
            'total' => (int) $qty * (float) $product->price,
            'payment_method' => strtolower(trim($metodeBayar)),
        ];
    }

    /**
     * Laravel Excel validation rules (basic structure validation)
     */
    public function rules(): array
    {
        return [
            '*.tanggal' => 'nullable',
            '*.kode_produk' => 'nullable',
            '*.qty' => 'nullable',
            '*.metode_bayar' => 'nullable',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages(): array
    {
        return [
            '*.tanggal.required' => 'Kolom Tanggal wajib diisi pada baris :attribute',
            '*.kode_produk.required' => 'Kolom Kode Produk wajib diisi pada baris :attribute',
            '*.qty.required' => 'Kolom Qty wajib diisi pada baris :attribute',
            '*.metode_bayar.required' => 'Kolom Metode Bayar wajib diisi pada baris :attribute',
        ];
    }

    /**
     * Get validated rows ready for import
     */
    public function getValidRows(): Collection
    {
        return $this->validRows;
    }

    /**
     * Get validation errors per row
     */
    public function getRowErrors(): array
    {
        return $this->rowErrors;
    }

    /**
     * Check if there are any validation errors
     */
    public function hasErrors(): bool
    {
        return !empty($this->rowErrors);
    }

    /**
     * Get total count of valid rows
     */
    public function getValidCount(): int
    {
        return $this->validRows->count();
    }

    /**
     * Get total count of error rows
     */
    public function getErrorCount(): int
    {
        return count($this->rowErrors);
    }
}
