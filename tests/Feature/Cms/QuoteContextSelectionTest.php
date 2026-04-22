<?php

namespace Tests\Feature\Cms;

use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteContextSelectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_budget_page_preselects_first_product_when_arriving_from_equipment_category(): void
    {
        $page = Page::create([
            'slug' => 'presupuesto',
            'name' => 'Presupuesto',
            'is_active' => true,
        ]);

        PageSection::create([
            'page_id' => $page->id,
            'section_key' => 'presupuesto_banner',
            'title' => 'Presupuesto',
            'order' => 'A',
            'is_active' => true,
        ]);

        PageSection::create([
            'page_id' => $page->id,
            'section_key' => 'presupuesto_content',
            'title' => 'Datos de contacto',
            'subtitle' => 'Consulta',
            'order' => 'B',
            'is_active' => true,
        ]);

        $category = EquipmentCategory::create([
            'name' => 'Termotanques',
            'slug' => 'termotanques',
            'order' => 'A',
            'is_active' => true,
        ]);

        Equipment::create([
            'equipment_category_id' => $category->id,
            'title' => 'Termotanques',
            'slug' => 'termotanques',
            'description' => 'Ficha principal',
            'is_catalog_root' => true,
            'show_in_home' => false,
            'order' => 'A',
            'is_active' => true,
        ]);

        $vertical = Equipment::create([
            'equipment_category_id' => $category->id,
            'title' => 'Termotanque Vertical',
            'slug' => null,
            'description' => 'Producto vertical',
            'is_catalog_root' => false,
            'show_in_home' => false,
            'order' => 'AA',
            'is_active' => true,
        ]);

        Equipment::create([
            'equipment_category_id' => $category->id,
            'title' => 'Termotanque Horizontal',
            'slug' => null,
            'description' => 'Producto horizontal',
            'is_catalog_root' => false,
            'show_in_home' => false,
            'order' => 'AB',
            'is_active' => true,
        ]);

        $response = $this->get('/presupuesto?context=equipment&slug=termotanques');

        $response
            ->assertOk()
            ->assertSee('Termotanque Vertical')
            ->assertSee('Termotanque Horizontal')
            ->assertDontSee('>Termotanques</option>', false)
            ->assertSee('option value="' . $vertical->id . '" selected', false);
    }
}
