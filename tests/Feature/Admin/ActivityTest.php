<?php

use App\Enums\ActivityStatus;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('admin activity index component loads correctly', function () {
    Volt::test('admin.activities.index')
        ->assertStatus(200)
        ->assertSee('Gestión de Actividades');
});

test('can create an activity', function () {
    Volt::test('admin.activities.create')
        ->set('title', 'New Test Activity')
        ->set('description', 'Test Description')
        ->set('status', ActivityStatus::DRAFT->value)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.activities'));

    expect(Activity::where('title', 'New Test Activity')->exists())->toBeTrue();
});

test('started_at is required when status is UPCOMING', function () {
    Volt::test('admin.activities.create')
        ->set('title', 'Upcoming Activity')
        ->set('description', 'Description')
        ->set('status', ActivityStatus::UPCOMING->value)
        ->set('started_at', '')
        ->call('save')
        ->assertHasErrors(['started_at' => 'required']);
});

test('started_at must be in the future when status is UPCOMING', function () {
    Volt::test('admin.activities.create')
        ->set('title', 'Upcoming Activity')
        ->set('description', 'Description')
        ->set('status', ActivityStatus::UPCOMING->value)
        ->set('started_at', now()->subDay()->format('Y-m-d\TH:i'))
        ->call('save')
        ->assertHasErrors(['started_at' => 'after']);
});

test('can delete an activity', function () {
    $activity = Activity::factory()->create();

    Volt::test('admin.activities.index')
        ->call('delete', $activity->id)
        ->assertStatus(200);

    expect(Activity::find($activity->id))->toBeNull();
});

test('scheduled command activates upcoming activities', function () {
    $pastActivity = Activity::factory()->create([
        'status' => ActivityStatus::UPCOMING,
        'started_at' => now()->subMinute(),
        'title' => 'Past Upcoming Activity',
    ]);

    $futureActivity = Activity::factory()->create([
        'status' => ActivityStatus::UPCOMING,
        'started_at' => now()->addHour(),
        'title' => 'Future Upcoming Activity',
    ]);

    $this->artisan('activity:activate')
        ->assertExitCode(0)
        ->expectsOutput('Activated activity: Past Upcoming Activity');

    expect($pastActivity->fresh()->status)->toBe(ActivityStatus::ACTIVE);
    expect($futureActivity->fresh()->status)->toBe(ActivityStatus::UPCOMING);
});
