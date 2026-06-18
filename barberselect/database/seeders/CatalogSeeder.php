<?php

namespace Database\Seeders;

use App\Models\Catalog;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catalogs = [
            [
                'name' => 'Classic Fade',
                'category_name' => 'Fade',
                'description' => 'Gaya rambut klasik dengan transisi yang halus dari panjang ke pendek. Cocok untuk tampilan profesional dan bersih.',
                'image_url' => 'https://images.unsplash.com/photo-1596728325488-58c87691e9af?w=600&q=80'
            ],
            [
                'name' => 'Modern Undercut',
                'category_name' => 'Undercut',
                'description' => 'Gaya undercut modern dengan sisi yang dicukur dan atas yang stylish. Memberikan kesan edgy dan fashionable.',
                'image_url' => 'https://images.unsplash.com/photo-1596728325488-58c87691e9af?w=600&q=80'
            ],
            [
                'name' => 'Curly Waves',
                'category_name' => 'Curly',
                'description' => 'Gelombang rambut keriting yang memberikan tampilan alami dan menawan. Cocok untuk rambut bertekstur.',
                'image_url' => 'https://images.unsplash.com/photo-1560869713-7d0a29430803?w=600&q=80'
            ],
            [
                'name' => 'French Crop',
                'category_name' => 'Modern',
                'description' => 'Crop ala Prancis dengan sisi yang pendek dan atas yang sedikit panjang. Gaya yang timeless dan mudah dirawat.',
                'image_url' => 'https://images.unsplash.com/photo-1560869713-7d0a29430803?w=600&q=80'
            ],
            [
                'name' => 'Textured Crop',
                'category_name' => 'Modern',
                'description' => 'Crop dengan tekstur yang memberikan dimensi dan volume. Cocok untuk rambut lurus yang ingin terlihat lebih dinamis.',
                'image_url' => 'https://images.unsplash.com/photo-1596728325488-58c87691e9af?w=600&q=80'
            ],
            [
                'name' => 'Layered Bob',
                'category_name' => 'Layered',
                'description' => 'Bob dengan layering yang memberikan gerakan dan volume. Gaya wanita yang elegan dan mudah di-styling.',
                'image_url' => 'https://images.unsplash.com/photo-1560869713-7d0a29430803?w=600&q=80'
            ],
            [
                'name' => 'Mullet Revival',
                'category_name' => 'Classic',
                'description' => 'Gaya mullet yang kembali populer dengan variasi modern. Depan dan samping pendek, belakang panjang.',
                'image_url' => 'https://images.unsplash.com/photo-1596728325488-58c87691e9af?w=600&q=80'
            ],
            [
                'name' => 'Shag Cut',
                'category_name' => 'Layered',
                'description' => 'Potongan shag yang messy namun terlihat rapi. Memberikan tekstur dan gerakan alami pada rambut.',
                'image_url' => 'https://images.unsplash.com/photo-1560869713-7d0a29430803?w=600&q=80'
            ],
        ];

        foreach ($catalogs as $catalogData) {
            $category = Category::where('name', $catalogData['category_name'])->first();
            if ($category) {
                Catalog::create([
                    'name' => $catalogData['name'],
                    'category_id' => $category->id,
                    'description' => $catalogData['description'],
                    'image_url' => $catalogData['image_url'],
                ]);
            }
        }
    }
}
