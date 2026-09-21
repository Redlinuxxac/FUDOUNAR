<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contact_settings', function (Blueprint $table) {
            $table->boolean('is_facebook_active')->default(true)->after('facebook_url');
            $table->boolean('is_instagram_active')->default(true)->after('instagram_url');
            $table->boolean('is_twitter_active')->default(true)->after('twitter_url');
            $table->boolean('is_youtube_active')->default(true)->after('youtube_url');
            $table->boolean('is_tiktok_active')->default(true)->after('tiktok_url');
            $table->boolean('is_whatsapp_active')->default(true)->after('whatsapp_url');
            $table->boolean('is_linkedin_active')->default(true)->after('linkedin_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_settings', function (Blueprint $table) {
            $table->dropColumn([
                'is_facebook_active',
                'is_instagram_active',
                'is_twitter_active',
                'is_youtube_active',
                'is_tiktok_active',
                'is_whatsapp_active',
                'is_linkedin_active',
            ]);
        });
    }
};
