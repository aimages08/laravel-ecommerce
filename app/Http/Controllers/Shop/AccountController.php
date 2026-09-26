<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Barryvdh\DomPDF\Facade\Pdf;

class AccountController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)->with('items')->latest()->get();
        $addresses = $user->addresses()->latest()->get();

        return view('shop.my-account', compact('user', 'orders', 'addresses'));
    }

    /* ======= PROFILE ======= */

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return back()->with('success', 'Profile updated.');
    }

    /* ======= PASSWORD ======= */

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password updated.');
    }

    /* ======= ADDRESSES ======= */

    public function storeAddress(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'label' => 'nullable|string|max:50',
        ]);

        $data = $request->only([
            'label', 'name', 'phone', 'address_line1', 'address_line2',
            'city', 'state', 'postal_code',
        ]);
        $data['user_id'] = Auth::id();
        $data['country'] = 'Pakistan';

        if ($request->boolean('is_default')) {
            Auth::user()->addresses()->update(['is_default' => false]);
            $data['is_default'] = true;
        }

        Address::create($data);

        return back()->with('success', 'Address added.');
    }

    public function updateAddress(Request $request, Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'label' => 'nullable|string|max:50',
        ]);

        $data = $request->only([
            'label', 'name', 'phone', 'address_line1', 'address_line2',
            'city', 'state', 'postal_code',
        ]);

        if ($request->boolean('is_default')) {
            Auth::user()->addresses()->update(['is_default' => false]);
            $data['is_default'] = true;
        }

        $address->update($data);

        return back()->with('success', 'Address updated.');
    }

    public function deleteAddress(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }
        $address->delete();
        return back()->with('success', 'Address deleted.');
    }

    /* ======= ORDER DETAIL ======= */

    public function orderDetail($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with(['items', 'address'])
            ->firstOrFail();

        return view('shop.order-detail', compact('order'));
    }


    public function cancelOrder(Order $order)
    {
        // Must belong to the logged-in user
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Only allow cancel when pending or confirmed
        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'This order cannot be cancelled. Please contact support.');
        }

        $order->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return back()->with('success', 'Order cancelled successfully.');
    }


    public function invoice($orderNumber)
{
    $order = Order::where('order_number', $orderNumber)
        ->where('user_id', auth()->id())
        ->with(['items', 'address', 'user'])
        ->firstOrFail();

    $pdf = Pdf::loadView('admin.orders.invoice', compact('order'));
    $pdf->setPaper('A4');

    return $pdf->download('invoice-' . $order->order_number . '.pdf');
}



}