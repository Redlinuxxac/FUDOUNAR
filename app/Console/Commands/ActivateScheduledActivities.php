<?php

namespace App\Console\Commands;

use App\Enums\ActivityStatus;
use App\Models\Activity;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ActivateScheduledActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activity:activate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activate upcoming activities whose start time has arrived.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $activities = Activity::where('status', ActivityStatus::UPCOMING)
            ->where('started_at', '<=', now())
            ->get();

        if ($activities->isEmpty()) {
            $this->info('No activities to activate.');

            return;
        }

        foreach ($activities as $activity) {
            $activity->update(['status' => ActivityStatus::ACTIVE]);
            $this->info("Activated activity: {$activity->title}");
            Log::info("Activity activated automatically: ID {$activity->id} - {$activity->title}");
        }

        $this->info("Total activated: {$activities->count()}");
    }
}
