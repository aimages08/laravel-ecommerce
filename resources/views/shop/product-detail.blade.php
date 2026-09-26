@extends('layouts.shop')

@section('meta_title', $product->meta_title ?: $product->name . ' - ' . setting('store_name'))
@section('meta_description', $product->meta_description ?: Str::limit($product->description, 150))

@section('content')

    @php
        $primary = $product->images->where('is_primary', true)->first()
                   ?? $product->images->where('type', 'image')->first();
        $video = $product->images->where('type', 'video')->first();

        $primaryUrl = $primary
            ? (str_starts_with($primary->path, 'http') ? $primary->path : asset('storage/' . $primary->path))
            : null;

        $videoUrl = $video
            ? (str_starts_with($video->path, 'http') ? $video->path : asset('storage/' . $video->path))
            : null;
    @endphp

    <div class="bg-white rounded-xl shadow p-8 grid md:grid-cols-2 gap-8">
        {{-- Gallery --}}
        <div>
            @if ($primaryUrl)
                <img id="main-image" src="{{ $primaryUrl }}" class="w-full h-64 md:h-96 object-cover rounded-lg mb-3">
            @else
                <div class="w-full h-96 bg-gray-200 flex items-center justify-center text-gray-400 rounded-lg mb-3">No Image</div>
            @endif

            <div class="flex gap-2 overflow-x-auto">
                @foreach ($product->images->where('type', 'image') as $img)
                    @php $thumbUrl = str_starts_with($img->path, 'http') ? $img->path : asset('storage/' . $img->path); @endphp
                    <img src="{{ $thumbUrl }}"
                         onclick="document.getElementById('main-image').src = this.src"
                         class="w-20 h-20 object-cover rounded cursor-pointer border hover:border-indigo-600">
                @endforeach

                @if ($videoUrl)
                    <div class="w-20 h-20 bg-black text-white flex items-center justify-center rounded cursor-pointer text-xs"
                         onclick="document.getElementById('video-box').scrollIntoView({behavior:'smooth'})">
                        ▶ Video
                    </div>
                @endif
            </div>
        </div>

        {{-- Info --}}
        <div>
            <p class="text-sm text-indigo-600 mb-1">{{ $product->category->name ?? '' }}</p>
            <h1 class="text-3xl font-bold mb-3">{{ $product->name }}</h1>

            <div class="text-2xl font-bold text-indigo-600 mb-4" id="product-price">
                @if ($product->sale_price)
                    <span class="line-through text-gray-400 text-lg">Rs. {{ number_format($product->price) }}</span>
                    Rs. <span id="price-value">{{ number_format($product->sale_price) }}</span>
                @else
                    Rs. <span id="price-value">{{ number_format($product->price) }}</span>
                @endif
            </div>

            <p class="text-gray-700 mb-6">{{ $product->description }}</p>

            <p class="text-sm mb-4" id="stock-info">
                @if ($product->stock > 0)
                    <span class="text-green-600">In Stock ({{ $product->stock }})</span>
                @else
                    <span class="text-red-600">Out of Stock</span>
                @endif
            </p>

            {{-- VARIANTS --}}
            @if ($product->has_variants && $product->variants->count())
                @php
                    $variantData = $product->variants->map(function ($v) use ($product) {
                        return [
                            'id' => $v->id,
                            'label' => $v->label,
                            'value_ids' => $v->attributeValues->pluck('id')->toArray(),
                            'price' => $v->sale_price ?? $v->price ?? ($product->sale_price ?? $product->price),
                            'stock' => $v->stock,
                        ];
                    })->values()->toArray();
                @endphp

                <div class="mb-4" id="variant-selector" data-variants='@json($variantData)'>
                    @foreach ($product->productAttributes as $attr)
                        <div class="mb-3">
                            <label class="block text-sm font-medium mb-1">
                                {{ $attr->name }} <span class="text-gray-400">*</span>
                            </label>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($attr->values as $val)
                                    <button type="button"
                                            class="variant-value-btn border rounded-lg px-4 py-2 text-sm hover:border-indigo-600"
                                            data-value-id="{{ $val->id }}"
                                            data-attribute-id="{{ $attr->id }}">
                                        @if ($attr->type === 'color' && $val->color_code)
                                            <span class="w-3 h-3 rounded-full inline-block mr-1 align-middle"
                                                  style="background: {{ $val->color_code }}"></span>
                                        @endif
                                        {{ $val->value }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Add to Cart --}}
            <form id="add-to-cart-form" action="{{ route('shop.cart.add', $product) }}" method="POST" class="inline-block">
                @csrf
                <input type="hidden" name="variant_id" id="variant_id_input" value="">
                <button type="submit"
                        class="bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700">
                    Add to Cart
                </button>
            </form>

            {{-- Buy Now --}}
            <form id="buy-now-form" action="{{ route('shop.cart.buy-now', $product) }}" method="POST" class="inline-block ml-2">
                @csrf
                <input type="hidden" name="variant_id" id="variant_id_input_buy" value="">
                <button type="submit"
                        class="bg-orange-500 text-white px-8 py-3 rounded-lg hover:bg-orange-600">
                    Buy Now
                </button>
            </form>

            <div id="cart-msg" class="mt-3 text-green-600 text-sm hidden"></div>
        </div>
    </div>

    @if ($videoUrl)
        <div id="video-box" class="bg-white rounded-xl shadow p-6 mt-6">
            <h2 class="text-lg font-bold mb-3">Product Video</h2>
            <video controls class="w-full max-w-2xl rounded-lg">
                <source src="{{ $videoUrl }}" type="video/mp4">
            </video>
        </div>
    @endif

    {{-- REVIEWS --}}
    <div class="bg-white rounded-xl shadow p-8 mt-8">
        <h2 class="text-2xl font-bold mb-2">Customer Reviews</h2>

        <p class="text-sm text-gray-500 mb-6">
            @if ($totalReviews)
                Average rating: <strong>{{ number_format($avgRating, 1) }} / 5</strong>
                ({{ $totalReviews }} {{ \Illuminate\Support\Str::plural('review', $totalReviews) }})
            @else
                No reviews yet. Be the first!
            @endif
        </p>

        <div class="bg-gray-50 border rounded-xl p-6 mb-8">
            <h3 class="text-lg font-bold mb-4">Write a Review</h3>

            <form action="{{ route('shop.product.review', $product) }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm mb-1">Your Name *</label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()->name ?? '') }}"
                               required maxlength="100" class="w-full border rounded px-3 py-2">
                        @error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Email *</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}"
                               required maxlength="150" class="w-full border rounded px-3 py-2">
                        @error('email')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                        <p class="text-xs text-gray-500 mt-1">Email will not be published.</p>
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Rating *</label>
                        <select name="rating" required class="w-full border rounded px-3 py-2">
                            <option value="5">★★★★★ Excellent</option>
                            <option value="4">★★★★☆ Good</option>
                            <option value="3">★★★☆☆ Average</option>
                            <option value="2">★★☆☆☆ Poor</option>
                            <option value="1">★☆☆☆☆ Terrible</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm mb-1">Comment *</label>
                    <textarea name="comment" rows="4" required maxlength="1000"
                              class="w-full border rounded px-3 py-2"
                              placeholder="Share your experience...">{{ old('comment') }}</textarea>
                    @error('comment')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <button class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">
                    Submit Review
                </button>
            </form>
        </div>

        @forelse ($reviews as $review)
            <div class="border-b pb-4 mb-4 last:border-0">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-semibold">{{ $review->name }}</p>
                        <p class="text-yellow-500 text-sm">
                            {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                        </p>
                    </div>
                    <p class="text-xs text-gray-400">{{ $review->created_at->format('M d, Y') }}</p>
                </div>
                @if ($review->comment)
                    <p class="text-gray-700 text-sm mt-2">{{ $review->comment }}</p>
                @endif
                @if ($review->admin_reply)
                    <div class="mt-3 ml-4 pl-4 border-l-2 border-indigo-300 text-sm">
                        <p class="font-semibold text-indigo-600">Store Reply:</p>
                        <p class="text-gray-600">{{ $review->admin_reply }}</p>
                    </div>
                @endif
            </div>
        @empty
            <p class="text-gray-500 text-sm">No reviews yet. Be the first to review this product.</p>
        @endforelse

        @if ($reviews->hasPages())
            <div class="mt-6 pt-4 border-t flex justify-center">
                {{ $reviews->onEachSide(1)->links('pagination::tailwind') }}
            </div>
        @endif
    </div>

    {{-- Related --}}
    @if ($related->count())
        <h2 class="text-xl font-bold mt-10 mb-4">Related Products</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach ($related as $item)
                @php
                    $relImg = $item->primaryImage
                        ? (str_starts_with($item->primaryImage->path, 'http') ? $item->primaryImage->path : asset('storage/' . $item->primaryImage->path))
                        : null;
                @endphp

                <a href="{{ route('shop.product.show', $item->slug) }}"
                   class="bg-white rounded-xl shadow p-4 hover:shadow-lg">
                    @if ($relImg)
                        <img src="{{ $relImg }}" class="h-32 w-full object-cover rounded mb-2">
                    @else
                        <div class="h-32 bg-gray-200 rounded mb-2"></div>
                    @endif
                    <p class="font-medium text-sm">{{ $item->name }}</p>
                    <p class="text-indigo-600 font-bold text-sm">
                        Rs. {{ number_format($item->sale_price ?? $item->price) }}
                    </p>
                </a>
            @endforeach
        </div>
    @endif

    <script>
    @if ($product->has_variants && $product->variants->count())
    (function () {
        const allVariants = JSON.parse(document.getElementById('variant-selector').dataset.variants || '[]');
        const valueBtns = document.querySelectorAll('.variant-value-btn');
        const selected = {};

        function updateUI() {
            valueBtns.forEach(btn => {
                const aid = btn.dataset.attributeId;
                const vid = btn.dataset.valueId;
                if (selected[aid] == vid) {
                    btn.classList.add('border-indigo-600', 'bg-indigo-50', 'font-semibold');
                } else {
                    btn.classList.remove('border-indigo-600', 'bg-indigo-50', 'font-semibold');
                }
            });

            const attrCount = new Set([...valueBtns].map(b => b.dataset.attributeId)).size;
            const chosenIds = Object.values(selected).filter(Boolean);

            if (chosenIds.length < attrCount) {
                document.getElementById('variant_id_input').value = '';
                document.getElementById('variant_id_input_buy').value = '';
                return;
            }

            const match = allVariants.find(v =>
                chosenIds.every(id => v.value_ids.includes(parseInt(id)))
                && v.value_ids.length === chosenIds.length
            );

            if (match) {
                document.getElementById('variant_id_input').value = match.id;
                document.getElementById('variant_id_input_buy').value = match.id;

                document.getElementById('price-value').textContent = Number(match.price).toLocaleString();

                const stockInfo = document.getElementById('stock-info');
                if (match.stock > 0) {
                    stockInfo.innerHTML = `<span class="text-green-600">In Stock (${match.stock})</span>`;
                } else {
                    stockInfo.innerHTML = `<span class="text-red-600">Out of Stock</span>`;
                }
            }
        }

        valueBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const aid = btn.dataset.attributeId;
                const vid = btn.dataset.valueId;
                selected[aid] = (selected[aid] == vid) ? null : vid;
                updateUI();
            });
        });
    })();
    @endif

    document.getElementById('add-to-cart-form').addEventListener('submit', async function (e) {
        e.preventDefault();

        @if ($product->has_variants && $product->variants->count())
            if (!document.getElementById('variant_id_input').value) {
                alert('Please select all options first.');
                return;
            }
        @endif

        const form = e.target;
        const res = await fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: new FormData(form),
        });
        const data = await res.json();

        if (data.success) {
            const msg = document.getElementById('cart-msg');
            msg.textContent = data.message;
            msg.classList.remove('hidden');

            const cartLink = document.getElementById('cart-count-link');
            if (cartLink) cartLink.textContent = `Cart (${data.count})`;

            setTimeout(() => msg.classList.add('hidden'), 2000);
        } else if (data.error) {
            alert(data.error);
        }
    });
    </script>

@endsection