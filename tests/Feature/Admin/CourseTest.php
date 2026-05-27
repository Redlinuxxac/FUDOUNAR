<?php

use App\Enums\CourseModality;
use App\Enums\CourseStatus;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('admin course index component loads correctly', function () {
    Volt::test('admin.courses.index')
        ->assertStatus(200)
        ->assertSee('Gestión de Cursos');
});

test('can create a course with automatic slug', function () {
    Volt::test('admin.courses.create')
        ->set('title', 'New Pro Course')
        ->set('description', 'Course content')
        ->set('duration', 40)
        ->set('modality', CourseModality::PRESENTIAL->value)
        ->set('status', CourseStatus::DRAFT->value)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.courses'));

    $course = Course::where('title', 'New Pro Course')->first();
    expect($course)->not->toBeNull();
    expect($course->slug)->toBe('new-pro-course');
});

test('scheduled command opens course enrollments', function () {
    $pastCourse = Course::factory()->create([
        'status' => CourseStatus::UPCOMING,
        'started_at' => now()->subMinute(),
        'title' => 'Open Me',
    ]);

    $futureCourse = Course::factory()->create([
        'status' => CourseStatus::UPCOMING,
        'started_at' => now()->addHour(),
        'title' => 'Wait',
    ]);

    $this->artisan('course:activate')
        ->assertExitCode(0)
        ->expectsOutput('Activated course: Open Me');

    expect($pastCourse->fresh()->status)->toBe(CourseStatus::OPEN);
    expect($futureCourse->fresh()->status)->toBe(CourseStatus::UPCOMING);
});
