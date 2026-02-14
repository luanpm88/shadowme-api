<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transcript_segments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transcript_id')->constrained()->cascadeOnDelete();
            $table->decimal('start_time', 8, 2);
            $table->decimal('end_time', 8, 2);
            $table->text('text');
            $table->unsignedInteger('position');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transcript_segments');
    }
};
