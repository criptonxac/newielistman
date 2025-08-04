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
            // Increase PHP limits for large file uploads
            ini_set('upload_max_filesize', '100M');
            ini_set('post_max_size', '100M');
            ini_set('memory_limit', '256M');
            ini_set('max_execution_time', '600');
            ini_set('max_input_time', '600');
            set_time_limit(600);

            // Log detailed request information
            Log::info('Chunk upload request received', [
                'ip' => $request->ip(),
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'content_type' => $request->header('Content-Type'),
                'content_length' => $request->header('Content-Length'),
                'file_count' => count($request->allFiles()),
                'input_params' => $request->except(['file', 'chunk', '_token']),
                'upload_params' => [
                    'file_keys' => array_keys($request->allFiles()),
                    'has_file' => $request->hasFile('file'),
                    'has_chunk' => $request->hasFile('chunk'),
                ],
                'php_limits' => [
                    'upload_max_filesize' => ini_get('upload_max_filesize'),
                    'post_max_size' => ini_get('post_max_size'),
                    'memory_limit' => ini_get('memory_limit'),
                    'max_execution_time' => ini_get('max_execution_time'),
                    'max_input_time' => ini_get('max_input_time'),
                    'max_file_uploads' => ini_get('max_file_uploads'),
                ]
            ]);

            // Check for file in the request
            if (empty($request->allFiles())) {
                Log::warning('No files found in chunk request', [
                    'all_input' => array_keys($request->all()),
                    'files' => $request->allFiles(),
                    'request_headers' => $request->headers->all()
                ]);
                return response()->json([
                    'success' => false, 
                    'message' => 'Chunk fayli topilmadi.',
                    'debug' => [
                        'file_keys' => array_keys($request->allFiles()),
                        'has_file' => $request->hasFile('file'),
                        'has_chunk' => $request->hasFile('chunk'),
                        'content_type' => $request->header('Content-Type'),
                        'content_length' => $request->header('Content-Length'),
                    ]
                ], 400);
            }

            // Process the chunked upload
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
            // Increase PHP limits for large file operations
            ini_set('upload_max_filesize', '100M');
            ini_set('post_max_size', '100M');
            ini_set('memory_limit', '256M');
            ini_set('max_execution_time', '600');
            ini_set('max_input_time', '600');
            set_time_limit(600);

            // Log the finalize request with detailed information
            Log::info('Finalize upload request received', [
                'ip' => $request->ip(),
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'content_type' => $request->header('Content-Type'),
                'content_length' => $request->header('Content-Length'),
                'request_data' => $request->except(['_token']),
                'file_params' => [
                    'file_id' => $request->input('file_id'),
                    'file_name' => $request->input('file_name'),
                    'file_size' => $request->input('file_size'),
                    'chunk_index' => $request->input('chunk_index'),
                    'total_chunks' => $request->input('total_chunks'),
                ],
                'php_limits' => [
                    'upload_max_filesize' => ini_get('upload_max_filesize'),
                    'post_max_size' => ini_get('post_max_size'),
                    'memory_limit' => ini_get('memory_limit'),
                    'max_execution_time' => ini_get('max_execution_time'),
                    'max_input_time' => ini_get('max_input_time'),
                ]
            ]);

            // Validate required parameters
            $requiredParams = ['file_id', 'file_name', 'file_size'];
            $missingParams = array_filter($requiredParams, function($param) use ($request) {
                return !$request->has($param) || empty($request->input($param));
            });

            if (!empty($missingParams)) {
                $errorMessage = 'Zarur parametrlar etishmayapti: ' . implode(', ', $missingParams);
                
                Log::warning($errorMessage, [
                    'missing_params' => $missingParams,
                    'provided_params' => $request->only($requiredParams),
                    'all_input' => $request->except(['_token']),
                    'headers' => $request->headers->all()
                ]);
                
                return response()->json([
                    'success' => false, 
                    'message' => $errorMessage,
                    'missing_params' => array_values($missingParams),
                    'provided_params' => $request->only($requiredParams)
                ], 400);
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
            // Log the start of chunk processing
            Log::debug('Starting to handle chunked upload', [
                'request_data' => $request->except(['file', 'chunk', '_token']),
                'has_file' => $request->hasFile('file'),
                'has_chunk' => $request->hasFile('chunk'),
                'all_files' => array_keys($request->allFiles()),
                'content_type' => $request->header('Content-Type'),
                'content_length' => $request->header('Content-Length')
            ]);

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

            // Log validation passed
            Log::debug('Chunk metadata validated', [
                'chunk_index' => $validated['chunk_index'],
                'total_chunks' => $validated['total_chunks'],
                'file_id' => $validated['file_id'],
                'file_name' => $validated['file_name'],
                'file_size' => $validated['file_size']
            ]);

            // Detect file key in request
            $fileKey = $this->detectFileKey($request);
            if (!$fileKey) {
                $errorMessage = 'Chunk fayli topilmadi. Tekshirilgan kalitlar: ' . 
                              implode(', ', array_keys($request->allFiles()));
                
                Log::error($errorMessage, [
                    'available_files' => $request->allFiles(),
                    'request_data' => $request->except(['_token']),
                    'headers' => $request->headers->all()
                ]);
                
                return response()->json([
                    'success' => false, 
                    'message' => $errorMessage,
                    'debug' => [
                        'available_file_keys' => array_keys($request->allFiles()),
                        'content_type' => $request->header('Content-Type'),
                        'content_length' => $request->header('Content-Length'),
                        'request_method' => $request->method()
                    ]
                ], 400);
            }

            // Validate chunk file
            $chunkFile = $request->file($fileKey);
            if (!$chunkFile || !$chunkFile->isValid()) {
                $errorMessage = $chunkFile ? 
                    $this->getUploadErrorMessage($chunkFile->getError()) : 
                    'Fayl yuklanmadi';
                
                Log::error('Invalid chunk file', [
                    'file_key' => $fileKey,
                    'original_name' => $chunkFile ? $chunkFile->getClientOriginalName() : null,
                    'size' => $chunkFile ? $chunkFile->getSize() : 0,
                    'error' => $chunkFile ? $chunkFile->getError() : 'No file',
                    'error_message' => $errorMessage,
                    'request_data' => $request->except(['_token'])
                ]);
                
                return response()->json([
                    'success' => false, 
                    'message' => 'Noto\'g\'ri chunk fayli: ' . $errorMessage,
                    'error_code' => $chunkFile ? $chunkFile->getError() : 'NO_FILE',
                    'error_message' => $errorMessage
                ], 400);
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
        // Log the start of chunk merging
        Log::debug('Starting to finalize chunked upload', [
            'file_id' => $request->input('file_id'),
            'file_name' => $request->input('file_name'),
            'file_size' => $request->input('file_size'),
            'test_id' => $request->input('test_id'),
            'part_id' => $request->input('part_id')
        ]);
        
        $fileId = $request->input('file_id');
        $fileName = $request->input('file_name');
        $fileSize = $request->input('file_size');
        $testId = $request->input('test_id');
        $part = $request->input('part_id', 'part1');
        
        // Validate required parameters
        if (empty($fileId) || empty($fileName) || empty($fileSize)) {
            $error = 'Missing required parameters for chunk finalization';
            Log::error($error, [
                'file_id' => $fileId,
                'file_name' => $fileName,
                'file_size' => $fileSize
            ]);
            return response()->json([
                'success' => false,
                'message' => $error,
                'missing_fields' => array_filter([
                    empty($fileId) ? 'file_id' : null,
                    empty($fileName) ? 'file_name' : null,
                    empty($fileSize) ? 'file_size' : null
                ])
            ], 400);
        }
        try {
            // Path to the chunk directory and info file
            $chunkDir = storage_path('app/tmp/' . $this->tempChunkDir . '/' . $fileId);
            $infoPath = $chunkDir . '/info.json';
            
            // Log directory and file info
            Log::debug('Checking chunk directory and info file', [
                'chunk_dir' => $chunkDir,
                'info_path' => $infoPath,
                'directory_exists' => file_exists($chunkDir),
                'info_file_exists' => file_exists($infoPath)
            ]);

            // Validate chunk directory and info file
            if (!file_exists($chunkDir) || !is_dir($chunkDir)) {
                $error = 'Chunk directory not found';
                Log::error($error, ['directory' => $chunkDir]);
                return response()->json(['success' => false, 'message' => $error], 400);
            }

            if (!file_exists($infoPath) || !is_readable($infoPath)) {
                $error = 'Chunk info file not found or not readable';
                Log::error($error, ['path' => $infoPath]);
                return response()->json(['success' => false, 'message' => $error], 400);
            }
            
            // Read and validate info file
            $info = json_decode(file_get_contents($infoPath), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $error = 'Invalid chunk info format';
                Log::error($error, ['error' => json_last_error_msg()]);
                return response()->json(['success' => false, 'message' => $error], 400);
            }

            // Validate required chunk information
            if (!isset($info['total_chunks'], $info['file_name'], $info['file_size'], $info['received_chunks'])) {
                $error = 'Missing required chunk information in info file';
                Log::error($error, ['info' => $info]);
                return response()->json([
                    'success' => false, 
                    'message' => $error,
                    'missing_fields' => array_diff(
                        ['total_chunks', 'file_name', 'file_size', 'received_chunks'],
                        array_keys($info)
                    )
                ], 400);
            }
            
            $totalChunks = (int)$info['total_chunks'];
            $receivedChunks = is_array($info['received_chunks']) ? count($info['received_chunks']) : 0;
            
            // Log chunk validation info
            Log::debug('Validating chunks', [
                'expected_chunks' => $totalChunks,
                'received_chunks' => $receivedChunks,
                'received_chunks_list' => $info['received_chunks']
            ]);
            
            // Check if all chunks are received
            if ($receivedChunks !== $totalChunks) {
                $missingChunks = array_diff(range(0, $totalChunks - 1), $info['received_chunks']);
                $error = 'Not all chunks received. Missing: ' . implode(', ', $missingChunks);
                
                Log::error($error, [
                    'expected' => $totalChunks,
                    'received' => $receivedChunks,
                    'missing' => $missingChunks
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Barcha chunk lar yuklanmagan',
                    'received' => $receivedChunks,
                    'missing_chunks' => $missingChunks
                ], 400);
            }
            
            // Create a temporary file for merging chunks
            $tempMergedPath = tempnam(sys_get_temp_dir(), 'merged_');
            if ($tempMergedPath === false) {
                $error = 'Failed to create temporary file for merging';
                Log::error($error, ['tmp_dir' => sys_get_temp_dir()]);
                return response()->json([
                    'success' => false, 
                    'message' => 'Chunk larni birlashtirishda xatolik',
                    'error' => $error
                ], 500);
            }
            
            Log::debug('Created temporary file for merging', ['path' => $tempMergedPath]);
            
            // Open the temporary file for writing
            $mergedFile = fopen($tempMergedPath, 'wb');
            if ($mergedFile === false) {
                $error = 'Failed to open temporary file for writing';
                Log::error($error, ['path' => $tempMergedPath]);
                @unlink($tempMergedPath); // Clean up
                return response()->json([
                    'success' => false, 
                    'message' => 'Chunk larni birlashtirishda xatolik',
                    'error' => $error
                ], 500);
            }

            // Merge chunks with error handling
            $totalBytes = 0;
            
            try {
                for ($i = 0; $i < $totalChunks; $i++) {
                    $chunkPath = $chunkDir . '/chunk_' . $i;
                    
                    // Verify chunk exists
                    if (!file_exists($chunkPath)) {
                        throw new Exception("Chunk $i not found: $chunkPath");
                    }
                    
                    // Read and write chunk
                    $chunkData = file_get_contents($chunkPath);
                    if ($chunkData === false) {
                        throw new Exception("Failed to read chunk $i");
                    }
                    
                    $bytes = fwrite($mergedFile, $chunkData);
                    if ($bytes === false) {
                        throw new Exception("Failed to write chunk $i");
                    }
                    
                    $totalBytes += $bytes;
                    unset($chunkData); // Free memory
                }
                
                // Close file handle
                fclose($mergedFile);
                $mergedFile = null;
                
                // Verify size
                $actualSize = filesize($tempMergedPath);
                $tolerance = $fileSize * 0.05;
                
                if (abs($actualSize - $fileSize) > $tolerance) {
                    throw new Exception("File size mismatch. Expected: $fileSize, Got: $actualSize");
                }
                
                Log::info('Merged chunks successfully', [
                    'file' => $tempMergedPath,
                    'size' => $actualSize,
                    'chunks' => $totalChunks
                ]);
                
            } catch (Exception $e) {
                // Clean up on error
                if ($mergedFile) @fclose($mergedFile);
                if (file_exists($tempMergedPath)) @unlink($tempMergedPath);
                
                Log::error('Merge failed: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Chunk larni birlashtirishda xatolik',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                // Verify file exists and is readable
                if (!file_exists($tempMergedPath) || !is_readable($tempMergedPath)) {
                    throw new Exception('Merged file not found or not readable');
                }
                
                // Get MIME type
                $mimeType = mime_content_type($tempMergedPath);
                if ($mimeType === false) {
                    throw new Exception('Failed to determine MIME type of merged file');
                }
                
                // Create UploadedFile instance
                $uploadedFile = new \Illuminate\Http\UploadedFile(
                    $tempMergedPath,
                    $fileName,
                    $mimeType,
                    null,
                    true // Test mode to avoid moving the file again
                );
                
                Log::debug('Created UploadedFile instance', [
                    'path' => $tempMergedPath,
                    'name' => $fileName,
                    'mime' => $mimeType,
                    'size' => filesize($tempMergedPath)
                ]);
                
                // Process the file
                $result = $this->processAndSaveFile($uploadedFile, $testId, $part);
                
                // Verify processing was successful
                if (!is_object($result) || !method_exists($result, 'getData')) {
                    throw new Exception('Invalid response from processAndSaveFile');
                }
                
                $responseData = $result->getData();
                if (!isset($responseData->success) || $responseData->success !== true) {
                    throw new Exception('File processing failed: ' . ($responseData->message ?? 'Unknown error'));
                }
                
                Log::info('Successfully processed uploaded file', [
                    'file' => $fileName,
                    'test_id' => $testId,
                    'part' => $part
                ]);
                
                return $result;
                
            } catch (Exception $e) {
                Log::error('File processing failed: ' . $e->getMessage(), [
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'temp_file' => $tempMergedPath ?? null,
                    'file_exists' => isset($tempMergedPath) ? file_exists($tempMergedPath) : null
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Faylni qayta ishlashda xatolik: ' . $e->getMessage()
                ], 500);
                
            } finally {
                // Always clean up temporary files
                try {
                    if (!empty($tempMergedPath) && file_exists($tempMergedPath)) {
                        @unlink($tempMergedPath);
                    }
                    if (!empty($chunkDir) && is_dir($chunkDir)) {
                        $this->cleanupChunks($chunkDir);
                    }
                } catch (Exception $e) {
                    Log::error('Error during cleanup: ' . $e->getMessage());
                }
            }

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
        // List of possible file field names to check
        $fileKeys = ['file', 'chunk', 'audio_file', 'audio_chunk', 'audio'];
        
        // Log all files in the request for debugging
        $allFiles = $request->allFiles();
        Log::debug('Detecting file key in request', [
            'available_file_keys' => array_keys($allFiles),
            'request_content_type' => $request->header('Content-Type'),
            'request_method' => $request->method(),
            'request_headers' => $request->headers->all()
        ]);

        // First, check for explicitly defined file keys
        foreach ($fileKeys as $key) {
            if ($request->hasFile($key)) {
                $file = $request->file($key);
                if ($file->isValid()) {
                    Log::debug("Found valid file with key: $key", [
                        'original_name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'error' => $file->getError(),
                        'error_message' => $this->getUploadErrorMessage($file->getError())
                    ]);
                    return $key;
                } else {
                    Log::warning("Invalid file found with key: $key", [
                        'error_code' => $file->getError(),
                        'error_message' => $this->getUploadErrorMessage($file->getError())
                    ]);
                }
            }
        }

        // If no predefined keys found, try to find any file in the request
        foreach ($allFiles as $key => $file) {
            if (is_array($file)) {
                // Handle array of files
                foreach ($file as $f) {
                    if ($f->isValid()) {
                        Log::debug("Found valid file in array with key: $key");
                        return $key;
                    }
                }
            } elseif ($file->isValid()) {
                Log::debug("Found valid file with dynamic key: $key");
                return $key;
            }
        }

        // Log detailed error if no valid file was found
        $errorMessage = 'No valid file found in request. Available keys: ' . implode(', ', array_keys($allFiles));
        Log::error($errorMessage, [
            'available_files' => array_map(function($file) {
                return is_array($file) ? 'array(' . count($file) . ')' : (
                    is_object($file) ? get_class($file) : gettype($file)
                );
            }, $allFiles),
            'request_data' => $request->except(['_token']),
            'content_type' => $request->header('Content-Type'),
            'request_method' => $request->method()
        ]);

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
