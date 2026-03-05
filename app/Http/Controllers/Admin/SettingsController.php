<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Settings', [
            'settings' => StoreSetting::all_settings(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'store_name'     => 'required|string|max:255',
            'store_tagline'  => 'nullable|string|max:255',
            'address'        => 'nullable|string',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email',
            'open_time'      => 'nullable|string',
            'close_time'     => 'nullable|string',
            'delivery_free_above' => 'nullable|numeric|min:0',
            'delivery_charge' => 'nullable|numeric|min:0',
            'about_text'     => 'nullable|string',
        ]);

        foreach ($data as $key => $value) {
            StoreSetting::set($key, $value);
        }

        return back()->with('success', 'Settings saved successfully.');
    }
}
