<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $blueprint) {
            $blueprint->string('slug')->nullable()->after('title')->unique();
        });

        // Generate slugs for existing activities
        $activities = DB::table('activities')->get();
        foreach ($activities as $activity) {
            $slug = Str::slug($activity->title);
            $originalSlug = $slug;
            $count = 1;

            while (DB::table('activities')->where('slug', $slug)->exists()) {
                $slug = "{$originalSlug}-{$count}";
                $count++;
            }

            DB::table('activities')
                ->where('id', $activity->id)
                ->update(['slug' => $slug]);
        }

        // Make slug non-nullable after populating
        Schema::table('activities', function (Blueprint $blueprint) {
            $blueprint->string('slug')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $blueprint) {
            $blueprint->dropColumn('slug');
        });
    }
};
