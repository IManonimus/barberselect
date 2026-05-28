<?php

namespace App\Http\Controllers;

use App\Models\Catalog;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user && $user->is_admin) {
            return redirect('/admin');
        }

        $users = User::orderBy('created_at', 'desc')->limit(10)->get();
        $totalUsers = User::count();
        $totalCatalogs = Catalog::count();
        $latestRecommendation = $request->session()->get('last_ai_recommendation');

        return view('dashboard', [
            'users' => $users,
            'totalUsers' => $totalUsers,
            'totalCatalogs' => $totalCatalogs,
            'latestRecommendation' => $latestRecommendation,
        ]);
    }
}
