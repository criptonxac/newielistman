<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAnswer extends Model
{
    protected $fillable = [
        'user_test_attempt_id',
        'test_question_id', 
        'user_answer',
        'is_correct',
        'points_earned',
        'answered_at'
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'points_earned' => 'integer',
        'answered_at' => 'datetime'
    ];

    public function userTestAttempt(): BelongsTo
    {
        return $this->belongsTo(UserTestAttempt::class);
    }

    public function testQuestion(): BelongsTo
    {
        return $this->belongsTo(TestQuestion::class);
    }

    public function scopeCorrect($query)
    {
        return $query->where('is_correct', true);
    }

    public function scopeIncorrect($query)
    {
        return $query->where('is_correct', false);
    }
}
