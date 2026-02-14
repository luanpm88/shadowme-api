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
                        'start_time' => 63,
                        'end_time' => 67.1,
                        'text' => 'We know people are like this conversation, and it very hard to listen to somebody',
                    ],
                    [
                        'start_time' => 212.2,
                        'end_time' => 215,
                        'text' => 'A friend of mine described it as standing in your own truth,',
                    ],
                    [
                        'start_time' => 232.7,
                        'end_time' => 235.67,
                        'text' => 'First of all, I think absolute honesty may not be what we want.',
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
                        'start_time' => 63,
                        'end_time' => 67.1,
                        'text' => 'We know people are like this conversation, and it very hard to listen to somebody',
                    ],
                    [
                        'start_time' => 212.2,
                        'end_time' => 215,
                        'text' => 'A friend of mine described it as standing in your own truth,',
                    ],
                    [
                        'start_time' => 232.7,
                        'end_time' => 235.67,
                        'text' => 'First of all, I think absolute honesty may not be what we want.',
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
                        'start_time' => 63,
                        'end_time' => 67.1,
                        'text' => 'We know people are like this conversation, and it very hard to listen to somebody',
                    ],
                    [
                        'start_time' => 212.2,
                        'end_time' => 215,
                        'text' => 'A friend of mine described it as standing in your own truth,',
                    ],
                    [
                        'start_time' => 232.7,
                        'end_time' => 235.67,
                        'text' => 'First of all, I think absolute honesty may not be what we want.',
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
