<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AudioController extends Controller
{
    /**
     * Audio fayllarni yuklash uchun controller
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(\App\Http\Middleware\CheckRole::class.':admin,teacher');
    }

    /**
     * Audio faylni yuklash
     */
    public function upload(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'audio_file' => 'required|file|mimes:mp3,wav,ogg,m4a,aac|max:102400', // 100MB
                'part' => 'nullable|string'
            ]);

            if (!$request->hasFile('audio_file')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Audio fayl topilmadi'
                ], 400);
            }

            $file = $request->file('audio_file');
            $part = $request->input('part', 'part1');
            
            // Generate a unique filename
            $filename = 'listening-' . $part . '-' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            
            // Store the file
            $path = $file->storeAs('public/audio', $filename);
            
            // Create a public URL
            $url = Storage::url($path);
            
            Log::info('Audio file uploaded successfully', [
                'filename' => $filename,
                'path' => $path,
                'url' => $url,
                'part' => $part
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Audio fayl muvaffaqiyatli yuklandi',
                'filename' => $filename,
                'url' => $url,
                'part' => $part
            ]);
        } catch (\Exception $e) {
            Log::error('Audio upload error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Audio faylni yuklashda xatolik: ' . $e->getMessage()
            ], 500);
        }
    }
}
