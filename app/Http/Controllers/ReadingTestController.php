<?php

namespace App\Http\Controllers;

use App\Models\ReadingTest;
use App\Models\ReadingTestItem;
use App\Models\AppTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReadingTestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isAdmin() && !Auth::user()->isTeacher()) {
                abort(403, 'Faqat admin va teacher foydalanuvchilar reading test boshqara oladi');
            }
            return $next($request);
        });
    }

    /**
     * Reading Test - Barcha testlar ro'yxati
     */
    public function index(Request $request)
    {
        $query = ReadingTest::with('appTest');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('appTest', function($appQuery) use ($search) {
                      $appQuery->where('title', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by app_test_id
        if ($request->filled('app_test_id')) {
            $query->where('app_test_id', $request->get('app_test_id'));
        }

        $readingTests = $query->orderBy('id', 'desc')->paginate(15);
        $appTests = AppTest::active()->get();

        $layout = Auth::user()->isAdmin() ? 'layouts.main' : 'layouts.teacher';
        return view('reading-tests.index', compact('readingTests', 'appTests', 'layout'));
    }

    /**
     * Reading Test - Yangi test yaratish formasi
     */
    public function create()
    {
        $appTests = AppTest::active()->get();
        $layout = Auth::user()->isAdmin() ? 'layouts.main' : 'layouts.teacher';
        return view('reading-tests.create', compact('appTests', 'layout'));
    }

    /**
     * Reading Test - Yangi testni saqlash
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'app_test_id' => 'required|exists:app_test,id',
            'title' => 'required|string|max:255',
            'body' => 'nullable|string', // Frontend dan JSON string keladi
            // Qo'shimcha maydonlar (ixtiyoriy, agar frontend dan alohida kelsa)
            'content_title' => 'nullable|string|max:255',
            'content_image' => 'nullable|string',
            'content_body' => 'nullable|string',
        ]);

        // Agar alohida content maydonlari kelgan bo'lsa, ularni JSON ga yig'amiz
        if ($request->has('content_title') || $request->has('content_image') || $request->has('content_body')) {
            $contentData = [
                'title' => $validated['content_title'] ?? '',
                'image' => $validated['content_image'] ?? '',
                'body' => $validated['content_body'] ?? ''
            ];
            $validated['body'] = json_encode($contentData);
        }


        // Agar body bo'sh bo'lsa, default JSON qo'yamiz
        if (empty($validated['body'])) {
            $validated['body'] = json_encode(['title' => '', 'image' => '', 'body' => '']);
        }
        // JSON formatini tekshirish
        $bodyArray = json_decode($validated['body'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return back()->withErrors(['body' => 'Body maydonida to\'g\'ri JSON format bo\'lishi kerak.'])->withInput();
        }

        // Faqat asosiy maydonlarni saqlash
        ReadingTest::create([
            'app_test_id' => $validated['app_test_id'],
            'title' => $validated['title'],
            'body' => $validated['body']
        ]);

        return redirect()
            ->route('reading-tests.index')
            ->with('success', 'Reading test muvaffaqiyatli yaratildi!');
    }

    /**
     * Reading Test - Testni ko'rish
     */
    public function show(ReadingTest $readingTest)
    {
        $readingTest->load('appTest');
        $layout = Auth::user()->isAdmin() ? 'layouts.main' : 'layouts.teacher';
        return view('reading-tests.show', compact('readingTest', 'layout'));
    }

    /**
     * Reading Test - Testni tahrirlash formasi
     */
    public function edit(ReadingTest $readingTest)
    {
        $appTests = AppTest::active()->get();
        $readingTest->load('appTest');
        $layout = Auth::user()->isAdmin() ? 'layouts.main' : 'layouts.teacher';
        return view('reading-tests.edit', compact('readingTest', 'appTests', 'layout'));
    }

    /**
     * Reading Test - Testni yangilash
     */
    public function update(Request $request, ReadingTest $readingTest)
    {
        $validated = $request->validate([
            'app_test_id' => 'required|exists:app_test,id',
            'title' => 'required|string|max:255',
            'body' => 'nullable|string', // Frontend dan JSON string keladi
            // Qo'shimcha maydonlar (ixtiyoriy, agar frontend dan alohida kelsa)
            'content_title' => 'nullable|string|max:255',
            'content_image' => 'nullable|string',
            'content_body' => 'nullable|string',
        ]);

        // Agar alohida content maydonlari kelgan bo'lsa, ularni JSON ga yig'amiz
        if ($request->has('content_title') || $request->has('content_image') || $request->has('content_body')) {
            $contentData = [
                'title' => $validated['content_title'] ?? '',
                'image' => $validated['content_image'] ?? '',
                'body' => $validated['content_body'] ?? ''
            ];
            $validated['body'] = json_encode($contentData);
        }

        // JSON formatini tekshirish
        $bodyArray = json_decode($validated['body'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return back()->withErrors(['body' => 'Body maydonida to\'g\'ri JSON format bo\'lishi kerak.'])->withInput();
        }

        // Faqat asosiy maydonlarni yangilash
        $readingTest->update([
            'app_test_id' => $validated['app_test_id'],
            'title' => $validated['title'],
            'body' => $validated['body']
        ]);

        return redirect()
            ->route('reading-tests.index')
            ->with('success', 'Reading test muvaffaqiyatli yangilandi!');
    }

    /**
     * Reading Test - Testni o'chirish
     */
    public function destroy(ReadingTest $readingTest)
    {
        $readingTest->delete();

        return redirect()
            ->route('reading-tests.index')
            ->with('success', 'Reading test muvaffaqiyatli o\'chirildi!');
    }

    /**
     * Reading Test - JSON body formatini ko'rish (AJAX)
     */
    public function getBodyFormat()
    {
        $format = [
            'passages' => [
                [
                    'title' => 'Passage 1 Title',
                    'content' => 'Passage 1 content here...',
                    'questions_count' => 13
                ],
                [
                    'title' => 'Passage 2 Title', 
                    'content' => 'Passage 2 content here...',
                    'questions_count' => 13
                ],
                [
                    'title' => 'Passage 3 Title',
                    'content' => 'Passage 3 content here...',
                    'questions_count' => 14
                ]
            ],
            'instructions' => 'General instructions for the reading test',
            'time_limit' => 60,
            'total_questions' => 40
        ];

        return response()->json($format);
    }

    /**
     * Reading Test - App Test bo'yicha reading testlarni olish (AJAX)
     */
    public function getByAppTest(AppTest $appTest)
    {
        $readingTests = ReadingTest::where('app_test_id', $appTest->id)
            ->select('id', 'title', 'body')
            ->get();

        return response()->json($readingTests);
    }

    // ==================== READING TEST ITEM CRUD METHODS ====================

    /**
     * Reading Test Item - Barcha itemlar ro'yxati
     */
    public function itemIndex(ReadingTest $readingTest, Request $request)
    {
        $query = $readingTest->items();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('title', 'like', "%{$search}%");
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->get('type'));
        }

        $items = $query->orderBy('id', 'desc')->paginate(15);
        $types = ReadingTestItem::getTypes();

        return view('reading-tests.items.index', compact('readingTest', 'items', 'types'));
    }

    /**
     * Reading Test Item - Yangi item yaratish formasi
     */
    public function itemCreate(ReadingTest $readingTest)
    {
        $types = ReadingTestItem::getTypes();
        return view('reading-tests.items.create', compact('readingTest', 'types'));
    }

    /**
     * Reading Test Item - Yangi itemni saqlash
     */
    public function itemStore(Request $request, ReadingTest $readingTest)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'nullable|string',
            'type' => ['required', 'string', Rule::in(array_keys(ReadingTestItem::getTypes()))],
        ]);

        $validated['reading_test_id'] = $readingTest->id;
        
        // Agar body bo'sh bo'lsa, default JSON qo'yamiz
        if (empty($validated['body'])) {
            $validated['body'] = json_encode(['title' => '', 'body' => '', 'image_url' => '']);
        }

        ReadingTestItem::create($validated);

        return redirect()
            ->route('reading-tests.items.index', $readingTest)
            ->with('success', 'Reading test item muvaffaqiyatli yaratildi!');
    }

    /**
     * Reading Test Item - Itemni ko'rish
     */
    public function itemShow(ReadingTest $readingTest, ReadingTestItem $item)
    {
        return view('reading-tests.items.show', compact('readingTest', 'item'));
    }

    /**
     * Reading Test Item - Itemni tahrirlash formasi
     */
    public function itemEdit(ReadingTest $readingTest, ReadingTestItem $item)
    {
        $types = ReadingTestItem::getTypes();
        return view('reading-tests.items.edit', compact('readingTest', 'item', 'types'));
    }

    /**
     * Reading Test Item - Itemni yangilash
     */
    public function itemUpdate(Request $request, ReadingTest $readingTest, ReadingTestItem $item)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'nullable|string',
            'type' => ['required', 'string', Rule::in(array_keys(ReadingTestItem::getTypes()))],
        ]);
        
        // Agar body bo'sh bo'lsa, default JSON qo'yamiz
        if (empty($validated['body'])) {
            $validated['body'] = json_encode(['title' => '', 'body' => '', 'image_url' => '']);
        }

        $item->update($validated);

        return redirect()
            ->route('reading-tests.items.index', $readingTest)
            ->with('success', 'Reading test item muvaffaqiyatli yangilandi!');
    }

    /**
     * Reading Test Item - Itemni o'chirish
     */
    public function itemDestroy(ReadingTest $readingTest, ReadingTestItem $item)
    {
        $item->delete();

        return redirect()
            ->route('reading-tests.items.index', $readingTest)
            ->with('success', 'Reading test item muvaffaqiyatli o\'chirildi!');
    }

    /**
     * Reading Test Item - Item body formatini ko'rish (AJAX)
     */
    public function itemGetBodyFormat($type)
    {
        $formats = [
            'passage' => [
                'content' => 'Passage text content here...',
                'word_count' => 300,
                'difficulty_level' => 'intermediate'
            ],
            'question' => [
                'question_text' => 'What is the main idea of the passage?',
                'question_type' => 'multiple_choice',
                'options' => ['A', 'B', 'C', 'D'],
                'correct_answer' => 'A',
                'explanation' => 'Explanation for the correct answer'
            ],
            'instruction' => [
                'instruction_text' => 'Read the passage and answer the questions below.',
                'time_limit' => 20,
                'special_notes' => 'Pay attention to keywords'
            ]
        ];

        $format = $formats[$type] ?? [];
        return response()->json($format);
    }
}
