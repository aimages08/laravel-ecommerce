<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Mail\BankDetailsMail;
use App\Mail\OrderPlaced;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\InventoryService;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('shop.cart')->with('error', 'Cart is empty.');
        }

        $subtotal = collect($cart)->sum(fn ($i) => $i['price'] * $i['quantity']);

        $discount = 0;
        $coupon = null;
        if (session()->has('coupon_id')) {
            $coupon = Coupon::find(session('coupon_id'));
            if ($coupon && $coupon->isValidFor($subtotal)) {
                $discount = $coupon->calculateDiscount($subtotal);
            } else {
                session()->forget('coupon_id');
                $coupon = null;
            }
        }

        $shipping = app(ShippingService::class)->calculate($cart, $subtotal);
        $total = max(0, $subtotal - $discount) + $shipping;

        $defaultAddress = null;
        if (auth()->check()) {
            $defaultAddress = auth()->user()->addresses()
                ->orderByDesc('is_default')
                ->first();
        }

        $paymentMethods = PaymentMethod::enabled()->get();

        return view('shop.checkout', compact(
            'cart', 'subtotal', 'discount', 'coupon', 'shipping', 'total',
            'defaultAddress', 'paymentMethods'
        ));
    }

    public function store(Request $request)
    {
        $enabledCodes = PaymentMethod::where('is_enabled', true)->pluck('code')->toArray();

        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'payment_method' => 'required|in:'.implode(',', $enabledCodes),
        ];

        if ($request->boolean('create_account') && ! Auth::check()) {
            $rules['email'] = 'required|email|unique:users,email';
            $rules['password'] = ['required', 'confirmed', Rules\Password::defaults()];
        }

        $request->validate($rules);

        // Block if email belongs to a blocked account
        if ($request->filled('email')) {
            $blockedUser = User::where('email', $request->email)
                ->where('is_blocked', true)
                ->exists();

            if ($blockedUser) {
                return back()->with('error', 'This email is blocked. Please contact support.');
            }
        }

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('shop.cart')->with('error', 'Cart is empty.');
        }

        DB::beginTransaction();
        try {
            $subtotal = collect($cart)->sum(fn ($i) => $i['price'] * $i['quantity']);

            $discount = 0;
            $coupon = null;
            if (session()->has('coupon_id')) {
                $coupon = Coupon::find(session('coupon_id'));
                if ($coupon && $coupon->isValidFor($subtotal)) {
                    $discount = $coupon->calculateDiscount($subtotal);
                }
            }

            $shipping = app(ShippingService::class)->calculate($cart, $subtotal);
            $total = max(0, $subtotal - $discount) + $shipping;

            $userId = Auth::id();

            if ($request->boolean('create_account') && ! Auth::check()) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => 'customer',
                ]);
                Auth::login($user);
                $userId = $user->id;
            }

            $order = Order::create([
                'order_number' => 'ORD-'.strtoupper(uniqid()),
                'user_id' => $userId,
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'total' => $total,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'payment_status' => 'unpaid',
                'notes' => $request->notes,
                'coupon_id' => $coupon?->id,
                'discount' => $discount,
            ]);

           foreach ($cart as $cartKey => $item) {
                    $productId = $item['product_id'] ?? (is_numeric($cartKey) ? $cartKey : null);
                    $variantId = $item['variant_id'] ?? null;

                    OrderItem::create([
                        'order_id'     => $order->id,
                        'product_id'   => $productId,
                        'product_name' => $item['name'],
                        'price'        => $item['price'],
                        'quantity'     => $item['quantity'],
                        'subtotal'     => $item['price'] * $item['quantity'],
                    ]);

                    // Stock deduction
                    $product = Product::find($productId);
                    if ($product && !$product->is_digital) {
                        if ($variantId) {
                            $variant = ProductVariant::find($variantId);
                            if ($variant) {
                                app(InventoryService::class)->adjust(
                                    $variant,
                                    -$item['quantity'],
                                    'sale',
                                    "Order {$order->order_number}"
                                );
                            }
                        } else {
                            app(InventoryService::class)->deductForOrder(
                                $product,
                                $item['quantity'],
                                $order->order_number
                            );
                        }
                    }
                }

            OrderAddress::create([
                'order_id' => $order->id,
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address_line1' => $request->address_line1,
                'address_line2' => $request->address_line2,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'country' => 'Pakistan',
            ]);

            Payment::create([
                'order_id' => $order->id,
                'gateway' => $request->payment_method,
                'amount' => $total,
                'currency' => setting('currency_code', 'PKR'),
                'status' => 'pending',
                'notes' => 'Order placed, awaiting payment.',
            ]);

            if ($coupon) {
                $coupon->increment('used_count');
            }

            DB::commit();

            // Emails
            try {
                $order->load('items', 'address', 'user');
                $customerEmail = $order->address->email ?? $order->user->email ?? null;
                if ($customerEmail) {
                    Mail::to($customerEmail)->send(new OrderPlaced($order));
                }
                $adminEmail = setting('store_email');
                if ($adminEmail && $adminEmail !== $customerEmail) {
                    Mail::to($adminEmail)->send(new OrderPlaced($order));
                }
            } catch (\Exception $e) {
                \Log::warning('Order email failed: '.$e->getMessage());
            }

            // Auto bank details
            try {
                if ($order->payment_method === 'bank') {
                    $bankMethod = PaymentMethod::where('code', 'bank')->first();
                    if ($bankMethod && $bankMethod->getSetting('auto_send_bank_details') == '1') {
                        $customerEmail = $order->address->email ?? $order->user->email ?? null;
                        if ($customerEmail) {
                            Mail::to($customerEmail)->send(new BankDetailsMail($order, $bankMethod));
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Bank details email failed: '.$e->getMessage());
            }

            session()->forget('cart');
            session()->forget('coupon_id');

            if ($request->boolean('create_account') && Auth::check()) {
                return redirect()->route('shop.my-account')
                    ->with('success', 'Order placed & account created!');
            }

            return redirect()->route('shop.order.success', $order->order_number)
                ->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Something went wrong: '.$e->getMessage());
        }
    }

    public function success($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->with('items', 'address')->firstOrFail();

        return view('shop.order-success', compact('order'));
    }
}
