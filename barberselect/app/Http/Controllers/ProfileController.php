<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        Activity::create([
            'user_id' => $user->id,
            'action' => 'profile.updated',
            'meta' => [
                'scope' => $user->is_admin ? 'admin' : 'user',
            ],
        ]);

        return back()->with('status', 'Profil berhasil diperbarui.');
    }

    protected function ensureAdmin(Request $request): void
    {
        $user = $request->user();
        if (! $user || ! $user->is_admin) {
            abort(403);
        }
    }

    public function adminEdit(Request $request)
    {
        $this->ensureAdmin($request);

        return view('admin.profile', [
            'user' => $request->user(),
        ]);
    }

    public function adminUpdate(Request $request)
    {
        $this->ensureAdmin($request);

        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        Activity::create([
            'user_id' => $user->id,
            'action' => 'admin.profile.updated',
            'meta' => [
                'scope' => 'admin',
            ],
        ]);

        return back()->with('status', 'Profil admin berhasil diperbarui.');
    }

    public function apiShow(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'user' => $user,
        ]);
    }

    public function apiUpdate(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];

        if (! empty($data['password'])) {
            $user->password = $data['password']; // cast hashed handles it
        }

        $user->save();

        Activity::create([
            'user_id' => $user->id,
            'action' => 'profile.updated',
            'meta' => [
                'scope' => $user->is_admin ? 'admin' : 'user',
                'via' => 'api',
            ],
        ]);

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'user' => $user,
        ]);
    }
}
