<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageSetting extends Model
{
    protected $fillable = ['data'];

    protected $casts = [
        'data' => 'array',
    ];

    public static function defaults(): array
    {
        return [
            'hero' => [
                'kicker' => 'SIGNATURE LOOKS',
                'title' => 'Gaya rambut yang terasa premium, bukan sekadar rapi.',
                'subtitle' => 'BarberSelect membantu kamu menemukan gaya yang cocok dengan bentuk wajah, karakter rambut, dan aktivitas harian—dengan kurasi katalog dan rekomendasi AI yang halus.',
                'background_url' => 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=2400&q=80',
                'cta_primary_text' => 'Mulai dengan AI',
                'cta_primary_href' => '#ai',
                'cta_secondary_text' => 'Jelajahi katalog',
                'cta_secondary_href' => '#catalog',
            ],
            'sections' => [
                'catalog' => true,
                'trends' => true,
                'ai' => true,
                'about' => true,
            ],
            'catalog' => [
                'kicker' => 'CATALOG',
                'title' => 'Katalog gaya rambut',
                'subtitle' => 'Pilih vibe yang kamu mau. Lihat detailnya dan simpan sebagai referensi sebelum ke barbershop.',
                'hint' => 'Filter kategori untuk tampilan yang lebih fokus.',
                'take' => 6,
            ],
            'trends' => [
                'kicker' => 'DISCOVER',
                'title' => 'Tren rambut terbaru',
                'subtitle' => 'Snapshot tren yang lagi naik. Cocok buat cari inspirasi sebelum kamu pilih yang paling “kamu”.',
                'hint' => 'Scroll horizontal untuk melihat semuanya.',
                'items' => [
                    ['title' => 'Textured Crop', 'desc' => 'Tampilan rapi dengan tekstur yang memberi dimensi, cocok buat daily look.'],
                    ['title' => 'Low Taper Fade', 'desc' => 'Fade halus yang terasa premium, pas untuk style kantor maupun casual.'],
                    ['title' => 'Modern Mullet', 'desc' => 'Versi clean dari mullet: lebih terstruktur, tetap standout tanpa berlebihan.'],
                    ['title' => 'Classic Side Part', 'desc' => 'Elegan dan timeless. Bagus untuk wajah oval dan gaya formal.'],
                    ['title' => 'Soft Quiff', 'desc' => 'Volume yang natural, terlihat “mahal” tanpa styling berlebihan.'],
                ],
            ],
            'ai' => [
                'kicker' => 'AI ASSISTANT',
                'title' => 'Cari gaya rambutmu',
                'subtitle' => 'Tulis preferensi kamu (bentuk wajah, aktivitas, kesan yang diinginkan). AI akan menyusun rekomendasi yang mudah kamu bawa ke barber.',
                'label' => 'Deskripsimu',
                'placeholder' => 'Contoh: wajah oval, kerja kantoran, suka gaya rapi',
                'button_text' => 'Cari',
                'hint' => 'Rate limit aktif. Jika terlalu sering, tunggu 1 menit lalu coba lagi.',
                'result_title' => 'Hasil rekomendasi',
                'disclaimer_title' => 'Disclaimer',
                'disclaimer_text' => 'BarberSelect hanya sebagai referensi. Hasil akhir bisa berbeda tergantung jenis rambut, bentuk wajah, dan teknik barber.',
            ],
            'about' => [
                'kicker' => 'ABOUT',
                'title' => 'Tentang BarberSelect',
                'subtitle' => 'BarberSelect adalah platform referensi gaya rambut modern yang membantu kamu menemukan inspirasi untuk penampilan terbaik—dengan katalog yang bersih dan rekomendasi yang personal.',
                'bullets' => [
                    'Kurasi gaya yang mudah dipahami dan gampang dijelaskan ke barber.',
                    'Rekomendasi AI fokus pada kebutuhan praktis: pekerjaan, aktivitas, dan preferensi kamu.',
                    'Tampilan premium, modern, dan bersih—tanpa elemen yang mengganggu.',
                ],
            ],
            'footer' => [
                'left' => '© {year} BarberSelect. All rights reserved.',
                'right' => 'Built for clean, premium discovery.',
            ],
        ];
    }

    public static function current(): array
    {
        $row = static::query()->latest()->first();
        $data = is_array($row?->data) ? $row->data : [];

        return array_replace_recursive(static::defaults(), $data);
    }

    public static function saveCurrent(array $data): self
    {
        $row = static::query()->latest()->first();
        if (! $row) {
            return static::query()->create(['data' => $data]);
        }

        $row->update(['data' => $data]);
        return $row;
    }
}

