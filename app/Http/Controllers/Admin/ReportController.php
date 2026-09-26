<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /* ============ SALES ============ */
    public function sales(Request $request)
    {
        [$from, $to] = $this->dateRange($request);

        $orders = Order::with('user')
            ->whereBetween('created_at', [$from, $to])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $totals = [
            'orders'    => Order::whereBetween('created_at', [$from, $to])->count(),
            'revenue'   => Order::whereBetween('created_at', [$from, $to])
                ->whereIn('status', ['delivered', 'shipped', 'confirmed', 'processing', 'pending'])
                ->sum('total'),
            'cancelled' => Order::whereBetween('created_at', [$from, $to])->where('status', 'cancelled')->count(),
            'avg'       => Order::whereBetween('created_at', [$from, $to])->avg('total') ?? 0,
        ];

        return view('admin.reports.sales', compact('orders', 'totals', 'from', 'to'));
    }

    public function salesCsv(Request $request): StreamedResponse
    {
        [$from, $to] = $this->dateRange($request);
        $filename = "sales-report-{$from->format('Y-m-d')}-to-{$to->format('Y-m-d')}.csv";

        return response()->streamDownload(function () use ($from, $to) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Order #', 'Customer', 'Total', 'Status', 'Payment', 'Date']);

            Order::with('user')->whereBetween('created_at', [$from, $to])->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $o) {
                    fputcsv($out, [
                        $o->order_number,
                        $o->user->name ?? 'Guest',
                        $o->total,
                        $o->status,
                        $o->payment_method,
                        $o->created_at->format('Y-m-d H:i'),
                    ]);
                }
            });
            fclose($out);
        }, $filename);
    }

    /* ============ PRODUCTS ============ */
    public function products(Request $request)
    {
        [$from, $to] = $this->dateRange($request);

        $rows = OrderItem::selectRaw('product_id, product_name, SUM(quantity) as qty, SUM(subtotal) as revenue')
            ->whereHas('order', fn($q) => $q->whereBetween('created_at', [$from, $to])
                ->whereIn('status', ['delivered', 'shipped', 'confirmed', 'processing', 'pending']))
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('revenue')
            ->paginate(20)
            ->withQueryString();

        return view('admin.reports.products', compact('rows', 'from', 'to'));
    }

    /* ============ CUSTOMERS ============ */
    public function customers(Request $request)
    {
        [$from, $to] = $this->dateRange($request);

        $rows = User::where('role', 'customer')
            ->withCount(['orders' => fn($q) => $q->whereBetween('created_at', [$from, $to])])
            ->withSum(['orders as total_spent' => fn($q) => $q->whereBetween('created_at', [$from, $to])], 'total')
            ->orderByDesc('total_spent')
            ->paginate(20)
            ->withQueryString();

        return view('admin.reports.customers', compact('rows', 'from', 'to'));
    }

    /* ============ PAYMENTS ============ */
    public function payments(Request $request)
    {
        [$from, $to] = $this->dateRange($request);

        $rows = Order::selectRaw('payment_method, COUNT(*) as count, SUM(total) as revenue')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('payment_method')
            ->get();

        return view('admin.reports.payments', compact('rows', 'from', 'to'));
    }

    /* ============ HELPER ============ */
    private function dateRange(Request $request): array
    {
        $preset = $request->preset ?? 'this_month';
        $from = $request->from;
        $to = $request->to;

        if ($preset === 'today') {
            $from = now()->startOfDay();
            $to = now()->endOfDay();
        } elseif ($preset === 'yesterday') {
            $from = now()->subDay()->startOfDay();
            $to = now()->subDay()->endOfDay();
        } elseif ($preset === 'this_week') {
            $from = now()->startOfWeek();
            $to = now()->endOfWeek();
        } elseif ($preset === 'this_month') {
            $from = now()->startOfMonth();
            $to = now()->endOfMonth();
        } elseif ($preset === 'custom' && $from && $to) {
            $from = \Carbon\Carbon::parse($from)->startOfDay();
            $to = \Carbon\Carbon::parse($to)->endOfDay();
        } else {
            $from = now()->startOfMonth();
            $to = now()->endOfMonth();
        }

        return [$from, $to];
    }
}