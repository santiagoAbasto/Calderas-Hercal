<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\ClientType;

class ClientsController extends Controller
{
    public function index()
    {
        $page = Page::where('slug', 'clientes')
            ->where('is_active', true)
            ->with([
                'sections' => fn ($query) => $query
                    ->where('is_active', true)
                    ->with('activeMedia'),
            ])
            ->first();

        $clients = ClientType::where('is_active', true)
            ->orderBy('order')
            ->get();

        return view('web.clients.index', compact('page', 'clients'));
    }
}
