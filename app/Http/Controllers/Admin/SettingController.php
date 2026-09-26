<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::orderBy('group')->orderBy('key')->get()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    }

        public function update(Request $request)
        {
            foreach ($request->except('_token', '_method') as $key => $value) {
                Setting::where('key', $key)->update(['value' => $value ?? '']);
            }

            // Handle file uploads
            foreach (['logo', 'favicon'] as $fileKey) {
                if ($request->hasFile($fileKey)) {
                    $path = $request->file($fileKey)->store('branding', 'public');
                    Setting::where('key', $fileKey)->update(['value' => $path]);
                }
            }

            return back()->with('success', 'Settings saved.');
        }


    
}