<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CmsPageEditorController extends Controller
{
    public function edit(Request $request, string $slug): Response
    {
        abort_if($slug === 'normas', 404);

        $page = Page::query()
            ->where('slug', $slug)
            ->with(['sections.media'])
            ->firstOrCreate(
                ['slug' => $slug],
                ['name' => ucfirst(str_replace('-', ' ', $slug)), 'is_active' => true]
            );

        $labels = [
            'home' => 'Home',
            'nosotros' => 'Nosotros',
            'clientes' => 'Clientes',
            'contacto' => 'Contacto',
            'presupuesto' => 'Presupuesto',
        ];

        return Inertia::render('Admin/CMS/PageEditor', [
            'page' => $this->serializePage($page->load(['sections.media'])),
            'pageLabel' => $labels[$slug] ?? ucfirst(str_replace('-', ' ', $slug)),
        ]);
    }

    protected function serializePage(Page $page): array
    {
        return [
            'id' => $page->id,
            'slug' => $page->slug,
            'name' => $page->name,
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
            'meta_keywords' => $page->meta_keywords,
            'is_active' => (bool) $page->is_active,
            'sections' => $page->sections
                ->sortBy('order')
                ->values()
                ->map(fn ($section) => [
                    'id' => $section->id,
                    'section_key' => $section->section_key,
                    'title' => $section->title,
                    'subtitle' => $section->subtitle,
                    'description' => $section->description,
                    'meta' => $section->meta ?? [],
                    'order' => $section->order,
                    'is_active' => (bool) $section->is_active,
                    'media' => $section->media
                        ->sortBy('order')
                        ->values()
                        ->map(fn ($media) => [
                            'id' => $media->id,
                            'type' => $media->type,
                            'file_path' => $media->file_path,
                            'file_url' => $media->file_url,
                            'youtube_url' => $media->youtube_url,
                            'youtube_embed' => $media->youtube_embed,
                            'alt_text' => $media->alt_text,
                            'order' => $media->order,
                            'is_active' => (bool) $media->is_active,
                        ]),
                ]),
        ];
    }
}
