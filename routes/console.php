<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;

Schedule::command('queue:prune-batches --hours=48')
    ->daily()
    ->when(fn () => Schema::hasTable('job_batches'));
