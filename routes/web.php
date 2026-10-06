<?php

use App\Http\Controllers\Web\CourseRegistrationController;
use App\Http\Controllers\Web\SitemapController;
use App\Models\AboutPage;
use App\Models\Activity;
use App\Models\ContactSetting;
use App\Models\Course;
use App\Models\Post;
use App\Models\Slide;
use App\Models\SocialShare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('web.index', [
        'slides' => Slide::active()->ordered()->get(),
    ]);
})->name('home');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::view('/politica-de-privacidad', 'web.privacy')->name('privacy');
Route::view('/terminos-y-condiciones', 'web.terms')->name('terms');

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
    $activity = Activity::active()->where('slug', $slug)->firstOrFail();
    $activity->increment('views');

    return view('web.activity-detail', [
        'activity' => $activity,
    ]);
})->name('activities.show');

Route::view('/blog', 'web.blog')->name('blog');
Route::get('/blog/{slug}', function (string $slug) {
    $post = Post::published()->where('slug', $slug)->firstOrFail();
    $post->increment('views');

    return view('web.blog-detail', [
        'post' => $post,
    ]);
})->name('blog.show');

Route::view('/cursos', 'web.courses')->name('courses');
Route::get('/cursos/inscripcion/validar/{token}', [CourseRegistrationController::class, 'verify'])->name('courses.registration.verify');
Route::get('/cursos/{slug}', function (string $slug) {
    $course = Course::open()->where('slug', $slug)->firstOrFail();
    $course->increment('views');

    return view('web.course-detail', [
        'course' => $course,
    ]);
})->name('courses.show');

Route::post('/track-share', function (Request $request) {
    $validated = $request->validate([
        'type' => 'required|string|in:activity,post,course',
        'id' => 'required|integer',
        'platform' => 'required|string|in:facebook,twitter,linkedin,whatsapp,native',
    ]);

    $modelClass = match ($validated['type']) {
        'activity' => Activity::class,
        'post' => Post::class,
        'course' => Course::class,
    };

    $item = $modelClass::find($validated['id']);
    if ($item) {
        SocialShare::create([
            'shareable_type' => $modelClass,
            'shareable_id' => $item->id,
            'platform' => $validated['platform'],
            'ip_address' => $request->ip(),
        ]);
    }

    return response()->json(['success' => true]);
})->name('track.share');

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
        Volt::route('/course-registrations', 'admin.course-registrations.index')->name('admin.course-registrations');

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
