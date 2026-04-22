<?php

use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Support\CmsOrder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $categories = EquipmentCategory::query()->with(['equipments' => fn ($query) => $query->orderBy('id')])->orderBy('id')->get();

        foreach ($categories->values() as $categoryIndex => $category) {
            $category->update([
                'order' => CmsOrder::labelForIndex($categoryIndex),
                'show_in_home' => $category->equipments->contains(fn (Equipment $equipment) => (bool) $equipment->show_in_home),
            ]);

            $cover = $category->equipments->firstWhere('slug', $category->slug)
                ?? $category->equipments->firstWhere('title', $category->name)
                ?? $category->equipments->firstWhere('is_catalog_root', true)
                ?? $category->equipments->first();

            $ordered = collect();

            if ($cover) {
                $ordered->push($cover);
            }

            $ordered = $ordered->concat(
                $category->equipments
                    ->reject(fn (Equipment $equipment) => $cover && $equipment->id === $cover->id)
                    ->sortBy('id')
                    ->values()
            )->values();

            foreach ($ordered as $equipmentIndex => $equipment) {
                $equipment->update([
                    'order' => CmsOrder::labelForIndex($equipmentIndex),
                    'is_catalog_root' => $cover ? $equipment->id === $cover->id : (bool) $equipment->is_catalog_root,
                ]);
            }
        }
    }

    public function down(): void
    {
    }
};
