@php
    $imageUrl = $product->primaryImage
        ? (str_starts_with($product->primaryImage->path, 'http')
            ? $product->primaryImage->path
            : asset('storage/' . $product->primaryImage->path))
        : null;
@endphp

<div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden flex flex-col">
    <a href="{{ route('shop.product.show', $product->slug) }}"
       class="block w-full h-48 bg-gray-100 overflow-hidden">
        @if ($imageUrl)
            <img src="{{ $imageUrl }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">
                No Image
            </div>
        @endif
    </a>
    <div class="p-3 flex-1 flex flex-col">
        <h3 class="font-semibold text-sm mb-1">
            <a href="{{ route('shop.product.show', $product->slug) }}"
               class="hover:text-indigo-600">{{ $product->name }}</a>
        </h3>
        <div class="mt-auto font-bold text-indigo-600 text-sm">
            @if ($product->sale_price)
                <span class="line-through text-gray-400 text-xs">
                    Rs. {{ number_format($product->price) }}
                </span>
                Rs. {{ number_format($product->sale_price) }}
            @else
                Rs. {{ number_format($product->price) }}
            @endif
        </div>
    </div>
</div>