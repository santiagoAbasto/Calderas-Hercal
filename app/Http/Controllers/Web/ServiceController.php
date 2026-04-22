<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $page = Page::where('slug', 'servicios')
            ->with(['sections.media'])
            ->first();

        $services = Service::where('is_active', true)
            ->orderBy('order')
            ->get();

        return view('web.services.index', compact('page', 'services'));
    }

    public function show($slug)
    {
        $page = Page::where('slug', 'servicios')
            ->with(['sections.media'])
            ->first();

        $service = Service::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('web.services.show', compact('page', 'service'));
    }
}
