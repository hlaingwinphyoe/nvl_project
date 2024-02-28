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
        $start_date = request('start') ? Carbon::parse(request('start')) : now()->subDays(6);

        $end_date = request('end') ? Carbon::parse(request('end')) : now();

        $today = Carbon::now()->format('Y-m-d');

        $moreThanTenOrders = Order::with('customer', 'user')
            ->withCount('order_items')
            ->having('order_items_count', '>=', 10)
            ->get()
            ->map(fn ($order) => [
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

        $orders = Order::whereDate('created_at', '>=', $start_date)->whereDate('created_at', '<=', $end_date)->get()->groupBy(function ($order) {
            return Carbon::parse($order->created_at)->format('d M');
        });

        $labels = $datasets = [];

        foreach ($orders as $index => $order) {
            if ($order->count()) {
                array_push($labels, $index);
                array_push($datasets, $order->count());
            }
        }


        return Inertia::render('Dashboard', [
            'moreThanTenOrders' => $moreThanTenOrders,
            'todayOrderCount' => $todayOrderCount,
            'totalCustomers' => $totalCustomers,
            'totalSeries' => $totalSeries,
            'newCustomers' => $newCustomers,
            'labels' => $labels,
            'datasets' => $datasets,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'today' => $today,
        ]);
    }
}
