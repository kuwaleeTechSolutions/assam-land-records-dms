<?php

use App\Jobs\CleanupPrivateTemporaryFiles;
use App\Jobs\ScanRepositoryIntegrity;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new ScanRepositoryIntegrity(2000))
    ->dailyAt('01:30')
    ->withoutOverlapping()
    ->onOneServer();

Schedule::job(new CleanupPrivateTemporaryFiles(24))
    ->hourly()
    ->withoutOverlapping()
    ->onOneServer();

Schedule::command('queue:prune-failed --hours=168')
    ->dailyAt('03:30');
