<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Support\CmsOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class EquipmentController extends Controller
{
    public function index(): Response
    {
        $categories = EquipmentCategory::query()
            ->with(['equipments' => fn ($query) => $query->orderBy('order')])
            ->get();

        $categories = $categories
            ->sort(fn (EquipmentCategory $left, EquipmentCategory $right) => CmsOrder::compare($left->order, $right->order))
            ->values();

        $coverIds = $categories
            ->map(fn (EquipmentCategory $category) => $this->resolveDeclaredCategoryCover($category)?->id)
            ->filter()
            ->values();

        return Inertia::render('Admin/Equipments/Index', [
            'equipments' => Equipment::query()
                ->with(['category', 'images'])
                ->when($coverIds->isNotEmpty(), fn ($query) => $query->whereNotIn('id', $coverIds->all()))
                ->get()
                ->sort(function (Equipment $left, Equipment $right) {
                    $categoryOrder = CmsOrder::compare($left->category?->order, $right->category?->order);

                    if ($categoryOrder !== 0) {
                        return $categoryOrder;
                    }

                    return CmsOrder::compare($left->order, $right->order);
                })
                ->values()
                ->map(fn (Equipment $equipment) => $this->serializeEquipment($equipment)),
            'categories' => $this->serializeCategories($categories),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Equipments/Create', [
            'categories' => $this->serializeCategories(),
            'nextOrder' => CmsOrder::labelForIndex(Equipment::query()->where('is_catalog_root', false)->count()),
        ]);
    }

    public function edit(Equipment $equipment): Response
    {
        return Inertia::render('Admin/Equipments/Edit', [
            'equipment' => $this->serializeEquipment($equipment->load(['category', 'images'])),
            'categories' => $this->serializeCategories(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:equipments,slug'],
            'equipment_category_id' => ['required', 'exists:equipment_categories,id'],
            'description' => ['nullable', 'string'],
            'main_image' => ['nullable', 'image', 'max:5120'],
            'features' => ['nullable', 'array'],
            'features.*' => ['nullable', 'string', 'max:255'],
            'show_in_home' => ['nullable', 'boolean'],
            'order' => ['nullable', 'string', 'max:10'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data = [
            'equipment_category_id' => $validated['equipment_category_id'],
            'title' => cms_plain_text($validated['title']),
            'slug' => cms_plain_text($validated['slug'] ?? null),
            'description' => cms_rich_text($validated['description'] ?? null),
            'features' => collect($validated['features'] ?? [])
                ->map(fn ($feature) => cms_plain_text($feature))
                ->filter()
                ->values()
                ->all(),
            'is_catalog_root' => false,
            'show_in_home' => $request->boolean('show_in_home'),
            'order' => cms_plain_text($validated['order'] ?? null) ?? CmsOrder::labelForIndex(Equipment::query()->where('is_catalog_root', false)->count()),
            'is_active' => $request->boolean('is_active', true),
        ];

        DB::transaction(function () use ($request, $data): void {
            if ($request->hasFile('main_image')) {
                $data['main_image'] = $request->file('main_image')->store('equipments', 'public');
            }

            Equipment::create($data);
        });

        return redirect()
            ->route('admin.equipments.index')
            ->with('success', 'Equipo creado correctamente.');
    }

    public function update(Request $request, Equipment $equipment): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:equipments,slug,' . $equipment->id],
            'equipment_category_id' => ['required', 'exists:equipment_categories,id'],
            'description' => ['nullable', 'string'],
            'main_image' => ['nullable', 'image', 'max:5120'],
            'features' => ['nullable', 'array'],
            'features.*' => ['nullable', 'string', 'max:255'],
            'show_in_home' => ['nullable', 'boolean'],
            'order' => ['nullable', 'string', 'max:10'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data = [
            'equipment_category_id' => $validated['equipment_category_id'],
            'title' => cms_plain_text($validated['title']),
            'slug' => cms_plain_text($validated['slug'] ?? null),
            'description' => cms_rich_text($validated['description'] ?? null),
            'features' => collect($validated['features'] ?? [])
                ->map(fn ($feature) => cms_plain_text($feature))
                ->filter()
                ->values()
                ->all(),
            'is_catalog_root' => $equipment->is_catalog_root,
            'show_in_home' => $request->boolean('show_in_home'),
            'order' => cms_plain_text($validated['order'] ?? null) ?? $equipment->order,
            'is_active' => $request->boolean('is_active', $equipment->is_active),
        ];

        DB::transaction(function () use ($request, $equipment, $data): void {
            if ($request->hasFile('main_image')) {
                if ($equipment->main_image) {
                    Storage::disk('public')->delete($equipment->main_image);
                }

                $data['main_image'] = $request->file('main_image')->store('equipments', 'public');
            }
            $equipment->update($data);
        });

        return redirect()
            ->route('admin.equipments.index')
            ->with('success', 'Equipo actualizado correctamente.');
    }

    public function destroy(Equipment $equipment): RedirectResponse
    {
        if ($equipment->main_image) {
            Storage::disk('public')->delete($equipment->main_image);
        }

        foreach ($equipment->images as $image) {
            if ($image->file_path) {
                Storage::disk('public')->delete($image->file_path);
            }
        }

        $equipment->delete();

        return redirect()
            ->route('admin.equipments.index')
            ->with('success', 'Equipo eliminado correctamente.');
    }

    public function toggleHome(Equipment $equipment): RedirectResponse
    {
        $equipment->update([
            'show_in_home' => ! $equipment->show_in_home,
        ]);

        return back()->with('success', 'Visibilidad en Home actualizada.');
    }

    protected function serializeEquipment(Equipment $equipment): array
    {
        return [
            'id' => $equipment->id,
            'title' => $equipment->title,
            'slug' => $equipment->slug,
            'description' => $equipment->description,
            'main_image_url' => $equipment->main_image_url,
            'features' => $equipment->features ?? [],
            'equipment_category_id' => $equipment->equipment_category_id,
            'category' => $equipment->category ? [
                'id' => $equipment->category->id,
                'name' => $equipment->category->name,
            ] : null,
            'images' => $equipment->images->map(fn ($image) => [
                'id' => $image->id,
                'image_url' => $image->image_url,
                'order' => $image->order,
                'is_active' => (bool) $image->is_active,
            ])->values(),
            'show_in_home' => (bool) $equipment->show_in_home,
            'is_active' => (bool) $equipment->is_active,
            'order' => $equipment->order,
            'created_at' => optional($equipment->created_at)->format('d/m/Y H:i'),
        ];
    }

    protected function serializeCategories($categories = null): array
    {
        $categories ??= EquipmentCategory::query()
            ->get();
        
        return collect($categories)
            ->sort(fn (EquipmentCategory $left, EquipmentCategory $right) => CmsOrder::compare($left->order, $right->order))
            ->map(fn (EquipmentCategory $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'order' => $category->order,
                'is_active' => (bool) $category->is_active,
            ])
            ->all();
    }

    protected function resolveCategoryCover(EquipmentCategory $category): ?Equipment
    {
        $equipments = $category->equipments;

        if ($equipments->isEmpty()) {
            return null;
        }

        return $equipments->firstWhere('slug', $category->slug)
            ?? $equipments->firstWhere('title', $category->name)
            ?? $equipments->firstWhere('is_catalog_root', true)
            ?? $equipments->first();
    }

    protected function resolveDeclaredCategoryCover(EquipmentCategory $category): ?Equipment
    {
        $equipments = $category->equipments;

        if ($equipments->isEmpty()) {
            return null;
        }

        return $equipments->firstWhere('slug', $category->slug)
            ?? $equipments->firstWhere('title', $category->name)
            ?? $equipments->firstWhere('is_catalog_root', true);
    }
}
