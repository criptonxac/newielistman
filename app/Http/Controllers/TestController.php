<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\TestAnswer;
use App\Models\TestCategory;
use App\Models\AppTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isStudent()) {
                abort(403, 'Faqat talabalar test topshira oladi');
            }
            return $next($request);
        })->except(['appTestIndex', 'appTestCreate', 'appTestStore', 'appTestShow', 'appTestEdit', 'appTestUpdate', 'appTestDestroy', 'appTestToggleStatus']);
    }

 
    public function appTestIndex(Request $request)
    {
        $query = AppTest::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('desc', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->get('type'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $status = $request->get('status') === 'active';
            $query->where('is_active', $status);
        }

        $appTests = $query->orderBy('created_at', 'desc')->paginate(15);
        $types = AppTest::getTypes();

        $layout = 'layouts.teacher';
        return view('app-tests.index', compact('appTests', 'types', 'layout'));
    }

    /**
     * AppTest - Yangi test yaratish formasi
     */
    public function appTestCreate()
    {
        $types = AppTest::getTypes();
        $layout = 'layouts.teacher';
        return view('app-tests.create', compact('types', 'layout'));
    }

    /**
     * AppTest - Yangi testni saqlash
     */
    public function appTestStore(Request $request)
    {
        // if (!Auth::user()->isAdmin() && !Auth::user()->isTeacher()) {
        //     abort(403, 'Faqat admin va teacher foydalanuvchilar test yarata oladi');
        // }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'desc' => 'required|string',
            'type' => ['required', 'string', Rule::in(array_keys(AppTest::getTypes()))],
            'is_active' => 'boolean',
            'test_time' => 'required|string|max:50',
        ]);

        $validated['is_active'] = $request->has('is_active');

        AppTest::create($validated);

        return redirect()
            ->route('tests.app-tests.index')
            ->with('success', 'Test muvaffaqiyatli yaratildi!');
    }

    /**
     * AppTest - Testni ko'rish
     */
    public function appTestShow(AppTest $appTest)
    {
        // if (!Auth::user()->isAdmin() && !Auth::user()->isTeacher()) {
        //     abort(403, 'Faqat admin va teacher foydalanuvchilar test ko\'ra oladi');
        // }

        $layout = 'layouts.teacher'; // Vaqtincha teacher layout ishlatamiz
        return view('app-tests.show', compact('appTest', 'layout'));
    }

    /**
     * AppTest - Testni tahrirlash formasi
     */
    public function appTestEdit(AppTest $appTest)
    {
        // if (!Auth::user()->isAdmin() && !Auth::user()->isTeacher()) {
        //     abort(403, 'Faqat admin va teacher foydalanuvchilar test tahrir qila oladi');
        // }

        $types = AppTest::getTypes();
        $layout = 'layouts.teacher'; // Vaqtincha teacher layout ishlatamiz
        return view('app-tests.edit', compact('appTest', 'types', 'layout'));
    }

    /**
     * AppTest - Testni yangilash
     */
    public function appTestUpdate(Request $request, AppTest $appTest)
    {
        // if (!Auth::user()->isAdmin() && !Auth::user()->isTeacher()) {
        //     abort(403, 'Faqat admin va teacher foydalanuvchilar test tahrir qila oladi');
        // }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'desc' => 'required|string',
            'type' => ['required', 'string', Rule::in(array_keys(AppTest::getTypes()))],
            'is_active' => 'boolean',
            'test_time' => 'required|string|max:50',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $appTest->update($validated);

        return redirect()
            ->route('tests.app-tests.index')
            ->with('success', 'Test muvaffaqiyatli yangilandi!');
    }

    /**
     * AppTest - Testni o'chirish
     */
    public function appTestDestroy(AppTest $appTest)
    {
        // if (!Auth::user()->isAdmin() && !Auth::user()->isTeacher()) {
        //     abort(403, 'Faqat admin va teacher foydalanuvchilar test o\'chira oladi');
        // }

        $appTest->delete();

        return redirect()
            ->route('tests.app-tests.index')
            ->with('success', 'Test muvaffaqiyatli o\'chirildi!');
    }

    /**
     * AppTest - Test statusini o'zgartirish (Active/Inactive)
     */
    public function appTestToggleStatus(AppTest $appTest)
    {
        // if (!Auth::user()->isAdmin() && !Auth::user()->isTeacher()) {
        //     abort(403, 'Faqat admin va teacher foydalanuvchilar test statusini o\'zgartira oladi');
        // }

        $appTest->update([
            'is_active' => !$appTest->is_active
        ]);

        $status = $appTest->is_active ? 'faollashtirildi' : 'nofaol qilindi';
        
        return redirect()
            ->back()
            ->with('success', "Test {$status}!");
    }
}