<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestPassage extends Model
{
    protected $fillable = [
        'test_id',
        'title',
        'content',
        'part',
        'sort_order',
    ];

    /**
     * Get the test that owns the passage
     */
    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }
}
