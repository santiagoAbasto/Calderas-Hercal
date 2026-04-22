<?php

namespace Tests\Feature\Cms;

use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeEquipmentVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_equipment_marked_for_home_is_rendered_even_if_not_catalog_root(): void
    {
        $page = Page::create([
            'slug' => 'home',
            'name' => 'Home',
            'is_active' => true,
        ]);

        PageSection::create([
            'page_id' => $page->id,
            'section_key' => 'equipments',
            'title' => 'Equipos',
            'order' => 'B',
            'is_active' => true,
        ]);

        $category = EquipmentCategory::create([
            'name' => 'Calderas',
            'slug' => 'calderas',
            'order' => 'A',
            'is_active' => true,
        ]);

        Equipment::create([
            'equipment_category_id' => $category->id,
            'title' => 'Caldera de vapor',
            'slug' => 'caldera-de-vapor',
            'description' => 'Equipo industrial',
            'is_catalog_root' => false,
            'show_in_home' => true,
            'order' => 'A',
            'is_active' => true,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Caldera de vapor');
    }

    public function test_category_marked_for_home_renders_its_cover_in_home(): void
    {
        $page = Page::create([
            'slug' => 'home',
            'name' => 'Home',
            'is_active' => true,
        ]);

        PageSection::create([
            'page_id' => $page->id,
            'section_key' => 'equipments',
            'title' => 'Equipos',
            'order' => 'B',
            'is_active' => true,
        ]);

        $category = EquipmentCategory::create([
            'name' => 'Termotanques',
            'slug' => 'termotanques',
            'order' => 'A',
            'show_in_home' => true,
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

        $this->get('/')
            ->assertOk()
            ->assertSee('Termotanques');
    }

    public function test_category_cover_is_hidden_from_home_when_category_home_is_off(): void
    {
        $page = Page::create([
            'slug' => 'home',
            'name' => 'Home',
            'is_active' => true,
        ]);

        PageSection::create([
            'page_id' => $page->id,
            'section_key' => 'equipments',
            'title' => 'Equipos',
            'order' => 'B',
            'is_active' => true,
        ]);

        $category = EquipmentCategory::create([
            'name' => 'Termotanques',
            'slug' => 'termotanques',
            'order' => 'A',
            'show_in_home' => false,
            'is_active' => true,
        ]);

        Equipment::create([
            'equipment_category_id' => $category->id,
            'title' => 'Termotanques',
            'slug' => 'termotanques',
            'description' => 'Ficha principal',
            'is_catalog_root' => true,
            'show_in_home' => true,
            'order' => 'A',
            'is_active' => true,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertDontSeeHtml('href="http://localhost/equipos/termotanques"');
    }

    public function test_inactive_category_does_not_render_products_in_home_even_if_product_is_marked(): void
    {
        $page = Page::create([
            'slug' => 'home',
            'name' => 'Home',
            'is_active' => true,
        ]);

        PageSection::create([
            'page_id' => $page->id,
            'section_key' => 'equipments',
            'title' => 'Equipos',
            'order' => 'B',
            'is_active' => true,
        ]);

        $category = EquipmentCategory::create([
            'name' => 'Calderas',
            'slug' => 'calderas',
            'order' => 'A',
            'show_in_home' => false,
            'is_active' => false,
        ]);

        Equipment::create([
            'equipment_category_id' => $category->id,
            'title' => 'Caldera de vapor',
            'slug' => 'caldera-de-vapor',
            'description' => 'Equipo industrial',
            'is_catalog_root' => false,
            'show_in_home' => true,
            'order' => 'A',
            'is_active' => true,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('Caldera de vapor');
    }

    public function test_home_shows_all_enabled_category_covers_and_products_without_cutting_to_six(): void
    {
        $page = Page::create([
            'slug' => 'home',
            'name' => 'Home',
            'is_active' => true,
        ]);

        PageSection::create([
            'page_id' => $page->id,
            'section_key' => 'equipments',
            'title' => 'Equipos',
            'order' => 'B',
            'is_active' => true,
        ]);

        for ($index = 1; $index <= 3; $index++) {
            $category = EquipmentCategory::create([
                'name' => "Categoria {$index}",
                'slug' => "categoria-{$index}",
                'order' => (string) $index,
                'show_in_home' => true,
                'is_active' => true,
            ]);

            Equipment::create([
                'equipment_category_id' => $category->id,
                'title' => "Categoria {$index}",
                'slug' => "categoria-{$index}",
                'description' => 'Portada',
                'is_catalog_root' => true,
                'show_in_home' => false,
                'order' => 'A',
                'is_active' => true,
            ]);
        }

        $productsCategory = EquipmentCategory::create([
            'name' => 'Productos',
            'slug' => 'productos',
            'order' => 'Z',
            'show_in_home' => false,
            'is_active' => true,
        ]);

        for ($index = 1; $index <= 5; $index++) {
            Equipment::create([
                'equipment_category_id' => $productsCategory->id,
                'title' => "Producto {$index}",
                'slug' => "producto-{$index}",
                'description' => 'Producto',
                'is_catalog_root' => false,
                'show_in_home' => true,
                'order' => 'A' . chr(64 + $index),
                'is_active' => true,
            ]);
        }

        $response = $this->get('/');

        $response->assertOk();

        for ($index = 1; $index <= 3; $index++) {
            $response->assertSee("Categoria {$index}");
        }

        for ($index = 1; $index <= 5; $index++) {
            $response->assertSee("Producto {$index}");
        }
    }
}
