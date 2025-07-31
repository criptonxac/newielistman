<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestCategory extends Model
{
    use HasFactory;
    
    protected $table = 'test_categories';

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
        'is_active' => 'boolean'
    ];

    // Relationships
    public function tests()
    {
        return $this->hasMany(Test::class);
    }
    
    public function activeTests()
    {
        return $this->hasMany(Test::class)->where('is_active', true);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Accessors & Mutators
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = \Str::slug($value);
    }
}