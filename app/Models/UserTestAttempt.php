<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserTestAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'test_id',
        'started_at',
        'completed_at',
        'total_score',
        'total_questions',
        'correct_answers',
        'results',
        'status',
        'session_id'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'results' => 'array',
        'total_score' => 'integer',
        'total_questions' => 'integer',
        'correct_answers' => 'integer'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    public function userAnswers(): HasMany
    {
        return $this->hasMany(UserAnswer::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeBySession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    public function calculateScore()
    {
        $this->correct_answers = $this->userAnswers()->where('is_correct', true)->count();
        $this->total_score = $this->userAnswers()->sum('points_earned');
        $this->save();
    }

    public function getScorePercentageAttribute()
    {
        if ($this->total_questions > 0) {
            return round(($this->correct_answers / $this->total_questions) * 100, 1);
        }
        return 0;
    }
}
