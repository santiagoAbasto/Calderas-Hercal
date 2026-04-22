<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Database\Seeder;

class EquipmentsPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::updateOrCreate(
            ['slug' => 'equipos'],
            ['name' => 'Equipos', 'is_active' => true]
        );

        PageSection::updateOrCreate(
            [
                'page_id' => $page->id,
                'section_key' => 'equipos_banner',
            ],
            [
                'page_id' => $page->id,
                'section_key' => 'equipos_banner',
                'title' => 'Equipos',
                'subtitle' => null,
                'description' => null,
                'meta' => [
                    'seo_description' => 'Equipos industriales de Calderas Hercal: calderas, termotanques y tanques intermediarios de agua caliente.',
                    'breadcrumb_home_text' => 'Inicio',
                    'breadcrumb_current_text' => 'Equipos',
                    'card_button_text' => 'Ver más',
                ],
                'order' => 1,
                'is_active' => true,
            ]
        );
    }
}
