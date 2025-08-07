<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListeningTest extends Model
{
    use HasFactory;

    protected $table = 'listening_test';

    public $timestamps = true; // Timestamps yoqildi

    protected $fillable = [
        'app_test_id',
        'title',
        'audio',
    ];

    protected $casts = [
        'audio' => 'array', // JSON ma'lumotni array sifatida cast qilish
    ];

    // Relationships
    public function appTest()
    {
        return $this->belongsTo(AppTest::class, 'app_test_id');
    }

    public function items()
    {
        return $this->hasMany(ListeningTestItem::class, 'listening_test_id');
    }

    // Scopes
    public function scopeByAppTest($query, $appTestId)
    {
        return $query->where('app_test_id', $appTestId);
    }

    // Accessors
    public function getFormattedAudioAttribute()
    {
        return is_array($this->audio) ? $this->audio : json_decode($this->audio, true);
    }

    // Mutators
    public function setAudioAttribute($value)
    {
        $this->attributes['audio'] = is_array($value) ? json_encode($value) : $value;
    }
}
