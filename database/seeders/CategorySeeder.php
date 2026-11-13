<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Books', 'icon' => '📚'],
            ['name' => 'Academic Notes and Lecture Sheets', 'icon' => '📝'],
            ['name' => 'Calculators', 'icon' => '🧮'],
            ['name' => 'Electronics', 'icon' => '💻'],
            ['name' => 'Lab Equipment', 'icon' => '🔬'],
            ['name' => 'Sports Equipment', 'icon' => '⚽'],
            ['name' => 'Stationery', 'icon' => '✏️'],
            ['name' => 'Other Useful Campus Items', 'icon' => '🎒'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['name' => $cat['name']],
                ['slug' => Str::slug($cat['name']), 'icon' => $cat['icon']]
            );
        }
    }
}
