<?php

namespace App\Console\Commands;

use App\Enums\CourseRegistrationStatus;
use App\Models\CourseRegistration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReleaseExpiredCourseRegistrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'courses:release-expired-registrations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Libera los cupos de reservas presenciales cuyo plazo ha vencido';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $expiredRegistrations = CourseRegistration::where('status', CourseRegistrationStatus::VERIFIED)
            ->where('expires_at', '<', now())
            ->get();

        if ($expiredRegistrations->isEmpty()) {
            $this->info('No hay reservas vencidas por liberar.');

            return self::SUCCESS;
        }

        foreach ($expiredRegistrations as $registration) {
            $registration->update([
                'status' => CourseRegistrationStatus::EXPIRED,
            ]);

            Log::info("Reserva de curso liberada por vencimiento: ID {$registration->id} - {$registration->email}");
        }

        $this->info("Se han liberado {$expiredRegistrations->count()} reservas vencidas.");

        return self::SUCCESS;
    }
}
