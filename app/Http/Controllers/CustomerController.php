<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerStoreRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::query()->filterOn()->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($customer) => [
                'id' => $customer->id,
                'name' => $customer->name,
                'username' => $customer->username,
                'phone' => $customer->phone,
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
}
