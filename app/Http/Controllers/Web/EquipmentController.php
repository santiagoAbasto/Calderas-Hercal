<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\Page;

class EquipmentController extends Controller
{
    public function index()
    {
        $page = Page::where('slug', 'equipos')
            ->with(['sections' => fn ($q) => $q
                ->where('is_active', true)
                ->with('activeMedia'),
            ])
            ->first();

        $equipments = EquipmentCategory::query()
            ->where('is_active', true)
            ->with(['equipments' => fn ($query) => $query
                ->where('is_active', true)
                ->orderBy('order')
                ->with(['images', 'category']),
            ])
            ->orderBy('order')
            ->get();

        $equipments = $equipments
            ->map(fn (EquipmentCategory $category) => $this->resolveCategoryCover($category))
            ->filter()
            ->values();

        return view('web.equipments.index', compact('page', 'equipments'));
    }

    public function show($slug)
    {
        $category = EquipmentCategory::query()
            ->where('is_active', true)
            ->where('slug', $slug)
            ->first();

        if ($category) {
            $allCategoryEquipments = Equipment::query()
                ->with(['images', 'category'])
                ->where('is_active', true)
                ->where('equipment_category_id', $category->id)
                ->orderBy('order')
                ->get();

            $rootEquipment = $this->resolveCategoryCover($category, $allCategoryEquipments);
            $products = $allCategoryEquipments
                ->when($rootEquipment, fn ($collection) => $collection->reject(fn (Equipment $equipment) => $equipment->id === $rootEquipment->id))
                ->values();

            abort_if($products->isEmpty() && ! $rootEquipment, 404);

            $equipment = $rootEquipment ?? $products->first();
            $selectedProductId = $products->first()?->id;

            return view('web.equipments.show', compact('equipment', 'products', 'selectedProductId'));
        }

        $equipmentQuery = Equipment::query()
            ->with(['images', 'category'])
            ->where('is_active', true)
            ->whereHas('category', fn ($query) => $query->where('is_active', true));

        $equipment = $equipmentQuery
            ->where('slug', $slug)
            ->first();

        if (! $equipment && ctype_digit((string) $slug)) {
            $equipment = (clone $equipmentQuery)->findOrFail((int) $slug);
        }

        abort_unless($equipment, 404);

        $products = Equipment::query()
            ->with(['images', 'category'])
            ->where('is_active', true)
            ->where('equipment_category_id', $equipment->equipment_category_id)
            ->orderBy('order')
            ->get();

        $rootEquipment = $equipment->category
            ? $this->resolveCategoryCover($equipment->category, $products)
            : null;

        if ($rootEquipment) {
            $products = $products
                ->reject(fn (Equipment $item) => $item->id === $rootEquipment->id)
                ->values();
        }

        $selectedProductId = $products->firstWhere('id', $equipment->id)?->id ?? $products->first()?->id;

        return view('web.equipments.show', compact('equipment', 'products', 'selectedProductId'));
    }

    protected function resolveCategoryCover(EquipmentCategory $category, $equipments = null): ?Equipment
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
}
