<?php

namespace Database\Seeders;

use App\Models\ClientType;
use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ClientesPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::updateOrCreate(
            ['slug' => 'clientes'],
            ['name' => 'Clientes', 'is_active' => true]
        );

        PageSection::updateOrCreate(
            [
                'page_id' => $page->id,
                'section_key' => 'clientes_banner',
            ],
            [
                'page_id' => $page->id,
                'section_key' => 'clientes_banner',
                'title' => 'Clientes',
                'subtitle' => null,
                'description' => null,
                'meta' => [
                    'seo_description' => 'Clientes y segmentos de atencion de Calderas Hercal.',
                    'breadcrumb_home_text' => 'Inicio',
                    'breadcrumb_current_text' => 'Clientes',
                ],
                'order' => 1,
                'is_active' => true,
            ]
        );

        PageSection::updateOrCreate(
            [
                'page_id' => $page->id,
                'section_key' => 'clientes_content',
            ],
            [
                'page_id' => $page->id,
                'section_key' => 'clientes_content',
                'title' => 'Brindamos atención a:',
                'subtitle' => null,
                'description' => null,
                'meta' => [],
                'order' => 2,
                'is_active' => true,
            ]
        );

        $clients = [
            ['name' => 'Consorcios', 'slug' => 'consorcios', 'order' => 'A', 'icon_width' => 51, 'icon_height' => 51],
            ['name' => 'Edificios públicos', 'slug' => 'edificios-publicos', 'order' => 'AA', 'icon_width' => 51, 'icon_height' => 51],
            ['name' => 'Hospitales', 'slug' => 'hospitales', 'order' => 'AB', 'icon_width' => 51, 'icon_height' => 51],
            ['name' => 'Hoteles', 'slug' => 'hoteles', 'order' => 'AC', 'icon_width' => 35, 'icon_height' => 35],
            ['name' => 'Clubes', 'slug' => 'clubes', 'order' => 'B', 'icon_width' => 35, 'icon_height' => 43],
        ];

        foreach ($clients as $data) {
            ClientType::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'slug' => $data['slug'] ?: Str::slug($data['name']),
                    'order' => $data['order'],
                    'icon_width' => $data['icon_width'],
                    'icon_height' => $data['icon_height'],
                    'is_active' => true,
                ]
            );
        }
    }
}
