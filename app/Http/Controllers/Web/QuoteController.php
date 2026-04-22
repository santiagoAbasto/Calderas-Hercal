<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\FormSubmission;
use App\Models\Page;
use App\Models\Quote;
use App\Models\Service;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function index(Request $request)
    {
        $page = Page::where('slug', 'presupuesto')
            ->where('is_active', true)
            ->with([
                'sections' => fn ($query) => $query
                    ->where('is_active', true)
                    ->with('activeMedia'),
            ])
            ->firstOrFail();

        $services = Service::where('is_active', true)
            ->orderBy('order')
            ->get(['title', 'slug']);

        $equipments = Equipment::where('is_active', true)
            ->where('is_catalog_root', false)
            ->orderBy('order')
            ->get(['id', 'equipment_category_id', 'title', 'slug']);

        $context = $request->string('context')->toString();
        $slug = $request->string('slug')->toString();

        $subjectType = in_array($context, ['service', 'equipment'], true) ? $context : 'service';
        $selectedItem = null;

        if ($subjectType === 'equipment' && $slug !== '') {
            $selectedItem = $this->resolveEquipmentSelection($equipments, $slug);

            if (! $selectedItem) {
                $selectedItem = Equipment::query()
                    ->where('is_active', true)
                    ->where('is_catalog_root', false)
                    ->whereHas('category', fn ($query) => $query->where('slug', $slug)->where('is_active', true))
                    ->orderBy('order')
                    ->first();
            }
        }

        if (!$selectedItem && $slug !== '') {
            $selectedItem = $services->firstWhere('slug', $slug);
            if ($selectedItem) {
                $subjectType = 'service';
            }
        }

        $options = $subjectType === 'equipment' ? $equipments : $services;

        return view('web.presupuesto.index', [
            'page' => $page,
            'services' => $services,
            'equipments' => $equipments,
            'subjectType' => $subjectType,
            'options' => $options,
            'selectedSlug' => $selectedItem ? ($selectedItem->slug ?: (string) $selectedItem->id) : null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'message' => 'required|string|max:5000',
            'subject_type' => 'required|in:service,equipment',
            'subject_slug' => 'nullable|string|max:255',
        ]);

        $subjectType = $validated['subject_type'];
        $subjectSlug = $validated['subject_slug'] ?? null;
        $subjectName = null;
        $serviceId = null;
        $serviceName = null;

        if ($subjectType === 'service' && $subjectSlug) {
            $service = Service::where('slug', $subjectSlug)->where('is_active', true)->first();
            if ($service) {
                $serviceId = $service->id;
                $serviceName = $service->title;
                $subjectName = $service->title;
            }
        }

        if ($subjectType === 'equipment' && $subjectSlug) {
            $equipment = $this->findEquipmentByIdentifier($subjectSlug);
            if ($equipment) {
                $subjectName = $equipment->title;
                $serviceName = $equipment->title;
                $subjectSlug = $equipment->slug ?: (string) $equipment->id;
            }
        }

        Quote::create([
            'name' => cms_plain_text($validated['name']),
            'last_name' => cms_plain_text($validated['last_name']),
            'email' => cms_email($validated['email']),
            'phone' => cms_phone($validated['phone']),
            'service_id' => $serviceId,
            'service_name' => $serviceName,
            'subject_type' => $subjectType,
            'subject_slug' => $subjectSlug,
            'subject_name' => $subjectName,
            'message' => cms_plain_text($validated['message']),
            'ip_address' => cms_ip_fingerprint($request->ip()),
        ]);

        FormSubmission::create([
            'type' => 'presupuesto',
            'data' => [
                'name' => cms_plain_text($validated['name']),
                'last_name' => cms_plain_text($validated['last_name']),
                'email' => cms_email($validated['email']),
                'phone' => cms_phone($validated['phone']),
                'message' => cms_plain_text($validated['message']),
                'subject_type' => $subjectType,
                'subject_slug' => $subjectSlug,
                'subject_name' => $subjectName,
            ],
            'ip' => cms_ip_fingerprint($request->ip()),
            'user_agent' => substr((string) $request->userAgent(), 0, 1000),
        ]);

        return back()->with('success', 'Tu solicitud fue enviada correctamente. Te contactaremos a la brevedad.');
    }

    protected function resolveEquipmentSelection($equipments, string $identifier): ?Equipment
    {
        return $equipments->first(function (Equipment $equipment) use ($identifier) {
            return ($equipment->slug && $equipment->slug === $identifier)
                || (string) $equipment->id === $identifier;
        });
    }

    protected function findEquipmentByIdentifier(string $identifier): ?Equipment
    {
        return Equipment::query()
            ->where('is_active', true)
            ->where('is_catalog_root', false)
            ->where(function ($query) use ($identifier) {
                $query->where('slug', $identifier);

                if (ctype_digit($identifier)) {
                    $query->orWhere('id', (int) $identifier);
                }
            })
            ->first();
    }
}
