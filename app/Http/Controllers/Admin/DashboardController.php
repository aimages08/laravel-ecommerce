<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSales = Order::whereIn('status', ['delivered', 'shipped', 'confirmed', 'processing'])->sum('total');
        $todaySales = Order::whereDate('created_at', today())->sum('total');
        $monthSales = Order::whereMonth('created_at', now()->month)->sum('total');

        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $completedOrders = Order::where('status', 'delivered')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();

        $totalCustomers = User::where('role', 'customer')->count();
        $totalProducts = Product::count();
        $lowStockProducts = Product::where('low_stock_alert_enabled', true)
                ->whereColumn('stock', '<=', 'low_stock_threshold')
                ->where('stock', '>', 0)
                ->orderBy('stock')
                ->take(10)
                ->get();

        $lowStock = $lowStockProducts->count();

        $outOfStock = Product::where('stock', '<=', 0)->count();

        $recentOrders = Order::with('user')->latest()->take(5)->get();
        $recentCustomers = User::where('role', 'customer')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalSales', 'todaySales', 'monthSales',
            'totalOrders', 'pendingOrders', 'completedOrders', 'cancelledOrders',
            'totalCustomers', 'totalProducts', 'lowStock', 'outOfStock',
            'recentOrders', 'recentCustomers',
            'lowStockProducts'
        ));

    }
}
