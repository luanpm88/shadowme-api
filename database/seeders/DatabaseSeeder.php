<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Clear existing data once so seeders can run deterministically.
        DB::statement('PRAGMA foreign_keys = OFF');
        DB::table('transcript_segments')->delete();
        DB::table('transcripts')->delete();
        DB::table('clips')->delete();
        DB::table('video_progress')->delete();
        DB::table('videos')->delete();
        DB::table('refresh_tokens')->delete();
        DB::table('personal_access_tokens')->delete();
        DB::table('users')->delete();
        DB::statement('PRAGMA foreign_keys = ON');

        $this->call([
            UserSeeder::class,
            VideoSeeder::class,
        ]);
    }
}
