<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageSection;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ServicesPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::updateOrCreate(
            ['slug' => 'servicios'],
            ['name' => 'Servicios', 'is_active' => true]
        );

        PageSection::updateOrCreate(
            [
                'page_id' => $page->id,
                'section_key' => 'servicios_banner',
            ],
            [
                'page_id' => $page->id,
                'section_key' => 'servicios_banner',
                'title' => 'Servicios',
                'subtitle' => null,
                'description' => null,
                'meta' => [
                    'seo_description' => 'Servicios especializados de Calderas Hercal: mantenimiento, reparaciones, automatización, aislaciones, certificaciones y más.',
                    'breadcrumb_home_text' => 'Inicio',
                    'breadcrumb_current_text' => 'Servicios',
                    'card_button_text' => 'Ver todas',
                    'detail_button_text' => 'Consultar',
                    'detail_button_url' => '/presupuesto',
                ],
                'order' => 1,
                'is_active' => true,
            ]
        );

        $servicesData = [
            [
                'title' => 'Análisis de gases de combustión',
                'slug' => 'analisis-gases-combustion',
                'subtitle' => null,
                'description' => 'Medición y análisis de gases de combustión para optimizar la eficiencia térmica y verificar el correcto funcionamiento de quemadores y calderas.',
                'order' => 'AD',
                'show_in_home' => false,
            ],
            [
                'title' => 'Armado de obra',
                'slug' => 'armado-de-obra',
                'subtitle' => '(Reparaciones)',
                'description' => 'Montaje y armado integral de obra para intervenciones, reparaciones y reemplazos de equipos térmicos e instalaciones asociadas.',
                'order' => 'AE',
                'show_in_home' => false,
            ],
            [
                'title' => 'Aislaciones térmicas',
                'slug' => 'aislaciones-termicas',
                'subtitle' => null,
                'description' => 'Aplicación y renovación de aislaciones térmicas para cañerías, calderas y equipos, reduciendo pérdidas energéticas y mejorando la seguridad.',
                'order' => 'AF',
                'show_in_home' => false,
            ],
            [
                'title' => 'Servicio de quemadores',
                'slug' => 'servicio-de-quemadores',
                'subtitle' => null,
                'description' => 'Mantenimiento, regulación y reparación de quemadores industriales para asegurar combustión eficiente y operación confiable.',
                'order' => 'AG',
                'show_in_home' => false,
            ],
            [
                'title' => 'Medición de espesores',
                'slug' => 'medicion-de-espesores',
                'subtitle' => '(Por ultrasonido)',
                'description' => 'Control de espesores por ultrasonido para evaluar desgaste, corrosión y estado estructural de equipos e instalaciones.',
                'order' => 'AH',
                'show_in_home' => false,
            ],
            [
                'title' => 'Ventas y financiación de equipos modernos',
                'slug' => 'ventas-financiacion-equipos-modernos',
                'subtitle' => null,
                'description' => 'Asesoramiento comercial y opciones de financiación para incorporación de equipos térmicos modernos y soluciones de alta eficiencia.',
                'order' => 'AI',
                'show_in_home' => false,
            ],
            [
                'title' => 'Automatizaciones por temperatura y horario',
                'slug' => 'automatizaciones-por-temperatura-y-horario',
                'subtitle' => null,
                'description' => 'Implementación de automatizaciones para control por temperatura y programación horaria en sistemas de calefacción y generación de calor.',
                'order' => 'AJ',
                'show_in_home' => false,
            ],
            [
                'title' => 'Cañerías de calefacción',
                'slug' => 'canerias-de-calefaccion',
                'subtitle' => null,
                'description' => 'Diseño, montaje y reparación de cañerías de calefacción para instalaciones industriales y comerciales.',
                'order' => 'AK',
                'show_in_home' => false,
            ],
            [
                'title' => 'Habilitación y certificaciones',
                'slug' => 'habilitacion-y-certificaciones',
                'subtitle' => null,
                'description' => 'Gestión técnica y documental para habilitaciones, verificaciones reglamentarias y certificaciones vinculadas a equipos térmicos.',
                'order' => 'AL',
                'show_in_home' => false,
            ],
        ];

        foreach ($servicesData as $data) {
            Service::updateOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, ['is_active' => true])
            );
        }
    }
}
