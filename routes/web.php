<?php

use App\Models\AboutPage;
use App\Models\Activity;
use App\Models\ContactSetting;
use App\Models\Course;
use App\Models\Post;
use App\Models\Slide;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('web.index', [
        'slides' => Slide::active()->ordered()->get(),
    ]);
})->name('home');

Route::get('/ads.txt', function () {
    $adsenseId = ContactSetting::first()?->adsense_id;
    if (! $adsenseId) {
        return response('No AdSense ID configured.', 404);
    }

    return response("google.com, {$adsenseId}, DIRECT, f08c47fec0942fa0")
        ->header('Content-Type', 'text/plain');
});

Route::get('/quienes-somos', function () {
    return view('web.about', [
        'about' => AboutPage::first(),
    ]);
})->name('about');

Route::view('/actividades', 'web.activities')->name('activities');
Route::get('/actividades/{slug}', function (string $slug) {
    return view('web.activity-detail', [
        'activity' => Activity::active()->where('slug', $slug)->firstOrFail(),
    ]);
})->name('activities.show');

Route::view('/blog', 'web.blog')->name('blog');
Route::get('/blog/{slug}', function (string $slug) {
    return view('web.blog-detail', [
        'post' => Post::published()->where('slug', $slug)->firstOrFail(),
    ]);
})->name('blog.show');

Route::view('/cursos', 'web.courses')->name('courses');
Route::get('/cursos/{slug}', function (string $slug) {
    return view('web.course-detail', [
        'course' => Course::open()->where('slug', $slug)->firstOrFail(),
    ]);
})->name('courses.show');

Route::get('/contacto', function () {
    return view('web.contact', [
        'contact' => ContactSetting::first(),
    ]);
})->name('contact');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('/dashboard', '/admin/dashboard');

    Route::prefix('admin')->group(function () {
        Route::view('/dashboard', 'admin.dashboard')->name('dashboard');

        // Actividades
        Volt::route('/activities', 'admin.activities.index')->name('admin.activities');
        Volt::route('/activities/create', 'admin.activities.create')->name('admin.activities.create');
        Volt::route('/activities/{activity}/edit', 'admin.activities.edit')->name('admin.activities.edit');

        // Blog
        Volt::route('/posts', 'admin.posts.index')->name('admin.posts');
        Volt::route('/posts/create', 'admin.posts.create')->name('admin.posts.create');
        Volt::route('/posts/{post}/edit', 'admin.posts.edit')->name('admin.posts.edit');

        // Cursos
        Volt::route('/courses', 'admin.courses.index')->name('admin.courses');
        Volt::route('/courses/create', 'admin.courses.create')->name('admin.courses.create');
        Volt::route('/courses/{course}/edit', 'admin.courses.edit')->name('admin.courses.edit');

        // Usuarios
        Volt::route('/users', 'admin.users.index')->name('admin.users');
        Volt::route('/users/create', 'admin.users.create')->name('admin.users.create');
        Volt::route('/users/{user}/edit', 'admin.users.edit')->name('admin.users.edit');

        // Roles y Permisos
        Volt::route('/roles', 'admin.roles.index')->name('admin.roles');
        Volt::route('/roles/create', 'admin.roles.create')->name('admin.roles.create');
        Volt::route('/roles/{role}/edit', 'admin.roles.edit')->name('admin.roles.edit');

        Volt::route('/permissions', 'admin.permissions.index')->name('admin.permissions');
        Volt::route('/permissions/create', 'admin.permissions.create')->name('admin.permissions.create');
        Volt::route('/permissions/{permission}/edit', 'admin.permissions.edit')->name('admin.permissions.edit');

        // Páginas
        Volt::route('/pages/about', 'admin.about.edit')->name('admin.about.edit');

        // Slides
        Volt::route('/slides', 'admin.slides.index')->name('admin.slides');
        Volt::route('/slides/create', 'admin.slides.create')->name('admin.slides.create');
        Volt::route('/slides/{slide}/edit', 'admin.slides.edit')->name('admin.slides.edit');
    });
});

require __DIR__.'/settings.php';
