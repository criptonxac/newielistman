<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestCategory;
use App\Models\TestQuestion;
use App\Models\TestAudioFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TestManagementController extends Controller
{
    /**
     * Get the maximum file upload size in bytes
     * 
     * @return int Maximum file upload size in bytes
     */
    protected function getMaximumFileUploadSize()
    {
        $maxSize = $this->parseSize(ini_get('post_max_size'));
        $uploadMax = $this->parseSize(ini_get('upload_max_filesize'));
        
        // Return the smaller of the two
        return min($maxSize, $uploadMax);
    }
    
    /**
     * Parse the php.ini size format to bytes
     * 
     * @param string $size The size string (e.g., '8M', '16K')
     * @return int Size in bytes
     */
    protected function parseSize($size)
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove non-unit characters
        $size = preg_replace('/[^0-9\\.]/', '', $size); // Remove non-numeric characters
        
        if ($unit) {
            // Find the position of the unit in the ordered string
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }
        
        return round($size);
    }
    
    /**
     * Format bytes to a human-readable format
     * 
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
    
    /**
     * Get error message for file upload error code
     * 
     * @param int $errorCode
     * @return string
     */
    protected function getFileUploadErrorMessage($errorCode)
    {
        $phpFileUploadErrors = [
            0 => 'There is no error, the file uploaded with success',
            1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
            3 => 'The uploaded file was only partially uploaded',
            4 => 'No file was uploaded',
            6 => 'Missing a temporary folder',
            7 => 'Failed to write file to disk',
            8 => 'A PHP extension stopped the file upload',
        ];
        
        return $phpFileUploadErrors[$errorCode] ?? 'Unknown upload error';
    }
    public function __construct()
    {
        // Faqat admin va teacher kirishi mumkin
        $this->middleware(function ($request, $next) {
            if (!Auth::user() || (!Auth::user()->isAdmin() && !Auth::user()->isTeacher())) {
                abort(403, 'Ruxsat berilmagan');
            }
            return $next($request);
        });
    }

    /**
     * Testlar ro'yxati
     * 
     * Filtrlash, qidirish va tartiblash imkoniyatlari bilan testlar ro'yxati.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Filtrlar va qidiruv parametrlari
        $search = $request->input('search');
        $category = $request->input('category');
        $status = $request->input('status');
        $sort = $request->input('sort', 'latest');
        $perPage = $request->input('per_page', 15);

        // Asosiy so'rov
        $query = Test::with(['category', 'audioFiles'])
            ->withCount('questions')
            ->when(Auth::user()->isTeacher(), function($query) {
                // Teacher faqat o'z testlarini ko'radi
                $query->where('created_by', Auth::id());
            })
            ->when($search, function($query, $search) {
                // Qidiruv bo'yicha filtrlash
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($category, function($query, $category) {
                // Kategoriya bo'yicha filtrlash
                $query->whereHas('category', function($q) use ($category) {
                    $q->where('id', $category);
                });
            })
            ->when($status, function($query, $status) {
                // Status bo'yicha filtrlash
                if ($status === 'active') {
                    $query->where('is_active', true);
                } elseif ($status === 'inactive') {
                    $query->where('is_active', false);
                }
            });

        // Tartiblash
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;
            case 'questions_asc':
                $query->orderBy('questions_count', 'asc');
                break;
            case 'questions_desc':
                $query->orderBy('questions_count', 'desc');
                break;
            default: // latest
                $query->latest();
        }

        // Paginatsiya
        $tests = $query->paginate(min($perPage, 100))->withQueryString();

        // Kategoriyalar filtri uchun
        $categories = TestCategory::active()->get();
        
        $layout = Auth::user()->isAdmin() ? 'admin.dashboard' : 'teacher.dashboard';
        
        return view('test-management.index', compact(
            'tests', 
            'categories',
            'layout',
            'search',
            'category',
            'status',
            'sort',
            'perPage'
        ));
    }

    /**
     * Yangi test yaratish formasi
     */
    public function create()
    {
        $categories = TestCategory::active()->get();
        $layout = Auth::user()->isAdmin() ? 'admin.dashboard' : 'teacher.dashboard';
        return view('test-management.create', compact('categories', 'layout'));
    }

    /**
     * Yangi test saqlash
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'test_category_id' => 'required|exists:test_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:practice,sample,familiarisation,mock,real',
            'duration_minutes' => 'required|integer|min:1',
            'pass_score' => 'required|integer|min:0|max:100',
            'attempts_allowed' => 'required|integer|min:1',
            'is_active' => 'boolean'
        ]);

        $validated['created_by'] = Auth::id();
        $validated['is_active'] = $request->has('is_active');
        
        // Noyob slug yaratish
        $baseSlug = \Illuminate\Support\Str::slug($validated['title']);
        $slug = $baseSlug;
        $counter = 1;
        
        // Slug mavjudligini tekshirish va noyob slug yaratish
        while (Test::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        
        $validated['slug'] = $slug;

        $test = Test::create($validated);

        return redirect()
            ->route('test-management.questions.create', $test->id)
            ->with('success', 'Test muvaffaqiyatli yaratildi. Endi savollar qo\'shing.');
    }

    /**
     * Test tahrirlash formasi
     */
    public function edit(Test $test)
    {
        // Teacher faqat o'z testini tahrirlashi mumkin
        if (Auth::user()->isTeacher() && $test->created_by !== Auth::id()) {
            abort(403);
        }

        $categories = TestCategory::active()->get();
        $layout = Auth::user()->isAdmin() ? 'admin.dashboard' : 'teacher.dashboard';
        return view('test-management.edit', compact('test', 'categories', 'layout'));
    }

    /**
     * Test yangilash
     */
    public function update(Request $request, Test $test)
    {
        // Teacher faqat o'z testini yangilashi mumkin
        if (Auth::user()->isTeacher() && $test->created_by !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'test_category_id' => 'required|exists:test_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:practice,sample,familiarisation,mock,real',
            'duration_minutes' => 'required|integer|min:1',
            'pass_score' => 'required|integer|min:0|max:100',
            'attempts_allowed' => 'required|integer|min:1',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['slug'] = \Illuminate\Support\Str::slug($validated['title']);

        $test->update($validated);

        return redirect()
            ->route('test-management.index')
            ->with('success', 'Test muvaffaqiyatli yangilandi.');
    }

    /**
     * Test o'chirish
     */
    public function destroy(Test $test)
    {
        // Teacher faqat o'z testini o'chirishi mumkin
        if (Auth::user()->isTeacher() && $test->created_by !== Auth::id()) {
            abort(403);
        }

        // Audio fayllarni o'chirish
        foreach ($test->audioFiles as $audio) {
            $audio->deleteFile();
        }

        $test->delete();

        return redirect()
            ->route('teacher.tests.index')
            ->with('success', 'Test muvaffaqiyatli o\'chirildi.');
    }

    /**
     * Test savollarini qo'shish/tahrirlash sahifasi
     */
    public function createQuestions(Test $test)
    {
        // Teacher faqat o'z testiga savol qo'shishi mumkin
        if (Auth::user()->isTeacher() && $test->created_by !== Auth::id()) {
            abort(403);
        }

        $questions = $test->questions()
            ->orderBy('part_number')
            ->orderBy('question_number')
            ->paginate(10);

        $layout = Auth::user()->isAdmin() ? 'admin.dashboard' : 'teacher.dashboard';

        return view('test-management.questions.create', compact('test', 'questions', 'layout'));
    }

    /**
     * Savolni tahrirlash sahifasini ko'rsatish
     */
    public function editQuestion(Request $request, Test $test, TestQuestion $question)
    {
        // Faqat shu testga tegishli savollarni tahrirlashga ruxsat berish
        if ($question->test_id !== $test->id) {
            return redirect()
                ->route('test-management.questions.create', $test)
                ->with('error', 'Bu savol siz tanlagan testga tegishli emas!');
        }
        
        return view('test-management.questions.edit', [
            'test' => $test,
            'question' => $question,
        ]);
    }

    /**
     * Savollarni saqlash
     */
    public function storeQuestions(Request $request, Test $test)
    {
        // Teacher faqat o'z testiga savol qo'shishi mumkin
        if (Auth::user()->isTeacher() && $test->created_by !== Auth::id()) {
            \Log::warning('Unauthorized access attempt', [
                'user_id' => Auth::id(),
                'test_owner' => $test->created_by,
                'test_id' => $test->id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Ruxsat berilmagan!'
            ], 403);
        }

        // Log request ma'lumotlari
        \Log::info('=== STORE QUESTIONS STARTED ===');
        \Log::info('Test ID: ' . $test->id);
        \Log::info('Request Headers: ' . json_encode($request->headers->all(), JSON_PRETTY_PRINT));
        \Log::info('Request Data: ' . json_encode($request->except(['_token', 'questions']), JSON_PRETTY_PRINT));
        \Log::info('Files in request: ' . json_encode($request->allFiles()));
        \Log::info('Raw request data: ' . file_get_contents('php://input'));
        
        if ($request->has('questions')) {
            $questions = $request->questions;
            \Log::info('Questions count: ' . count($questions));
            \Log::info('Questions data: ' . json_encode($questions, JSON_PRETTY_PRINT));
            
            // Validate each question
            foreach ($questions as $id => &$question) {
                \Log::info('Processing question:', ['question_id' => $id, 'data' => $question]);
                
                // Skip if question data is not an array
                if (!is_array($question)) {
                    \Log::warning('Invalid question data format, skipping', ['question_id' => $id]);
                    continue;
                }
                
                // Set default values
                $question['question_type'] = $question['question_type'] ?? 'multiple_choice';
                $question['points'] = isset($question['points']) ? (int)$question['points'] : 1;
                $question['part_number'] = isset($question['part_number']) ? (int)$question['part_number'] : 1;
                $question['question_number'] = isset($question['question_number']) ? (int)$question['question_number'] : 1;
                
                // Handle options and correct_answers
                if (isset($question['options']) && is_string($question['options'])) {
                    $question['options'] = json_decode($question['options'], true) ?? [];
                } elseif (!isset($question['options'])) {
                    $question['options'] = [];
                }
                
                if (isset($question['correct_answers']) && is_string($question['correct_answers'])) {
                    $question['correct_answers'] = json_decode($question['correct_answers'], true) ?? [];
                } elseif (!isset($question['correct_answers'])) {
                    $question['correct_answers'] = [];
                }
                
                // Set default values if not provided
                $question['question_type'] = !empty($question['question_type']) ? $question['question_type'] : 'multiple_choice';
                $question['points'] = !empty($question['points']) && is_numeric($question['points']) ? (int)$question['points'] : 1;
                $question['part_number'] = !empty($question['part_number']) ? (int)$question['part_number'] : 1;
                $question['question_number'] = !empty($question['question_number']) ? (int)$question['question_number'] : 1;
                
                // Ensure options and correct_answers are properly formatted as arrays
                if (isset($question['options']) && is_string($question['options'])) {
                    $question['options'] = json_decode($question['options'], true) ?? [];
                } elseif (!isset($question['options'])) {
                    $question['options'] = [];
                }
                
                if (isset($question['correct_answers']) && is_string($question['correct_answers'])) {
                    $question['correct_answers'] = json_decode($question['correct_answers'], true) ?? [];
                } elseif (!isset($question['correct_answers'])) {
                    $question['correct_answers'] = [];
                }
            }
        } else {
            \Log::warning('No questions in request');
            return response()->json([
                'success' => false,
                'message' => 'Hech qanday savol topilmadi!'
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Audio fayllarni yuklash (Listening uchun)
            if ($test->category && $test->category->slug === 'listening' && $request->hasFile('audio_files')) {
                $files = $request->file('audio_files');
                if (!is_array($files)) {
                    $files = [$files];
                }

                \Log::info('Audio files found in request', [
                    'file_count' => count($files),
                    'file_names' => collect($files)->map(function($file) { 
                        return $file ? $file->getClientOriginalName() : 'null'; 
                    }),
                    'request_data' => $request->all()
                ]);
                
                // Avvalgi audio fayllarni o'chirib tashlash
                $test->audioFiles()->delete();
                
                // Yangi fayllarni yuklash
                foreach ($files as $index => $audioFile) {
                    if (!$audioFile || !$audioFile->isValid()) {
                        \Log::error('Invalid file in request', [
                            'index' => $index,
                            'file' => $audioFile ? $audioFile->getClientOriginalName() : 'null',
                            'is_valid' => $audioFile ? $audioFile->isValid() : false,
                            'error' => $audioFile ? $audioFile->getError() : 'No file'
                        ]);
                        continue;
                    }

                    // Fayl hajmi va formati tekshirish
                    $maxSize = 10 * 1024 * 1024; // 10MB in bytes
                    $allowedMimeTypes = [
                        'audio/mpeg', 
                        'audio/mp3', 
                        'audio/wav', 
                        'audio/ogg',
                        'audio/x-wav',
                        'audio/x-m4a'
                    ];
                    
                    $mimeType = $audioFile->getMimeType();
                    $fileSize = $audioFile->getSize();
                    
                    if (!in_array($mimeType, $allowedMimeTypes)) {
                        $error = "Noto'g'ri fayl formati: {$mimeType}. Faqat MP3, WAV yoki OGG fayllarini yuklashingiz mumkin.";
                        \Log::error($error, [
                            'file' => $audioFile->getClientOriginalName(),
                            'mime_type' => $mimeType,
                            'allowed_types' => $allowedMimeTypes
                        ]);
                        throw new \Exception($error);
                    }
                    
                    if ($fileSize > $maxSize) {
                        $error = "Fayl hajmi " . round($fileSize/1024/1024, 2) . "MB, maksimal ruxsat etilgan hajm " . ($maxSize/1024/1024) . "MB";
                        \Log::error($error, [
                            'file' => $audioFile->getClientOriginalName(),
                            'size' => $fileSize,
                            'max_size' => $maxSize
                        ]);
                        throw new \Exception($error);
                    }
                    
                    // Papka mavjudligini tekshirish
                    $directory = 'audio/tests/' . $test->id;
                    $fullPath = storage_path('app/public/' . $directory);
                    
                    if (!file_exists($fullPath)) {
                        if (!mkdir($fullPath, 0755, true)) {
                            $error = "Papka yaratib bo'lmadi: " . $fullPath;
                            \Log::error($error);
                            throw new \Exception($error);
                        }
                    }
                    
                    // Faylni saqlash
                    try {
                        $path = $audioFile->storeAs(
                            $directory, 
                            time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '_', $audioFile->getClientOriginalName()),
                            'public'
                        );
                        
                        if (!$path) {
                            throw new \Exception('Faylni saqlab bo\'lmadi');
                        }
                        
                        // Fayl mavjudligini tekshirish
                        $fullPath = storage_path('app/public/' . $path);
                        if (!file_exists($fullPath)) {
                            throw new \Exception("Fayl saqlanganiga qaramay topilmadi: " . $fullPath);
                        }
                        
                        // Fayl haqida ma'lumotlarni saqlash
                        TestAudioFile::create([
                            'test_id' => $test->id,
                            'file_path' => $path,
                            'file_name' => $audioFile->getClientOriginalName(),
                            'part_number' => 1,
                            'play_order' => $index + 1,
                            'auto_play' => true,
                            'duration_seconds' => 0
                        ]);
                        
                        \Log::info('Audio file uploaded successfully', [
                            'file_name' => $audioFile->getClientOriginalName(),
                            'saved_path' => $path,
                            'full_path' => $fullPath,
                            'size' => $fileSize,
                            'mime_type' => $mimeType
                        ]);
                        
                    } catch (\Exception $e) {
                        \Log::error('Error saving audio file', [
                            'file' => $audioFile->getClientOriginalName(),
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        throw new \Exception("Faylni saqlashda xatolik: " . $e->getMessage());
                    }
                }
                \Log::info('All audio files processed successfully');
            } else {
                \Log::info('No audio files found in request or not a listening test', [
                    'has_files' => $request->hasFile('audio_files'),
                    'category_slug' => $test->category ? $test->category->slug : null,
                    'is_listening' => $test->category && $test->category->slug === 'listening'
                ]);
            }

            $savedQuestions = [];
            // Savollarni yangilash/qo'shish
            if ($request->has('questions') && is_array($request->questions) && count($request->questions) > 0) {
                \Log::info('Starting to process ' . count($request->questions) . ' questions');
                
                foreach ($request->questions as $questionId => $questionData) {
                    try {
                        // Tozalash va standart qiymatlar
                        $questionData = array_filter($questionData, function($value) {
                            return $value !== null && $value !== '';
                        });

                        // Determine if this is a new question
                        $isNewQuestion = true;
                        $questionModel = null;
                        
                        // Try to find existing question if ID is provided and valid
                        if (!empty($questionId) && $questionId !== '0' && is_numeric($questionId)) {
                            $questionModel = TestQuestion::where('id', $questionId)
                                ->where('test_id', $test->id)
                                ->first();
                                
                            if ($questionModel) {
                                $isNewQuestion = false;
                                \Log::info('Found existing question', ['question_id' => $questionId]);
                                
                                // Update part_number if provided in audio_parts
                                if (isset($request->audio_parts[$questionId])) {
                                    $questionData['part_number'] = (int)$request->audio_parts[$questionId];
                                }
                                
                                // Prepare data for update
                                $updateData = [
                                    'question_text' => $questionData['question_text'],
                                    'question_type' => $questionData['question_type'],
                                    'points' => (int)($questionData['points'] ?? 1),
                                    'part_number' => (int)($questionData['part_number'] ?? 1),
                                    'question_number' => (int)($questionData['question_number'] ?? 1),
                                    'explanation' => $questionData['explanation'] ?? null,
                                ];
                                
                                // Handle options and correct_answers
                                $updateData['options'] = !empty($questionData['options']) ? 
                                    json_encode(array_values($questionData['options']), JSON_UNESCAPED_UNICODE) : 
                                    json_encode([]);
                                    
                                $updateData['correct_answers'] = !empty($questionData['correct_answers']) ? 
                                    json_encode(array_values($questionData['correct_answers']), JSON_UNESCAPED_UNICODE) : 
                                    json_encode([]);
                                
                                \Log::info('Updating question ' . $questionId . ' with data:', $updateData);
                                
                                try {
                                    $questionModel->update($updateData);
                                    $savedQuestions[] = $questionModel->id;
                                    \Log::info('Successfully updated question: ' . $questionModel->id);
                                    continue; // Move to next question
                                } catch (\Exception $e) {
                                    \Log::error('Failed to update question: ' . $e->getMessage(), [
                                        'question_id' => $questionId,
                                        'exception' => $e->getTraceAsString(),
                                        'data' => $updateData
                                    ]);
                                    throw $e;
                                }
                            }
                        }
                        // Handle new question
                        if ($isNewQuestion) {
                            // Prepare data for new question
                            $newQuestionData = [
                                'test_id' => $test->id,
                                'question_text' => $questionData['question_text'],
                                'question_type' => $questionData['question_type'],
                                'points' => (int)($questionData['points'] ?? 1),
                                'part_number' => (int)($questionData['part_number'] ?? 1),
                                'question_number' => (int)($questionData['question_number'] ?? 1),
                                'explanation' => $questionData['explanation'] ?? null,
                            ];
                            
                            // Handle audio parts for new questions
                            if (isset($request->audio_parts['new']) && is_array($request->audio_parts['new'])) {
                                $newIndex = strpos($questionId, 'new_') === 0 ? substr($questionId, 4) : $questionId;
                                if (isset($request->audio_parts['new'][$newIndex])) {
                                    $newQuestionData['part_number'] = (int)$request->audio_parts['new'][$newIndex];
                                }
                            }
                            
                            // Handle options and correct_answers for new question
                            $newQuestionData['options'] = !empty($questionData['options']) ? 
                                json_encode(array_values($questionData['options']), JSON_UNESCAPED_UNICODE) : 
                                json_encode([]);
                                
                            $newQuestionData['correct_answers'] = !empty($questionData['correct_answers']) ? 
                                json_encode(array_values($questionData['correct_answers']), JSON_UNESCAPED_UNICODE) : 
                                json_encode([]);
                                
                            // Ensure required fields are set
                            if (empty($newQuestionData['question_text'])) {
                                throw new \Exception('Savol matni kiritilmagan!');
                            }
                            
                            \Log::info('Creating new question with data:', $newQuestionData);
                            
                            try {
                                $newQuestion = TestQuestion::create($newQuestionData);
                                $savedQuestions[] = $newQuestion->id;
                                \Log::info('Successfully created new question: ' . $newQuestion->id);
                            } catch (\Exception $e) {
                                \Log::error('Failed to create question: ' . $e->getMessage(), [
                                    'exception' => $e->getTraceAsString(),
                                    'data' => $newQuestionData
                                ]);
                                throw new \Exception('Savolni yaratishda xatolik: ' . $e->getMessage());
                            }
                        }
                    } catch (\Exception $e) {
                        \Log::error('Savolni saqlashda xatolik', [
                            'questionId' => $questionId,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                            'data' => $questionData ?? null
                        ]);
                        
                        DB::rollBack();
                        
                        return response()->json([
                            'success' => false,
                            'message' => 'Savolni saqlashda xatolik: ' . $e->getMessage(),
                            'error_details' => $e->getTraceAsString()
                        ], 500);
                    }
                }
                \Log::info('Finished processing questions');
            } else {
                \Log::warning('No valid questions found in request');
                return response()->json([
                    'success' => false,
                    'message' => 'Hech qanday savol topilmadi!'
                ], 400);
            }

            // Commit the transaction
            DB::commit();
            
            // Refresh test data
            $test->refresh();
            $questionsCount = $test->questions()->count();
            
            // Log success
            \Log::info('Questions saved successfully', [
                'test_id' => $test->id,
                'saved_questions' => $savedQuestions ?? [],
                'total_questions' => $questionsCount
            ]);
            
            // Prepare success response
            $response = [
                'success' => true,
                'message' => 'Savollar muvaffaqiyatli saqlandi!',
                'redirect' => route('test-management.index'),
                'questions_count' => $questionsCount,
                'saved_questions' => $savedQuestions ?? []
            ];
            
            \Log::info('Sending success response', $response);
            return response()->json($response);

        } catch (\Exception $e) {
            DB::rollback();
            
            // Log the error with stack trace
            \Log::error('Error in storeQuestions: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'test_id' => $test->id ?? null,
                'user_id' => Auth::id()
            ]);
            
            // Return detailed error response
            $response = [
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage(),
                'error_details' => config('app.debug') ? $e->getTraceAsString() : null
            ];
            
            return response()->json($response, 500);
        }
    }

    /**
     * Yangi savol qo'shish
     */
    public function addQuestion(Test $test)
    {
        // Teacher faqat o'z testiga savol qo'shishi mumkin
        if (Auth::user()->isTeacher() && $test->created_by !== Auth::id()) {
            abort(403);
        }

        $questionNumber = $test->questions()->count() + 1;
        $layout = Auth::user()->isAdmin() ? 'admin.dashboard' : 'teacher.dashboard';

        return view('test-management.questions.add', compact('test', 'questionNumber', 'layout'));
    }

    /**
     * Savolni o'chirish
     */
    public function deleteQuestion(Test $test, TestQuestion $question)
    {
        // Teacher faqat o'z testidagi savolni o'chirishi mumkin
        if (Auth::user()->isTeacher() && $test->created_by !== Auth::id()) {
            abort(403);
        }

        if ($question->test_id !== $test->id) {
            abort(404);
        }

        $question->delete();

        // Savol raqamlarini qayta tartibga solish
        $test->questions()
            ->where('part_number', $question->part_number)
            ->where('question_number', '>', $question->question_number)
            ->decrement('question_number');

        return response()->json(['success' => true]);
    }

    /**
     * Test preview (ko'rish)
     */
    public function preview(Test $test)
    {
        // Teacher faqat o'z testini ko'rishi mumkin
        if (Auth::user()->isTeacher() && $test->created_by !== Auth::id()) {
            abort(403);
        }

        $test->load(['questions', 'audioFiles', 'category']);
        $layout = Auth::user()->isAdmin() ? 'admin.dashboard' : 'teacher.dashboard';

        return view('test-management.preview', compact('test', 'layout'));
    }

    /**
     * Test natijalarini ko'rish
     */
    public function results(Test $test)
    {
        // Teacher faqat o'z testining natijalarini ko'rishi mumkin
        if (Auth::user()->isTeacher() && $test->created_by !== Auth::id()) {
            abort(403);
        }

        $attempts = $test->attempts()
            ->with(['user', 'testAnswers.testQuestion'])
            ->completed()
            ->latest()
            ->paginate(20);
        
        $layout = Auth::user()->isAdmin() ? 'admin.dashboard' : 'teacher.dashboard';

        return view('test-management.results', compact('test', 'attempts', 'layout'));
    }

    /**
     * Test nusxalash
     */
    public function duplicate(Test $test)
    {
        DB::beginTransaction();

        try {
            // Test nusxasi
            $newTest = $test->replicate();
            $newTest->title = $test->title . ' (Nusxa)';
            $newTest->created_by = Auth::id();
            $newTest->save();

            // Savollarni nusxalash
            foreach ($test->questions as $question) {
                $newQuestion = $question->replicate();
                $newQuestion->test_id = $newTest->id;
                $newQuestion->save();
            }

            // Audio fayllarni nusxalash
            foreach ($test->audioFiles as $audio) {
                $newAudio = $audio->replicate();
                $newAudio->test_id = $newTest->id;
                
                // Fayl nusxasi
                if (Storage::disk('public')->exists($audio->file_path)) {
                    $newPath = 'audio/tests/' . $newTest->id . '/' . basename($audio->file_path);
                    Storage::disk('public')->copy($audio->file_path, $newPath);
                    $newAudio->file_path = $newPath;
                }
                
                $newAudio->save();
            }

            DB::commit();

            return redirect()
                ->route('admin.tests.edit', $newTest->id)
                ->with('success', 'Test muvaffaqiyatli nusxalandi!');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()
                ->back()
                ->with('error', 'Nusxalashda xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Audio fayl yuklash
     */
    public function uploadAudio(Request $request)
    {
        // Enable error reporting for debugging
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        // Log all request data
        $logData = [
            'request_data' => $request->except(['_token', 'audio_file', 'audio']),
            'files' => array_keys($request->allFiles()),
            'headers' => $request->headers->all(),
            'post_max_size' => ini_get('post_max_size'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'max_input_time' => ini_get('max_input_time'),
            'file_uploads' => ini_get('file_uploads'),
            'upload_tmp_dir' => ini_get('upload_tmp_dir'),
            'upload_tmp_dir_writable' => is_writable(ini_get('upload_tmp_dir')),
            'storage_path_writable' => is_writable(storage_path()),
            'public_path_writable' => is_writable(public_path()),
            'post_data_keys' => array_keys($_POST),
            'files_data' => array_map(function($file) {
                return [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                    'error' => $file->getError(),
                    'error_message' => $this->getFileUploadErrorMessage($file->getError()),
                    'is_valid' => $file->isValid()
                ];
            }, $request->allFiles())
        ];
        
        \Log::info('Audio upload request received', $logData);

        // Check if any file is present in the request
        if (!$request->hasFile('audio_file') && !$request->hasFile('audio')) {
            $errorMessage = 'No audio file found in the request. ';
            $errorMessage .= 'Files in request: ' . implode(', ', array_keys($request->allFiles()));
            
            \Log::warning($errorMessage, [
                'all_files' => $request->allFiles(),
                'request_data' => $request->except('_token'),
                'post_data' => $_POST,
                'files_data' => $_FILES
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'request_data' => $request->except('_token'),
                'files_received' => array_keys($request->allFiles()),
                'post_data' => $_POST,
                'files_data' => $_FILES
            ], 400);
        }

        // Determine which file key to use
        $fileKey = $request->hasFile('audio_file') ? 'audio_file' : 'audio';
        $file = $request->file($fileKey);

        // Log file details
        $fileInfo = [
            'file_key' => $fileKey,
            'original_name' => $file->getClientOriginalName(),
            'extension' => $file->getClientOriginalExtension(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'error_code' => $file->getError(),
            'error_message' => $this->getFileUploadErrorMessage($file->getError()),
            'is_valid' => $file->isValid(),
            'path' => $file->getPathname(),
            'real_path' => $file->getRealPath(),
            'temporary_path' => $file->getRealPath(),
            'temporary_directory' => dirname($file->getRealPath()),
            'temporary_directory_writable' => is_writable(dirname($file->getRealPath())),
            'php_ini_settings' => [
                'post_max_size' => ini_get('post_max_size'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'max_file_uploads' => ini_get('max_file_uploads'),
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
                'max_input_time' => ini_get('max_input_time'),
                'file_uploads' => ini_get('file_uploads'),
                'upload_tmp_dir' => ini_get('upload_tmp_dir'),
                'upload_tmp_dir_writable' => is_writable(ini_get('upload_tmp_dir')),
            ]
        ];

        \Log::info('Processing file upload:', $fileInfo);

        // Check if file is valid
        if (!$file->isValid()) {
            $errorMessage = 'File upload failed: ' . $fileInfo['error_message'];
            \Log::error($errorMessage, $fileInfo);
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'file_info' => $fileInfo,
                'php_ini_settings' => $fileInfo['php_ini_settings']
            ], 422);
        }

        // Check file size
        $maxFileSize = $this->getMaximumFileUploadSize();
        if ($file->getSize() > $maxFileSize) {
            $errorMessage = sprintf(
                'File is too large. Maximum allowed size is %s, file size is %s',
                $this->formatBytes($maxFileSize),
                $this->formatBytes($file->getSize())
            );
            \Log::error($errorMessage, $fileInfo);
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'max_size' => $maxFileSize,
                'file_size' => $file->getSize(),
                'max_size_human' => $this->formatBytes($maxFileSize),
                'file_size_human' => $this->formatBytes($file->getSize())
            ], 422);
        }

        try {
            // Manually validate the request
            $validator = \Validator::make($request->all(), [
                $fileKey => 'required|file|mimes:mp3,wav,ogg|max:20480', // Max 20MB
                'test_id' => 'required|exists:tests,id',
                'part_id' => 'required|integer|min:1',
            ], [
                $fileKey . '.required' => 'Iltimos, audio faylni yuklang',
                $fileKey . '.mimes' => 'Faqat MP3, WAV yoki OGG formatidagi fayllarni yuklashingiz mumkin',
                $fileKey . '.max' => 'Fayl hajmi 20 MB dan oshmasligi kerak',
                'test_id.required' => 'Test ID si kerak',
                'test_id.exists' => 'Noto\'g\'ri test ID si',
                'part_id.required' => 'Qism raqami kerak',
                'part_id.integer' => 'Qism raqami butun son bo\'lishi kerak',
                'part_id.min' => 'Qism raqami 1 dan katta yoki teng bo\'lishi kerak'
            ]);

            if ($validator->fails()) {
                \Log::warning('Validation failed', [
                    'errors' => $validator->errors(),
                    'request_data' => $request->except($fileKey)
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                    'request_data' => $request->except($fileKey)
                ], 422);
            }

            $validated = $validator->validated();
            $testId = $validated['test_id'];
            $partNumber = $validated['part_id'];
            
            // Log file info before processing
            \Log::info('File info before processing:', [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension(),
                'is_valid' => $file->isValid(),
                'error' => $file->getError(),
                'path' => $file->getPathname(),
                'real_path' => $file->getRealPath(),
            ]);

            // Create directory if not exists
            $relativePath = 'audio/tests/' . $testId . '/part' . $partNumber;
            $storagePath = 'public/' . $relativePath;
            
            try {
                if (!Storage::exists($storagePath)) {
                    $created = Storage::makeDirectory($storagePath, 0755, true);
                    \Log::info('Created directory: ' . $storagePath, [
                        'created' => $created,
                        'path' => $storagePath,
                        'full_path' => storage_path('app/' . $storagePath)
                    ]);
                }

                // Generate unique filename
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                
                // Store file
                $storedPath = $file->storeAs($relativePath, $filename, 'public');
                
                if (!$storedPath) {
                    throw new \Exception('Failed to store the uploaded file - storage returned false');
                }

                // Verify file was actually stored
                $fullPath = storage_path('app/public/' . $storedPath);
                $fileExists = file_exists($fullPath);
                $fileSize = $fileExists ? filesize($fullPath) : 0;
                
                \Log::info('File storage result:', [
                    'stored_path' => $storedPath,
                    'full_path' => $fullPath,
                    'file_exists' => $fileExists,
                    'file_size' => $fileSize,
                    'original_size' => $file->getSize(),
                    'storage_disk' => config('filesystems.default'),
                    'storage_path' => storage_path(),
                    'public_path' => public_path(),
                    'base_path' => base_path(),
                    'app_path' => app_path()
                ]);
                
                if (!$fileExists || $fileSize === 0) {
                    throw new \Exception('File was not stored correctly or is empty');
                }
            } catch (\Exception $e) {
                \Log::error('File storage error: ' . $e->getMessage(), [
                    'exception' => $e,
                    'test_id' => $testId,
                    'part_number' => $partNumber,
                    'filename' => $filename ?? 'unknown',
                    'storage_path' => $storagePath,
                    'relative_path' => $relativePath,
                    'storage_disk' => config('filesystems.default'),
                    'storage_driver' => config('filesystems.disks.' . config('filesystems.default') . '.driver')
                ]);
                
                throw new \Exception('File storage failed: ' . $e->getMessage());
            }

            // Save file info to database
            try {
                $audioFile = TestAudioFile::create([
                    'test_id' => $testId,
                    'part_number' => $partNumber,
                    'filename' => $filename,
                    'original_filename' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'path' => $storedPath,
                    'play_order' => 1, // Default play order
                ]);

                \Log::info('Audio file record created', [
                    'audio_file_id' => $audioFile->id,
                    'test_id' => $testId,
                    'part_number' => $partNumber,
                    'path' => $storedPath,
                    'public_url' => Storage::url($storedPath),
                    'file_info' => [
                        'exists' => Storage::exists('public/' . $storedPath),
                        'size' => Storage::size('public/' . $storedPath),
                        'last_modified' => Storage::lastModified('public/' . $storedPath),
                    ]
                ]);

                // Verify the file is accessible via URL
                $publicUrl = Storage::url($storedPath);
                $isAccessible = false;
                
                try {
                    $client = new \GuzzleHttp\Client(['verify' => false]);
                    $response = $client->head(url($publicUrl));
                    $isAccessible = $response->getStatusCode() === 200;
                } catch (\Exception $e) {
                    \Log::warning('Could not verify public URL accessibility', [
                        'url' => $publicUrl,
                        'error' => $e->getMessage()
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Audio file uploaded successfully',
                    'file' => [
                        'id' => $audioFile->id,
                        'filename' => $filename,
                        'original_name' => $file->getClientOriginalName(),
                        'url' => $publicUrl,
                        'path' => $storedPath,
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'is_accessible' => $isAccessible,
                        'storage' => [
                            'disk' => config('filesystems.default'),
                            'visibility' => Storage::getVisibility('public/' . $storedPath),
                            'url' => Storage::url($storedPath),
                            'exists' => Storage::exists('public/' . $storedPath),
                        ]
                    ]
                ]);

            } catch (\Exception $e) {
                // If database save fails, clean up the stored file
                if (isset($storedPath) && Storage::exists('public/' . $storedPath)) {
                    Storage::delete('public/' . $storedPath);
                }
                
                throw new \Exception('Failed to save file record to database: ' . $e->getMessage());
            }

        } catch (\Exception $e) {
            $errorDetails = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except([$fileKey, '_token']),
                'file_uploaded' => isset($storedPath) && Storage::exists('public/' . $storedPath),
                'php_upload_errors' => [
                    'UPLOAD_ERR_OK' => UPLOAD_ERR_OK,
                    'UPLOAD_ERR_INI_SIZE' => UPLOAD_ERR_INI_SIZE,
                    'UPLOAD_ERR_FORM_SIZE' => UPLOAD_ERR_FORM_SIZE,
                    'UPLOAD_ERR_PARTIAL' => UPLOAD_ERR_PARTIAL,
                    'UPLOAD_ERR_NO_FILE' => UPLOAD_ERR_NO_FILE,
                    'UPLOAD_ERR_NO_TMP_DIR' => UPLOAD_ERR_NO_TMP_DIR,
                    'UPLOAD_ERR_CANT_WRITE' => UPLOAD_ERR_CANT_WRITE,
                    'UPLOAD_ERR_EXTENSION' => UPLOAD_ERR_EXTENSION,
                ],
                'php_post_max_size' => ini_get('post_max_size'),
                'php_upload_max_filesize' => ini_get('upload_max_filesize'),
                'php_max_file_uploads' => ini_get('max_file_uploads'),
                'php_memory_limit' => ini_get('memory_limit'),
            ];
            
            \Log::error('Audio upload failed: ' . $e->getMessage(), $errorDetails);

            return response()->json([
                'success' => false,
                'message' => 'Audio file upload failed: ' . $e->getMessage(),
                'error' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ],
                'debug' => [
                    'php_upload_errors' => [
                        'UPLOAD_ERR_OK' => 'No error',
                        'UPLOAD_ERR_INI_SIZE' => 'File is larger than upload_max_filesize',
                        'UPLOAD_ERR_FORM_SIZE' => 'File is larger than form MAX_FILE_SIZE',
                        'UPLOAD_ERR_PARTIAL' => 'File was only partially uploaded',
                        'UPLOAD_ERR_NO_FILE' => 'No file was uploaded',
                        'UPLOAD_ERR_NO_TMP_DIR' => 'Missing temporary folder',
                        'UPLOAD_ERR_CANT_WRITE' => 'Failed to write file to disk',
                        'UPLOAD_ERR_EXTENSION' => 'A PHP extension stopped the file upload',
                    ],
                    'php_post_max_size' => ini_get('post_max_size'),
                    'php_upload_max_filesize' => ini_get('upload_max_filesize'),
                    'php_max_file_uploads' => ini_get('max_file_uploads'),
                    'php_memory_limit' => ini_get('memory_limit'),
                ]
            ], 500);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error during audio upload: ' . $e->getMessage(), [
                'errors' => $e->errors(),
                'request' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
                'request_data' => $request->except('audio')
            ], 422);
            
        } catch (\Exception $e) {
            $errorContext = [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except('audio')
            ];
            
            \Log::error('Audio upload error: ' . $e->getMessage(), $errorContext);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload audio file: ' . $e->getMessage(),
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred',
                'file' => config('app.debug') ? $e->getFile() : null,
                'line' => config('app.debug') ? $e->getLine() : null
            ], 500);
        }
    }
}