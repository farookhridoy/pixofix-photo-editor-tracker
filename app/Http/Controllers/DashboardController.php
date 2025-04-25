<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $pageTitle = 'Dashboard';

        $totalOrders = 0;
        $fileStats = collect();
        $employeeStats = collect();
        $orders = collect();

        if ($user->hasRole('Admin')) {
            $adminData = Cache::remember('admin_dashboard_data', now()->addMinutes(5), function () {
                return [
                    'totalOrders' => Order::count(),

                    'fileStats' => OrderFile::select('status', DB::raw('COUNT(*) as count'))
                        ->groupBy('status')
                        ->pluck('count', 'status'),

                    'employeeStats' => User::select('id', 'name')
                        ->whereHas('roles', fn($q) => $q->where('name', '!=', 'Admin'))
                        ->withCount([
                            'claimedFiles as claimed_count' => fn($q) => $q->where('status', 'claimed'),
                            'claimedFiles as in_progress_count' => fn($q) => $q->where('status', 'in_progress'),
                            'claimedFiles as completed_count' => fn($q) => $q->where('status', 'completed'),
                        ])
                        ->get()
                ];
            });

            $orders = Order::select('id', 'order_number')
                ->with(['files:id,order_id,status'])
                ->latest()
                ->paginate(20);

            return view('dashboard', [
                'pageTitle' => $pageTitle,
                'totalOrders' => $adminData['totalOrders'],
                'fileStats' => $adminData['fileStats'],
                'employeeStats' => $adminData['employeeStats'],
                'orders' => $orders
            ]);
        }

        // Employee logic (no caching needed)
        $totalOrders = Order::whereHas('files', fn($q) => $q->where('claimed_by', $user->id))->count();

        $fileStats = OrderFile::select('status', DB::raw('COUNT(*) as count'))
            ->where('claimed_by', $user->id)
            ->groupBy('status')
            ->pluck('count', 'status');

        $employeeStats = collect([[
            'name' => $user->name,
            'claimed_count' => OrderFile::where('claimed_by', $user->id)->where('status', 'claimed')->count(),
            'in_progress_count' => OrderFile::where('claimed_by', $user->id)->where('status', 'in_progress')->count(),
            'completed_count' => OrderFile::where('claimed_by', $user->id)->where('status', 'completed')->count(),
        ]]);

        return view('dashboard', compact(
            'totalOrders', 'fileStats', 'employeeStats', 'pageTitle'
        ));
    }

}
