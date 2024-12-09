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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();  // Incrementing ID primary key
            $table->string('title');  // String field for the post title
            $table->text('body');  // Text field for the post content
            $table->unsignedBigInteger('user_id');   // Unsigned big integer for the user ID who created the post
            $table->foreign('user_id')->references('id')->on('users');  // Foreign key constraint referencing the 'users' table
            $table->enum('status', ['draft', 'published'])->default('draft');  // Enum field for the post status (draft or published)
            $table->timestamps();   // Timestamps for created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
