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
            $table->string('facebook_url')->nullable()->after('adsense_id');
            $table->string('instagram_url')->nullable()->after('facebook_url');
            $table->string('twitter_url')->nullable()->after('instagram_url');
            $table->string('youtube_url')->nullable()->after('twitter_url');
            $table->string('tiktok_url')->nullable()->after('youtube_url');
            $table->string('whatsapp_url')->nullable()->after('tiktok_url');
            $table->string('linkedin_url')->nullable()->after('whatsapp_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_settings', function (Blueprint $table) {
            $table->dropColumn([
                'facebook_url',
                'instagram_url',
                'twitter_url',
                'youtube_url',
                'tiktok_url',
                'whatsapp_url',
                'linkedin_url',
            ]);
        });
    }
};
