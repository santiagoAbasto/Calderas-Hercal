<?php

namespace Tests\Feature\Cms;

use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicEquipmentCatalogTest extends TestCase
{
    use RefreshDatabase;

    protected int $initialOutputBufferLevel = 0;

    protected function setUp(): void
    {
        parent::setUp();

        $this->initialOutputBufferLevel = ob_get_level();
    }

    protected function tearDown(): void
    {
        while (ob_get_level() > $this->initialOutputBufferLevel) {
            ob_end_clean();
        }

        parent::tearDown();
    }

    public function test_equipment_without_slug_is_visible_in_public_index(): void
    {
        $page = Page::create([
            'slug' => 'equipos',
            'name' => 'Equipos',
            'is_active' => true,
        ]);

        PageSection::create([
            'page_id' => $page->id,
            'section_key' => 'equipos_banner',
            'title' => 'Equipos',
            'order' => 'A',
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
            'title' => 'Caldera',
            'slug' => null,
            'description' => 'Equipo principal',
            'is_catalog_root' => true,
            'show_in_home' => false,
            'order' => 'A',
            'is_active' => true,
        ]);

        $this->get('/equipos')
            ->assertOk()
            ->assertSee('Caldera');
    }

    public function test_equipment_detail_shows_all_active_items_from_the_same_category(): void
    {
        $category = EquipmentCategory::create([
            'name' => 'Calderas',
            'slug' => 'calderas',
            'order' => 'A',
            'is_active' => true,
        ]);

        $root = Equipment::create([
            'equipment_category_id' => $category->id,
            'title' => 'Caldera',
            'slug' => 'caldera',
            'description' => 'Equipo principal',
            'is_catalog_root' => true,
            'show_in_home' => false,
            'order' => 'A',
            'is_active' => true,
        ]);

        Equipment::create([
            'equipment_category_id' => $category->id,
            'title' => 'Caldera de vapor',
            'slug' => 'caldera-de-vapor',
            'description' => 'Equipo secundario',
            'is_catalog_root' => false,
            'show_in_home' => false,
            'order' => 'B',
            'is_active' => true,
        ]);

        Equipment::create([
            'equipment_category_id' => $category->id,
            'title' => 'Caldera mural',
            'slug' => 'caldera-mural',
            'description' => 'Equipo terciario',
            'is_catalog_root' => false,
            'show_in_home' => false,
            'order' => 'C',
            'is_active' => true,
        ]);

        $this->get("/equipos/{$root->slug}")
            ->assertOk()
            ->assertSee('Caldera')
            ->assertSee('Caldera de vapor')
            ->assertSee('Caldera mural');
    }

    public function test_category_slug_renders_only_child_products_in_public_tabs(): void
    {
        $category = EquipmentCategory::create([
            'name' => 'Calderas',
            'slug' => 'calderas',
            'order' => 'A',
            'is_active' => true,
        ]);

        Equipment::create([
            'equipment_category_id' => $category->id,
            'title' => 'Calderas',
            'slug' => 'calderas',
            'description' => 'Categoría principal',
            'is_catalog_root' => true,
            'show_in_home' => false,
            'order' => 'A',
            'is_active' => true,
        ]);

        Equipment::create([
            'equipment_category_id' => $category->id,
            'title' => 'Caldera de vapor',
            'slug' => 'caldera-de-vapor',
            'description' => 'Equipo vapor',
            'is_catalog_root' => false,
            'show_in_home' => false,
            'order' => 'B',
            'is_active' => true,
        ]);

        Equipment::create([
            'equipment_category_id' => $category->id,
            'title' => 'Caldera de agua',
            'slug' => 'caldera-de-agua',
            'description' => 'Equipo agua',
            'is_catalog_root' => false,
            'show_in_home' => false,
            'order' => 'C',
            'is_active' => true,
        ]);

        $response = $this->get('/equipos/calderas');

        $response->assertOk();
        $response->assertSee('Caldera de vapor');
        $response->assertSee('Caldera de agua');
        $response->assertDontSeeHtml('>Calderas</button>');
    }

    public function test_public_equipment_index_uses_category_cover_only_once(): void
    {
        $page = Page::create([
            'slug' => 'equipos',
            'name' => 'Equipos',
            'is_active' => true,
        ]);

        PageSection::create([
            'page_id' => $page->id,
            'section_key' => 'equipos_banner',
            'title' => 'Equipos',
            'order' => 'A',
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
            'title' => 'Calderas',
            'slug' => 'calderas',
            'description' => 'Portada',
            'is_catalog_root' => true,
            'show_in_home' => false,
            'order' => 'A',
            'is_active' => true,
        ]);

        Equipment::create([
            'equipment_category_id' => $category->id,
            'title' => 'Caldera de vapor',
            'slug' => 'caldera-de-vapor',
            'description' => 'Producto',
            'is_catalog_root' => false,
            'show_in_home' => false,
            'order' => 'B',
            'is_active' => true,
        ]);

        Equipment::create([
            'equipment_category_id' => $category->id,
            'title' => 'Caldera de agua',
            'slug' => 'Caldera-de-Agua',
            'description' => 'Producto mal marcado',
            'is_catalog_root' => true,
            'show_in_home' => false,
            'order' => 'C',
            'is_active' => true,
        ]);

        $response = $this->get('/equipos');

        $response->assertOk();
        $response->assertSee('Calderas');
        $response->assertDontSee('Caldera de agua');
    }

    public function test_category_without_child_products_does_not_render_cover_as_product_tab(): void
    {
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
            'description' => 'Portada de categoría',
            'is_catalog_root' => true,
            'show_in_home' => false,
            'order' => 'A',
            'is_active' => true,
        ]);

        $response = $this->get('/equipos/termotanques');

        $response->assertOk();
        $response->assertDontSeeHtml('data-equipo-tab=');
        $response->assertSee('Portada de categoría');
    }
}
