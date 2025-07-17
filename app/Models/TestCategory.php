<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestCategory extends Model
{
    protected $fillable = [
        'name',
        'slug', 
        'description',
        'icon',
        'duration_minutes',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'duration_minutes' => 'integer',
        'sort_order' => 'integer'
    ];

    public function tests(): HasMany
    {
        return $this->hasMany(Test::class);
    }

    public function activeTests(): HasMany
    {
        return $this->hasMany(Test::class)->where('is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function getActiveTestsCountAttribute()
    {
        return $this->activeTests()->count();
    }
}
