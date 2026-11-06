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
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->string('type', 10);
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('synopsis');
            $table->unsignedSmallInteger('release_year');
            $table->date('release_date')->nullable();
            $table->string('tagline')->nullable();
            $table->unsignedSmallInteger('duration_min')->nullable();
            $table->string('age_rating', 5);
            $table->json('cast_members')->nullable();
            $table->string('poster')->nullable();
            $table->string('backdrop')->nullable();
            $table->string('trailer_url')->nullable();
            $table->string('video_path')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedBigInteger('views')->default(0);
            $table->timestamps();

            $table->index(['type', 'release_year']);
            $table->index('is_featured');
            $table->index('views');
            $table->index('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
