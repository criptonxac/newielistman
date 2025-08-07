<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppTest extends Model
{
    use HasFactory;

    protected $table = 'app_test';

    protected $fillable = [
        'title',
        'desc',
        'type',
        'is_active',
        'test_time',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function readingTests()
    {
        return $this->hasMany(ReadingTest::class, 'app_test_id');
    }

    public function listeningTests()
    {
        return $this->hasMany(ListeningTest::class, 'app_test_id');
    }

    public function writingTests()
    {
        return $this->hasMany(WritingTest::class, 'app_test_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Accessors
    public function getStatusAttribute()
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    // Constants for test types
    const TYPE_LISTENING = 'listening';
    const TYPE_READING = 'reading';
    const TYPE_WRITING = 'writing';
    const TYPE_SPEAKING = 'speaking';

    public static function getTypes()
    {
        return [
            self::TYPE_LISTENING => 'Listening',
            self::TYPE_READING => 'Reading',
            self::TYPE_WRITING => 'Writing',
            self::TYPE_SPEAKING => 'Speaking',
        ];
    }
}
