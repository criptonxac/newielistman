<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class AudioController extends Controller
{
    /**
     * Constructor - middleware qo'shishingiz mumkin
     */
    public function __construct()
    {
        // Agar kerak bo'lsa middleware qo'shing
        // $this->middleware('auth');
        // $this->middleware('role:admin,teacher');
    }

    /**
     * Test endpoint - sistemani tekshirish uchun
     * URL: /audio/test
     */
    public function test()
    {
        $audioPath = storage_path('app/public/audio');
        
        return response()->json([
            'success' => true,
            'message' => 'Audio controller ishlayapti!',
            'data' => [
                'timestamp' => now(),
                'storage_path' => $audioPath,
                'storage_exists' => is_dir($audioPath),
                'storage_writable' => is_writable(storage_path('app/public')),
                'public_path' => public_path('storage'),
                'symlink_exists' => is_link(public_path('storage')),
                'php_upload_max_filesize' => ini_get('upload_max_filesize'),
                'php_post_max_size' => ini_get('post_max_size'),
                'php_max_execution_time' => ini_get('max_execution_time')
            ]
        ]);
    }

    /**
     * Audio fayl yuklash
     * URL: POST /audio/upload
     */
    public function upload(Request $request)
    {
        // Debug uchun request ma'lumotlarini log qilish
        Log::info('Audio upload request started', [
            'method' => $request->method(),
            'has_file' => $request->hasFile('audio_file'),
            'content_type' => $request->header('Content-Type'),
            'content_length' => $request->header('Content-Length'),
            'user_agent' => $request->header('User-Agent'),
            'all_files' => count($request->allFiles()),
            'ip' => $request->ip()
        ]);

        try {
            // 1. Asosiy validatsiya
            $validated = $request->validate([
                'audio_file' => [
                    'required',
                    'file',
                    'max:102400', // 100MB
                    'mimes:mp3,wav,ogg,m4a,aac,flac'
                ],
                'part' => 'nullable|string|in:part1,part2,part3,part4',
                'test_id' => 'nullable|integer',
                'question_id' => 'nullable|integer'
            ], [
                'audio_file.required' => 'Audio fayl tanlanmagan',
                'audio_file.file' => 'Yuklangan element fayl emas',
                'audio_file.max' => 'Fayl hajmi 100MB dan oshmasligi kerak',
                'audio_file.mimes' => 'Faqat audio fayllar (MP3, WAV, OGG, M4A, AAC, FLAC) ruxsat etilgan'
            ]);

            $file = $request->file('audio_file');
            
            // 2. Fayl tekshiruvi
            if (!$file || !$file->isValid()) {
                Log::error('Invalid file uploaded', [
                    'file_error' => $file ? $file->getError() : 'No file',
                    'file_error_message' => $file ? $file->getErrorMessage() : 'No file provided'
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Fayl yuklashda xatolik yuz berdi'
                ], 400);
            }

            // 3. Fayl ma'lumotlarini olish
            $originalName = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());
            $size = $file->getSize();
            $mimeType = $file->getMimeType();
            $part = $validated['part'] ?? 'part1';
            $testId = $validated['test_id'] ?? null;

            Log::info('File details', [
                'original_name' => $originalName,
                'extension' => $extension,
                'size' => $size,
                'mime_type' => $mimeType,
                'part' => $part,
                'test_id' => $testId
            ]);

            // 4. Qo'shimcha validatsiya
            $allowedExtensions = ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'flac'];
            if (!in_array($extension, $allowedExtensions)) {
                return response()->json([
                    'success' => false,
                    'message' => "Qo'llab-quvvatlanmaydigan format: {$extension}"
                ], 400);
            }

            // 5. Fayl nomini yaratish
            $cleanName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));
            $timestamp = time();
            $random = Str::random(8);
            
            $filename = sprintf(
                'audio_%s_%s_%s_%s.%s',
                $part,
                $cleanName,
                $timestamp,
                $random,
                $extension
            );

            // 6. Saqlash yo'lini belgilash
            $directory = 'audio';
            if ($testId) {
                $directory .= "/test_{$testId}";
            }
            if ($part) {
                $directory .= "/{$part}";
            }

            // 7. Faylni saqlash
            $storagePath = "public/{$directory}";
            $filePath = $file->storeAs($storagePath, $filename);
            
            if (!$filePath) {
                Log::error('Failed to store file', [
                    'storage_path' => $storagePath,
                    'filename' => $filename
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Faylni saqlashda xatolik yuz berdi'
                ], 500);
            }

            // 8. URL yaratish
            $publicUrl = Storage::url($filePath);
            $fullUrl = url($publicUrl);
            $fullPath = storage_path('app/' . $filePath);

            // 9. Fayl mavjudligini tekshirish
            if (!file_exists($fullPath)) {
                Log::error('File was not saved properly', [
                    'file_path' => $filePath,
                    'full_path' => $fullPath
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Fayl to\'g\'ri saqlanmadi'
                ], 500);
            }

            // 10. Haqiqiy fayl ma'lumotlari
            $actualSize = filesize($fullPath);
            $duration = $this->getAudioDuration($fullPath);

            Log::info('File uploaded successfully', [
                'filename' => $filename,
                'file_path' => $filePath,
                'public_url' => $publicUrl,
                'full_url' => $fullUrl,
                'actual_size' => $actualSize,
                'duration' => $duration
            ]);

            // 11. Muvaffaqiyatli javob
            return response()->json([
                'success' => true,
                'message' => 'Audio fayl muvaffaqiyatli yuklandi',
                'data' => [
                    'id' => uniqid('audio_'),
                    'filename' => $filename,
                    'original_name' => $originalName,
                    'path' => str_replace('public/', '', $filePath),
                    'url' => $publicUrl,
                    'full_url' => $fullUrl,
                    'size' => $actualSize,
                    'size_formatted' => $this->formatBytes($actualSize),
                    'mime_type' => $mimeType,
                    'extension' => $extension,
                    'part' => $part,
                    'test_id' => $testId,
                    'duration' => $duration,
                    'duration_formatted' => $duration ? $this->formatDuration($duration) : null,
                    'uploaded_at' => now()->toISOString()
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed', [
                'errors' => $e->errors(),
                'input' => $request->except(['audio_file'])
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validatsiya xatoligi',
                'errors' => $e->errors()
            ], 422);

        } catch (Exception $e) {
            Log::error('Unexpected upload error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server xatoligi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Audio fayllar ro'yxati
     * URL: GET /audio/list
     */
    public function list(Request $request)
    {
        try {
            $testId = $request->input('test_id');
            $part = $request->input('part');
            $limit = $request->input('limit', 50);
            $offset = $request->input('offset', 0);

            // Asosiy yo'l
            $searchPath = 'public/audio';
            if ($testId) {
                $searchPath .= "/test_{$testId}";
            }
            if ($part) {
                $searchPath .= "/{$part}";
            }

            $files = [];
            $allFiles = Storage::allFiles($searchPath);

            foreach ($allFiles as $filePath) {
                $fullPath = storage_path('app/' . $filePath);
                
                if (is_file($fullPath)) {
                    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                    $allowedExtensions = ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'flac'];
                    
                    if (in_array($extension, $allowedExtensions)) {
                        $filename = basename($filePath);
                        $size = filesize($fullPath);
                        $url = Storage::url($filePath);
                        
                        $files[] = [
                            'id' => md5($filePath),
                            'filename' => $filename,
                            'original_name' => $this->extractOriginalName($filename),
                            'path' => str_replace('public/', '', $filePath),
                            'url' => $url,
                            'full_url' => url($url),
                            'size' => $size,
                            'size_formatted' => $this->formatBytes($size),
                            'extension' => $extension,
                            'mime_type' => $this->getMimeTypeByExtension($extension),
                            'modified' => filemtime($fullPath),
                            'modified_formatted' => date('Y-m-d H:i:s', filemtime($fullPath)),
                            'part' => $this->extractPartFromPath($filePath),
                            'test_id' => $this->extractTestIdFromPath($filePath)
                        ];
                    }
                }
            }

            // Sana bo'yicha tartiblash (yangilar birinchi)
            usort($files, function($a, $b) {
                return $b['modified'] - $a['modified'];
            });

            // Pagination
            $total = count($files);
            $files = array_slice($files, $offset, $limit);

            Log::info('Files listed', [
                'search_path' => $searchPath,
                'total_found' => $total,
                'returned' => count($files),
                'test_id' => $testId,
                'part' => $part
            ]);

            return response()->json([
                'success' => true,
                'data' => $files,
                'meta' => [
                    'total' => $total,
                    'count' => count($files),
                    'limit' => $limit,
                    'offset' => $offset
                ]
            ]);

        } catch (Exception $e) {
            Log::error('List files error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Fayllar ro\'yxatini olishda xatolik'
            ], 500);
        }
    }

    /**
     * Audio faylni streaming
     * URL: GET /audio/stream/{filename}
     */
    public function stream($filename)
    {
        try {
            // Xavfsizlik uchun fayl nomini tozalash
            $filename = basename($filename);
            
            // Faylni topish
            $filePath = $this->findAudioFile($filename);
            
            if (!$filePath) {
                Log::warning('Audio file not found for streaming', [
                    'filename' => $filename
                ]);
                
                return response()->json([
                    'error' => 'Audio fayl topilmadi'
                ], 404);
            }

            $fullPath = storage_path('app/' . $filePath);
            
            if (!file_exists($fullPath) || !is_readable($fullPath)) {
                return response()->json([
                    'error' => 'Fayl mavjud emas yoki o\'qib bo\'lmaydi'
                ], 404);
            }

            // Fayl ma'lumotlari
            $fileSize = filesize($fullPath);
            $mimeType = $this->getMimeTypeByExtension(pathinfo($fullPath, PATHINFO_EXTENSION));

            Log::info('Streaming audio file', [
                'filename' => $filename,
                'file_path' => $filePath,
                'size' => $fileSize,
                'mime_type' => $mimeType
            ]);

            // Headers
            $headers = [
                'Content-Type' => $mimeType,
                'Accept-Ranges' => 'bytes',
                'Content-Length' => $fileSize,
                'Cache-Control' => 'public, max-age=3600',
                'Pragma' => 'public'
            ];

            return response()->file($fullPath, $headers);

        } catch (Exception $e) {
            Log::error('Audio streaming error', [
                'filename' => $filename,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Audio faylni yuklashda xatolik'
            ], 500);
        }
    }

    /**
     * Audio faylni o'chirish
     * URL: DELETE /audio/delete
     */
    public function delete(Request $request)
    {
        try {
            $filename = $request->input('filename');
            $fileId = $request->input('file_id');
            
            if (!$filename && !$fileId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fayl nomi yoki ID ko\'rsatilmagan'
                ], 400);
            }

            // Faylni topish
            $filePath = null;
            if ($filename) {
                $filePath = $this->findAudioFile($filename);
            } elseif ($fileId) {
                $filePath = $this->findAudioFileById($fileId);
            }

            if (!$filePath) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fayl topilmadi'
                ], 404);
            }

            // Faylni o'chirish
            if (Storage::exists($filePath)) {
                Storage::delete($filePath);
                
                Log::info('Audio file deleted', [
                    'filename' => $filename,
                    'file_id' => $fileId,
                    'file_path' => $filePath
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Audio fayl o\'chirildi'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Fayl mavjud emas'
                ], 404);
            }

        } catch (Exception $e) {
            Log::error('Delete audio file error', [
                'filename' => $request->input('filename'),
                'file_id' => $request->input('file_id'),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Faylni o\'chirishda xatolik'
            ], 500);
        }
    }

    /**
     * Audio faylni topish
     */
    private function findAudioFile($filename)
    {
        $searchPaths = [
            'public/audio',
            'public/audio/test_*',
            'public/audio/test_*/part*',
            'public/audio/part*'
        ];

        foreach ($searchPaths as $pattern) {
            $files = Storage::allFiles($pattern);
            foreach ($files as $filePath) {
                if (basename($filePath) === $filename) {
                    return $filePath;
                }
            }
        }

        return null;
    }

    /**
     * ID bo'yicha audio faylni topish
     */
    private function findAudioFileById($fileId)
    {
        $files = Storage::allFiles('public/audio');
        foreach ($files as $filePath) {
            if (md5($filePath) === $fileId) {
                return $filePath;
            }
        }
        return null;
    }

    /**
     * Audio davomiyligini olish
     */
    private function getAudioDuration($filePath)
    {
        try {
            // getID3 kutubxonasi bilan
            if (class_exists('\getID3')) {
                $getID3 = new \getID3;
                $fileInfo = $getID3->analyze($filePath);
                if (isset($fileInfo['playtime_seconds'])) {
                    return round($fileInfo['playtime_seconds'], 2);
                }
            }

            // ffprobe bilan
            if (function_exists('shell_exec') && $this->commandExists('ffprobe')) {
                $command = "ffprobe -v quiet -show_entries format=duration -of csv=p=0 " . escapeshellarg($filePath) . " 2>/dev/null";
                $output = shell_exec($command);
                if ($output && is_numeric(trim($output))) {
                    return round(floatval(trim($output)), 2);
                }
            }

            return null;
        } catch (Exception $e) {
            Log::debug('Could not get audio duration', [
                'file' => $filePath,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Command mavjudligini tekshirish
     */
    private function commandExists($cmd)
    {
        $return = shell_exec("which {$cmd} 2>/dev/null");
        return !empty($return);
    }

    /**
     * Kengaytma bo'yicha MIME type olish
     */
    private function getMimeTypeByExtension($extension)
    {
        $mimeTypes = [
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'ogg' => 'audio/ogg',
            'm4a' => 'audio/mp4',
            'aac' => 'audio/aac',
            'flac' => 'audio/flac'
        ];

        return $mimeTypes[strtolower($extension)] ?? 'audio/mpeg';
    }

    /**
     * Fayl nomidan asl nomni ajratib olish
     */
    private function extractOriginalName($filename)
    {
        // Format: audio_part1_clean-name_timestamp_random.ext
        $parts = explode('_', pathinfo($filename, PATHINFO_FILENAME));
        if (count($parts) >= 4) {
            // part va timestamp, random ni olib tashlash
            $nameParts = array_slice($parts, 2, -2);
            return implode('_', $nameParts);
        }
        return pathinfo($filename, PATHINFO_FILENAME);
    }

    /**
     * Yo'ldan part nomini ajratib olish
     */
    private function extractPartFromPath($path)
    {
        if (preg_match('/\/(part\d+)\//', $path, $matches)) {
            return $matches[1];
        }
        if (preg_match('/_(part\d+)_/', basename($path), $matches)) {
            return $matches[1];
        }
        return 'unknown';
    }

    /**
     * Yo'ldan test ID sini ajratib olish
     */
    private function extractTestIdFromPath($path)
    {
        if (preg_match('/\/test_(\d+)\//', $path, $matches)) {
            return (int)$matches[1];
        }
        return null;
    }

    /**
     * Baytlarni formatga o'tkazish
     */
    private function formatBytes($bytes, $precision = 2)
    {
        if ($bytes == 0) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Davomiylikni formatga o'tkazish
     */
    private function formatDuration($seconds)
    {
        if (!$seconds || $seconds < 0) return '0:00';
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = floor($seconds % 60);
        
        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        } else {
            return sprintf('%d:%02d', $minutes, $seconds);
        }
    }
}