<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Database\Seeder;

class ProjectsPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::updateOrCreate(
            ['slug' => 'trabajos-realizados'],
            ['name' => 'Trabajos realizados', 'is_active' => true]
        );

        PageSection::updateOrCreate(
            [
                'page_id' => $page->id,
                'section_key' => 'trabajos_banner',
            ],
            [
                'page_id' => $page->id,
                'section_key' => 'trabajos_banner',
                'title' => 'Trabajos realizados',
                'subtitle' => null,
                'description' => null,
                'meta' => [
                    'seo_description' => 'Trabajos realizados por Calderas Hercal en calderas, termotanques y soluciones térmicas industriales.',
                    'breadcrumb_home_text' => 'Inicio',
                    'breadcrumb_current_text' => 'Trabajos realizados',
                    'card_button_text' => 'Ver más',
                ],
                'order' => 1,
                'is_active' => true,
            ]
        );
    }
}
