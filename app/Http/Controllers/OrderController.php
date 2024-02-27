<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Serie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        $perPage = request('page_size') ?: 10;
        // $orders = Order::query()
        //     ->with(['customer', 'serie', 'user'])
        //     ->filterOn()
        //     ->orderBy('id', 'desc')
        //     ->paginate($perPage)
        //     ->groupBy('customer_id');

        // ->through(fn ($order) => [
        //     'id' => $order->id,
        //     'customer_name' => $order->customer->name,
        //     'serie_title' => $order->serie->title,
        //     'customer_id' => $order->customer_id,
        //     'serie_id' => $order->serie_id,
        //     'buy_price' => $order->buy_price,
        //     'user' => $order->owner,
        //     'created_at' => $order->created_at->diffforHumans()
        // ]);

        $orders = DB::table('orders')
            ->join('customers', 'customers.id', '=', 'orders.customer_id')
            ->join('series', 'series.id', '=', 'orders.serie_id')
            ->leftJoin('users', 'users.id', '=', 'orders.created_by')
            ->selectRaw('count(orders.id) as number_of_orders, orders.customer_id, customers.name as customer_name, sum(orders.buy_price) as total, max(orders.created_at) as created_at, min(users.name) as user')
            ->groupBy('orders.customer_id')
            ->paginate($perPage);

        $customers = Customer::orderBy('name')->get();
        $series = Serie::orderBy('title')->get();

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'customers' => $customers,
            'series' => $series
        ]);
    }

    public function store(OrderStoreRequest $request)
    {
        try {
            foreach ($request->series as $serie_id) {
                $serie = Serie::findOrFail($serie_id);

                if ($serie) {
                    $order = Order::create([
                        'customer_id' => $request->customer,
                        'serie_id' => $serie->id,
                        'buy_price' => $serie->price,
                    ]);
                }
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('success', $e->getMessage());
        }
        return redirect()->back()->with('success', "Successfully Ordered.");
    }


    public function show(Customer $customer)
    {
        $orders = Order::with('serie')->where('customer_id', $customer->id)->get()->map(fn ($order) => [
            'serie_title' => $order->serie ? $order->serie->title : '',
            'buy_price' => $order->buy_price,
            'order_time' => $order->created_at->diffForHumans()
        ]);

        return response()->json($orders);
    }
}
