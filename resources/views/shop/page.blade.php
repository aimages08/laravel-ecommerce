@extends('layouts.shop')

@section('meta_title', $page->meta_title ?: $page->title)
@section('meta_description', $page->meta_description ?: '')

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow">
        <h1 class="text-3xl font-bold mb-6">{{ $page->title }}</h1>
        <div class="prose max-w-none text-gray-700">
            {!! $page->content !!}
        </div>
    </div>
@endsection