<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Catalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GroqController extends Controller
{
    public function recommend(Request $request)
    {
        $validated = $request->validate([
            'query' => ['nullable', 'string', 'max:500', 'required_without:image'],
            'image' => ['nullable', 'string', 'max:500000', 'required_without:query'], // base64 image string
        ]);

        $query = $validated['query'] ?? null;
        $image = $validated['image'] ?? null;

        $apiKey = config('services.groq.api_key');
        if (! $apiKey) {
            return response()->json(['error' => 'Konfigurasi API Groq belum tersedia.'], 500);
        }

        $model = config('services.groq.model', 'llama-3.3-70b-versatile');
        $endpoint = 'https://api.groq.com/openai/v1/chat/completions';

        $systemInstruction = 'Kamu adalah asisten barber profesional. Beri rekomendasi gaya rambut yang personal, praktis, dan mudah dipahami dalam bahasa Indonesia.';

        $userPromptParts = [];
        if ($query) {
            $userPromptParts[] = 'Profil/permintaan user: ' . $query;
        }
        if ($image) {
            $userPromptParts[] = 'User juga mengirim gambar, namun analisis dilakukan berdasarkan deskripsi teks.';
        }

        $messages = [
            ['role' => 'system', 'content' => $systemInstruction],
            ['role' => 'user', 'content' => implode("\n", $userPromptParts)],
        ];

        try {
            $response = Http::timeout(20)
                ->withToken($apiKey)
                ->post($endpoint, [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 0.7,
                ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'error' => 'Layanan AI sedang tidak tersedia. Koneksi ke Groq timeout/gagal.',
                'detail' => $exception->getMessage(),
            ], 503);
        }

        if ($response->failed()) {
            $errorMessage = $response->json('error.message')
                ?? $response->json('message')
                ?? 'AI tidak dapat memproses permintaan saat ini. Coba lagi sebentar.';

            if ($response->status() === 429) {
                return response()->json([
                    'error' => 'AI sedang ramai atau kuota API sudah mencapai batas. Coba lagi dalam 1 menit.',
                    'error_code' => 'groq_rate_limited',
                ], 503);
            }

            return response()->json([
                'error' => $errorMessage,
            ], $response->status());
        }

        $result = $response->json();
        $text = trim($result['choices'][0]['message']['content'] ?? '');
        $text = $text ?: 'Tidak ada rekomendasi.';

        $catalogRecommendations = Catalog::with('category')
            ->when($query, function ($builder) use ($query) {
                $builder->where(function ($subQuery) use ($query) {
                    $subQuery->where('name', 'like', '%' . $query . '%')
                        ->orWhere('description', 'like', '%' . $query . '%')
                        ->orWhereHas('category', function ($categoryQuery) use ($query) {
                            $categoryQuery->where('name', 'like', '%' . $query . '%');
                        });
                });
            })
            ->inRandomOrder()
            ->limit(4)
            ->get()
            ->map(function (Catalog $catalog) {
                return [
                    'id' => $catalog->id,
                    'name' => $catalog->name,
                    'description' => $catalog->description,
                    'category' => $catalog->category?->name,
                    'image_url' => $catalog->image_url,
                    'detail_url' => route('catalog.show', $catalog),
                ];
            })
            ->values();

        if ($catalogRecommendations->isEmpty()) {
            $catalogRecommendations = Catalog::with('category')
                ->inRandomOrder()
                ->limit(4)
                ->get()
                ->map(function (Catalog $catalog) {
                    return [
                        'id' => $catalog->id,
                        'name' => $catalog->name,
                        'description' => $catalog->description,
                        'category' => $catalog->category?->name,
                        'image_url' => $catalog->image_url,
                        'detail_url' => route('catalog.show', $catalog),
                    ];
                })
                ->values();
        }

        if ($request->hasSession()) {
            $request->session()->put('last_ai_recommendation', [
                'query' => $query,
                'recommendation' => $text,
                'catalog_recommendations' => $catalogRecommendations->toArray(),
                'generated_at' => now()->toDateTimeString(),
            ]);
        }

        Activity::create([
            'user_id' => $request->user()?->id,
            'action' => 'ai.recommendation.generated',
            'meta' => [
                'query' => $query,
                'recommendations' => $catalogRecommendations->pluck('id')->all(),
            ],
        ]);

        return response()->json([
            'recommendation' => $text,
            'catalog_recommendations' => $catalogRecommendations,
        ]);
    }
}