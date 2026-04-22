<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Database\Seeder;

class PresupuestoPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::updateOrCreate(
            ['slug' => 'presupuesto'],
            ['name' => 'Presupuesto', 'is_active' => true]
        );

        PageSection::updateOrCreate(
            [
                'page_id' => $page->id,
                'section_key' => 'presupuesto_banner',
            ],
            [
                'page_id' => $page->id,
                'section_key' => 'presupuesto_banner',
                'title' => 'Presupuesto',
                'subtitle' => null,
                'description' => null,
                'meta' => [
                    'seo_description' => 'Solicitar presupuesto Calderas Hercal.',
                    'breadcrumb_home_text' => 'Inicio',
                    'breadcrumb_current_text' => 'Presupuesto',
                ],
                'order' => 1,
                'is_active' => true,
            ]
        );

        PageSection::updateOrCreate(
            [
                'page_id' => $page->id,
                'section_key' => 'presupuesto_content',
            ],
            [
                'page_id' => $page->id,
                'section_key' => 'presupuesto_content',
                'title' => 'Datos de contacto',
                'subtitle' => 'Consulta',
                'description' => null,
                'meta' => [
                    'name_label' => 'Nombre*',
                    'email_label' => 'Email*',
                    'last_name_label' => 'Apellido*',
                    'phone_label' => 'Teléfono*',
                    'service_label' => 'Servicio',
                    'equipment_label' => 'Equipos',
                    'message_label' => 'Consulta',
                    'button_text' => 'Solicitar presupuesto',
                ],
                'order' => 2,
                'is_active' => true,
            ]
        );
    }
}
