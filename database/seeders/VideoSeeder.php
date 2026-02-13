<?php

namespace Database\Seeders;

use App\Models\Video;
use App\Models\Transcript;
use App\Models\TranscriptSegment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Video 1: Street Interview
        $video1 = Video::create([
            'title' => 'Street Interview: Confidence in English',
            'description' => 'A natural street interview with clear pacing and natural English conversation. Perfect for B1 level learners.',
            'level' => 'B1',
            'duration_seconds' => 420,
            'source_type' => 'upload',
            'source_id' => 'interview-confidence.mp4',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=900&q=80',
            'language' => 'en',
            'topic_tags' => ['Speaking', 'Real Life', 'Interview'],
            'is_published' => true,
            'is_featured' => true,
        ]);

        // Create transcript for video 1
        $transcript1 = Transcript::create([
            'video_id' => $video1->id,
            'language' => 'en',
        ]);

        // Create segments
        $segments1 = [
            [
                'start_time' => 0,
                'end_time' => 4,
                'text' => 'Hi, I\'m interviewing people on the street today about their English learning journey.',
                'position' => 1,
            ],
            [
                'start_time' => 4,
                'end_time' => 8,
                'text' => 'Many people find it intimidating to speak English in public, but it\'s actually a lot easier than they think.',
                'position' => 2,
            ],
            [
                'start_time' => 8,
                'end_time' => 12,
                'text' => 'The key is to practice consistently and not worry about making mistakes.',
                'position' => 3,
            ],
            [
                'start_time' => 12,
                'end_time' => 16,
                'text' => 'Everyone makes mistakes when learning a new language, and that\'s perfectly normal.',
                'position' => 4,
            ],
            [
                'start_time' => 16,
                'end_time' => 20,
                'text' => 'The most important thing is to just keep practicing and stay confident.',
                'position' => 5,
            ],
        ];

        foreach ($segments1 as $segment) {
            TranscriptSegment::create(array_merge($segment, [
                'transcript_id' => $transcript1->id,
            ]));
        }

        // Video 2: TED Talk
        $video2 = Video::create([
            'title' => 'TED: The Power of Listening',
            'description' => 'A TED talk about active listening skills with clear pronunciation. Ideal for B2 level learners.',
            'level' => 'B2',
            'duration_seconds' => 560,
            'source_type' => 'upload',
            'source_id' => 'interview-confidence.mp4',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1508704019882-f9cf40e475b4?auto=format&fit=crop&w=900&q=80',
            'language' => 'en',
            'topic_tags' => ['TED', 'Listening', 'Psychology'],
            'is_published' => true,
            'is_featured' => false,
        ]);

        // Create transcript for video 2
        $transcript2 = Transcript::create([
            'video_id' => $video2->id,
            'language' => 'en',
        ]);

        $segments2 = [
            [
                'start_time' => 0,
                'end_time' => 5,
                'text' => 'We learn by listening deeply before we respond. Silence creates the space for meaning to land.',
                'position' => 1,
            ],
            [
                'start_time' => 5,
                'end_time' => 10,
                'text' => 'Most people don\'t really listen. They\'re just waiting for their turn to speak.',
                'position' => 2,
            ],
            [
                'start_time' => 10,
                'end_time' => 15,
                'text' => 'Active listening is a skill that can be learned and developed over time.',
                'position' => 3,
            ],
            [
                'start_time' => 15,
                'end_time' => 20,
                'text' => 'When you truly listen, you show respect and build deeper connections with others.',
                'position' => 4,
            ],
        ];

        foreach ($segments2 as $segment) {
            TranscriptSegment::create(array_merge($segment, [
                'transcript_id' => $transcript2->id,
            ]));
        }

        // Video 3: Mini Lesson
        $video3 = Video::create([
            'title' => 'Mini Lesson: Clear Intonation',
            'description' => 'A short structured lesson about English intonation with natural pauses for shadowing practice.',
            'level' => 'A2',
            'duration_seconds' => 300,
            'source_type' => 'upload',
            'source_id' => 'interview-confidence.mp4',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1517638851339-4aa32003c11a?auto=format&fit=crop&w=900&q=80',
            'language' => 'en',
            'topic_tags' => ['Lesson', 'Pronunciation', 'Beginner'],
            'is_published' => true,
            'is_featured' => false,
        ]);

        // Create transcript for video 3
        $transcript3 = Transcript::create([
            'video_id' => $video3->id,
            'language' => 'en',
        ]);

        $segments3 = [
            [
                'start_time' => 0,
                'end_time' => 4,
                'text' => 'Hello everyone, today we\'re going to learn about intonation in English.',
                'position' => 1,
            ],
            [
                'start_time' => 4,
                'end_time' => 8,
                'text' => 'Intonation is the rise and fall of your voice when you speak.',
                'position' => 2,
            ],
            [
                'start_time' => 8,
                'end_time' => 12,
                'text' => 'For questions, your voice usually goes up at the end.',
                'position' => 3,
            ],
            [
                'start_time' => 12,
                'end_time' => 16,
                'text' => 'For statements, your voice usually goes down at the end.',
                'position' => 4,
            ],
            [
                'start_time' => 16,
                'end_time' => 20,
                'text' => 'Let\'s practice together now. Repeat after me.',
                'position' => 5,
            ],
        ];

        foreach ($segments3 as $segment) {
            TranscriptSegment::create(array_merge($segment, [
                'transcript_id' => $transcript3->id,
            ]));
        }
    }
}
