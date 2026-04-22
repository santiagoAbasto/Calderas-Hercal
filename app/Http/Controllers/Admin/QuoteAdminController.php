<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quote;

class QuoteAdminController extends Controller
{
    public function index()
    {
        $quotes = Quote::with('service')
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json($quotes);
    }

    public function show(Quote $quote)
    {
        // Marcar como leído la primera vez que se abre
        if (is_null($quote->read_at)) {
            $quote->update(['read_at' => now()]);
        }

        return response()->json($quote->load('service'));
    }

    public function destroy(Quote $quote)
    {
        $quote->delete();

        return response()->json(['ok' => true]);
    }
}
