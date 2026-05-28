<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Fade', 'image_url' => 'https://images.unsplash.com/photo-1505686994434-8d530e196d5a?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'],
            ['name' => 'Undercut', 'image_url' => 'https://images.unsplash.com/photo-1551836022-d5d88e9218df?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'],
            ['name' => 'Curly', 'image_url' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'],
            ['name' => 'Modern', 'image_url' => 'https://images.unsplash.com/photo-1478145046317-39f10e56b5e9?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'],
            ['name' => 'Classic', 'image_url' => 'https://images.unsplash.com/photo-1533106418984-25c0a98fd033?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'],
            ['name' => 'Layered', 'image_url' => 'https://images.unsplash.com/photo-1581276879432-15a6f2f4b896?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
