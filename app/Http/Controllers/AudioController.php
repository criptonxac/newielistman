<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class AudioController extends Controller
{
    // Temporary directory for chunk storage
    private $tempChunkDir = 'chunks';
    /**
     * Audio fayl yuklash - FIXED VERSION
     */
    public function upload(Request $request)
    {
        $request->validate([
            'audio_file' => 'required|file|mimes:mp3,wav,ogg,flac,m4a|max:102400' // 100MB
        ]);

        if ($request->hasFile('audio_file')) {
            $path = $request->file('audio_file')->store('audios', 'public');
            return response()->json([
                'success' => true,
                'data' => [
                    'url' => asset('storage/' . $path),
                    'original_name' => $request->file('audio_file')->getClientOriginalName(),
                    'mime_type' => $request->file('audio_file')->getMimeType(),
                    'size_formatted' => $this->formatBytes($request->file('audio_file')->getSize()),
                    'uploaded_at' => now()->toDateTimeString(),
                    'extension' => $request->file('audio_file')->getClientOriginalExtension(),
                    'full_url' => asset('storage/' . $path),
                    'duration_formatted' => null // optional
                ]
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No file uploaded'], 400);
    }

    /**
     * Upload xatoliklarini tushunarli qilish
     */
    private function getUploadErrorMessage($errorCode)
    {
        switch ($errorCode) {
            case UPLOAD_ERR_OK:
                return 'Xatolik yo\'q';
            case UPLOAD_ERR_INI_SIZE:
                return 'Fayl hajmi PHP konfiguratsiyasidagi limitdan katta';
            case UPLOAD_ERR_FORM_SIZE:
                return 'Fayl hajmi HTML forma limitidan katta';
            case UPLOAD_ERR_PARTIAL:
                return 'Fayl qisman yuklangan';
            case UPLOAD_ERR_NO_FILE:
                return 'Fayl yuklanmagan';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Vaqtinchalik papka topilmadi';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Faylni diskga yozib bo\'lmadi';
            case UPLOAD_ERR_EXTENSION:
                return 'PHP kengaytmasi fayl yuklashni to\'xtatdi';
            default:
                return 'Noma\'lum xatolik: ' . $errorCode;
        }
    }


    /**
     * Audio fayllar ro'yxati
     */
    public function list(Request $request)
    {
        try {
            $testId = $request->input('test_id');
            $part = $request->input('part', 'part1');

            $searchPath = 'public/audio';
            if ($testId) {
                $searchPath .= "/test_{$testId}";
            }
            $searchPath .= "/{$part}";

            $files = [];
            if (Storage::exists($searchPath)) {
                $allFiles = Storage::allFiles($searchPath);

                foreach ($allFiles as $filePath) {
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

                // Fayl nomiga ko'ra tartiblash
                usort($files, function($a, $b) {
                    return strcmp($a['filename'], $b['filename']);
                });
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
            ], 200, ['Content-Type' => 'application/json']);

        } catch (Exception $e) {
            Log::error('List files error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ro\'yxatni olishda xatolik: ' . $e->getMessage()
            ], 500, ['Content-Type' => 'application/json']);
        }
    }

    /**
     * Audio davomiyligini olish
     */
    private function getAudioDuration($filePath)
    {
        try {
            // getID3 kutubxonasi
            if (class_exists('\getID3')) {
                $getID3 = new \getID3;
                $fileInfo = $getID3->analyze($filePath);
                if (isset($fileInfo['playtime_seconds']) && is_numeric($fileInfo['playtime_seconds'])) {
                    return round($fileInfo['playtime_seconds'], 2);
                }
            }

            // ffprobe (agar mavjud bo'lsa)
            if (function_exists('shell_exec') && !empty(shell_exec('which ffprobe'))) {
                $command = "ffprobe -v quiet -show_entries format=duration -of csv=p=0 " . escapeshellarg($filePath) . " 2>/dev/null";
                $output = shell_exec($command);
                if ($output && is_numeric(trim($output))) {
                    return round(floatval(trim($output)), 2);
                }
            }

            // ffmpeg (agar mavjud bo'lsa)
            if (function_exists('shell_exec') && !empty(shell_exec('which ffmpeg'))) {
                $command = "ffmpeg -i " . escapeshellarg($filePath) . " 2>&1 | grep Duration | cut -d ' ' -f 4 | sed s/,//";
                $output = shell_exec($command);
                if ($output && preg_match('/(\d+):(\d+):(\d+\.\d+)/', trim($output), $matches)) {
                    $hours = intval($matches[1]);
                    $minutes = intval($matches[2]);
                    $seconds = floatval($matches[3]);
                    return $hours * 3600 + $minutes * 60 + $seconds;
                }
            }

            return null;
        } catch (Exception $e) {
            Log::warning('Could not get audio duration', [
                'file' => $filePath,
                'error' => $e->getMessage()
            ]);
            return null;
        }
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

    /**
     * Upload a chunk of an audio file
     */
    public function uploadChunk(Request $request)
    {
        try {
            ini_set('upload_max_filesize', '100M');
            ini_set('post_max_size', '100M');
            ini_set('memory_limit', '256M');
            ini_set('max_execution_time', '600');
            ini_set('max_input_time', '600');
            set_time_limit(600);

            Log::info('Chunk upload request received', [
                'ip' => $request->ip(),
                'content_length' => $request->header('Content-Length'),
                'file_count' => count($request->allFiles()),
                'params' => $request->except(['file', 'chunk']),
            ]);

            if (empty($request->allFiles())) {
                Log::warning('No files found in chunk request', [
                    'all_input' => $request->all()
                ]);
                return response()->json(['success' => false, 'message' => 'Chunk fayli topilmadi.'], 400);
            }

            return $this->handleChunkedUpload($request);

        } catch (Exception $e) {
            Log::error('Unexpected error in uploadChunk', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Chunk yuklashda kutilmagan xatolik: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Finalize a chunked upload
     */
    public function finalizeUpload(Request $request)
    {
        try {
            ini_set('upload_max_filesize', '100M');
            ini_set('post_max_size', '100M');
            ini_set('memory_limit', '256M');
            ini_set('max_execution_time', '600');
            ini_set('max_input_time', '600');
            set_time_limit(600);

            Log::info('Finalize upload request', [
                'ip' => $request->ip(),
                'file_id' => $request->input('file_id'),
                'file_name' => $request->input('file_name'),
                'file_size' => $request->input('file_size'),
            ]);

            if (!$request->has('file_id') || !$request->has('file_name') || !$request->has('file_size')) {
                Log::warning('Missing required parameters in finalize request', [
                    'all_input' => $request->all()
                ]);
                return response()->json(['success' => false, 'message' => 'Zarur parametrlar etishmayapti'], 400);
            }

            return $this->finalizeChunkedUpload($request);

        } catch (Exception $e) {
            Log::error('Unexpected error in finalizeUpload', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Yuklashni yakunlashda kutilmagan xatolik: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Handle chunked upload process
     */
    private function handleChunkedUpload(Request $request)
    {
        try {
            // Validate chunk metadata
            $validated = $request->validate([
                'chunk_index' => 'required|integer|min:0',
                'total_chunks' => 'required|integer|min:1',
                'file_id' => 'required|string',
                'file_name' => 'required|string',
                'file_size' => 'required|integer|min:1',
            ], [
                'chunk_index.required' => 'Chunk indeksi ko\'rsatilmagan',
                'total_chunks.required' => 'Umumiy chunk soni ko\'rsatilmagan',
                'file_id.required' => 'Fayl ID si ko\'rsatilmagan',
                'file_name.required' => 'Fayl nomi ko\'rsatilmagan',
                'file_size.required' => 'Fayl hajmi ko\'rsatilmagan',
            ]);

            // Detect file key in request
            $fileKey = $this->detectFileKey($request);
            if (!$fileKey) {
                Log::error('No chunk file found in request');
                return response()->json(['success' => false, 'message' => 'Chunk fayli topilmadi'], 400);
            }

            // Validate chunk file
            $chunkFile = $request->file($fileKey);
            if (!$chunkFile || !$chunkFile->isValid()) {
                Log::error('Invalid chunk file', [
                    'error' => $chunkFile ? $chunkFile->getError() : 'No file',
                    'error_message' => $chunkFile ? $this->getUploadErrorMessage($chunkFile->getError()) : 'No file'
                ]);
                return response()->json(['success' => false, 'message' => 'Noto\'g\'ri chunk fayli'], 400);
            }

            // Create temp directory for chunks if it doesn't exist
            $chunkDir = storage_path('app/tmp/' . $this->tempChunkDir . '/' . $validated['file_id']);
            if (!file_exists($chunkDir)) {
                if (!mkdir($chunkDir, 0755, true)) {
                    Log::error('Failed to create chunk directory', ['dir' => $chunkDir]);
                    return response()->json(['success' => false, 'message' => 'Chunk papkasini yaratib bo\'lmadi'], 500);
                }
            }

            // Save chunk file
            $chunkPath = $chunkDir . '/chunk_' . $validated['chunk_index'];
            if (!$chunkFile->move($chunkDir, 'chunk_' . $validated['chunk_index'])) {
                Log::error('Failed to save chunk file', ['path' => $chunkPath]);
                return response()->json(['success' => false, 'message' => 'Chunk faylini saqlashda xatolik'], 500);
            }

            // Update info file with received chunks
            $infoPath = $chunkDir . '/info.json';
            $info = [];

            if (file_exists($infoPath)) {
                $info = json_decode(file_get_contents($infoPath), true) ?: [];
            }

            $info['file_id'] = $validated['file_id'];
            $info['file_name'] = $validated['file_name'];
            $info['file_size'] = $validated['file_size'];
            $info['total_chunks'] = $validated['total_chunks'];
            $info['received_chunks'] = isset($info['received_chunks']) ? $info['received_chunks'] : [];

            if (!in_array($validated['chunk_index'], $info['received_chunks'])) {
                $info['received_chunks'][] = $validated['chunk_index'];
            }

            file_put_contents($infoPath, json_encode($info));

            // Calculate progress
            $totalChunks = $validated['total_chunks'];
            $receivedChunks = count($info['received_chunks']);
            $progress = ($receivedChunks / $totalChunks) * 100;

            Log::info('Chunk saved successfully', [
                'chunk_index' => $validated['chunk_index'],
                'total_chunks' => $totalChunks,
                'received_chunks' => $receivedChunks,
                'progress' => $progress
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Chunk yuklandi',
                'data' => [
                    'file_id' => $validated['file_id'],
                    'chunk_index' => $validated['chunk_index'],
                    'total_chunks' => $totalChunks,
                    'received_chunks' => $receivedChunks,
                    'progress' => round($progress, 2)
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Error handling chunked upload', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Chunk yuklashda xatolik: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Finalize chunked upload by merging chunks
     */
    private function finalizeChunkedUpload(Request $request)
    {
        try {
            // Get request data
            $fileId = $request->input('file_id');
            $fileName = $request->input('file_name');
            $fileSize = (int)$request->input('file_size');
            $testId = $request->input('test_id');
            $part = $request->input('part', 'part1');

            // Check if chunk directory exists
            $chunkDir = storage_path('app/tmp/' . $this->tempChunkDir . '/' . $fileId);
            if (!file_exists($chunkDir)) {
                Log::error('Chunk directory not found', ['dir' => $chunkDir]);
                return response()->json(['success' => false, 'message' => 'Chunk papkasi topilmadi'], 400);
            }

            // Check info file
            $infoPath = $chunkDir . '/info.json';
            if (!file_exists($infoPath)) {
                Log::error('Chunk info file not found', ['path' => $infoPath]);
                return response()->json(['success' => false, 'message' => 'Chunk ma\'lumotlari topilmadi'], 400);
            }

            // Read info file
            $info = json_decode(file_get_contents($infoPath), true);
            if (!$info) {
                Log::error('Invalid chunk info file', ['path' => $infoPath]);
                return response()->json(['success' => false, 'message' => 'Chunk ma\'lumotlari noto\'g\'ri'], 400);
            }

            // Verify all chunks are received
            $totalChunks = $info['total_chunks'];
            $receivedChunks = count($info['received_chunks']);

            if ($receivedChunks < $totalChunks) {
                Log::warning('Not all chunks received', [
                    'received' => $receivedChunks,
                    'total' => $totalChunks,
                    'missing' => array_diff(range(0, $totalChunks - 1), $info['received_chunks'])
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Barcha chunklar qabul qilinmagan',
                    'data' => [
                        'received' => $receivedChunks,
                        'total' => $totalChunks
                    ]
                ], 400);
            }

            // Create temp file for merging
            $tempMergedPath = $chunkDir . '/' . $fileName;
            $mergedFile = fopen($tempMergedPath, 'wb');

            if (!$mergedFile) {
                Log::error('Failed to create merged file', ['path' => $tempMergedPath]);
                return response()->json(['success' => false, 'message' => 'Faylni birlashtirish uchun yaratib bo\'lmadi'], 500);
            }

            // Merge chunks in order
            for ($i = 0; $i < $totalChunks; $i++) {
                $chunkPath = $chunkDir . '/chunk_' . $i;
                if (!file_exists($chunkPath)) {
                    Log::error('Chunk file missing during merge', ['chunk' => $i, 'path' => $chunkPath]);
                    fclose($mergedFile);
                    return response()->json(['success' => false, 'message' => 'Chunk fayli topilmadi: ' . $i], 500);
                }

                $chunkContent = file_get_contents($chunkPath);
                fwrite($mergedFile, $chunkContent);
                unset($chunkContent); // Free memory
            }

            fclose($mergedFile);

            // Verify file size with 5% tolerance
            $actualSize = filesize($tempMergedPath);
            $sizeDiff = abs($actualSize - $fileSize);
            $tolerance = $fileSize * 0.05; // 5% tolerance

            if ($sizeDiff > $tolerance) {
                Log::error('File size mismatch', [
                    'expected' => $fileSize,
                    'actual' => $actualSize,
                    'difference' => $sizeDiff,
                    'tolerance' => $tolerance
                ]);
                return response()->json(['success' => false, 'message' => 'Fayl hajmi mos kelmaydi'], 400);
            }

            // Create UploadedFile instance from merged file
            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $tempMergedPath,
                $fileName,
                mime_content_type($tempMergedPath),
                null,
                true // Test mode to avoid moving the file again
            );

            // Process the file as a normal upload
            $result = $this->processAndSaveFile($uploadedFile, $testId, $part);

            // Clean up chunk files
            $this->cleanupChunks($chunkDir);

            return response()->json($result);

        } catch (Exception $e) {
            Log::error('Error finalizing chunked upload', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Yuklashni yakunlashda xatolik: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Detect file input key in request
     */
    private function detectFileKey(Request $request)
    {
        $fileKeys = ['file', 'chunk', 'audio_file', 'audio_chunk'];

        foreach ($fileKeys as $key) {
            if ($request->hasFile($key) && $request->file($key)->isValid()) {
                return $key;
            }
        }

        // If no predefined keys found, try to find any file in the request
        foreach ($request->allFiles() as $key => $file) {
            if ($file->isValid()) {
                return $key;
            }
        }

        return null;
    }

    /**
     * Process and save the uploaded file
     */
    private function processAndSaveFile($file, $testId = null, $part = 'part1')
    {
        try {
            // Fayl ma'lumotlari
            $originalName = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());
            $size = $file->getSize();
            $mimeType = $file->getMimeType();

            // Fayl nomini xavfsiz qilish
            $safeFileName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '_' . time() . '.' . $extension;

            // Saqlash papkasini aniqlash
            $storagePath = 'public/audio';

            if ($testId) {
                $storagePath .= "/test_{$testId}";
            }

            $storagePath .= "/{$part}";

            // Papkani yaratish (agar mavjud bo'lmasa)
            if (!Storage::exists($storagePath)) {
                if (!Storage::makeDirectory($storagePath, 0755, true)) {
                    Log::error('Failed to create directory', ['path' => $storagePath]);
                    return ['success' => false, 'message' => 'Papkani yaratib bo\'lmadi'];
                }
            }

            // Faylni saqlash
            $path = $file->storeAs($storagePath, $safeFileName);

            if (!$path) {
                Log::error('Failed to save file', ['original' => $originalName, 'path' => $storagePath]);
                return ['success' => false, 'message' => 'Faylni saqlashda xatolik'];
            }

            // Fayl yo'li
            $fullPath = storage_path('app/' . $path);

            // Audio davomiyligi
            $duration = $this->getAudioDuration($fullPath);

            Log::info('File saved successfully', [
                'original_name' => $originalName,
                'path' => $path,
                'size' => $size,
                'mime' => $mimeType,
                'duration' => $duration
            ]);

            return [
                'success' => true,
                'message' => 'Audio fayl muvaffaqiyatli yuklandi',
                'data' => [
                    'filename' => basename($path),
                    'original_name' => $originalName,
                    'url' => Storage::url($path),
                    'full_url' => url(Storage::url($path)),
                    'size' => $size,
                    'size_formatted' => $this->formatBytes($size),
                    'extension' => $extension,
                    'mime_type' => $mimeType,
                    'duration' => $duration,
                    'duration_formatted' => $this->formatDuration($duration),
                    'test_id' => $testId,
                    'part' => $part
                ]
            ];

        } catch (Exception $e) {
            Log::error('Error processing file', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return ['success' => false, 'message' => 'Faylni qayta ishlashda xatolik: ' . $e->getMessage()];
        }
    }

    /**
     * Clean up chunk files after successful merge
     */
    private function cleanupChunks($chunkDir)
    {
        try {
            if (is_dir($chunkDir)) {
                $files = glob($chunkDir . '/*');

                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }

                rmdir($chunkDir);

                Log::info('Cleaned up chunk directory', ['dir' => $chunkDir]);
            }
        } catch (Exception $e) {
            Log::warning('Failed to clean up chunks', [
                'dir' => $chunkDir,
                'error' => $e->getMessage()
            ]);
        }
    }
}
