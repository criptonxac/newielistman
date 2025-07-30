<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'test_id',
        'started_at',
        'completed_at',
        'time_spent_seconds',
        'score',
        'correct_answers',
        'wrong_answers',
        'status',
        'answers'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'time_spent_seconds' => 'integer',
        'score' => 'decimal:2',
        'correct_answers' => 'integer',
        'wrong_answers' => 'integer',
        'answers' => 'array'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function testAnswers()
    {
        return $this->hasMany(TestAnswer::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    // Methods
    public function complete()
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->time_spent_seconds = $this->started_at->diffInSeconds($this->completed_at);
        $this->calculateScore();
        $this->save();
    }

    public function calculateScore()
    {
        $totalPoints = $this->test->total_points;
        $earnedPoints = $this->testAnswers()->sum('points_earned');
        $this->correct_answers = $this->testAnswers()->where('is_correct', true)->count();
        $this->wrong_answers = $this->testAnswers()->where('is_correct', false)->count();
        
        if ($totalPoints > 0) {
            $this->score = round(($earnedPoints / $totalPoints) * 100, 2);
        } else {
            $this->score = 0;
        }
    }

    public function isPassed()
    {
        return $this->score >= $this->test->pass_score;
    }

    public function getFormattedDurationAttribute()
    {
        if (!$this->time_spent_seconds) {
            return '0:00';
        }
        
        $hours = floor($this->time_spent_seconds / 3600);
        $minutes = floor(($this->time_spent_seconds % 3600) / 60);
        $seconds = $this->time_spent_seconds % 60;
        
        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        }
        
        return sprintf('%d:%02d', $minutes, $seconds);
    }

    public function getRemainingTime()
    {
        if ($this->status !== 'in_progress') {
            return 0;
        }
        
        $totalSeconds = $this->test->duration_minutes * 60;
        $elapsedSeconds = $this->started_at->diffInSeconds(now());
        $remaining = $totalSeconds - $elapsedSeconds;
        
        return max(0, $remaining);
    }
}