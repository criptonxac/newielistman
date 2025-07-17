<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestQuestion extends Model
{
    protected $fillable = [
        'test_id',
        'question_number',
        'question_type',
        'question_text',
        'options',
        'correct_answer',
        'acceptable_answers',
        'points',
        'explanation',
        'resources',
        'sort_order'
    ];

    protected $casts = [
        'options' => 'array',
        'acceptable_answers' => 'array',
        'resources' => 'array',
        'points' => 'integer',
        'question_number' => 'integer',
        'sort_order' => 'integer'
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    public function userAnswers(): HasMany
    {
        return $this->hasMany(UserAnswer::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('question_number');
    }

    public function isCorrectAnswer($userAnswer): bool
    {
        if (empty($userAnswer)) {
            return false;
        }

        // Agar acceptable_answers mavjud bo'lsa, ularni tekshir
        if (!empty($this->acceptable_answers)) {
            return in_array(strtolower(trim($userAnswer)), array_map('strtolower', $this->acceptable_answers));
        }

        // Aks holda, correct_answer bilan solishtir
        return strtolower(trim($userAnswer)) === strtolower(trim($this->correct_answer ?? ''));
    }
}
