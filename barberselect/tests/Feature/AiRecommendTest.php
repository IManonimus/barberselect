<?php

namespace Tests\Feature;

use App\Models\Catalog;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiRecommendTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_recommend_requires_login(): void
    {
        $this->postJson('/ai/recommend', ['query' => 'wajah oval rapi untuk kantor'])
            ->assertUnauthorized(); // JSON request returns 401 via auth middleware
    }

    public function test_ai_recommend_returns_recommendation_and_catalogs_and_stores_session(): void
    {
        config([
            'services.gemini.api_key' => 'testing-key',
            'services.gemini.model' => 'gemini-2.0-flash',
        ]);

        $category = Category::create([
            'name' => 'Modern',
        ]);

        Catalog::create([
            'name' => 'Clean Fade',
            'category_id' => $category->id,
            'description' => 'Rapi, clean, cocok untuk kantor.',
            'image_url' => 'https://example.test/fade.jpg',
        ]);

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => "Rekomendasi:\n- Clean fade\n- Side part\n"],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession([])
            ->postJson('/ai/recommend', [
                'query' => 'wajah oval rapi untuk kantor',
            ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'recommendation',
                'catalog_recommendations' => [
                    '*' => ['id', 'name', 'description', 'category', 'image_url', 'detail_url'],
                ],
            ]);

        $this->assertNotEmpty(session('last_ai_recommendation.recommendation'));
        $this->assertSame('wajah oval rapi untuk kantor', session('last_ai_recommendation.query'));
        $this->assertNotEmpty(session('last_ai_recommendation.generated_at'));
    }

    public function test_ai_recommend_returns_500_when_api_key_missing(): void
    {
        config([
            'services.gemini.api_key' => null,
        ]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/ai/recommend', ['query' => 'test'])
            ->assertStatus(500)
            ->assertJson([
                'error' => 'Konfigurasi API Gemini belum tersedia.',
            ]);
    }

    public function test_ai_recommend_validates_query_or_image(): void
    {
        config([
            'services.gemini.api_key' => 'testing-key',
        ]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/ai/recommend', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['query', 'image']);
    }
}

