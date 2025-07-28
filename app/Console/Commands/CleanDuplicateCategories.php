<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TestCategory;

class CleanDuplicateCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'categories:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean duplicate test categories';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Hozirgi kategoriyalar:');
        $categories = TestCategory::select('id', 'name', 'slug')->orderBy('id')->get();
        foreach ($categories as $cat) {
            $this->line("{$cat->id} - {$cat->name} ({$cat->slug})");
        }

        $this->info('\nTakroriy kategoriyalarni o\'chirish...');

        // Takroriy kategoriyalarni o'chirish (yangi qo'shilganlarini)
        $duplicateIds = [7, 8, 9, 10];
        $deleted = TestCategory::whereIn('id', $duplicateIds)->delete();

        $this->info("O'chirildi: {$deleted} ta kategoriya");

        $this->info('\nQolgan kategoriyalar:');
        $remainingCategories = TestCategory::select('id', 'name', 'slug')->orderBy('id')->get();
        foreach ($remainingCategories as $cat) {
            $this->line("{$cat->id} - {$cat->name} ({$cat->slug})");
        }

        $this->info('\nTozalash yakunlandi!');
        return 0;
    }
}
