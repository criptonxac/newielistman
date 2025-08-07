<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListeningTestItem extends Model
{
    use HasFactory;

    protected $table = 'listening_test_items';

    public $timestamps = true; // Timestamps yoqildi

    protected $fillable = [
        'listening_test_id',
        'title',
        'body',
        'type',
    ];

    protected $casts = [
        'body' => 'array', // JSON ma'lumotni array sifatida cast qilish
    ];

    // Relationships
    public function listeningTest()
    {
        return $this->belongsTo(ListeningTest::class, 'listening_test_id');
    }

    // Scopes
    public function scopeByListeningTest($query, $listeningTestId)
    {
        return $query->where('listening_test_id', $listeningTestId);
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
}
