<?php

namespace App\Http\Controllers;

use App\Models\LandingPageSetting;
use Illuminate\Http\Request;

class LandingPageApiController extends Controller
{
    public function show(Request $request)
    {
        return response()->json([
            'data' => LandingPageSetting::current(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        if (! $user || ! $user->is_admin) {
            abort(403);
        }

        // Reuse the same payload shape used by the web admin.
        // Expect client to send a full/partial `data` array.
        $validated = $request->validate([
            'data' => ['required', 'array'],
        ]);

        $row = LandingPageSetting::saveCurrent($validated['data']);

        return response()->json([
            'message' => 'Landing page mobile diperbarui.',
            'data' => $row->fresh()->data,
        ]);
    }
}

