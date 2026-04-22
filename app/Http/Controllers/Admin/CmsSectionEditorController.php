<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientType;
use App\Models\Page;
use App\Models\PageSection;
use App\Support\CmsOrder;
use App\Support\CmsSectionBlueprint;
use Inertia\Inertia;
use Inertia\Response;

class CmsSectionEditorController extends Controller
{
    public function edit(string $slug, string $editor): Response
    {
        $blueprint = CmsSectionBlueprint::find($slug, $editor);

        abort_unless($blueprint, 404);

        $page = Page::query()
            ->where('slug', $slug)
            ->with(['sections.media'])
            ->firstOrCreate(
                ['slug' => $slug],
                ['name' => $blueprint['page_label'], 'is_active' => true]
            );

        $section = null;

        if (! empty($blueprint['section_key'])) {
            $section = $page->sections
                ->firstWhere('section_key', $blueprint['section_key']);
        }

        $serializedSection = $this->serializeSection($section, $blueprint);

        if ($slug === 'nosotros' && $editor === 'contenido') {
            $serializedSection['meta'] = array_merge(
                $this->legacyNosotrosMeta($page),
                $serializedSection['meta'] ?? [],
            );
        }

        return Inertia::render('Admin/CMS/SectionEditor', [
            'editor' => $blueprint,
            'page' => $this->serializePage($page),
            'section' => $serializedSection,
            'clientTypes' => $slug === 'clientes' && $editor === 'contenido'
                ? $this->serializeClientTypes()
                : [],
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
        ];
    }

    protected function serializeSection(?PageSection $section, array $blueprint): array
    {
        if (! $section) {
            return [
                'id' => null,
                'section_key' => $blueprint['section_key'],
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'meta' => [],
                'order' => $blueprint['section_defaults']['order'] ?? 'A',
                'is_active' => true,
                'media' => [],
            ];
        }

        $richTextFields = collect($blueprint['fields'] ?? [])
            ->filter(fn ($field) => ($field['type'] ?? null) === 'richtext')
            ->pluck('key')
            ->all();

        $plainTextFields = collect($blueprint['fields'] ?? [])
            ->filter(fn ($field) => ($field['type'] ?? null) !== 'richtext')
            ->pluck('key')
            ->all();

        $richTextMetaFields = collect($blueprint['meta_fields'] ?? [])
            ->filter(fn ($field) => ($field['type'] ?? null) === 'richtext')
            ->pluck('key')
            ->all();

        $plainTextMetaFields = collect($blueprint['meta_fields'] ?? [])
            ->filter(fn ($field) => ($field['type'] ?? null) !== 'richtext')
            ->pluck('key')
            ->all();

        $meta = $section->meta ?? [];

        foreach ($richTextMetaFields as $key) {
            if (array_key_exists($key, $meta)) {
                $meta[$key] = $this->normalizeRichTextForEditor($meta[$key]);
            }
        }

        foreach ($plainTextMetaFields as $key) {
            if (array_key_exists($key, $meta) && is_string($meta[$key])) {
                $meta[$key] = $this->normalizePlainTextForEditor($meta[$key]);
            }
        }

        return [
            'id' => $section->id,
            'section_key' => $section->section_key,
            'title' => in_array('title', $richTextFields, true)
                ? $this->normalizeRichTextForEditor($section->title)
                : (in_array('title', $plainTextFields, true) ? $this->normalizePlainTextForEditor($section->title) : $section->title),
            'subtitle' => in_array('subtitle', $richTextFields, true)
                ? $this->normalizeRichTextForEditor($section->subtitle)
                : (in_array('subtitle', $plainTextFields, true) ? $this->normalizePlainTextForEditor($section->subtitle) : $section->subtitle),
            'description' => in_array('description', $richTextFields, true)
                ? $this->normalizeRichTextForEditor($section->description)
                : (in_array('description', $plainTextFields, true) ? $this->normalizePlainTextForEditor($section->description) : $section->description),
            'meta' => $meta,
            'order' => $section->order,
            'is_active' => (bool) $section->is_active,
            'media' => $section->media
                ->sort(static fn ($left, $right) => CmsOrder::compare($left->order, $right->order))
                ->values()
                ->map(fn ($media) => [
                    'id' => $media->id,
                    'type' => $media->type,
                    'title' => $media->title,
                    'description' => $media->description,
                    'button_text' => $media->button_text,
                    'button_url' => $media->button_url,
                    'file_url' => $media->file_url,
                    'youtube_url' => $media->youtube_url,
                    'youtube_embed' => $media->youtube_embed,
                    'alt_text' => $media->alt_text,
                    'order' => $media->order,
                    'is_active' => (bool) $media->is_active,
                ]),
        ];
    }

    protected function normalizeRichTextForEditor(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if ($value !== strip_tags($value)) {
            return $value;
        }

        $paragraphs = preg_split('/\r\n\r\n|\n\n|\r\r/', $value) ?: [];
        $paragraphs = array_map('trim', $paragraphs);
        $paragraphs = array_filter($paragraphs, fn ($paragraph) => $paragraph !== '');

        if ($paragraphs === []) {
            return $value;
        }

        return collect($paragraphs)
            ->map(fn ($paragraph) => '<p>' . nl2br(e($paragraph), false) . '</p>')
            ->implode('');
    }

    protected function normalizePlainTextForEditor(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        return trim(strip_tags($value));
    }

    protected function legacyNosotrosMeta(Page $page): array
    {
        $mission = $page->sections->firstWhere('section_key', 'nosotros_mision');
        $vision = $page->sections->firstWhere('section_key', 'nosotros_vision');
        $values = $page->sections->firstWhere('section_key', 'nosotros_valores');

        return [
            'mission_title' => $mission?->title ?? '',
            'mission_description' => $mission?->description ?? '',
            'vision_title' => $vision?->title ?? '',
            'vision_description' => $vision?->description ?? '',
            'values_title' => $values?->title ?? '',
            'values_description' => $values?->description ?? '',
        ];
    }

    protected function serializeClientTypes(): array
    {
        return ClientType::query()
            ->orderBy('order')
            ->get()
            ->map(fn (ClientType $client) => [
                'id' => $client->id,
                'name' => $client->name,
                'slug' => $client->slug,
                'icon_url' => $client->icon_url,
                'icon_width' => $client->icon_width,
                'icon_height' => $client->icon_height,
                'order' => $client->order,
                'is_active' => (bool) $client->is_active,
            ])
            ->values()
            ->all();
    }
}
