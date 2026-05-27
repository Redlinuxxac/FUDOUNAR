<?php

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('admin post index component loads correctly', function () {
    Volt::test('admin.posts.index')
        ->assertStatus(200)
        ->assertSee('Gestión de Blog');
});

test('can create a blog post with automatic slug', function () {
    Volt::test('admin.posts.create')
        ->set('title', 'New Blog Post')
        ->set('content', 'Detailed content of the post.')
        ->set('status', PostStatus::DRAFT->value)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.posts'));

    $post = Post::where('title', 'New Blog Post')->first();
    expect($post)->not->toBeNull();
    expect($post->slug)->toBe('new-blog-post');
});

test('published_at is required for upcoming posts', function () {
    Volt::test('admin.posts.create')
        ->set('title', 'Scheduled Post')
        ->set('content', 'Content')
        ->set('status', PostStatus::UPCOMING->value)
        ->set('published_at', '')
        ->call('save')
        ->assertHasErrors(['published_at' => 'required']);
});

test('scheduled command publishes upcoming posts', function () {
    $pastPost = Post::factory()->create([
        'status' => PostStatus::UPCOMING,
        'published_at' => now()->subMinute(),
        'title' => 'Release Now',
    ]);

    $futurePost = Post::factory()->create([
        'status' => PostStatus::UPCOMING,
        'published_at' => now()->addHour(),
        'title' => 'Stay Hidden',
    ]);

    $this->artisan('post:publish')
        ->assertExitCode(0)
        ->expectsOutput('Published post: Release Now');

    expect($pastPost->fresh()->status)->toBe(PostStatus::PUBLISHED);
    expect($futurePost->fresh()->status)->toBe(PostStatus::UPCOMING);
});
