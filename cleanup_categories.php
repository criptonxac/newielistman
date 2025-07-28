<?php

require_once 'bootstrap/app.php';

use App\Models\TestCategory;

echo "Hozirgi kategoriyalar:\n";
$categories = TestCategory::select('id', 'name', 'slug')->orderBy('id')->get();
foreach ($categories as $cat) {
    echo "{$cat->id} - {$cat->name} ({$cat->slug})\n";
}

echo "\nTakroriy kategoriyalarni o'chirish...\n";

// Takroriy kategoriyalarni o'chirish (yangi qo'shilganlarini)
$duplicateIds = [7, 8, 9, 10];
$deleted = TestCategory::whereIn('id', $duplicateIds)->delete();

echo "O'chirildi: {$deleted} ta kategoriya\n";

echo "\nQolgan kategoriyalar:\n";
$remainingCategories = TestCategory::select('id', 'name', 'slug')->orderBy('id')->get();
foreach ($remainingCategories as $cat) {
    echo "{$cat->id} - {$cat->name} ({$cat->slug})\n";
}

echo "\nTozalash yakunlandi!\n";
