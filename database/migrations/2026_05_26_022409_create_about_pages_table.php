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
        Schema::create('about_pages', function (Blueprint $table) {
            $table->id();
            // Misión
            $table->text('mission_text')->nullable();
            $table->string('mission_image')->nullable();
            // Visión
            $table->text('vision_text')->nullable();
            $table->string('vision_image')->nullable();
            // Valores
            $table->text('values_text')->nullable();
            $table->string('values_image')->nullable();
            // Historia
            $table->text('history_text')->nullable();
            // Equipo
            $table->text('team_text')->nullable();
            $table->string('team_image')->nullable();
            // Otros
            $table->text('impact_text')->nullable(); // Rich text para lista
            $table->text('achievements_text')->nullable(); // Rich text para lista
            $table->text('why_donate_text')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_pages');
    }
};
