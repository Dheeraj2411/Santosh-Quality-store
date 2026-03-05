<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function index(): Response
    {
        $settings = StoreSetting::all_settings();
        return Inertia::render('Contact', compact('settings'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10|max:2000',
        ]);

        // In production you'd send an email here
        return back()->with('success', 'Thank you! We will get back to you shortly.');
    }
}
