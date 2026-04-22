<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Support\CmsOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Projects/Index', [
            'projects' => Project::query()
                ->with('images')
                ->orderBy('order')
                ->get()
                ->map(fn (Project $project) => $this->serializeProject($project)),
        ]);
    }

    public function create(): Response
    {
        $lastOrder = Project::query()
            ->pluck('order')
            ->filter()
            ->sort(fn ($left, $right) => CmsOrder::compare($left, $right))
            ->last();

        return Inertia::render('Admin/Projects/Create', [
            'nextOrder' => CmsOrder::next($lastOrder),
        ]);
    }

    public function edit(Project $project): Response
    {
        return Inertia::render('Admin/Projects/Edit', [
            'project' => $this->serializeProject($project->load('images')),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:projects,slug'],
            'description' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'image', 'max:5120'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['image', 'max:5120'],
            'order' => ['nullable', 'string', 'max:10'],
            'show_in_home' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $title = cms_plain_text($validated['title']);
        $slug = cms_plain_text($validated['slug'] ?? null) ?: Str::slug($title);

        $data = [
            'title' => $title,
            'slug' => $slug,
            'description' => cms_rich_text($validated['description'] ?? null),
            'order' => cms_plain_text($validated['order'] ?? null) ?: $this->nextOrder(),
            'show_in_home' => $request->boolean('show_in_home'),
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('projects', 'public');
        }

        $project = Project::create($data);

        if ($request->hasFile('gallery_images')) {
            $nextImageOrder = 'A';

            foreach ($request->file('gallery_images') as $imageFile) {
                $path = $imageFile->store('projects', 'public');

                ProjectImage::create([
                    'project_id' => $project->id,
                    'file_path' => $path,
                    'order' => $nextImageOrder,
                    'is_active' => true,
                ]);

                $nextImageOrder = CmsOrder::next($nextImageOrder);
            }
        }

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Proyecto creado correctamente.');
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:projects,slug,' . $project->id],
            'description' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'image', 'max:5120'],
            'order' => ['nullable', 'string', 'max:10'],
            'show_in_home' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $title = cms_plain_text($validated['title']);
        $slug = cms_plain_text($validated['slug'] ?? null) ?: Str::slug($title);

        $data = [
            'title' => $title,
            'slug' => $slug,
            'description' => cms_rich_text($validated['description'] ?? null),
            'order' => cms_plain_text($validated['order'] ?? null) ?? $project->order,
            'show_in_home' => $request->boolean('show_in_home'),
            'is_active' => $request->boolean('is_active', $project->is_active),
        ];

        if ($request->hasFile('cover_image')) {
            if ($project->cover_image) {
                Storage::disk('public')->delete($project->cover_image);
            }

            $data['cover_image'] = $request->file('cover_image')->store('projects', 'public');
        }

        $project->update($data);

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Proyecto actualizado correctamente.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        if ($project->cover_image) {
            Storage::disk('public')->delete($project->cover_image);
        }

        foreach ($project->images as $image) {
            if ($image->file_path) {
                Storage::disk('public')->delete($image->file_path);
            }
        }

        $project->delete();

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Proyecto eliminado correctamente.');
    }

    public function toggleHome(Project $project): RedirectResponse
    {
        $project->update([
            'show_in_home' => ! $project->show_in_home,
        ]);

        return back()->with('success', 'Visibilidad en Home actualizada.');
    }

    protected function serializeProject(Project $project): array
    {
        return [
            'id' => $project->id,
            'title' => $project->title,
            'slug' => $project->slug,
            'description' => $project->description,
            'cover_image_url' => $project->cover_image_url,
            'images' => $project->images->map(fn ($image) => [
                'id' => $image->id,
                'image_url' => $image->image_url,
                'order' => $image->order,
                'is_active' => (bool) $image->is_active,
            ])->values(),
            'show_in_home' => (bool) $project->show_in_home,
            'is_active' => (bool) $project->is_active,
            'order' => $project->order,
            'created_at' => optional($project->created_at)->format('d/m/Y H:i'),
        ];
    }

    protected function nextOrder(): string
    {
        $lastOrder = Project::query()
            ->pluck('order')
            ->filter()
            ->sort(fn ($left, $right) => CmsOrder::compare($left, $right))
            ->last();

        return CmsOrder::next($lastOrder);
    }
}
