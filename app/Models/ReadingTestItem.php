<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadingTestItem extends Model
{
    use HasFactory;

    protected $table = 'reading_test_items';

    public $timestamps = false; // Chunki jadvalda timestamps yo'q

    protected $fillable = [
        'reading_test_id',
        'title',
        'body',
        'type',
    ];

    protected $casts = [
        'body' => 'array', // JSON ma'lumotni array sifatida cast qilish
    ];

    // Relationships
    public function readingTest()
    {
        return $this->belongsTo(ReadingTest::class, 'reading_test_id');
    }

    // Scopes
    public function scopeByReadingTest($query, $readingTestId)
    {
        return $query->where('reading_test_id', $readingTestId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Accessors
    public function getFormattedBodyAttribute()
    {
        return is_array($this->body) ? $this->body : json_decode($this->body, true);
    }

    // Mutators
    public function setBodyAttribute($value)
    {
        $this->attributes['body'] = is_array($value) ? json_encode($value) : $value;
    }

    // Constants for item types
    const TYPE_PASSAGE = 'passage';
    const TYPE_QUESTION = 'question';
    const TYPE_INSTRUCTION = 'instruction';

    public static function getTypes()
    {
        return [
            self::TYPE_PASSAGE => 'Passage',
            self::TYPE_QUESTION => 'Question',
            self::TYPE_INSTRUCTION => 'Instruction',
        ];
    }
}
