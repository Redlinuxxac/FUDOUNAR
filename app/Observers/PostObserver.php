<?php

namespace App\Observers;

use App\Models\Image;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class PostObserver
{
    /**
     * Handle the Post "created" event. creating
     */
    public function creating(Post $post): void
    {
        $post->user_id = Auth::id();
    }
    /**
     * Handle the Post "creating" event. creating
     */
    public function created(Post $post): void
    {
        //dd($post);
    }

    /**
     * Handle the Post "updated" event.
     */
    public function updated(Post $post): void
    {
        //dd($post);
    }

    /**
     * Handle the Post "deleted" event.
     */
    public function deleted(Post $post): void
    {
        //
    }

    /**
     * Handle the Post "restored" event.
     */
    public function restored(Post $post): void
    {
        //
    }

    /**
     * Handle the Post "force deleted" event.
     */
    public function forceDeleted(Post $post): void
    {
        //
    }
}
