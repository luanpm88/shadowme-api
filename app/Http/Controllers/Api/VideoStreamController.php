<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Request;

class VideoStreamController
{
    public function stream(Request $request, string $filename): StreamedResponse
    {
        $safeFilename = basename($filename);
        $relativePath = "videos/{$safeFilename}";
        $disk = Storage::disk('public');

        if (!$disk->exists($relativePath)) {
            abort(404, 'Video not found');
        }

        $file = $disk->path($relativePath);
        $mimeType = $disk->mimeType($relativePath) ?? 'video/mp4';
        $fileSize = filesize($file);
        $start = 0;
        $end = $fileSize - 1;
        $status = 200;

        $range = $request->header('Range');
        if ($range && preg_match('/bytes=(\d+)-(\d*)/', $range, $matches)) {
            $start = (int) $matches[1];
            if ($matches[2] !== '') {
                $end = (int) $matches[2];
            }
            if ($end > $fileSize - 1) {
                $end = $fileSize - 1;
            }
            if ($start > $end) {
                $start = 0;
            }
            $status = 206;
        }

        $length = $end - $start + 1;

        return response()->stream(
            function () use ($file, $start, $length) {
                $handle = fopen($file, 'rb');
                if ($handle === false) {
                    return;
                }
                fseek($handle, $start);
                $remaining = $length;
                $bufferSize = 1024 * 1024;
                while ($remaining > 0 && !feof($handle)) {
                    $read = fread($handle, (int) min($bufferSize, $remaining));
                    if ($read === false) {
                        break;
                    }
                    $remaining -= strlen($read);
                    echo $read;
                    flush();
                }
                fclose($handle);
            },
            $status,
            [
                'Content-Type' => $mimeType,
                'Content-Length' => $length,
                'Content-Disposition' => 'inline; filename="' . basename($file) . '"',
                'Cache-Control' => 'public, max-age=86400',
                'Accept-Ranges' => 'bytes',
                'Content-Range' => "bytes {$start}-{$end}/{$fileSize}",
            ]
        );
    }
}
