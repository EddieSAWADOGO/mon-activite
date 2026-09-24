<?php

namespace App\Domain\Stock\Services;

use App\Domain\Produits\Models\Product;
use App\Domain\Produits\Models\StockUnit;
use App\Domain\Stock\Models\StockMovement;
use App\Domain\Stock\Models\StockSnapshot;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StockSnapshotService
{
    public function generateSnapshotForMonth(int $year, int $month): int
    {
        return DB::transaction(function () use ($year, $month) {
            $snapshotDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
            $units = StockUnit::with('product')->get();
            $count = 0;

            foreach ($units as $unit) {
                StockSnapshot::updateOrCreate(
                    [
                        'year' => $year,
                        'month' => $month,
                        'stock_unit_id' => $unit->id,
                    ],
                    [
                        'product_id' => $unit->product_id,
                        'quantity' => $unit->current_stock,
                        'snapshot_date' => $snapshotDate,
                    ]
                );
                $count++;
            }

            return $count;
        });
    }

    public function computeStockAtDate(Product $product, Carbon $targetDate): Collection
    {
        $units = $product->units;
        $unitQuantities = [];

        // Find the latest snapshot for this product on or before the targetDate
        $snapshotDate = null;
        $latestSnapshotDate = StockSnapshot::where('product_id', $product->id)
            ->where('snapshot_date', '<=', $targetDate)
            ->max('snapshot_date');

        if ($latestSnapshotDate) {
            $snapshotDate = Carbon::parse($latestSnapshotDate);
            $snapshots = StockSnapshot::where('product_id', $product->id)
                ->where('snapshot_date', $snapshotDate)
                ->get()
                ->keyBy('stock_unit_id');

            foreach ($units as $unit) {
                $unitQuantities[$unit->id] = isset($snapshots[$unit->id])
                    ? (float) $snapshots[$unit->id]->quantity
                    : 0.0;
            }
        } else {
            foreach ($units as $unit) {
                $unitQuantities[$unit->id] = 0.0;
            }
        }

        // Query movements after snapshotDate up to targetDate
        $movementsQuery = StockMovement::where('product_id', $product->id)
            ->where('movement_date', '<=', $targetDate);

        if ($snapshotDate) {
            $movementsQuery->where('movement_date', '>', $snapshotDate);
        }

        $movements = $movementsQuery->orderBy('movement_date', 'asc')->get();

        foreach ($movements as $m) {
            $uId = $m->stock_unit_id;
            if (! isset($unitQuantities[$uId])) {
                $unitQuantities[$uId] = 0.0;
            }

            if ($m->direction === 'in') {
                $unitQuantities[$uId] += (float) $m->quantity;
            } else {
                $unitQuantities[$uId] -= (float) $m->quantity;
            }
        }

        return $units->map(function (StockUnit $unit) use ($unitQuantities) {
            return [
                'unit' => $unit,
                'calculated_stock' => $unitQuantities[$unit->id] ?? 0.0,
            ];
        });
    }
}
