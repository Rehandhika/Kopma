<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * POS Entry Service - Simplified for batch operations
 */
class PosEntryService
{
    /**
     * Get daily summary with single optimized query
     */
    public function getDailySummary(Carbon $date): array
    {
        $result = Sale::query()
            ->whereDate('date', $date)
            ->selectRaw('
                COALESCE(SUM(total_amount), 0) as total,
                COUNT(*) as count,
                COALESCE(SUM(CASE WHEN payment_method = "cash" THEN total_amount ELSE 0 END), 0) as cash,
                COALESCE(SUM(CASE WHEN payment_method = "transfer" THEN total_amount ELSE 0 END), 0) as transfer,
                COALESCE(SUM(CASE WHEN payment_method = "qris" THEN total_amount ELSE 0 END), 0) as qris
            ')
            ->first();

        return [
            'total_amount' => (float) $result->total,
            'transaction_count' => (int) $result->count,
            'cash_total' => (float) $result->cash,
            'transfer_total' => (float) $result->transfer,
            'ewallet_total' => (float) $result->qris,
        ];
    }

    /**
     * Delete transaction and restore stock
     */
    public function deleteTransaction(int $id): void
    {
        DB::transaction(function () use ($id) {
            $sale = Sale::with('items')->findOrFail($id);
            
            foreach ($sale->items as $item) {
                Product::where('id', $item->product_id)->increment('stock', $item->quantity);
            }
            
            $sale->items()->delete();
            $sale->delete();
        });
    }
}
