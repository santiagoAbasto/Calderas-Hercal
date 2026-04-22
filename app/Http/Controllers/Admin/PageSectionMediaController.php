<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageSection;
use App\Models\PageSectionMedia;
use App\Support\CmsSecurity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PageSectionMediaController extends Controller
{
    public function store(Request $request, PageSection $section)
    {
        $validated = $request->validate([
            'page_section_id' => 'nullable|exists:page_sections,id',
            'type'            => 'required|in:image,video,youtube',
            'youtube_url'     => 'nullable|url',
            'title'           => 'nullable|string|max:255',
            'description'     => 'nullable|string',
            'button_text'     => 'nullable|string|max:255',
            'button_url'      => 'nullable|string|max:2048',
            'alt_text'        => 'nullable|string|max:255',
            'order'           => 'nullable|string|max:10',
            'is_active'       => 'nullable|boolean',
        ]);

        $this->validateMediaPayload($request);
        $pageSectionId = $validated['page_section_id'] ?? $section->id;

        $path = null;

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('pages', 'public');
        }

        $media = PageSectionMedia::create([
            'page_section_id' => $pageSectionId,
            'type'            => $request->type,
            'file_path'       => $path,
            'youtube_url'     => $request->youtube_url,
            'title'           => cms_rich_text($request->title),
            'description'     => cms_rich_text($request->description),
            'button_text'     => cms_plain_text($request->button_text),
            'button_url'      => CmsSecurity::sanitizeUrl($request->button_url, allowRelative: true),
            'alt_text'        => cms_plain_text($request->alt_text),
            'order'           => cms_plain_text($request->order) ?? 'A',
            'is_active'       => $request->boolean('is_active', true),
        ]);

        return response()->json($media->append(['file_url', 'youtube_embed']), 201);
    }

    public function update(Request $request, PageSectionMedia $media)
    {
        $request->validate([
            'type'        => 'sometimes|required|in:image,video,youtube',
            'youtube_url' => 'nullable|url',
            'title'       => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:255',
            'button_url'  => 'nullable|string|max:2048',
            'alt_text'    => 'nullable|string|max:255',
            'order'       => 'nullable|string|max:10',
            'is_active'   => 'nullable|boolean',
        ]);

        $this->validateMediaPayload($request, $media);

        $data = $request->only('type', 'youtube_url', 'alt_text', 'order');
        $type = $request->input('type', $media->type);
        $data['title'] = $request->exists('title') ? cms_rich_text($request->title) : $media->title;
        $data['description'] = $request->exists('description') ? cms_rich_text($request->description) : $media->description;
        $data['button_text'] = $request->exists('button_text') ? cms_plain_text($request->button_text) : $media->button_text;
        $data['button_url'] = $request->exists('button_url')
            ? CmsSecurity::sanitizeUrl($request->button_url, allowRelative: true)
            : $media->button_url;
        $data['alt_text'] = array_key_exists('alt_text', $data) ? cms_plain_text($request->alt_text) : $media->alt_text;
        $data['order'] = array_key_exists('order', $data) ? (cms_plain_text($request->order) ?? $media->order) : $media->order;
        $data['is_active'] = $request->exists('is_active') ? $request->boolean('is_active') : $media->is_active;

        if ($request->hasFile('file')) {
            // Reemplazar archivo anterior
            if ($media->file_path) {
                Storage::disk('public')->delete($media->file_path);
            }
            $data['file_path'] = $request->file('file')->store('pages', 'public');
        }

        if ($type === 'youtube') {
            if ($media->file_path) {
                Storage::disk('public')->delete($media->file_path);
            }

            $data['file_path'] = null;
        } else {
            $data['youtube_url'] = null;
        }

        $media->update($data);

        return response()->json($media->append(['file_url', 'youtube_embed']));
    }

    public function destroy(PageSectionMedia $media)
    {
        if ($media->file_path) {
            Storage::disk('public')->delete($media->file_path);
        }

        $media->delete();

        return response()->json(['ok' => true]);
    }

    protected function validateMediaPayload(Request $request, ?PageSectionMedia $media = null): void
    {
        $type = $request->input('type', $media?->type);

        if ($type === 'youtube') {
            if ($request->hasFile('file')) {
                throw ValidationException::withMessages([
                    'file' => 'No se puede subir un archivo cuando el tipo es YouTube.',
                ]);
            }

            if (! $request->filled('youtube_url') && $media === null) {
                throw ValidationException::withMessages([
                    'youtube_url' => 'Debes indicar una URL de YouTube.',
                ]);
            }

            return;
        }

        if (! in_array($type, ['image', 'video'], true)) {
            return;
        }

        if ($request->filled('youtube_url')) {
            throw ValidationException::withMessages([
                'youtube_url' => 'La URL de YouTube solo se admite cuando el tipo es YouTube.',
            ]);
        }

        $mustUploadFile = $media === null || $request->has('type') && $type !== $media->type;

        if (! $request->hasFile('file')) {
            if ($mustUploadFile) {
                throw ValidationException::withMessages([
                    'file' => 'Debes subir un archivo valido para este tipo de media.',
                ]);
            }

            return;
        }

        if ($type === 'image') {
            $request->validate([
                'file' => [
                    'image',
                    'mimes:' . implode(',', config('security.uploads.page_media.image_mimes', ['jpg', 'jpeg', 'png', 'webp', 'gif'])),
                    'max:' . (int) config('security.uploads.page_media.image_max_kb', 5120),
                ],
            ]);

            return;
        }

        $request->validate([
            'file' => [
                'file',
                'mimetypes:' . implode(',', config('security.uploads.page_media.video_mimetypes', ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime'])),
                'max:' . (int) config('security.uploads.page_media.video_max_kb', 20480),
            ],
        ]);
    }
}
