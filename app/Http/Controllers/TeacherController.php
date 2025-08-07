<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AppTest;
use App\Models\UserTestAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TeacherController extends Controller
{
    public function dashboard(Request $request)
    {
        $stats = [
            'total_tests' => AppTest::count(),
            'total_students' => User::where('role', 'student')->count(),
            'total_attempts' => UserTestAttempt::count(),
            'average_score' => UserTestAttempt::whereNotNull('completed_at')->avg('total_score') ?? 0,
        ];

        $recent_attempts = UserTestAttempt::with(['user', 'test'])
            ->whereHas('user')
            ->whereHas('test')
            ->latest()
            ->limit(10)
            ->get();

        return view('teacher.dashboard', compact('stats', 'recent_attempts'));
    }

    public function students(Request $request)
    {
        $students = User::where('role', 'student')->paginate(20);
        return view('teacher.students', compact('students'));
    }

    public function results(Request $request)
    {
        $results = UserTestAttempt::with(['user', 'test'])
            ->whereHas('user') // Only get attempts where user exists
            ->latest()
            ->paginate(20);
        return view('teacher.results', compact('results'));
    }

    public function exportUser(Request $request, User $user)
    {
        $format = $request->get('format', 'pdf');

        // Get user's test attempts with detailed results
        $attempts = UserTestAttempt::with(['test', 'test.category'])
            ->where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->get();

        // Group results by skill type based on test title
        $skillResults = [
            'listening' => [],
            'reading' => [],
            'writing' => [],
            'speaking' => []
        ];

        foreach ($attempts as $attempt) {
            if ($attempt->test) {
                $testTitle = strtolower($attempt->test->title);
                $categoryName = $attempt->test->category ? strtolower($attempt->test->category->name) : '';

                // Check both test title and category name for skill type
                if (str_contains($testTitle, 'listening') || str_contains($categoryName, 'listening')) {
                    $skillResults['listening'][] = [
                        'test_name' => $attempt->test->title,
                        'score' => $attempt->total_score ?? 0,
                        'date' => $attempt->completed_at->format('d.m.Y')
                    ];
                } elseif (str_contains($testTitle, 'reading') || str_contains($categoryName, 'reading')) {
                    $skillResults['reading'][] = [
                        'test_name' => $attempt->test->title,
                        'score' => $attempt->total_score ?? 0,
                        'date' => $attempt->completed_at->format('d.m.Y')
                    ];
                } elseif (str_contains($testTitle, 'writing') || str_contains($categoryName, 'writing')) {
                    $skillResults['writing'][] = [
                        'test_name' => $attempt->test->title,
                        'score' => $attempt->total_score ?? 0,
                        'date' => $attempt->completed_at->format('d.m.Y')
                    ];
                } elseif (str_contains($testTitle, 'speaking') || str_contains($categoryName, 'speaking')) {
                    $skillResults['speaking'][] = [
                        'test_name' => $attempt->test->title,
                        'score' => $attempt->total_score ?? 0,
                        'date' => $attempt->completed_at->format('d.m.Y')
                    ];
                }
            }
        }

        // Calculate averages
        $averages = [
            'listening' => count($skillResults['listening']) > 0 ?
                array_sum(array_column($skillResults['listening'], 'score')) / count($skillResults['listening']) : 0,
            'reading' => count($skillResults['reading']) > 0 ?
                array_sum(array_column($skillResults['reading'], 'score')) / count($skillResults['reading']) : 0,
            'writing' => count($skillResults['writing']) > 0 ?
                array_sum(array_column($skillResults['writing'], 'score')) / count($skillResults['writing']) : 0,
            'speaking' => count($skillResults['speaking']) > 0 ?
                array_sum(array_column($skillResults['speaking'], 'score')) / count($skillResults['speaking']) : 0,
        ];

        $data = [
            'user' => $user,
            'skillResults' => $skillResults,
            'averages' => $averages,
            'totalAttempts' => $attempts->count(),
            'overallAverage' => array_sum($averages) / 4
        ];

        switch ($format) {
            case 'pdf':
                return $this->exportToPDF($data);
            case 'word':
                return $this->exportToWord($data);
            case 'excel':
                return $this->exportToExcel($data);
            default:
                return $this->exportToPDF($data);
        }
    }

    private function exportToPDF($data)
    {
        // Create HTML content optimized for PDF printing
        $html = view('teacher.exports.user-report', $data)->render();

        // Add PDF-specific styling
        $pdfHtml = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Test Natijalari - ' . $data['user']->name . '</title>
            <style>
                @page { margin: 1cm; size: A4; }
                body { font-family: Arial, sans-serif; font-size: 12px; line-height: 1.4; }
                .header { text-align: center; margin-bottom: 20px; }
                .section { margin-bottom: 15px; }
                .skill-section { page-break-inside: avoid; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f5f5f5; font-weight: bold; }
                .chart-placeholder { height: 200px; border: 2px dashed #ccc; text-align: center; padding: 80px 0; }
            </style>
        </head>
        <body>' . $html . '</body>
        </html>';

        $fileName = 'test-natijalari-' . str_replace(' ', '-', $data['user']->name) . '-' . date('Y-m-d') . '.html';

        return response($pdfHtml)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    private function exportToWord($data)
    {
        // Create HTML content that can be opened in Word
        $html = view('teacher.exports.user-report', $data)->render();

        $fileName = 'test-natijalari-' . $data['user']->name . '-' . date('Y-m-d') . '.doc';

        return response($html)
            ->header('Content-Type', 'application/msword')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    private function exportToExcel($data)
    {
        // Increase execution time for Excel export
        set_time_limit(120);

        $fileName = 'test-natijalari-' . str_replace(' ', '-', $data['user']->name) . '-' . date('Y-m-d') . '.xlsx';

        try {
            return Excel::download(new \App\Exports\UserReportExport($data), $fileName);
        } catch (\Exception $e) {
            // If Excel export fails, fallback to simple CSV-like format
            $csvContent = $this->generateSimpleCSV($data);
            $csvFileName = 'test-natijalari-' . str_replace(' ', '-', $data['user']->name) . '-' . date('Y-m-d') . '.csv';

            return response($csvContent)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $csvFileName . '"');
        }
    }

    private function generateSimpleCSV($data)
    {
        $csv = "Foydalanuvchi Ma'lumotlari\n";
        $csv .= "Ism," . $data['user']->name . "\n";
        $csv .= "Email," . $data['user']->email . "\n";
        $csv .= "Jami Urinishlar," . $data['totalAttempts'] . "\n";
        $csv .= "Umumiy O'rtacha," . number_format($data['overallAverage'], 1) . "%\n\n";

        $csv .= "Ko'nikmalar O'rtachasi\n";
        $csv .= "Listening," . number_format($data['averages']['listening'], 1) . "%," . count($data['skillResults']['listening']) . " ta test\n";
        $csv .= "Reading," . number_format($data['averages']['reading'], 1) . "%," . count($data['skillResults']['reading']) . " ta test\n";
        $csv .= "Writing," . number_format($data['averages']['writing'], 1) . "%," . count($data['skillResults']['writing']) . " ta test\n";
        $csv .= "Speaking," . number_format($data['averages']['speaking'], 1) . "%," . count($data['skillResults']['speaking']) . " ta test\n\n";

        foreach (['listening', 'reading', 'writing', 'speaking'] as $skill) {
            $skillName = ucfirst($skill);
            $csv .= $skillName . " Test Natijalari\n";

            if (count($data['skillResults'][$skill]) > 0) {
                foreach ($data['skillResults'][$skill] as $result) {
                    $csv .= $result['test_name'] . "," . $result['score'] . "%," . $result['date'] . "\n";
                }
            } else {
                $csv .= "Test topshirilmagan,0%,-\n";
            }
            $csv .= "\n";
        }

        return $csv;
    }
}
