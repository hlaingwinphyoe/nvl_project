<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Serie;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $moreThanTenOrders = Order::with('customer', 'user')->withCount('order_items')->having('order_items_count', '>=', 10)->get()->map(fn ($order) => [
            'id' => $order->id,
            'customer' => $order->customer->name,
            'order_count' => $order->order_items_count,
            'time' => $order->created_at->diffForHumans(),
            'user' => $order->owner
        ]);

        $todayOrderCount = Order::whereDate('created_at', Carbon::today())->count();

        $totalCustomers = Customer::count();
        $totalSeries = Serie::count();

        $newCustomers = Customer::whereMonth('created_at', Carbon::now()->month)->count();

        return Inertia::render('Dashboard', [
            'moreThanTenOrders' => $moreThanTenOrders,
            'todayOrderCount' => $todayOrderCount,
            'totalCustomers' => $totalCustomers,
            'totalSeries' => $totalSeries,
            'newCustomers' => $newCustomers,
        ]);
    }
}
