<?php

namespace App\Console\Commands;

use App\Domain\Stock\Services\StockSnapshotService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateMonthlyStockSnapshot extends Command
{
    protected $signature = 'stock:snapshot {--year= : Année de la clôture (ex: 2026)} {--month= : Mois de la clôture (1-12)}';

    protected $description = 'Génère la clôture (snapshot) mensuelle de tous les compteurs de stock par unité.';

    public function handle(StockSnapshotService $snapshotService): int
    {
        $year = $this->option('year') ? (int) $this->option('year') : now()->year;
        $month = $this->option('month') ? (int) $this->option('month') : now()->month;

        $this->info("Génération du snapshot de stock pour {$month}/{$year}...");

        $count = $snapshotService->generateSnapshotForMonth($year, $month);

        $this->info("Snapshot terminé avec succès pour {$count} unités de stock.");

        return Command::SUCCESS;
    }
}
