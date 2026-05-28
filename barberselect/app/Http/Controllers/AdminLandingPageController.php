<?php

namespace App\Http\Controllers;

use App\Models\LandingPageSetting;
use Illuminate\Http\Request;

class AdminLandingPageController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();
        if (! $user || ! $user->is_admin) {
            abort(403);
        }

        return view('admin.landing-page', [
            'user' => $user,
            'lp' => LandingPageSetting::current(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        if (! $user || ! $user->is_admin) {
            abort(403);
        }

        $validated = $request->validate([
            'hero_kicker' => ['nullable', 'string', 'max:80'],
            'hero_title' => ['nullable', 'string', 'max:180'],
            'hero_subtitle' => ['nullable', 'string', 'max:400'],
            'hero_background_url' => ['nullable', 'string', 'max:1000'],
            'hero_cta_primary_text' => ['nullable', 'string', 'max:40'],
            'hero_cta_primary_href' => ['nullable', 'string', 'max:200'],
            'hero_cta_secondary_text' => ['nullable', 'string', 'max:40'],
            'hero_cta_secondary_href' => ['nullable', 'string', 'max:200'],

            'section_catalog' => ['nullable'],
            'section_trends' => ['nullable'],
            'section_ai' => ['nullable'],
            'section_about' => ['nullable'],

            'catalog_kicker' => ['nullable', 'string', 'max:80'],
            'catalog_title' => ['nullable', 'string', 'max:120'],
            'catalog_subtitle' => ['nullable', 'string', 'max:400'],
            'catalog_hint' => ['nullable', 'string', 'max:200'],
            'catalog_take' => ['nullable', 'integer', 'min:1', 'max:24'],

            'trends_kicker' => ['nullable', 'string', 'max:80'],
            'trends_title' => ['nullable', 'string', 'max:120'],
            'trends_subtitle' => ['nullable', 'string', 'max:400'],
            'trends_hint' => ['nullable', 'string', 'max:200'],
            'trends_items_json' => ['nullable', 'string', 'max:20000'],

            'ai_kicker' => ['nullable', 'string', 'max:80'],
            'ai_title' => ['nullable', 'string', 'max:120'],
            'ai_subtitle' => ['nullable', 'string', 'max:500'],
            'ai_label' => ['nullable', 'string', 'max:80'],
            'ai_placeholder' => ['nullable', 'string', 'max:140'],
            'ai_button_text' => ['nullable', 'string', 'max:40'],
            'ai_hint' => ['nullable', 'string', 'max:200'],
            'ai_result_title' => ['nullable', 'string', 'max:80'],
            'ai_disclaimer_title' => ['nullable', 'string', 'max:80'],
            'ai_disclaimer_text' => ['nullable', 'string', 'max:400'],

            'about_kicker' => ['nullable', 'string', 'max:80'],
            'about_title' => ['nullable', 'string', 'max:120'],
            'about_subtitle' => ['nullable', 'string', 'max:500'],
            'about_bullets_json' => ['nullable', 'string', 'max:20000'],

            'footer_left' => ['nullable', 'string', 'max:200'],
            'footer_right' => ['nullable', 'string', 'max:200'],
        ]);

        $defaults = LandingPageSetting::defaults();

        $trendsItems = $defaults['trends']['items'];
        if (! empty($validated['trends_items_json'])) {
            $decoded = json_decode($validated['trends_items_json'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $trendsItems = collect($decoded)
                    ->filter(fn ($it) => is_array($it))
                    ->map(fn ($it) => [
                        'title' => (string) ($it['title'] ?? ''),
                        'desc' => (string) ($it['desc'] ?? ''),
                    ])
                    ->filter(fn ($it) => trim($it['title']) !== '' || trim($it['desc']) !== '')
                    ->take(12)
                    ->values()
                    ->all();
            }
        }

        $aboutBullets = $defaults['about']['bullets'];
        if (! empty($validated['about_bullets_json'])) {
            $decoded = json_decode($validated['about_bullets_json'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $aboutBullets = collect($decoded)
                    ->map(fn ($it) => is_string($it) ? $it : '')
                    ->filter(fn ($it) => trim($it) !== '')
                    ->take(12)
                    ->values()
                    ->all();
            }
        }

        $data = [
            'hero' => [
                'kicker' => $validated['hero_kicker'] ?? $defaults['hero']['kicker'],
                'title' => $validated['hero_title'] ?? $defaults['hero']['title'],
                'subtitle' => $validated['hero_subtitle'] ?? $defaults['hero']['subtitle'],
                'background_url' => $validated['hero_background_url'] ?? $defaults['hero']['background_url'],
                'cta_primary_text' => $validated['hero_cta_primary_text'] ?? $defaults['hero']['cta_primary_text'],
                'cta_primary_href' => $validated['hero_cta_primary_href'] ?? $defaults['hero']['cta_primary_href'],
                'cta_secondary_text' => $validated['hero_cta_secondary_text'] ?? $defaults['hero']['cta_secondary_text'],
                'cta_secondary_href' => $validated['hero_cta_secondary_href'] ?? $defaults['hero']['cta_secondary_href'],
            ],
            'sections' => [
                'catalog' => (bool) ($request->input('section_catalog') ?? false),
                'trends' => (bool) ($request->input('section_trends') ?? false),
                'ai' => (bool) ($request->input('section_ai') ?? false),
                'about' => (bool) ($request->input('section_about') ?? false),
            ],
            'catalog' => [
                'kicker' => $validated['catalog_kicker'] ?? $defaults['catalog']['kicker'],
                'title' => $validated['catalog_title'] ?? $defaults['catalog']['title'],
                'subtitle' => $validated['catalog_subtitle'] ?? $defaults['catalog']['subtitle'],
                'hint' => $validated['catalog_hint'] ?? $defaults['catalog']['hint'],
                'take' => $validated['catalog_take'] ?? $defaults['catalog']['take'],
            ],
            'trends' => [
                'kicker' => $validated['trends_kicker'] ?? $defaults['trends']['kicker'],
                'title' => $validated['trends_title'] ?? $defaults['trends']['title'],
                'subtitle' => $validated['trends_subtitle'] ?? $defaults['trends']['subtitle'],
                'hint' => $validated['trends_hint'] ?? $defaults['trends']['hint'],
                'items' => $trendsItems,
            ],
            'ai' => [
                'kicker' => $validated['ai_kicker'] ?? $defaults['ai']['kicker'],
                'title' => $validated['ai_title'] ?? $defaults['ai']['title'],
                'subtitle' => $validated['ai_subtitle'] ?? $defaults['ai']['subtitle'],
                'label' => $validated['ai_label'] ?? $defaults['ai']['label'],
                'placeholder' => $validated['ai_placeholder'] ?? $defaults['ai']['placeholder'],
                'button_text' => $validated['ai_button_text'] ?? $defaults['ai']['button_text'],
                'hint' => $validated['ai_hint'] ?? $defaults['ai']['hint'],
                'result_title' => $validated['ai_result_title'] ?? $defaults['ai']['result_title'],
                'disclaimer_title' => $validated['ai_disclaimer_title'] ?? $defaults['ai']['disclaimer_title'],
                'disclaimer_text' => $validated['ai_disclaimer_text'] ?? $defaults['ai']['disclaimer_text'],
            ],
            'about' => [
                'kicker' => $validated['about_kicker'] ?? $defaults['about']['kicker'],
                'title' => $validated['about_title'] ?? $defaults['about']['title'],
                'subtitle' => $validated['about_subtitle'] ?? $defaults['about']['subtitle'],
                'bullets' => $aboutBullets,
            ],
            'footer' => [
                'left' => $validated['footer_left'] ?? $defaults['footer']['left'],
                'right' => $validated['footer_right'] ?? $defaults['footer']['right'],
            ],
        ];

        LandingPageSetting::saveCurrent($data);

        return redirect('/admin/landing-page')->with('status', 'Landing page berhasil diperbarui.');
    }
}

