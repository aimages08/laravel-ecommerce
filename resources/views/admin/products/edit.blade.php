@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')

    @php
        $variantData = $product->variants->map(function ($v) {
            return [
                'id' => $v->id,
                'label' => $v->label,
                'value_ids' => $v->attributeValues->pluck('id')->toArray(),
                'sku' => $v->sku,
                'price' => $v->price,
                'sale_price' => $v->sale_price,
                'stock' => $v->stock,
            ];
        })->values()->toArray();
    @endphp

    <div class="bg-white p-6 rounded-xl shadow" x-data="{ tab: 'basic' }">

        {{-- Existing Media --}}
        @if ($product->images->count())
            <div class="mb-6 pb-6 border-b">
                <label class="block mb-2 font-medium">Existing Media</label>
                <div class="grid grid-cols-3 gap-3">
                    @foreach ($product->images as $media)
                        <div class="relative border rounded p-2 bg-gray-50">
                            @if ($media->type === 'video')
                                <video class="w-full h-24 object-cover rounded" controls>
                                    <source src="{{ str_starts_with($media->path, 'http') ? $media->path : asset('storage/' . $media->path) }}">
                                </video>
                            @else
                                <img src="{{ str_starts_with($media->path, 'http') ? $media->path : asset('storage/' . $media->path) }}"
                                     class="w-full h-24 object-cover rounded">
                            @endif

                            @if ($media->is_primary)
                                <span class="absolute top-1 left-1 bg-indigo-600 text-white text-xs px-2 py-0.5 rounded">
                                    Primary
                                </span>
                            @endif

                            <div class="flex gap-2 mt-2 text-xs">
                                @if ($media->type === 'image' && !$media->is_primary)
                                    <form action="{{ route('admin.products.image.primary', $media) }}"
                                          method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button class="text-indigo-600 hover:underline">Set Primary</button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.products.image.delete', $media) }}"
                                      method="POST" class="inline"
                                      onsubmit="return confirm('Delete this media?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- MAIN FORM --}}
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Tabs --}}
            <div class="flex gap-2 mb-6 border-b">
                <button type="button" @click="tab='basic'"
                        :class="tab==='basic' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500'"
                        class="px-4 py-2 border-b-2 font-medium">Basic</button>
                <button type="button" @click="tab='pricing'"
                        :class="tab==='pricing' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500'"
                        class="px-4 py-2 border-b-2 font-medium">Pricing</button>
                <button type="button" @click="tab='inventory'"
                        :class="tab==='inventory' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500'"
                        class="px-4 py-2 border-b-2 font-medium">Inventory</button>
                <button type="button" @click="tab='shipping'"
                        :class="tab==='shipping' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500'"
                        class="px-4 py-2 border-b-2 font-medium">Shipping</button>
                <button type="button" @click="tab='variants'"
                        :class="tab==='variants' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500'"
                        class="px-4 py-2 border-b-2 font-medium">Variants</button>
                <button type="button" @click="tab='seo'"
                        :class="tab==='seo' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500'"
                        class="px-4 py-2 border-b-2 font-medium">SEO</button>
            </div>

            {{-- BASIC --}}
            <div x-show="tab==='basic'" class="space-y-4">
                <div>
                    <label class="block mb-1 font-medium">Name *</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}"
                           class="w-full border rounded px-3 py-2">
                    @error('name')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block mb-1 font-medium">SKU</label>
                        <input type="text" name="sku" value="{{ old('sku', $product->sku) }}"
                               class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Barcode</label>
                        <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}"
                               class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Status</label>
                        <select name="status" class="w-full border rounded px-3 py-2">
                            <option value="published" {{ old('status', $product->status) === 'published' ? 'selected' : '' }}>Published</option>
                            <option value="draft" {{ old('status', $product->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="archived" {{ old('status', $product->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 font-medium">Category</label>
                        <select name="category_id" class="w-full border rounded px-3 py-2">
                            <option value="">-- Select --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Brand</label>
                        <select name="brand_id" class="w-full border rounded px-3 py-2">
                            <option value="">-- Select --</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}"
                                    {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block mb-1 font-medium">Description</label>
                    <textarea name="description" rows="4"
                              class="w-full border rounded px-3 py-2">{{ old('description', $product->description) }}</textarea>
                </div>

                <div>
                    <label class="block mb-1 font-medium">Add More Images</label>
                    <input type="file" name="images[]" multiple accept="image/*"
                           class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block mb-1 font-medium">Add / Replace Video (mp4)</label>
                    <input type="file" name="video" accept="video/mp4"
                           class="w-full border rounded px-3 py-2">
                </div>
            </div>

            {{-- PRICING --}}
            <div x-show="tab==='pricing'" class="space-y-4">
                <div class="grid grid-cols-4 gap-4">
                    <div>
                        <label class="block mb-1 font-medium">Cost Price</label>
                        <input type="number" step="0.01" name="cost_price"
                               value="{{ old('cost_price', $product->cost_price) }}"
                               class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Price *</label>
                        <input type="number" step="0.01" name="price"
                               value="{{ old('price', $product->price) }}"
                               class="w-full border rounded px-3 py-2">
                        @error('price')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Sale Price</label>
                        <input type="number" step="0.01" name="sale_price"
                               value="{{ old('sale_price', $product->sale_price) }}"
                               class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Tax Rate (%)</label>
                        <input type="number" step="0.01" name="tax_rate"
                               value="{{ old('tax_rate', $product->tax_rate) }}"
                               class="w-full border rounded px-3 py-2">
                    </div>
                </div>
            </div>

            {{-- INVENTORY --}}
            <div x-show="tab==='inventory'" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 font-medium">Stock *</label>
                        <input type="number" name="stock"
                               value="{{ old('stock', $product->stock) }}"
                               class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Low Stock Threshold</label>
                        <input type="number" name="low_stock_threshold"
                               value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}"
                               class="w-full border rounded px-3 py-2">
                    </div>
                </div>

                <div class="pt-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="low_stock_alert_enabled" value="1"
                               {{ old('low_stock_alert_enabled', $product->low_stock_alert_enabled) ? 'checked' : '' }} class="mr-2">
                        Enable low stock alert for this product
                    </label>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_featured" value="1"
                               {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="mr-2"> Featured
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_new" value="1"
                               {{ old('is_new', $product->is_new) ? 'checked' : '' }} class="mr-2"> New Arrival
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="mr-2"> Active
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_digital" value="1"
                               {{ old('is_digital', $product->is_digital) ? 'checked' : '' }} class="mr-2"> Digital Product
                    </label>
                </div>
            </div>

            {{-- SHIPPING --}}
            <div x-show="tab==='shipping'" class="space-y-4"
                 x-data="{ type: '{{ old('shipping_type', $product->shipping_type) }}' }">
                <div>
                    <label class="block mb-1 font-medium">Shipping Type *</label>
                    <select name="shipping_type" x-model="type" class="w-full border rounded px-3 py-2">
                        <option value="global">Use Global Rate</option>
                        <option value="free">Free Shipping</option>
                        <option value="flat">Flat Fee</option>
                        <option value="per_kg">Per KG Rate</option>
                    </select>
                </div>

                <div x-show="type==='flat'" class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 font-medium">Flat Fee (Rs.)</label>
                        <input type="number" step="0.01" name="shipping_fee"
                               value="{{ old('shipping_fee', $product->shipping_fee) }}"
                               class="w-full border rounded px-3 py-2">
                    </div>
                </div>

                <div x-show="type==='per_kg'" class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 font-medium">Weight (kg)</label>
                        <input type="number" step="0.01" name="weight"
                               value="{{ old('weight', $product->weight) }}"
                               class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Rate per KG (Rs.)</label>
                        <input type="number" step="0.01" name="shipping_rate_per_kg"
                               value="{{ old('shipping_rate_per_kg', $product->shipping_rate_per_kg) }}"
                               class="w-full border rounded px-3 py-2">
                    </div>
                </div>

                <div class="pt-3">
                    <h3 class="font-medium mb-2">Product Dimensions (optional)</h3>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block mb-1 text-sm">Length (cm)</label>
                            <input type="number" step="0.01" name="length"
                                   value="{{ old('length', $product->length) }}"
                                   class="w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="block mb-1 text-sm">Width (cm)</label>
                            <input type="number" step="0.01" name="width"
                                   value="{{ old('width', $product->width) }}"
                                   class="w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="block mb-1 text-sm">Height (cm)</label>
                            <input type="number" step="0.01" name="height"
                                   value="{{ old('height', $product->height) }}"
                                   class="w-full border rounded px-3 py-2">
                        </div>
                    </div>
                </div>
            </div>

            {{-- VARIANTS --}}
            <div x-show="tab==='variants'" x-data="variantManager()" x-cloak>
                <div class="mb-4">
                    <label class="inline-flex items-center font-medium">
                        <input type="checkbox" x-model="hasVariants" name="has_variants" value="1" class="mr-2">
                        This product has variants (Size, Color, etc.)
                    </label>
                </div>

                <template x-if="hasVariants">
                    <div>
                        <h3 class="font-bold mb-2">Select Attributes</h3>
                        <p class="text-sm text-gray-500 mb-3">Tick attributes, then generate variants.</p>

                        <div class="grid grid-cols-2 gap-3 mb-4">
                            @foreach (\App\Models\Attribute::active()->with('values')->get() as $attr)
                                <div class="border rounded p-3 bg-gray-50">
                                    <label class="inline-flex items-center font-medium">
                                        <input type="checkbox" name="attributes[]" value="{{ $attr->id }}"
                                               x-model="selectedAttributes"
                                               @change="toggleAttribute({{ $attr->id }}, {{ $attr->values->toJson() }})"
                                               class="mr-2">
                                        {{ $attr->name }}
                                    </label>
                                    <div class="mt-2 ml-6">
                                        @foreach ($attr->values as $val)
                                            <label class="inline-flex items-center text-sm mr-3">
                                                <input type="checkbox"
                                                       x-model="selectedValues[{{ $attr->id }}]"
                                                       value="{{ $val->id }}"
                                                       class="mr-1">
                                                @if ($attr->type === 'color')
                                                    <span class="w-3 h-3 rounded-full inline-block mr-1"
                                                          style="background: {{ $val->color_code ?? '#ccc' }}"></span>
                                                @endif
                                                {{ $val->value }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" @click="generateVariants()"
                                class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                            Regenerate Variant Grid
                        </button>

                        <template x-if="variants.length > 0">
                            <div class="mt-6 overflow-x-auto">
                                <table class="w-full text-sm border">
                                    <thead class="bg-gray-100 text-left">
                                        <tr>
                                            <th class="p-2">Variant</th>
                                            <th class="p-2">SKU</th>
                                            <th class="p-2">Price</th>
                                            <th class="p-2">Sale Price</th>
                                            <th class="p-2">Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(row, i) in variants" :key="i">
                                            <tr class="border-t">
                                                <td class="p-2">
                                                    <span x-text="row.label"></span>
                                                    <input type="hidden" :name="`variants[${i}][id]`" :value="row.id || ''">
                                                    <template x-for="vId in row.value_ids" :key="vId">
                                                        <input type="hidden" :name="`variants[${i}][value_ids][]`" :value="vId">
                                                    </template>
                                                </td>
                                                <td class="p-2"><input type="text" :name="`variants[${i}][sku]`" x-model="row.sku" class="w-full border rounded px-2 py-1 text-xs"></td>
                                                <td class="p-2"><input type="number" step="0.01" :name="`variants[${i}][price]`" x-model="row.price" class="w-24 border rounded px-2 py-1 text-xs"></td>
                                                <td class="p-2"><input type="number" step="0.01" :name="`variants[${i}][sale_price]`" x-model="row.sale_price" class="w-24 border rounded px-2 py-1 text-xs"></td>
                                                <td class="p-2"><input type="number" :name="`variants[${i}][stock]`" x-model="row.stock" class="w-20 border rounded px-2 py-1 text-xs"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            {{-- SEO --}}
            <div x-show="tab==='seo'" class="space-y-4">
                <div>
                    <label class="block mb-1 font-medium">Meta Title</label>
                    <input type="text" name="meta_title"
                           value="{{ old('meta_title', $product->meta_title) }}"
                           class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block mb-1 font-medium">Meta Description</label>
                    <textarea name="meta_description" rows="3"
                              class="w-full border rounded px-3 py-2">{{ old('meta_description', $product->meta_description) }}</textarea>
                </div>
            </div>

            <div class="flex gap-2 mt-6 pt-4 border-t">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">Update</button>
                <a href="{{ route('admin.products.index') }}" class="px-6 py-2 rounded border">Cancel</a>
            </div>
        </form>
    </div>

    <script>
    function variantManager() {
        return {
            hasVariants: {{ old('has_variants', $product->has_variants) ? 'true' : 'false' }},
            selectedAttributes: [],
            selectedValues: {},
            variants: {!! json_encode(old('variants', $variantData)) !!},

            toggleAttribute(id, values) {
                if (this.selectedAttributes.includes(id)) {
                    if (!this.selectedValues[id]) {
                        this.selectedValues[id] = values.map(v => v.id);
                    }
                } else {
                    delete this.selectedValues[id];
                }
            },

            async generateVariants() {
                const allIds = Object.values(this.selectedValues).flat().filter(Boolean);
                if (allIds.length === 0) {
                    alert('Select at least one attribute value first.');
                    return;
                }

                const res = await fetch('{{ route('admin.products.generate-variants') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ value_ids: allIds }),
                });

                const data = await res.json();
                this.variants = data.variants.map(v => ({
                    id: null,
                    label: v.label,
                    value_ids: v.value_ids,
                    sku: '',
                    price: '',
                    sale_price: '',
                    stock: 0,
                }));
            },
        };
    }
    </script>
@endsection