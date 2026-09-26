<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:150',
        ]);

        $sub = NewsletterSubscriber::where('email', $request->email)->first();

        if ($sub) {
            if ($sub->is_subscribed) {
                return back()->with('success', 'You are already subscribed.');
            }
            $sub->update(['is_subscribed' => true, 'unsubscribed_at' => null]);
            return back()->with('success', 'Welcome back! You are subscribed again.');
        }

        NewsletterSubscriber::create(['email' => $request->email]);

        return back()->with('success', 'Thank you for subscribing!');
    }

    public function unsubscribe($token)
    {
        $sub = NewsletterSubscriber::where('token', $token)->firstOrFail();

        $sub->update([
            'is_subscribed' => false,
            'unsubscribed_at' => now(),
        ]);

        return redirect()->route('shop.home')->with('success', 'You have been unsubscribed.');
    }
}