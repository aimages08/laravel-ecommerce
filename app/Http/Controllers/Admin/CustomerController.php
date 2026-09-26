<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'customer')->latest();

        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        $customers = $query->withCount('orders')
            ->withSum('orders as total_spent', 'total')
            ->paginate(15)
            ->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $customer)
    {
        if ($customer->role !== 'customer') {
            abort(404);
        }

        $orders = Order::where('user_id', $customer->id)->latest()->get();

        return view('admin.customers.show', compact('customer', 'orders'));
    }

    public function toggleBlock(User $customer)
    {
        if ($customer->role !== 'customer') {
            abort(404);
        }

        $customer->update(['is_blocked' => !$customer->is_blocked]);

        // Kill their active sessions if blocking
        if ($customer->is_blocked) {
            \DB::table('sessions')->where('user_id', $customer->id)->delete();
        }

        return back()->with('success', 'Customer status changed.');
    }
}