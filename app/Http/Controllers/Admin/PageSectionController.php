<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Http\Request;

class PageSectionController extends Controller
{
    protected const RICH_TEXT_META_KEYS = [
        'description_extra',
        'button_text',
        'mission_title',
        'mission_description',
        'vision_title',
        'vision_description',
        'values_title',
        'values_description',
    ];

    public function index(Page $page)
    {
        $sections = $page->sections()->with('media')->get();

        return response()->json($sections);
    }

    public function store(Request $request)
    {
        $request->validate([
            'page_id'     => 'required|exists:pages,id',
            'section_key' => 'required|string|max:100',
            'title'       => 'nullable|string',
            'subtitle'    => 'nullable|string',
            'description' => 'nullable|string',
            'meta'        => 'nullable|array',
            'order'       => 'nullable|string|max:10',
            'is_active'   => 'nullable|boolean',
        ]);

        $section = PageSection::create([
            'page_id'     => $request->page_id,
            'section_key' => cms_plain_text($request->section_key),
            'title'       => cms_rich_text($request->title),
            'subtitle'    => cms_rich_text($request->subtitle),
            'description' => cms_rich_text($request->description),
            'meta'        => $this->sanitizeMeta($request->meta ?? []),
            'order'       => cms_plain_text($request->order) ?? 'A',
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return response()->json($section, 201);
    }

    public function update(Request $request, PageSection $section)
    {
        $request->validate([
            'section_key' => 'sometimes|required|string|max:100',
            'title'       => 'nullable|string',
            'subtitle'    => 'nullable|string',
            'description' => 'nullable|string',
            'meta'        => 'nullable|array',
            'order'       => 'nullable|string|max:10',
            'is_active'   => 'nullable|boolean',
        ]);

        $section->update([
            'section_key' => $request->has('section_key') ? cms_plain_text($request->section_key) : $section->section_key,
            'title'       => $request->exists('title') ? cms_rich_text($request->title) : $section->title,
            'subtitle'    => $request->exists('subtitle') ? cms_rich_text($request->subtitle) : $section->subtitle,
            'description' => $request->exists('description') ? cms_rich_text($request->description) : $section->description,
            'meta'        => $request->exists('meta') ? $this->sanitizeMeta($request->meta ?? []) : $section->meta,
            'order'       => $request->exists('order') ? (cms_plain_text($request->order) ?? $section->order) : $section->order,
            'is_active'   => $request->exists('is_active') ? $request->boolean('is_active') : $section->is_active,
        ]);

        return response()->json($section);
    }

    public function destroy(PageSection $section)
    {
        $section->delete();

        return response()->json(['ok' => true]);
    }

    protected function sanitizeMeta(array $meta): array
    {
        $sanitized = [];

        foreach ($meta as $key => $value) {
            if (in_array($key, self::RICH_TEXT_META_KEYS, true) && is_string($value)) {
                $sanitized[$key] = cms_rich_text($value);
                continue;
            }

            $sanitized[$key] = cms_sanitize_array($value);
        }

        return $sanitized;
    }
}
