<?php

namespace Tests\Feature;

use App\Enums\CourseRegistrationStatus;
use App\Livewire\Web\CourseRegistrationModal;
use App\Mail\CourseRegistrationVerificationMail;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Livewire\Volt\Volt;
use Tests\TestCase;

class CourseRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_submit_course_registration_and_receives_verification_email(): void
    {
        Mail::fake();

        $course = Course::factory()->open()->create([
            'capacity' => 10,
            'reservation_days' => 5,
        ]);

        Livewire::test(CourseRegistrationModal::class, ['course' => $course])
            ->set('full_name', 'Carlos Mendoza')
            ->set('email', 'carlos@example.com')
            ->set('phone', '+297 594 1234')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('submitted', true);

        $this->assertDatabaseHas('course_registrations', [
            'course_id' => $course->id,
            'full_name' => 'Carlos Mendoza',
            'email' => 'carlos@example.com',
            'phone' => '+297 594 1234',
            'status' => CourseRegistrationStatus::PENDING->value,
        ]);

        Mail::assertSent(CourseRegistrationVerificationMail::class, function ($mail) {
            return $mail->hasTo('carlos@example.com');
        });
    }

    public function test_user_cannot_register_twice_with_same_email_for_same_course(): void
    {
        Mail::fake();

        $course = Course::factory()->open()->create([
            'capacity' => 10,
        ]);

        CourseRegistration::factory()->create([
            'course_id' => $course->id,
            'email' => 'carlos@example.com',
            'status' => CourseRegistrationStatus::PENDING,
        ]);

        Livewire::test(CourseRegistrationModal::class, ['course' => $course])
            ->set('full_name', 'Carlos Mendoza')
            ->set('email', 'carlos@example.com')
            ->set('phone', '+297 594 1234')
            ->call('submit')
            ->assertHasErrors(['email']);

        $this->assertDatabaseCount('course_registrations', 1);
        Mail::assertNothingSent();
    }

    public function test_user_can_validate_email_and_activates_reservation_with_deadline(): void
    {
        $course = Course::factory()->open()->create([
            'capacity' => 10,
            'reservation_days' => 4,
        ]);

        $registration = CourseRegistration::factory()->create([
            'course_id' => $course->id,
            'status' => CourseRegistrationStatus::PENDING,
            'verification_token' => 'test-token-1234567890123456789012345678901234567890',
        ]);

        $response = $this->get(route('courses.registration.verify', ['token' => $registration->verification_token]));

        $response->assertOk();
        $response->assertSee('Comprobante de Reserva de Cupo');
        $response->assertSee($registration->formattedCode());

        $registration->refresh();

        $this->assertEquals(CourseRegistrationStatus::VERIFIED, $registration->status);
        $this->assertNotNull($registration->verified_at);
        $this->assertNotNull($registration->expires_at);
        $this->assertTrue($registration->expires_at->isFuture());
    }

    public function test_cannot_register_when_course_capacity_is_full(): void
    {
        Mail::fake();

        $course = Course::factory()->open()->create([
            'capacity' => 1,
        ]);

        // Registrar y verificar a un estudiante para llenar el aforo
        CourseRegistration::factory()->verified()->create([
            'course_id' => $course->id,
            'expires_at' => now()->addDays(3),
        ]);

        $this->assertTrue($course->isFull());
        $this->assertEquals(0, $course->availableSlots());
        $this->assertFalse($course->canAcceptRegistrations());

        Livewire::test(CourseRegistrationModal::class, ['course' => $course])
            ->set('full_name', 'Segundo Aspirante')
            ->set('email', 'segundo@example.com')
            ->set('phone', '+58 414 7654321')
            ->call('submit')
            ->assertHasErrors(['email']);

        $this->assertDatabaseMissing('course_registrations', [
            'email' => 'segundo@example.com',
        ]);

        Mail::assertNothingSent();
    }

    public function test_validation_fails_if_course_filled_up_before_verification(): void
    {
        $course = Course::factory()->open()->create([
            'capacity' => 1,
            'reservation_days' => 3,
        ]);

        // Aspirante 1 tiene preinscripción pendiente
        $registration = CourseRegistration::factory()->create([
            'course_id' => $course->id,
            'status' => CourseRegistrationStatus::PENDING,
            'verification_token' => 'pending-token-1234567890123456789012345678901234567890',
        ]);

        // Aspirante 2 se valida y ocupa el único cupo
        CourseRegistration::factory()->verified()->create([
            'course_id' => $course->id,
            'expires_at' => now()->addDays(3),
        ]);

        $this->assertTrue($course->isFull());

        // Aspirante 1 intenta validar su token tarde
        $response = $this->get(route('courses.registration.verify', ['token' => $registration->verification_token]));

        $response->assertOk();
        $response->assertSee('el aforo para este curso se ha completado');

        $registration->refresh();
        $this->assertEquals(CourseRegistrationStatus::PENDING, $registration->status);
    }

    public function test_command_releases_expired_course_registrations(): void
    {
        $course = Course::factory()->open()->create([
            'capacity' => 2,
        ]);

        // Reserva vencida
        $expiredReg = CourseRegistration::factory()->create([
            'course_id' => $course->id,
            'status' => CourseRegistrationStatus::VERIFIED,
            'verified_at' => now()->subDays(6),
            'expires_at' => now()->subDay(),
        ]);

        // Reserva activa vigente
        $activeReg = CourseRegistration::factory()->create([
            'course_id' => $course->id,
            'status' => CourseRegistrationStatus::VERIFIED,
            'verified_at' => now()->subDay(),
            'expires_at' => now()->addDays(3),
        ]);

        $this->assertEquals(1, $course->availableSlots());

        // Ejecutar comando Artisan
        $this->artisan('courses:release-expired-registrations')
            ->expectsOutput('Se han liberado 1 reservas vencidas.')
            ->assertSuccessful();

        $expiredReg->refresh();
        $activeReg->refresh();

        $this->assertEquals(CourseRegistrationStatus::EXPIRED, $expiredReg->status);
        $this->assertEquals(CourseRegistrationStatus::VERIFIED, $activeReg->status);

        // El cupo vencido fue liberado
        $this->assertEquals(1, $course->fresh()->availableSlots());
    }

    public function test_admin_can_formalize_registration_in_person(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $course = Course::factory()->open()->create();
        $registration = CourseRegistration::factory()->verified()->create([
            'course_id' => $course->id,
        ]);

        Volt::test('admin.course-registrations.index')
            ->assertStatus(200)
            ->call('formalize', $registration->id);

        $registration->refresh();

        $this->assertEquals(CourseRegistrationStatus::ENROLLED, $registration->status);
        $this->assertNotNull($registration->enrolled_at);
    }

    public function test_admin_can_save_notes_on_registration(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $course = Course::factory()->open()->create();
        $registration = CourseRegistration::factory()->verified()->create([
            'course_id' => $course->id,
        ]);

        Volt::test('admin.course-registrations.index')
            ->call('openDetail', $registration->id)
            ->set('adminNotes', 'Consignó copia de cédula y arancel.')
            ->call('saveNotes');

        $registration->refresh();
        $this->assertEquals('Consignó copia de cédula y arancel.', $registration->admin_notes);
    }

    public function test_admin_can_resend_verification_email(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $this->actingAs($user);

        $course = Course::factory()->open()->create();
        $registration = CourseRegistration::factory()->create([
            'course_id' => $course->id,
            'status' => CourseRegistrationStatus::PENDING,
            'email' => 'alumno@example.com',
        ]);

        Volt::test('admin.course-registrations.index')
            ->call('resendVerification', $registration->id);

        Mail::assertSent(CourseRegistrationVerificationMail::class, function ($mail) {
            return $mail->hasTo('alumno@example.com');
        });
    }

    public function test_modal_automatically_refreshes_status_when_registration_is_verified(): void
    {
        Mail::fake();

        $course = Course::factory()->open()->create([
            'capacity' => 10,
            'reservation_days' => 5,
        ]);

        $component = Livewire::test(CourseRegistrationModal::class, ['course' => $course])
            ->set('full_name', 'Ana Gómez')
            ->set('email', 'ana@example.com')
            ->set('phone', '+58 414 1112233')
            ->call('submit')
            ->assertSet('submitted', true)
            ->assertSet('isVerified', false)
            ->assertSee('Esperando confirmación en tu correo');

        $registration = CourseRegistration::where('email', 'ana@example.com')->firstOrFail();

        // Antes de validar en correo, llamar a checkVerificationStatus no cambia el estado
        $component->call('checkVerificationStatus')
            ->assertSet('isVerified', false);

        // El participante hace clic en el enlace del correo y se valida
        $this->get(route('courses.registration.verify', ['token' => $registration->verification_token]))
            ->assertOk();

        // El componente sondea y detecta la validación automáticamente
        $component->call('checkVerificationStatus')
            ->assertSet('isVerified', true)
            ->assertSee('Correo Validado con Éxito')
            ->assertSee('Ver Mi Comprobante de Reserva');
    }
}
