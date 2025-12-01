<?php

use App\Jobs\ReleaseExpiredHolds;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new ReleaseExpiredHolds)->everyMinute();
