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
        Schema::table('videos', function (Blueprint $table) {
            // Add extension columns for organized storage
            $table->string('source_ext')->default('mp4')->comment('Video file extension (e.g., mp4, webm)');
            $table->string('thumb_ext')->nullable()->comment('Thumbnail extension (e.g., jpg, png, webp)');
            
            // Drop old columns - files now stored in storage/app/public/videos/{id}/
            $table->dropColumn(['source_id', 'thumbnail_url']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->string('source_id')->nullable()->comment('Original file name or remote ID');
            $table->string('thumbnail_url')->nullable()->comment('URL to thumbnail image');
            
            $table->dropColumn(['source_ext', 'thumb_ext']);
        });
    }
};
