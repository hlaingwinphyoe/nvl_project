<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerStoreRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Models\Customer;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index()
    {
        $perPage = request('page_size') ?: 10;
        $customers = Customer::query()
            ->filterOn()
            ->orderBy('id','desc')
            ->paginate($perPage)
            ->withQueryString()
            ->through(fn ($customer) => [
                'id' => $customer->id,
                'name' => $customer->name,
                'username' => $customer->username,
                'phone' => $customer->phone,
                'is_ban' => $customer->is_ban,
                'created_at' => $customer->created_at->diffforHumans()
            ]);
        return Inertia::render('Admin/Customers/Index', [
            'customers' => $customers
        ]);
    }

    public function store(CustomerStoreRequest $request)
    {
        $customer = Customer::create($request->validated());

        return redirect()->back()->with('success', "Successfully Created.");
    }

    public function update(CustomerUpdateRequest $request, Customer $customer)
    {
        $customer->update($request->validated());

        return redirect()->back()->with('success', "Successfully Updated.");
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->back()->with('success', "Successfully Deleted.");
    }

    public function handleBan(Customer $customer)
    {
        if ($customer->is_ban == 0) {
            $customer->update(['is_ban' => 1]);
            return redirect()->back()->with('success', 'Banned Successful!');
        } else {
            $customer->update(['is_ban' => 0]);
            return redirect()->back()->with('success', 'UnBan Successful!');
        }
    }
}
