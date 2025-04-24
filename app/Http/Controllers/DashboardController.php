<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(){
        $user = Auth::user();

        // For Admin: show all
        if ($user->hasRole('Admin')) {
            $totalOrders = Order::count();
            $fileStats = OrderFile::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status');

            $employeeStats = User::withCount([
                'claimedFiles as claimed_count' => fn ($q) => $q->where('status', 'claimed'),
                'claimedFiles as completed_count' => fn ($q) => $q->where('status', 'completed'),
            ])
                ->whereHas('roles', fn ($q) => $q->whereNotIn('name', ['Admin']))
                ->get(['id', 'name']);

        } else {
            // For Employee: show only their stats
            $totalOrders = Order::whereHas('files', fn ($q) =>
            $q->where('claimed_by', $user->id)
            )->count();

            $fileStats = OrderFile::selectRaw('status, COUNT(*) as count')
                ->where('claimed_by', $user->id)
                ->groupBy('status')
                ->pluck('count', 'status');

            $employeeStats = collect([[
                'name' => $user->name,
                'claimed_count' => OrderFile::where('claimed_by', $user->id)->where('status', 'claimed')->count(),
                'completed_count' => OrderFile::where('claimed_by', $user->id)->where('status', 'completed')->count(),
            ]]);

        }

        $pageTitle = 'Dashboard';

        return view('dashboard', compact('totalOrders', 'fileStats', 'employeeStats','pageTitle'));
    }
}
