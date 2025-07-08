<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $categories = [
            'business',
            'entertainment',
            'general',
            'health',
            'science',
            'sports',
            'technology',
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category],
                ['name' => ucfirst($category), 'slug' => $category]
            );
        }
    }
}
