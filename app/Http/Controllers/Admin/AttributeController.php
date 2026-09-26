<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttributeController extends Controller
{
    public function index()
    {
        $attributes = Attribute::with('values')->orderBy('sort_order')->paginate(20);
        return view('admin.attributes.index', compact('attributes'));
    }

    public function create()
    {
        return view('admin.attributes.create');
    }

        public function store(Request $request)
        {
            $request->validate([
                'name' => 'required|string|max:100|unique:attributes,name',
                'type' => 'required|in:select,color,text',
            ]);

            $attribute = Attribute::create([
                'name'       => $request->name,
                'slug'       => Str::slug($request->name),
                'type'       => $request->type,
                'is_active'  => $request->has('is_active'),
                'sort_order' => $request->sort_order ?? 0,
            ]);

            // Save values added at creation time
            $newValues = $request->input('new_values', []);
            $newColors = $request->input('new_colors', []);

            foreach ($newValues as $i => $val) {
                if (!$val) continue;
                $attribute->values()->create([
                    'value'      => $val,
                    'color_code' => $newColors[$i] ?? null,
                    'sort_order' => $i,
                ]);
            }

            return redirect()->route('admin.attributes.index')->with('success', 'Attribute created.');
        }

    public function edit(Attribute $attribute)
    {
        $attribute->load('values');
        return view('admin.attributes.edit', compact('attribute'));
    }

    public function update(Request $request, Attribute $attribute)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:attributes,name,' . $attribute->id,
            'type' => 'required|in:select,color,text',
        ]);

        $attribute->update([
            'name'       => $request->name,
            'slug'       => Str::slug($request->name),
            'type'       => $request->type,
            'is_active'  => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        // Update existing values
        $existing = $request->input('values', []);
        $colors   = $request->input('colors', []);

        foreach ($attribute->values as $v) {
            if (isset($existing[$v->id])) {
                $v->update([
                    'value'      => $existing[$v->id],
                    'color_code' => $colors[$v->id] ?? null,
                ]);
            }
        }

        // Add new values
        $newValues = $request->input('new_values', []);
        $newColors = $request->input('new_colors', []);

        foreach ($newValues as $i => $val) {
            if (!$val) continue;
            $attribute->values()->create([
                'value'      => $val,
                'color_code' => $newColors[$i] ?? null,
                'sort_order' => 99 + $i,
            ]);
        }

        return redirect()->route('admin.attributes.index')->with('success', 'Attribute updated.');
    }

    public function destroy(Attribute $attribute)
    {
        $attribute->delete();
        return redirect()->route('admin.attributes.index')->with('success', 'Attribute deleted.');
    }

    public function toggle(Attribute $attribute)
    {
        $attribute->update(['is_active' => !$attribute->is_active]);
        return back()->with('success', 'Status updated.');
    }

    public function deleteValue(AttributeValue $value)
    {
        $value->delete();
        return back()->with('success', 'Value removed.');
    }
}