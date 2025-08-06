<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestQuestion extends Model
{
    use HasFactory;

    protected $guarded = []; // Barcha maydonlarni mass assignment uchun ochiq qilish
    
    // Yoki aniq maydonlarni belgilash kerak bo'lsa:
    // protected $fillable = [
    //     'test_id',
    //     'part_number',
    //     'question_number',
    //     'question_text',
    //     'question_type',
    //     'options',
    //     'correct_answer',
    //     'correct_answers',
    //     'points',
    //     'explanation',
    //     'image_path',
    //     'sort_order',
    //     'created_by'
    // ];
    
    // created_at va updated_at avtomatik to'ldiriladi, shuning uchun ularni kiritish shart emas

    protected $casts = [
        'options' => 'array',
        'correct_answers' => 'array',
        'form_structure' => 'array',
        'drag_items' => 'array',
        'drop_zones' => 'array',
        'points' => 'integer',
        'part_number' => 'integer',
        'question_number' => 'integer',
        'show_option_letters' => 'boolean',
        'min_words' => 'integer',
        'max_words' => 'integer'
    ];

    // Relationships
    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function answers()
    {
        return $this->hasMany(TestAnswer::class);
    }

    // Methods
    public function checkAnswer($userAnswer)
    {
        if ($this->question_type === 'multiple_choice' || $this->question_type === 'true_false') {
            return strtolower(trim($userAnswer)) === strtolower(trim($this->correct_answer));
        }
        
        if ($this->question_type === 'fill_blank') {
            // Case insensitive comparison for fill blank
            $correctAnswer = strtolower(trim($this->correct_answer));
            $userAnswer = strtolower(trim($userAnswer));
            
            // Check for alternative answers if stored as JSON
            if ($this->correct_answers && is_array($this->correct_answers)) {
                $alternatives = array_map(function($ans) {
                    return strtolower(trim($ans));
                }, $this->correct_answers);
                
                return in_array($userAnswer, $alternatives) || $userAnswer === $correctAnswer;
            }
            
            return $userAnswer === $correctAnswer;
        }
        
        if ($this->question_type === 'drag_drop') {
            // For complex question types, implement custom logic
            return $this->checkComplexAnswer($userAnswer);
        }
        
        return false;
    }

    private function checkComplexAnswer($userAnswer)
    {
        // Implement logic for complex question types
        // This could involve JSON comparison, partial matching, etc.
        if (!is_array($userAnswer) || !is_array($this->correct_answers)) {
            return false;
        }
        
        // Simple array comparison for now
        return $userAnswer == $this->correct_answers;
    }

    public function getFormattedQuestionNumber()
    {
        return sprintf("Question %d", $this->question_number);
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image_path) {
            return null;
        }
        return Storage::url($this->image_path);
    }
}