<?php

namespace App\Console\Commands;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PublishUpcomingPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'post:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish upcoming blog posts whose publication time has arrived.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $posts = Post::where('status', PostStatus::UPCOMING)
            ->where('published_at', '<=', now())
            ->get();

        if ($posts->isEmpty()) {
            $this->info('No posts to publish.');

            return;
        }

        foreach ($posts as $post) {
            $post->update(['status' => PostStatus::PUBLISHED]);
            $this->info("Published post: {$post->title}");
            Log::info("Post published automatically: ID {$post->id} - {$post->title}");
        }

        $this->info("Total published: {$posts->count()}");
    }
}
