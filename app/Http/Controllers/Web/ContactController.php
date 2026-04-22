<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\FormSubmission;
use App\Models\Page;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $page = Page::where('slug', 'contacto')
            ->where('is_active', true)
            ->with([
                'sections' => fn ($query) => $query
                    ->where('is_active', true)
                    ->with('activeMedia'),
            ])
            ->first();

        $contentSection = $page?->sections->where('section_key', 'contacto_content')->first();
        $contentMeta = $contentSection?->meta ?? [];

        $contactEmails = $contentMeta['contact_emails'] ?? [];
        $contactPhones = $contentMeta['contact_phones'] ?? [];
        $contactEmails = collect($contactEmails)
            ->map(function ($email) {
                if (is_array($email)) {
                    return trim((string) ($email['email'] ?? $email['value'] ?? ''));
                }

                return trim((string) $email);
            })
            ->filter()
            ->values();

        $contactPhones = collect(is_array($contactPhones) ? $contactPhones : [])
            ->map(function ($phone) {
                if (is_array($phone)) {
                    return [
                        'label' => trim((string) ($phone['label'] ?? '')),
                        'number' => trim((string) ($phone['number'] ?? $phone['value'] ?? '')),
                    ];
                }

                return [
                    'label' => '',
                    'number' => trim((string) $phone),
                ];
            })
            ->filter(fn ($phone) => !empty($phone['number']))
            ->values();

        return view('web.contact.index', compact(
            'page',
            'contactEmails',
            'contactPhones'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email'     => 'required|email|max:255',
            'phone'     => 'required|string|max:50',
            'message'   => 'required|string|max:5000',
        ]);

        Contact::create([
            'name'       => cms_plain_text($validated['name']),
            'last_name'  => cms_plain_text($validated['last_name']),
            'email'      => cms_email($validated['email']),
            'phone'      => cms_phone($validated['phone']),
            'message'    => cms_plain_text($validated['message']),
            'ip_address' => cms_ip_fingerprint($request->ip()),
        ]);

        FormSubmission::create([
            'type' => 'contacto',
            'data' => [
                'name' => cms_plain_text($validated['name']),
                'last_name' => cms_plain_text($validated['last_name']),
                'email' => cms_email($validated['email']),
                'phone' => cms_phone($validated['phone']),
                'message' => cms_plain_text($validated['message']),
            ],
            'ip' => cms_ip_fingerprint($request->ip()),
            'user_agent' => substr((string) $request->userAgent(), 0, 1000),
        ]);

        return back()->with('success', 'Tu mensaje fue enviado correctamente. Te responderemos a la brevedad.');
    }
}
