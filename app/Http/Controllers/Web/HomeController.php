<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\Page;
use App\Models\Project;
use App\Models\Service;
use App\Support\CmsOrder;

class HomeController extends Controller
{
    public function index()
    {
        $home = Page::where('slug', 'home')
            ->where('is_active', true)
            ->with([
                'sections' => fn ($q) => $q
                    ->where('is_active', true)
                    ->with('activeMedia'),
            ])
            ->first();

        $services = Service::where('is_active', true)
            ->where('show_in_home', true)
            ->orderBy('order')
            ->get();

        $allActiveCategories = EquipmentCategory::query()
            ->where('is_active', true)
            ->with(['equipments' => fn ($query) => $query
                ->where('is_active', true)
                ->with('images')
                ->orderBy('order'),
            ])
            ->get();

        $coverIds = $allActiveCategories
            ->map(fn (EquipmentCategory $category) => $this->resolveDeclaredCategoryCover($category)?->id)
            ->filter()
            ->values();

        $categoryCovers = $allActiveCategories
            ->where('show_in_home', true)
            ->where('is_active', true)
            ->map(fn (EquipmentCategory $category) => $this->resolveCategoryCover($category))
            ->filter();

        $homeProducts = Equipment::query()
            ->where('is_active', true)
            ->where('show_in_home', true)
            ->whereHas('category', fn ($query) => $query->where('is_active', true))
            ->when($coverIds->isNotEmpty(), fn ($query) => $query->whereNotIn('id', $coverIds->all()))
            ->with(['images', 'category'])
            ->get();

        $equipments = $categoryCovers
            ->concat($homeProducts)
            ->unique('id')
            ->sort(function (Equipment $left, Equipment $right) {
                $categoryOrder = CmsOrder::compare($left->category?->order, $right->category?->order);

                if ($categoryOrder !== 0) {
                    return $categoryOrder;
                }

                return CmsOrder::compare($left->order, $right->order);
            })
            ->values();

        $projects = Project::where('is_active', true)
            ->where('show_in_home', true)
            ->whereNotNull('slug')
            ->with('images')
            ->orderBy('order')
            ->take(6)
            ->get();

        return view('web.home', compact(
            'home',
            'services',
            'equipments',
            'projects'
        ));
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
