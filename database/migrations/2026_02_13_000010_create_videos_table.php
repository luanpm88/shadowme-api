<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('level', 4);
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->string('source_type');
            $table->string('source_id');
            $table->string('source_url')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->string('language', 5)->default('en');
            $table->json('topic_tags')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
