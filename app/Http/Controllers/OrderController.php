<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Serie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        $perPage = request('page_size') ?: 10;
        $orders = Order::query()
            ->with('user', 'customer', 'order_items')
            ->filterOn()
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->through(fn ($order) => [
                'id' => $order->id,
                'customer_name' => $order->customer->name,
                'customer_id' => $order->customer_id,
                'order_items' => $order->order_items,
                'order_count' => $order->order_items->count(),
                'total_price' => $order->total_price,
                'user' => $order->owner,
                'created_at' => $order->created_at->diffforHumans()
            ]);

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
            $total = 0;
            foreach ($request->series as $serie_id) {
                $serie = Serie::findOrFail($serie_id);

                $total += $serie->price;
            }

            $order = Order::create([
                'customer_id' => $request->customer,
                'total_price' => $total,
            ]);

            foreach ($request->series as $serie_id) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'serie_id' => $serie_id,
                    'unit_price' => $serie->price,
                ]);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('success', $e->getMessage());
        }
        return redirect()->back()->with('success', "Successfully Ordered.");
    }

    public function update(OrderStoreRequest $request, Order $order)
    {
        try {
            $total = 0;
            foreach ($request->series as $serie_id) {
                $serie = Serie::findOrFail($serie_id);

                $total += $serie->price;
            }

            $order->update([
                'customer_id' => $request->customer,
                'total_price' => $total,
            ]);

            $order->order_items()->delete();

            foreach ($request->series as $serie_id) {
                $serie = Serie::findOrFail($serie_id);
                OrderItem::create([
                    'order_id' => $order->id,
                    'serie_id' => $serie_id,
                    'unit_price' => $serie->price,
                ]);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('success', $e->getMessage());
        }
        return redirect()->back()->with('success', "Order Updated.");
    }

    public function destroy(Order $order)
    {
        $order->order_items()->delete();

        $order->delete();

        return redirect()->back()->with('success', 'Order Deleted.');
    }
}
