<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Support\CmsOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Services/Index', [
            'services' => Service::query()
                ->orderBy('order')
                ->get()
                ->map(fn (Service $service) => $this->serializeService($service)),
        ]);
    }

    public function create(): Response
    {
        $lastOrder = Service::query()
            ->pluck('order')
            ->filter()
            ->sort(fn ($left, $right) => CmsOrder::compare($left, $right))
            ->last();

        return Inertia::render('Admin/Services/Create', [
            'nextOrder' => CmsOrder::next($lastOrder),
        ]);
    }

    public function edit(Service $service): Response
    {
        return Inertia::render('Admin/Services/Edit', [
            'service' => $this->serializeService($service),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:services,slug'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:5000'],
            'order' => ['nullable', 'string', 'max:10'],
            'show_in_home' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $title = cms_plain_text($validated['title']);
        $slug = cms_plain_text($validated['slug'] ?? null) ?: Str::slug($title);

        $data = [
            'title' => $title,
            'slug' => $slug,
            'subtitle' => cms_plain_text($validated['subtitle'] ?? null),
            'description' => cms_rich_text($validated['description'] ?? null),
            'order' => cms_plain_text($validated['order'] ?? null) ?: $this->nextOrder(),
            'show_in_home' => $request->boolean('show_in_home'),
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        Service::create($data);

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Servicio creado correctamente.');
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:services,slug,' . $service->id],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:5000'],
            'order' => ['nullable', 'string', 'max:10'],
            'show_in_home' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $title = cms_plain_text($validated['title']);
        $slug = cms_plain_text($validated['slug'] ?? null) ?: Str::slug($title);

        $data = [
            'title' => $title,
            'slug' => $slug,
            'subtitle' => cms_plain_text($validated['subtitle'] ?? null),
            'description' => cms_rich_text($validated['description'] ?? null),
            'order' => cms_plain_text($validated['order'] ?? null) ?? $service->order,
            'show_in_home' => $request->boolean('show_in_home'),
            'is_active' => $request->boolean('is_active', $service->is_active),
        ];

        if ($request->hasFile('image')) {
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }

            $data['image'] = $request->file('image')->store('services', 'public');
        }

        $service->update($data);

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Servicio actualizado correctamente.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        if ($service->image) {
            Storage::disk('public')->delete($service->image);
        }

        $service->delete();

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Servicio eliminado correctamente.');
    }

    public function toggleHome(Service $service): RedirectResponse
    {
        $service->update([
            'show_in_home' => ! $service->show_in_home,
        ]);

        return back()->with('success', 'Visibilidad en Home actualizada.');
    }

    protected function serializeService(Service $service): array
    {
        return [
            'id' => $service->id,
            'title' => $service->title,
            'slug' => $service->slug,
            'subtitle' => $service->subtitle,
            'description' => $service->description,
            'image_url' => $service->image_url,
            'order' => $service->order,
            'show_in_home' => (bool) $service->show_in_home,
            'is_active' => (bool) $service->is_active,
            'created_at' => optional($service->created_at)->format('d/m/Y H:i'),
        ];
    }

    protected function nextOrder(): string
    {
        $lastOrder = Service::query()
            ->pluck('order')
            ->filter()
            ->sort(fn ($left, $right) => CmsOrder::compare($left, $right))
            ->last();

        return CmsOrder::next($lastOrder);
    }
}
