<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WritingTest extends Model
{
    use HasFactory;

    protected $table = 'writing_test';

    public $timestamps = true; // Timestamps yoqildi

    protected $fillable = [
        'app_test_id',
        'title',
        'questions',
        'answer',
    ];

    protected $casts = [
        'questions' => 'array', // JSON ma'lumotni array sifatida cast qilish
        'answer' => 'array', // JSON ma'lumotni array sifatida cast qilish
    ];

    // Relationships
    public function appTest()
    {
        return $this->belongsTo(AppTest::class, 'app_test_id');
    }

    // Scopes
    public function scopeByAppTest($query, $appTestId)
    {
        return $query->where('app_test_id', $appTestId);
    }

    // Accessors
    public function getFormattedQuestionsAttribute()
    {
        return is_array($this->questions) ? $this->questions : json_decode($this->questions, true);
    }

    public function getFormattedAnswerAttribute()
    {
        return is_array($this->answer) ? $this->answer : json_decode($this->answer, true);
    }

    // Mutators
    public function setQuestionsAttribute($value)
    {
        $this->attributes['questions'] = is_array($value) ? json_encode($value) : $value;
    }

    public function setAnswerAttribute($value)
    {
        $this->attributes['answer'] = is_array($value) ? json_encode($value) : $value;
    }
}
