<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::query()
            ->where('slug', '!=', 'normas')
            ->orderBy('id')
            ->get();

        return response()->json($pages);
    }

    public function store(Request $request)
    {
        $request->validate([
            'slug' => 'required|unique:pages,slug',
            'name' => 'required|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        $slug = cms_plain_text($request->slug);

        abort_if($slug === 'normas', 404);

        $page = Page::create([
            'slug' => $slug,
            'name' => cms_plain_text($request->name),
            'meta_title' => cms_plain_text($request->input('meta_title')),
            'meta_description' => cms_plain_text($request->input('meta_description')),
            'meta_keywords' => cms_plain_text($request->input('meta_keywords')),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return response()->json($page);
    }

    public function show(Page $page)
    {
        abort_if($page->slug === 'normas', 404);

        return response()->json($page->load('sections.media'));
    }

    public function update(Request $request, Page $page)
    {
        abort_if($page->slug === 'normas', 404);

        $request->validate([
            'slug' => 'required|unique:pages,slug,' . $page->id,
            'name' => 'required|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        $slug = cms_plain_text($request->slug);

        abort_if($slug === 'normas', 404);

        $page->update([
            'slug' => $slug,
            'name' => cms_plain_text($request->name),
            'meta_title' => cms_plain_text($request->input('meta_title')),
            'meta_description' => cms_plain_text($request->input('meta_description')),
            'meta_keywords' => cms_plain_text($request->input('meta_keywords')),
            'is_active' => $request->boolean('is_active', $page->is_active),
        ]);

        return response()->json($page);
    }

    public function destroy(Page $page)
    {
        abort_if($page->slug === 'normas', 404);

        $page->delete();

        return response()->json(['message' => 'Eliminado']);
    }
}
