<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user')->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$request->search}%"));
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        $statuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'returned'];

        return view('admin.orders.index', compact('orders', 'statuses'));
    }

    public function show(Order $order)
    {
        $order->load(['items', 'address', 'user']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled,returned',
            'tracking_number' => 'nullable|string|max:100',
            'carrier' => 'nullable|string|max:100',
        ]);

        $data = ['status' => $request->status];

        if ($request->status === 'shipped' && !$order->shipped_at) {
            $data['shipped_at'] = now();
            $data['tracking_number'] = $request->tracking_number;
            $data['carrier'] = $request->carrier;
        }

        if ($request->status === 'delivered' && !$order->delivered_at) {
            $data['delivered_at'] = now();
        }

        if ($request->status === 'cancelled' && !$order->cancelled_at) {
            $data['cancelled_at'] = now();
        }

        $order->update($data);

        return back()->with('success', 'Order status updated.');
    }

    public function updatePayment(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required|in:unpaid,paid,refunded',
        ]);

        $order->update(['payment_status' => $request->payment_status]);

        return back()->with('success', 'Payment status updated.');
    }


        public function invoice(Order $order)
    {
        $order->load(['items', 'address', 'user']);

        $pdf = Pdf::loadView('admin.orders.invoice', compact('order'));
        $pdf->setPaper('A4');

        return $pdf->download('invoice-' . $order->order_number . '.pdf');
    }

    public function packingSlip(Order $order)
    {
        $order->load(['items', 'address']);

        $pdf = Pdf::loadView('admin.orders.packing-slip', compact('order'));
        $pdf->setPaper('A4');

        return $pdf->download('packing-slip-' . $order->order_number . '.pdf');
    }

}