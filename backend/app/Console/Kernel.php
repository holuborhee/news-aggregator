<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\ScrapeNewsJob;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(new ScrapeNewsJob)->everyTenMinutes();
    }
}
