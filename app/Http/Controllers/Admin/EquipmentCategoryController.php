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
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class EquipmentCategoryController extends Controller
{
    public function index(): Response
    {
        $categories = EquipmentCategory::with(['equipments' => fn ($query) => $query->orderBy('order')])
            ->get();

        $categories = $categories
            ->sort(fn (EquipmentCategory $left, EquipmentCategory $right) => CmsOrder::compare($left->order, $right->order))
            ->values();

        return Inertia::render('Admin/EquipmentCategories/Index', [
            'categories' => $categories->map(fn (EquipmentCategory $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'order' => $category->order,
                'show_in_home' => (bool) $category->show_in_home,
                'is_active' => (bool) $category->is_active,
                'equipments_count' => $this->countProducts($category),
                'cover_equipment_id' => $this->resolveCover($category)?->id,
                'products' => $category->equipments->map(fn (Equipment $equipment) => [
                    'id' => $equipment->id,
                    'title' => $equipment->title,
                ])->values(),
                'cover' => $this->serializeCover($category),
            ])->values(),
            'nextOrder' => CmsOrder::labelForIndex($categories->count()),
        ]);
    }

    public function edit(EquipmentCategory $equipmentCategory): Response
    {
        $equipmentCategory->load(['equipments' => fn ($query) => $query->orderBy('order')]);

        return Inertia::render('Admin/EquipmentCategories/Edit', [
            'category' => [
                'id' => $equipmentCategory->id,
                'name' => $equipmentCategory->name,
                'slug' => $equipmentCategory->slug,
                'order' => $equipmentCategory->order,
                'show_in_home' => (bool) $equipmentCategory->show_in_home,
                'is_active' => (bool) $equipmentCategory->is_active,
                'cover_equipment_id' => $this->resolveCover($equipmentCategory)?->id,
                'cover_image_url' => $this->resolveCover($equipmentCategory)?->main_image_url,
                'products' => $equipmentCategory->equipments->map(fn (Equipment $equipment) => [
                    'id' => $equipment->id,
                    'title' => $equipment->title,
                    'is_catalog_root' => (bool) $equipment->is_catalog_root,
                ])->values(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'slug'  => 'nullable|string|max:255|unique:equipment_categories,slug',
            'order' => 'nullable|string|max:10',
            'cover_image' => 'nullable|image|max:5120',
            'show_in_home' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $name = cms_plain_text($request->name);
        $slug = $this->resolveSlug($request->input('slug'), $name);
        $order = cms_plain_text($request->order) ?? CmsOrder::labelForIndex(EquipmentCategory::count());
        $showInHome = $request->boolean('show_in_home');
        $isActive = $request->boolean('is_active', true);

        $category = DB::transaction(function () use ($request, $name, $slug, $order, $showInHome, $isActive) {
            $category = EquipmentCategory::create([
                'name' => $name,
                'slug' => $slug,
                'order' => $order,
                'show_in_home' => $showInHome,
                'is_active' => $isActive,
            ]);

            $coverData = [
                'equipment_category_id' => $category->id,
                'title' => $name,
                'slug' => $slug,
                'description' => null,
                'features' => [],
                'is_catalog_root' => true,
                'show_in_home' => false,
                'order' => 'A',
                'is_active' => $isActive,
            ];

            if ($request->hasFile('cover_image')) {
                $coverData['main_image'] = $request->file('cover_image')->store('equipments', 'public');
            }

            Equipment::create($coverData);

            return $category;
        });

        return response()->json($category, 201);
    }

    public function update(Request $request, EquipmentCategory $equipmentCategory)
    {
        $request->validate([
            'name'  => 'sometimes|required|string|max:255',
            'slug'  => 'nullable|string|max:255|unique:equipment_categories,slug,' . $equipmentCategory->id,
            'order' => 'nullable|string|max:10',
            'cover_image' => 'nullable|image|max:5120',
            'show_in_home' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'cover_equipment_id' => 'nullable|exists:equipments,id',
        ]);

        $name = $request->exists('name') ? cms_plain_text($request->name) : $equipmentCategory->name;
        $slug = $request->exists('slug') || $request->exists('name')
            ? $this->resolveSlug($request->input('slug'), $name)
            : $equipmentCategory->slug;

        $cover = $this->resolveCover($equipmentCategory);

        if ($request->filled('cover_equipment_id')) {
            $cover = Equipment::query()
                ->where('equipment_category_id', $equipmentCategory->id)
                ->findOrFail($request->integer('cover_equipment_id'));

            Equipment::query()
                ->where('equipment_category_id', $equipmentCategory->id)
                ->update(['is_catalog_root' => false]);

            $cover->update(['is_catalog_root' => true]);
        }

        DB::transaction(function () use ($request, $equipmentCategory, $name, $slug, $cover) {
            $equipmentCategory->update([
                'name' => $name,
                'slug' => $slug,
                'order' => $request->exists('order') ? (cms_plain_text($request->order) ?? $equipmentCategory->order) : $equipmentCategory->order,
                'show_in_home' => $request->exists('show_in_home') ? $request->boolean('show_in_home') : $equipmentCategory->show_in_home,
                'is_active' => $request->exists('is_active') ? $request->boolean('is_active') : $equipmentCategory->is_active,
            ]);

            if ($cover) {
                $coverUpdates = [
                    'is_active' => $request->exists('is_active') ? $request->boolean('is_active') : $cover->is_active,
                ];

                if ($request->hasFile('cover_image')) {
                    if ($cover->main_image) {
                        Storage::disk('public')->delete($cover->main_image);
                    }

                    $coverUpdates['main_image'] = $request->file('cover_image')->store('equipments', 'public');
                }

                $cover->update($coverUpdates);
            }
        });

        if ($request->header('X-Inertia')) {
            return redirect()
                ->route('admin.equipment-categories.edit', $equipmentCategory)
                ->with('success', 'Categoría actualizada correctamente.');
        }

        return response()->json($equipmentCategory);
    }

    public function destroy(EquipmentCategory $equipmentCategory)
    {
        // Los equipments se eliminan por cascadeOnDelete en la FK
        $equipmentCategory->delete();

        return response()->json(['ok' => true]);
    }

    protected function countProducts(EquipmentCategory $category): int
    {
        $equipments = $category->equipments;

        if ($equipments->isEmpty()) {
            return 0;
        }

        $cover = $this->resolveCover($category, $equipments);

        if (! $cover) {
            return $equipments->count();
        }

        return $equipments->where('id', '!=', $cover->id)->count();
    }

    protected function serializeCover(EquipmentCategory $category): ?array
    {
        $equipments = $category->equipments;

        if ($equipments->isEmpty()) {
            return null;
        }

        $cover = $this->resolveCover($category, $equipments);

        if (! $cover instanceof Equipment) {
            return null;
        }

        return [
            'id' => $cover->id,
            'title' => $cover->title,
            'image_url' => $cover->main_image_url,
            'has_real_image' => $cover->main_image_url !== asset('images/placeholder-equipment.svg'),
        ];
    }

    protected function resolveCover(EquipmentCategory $category, $equipments = null): ?Equipment
    {
        $equipments = collect($equipments ?? $category->equipments);

        if ($equipments->isEmpty()) {
            return null;
        }

        return $equipments->firstWhere('slug', $category->slug)
            ?? $equipments->firstWhere('title', $category->name)
            ?? $equipments->firstWhere('is_catalog_root', true)
            ?? $equipments->first();
    }

    protected function resolveSlug(?string $slug, ?string $name): string
    {
        $candidate = cms_plain_text($slug);

        if ($candidate === null || $candidate === '') {
            $candidate = Str::slug((string) $name);
        } else {
            $candidate = Str::slug($candidate);
        }

        return $candidate !== '' ? $candidate : 'categoria';
    }
}
