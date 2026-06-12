<?php

namespace Database\Seeders;

use App\Models\BarberShop;
use Illuminate\Database\Seeder;

class BarberShopSeeder extends Seeder
{
    public function run(): void
    {
        $shops = [
            [
                'name' => 'BarberSelect Studio Sudirman',
                'address' => 'Jl. Jenderal Sudirman No. 52, Jakarta Pusat',
                'hours' => '09:00 – 21:00',
                'phone' => '021-5550101',
                'rating' => 4.9,
                'lat' => -6.2146,
                'lng' => 106.8214,
            ],
            [
                'name' => 'Premium Cuts Kemang',
                'address' => 'Jl. Kemang Raya No. 18, Jakarta Selatan',
                'hours' => '10:00 – 22:00',
                'phone' => '021-5550102',
                'rating' => 4.7,
                'lat' => -6.2615,
                'lng' => 106.8136,
            ],
            [
                'name' => 'Classic Barber Menteng',
                'address' => 'Jl. Teuku Umar No. 7, Jakarta Pusat',
                'hours' => '08:00 – 20:00',
                'phone' => '021-5550103',
                'rating' => 4.8,
                'lat' => -6.1944,
                'lng' => 106.8294,
            ],
            [
                'name' => 'Modern Fade Senopati',
                'address' => 'Jl. Senopati No. 45, Jakarta Selatan',
                'hours' => '09:00 – 21:00',
                'phone' => '021-5550104',
                'rating' => 4.6,
                'lat' => -6.2297,
                'lng' => 106.7997,
            ],
            [
                'name' => 'Sharp Edge Kelapa Gading',
                'address' => 'Jl. Boulevard Barat Raya No. 12, Jakarta Utara',
                'hours' => '10:00 – 21:00',
                'phone' => '021-5550105',
                'rating' => 4.5,
                'lat' => -6.1578,
                'lng' => 106.9053,
            ],
            [
                'name' => 'Urban Groom BSD',
                'address' => 'Jl. BSD Raya Utama No. 8, Tangerang Selatan',
                'hours' => '09:00 – 20:00',
                'phone' => '021-5550106',
                'rating' => 4.4,
                'lat' => -6.3014,
                'lng' => 106.6538,
            ],
            [
                'name' => 'BarberSelect Wonocolo',
                'address' => 'Jl. Raya Wonocolo No. 15, Surabaya',
                'hours' => '09:00 – 21:00',
                'phone' => '031-5550101',
                'rating' => 4.8,
                'lat' => -7.2945,
                'lng' => 112.7342,
            ],
            [
                'name' => 'Fade House Surabaya',
                'address' => 'Jl. Raya Darmo No. 88, Surabaya',
                'hours' => '10:00 – 22:00',
                'phone' => '031-5550102',
                'rating' => 4.7,
                'lat' => -7.2856,
                'lng' => 112.7289,
            ],
            [
                'name' => 'Classic Cut Sidoarjo',
                'address' => 'Jl. Pahlawan No. 22, Sidoarjo',
                'hours' => '08:00 – 20:00',
                'phone' => '031-5550103',
                'rating' => 4.6,
                'lat' => -7.4478,
                'lng' => 112.7183,
            ],
            [
                'name' => 'Sharp Edge Sedati',
                'address' => 'Jl. Sedati Agung No. 5, Sidoarjo',
                'hours' => '09:00 – 21:00',
                'phone' => '031-5550104',
                'rating' => 4.5,
                'lat' => -7.3689,
                'lng' => 112.7856,
            ],
        ];

        foreach ($shops as $shop) {
            BarberShop::updateOrCreate(
                ['name' => $shop['name']],
                array_merge($shop, ['is_active' => true])
            );
        }
    }
}
