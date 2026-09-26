<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\ShippingService;

class CartController extends Controller
{


            public function index()
            {
                $cart = session()->get('cart', []);
                $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);

                // Coupon
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

                // Shipping
                $shipping = app(ShippingService::class)->calculate($cart, $subtotal);

                $total = max(0, $subtotal - $discount) + $shipping;

                return view('shop.cart', compact('cart', 'subtotal', 'discount', 'coupon', 'shipping', 'total'));
            }

    public function add(Request $request, Product $product)
    {
        $cart = session()->get('cart', []);
        $id = $product->id;

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                'name' => $product->name,
                'price' => $product->sale_price ?? $product->price,
                'quantity' => 1,
                'image' => $product->primaryImage->path ?? null,
            ];
        }

        session()->put('cart', $cart);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Added to cart.',
                'count'   => count($cart),
            ]);
        }

        return redirect()->route('shop.cart')->with('success', 'Added to cart.');
    }

    public function update(Request $request, $id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = max(1, (int) $request->quantity);
            session()->put('cart', $cart);
        }
        return redirect()->route('shop.cart')->with('success', 'Cart updated.');
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);
        unset($cart[$id]);
        session()->put('cart', $cart);
        return redirect()->route('shop.cart')->with('success', 'Item removed.');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $coupon = Coupon::where('code', strtoupper($request->code))->first();

        if (!$coupon) {
            return back()->with('error', 'Invalid coupon code.');
        }

        $cart = session()->get('cart', []);
        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);

        if (!$coupon->isValidFor($subtotal)) {
            return back()->with('error', 'This coupon is not valid for your cart.');
        }

        session()->put('coupon_id', $coupon->id);

        return back()->with('success', "Coupon {$coupon->code} applied!");
    }

    public function removeCoupon()
    {
        session()->forget('coupon_id');
        return back()->with('success', 'Coupon removed.');
    }


    public function buyNow(Request $request, Product $product)
{
    $cart = session()->get('cart', []);
    $id = $product->id;

    $cart[$id] = [
        'name' => $product->name,
        'price' => $product->sale_price ?? $product->price,
        'quantity' => 1,
        'image' => $product->primaryImage->path ?? null,
    ];

    session()->put('cart', $cart);

    return redirect()->route('shop.checkout');
}

}