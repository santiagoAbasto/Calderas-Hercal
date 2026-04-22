<?php

namespace Tests\Feature\Cms;

use App\Models\Page;
use App\Models\PageSection;
use App\Models\PageSectionMedia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeSliderActivationTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_slider_block_can_be_deactivated_from_admin(): void
    {
        config(['security.admin_allowed_emails' => ['admin@example.com']]);

        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
        ]);

        $page = Page::create([
            'slug' => 'home',
            'name' => 'Home',
            'is_active' => true,
        ]);

        $section = PageSection::create([
            'page_id' => $page->id,
            'section_key' => 'hero',
            'title' => '<span>Hero Home</span>',
            'order' => 'A',
            'is_active' => true,
        ]);

        $this->actingAs($user)->post("/admin/sections/{$section->id}", [
            'title' => '<span>Hero Home</span>',
            'is_active' => false,
        ])->assertOk();

        $this->assertDatabaseHas('page_sections', [
            'id' => $section->id,
            'is_active' => false,
        ]);
    }

    public function test_home_slider_slide_can_be_deactivated_from_admin(): void
    {
        config(['security.admin_allowed_emails' => ['admin@example.com']]);

        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
        ]);

        $page = Page::create([
            'slug' => 'home',
            'name' => 'Home',
            'is_active' => true,
        ]);

        $section = PageSection::create([
            'page_id' => $page->id,
            'section_key' => 'hero',
            'title' => '<span>Hero Home</span>',
            'order' => 'A',
            'is_active' => true,
        ]);

        $media = PageSectionMedia::create([
            'page_section_id' => $section->id,
            'type' => 'image',
            'file_path' => 'pages/home.png',
            'title' => '<span>Slide 1</span>',
            'order' => 'A',
            'is_active' => true,
        ]);

        $this->actingAs($user)->post("/admin/section-media/{$media->id}", [
            'type' => 'image',
            'title' => '<span>Slide 1</span>',
            'is_active' => false,
        ])->assertOk();

        $this->assertDatabaseHas('page_section_media', [
            'id' => $media->id,
            'is_active' => false,
        ]);
    }

    public function test_home_does_not_render_hero_when_block_is_inactive(): void
    {
        $page = Page::create([
            'slug' => 'home',
            'name' => 'Home',
            'is_active' => true,
        ]);

        $section = PageSection::create([
            'page_id' => $page->id,
            'section_key' => 'hero',
            'title' => '<span>Hero Home</span>',
            'order' => 'A',
            'is_active' => false,
        ]);

        PageSectionMedia::create([
            'page_section_id' => $section->id,
            'type' => 'image',
            'file_path' => 'pages/home.png',
            'title' => '<span>Slide 1</span>',
            'order' => 'A',
            'is_active' => true,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('hc-hero', false)
            ->assertDontSee('Hero Home', false);
    }

    public function test_home_does_not_render_hero_when_all_slides_are_inactive(): void
    {
        $page = Page::create([
            'slug' => 'home',
            'name' => 'Home',
            'is_active' => true,
        ]);

        $section = PageSection::create([
            'page_id' => $page->id,
            'section_key' => 'hero',
            'title' => '<span>Hero Home</span>',
            'order' => 'A',
            'is_active' => true,
        ]);

        PageSectionMedia::create([
            'page_section_id' => $section->id,
            'type' => 'image',
            'file_path' => 'pages/home.png',
            'title' => '<span>Slide 1</span>',
            'order' => 'A',
            'is_active' => false,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('hc-hero', false)
            ->assertDontSee('Hero Home', false);
    }
}
