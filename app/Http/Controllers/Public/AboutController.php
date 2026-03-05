<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use Inertia\Inertia;
use Inertia\Response;

class AboutController extends Controller
{
    public function index(): Response
    {
        $settings = StoreSetting::all_settings();
        return Inertia::render('About', compact('settings'));
    }
}
