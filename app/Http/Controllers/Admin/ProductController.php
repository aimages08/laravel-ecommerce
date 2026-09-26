<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'brand', 'primaryImage'])->latest()->paginate(10);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();

        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateProduct($request);

        $validated['slug'] = Str::slug($request->name);
        $validated['sku'] = $request->sku ?: null;

        $product = Product::create($validated);

        $this->handleMedia($request, $product);

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();

        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $this->validateProduct($request, $product->id);

        $validated['slug'] = Str::slug($request->name);
        $validated['sku'] = $request->sku ?: null;

        $product->update($validated);

        $this->handleMedia($request, $product);

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->path);
        }
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }

    public function deleteImage(ProductImage $image)
    {
        Storage::disk('public')->delete($image->path);
        $image->delete();

        return back()->with('success', 'Media deleted.');
    }

    public function setPrimary(ProductImage $image)
    {
        ProductImage::where('product_id', $image->product_id)->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return back()->with('success', 'Primary image set.');
    }

    /* ============= Helpers ============= */

    private function validateProduct(Request $request, $ignoreId = null): array
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:products,name'.($ignoreId ? ",$ignoreId" : ''),
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'low_stock_alert_enabled' => 'nullable|boolean',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'barcode' => 'nullable|string|max:100',
            'shipping_type' => 'required|in:global,flat,per_kg,free',
            'shipping_fee' => 'nullable|numeric|min:0',
            'shipping_rate_per_kg' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published,archived',
            'images' => 'nullable|array|max:6',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'video' => 'nullable|mimes:mp4|max:20480',
        ]);

        return [
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'name' => $request->name,
            'sku' => $request->sku,
            'barcode' => $request->barcode,
            'description' => $request->description,
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'cost_price' => $request->cost_price,
            'tax_rate' => $request->tax_rate ?? 0,
            'stock' => $request->stock,
            'low_stock_threshold' => $request->low_stock_threshold ?? 5,
            'low_stock_alert_enabled' => $request->has('low_stock_alert_enabled'),
            'shipping_type' => $request->shipping_type,
            'shipping_fee' => $request->shipping_fee,
            'shipping_rate_per_kg' => $request->shipping_rate_per_kg,
            'weight' => $request->weight,
            'length' => $request->length,
            'width' => $request->width,
            'height' => $request->height,
            'is_featured' => $request->has('is_featured'),
            'is_new' => $request->has('is_new'),
            'is_active' => $request->has('is_active'),
            'is_digital' => $request->has('is_digital'),
            'status' => $request->status,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
        ];
    }

    private function handleMedia(Request $request, Product $product): void
    {
        if ($request->hasFile('images')) {
            $sort = $product->images()->max('sort_order') + 1;
            foreach ($request->file('images') as $file) {
                $path = $file->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $path,
                    'type' => 'image',
                    'is_primary' => $product->images()->count() === 0,
                    'sort_order' => $sort++,
                ]);
            }
        }

        if ($request->hasFile('video')) {
            $path = $request->file('video')->store('products', 'public');
            ProductImage::create([
                'product_id' => $product->id,
                'path' => $path,
                'type' => 'video',
                'sort_order' => 999,
            ]);
        }
    }

    public function generateVariants(Request $request)
    {
        $request->validate([
            'value_ids' => 'required|array|min:1',
            'value_ids.*' => 'exists:attribute_values,id',
        ]);

        $values = AttributeValue::with('attribute')
            ->whereIn('id', $request->value_ids)
            ->get()
            ->groupBy('attribute_id');

        $matrix = [[]];
        foreach ($values as $group) {
            $new = [];
            foreach ($matrix as $row) {
                foreach ($group as $val) {
                    $new[] = array_merge($row, [$val]);
                }
            }
            $matrix = $new;
        }

        $rows = [];
        foreach ($matrix as $combo) {
            $rows[] = [
                'labels' => array_map(fn ($v) => [
                    'attr' => $v->attribute->name,
                    'value' => $v->value,
                    'color' => $v->color_code,
                ], $combo),
                'value_ids' => array_map(fn ($v) => $v->id, $combo),
                'label' => implode(' / ', array_map(fn ($v) => $v->value, $combo)),
            ];
        }

        return response()->json(['variants' => $rows]);
    }
}
