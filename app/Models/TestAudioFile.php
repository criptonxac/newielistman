<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TestAudioFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_id',
        'file_path',
        'file_name',
        'duration_seconds',
        'part_number',
        'play_order',
        'auto_play'
    ];

    protected $casts = [
        'auto_play' => 'boolean',
        'duration_seconds' => 'integer',
        'part_number' => 'integer',
        'play_order' => 'integer'
    ];

    // Relationships
    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    // Accessors
    public function getUrlAttribute()
    {
        return Storage::url($this->file_path);
    }

    public function getFormattedDurationAttribute()
    {
        if (!$this->duration_seconds) {
            return '0:00';
        }
        
        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;
        return sprintf('%d:%02d', $minutes, $seconds);
    }

    // Methods
    public function deleteFile()
    {
        try {
            // Try deleting from public storage first
            $publicPath = 'public/' . ltrim($this->file_path, '/');
            if (Storage::exists($publicPath)) {
                Storage::delete($publicPath);
                \Log::info("Deleted file from public storage: " . $publicPath);
                return true;
            }
            
            // If not found in public, try direct path
            if (Storage::exists($this->file_path)) {
                Storage::delete($this->file_path);
                \Log::info("Deleted file using direct path: " . $this->file_path);
                return true;
            }
            
            \Log::warning("File not found for deletion: " . $this->file_path);
            return false;
            
        } catch (\Exception $e) {
            \Log::error("Error deleting file: " . $e->getMessage(), [
                'file_path' => $this->file_path,
                'exception' => $e
            ]);
            return false;
        }
    }
}