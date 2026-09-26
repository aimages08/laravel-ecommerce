<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NewsletterController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsletterSubscriber::latest();

        if ($request->filter === 'active') {
            $query->where('is_subscribed', true);
        } elseif ($request->filter === 'inactive') {
            $query->where('is_subscribed', false);
        }

        $subscribers = $query->paginate(20)->withQueryString();

        $totalActive   = NewsletterSubscriber::where('is_subscribed', true)->count();
        $totalInactive = NewsletterSubscriber::where('is_subscribed', false)->count();

        return view('admin.newsletter.index', compact('subscribers', 'totalActive', 'totalInactive'));
    }

    public function destroy(NewsletterSubscriber $subscriber)
    {
        $subscriber->delete();
        return back()->with('success', 'Subscriber removed.');
    }

    public function export(): StreamedResponse
    {
        $filename = 'newsletter-subscribers-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Email', 'Subscribed', 'Date']);

            NewsletterSubscriber::where('is_subscribed', true)->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $r) {
                    fputcsv($out, [$r->email, 'Yes', $r->created_at->format('Y-m-d H:i')]);
                }
            });

            fclose($out);
        }, $filename);
    }
}