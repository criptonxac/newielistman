<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    const ROLE_ADMIN = 'admin';
    const ROLE_TEACHER = 'teacher';
    const ROLE_STUDENT = 'student';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'date_of_birth',
        'is_active'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'is_active' => 'boolean'
        ];
    }

    // ===== RELATIONSHIPS =====
    
    /**
     * Get all test attempts for the user (yangi nom bilan)
     */
    public function listeningTestAttempts(): HasMany
    {
        return $this->hasMany(TestAttempt::class);
    }

    /**
     * Eski UserTestAttempt relationship (compatibility uchun)
     */
    public function testAttempts(): HasMany
    {
        return $this->hasMany(UserTestAttempt::class);
    }

    /**
     * Get completed listening test attempts
     */
    public function completedListeningAttempts(): HasMany
    {
        return $this->hasMany(TestAttempt::class)->where('status', 'completed');
    }

    /**
     * Eski completed attempts (compatibility uchun)
     */
    public function completedAttempts(): HasMany
    {
        return $this->hasMany(UserTestAttempt::class)->where('status', 'completed');
    }

    /**
     * Get in-progress test attempts
     */
    public function inProgressAttempts(): HasMany
    {
        return $this->hasMany(TestAttempt::class)->where('status', 'in_progress');
    }

    // ===== ROLE METHODS =====

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isTeacher(): bool
    {
        return $this->role === self::ROLE_TEACHER;
    }

    public function isStudent(): bool
    {
        return $this->role === self::ROLE_STUDENT;
    }

    // ===== LISTENING TEST SPECIFIC METHODS =====

    /**
     * Check if user has completed a specific test
     */
    public function hasCompletedTest($testId): bool
    {
        return $this->listeningTestAttempts()
            ->where('test_id', $testId)
            ->where('status', 'completed')
            ->exists();
    }

    /**
     * Get user's score for a specific test
     */
    public function getTestScore($testId): ?float
    {
        $attempt = $this->listeningTestAttempts()
            ->where('test_id', $testId)
            ->where('status', 'completed')
            ->latest()
            ->first();
            
        return $attempt ? $attempt->score : null;
    }

    /**
     * Get user's best score for a specific test
     */
    public function getBestTestScore($testId): ?float
    {
        return $this->listeningTestAttempts()
            ->where('test_id', $testId)
            ->where('status', 'completed')
            ->max('score');
    }

    /**
     * Check if user can attempt a test
     */
    public function canAttemptTest($testId): bool
    {
        $test = Test::find($testId);
        if (!$test) {
            return false;
        }

        $attemptCount = $this->listeningTestAttempts()
            ->where('test_id', $testId)
            ->where('status', 'completed')
            ->count();

        return $attemptCount < $test->attempts_allowed;
    }

    /**
     * Get current in-progress attempt for a test
     */
    public function getCurrentAttempt($testId)
    {
        return $this->listeningTestAttempts()
            ->where('test_id', $testId)
            ->where('status', 'in_progress')
            ->latest()
            ->first();
    }

    /**
     * Get listening test statistics
     */
    public function getListeningStats(): array
    {
        $attempts = $this->completedListeningAttempts()
            ->whereHas('test.category', function($query) {
                $query->where('slug', 'listening');
            });

        return [
            'total_tests' => $attempts->count(),
            'average_score' => round($attempts->avg('score') ?? 0, 2),
            'best_score' => $attempts->max('score') ?? 0,
            'total_time_spent' => $attempts->sum('time_spent_seconds'),
            'last_attempt' => $attempts->latest()->first()
        ];
    }

    /**
     * Get test attempts by category
     */
    public function getAttemptsByCategory($categorySlug)
    {
        return $this->listeningTestAttempts()
            ->whereHas('test.category', function($query) use ($categorySlug) {
                $query->where('slug', $categorySlug);
            })
            ->with(['test', 'test.category'])
            ->latest()
            ->get();
    }

    // ===== ESKI METHODS (compatibility uchun saqlanmoqda) =====

    public function getAverageScore(): float
    {
        // Ikkala attempt turini hisobga olish
        $oldAverage = $this->completedAttempts()->avg('total_score') ?? 0;
        $newAverage = $this->completedListeningAttempts()->avg('score') ?? 0;
        
        // Agar ikkala tur ham mavjud bo'lsa, o'rtachasini olish
        if ($oldAverage > 0 && $newAverage > 0) {
            return ($oldAverage + $newAverage) / 2;
        }
        
        return $oldAverage ?: $newAverage;
    }

    public function getTotalTests(): int
    {
        // Ikkala attempt turini ham hisoblash
        return $this->completedAttempts()->count() + 
               $this->completedListeningAttempts()->count();
    }

    /**
     * Get detailed test history
     */
    public function getTestHistory($limit = 10)
    {
        return $this->listeningTestAttempts()
            ->with(['test', 'test.category'])
            ->completed()
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get tests available for user
     */
    public function getAvailableTests()
    {
        return Test::active()
            ->with(['category', 'audioFiles'])
            ->get()
            ->filter(function($test) {
                return $this->canAttemptTest($test->id);
            });
    }

    public static function getRoles(): array
    {
        return [
            self::ROLE_ADMIN => 'Administrator',
            self::ROLE_TEACHER => 'O\'qituvchi',
            self::ROLE_STUDENT => 'Talaba'
        ];
    }
}