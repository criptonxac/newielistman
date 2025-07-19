
<?php

require_once 'vendor/autoload.php';

// Laravel environmentni yuklash
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Models\TestQuestion;
use App\Models\Test;

// Masalan, 1-testga yangi savol qo'shish
$test = Test::find(1); // Listening test

if ($test) {
    $newQuestion = TestQuestion::create([
        'test_id' => $test->id,
        'question_number' => $test->questions()->count() + 1,
        'question_type' => 'fill_blank',
        'question_text' => 'The conference will be held in the _______ building.',
        'correct_answer' => 'main',
        'acceptable_answers' => ['main', 'central', 'primary'],
        'points' => 1,
        'explanation' => 'Speaker clearly mentions the main building.',
        'sort_order' => $test->questions()->count() + 1
    ]);
    
    // Test total_questions sonini yangilash
    $test->update([
        'total_questions' => $test->questions()->count()
    ]);
    
    echo "Yangi savol qo'shildi! ID: " . $newQuestion->id . "\n";
} else {
    echo "Test topilmadi!\n";
}
?>
