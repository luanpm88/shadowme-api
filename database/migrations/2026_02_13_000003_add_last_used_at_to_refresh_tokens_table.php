<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Column is now created directly in the create_refresh_tokens_table migration
        // This migration is kept for backwards compatibility
    }

    public function down(): void
    {
        // No-op
    }
};
