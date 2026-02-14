<?php

namespace Database\Seeders;

use App\Models\Video;
use App\Models\Transcript;
use App\Models\TranscriptSegment;
use App\Services\VideoService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $videoService = new VideoService();

        // Define sample videos data structure
        $samples = [
            [
                'video' => [
                    'title' => 'Street Interview: Confidence in English',
                    'description' => 'A natural street interview with clear pacing and natural English conversation. Perfect for B1 level learners.',
                    'level' => 'B1',
                    'duration_seconds' => 420,
                    'source_type' => 'upload',
                    'language' => 'en',
                    'topic_tags' => ['Speaking', 'Real Life', 'Interview'],
                    'is_published' => true,
                    'is_featured' => true,
                ],
                'source_path' => database_path('sample/sample_video.mp4'),
                'thumbnail_url' => 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=900&q=80',
                'segments' => [
                    [
                        'start_time' => 0.0,
                        'end_time' => 4.5,
                        'text' => 'Hi, I\'m interviewing people on the street today about their English learning journey.',
                    ],
                    [
                        'start_time' => 4.5,
                        'end_time' => 8.5,
                        'text' => 'Many people find it intimidating to speak English in public, but it\'s actually a lot easier than they think.',
                    ],
                    [
                        'start_time' => 8.5,
                        'end_time' => 12.5,
                        'text' => 'The key is to practice consistently and not worry about making mistakes.',
                    ],
                    [
                        'start_time' => 12.5,
                        'end_time' => 16.5,
                        'text' => 'Everyone makes mistakes when learning a new language, and that\'s perfectly normal.',
                    ],
                    [
                        'start_time' => 16.5,
                        'end_time' => 20.5,
                        'text' => 'The most important thing is to just keep practicing and stay confident.',
                    ],
                ],
            ],
            [
                'video' => [
                    'title' => 'TED: The Power of Listening',
                    'description' => 'A TED talk about active listening skills with clear pronunciation. Ideal for B2 level learners.',
                    'level' => 'B2',
                    'duration_seconds' => 560,
                    'source_type' => 'upload',
                    'language' => 'en',
                    'topic_tags' => ['TED', 'Listening', 'Psychology'],
                    'is_published' => true,
                    'is_featured' => false,
                ],
                'source_path' => database_path('sample/sample_video.mp4'),
                'thumbnail_url' => 'https://images.unsplash.com/photo-1508704019882-f9cf40e475b4?auto=format&fit=crop&w=900&q=80',
                'segments' => [
                    [
                        'start_time' => 0.0,
                        'end_time' => 5.0,
                        'text' => 'We learn by listening deeply before we respond. Silence creates the space for meaning to land.'
                    ],
                    [
                        'start_time' => 5.0,
                        'end_time' => 10.0,
                        'text' => 'Most people don\'t really listen. They\'re just waiting for their turn to speak.',
                    ],
                    [
                        'start_time' => 10.0,
                        'end_time' => 15.0,
                        'text' => 'Active listening is a skill that can be learned and developed over time.',
                    ],
                    [
                        'start_time' => 15.0,
                        'end_time' => 20.0,
                        'text' => 'When you truly listen, you show respect and build deeper connections with others.',
                    ],
                ],
            ],
            [
                'video' => [
                    'title' => 'Mini Lesson: Clear Intonation',
                    'description' => 'A short structured lesson about English intonation with natural pauses for shadowing practice.',
                    'level' => 'A2',
                    'duration_seconds' => 300,
                    'source_type' => 'upload',
                    'language' => 'en',
                    'topic_tags' => ['Lesson', 'Pronunciation', 'Beginner'],
                    'is_published' => true,
                    'is_featured' => false,
                ],
                'source_path' => database_path('sample/sample_video.mp4'),
                'thumbnail_url' => 'https://images.unsplash.com/photo-1517638851339-4aa32003c11a?auto=format&fit=crop&w=900&q=80',
                'segments' => [
                    [
                        'start_time' => 0.0,
                        'end_time' => 4.5,
                        'text' => 'Hello everyone, today we\'re going to learn about intonation in English.'
                    ],
                    [
                        'start_time' => 4.5,
                        'end_time' => 8.5,
                        'text' => 'Intonation is the rise and fall of your voice when you speak.',
                    ],
                    [
                        'start_time' => 8.5,
                        'end_time' => 12.5,
                        'text' => 'For questions, your voice usually goes up at the end.',
                    ],
                    [
                        'start_time' => 12.5,
                        'end_time' => 16.5,
                        'text' => 'For statements, your voice usually goes down at the end.',
                    ],
                    [
                        'start_time' => 16.5,
                        'end_time' => 20.5,
                        'text' => 'Let\'s practice together now. Repeat after me.',
                    ],
                ],
            ],
        ];

        // Loop through samples and create videos with transcripts
        foreach ($samples as $sample) {
            // 1. Create video with initial default values
            $video = Video::create([
                ...$sample['video'],
                'source_ext' => 'mp4',
                'thumb_ext' => null,
            ]);

            // 2. Copy sample video to storage
            $videoSourcePath = $sample['source_path'];
            $videoDestDir = storage_path('app/public/videos/' . $video->id);
            @mkdir($videoDestDir, 0755, true);
            
            if (file_exists($videoSourcePath)) {
                copy($videoSourcePath, $videoDestDir . '/video.mp4');
            }

            // 3. Upload thumbnail using VideoService
            // If download fails, uploadResult will have thumb_ext=null
            // In that case, Video::getThumbUrl() will return the placeholder
            $uploadResult = $videoService->uploadFiles(
                $video->id,
                thumbnailUrl: $sample['thumbnail_url']
            );

            // 4. Update video with extensions from upload result
            // source_ext is set to 'mp4' since we just copied the video file
            $video->update([
                'source_ext' => 'mp4',
                'thumb_ext' => $uploadResult['thumb_ext'],
            ]);

            // 5. Create transcript for video
            $transcript = Transcript::create([
                'video_id' => $video->id,
                'language' => $video->language,
            ]);

            // 6. Create transcript segments
            foreach ($sample['segments'] as $position => $segment) {
                TranscriptSegment::create([
                    'transcript_id' => $transcript->id,
                    'start_time' => $segment['start_time'],
                    'end_time' => $segment['end_time'],
                    'text' => $segment['text'],
                    'position' => $position + 1,
                ]);
            }
        }
    }
}
