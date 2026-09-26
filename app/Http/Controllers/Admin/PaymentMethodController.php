<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $methods = PaymentMethod::with('settings')->orderBy('sort_order')->get();
        return view('admin.payment-methods.index', compact('methods'));
    }

    public function create()
    {
        return view('admin.payment-methods.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|alpha_dash|unique:payment_methods,code',
        ]);

        PaymentMethod::create([
            'name'         => $request->name,
            'code'         => strtolower($request->code),
            'is_enabled'   => $request->has('is_enabled'),
            'is_test_mode' => $request->has('is_test_mode'),
            'instructions' => $request->instructions,
            'sort_order'   => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.payment-methods.index')->with('success', 'Payment method created.');
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        $paymentMethod->load('settings');
        return view('admin.payment-methods.edit', ['method' => $paymentMethod]);
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|alpha_dash|unique:payment_methods,code,' . $paymentMethod->id,
        ]);

        $paymentMethod->update([
            'name'         => $request->name,
            'code'         => strtolower($request->code),
            'is_enabled'   => $request->has('is_enabled'),
            'is_test_mode' => $request->has('is_test_mode'),
            'instructions' => $request->instructions,
            'sort_order'   => $request->sort_order ?? 0,
        ]);

        // Update existing credentials
        $existing = $request->input('settings', []);   // [id => value]
        $encrypt  = $request->input('encrypt', []);    // [id => 1]

        foreach ($paymentMethod->settings as $s) {
            if (isset($existing[$s->id])) {
                $newValue = $existing[$s->id];

                // If encrypted and empty input → keep current
                if ($s->is_encrypted && $newValue === '') {
                    // skip
                } else {
                    $s->value = !empty($encrypt[$s->id]) || $s->is_encrypted
                        ? ($newValue !== '' ? \Crypt::encryptString($newValue) : null)
                        : $newValue;
                    $s->is_encrypted = !empty($encrypt[$s->id]);
                }
                $s->save();
            }
        }

        // Add new credential fields
        $newKeys   = $request->input('new_keys', []);
        $newValues = $request->input('new_values', []);
        $newTypes  = $request->input('new_types', []);
        $newEncrypt= $request->input('new_encrypt', []);

        foreach ($newKeys as $i => $key) {
            if (!$key) continue;
            $value = $newValues[$i] ?? null;
            $shouldEncrypt = !empty($newEncrypt[$i]);

            $paymentMethod->settings()->updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value !== null && $value !== ''
                        ? ($shouldEncrypt ? \Crypt::encryptString($value) : $value)
                        : null,
                    'is_encrypted' => $shouldEncrypt,
                    'field_type'   => $type,
                    'sort_order'   => 99 + $i,
                ]
            );
        }

        return redirect()->route('admin.payment-methods.index')->with('success', 'Payment method updated.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        // Prevent deletion of core methods
        if (in_array($paymentMethod->code, ['cod'])) {
            return back()->with('error', 'This method cannot be deleted.');
        }

        $paymentMethod->delete();
        return redirect()->route('admin.payment-methods.index')->with('success', 'Payment method deleted.');
    }

    public function toggle(PaymentMethod $paymentMethod)
    {
        $paymentMethod->update(['is_enabled' => !$paymentMethod->is_enabled]);
        return back()->with('success', 'Status updated.');
    }

    public function deleteSetting(\App\Models\PaymentMethodSetting $setting)
    {
        $setting->delete();
        return back()->with('success', 'Credential field removed.');
    }
}