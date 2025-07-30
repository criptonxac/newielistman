<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'description',
        'type',
        'duration_minutes',
        'pass_score',
        'is_active',
        'attempts_allowed'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'duration_minutes' => 'integer',
        'pass_score' => 'integer',
        'attempts_allowed' => 'integer'
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function questions()
    {
        return $this->hasMany(TestQuestion::class)->orderBy('part_number')->orderBy('question_number');
    }

    public function audioFiles()
    {
        return $this->hasMany(TestAudioFile::class)->orderBy('part_number')->orderBy('play_order');
    }

    public function attempts()
    {
        return $this->hasMany(TestAttempt::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeListening($query)
    {
        return $query->whereHas('category', function($q) {
            $q->where('slug', 'listening');
        });
    }

    // Methods
    public function getTotalQuestionsAttribute()
    {
        return $this->questions()->count();
    }

    public function getTotalPointsAttribute()
    {
        return $this->questions()->sum('points');
    }

    public function getUserAttemptsCount($userId)
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->count();
    }

    public function canUserAttempt($userId)
    {
        $attemptCount = $this->getUserAttemptsCount($userId);
        return $attemptCount < $this->attempts_allowed;
    }

    public function getQuestionsByPart($partNumber)
    {
        return $this->questions()
            ->where('part_number', $partNumber)
            ->orderBy('question_number')
            ->get();
    }

    public function getAudioByPart($partNumber)
    {
        return $this->audioFiles()
            ->where('part_number', $partNumber)
            ->orderBy('play_order')
            ->get();
    }
}