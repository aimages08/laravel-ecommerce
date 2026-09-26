@extends('layouts.admin')

@section('title', 'Message from ' . $message->name)

@section('content')
    <a href="{{ route('admin.contact-messages.index') }}" class="text-indigo-600 hover:underline mb-4 inline-block">
        ← Back to Messages
    </a>

    <div class="bg-white rounded-xl shadow p-6 max-w-2xl">
        <h1 class="text-xl font-bold mb-4">{{ $message->subject }}</h1>

        <div class="bg-gray-50 p-4 rounded mb-4 text-sm">
            <p><strong>From:</strong> {{ $message->name }}</p>
            <p><strong>Email:</strong> {{ $message->email }}</p>
            @if ($message->phone)
                <p><strong>Phone:</strong> {{ $message->phone }}</p>
            @endif
            <p><strong>Date:</strong> {{ $message->created_at->format('M d, Y H:i') }}</p>
        </div>

        <div class="text-gray-700 whitespace-pre-wrap">{{ $message->message }}</div>

        <div class="mt-6 pt-4 border-t flex gap-2">
            <a href="mailto:{{ $message->email }}?subject=Re: {{ urlencode($message->subject) }}"
               class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-sm">
                Reply by Email
            </a>
            <form action="{{ route('admin.contact-messages.destroy', $message) }}" method="POST"
                  onsubmit="return confirm('Delete?')">
                @csrf @method('DELETE')
                <button class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 text-sm">Delete</button>
            </form>
        </div>
    </div>
@endsection