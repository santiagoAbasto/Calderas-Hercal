<?php

namespace Tests\Feature\Cms;

use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EquipmentCategoryCoverTest extends TestCase
{
    use RefreshDatabase;

    public function test_cover_assignment_is_controlled_from_categories_screen(): void
    {
        config(['security.admin_allowed_emails' => ['admin@example.com']]);

        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
        ]);

        $category = EquipmentCategory::create([
            'name' => 'Calderas',
            'slug' => 'calderas',
            'order' => 'A',
            'is_active' => true,
        ]);

        $first = Equipment::create([
            'equipment_category_id' => $category->id,
            'title' => 'Calderas',
            'slug' => 'calderas',
            'description' => 'Principal',
            'is_catalog_root' => true,
            'show_in_home' => false,
            'order' => 'A',
            'is_active' => true,
        ]);

        $second = Equipment::create([
            'equipment_category_id' => $category->id,
            'title' => 'Caldera de vapor',
            'slug' => 'caldera-de-vapor',
            'description' => 'Secundaria',
            'is_catalog_root' => false,
            'show_in_home' => false,
            'order' => 'B',
            'is_active' => true,
        ]);

        $this->actingAs($user)->post("/admin/equipment-categories/{$category->id}", [
            'name' => $category->name,
            'slug' => $category->slug,
            'order' => $category->order,
            'is_active' => true,
            'cover_equipment_id' => $second->id,
        ])->assertOk();

        $this->assertDatabaseHas('equipments', [
            'id' => $second->id,
            'is_catalog_root' => true,
        ]);

        $this->assertDatabaseHas('equipments', [
            'id' => $first->id,
            'is_catalog_root' => false,
        ]);
    }

    public function test_admin_products_index_hides_category_cover_rows(): void
    {
        config(['security.admin_allowed_emails' => ['admin@example.com']]);

        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
        ]);

        $category = EquipmentCategory::create([
            'name' => 'Calderas',
            'slug' => 'calderas',
            'order' => 'A',
            'is_active' => true,
        ]);

        Equipment::create([
            'equipment_category_id' => $category->id,
            'title' => 'Portada Calderas',
            'slug' => 'calderas',
            'description' => 'Portada',
            'is_catalog_root' => true,
            'show_in_home' => false,
            'order' => 'A',
            'is_active' => true,
        ]);

        Equipment::create([
            'equipment_category_id' => $category->id,
            'title' => 'Caldera de agua',
            'slug' => 'caldera-de-agua',
            'description' => 'Producto',
            'is_catalog_root' => false,
            'show_in_home' => false,
            'order' => 'B',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/admin/equipments');

        $response->assertOk();
        $response->assertSee('Caldera de agua');
        $response->assertDontSee('Portada Calderas');
    }

    public function test_admin_products_index_keeps_misflagged_product_if_it_is_not_the_real_cover(): void
    {
        config(['security.admin_allowed_emails' => ['admin@example.com']]);

        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
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
            'title' => 'Caldera de agua',
            'slug' => 'caldera-de-agua',
            'description' => 'Producto real mal marcado',
            'is_catalog_root' => true,
            'show_in_home' => false,
            'order' => 'B',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/admin/equipments');

        $response->assertOk();
        $response->assertSee('Caldera de agua');
    }

    public function test_category_screen_can_choose_the_cover_record(): void
    {
        config(['security.admin_allowed_emails' => ['admin@example.com']]);

        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
        ]);

        $category = EquipmentCategory::create([
            'name' => 'Calderas',
            'slug' => 'calderas',
            'order' => 'A',
            'is_active' => true,
        ]);

        $first = Equipment::create([
            'equipment_category_id' => $category->id,
            'title' => 'Calderas',
            'slug' => 'calderas',
            'description' => 'Portada',
            'is_catalog_root' => true,
            'show_in_home' => false,
            'order' => 'A',
            'is_active' => true,
        ]);

        $second = Equipment::create([
            'equipment_category_id' => $category->id,
            'title' => 'Caldera de agua',
            'slug' => 'caldera-de-agua',
            'description' => 'Producto',
            'is_catalog_root' => false,
            'show_in_home' => false,
            'order' => 'B',
            'is_active' => true,
        ]);

        $this->actingAs($user)->post("/admin/equipment-categories/{$category->id}", [
            'name' => $category->name,
            'slug' => $category->slug,
            'order' => $category->order,
            'is_active' => true,
            'cover_equipment_id' => $second->id,
        ])->assertOk();

        $this->assertDatabaseHas('equipments', [
            'id' => $second->id,
            'is_catalog_root' => true,
        ]);

        $this->assertDatabaseHas('equipments', [
            'id' => $first->id,
            'is_catalog_root' => false,
        ]);
    }

    public function test_creating_category_builds_cover_record_and_slug_from_name(): void
    {
        Storage::fake('public');
        config(['security.admin_allowed_emails' => ['admin@example.com']]);

        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->post('/admin/equipment-categories', [
            'name' => 'Tanques Intermediario de Agua Caliente',
            'slug' => '',
            'order' => 'A',
            'show_in_home' => true,
            'is_active' => true,
            'cover_image' => UploadedFile::fake()->image('tanque.png'),
        ]);

        $response->assertCreated();

        $category = EquipmentCategory::query()->firstOrFail();

        $this->assertSame('tanques-intermediario-de-agua-caliente', $category->slug);
        $this->assertTrue($category->show_in_home);

        $this->assertDatabaseHas('equipments', [
            'equipment_category_id' => $category->id,
            'title' => 'Tanques Intermediario de Agua Caliente',
            'slug' => 'tanques-intermediario-de-agua-caliente',
            'is_catalog_root' => true,
        ]);
    }
}
