<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Page;

class NosotrosController extends Controller
{
    public function index()
    {
        $page = Page::where('slug', 'nosotros')
            ->where('is_active', true)
            ->with([
                'sections' => fn ($q) => $q
                    ->where('is_active', true)
                    ->with('activeMedia'),
            ])
            ->firstOrFail();

        return view('web.nosotros.index', compact('page'));
    }
}
