<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'Top Rated',
                'slug' => 'top',
            ],
            [
                'name' => 'Best Sellers',
                'slug' => 'best',
            ],
            [
                'name' => 'New Arrivals',
                'slug' => 'new',
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
