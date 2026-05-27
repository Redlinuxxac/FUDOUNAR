<?php

namespace App\Console\Commands;

use App\Enums\CourseStatus;
use App\Models\Course;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ActivateScheduledCourses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'course:activate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Open enrollments for upcoming courses whose start time has arrived.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $courses = Course::where('status', CourseStatus::UPCOMING)
            ->where('started_at', '<=', now())
            ->get();

        if ($courses->isEmpty()) {
            $this->info('No courses to activate.');

            return;
        }

        foreach ($courses as $course) {
            $course->update(['status' => CourseStatus::OPEN]);
            $this->info("Activated course: {$course->title}");
            Log::info("Course enrollment opened automatically: ID {$course->id} - {$course->title}");
        }

        $this->info("Total activated: {$courses->count()}");
    }
}
