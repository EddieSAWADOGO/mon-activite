<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Clôture mensuelle automatique des compteurs de stock (snapshot par unité)
Schedule::command('stock:snapshot')->monthlyOn(1, '00:00');
