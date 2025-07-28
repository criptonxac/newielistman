<?php

namespace App\Models;

use App\Enums\TestStatus;
use App\Enums\TestType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Test extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description', 
        'test_category_id',
        'type',
        'duration_minutes',
        'total_questions',
        'instructions',
        'resources',
        'is_active',
        'is_timed'
    ];

    protected $casts = [
        'instructions' => 'array',
        'resources' => 'array',
        'is_active' => 'boolean',
        'is_timed' => 'boolean',
        'duration_minutes' => 'integer',
        'total_questions' => 'integer',
        'type' => TestType::class,
        'status' => TestStatus::class
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(TestCategory::class, 'test_category_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(TestQuestion::class)->orderBy('sort_order')->orderBy('question_number');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(UserTestAttempt::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function getRouteKeyName()
    {
        return 'id';
    }
}
