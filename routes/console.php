<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule daily check for overdue items
Schedule::command('borrowing:mark-overdue')->daily();

// Schedule daily reminders for upcoming due dates (Phase 5)
Schedule::command('borrowing:send-reminders')->daily();
