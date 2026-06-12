<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password123',
                'is_admin' => false,
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@barberselect.test'],
            [
                'name' => 'Admin BarberSelect',
                'password' => 'password123',
                'is_admin' => true,
            ]
        );

        $this->call([
            CategorySeeder::class,
            CatalogSeeder::class,
            BarberShopSeeder::class,
        ]);
    }
}
