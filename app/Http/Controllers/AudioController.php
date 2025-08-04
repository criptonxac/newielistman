<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class AudioController extends Controller
{
    private string $tempChunkDir = 'chunks';

    public function upload(Request $request)

    {
        // PHP limitlarini runtime’da oshiramiz (fallback uchun)
        ini_set('upload_max_filesize', '100M');
        ini_set('post_max_size', '120M');
        ini_set('memory_limit', '256M');
        ini_set('max_execution_time', '600');
        ini_set('max_input_time', '600');

        // Laravel validatsiyasi
        $request->validate([
            'audio_file' => 'required|file|mimes:mp3,wav,ogg,flac,m4a|max:102400'
        ]);

        if (!$request->hasFile('audio_file')) {
            return response()->json(['success' => false, 'message' => 'No file uploaded'], 400);
        }

        $file = $request->file('audio_file');
        $path = $file->store('audios', 'public');

        return response()->json([
            'success' => true,
            'data' => [
                'url' => asset("storage/$path"),
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size_formatted' => $this->formatBytes($file->getSize()),
                'uploaded_at' => now()->toDateTimeString(),
                'extension' => $file->getClientOriginalExtension(),
                'full_url' => asset("storage/$path"),
                'duration_formatted' => null
            ]
        ]);
    }


    private function getUploadErrorMessage(int $errorCode): string
    {
        return match ($errorCode) {
            UPLOAD_ERR_OK => 'Xatolik yo\'q',
            UPLOAD_ERR_INI_SIZE => 'Fayl hajmi PHP konfiguratsiyasidagi limitdan katta',
            UPLOAD_ERR_FORM_SIZE => 'Fayl hajmi HTML forma limitidan katta',
            UPLOAD_ERR_PARTIAL => 'Fayl qisman yuklangan',
            UPLOAD_ERR_NO_FILE => 'Fayl yuklanmagan',
            UPLOAD_ERR_NO_TMP_DIR => 'Vaqtinchalik papka topilmadi',
            UPLOAD_ERR_CANT_WRITE => 'Faylni diskga yozib bo\'lmadi',
            UPLOAD_ERR_EXTENSION => 'PHP kengaytmasi fayl yuklashni to\'xtatdi',
            default => 'Noma\'lum xatolik: ' . $errorCode
        };
    }

    public function list(Request $request)
    {
        try {
            $testId = $request->input('test_id');
            $part = $request->input('part', 'part1');

            $searchPath = "public/audio" . ($testId ? "/test_$testId" : '') . "/$part";
            $files = [];

            if (Storage::exists($searchPath)) {
                foreach (Storage::allFiles($searchPath) as $filePath) {
                    $fullPath = storage_path('app/' . $filePath);

                    if (is_file($fullPath)) {
                        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                        if (in_array($extension, ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'flac', 'webm'])) {
                            $files[] = [
                                'filename' => basename($filePath),
                                'url' => Storage::url($filePath),
                                'full_url' => url(Storage::url($filePath)),
                                'size' => filesize($fullPath),
                                'size_formatted' => $this->formatBytes(filesize($fullPath)),
                                'extension' => $extension,
                                'modified' => date('Y-m-d H:i:s', filemtime($fullPath)),
                                'duration' => $this->getAudioDuration($fullPath),
                                'duration_formatted' => $this->formatDuration($this->getAudioDuration($fullPath))
                            ];
                        }
                    }
                }

                usort($files, fn($a, $b) => strcmp($a['filename'], $b['filename']));
            }

            return response()->json([
                'success' => true,
                'message' => count($files) . ' ta audio fayl topildi',
                'data' => $files,
                'meta' => [
                    'total_count' => count($files),
                    'search_path' => $searchPath,
                    'test_id' => $testId,
                    'part' => $part
                ]
            ]);
        } catch (Exception $e) {
            Log::error('List files error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ro\'yxatni olishda xatolik: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getAudioDuration(string $filePath): ?float
    {
        try {
            if (class_exists('\\getID3')) {
                $getID3 = new \getID3;
                $fileInfo = $getID3->analyze($filePath);
                return isset($fileInfo['playtime_seconds']) ? round($fileInfo['playtime_seconds'], 2) : null;
            }

            if (function_exists('shell_exec') && shell_exec('which ffprobe')) {
                $output = shell_exec("ffprobe -v quiet -show_entries format=duration -of csv=p=0 " . escapeshellarg($filePath));
                return is_numeric(trim($output)) ? round(floatval(trim($output)), 2) : null;
            }

            if (function_exists('shell_exec') && shell_exec('which ffmpeg')) {
                $output = shell_exec("ffmpeg -i " . escapeshellarg($filePath) . " 2>&1 | grep Duration | cut -d ' ' -f 4 | sed s/,//");
                if (preg_match('/(\d+):(\d+):(\d+\.?\d*)/', trim($output), $matches)) {
                    return intval($matches[1]) * 3600 + intval($matches[2]) * 60 + floatval($matches[3]);
                }
            }
        } catch (Exception $e) {
            Log::warning('Could not get audio duration', ['file' => $filePath, 'error' => $e->getMessage()]);
        }

        return null;
    }

    private function formatBytes(int $bytes, int $precision = 2): string
    {
        if ($bytes === 0) return '0 B';

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = min(floor(log($bytes, 1024)), count($units) - 1);

        return round($bytes / (1024 ** $pow), $precision) . ' ' . $units[$pow];
    }

    private function formatDuration(?float $seconds): string
    {
        if (!$seconds || $seconds < 0) return '0:00';

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = floor($seconds % 60);

        return $hours > 0 ? sprintf('%d:%02d:%02d', $hours, $minutes, $seconds) : sprintf('%d:%02d', $minutes, $seconds);
    }
}
