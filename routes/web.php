<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Models\Post;

Route::get('/', function () {
    $preview_posts = Post::where('status', 'like','preview')->get();
    $posts = Post::where('status', 'like','published')->get();
    return view('web.index', compact('preview_posts','posts'));
})->name('home');

Route::get('/Actividad', function () {
    $preview_posts = Post::where('status', 'like','preview')->get();
    $posts = Post::where('status', 'like','published')->get();
    return view('web.actividad', compact('preview_posts','posts'));
})->name('Actividad');

Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::get('/quienes_somos', function () {
 return view('web.quenesomos');
})->name('quienes.somos');

//filament.admin.resources.posts.create › App\Filament\Resources\PostResource\Pages\CreatePost