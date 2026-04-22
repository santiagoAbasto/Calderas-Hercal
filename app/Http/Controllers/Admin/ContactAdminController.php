<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;

class ContactAdminController extends Controller
{
    public function index()
    {
        $contacts = Contact::orderByDesc('created_at')
            ->paginate(20);

        return response()->json($contacts);
    }

    public function show(Contact $contact)
    {
        // Marcar como leído la primera vez que se abre
        if (is_null($contact->read_at)) {
            $contact->update(['read_at' => now()]);
        }

        return response()->json($contact);
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return response()->json(['ok' => true]);
    }
}
