
<?php

namespace App\Exports;

use App\Models\UserTestAttempt;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TestResultsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return UserTestAttempt::with(['user', 'test.category'])
            ->completed()
            ->orderBy('completed_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Talaba nomi',
            'Email',
            'Test nomi',
            'Kategoriya',
            'Jami savollar',
            'To\'g\'ri javoblar',
            'Jami ball',
            'Foiz',
            'Boshlangan vaqt',
            'Tugallangan vaqt',
            'Davomiyligi (daqiqa)'
        ];
    }

    public function map($attempt): array
    {
        $duration = $attempt->started_at && $attempt->completed_at 
            ? $attempt->started_at->diffInMinutes($attempt->completed_at)
            : 0;

        return [
            $attempt->id,
            $attempt->user->name,
            $attempt->user->email,
            $attempt->test->title,
            $attempt->test->category->name,
            $attempt->total_questions,
            $attempt->correct_answers,
            $attempt->total_score,
            $attempt->score_percentage . '%',
            $attempt->started_at ? $attempt->started_at->format('d.m.Y H:i') : '',
            $attempt->completed_at ? $attempt->completed_at->format('d.m.Y H:i') : '',
            $duration
        ];
    }
}
