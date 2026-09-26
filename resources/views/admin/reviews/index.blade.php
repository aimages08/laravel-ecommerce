@extends('layouts.admin')

@section('title', 'Reviews')

@section('content')

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" class="bg-white p-4 rounded-xl shadow mb-4 flex gap-3 items-center">
        <label class="text-sm">Filter:</label>
        <select name="status" class="border rounded px-3 py-2 text-sm" onchange="this.form.submit()">
            <option value="">All</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
    </form>

    <div class="space-y-4">
        @forelse ($reviews as $review)
            <div class="bg-white rounded-xl shadow p-5">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="font-semibold">
                            {{ $review->name }}
                            <p class="text-xs text-gray-500">{{ $review->email ?? '—' }}</p>
                            <span class="text-xs text-gray-400">on</span>
                            <span class="text-indigo-600">{{ $review->product->name ?? '(deleted)' }}</span>
                        </p>
                        <p class="text-yellow-500">
                            {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                        </p>
                        <p class="text-xs text-gray-400">{{ $review->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded
                        @if($review->status === 'approved') bg-green-100 text-green-700
                        @elseif($review->status === 'rejected') bg-red-100 text-red-700
                        @else bg-yellow-100 text-yellow-800 @endif">
                        {{ ucfirst($review->status) }}
                    </span>
                </div>

                @if ($review->comment)
                    <p class="text-gray-700 text-sm mb-3">{{ $review->comment }}</p>
                @endif

                @if ($review->admin_reply)
                    <div class="bg-indigo-50 border-l-4 border-indigo-400 p-3 text-sm mb-3">
                        <p class="font-semibold text-indigo-700">Your reply:</p>
                        <p class="text-gray-700">{{ $review->admin_reply }}</p>
                    </div>
                @endif

                <div class="flex flex-wrap gap-2 pt-3 border-t">
                    @if ($review->status !== 'approved')
                        <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                            @csrf @method('PATCH')
                            <button class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700">
                                Approve
                            </button>
                        </form>
                    @endif

                    @if ($review->status !== 'rejected')
                        <form action="{{ route('admin.reviews.reject', $review) }}" method="POST">
                            @csrf @method('PATCH')
                            <button class="bg-yellow-500 text-white px-3 py-1 rounded text-xs hover:bg-yellow-600">
                                Reject
                            </button>
                        </form>
                    @endif

                    

                    <details class="w-full mt-2">
                        <summary class="cursor-pointer text-xs text-indigo-600">Reply / Edit Reply</summary>
                        <form action="{{ route('admin.reviews.reply', $review) }}" method="POST" class="mt-2 space-y-2">
                            @csrf
                            <textarea name="admin_reply" rows="2" placeholder="Your reply..."
                                      class="w-full border rounded px-3 py-2 text-sm">{{ $review->admin_reply }}</textarea>
                            <button class="bg-indigo-600 text-white px-3 py-1 rounded text-xs">Save Reply</button>
                        </form>
                    </details>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow p-8 text-center text-gray-500">
                No reviews found.
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $reviews->links() }}</div>
@endsection