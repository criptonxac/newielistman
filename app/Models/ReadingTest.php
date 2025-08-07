<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadingTest extends Model
{
    use HasFactory;

    protected $table = 'reading_test';

    public $timestamps = true; // Timestamps yoqildi

    protected $fillable = [
        'app_test_id',
        'title',
        'body',
    ];

    protected $casts = [
        'body' => 'array', // JSON ma'lumotni array sifatida cast qilish
    ];

    // Relationships
    public function appTest()
    {
        return $this->belongsTo(AppTest::class, 'app_test_id');
    }

    public function items()
    {
        return $this->hasMany(ReadingTestItem::class, 'reading_test_id');
    }

    // Scopes
    public function scopeByAppTest($query, $appTestId)
    {
        return $query->where('app_test_id', $appTestId);
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
}
