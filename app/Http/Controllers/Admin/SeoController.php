<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Support\CmsSecurity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class SeoController extends Controller
{
    protected const PAGE_DEFINITIONS = [
        ['slug' => 'home', 'label' => 'Home', 'default_name' => 'Home'],
        ['slug' => 'nosotros', 'label' => 'Nosotros', 'default_name' => 'Nosotros'],
        ['slug' => 'servicios', 'label' => 'Servicios', 'default_name' => 'Servicios'],
        ['slug' => 'equipos', 'label' => 'Equipos', 'default_name' => 'Equipos'],
        ['slug' => 'trabajos-realizados', 'label' => 'Trabajos realizados', 'default_name' => 'Trabajos realizados'],
        ['slug' => 'clientes', 'label' => 'Clientes', 'default_name' => 'Clientes'],
        ['slug' => 'contacto', 'label' => 'Contacto', 'default_name' => 'Contacto'],
        ['slug' => 'presupuesto', 'label' => 'Presupuesto', 'default_name' => 'Presupuesto'],
    ];

    public function index(): Response
    {
        $pages = collect(self::PAGE_DEFINITIONS)->map(function (array $definition) {
            $page = Page::query()->firstOrCreate(
                ['slug' => $definition['slug']],
                ['name' => $definition['default_name'], 'is_active' => true]
            );

            return [
                'id' => $page->id,
                'slug' => $page->slug,
                'label' => $definition['label'],
                'meta_title' => $page->meta_title,
                'meta_description' => $page->meta_description,
                'meta_keywords' => $page->meta_keywords,
                'canonical_url' => $page->canonical_url,
                'robots' => $page->robots ?: 'index,follow',
                'og_image_url' => $page->og_image_url,
            ];
        })->values();

        return Inertia::render('Admin/Seo/Index', [
            'pages' => $pages,
        ]);
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $validated = $request->validate([
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'meta_keywords' => ['nullable', 'string', 'max:1000'],
            'canonical_url' => ['nullable', 'string', 'max:2048'],
            'robots' => ['nullable', Rule::in([
                'index,follow',
                'noindex,follow',
                'index,nofollow',
                'noindex,nofollow',
            ])],
            'og_image' => ['nullable', 'image', 'max:5120'],
            'remove_og_image' => ['nullable', 'boolean'],
        ]);

        $data = [
            'meta_title' => cms_plain_text($validated['meta_title'] ?? null),
            'meta_description' => cms_plain_text($validated['meta_description'] ?? null),
            'meta_keywords' => cms_plain_text($validated['meta_keywords'] ?? null),
            'canonical_url' => CmsSecurity::sanitizeUrl($validated['canonical_url'] ?? null, allowRelative: false),
            'robots' => cms_plain_text($validated['robots'] ?? 'index,follow') ?: 'index,follow',
        ];

        if ($request->boolean('remove_og_image') && $page->og_image) {
            Storage::disk('public')->delete($page->og_image);
            $data['og_image'] = null;
        }

        if ($request->hasFile('og_image')) {
            if ($page->og_image) {
                Storage::disk('public')->delete($page->og_image);
            }

            $data['og_image'] = $request->file('og_image')->store('seo', 'public');
        }

        $page->update($data);

        return to_route('admin.seo.index')->with('success', 'SEO guardado correctamente.');
    }
}
