<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_attempt_id',
        'test_question_id',
        'user_answer',
        'is_correct',
        'points_earned',
        'time_spent_seconds'
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'points_earned' => 'integer',
        'time_spent_seconds' => 'integer'
    ];

    // Relationships
    public function testAttempt()
    {
        return $this->belongsTo(TestAttempt::class);
    }

    public function testQuestion()
    {
        return $this->belongsTo(TestQuestion::class);
    }

    // Methods
    public function checkAndScore()
    {
        $question = $this->testQuestion;
        $this->is_correct = $question->checkAnswer($this->user_answer);
        $this->points_earned = $this->is_correct ? $question->points : 0;
        $this->save();
    }
}