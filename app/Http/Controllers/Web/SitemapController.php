<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Course;
use App\Models\Post;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate the dynamic sitemap XML.
     */
    public function __invoke(): Response
    {
        $staticUrls = [
            [
                'loc' => route('home'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '1.0',
            ],
            [
                'loc' => route('about'),
                'lastmod' => now()->startOfMonth()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.8',
            ],
            [
                'loc' => route('activities'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ],
            [
                'loc' => route('blog'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.8',
            ],
            [
                'loc' => route('courses'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ],
            [
                'loc' => route('contact'),
                'lastmod' => now()->startOfMonth()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ],
            [
                'loc' => route('privacy'),
                'lastmod' => now()->startOfYear()->toAtomString(),
                'changefreq' => 'yearly',
                'priority' => '0.3',
            ],
            [
                'loc' => route('terms'),
                'lastmod' => now()->startOfYear()->toAtomString(),
                'changefreq' => 'yearly',
                'priority' => '0.3',
            ],
        ];

        $activities = Activity::active()
            ->latest('updated_at')
            ->get()
            ->map(fn (Activity $activity) => [
                'loc' => route('activities.show', $activity->slug),
                'lastmod' => ($activity->updated_at ?? now())->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ]);

        $posts = Post::published()
            ->latest('updated_at')
            ->get()
            ->map(fn (Post $post) => [
                'loc' => route('blog.show', $post->slug),
                'lastmod' => ($post->published_at ?? $post->updated_at ?? now())->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ]);

        $courses = Course::open()
            ->latest('updated_at')
            ->get()
            ->map(fn (Course $course) => [
                'loc' => route('courses.show', $course->slug),
                'lastmod' => ($course->updated_at ?? now())->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ]);

        $allUrls = collect($staticUrls)
            ->concat($activities)
            ->concat($posts)
            ->concat($courses);

        $xml = view('web.sitemap', ['urls' => $allUrls])->render();

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
}
