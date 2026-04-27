<?php
use Illuminate\Support\Facades\Schedule;

Schedule::command('tagihan:generate-otomatis')
    ->dailyAt('07:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/generate-tagihan.log'));
Schedule::command('billing:auto-isolir')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/auto-isolir.log'));

Schedule::command('onu:sync')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/onu-sync.log'));
