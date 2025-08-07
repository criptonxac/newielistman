<?php

namespace App\Http\Controllers;

use App\Models\ListeningTest;
use App\Models\ListeningTestItem;
use App\Models\AppTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ListeningTestCrudController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isAdmin() && !Auth::user()->isTeacher()) {
                abort(403, 'Faqat admin va teacher foydalanuvchilar listening test boshqara oladi');
            }
            return $next($request);
        });
    }

    /**
     * Listening Test - Barcha testlarni ko'rsatish
     */
    public function index(Request $request)
    {
        $query = ListeningTest::with('appTest');

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

        // Filter by AppTest
        if ($request->filled('app_test_id')) {
            $query->where('app_test_id', $request->get('app_test_id'));
        }

        $listeningTests = $query->orderBy('id', 'desc')->paginate(15);
        $appTests = AppTest::active()->get();

        $layout = Auth::user()->isAdmin() ? 'layouts.main' : 'layouts.teacher';
        return view('listening-tests.index', compact('listeningTests', 'appTests', 'layout'));
    }

    /**
     * Listening Test - Yangi test yaratish formasi
     */
    public function create()
    {
        $appTests = AppTest::active()->get();
        $layout = Auth::user()->isAdmin() ? 'layouts.main' : 'layouts.teacher';
        return view('listening-tests.create', compact('appTests', 'layout'));
    }

    /**
     * Listening Test - Yangi testni saqlash
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'app_test_id' => 'required|exists:app_test,id',
            'title' => 'required|string|max:255',
            'audio' => 'nullable|string', // Frontend dan JSON string keladi (ixtiyoriy)
            // Audio file upload
            'audio_file' => 'nullable|file|mimes:mp3,wav,ogg,m4a|max:102400', // 100MB max
            // Qo'shimcha maydonlar (ixtiyoriy, agar frontend dan alohida kelsa)
            'audio_title' => 'nullable|string|max:255',
            'audio_url' => 'nullable|url',
            'audio_description' => 'nullable|string',
        ]);

        // Audio file upload qilish`
        $audioUrl = null;
        if ($request->hasFile('audio_file')) {
            $audioFile = $request->file('audio_file');
            $fileName = time() . '_' . $audioFile->getClientOriginalName();
            $audioPath = $audioFile->storeAs('audio/listening-tests', $fileName, 'public');
            $audioUrl = '/storage/' . $audioPath;
        }

        // Audio ma'lumotlarini JSON formatda tayyorlash
        $audioData = [
            'title' => $validated['audio_title'] ?? '',
            'url' => $audioUrl ?? $validated['audio_url'] ?? '', // Upload qilingan fayl URL yoki manual URL
            'description' => $validated['audio_description'] ?? ''
        ];
        
        // Agar JSON string kelgan bo'lsa, uni decode qilib, yangi ma'lumotlar bilan birlashtirish
        if (!empty($validated['audio'])) {
            $existingAudio = json_decode($validated['audio'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($existingAudio)) {
                $audioData = array_merge($existingAudio, array_filter($audioData));
            }
        }

        // Faqat asosiy maydonlarni saqlash
        ListeningTest::create([
            'app_test_id' => $validated['app_test_id'],
            'title' => $validated['title'],
            'audio' => json_encode($audioData)
        ]);

        return redirect()
            ->route('listening-tests.index')
            ->with('success', 'Listening test muvaffaqiyatli yaratildi!');
    }

    /**
     * Listening Test - Bitta testni ko'rsatish
     */
    public function show(ListeningTest $listeningTest)
    {
        $layout = Auth::user()->isAdmin() ? 'layouts.main' : 'layouts.teacher';
        return view('listening-tests.show', compact('listeningTest', 'layout'));
    }

    /**
     * Listening Test - Testni tahrirlash formasi
     */
    public function edit(ListeningTest $listeningTest)
    {
        $appTests = AppTest::active()->get();
        $layout = Auth::user()->isAdmin() ? 'layouts.main' : 'layouts.teacher';
        return view('listening-tests.edit', compact('listeningTest', 'appTests', 'layout'));
    }

    /**
     * Listening Test - Testni yangilash
     */
    public function update(Request $request, ListeningTest $listeningTest)
    {
        $validated = $request->validate([
            'app_test_id' => 'required|exists:app_test,id',
            'title' => 'required|string|max:255',
            'audio' => 'nullable|string', // Frontend dan JSON string keladi (ixtiyoriy)
            // Audio file upload
            'audio_file' => 'nullable|file|mimes:mp3,wav,ogg,m4a|max:20480', // 20MB max
            // Qo'shimcha maydonlar (ixtiyoriy, agar frontend dan alohida kelsa)
            'audio_title' => 'nullable|string|max:255',
            'audio_url' => 'nullable|url',
            'audio_description' => 'nullable|string',
        ]);

        // Audio file upload qilish
        $audioUrl = null;
        if ($request->hasFile('audio_file')) {
            // Eski faylni o'chirish (ixtiyoriy)
            $oldAudio = json_decode($listeningTest->audio, true);
            if (isset($oldAudio['url']) && str_starts_with($oldAudio['url'], '/storage/')) {
                $oldPath = str_replace('/storage/', '', $oldAudio['url']);
                \Storage::disk('public')->delete($oldPath);
            }
            
            // Yangi faylni yuklash
            $audioFile = $request->file('audio_file');
            $fileName = time() . '_' . $audioFile->getClientOriginalName();
            $audioPath = $audioFile->storeAs('audio/listening-tests', $fileName, 'public');
            $audioUrl = '/storage/' . $audioPath;
        }

        // Mavjud audio ma'lumotlarini olish
        $existingAudio = json_decode($listeningTest->audio, true) ?? [];
        
        // Audio ma'lumotlarini yangilash
        $audioData = [
            'title' => $validated['audio_title'] ?? $existingAudio['title'] ?? '',
            'url' => $audioUrl ?? $validated['audio_url'] ?? $existingAudio['url'] ?? '',
            'description' => $validated['audio_description'] ?? $existingAudio['description'] ?? ''
        ];
        
        // Agar JSON string kelgan bo'lsa, uni decode qilib, yangi ma'lumotlar bilan birlashtirish
        if (!empty($validated['audio'])) {
            $newAudio = json_decode($validated['audio'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($newAudio)) {
                $audioData = array_merge($audioData, array_filter($newAudio));
            }
        }

        // Faqat asosiy maydonlarni yangilash
        $listeningTest->update([
            'app_test_id' => $validated['app_test_id'],
            'title' => $validated['title'],
            'audio' => json_encode($audioData)
        ]);

        return redirect()
            ->route('listening-tests.index')
            ->with('success', 'Listening test muvaffaqiyatli yangilandi!');
    }

    /**
     * Listening Test - Testni o'chirish
     */
    public function destroy(ListeningTest $listeningTest)
    {
        $listeningTest->delete();

        return redirect()
            ->route('listening-tests.index')
            ->with('success', 'Listening test muvaffaqiyatli o\'chirildi!');
    }

    // ==========================================
    // LISTENING TEST ITEMS CRUD METHODS
    // ==========================================

    /**
     * Listening Test Items - Barcha itemlarni ko'rsatish
     */
    public function itemIndex(ListeningTest $listeningTest)
    {
        \Log::info('ItemIndex called', ['test_id' => $listeningTest->id]);
        
        $items = $listeningTest->items()->orderBy('id', 'desc')->paginate(15);
        $layout = Auth::user()->isAdmin() ? 'layouts.main' : 'layouts.teacher';
        return view('listening-tests.items.index', compact('listeningTest', 'items', 'layout'));
    }

    /**
     * Listening Test Items - Yangi item yaratish formasi
     */
    public function itemCreate(ListeningTest $listeningTest)
    {
        $layout = Auth::user()->isAdmin() ? 'layouts.main' : 'layouts.teacher';
        return view('listening-tests.items.create', compact('listeningTest', 'layout'));
    }

    /**
     * Listening Test Items - Yangi itemni saqlash
     */
    public function itemStore(Request $request, ListeningTest $listeningTest)
    {
        \Log::info('ItemStore called', ['request_data' => $request->all()]);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:audio,question,instruction',
            'body' => 'nullable|string', // Frontend dan JSON string keladi
            // Qo'shimcha maydonlar (ixtiyoriy)
            'item_title' => 'nullable|string|max:255',
            'item_content' => 'nullable|string',
            'item_options' => 'nullable|string',
        ]);

        // Agar alohida item maydonlari kelgan bo'lsa, ularni JSON ga yig'amiz
        if ($request->has('item_title') || $request->has('item_content') || $request->has('item_options')) {
            $itemData = [
                'title' => $validated['item_title'] ?? '',
                'content' => $validated['item_content'] ?? '',
                'options' => $validated['item_options'] ?? ''
            ];
            $validated['body'] = json_encode($itemData);
        }


        // Agar body bo'sh bo'lsa, default JSON qo'yamiz
        if (empty($validated['body'])) {
            $validated['body'] = json_encode(['title' => '', 'content' => '', 'options' => '']);
        }
        // JSON formatini tekshirish
        $bodyArray = json_decode($validated['body'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return back()->withErrors(['body' => 'Body maydonida to\'g\'ri JSON format bo\'lishi kerak.'])->withInput();
        }

        $listeningTest->items()->create([
            'title' => $validated['title'],
            'type' => $validated['type'],
            'body' => $validated['body']
        ]);

        return redirect()
            ->route('listening-tests.items.index', $listeningTest)
            ->with('success', 'Listening test item muvaffaqiyatli yaratildi!');
    }

    /**
     * Listening Test Items - Bitta itemni ko'rsatish
     */
    public function itemShow(ListeningTest $listeningTest, ListeningTestItem $item)
    {
        $layout = Auth::user()->isAdmin() ? 'layouts.main' : 'layouts.teacher';
        return view('listening-tests.items.show', compact('listeningTest', 'item', 'layout'));
    }

    /**
     * Listening Test Items - Itemni tahrirlash formasi
     */
    public function itemEdit(ListeningTest $listeningTest, ListeningTestItem $item)
    {
        $layout = Auth::user()->isAdmin() ? 'layouts.main' : 'layouts.teacher';
        return view('listening-tests.items.edit', compact('listeningTest', 'item', 'layout'));
    }

    /**
     * Listening Test Items - Itemni yangilash
     */
    public function itemUpdate(Request $request, ListeningTest $listeningTest, ListeningTestItem $item)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:audio,question,instruction',
            'body' => 'nullable|string', // Frontend dan JSON string keladi
            // Qo'shimcha maydonlar (ixtiyoriy)
            'item_title' => 'nullable|string|max:255',
            'item_content' => 'nullable|string',
            'item_options' => 'nullable|string',
        ]);

        // Agar alohida item maydonlari kelgan bo'lsa, ularni JSON ga yig'amiz
        if ($request->has('item_title') || $request->has('item_content') || $request->has('item_options')) {
            $itemData = [
                'title' => $validated['item_title'] ?? '',
                'content' => $validated['item_content'] ?? '',
                'options' => $validated['item_options'] ?? ''
            ];
            $validated['body'] = json_encode($itemData);
        }
        
        // Agar body bo'sh bo'lsa, default JSON qo'yamiz
        if (empty($validated['body'])) {
            $validated['body'] = json_encode(['title' => '', 'content' => '', 'options' => '']);
        }

        // JSON formatini tekshirish
        $bodyArray = json_decode($validated['body'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return back()->withErrors(['body' => 'Body maydonida to\'g\'ri JSON format bo\'lishi kerak.'])->withInput();
        }

        $item->update([
            'title' => $validated['title'],
            'type' => $validated['type'],
            'body' => $validated['body']
        ]);

        return redirect()
            ->route('listening-tests.items.index', $listeningTest)
            ->with('success', 'Listening test item muvaffaqiyatli yangilandi!');
    }

    /**
     * Listening Test Items - Itemni o'chirish
     */
    public function itemDestroy(ListeningTest $listeningTest, ListeningTestItem $item)
    {
        $item->delete();

        return redirect()
            ->route('listening-tests.items.index', $listeningTest)
            ->with('success', 'Listening test item muvaffaqiyatli o\'chirildi!');
    }
}
