<?php

namespace App\Http\Controllers;

use App\Models\Catalog;
use App\Models\Category;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (! $user || ! $user->is_admin) {
            abort(403);
        }

        $totalUsers = User::count();
        $categoryCount = Category::count();
        $catalogCount = Catalog::count();
        $latestUsers = User::orderBy('created_at', 'desc')->limit(5)->get();
        $latestActivities = Activity::with('user')->latest()->limit(10)->get();

        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'categoryCount' => $categoryCount,
            'catalogCount' => $catalogCount,
            'latestUsers' => $latestUsers,
            'latestActivities' => $latestActivities,
            'user' => $user,
        ]);
    }

    public function activityFeed(Request $request)
    {
        $user = $request->user();
        if (! $user || ! $user->is_admin) {
            abort(403);
        }

        $items = Activity::with('user')->latest()->limit(10)->get()->map(function (Activity $activity) {
            return [
                'id' => $activity->id,
                'action' => $activity->action,
                'meta' => $activity->meta,
                'user' => $activity->user ? [
                    'id' => $activity->user->id,
                    'name' => $activity->user->name,
                    'email' => $activity->user->email,
                ] : null,
                'created_at' => $activity->created_at?->toIso8601String(),
                'created_at_human' => $activity->created_at?->diffForHumans(),
            ];
        });

        return response()->json([
            'items' => $items,
        ]);
    }
}
