<?php

namespace Database\Seeders;

use App\Models\EquipmentCategory;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\Project;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Equipment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HomeSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. SETTINGS ──────────────────────────────────────────────────────
        $settings = [
            ['key' => 'email',     'value' => 'hercal.ter@gmail.com',   'group' => 'contact'],
            ['key' => 'phone_1',   'value' => '+54 11 2116 5379',        'group' => 'contact'],
            ['key' => 'phone_2',   'value' => '+54 11 3337 4083',        'group' => 'contact'],
            ['key' => 'whatsapp',  'value' => '541121165379',            'group' => 'contact'],
            ['key' => 'instagram', 'value' => 'https://www.instagram.com/calderashercal/', 'group' => 'social'],
        ];

        foreach ($settings as $data) {
            Setting::updateOrCreate(['key' => $data['key']], $data);
        }

        // ── 2. SERVICES ───────────────────────────────────────────────────────
        $servicesData = [
            [
                'title'       => 'Corte y retiro de equipos en desuso',
                'slug'        => 'corte-retiro-equipos',
                'subtitle'    => null,
                'description' => 'Brindamos un servicio seguro y eficiente de corte y retiro de instalaciones en desuso, especialmente calderas y sistemas asociados. Nos encargamos de todo el proceso, desde la desconexión controlada hasta el desmontaje y disposición final, cumpliendo con las normativas vigentes. Garantizamos trabajos rápidos, ordenados y con mínimo impacto operativo, aportando soluciones confiables para la renovación y optimización de sus instalaciones.',
                'order'       => 'A',
                'show_in_home'=> true,
            ],
            [
                'title'       => 'Soldaduras',
                'slug'        => 'soldaduras',
                'subtitle'    => '(De calderas y termotanques)',
                'description' => 'Servicios especializados de soldadura para calderas y termotanques industriales, garantizando estanqueidad y cumplimiento de normas de seguridad.',
                'order'       => 'AA',
                'show_in_home'=> true,
            ],
            [
                'title'       => 'Reparaciones integrales',
                'slug'        => 'reparaciones-integrales',
                'subtitle'    => '(De calderas y termotanques)',
                'description' => 'Diagnóstico y reparación integral de calderas y termotanques. Reemplazamos componentes, optimizamos el rendimiento y extendemos la vida útil del equipo.',
                'order'       => 'AB',
                'show_in_home'=> true,
            ],
            [
                'title'       => 'Instalación de radiadores',
                'slug'        => 'instalacion-radiadores',
                'subtitle'    => null,
                'description' => 'Instalación profesional de sistemas de calefacción por radiadores, con diseño hidráulico y cálculo de cargas térmicas.',
                'order'       => 'AC',
                'show_in_home'=> true,
            ],
        ];

        foreach ($servicesData as $data) {
            Service::updateOrCreate(['slug' => $data['slug']], array_merge($data, ['is_active' => true]));
        }

        // ── 3. EQUIPMENT CATEGORIES & EQUIPMENT ──────────────────────────────
        $categoriesData = [
            ['name' => 'Calderas',                         'slug' => 'calderas',                          'order' => 1],
            ['name' => 'Termotanques',                     'slug' => 'termotanques',                      'order' => 2],
            ['name' => 'Tanques intermediario de agua caliente', 'slug' => 'tanques-intermediario',       'order' => 3],
        ];

        $equipmentData = [
            [
                'category_slug' => 'calderas',
                'title'         => 'Calderas',
                'slug'          => 'calderas',
                'description'   => 'Calderas industriales de vapor y agua caliente de alta eficiencia. Fabricadas con materiales de primera calidad y bajo normas IRAM e internacionales.',
                'features'      => null,
                'is_catalog_root' => true,
                'show_in_home'  => true,
                'order'         => 1,
            ],
            [
                'category_slug' => 'termotanques',
                'title'         => 'Termotanques',
                'slug'          => 'termotanques',
                'description'   => 'Termotanques verticales y horizontales para agua caliente sanitaria e industrial. Alta capacidad y excelente aislamiento térmico.',
                'features'      => null,
                'is_catalog_root' => true,
                'show_in_home'  => true,
                'order'         => 2,
            ],
            [
                'category_slug' => 'tanques-intermediario',
                'title'         => 'Tanques intermediario de agua caliente',
                'slug'          => 'tanques-intermediario',
                'description'   => 'Tanques intermediarios para sistemas de agua caliente industrial. Permiten almacenamiento y distribución eficiente en grandes instalaciones.',
                'features'      => null,
                'is_catalog_root' => true,
                'show_in_home'  => true,
                'order'         => 3,
            ],
            [
                'category_slug' => 'calderas',
                'title'         => 'Caldera de vapor',
                'slug'          => 'caldera-de-vapor',
                'description'   => 'Una caldera de vapor es un equipo utilizado para la calefacción por radiadores, generar vapor de agua para distribuir calor de forma eficiente en distintos ambientes. A través de un sistema de tuberías, el vapor llega a los radiadores, donde libera su energía térmica, brindando una calefacción uniforme y confortable.',
                'features'      => [
                    'Rendimiento confiable',
                    'Rápida transmisión de calor',
                    'Adaptabilidad a instalaciones residenciales o comerciales',
                    'Cuenta con dispositivos de control y seguridad',
                ],
                'is_catalog_root' => false,
                'show_in_home'  => false,
                'order'         => 10,
            ],
            [
                'category_slug' => 'calderas',
                'title'         => 'Caldera de agua',
                'slug'          => 'caldera-de-agua',
                'description'   => 'Equipo diseñado para producir agua caliente de forma segura y continua, ideal para procesos industriales y sistemas de climatización de alto rendimiento.',
                'features'      => [
                    'Operación estable y segura',
                    'Distribución térmica uniforme',
                    'Alta eficiencia energética',
                    'Mantenimiento accesible',
                ],
                'is_catalog_root' => false,
                'show_in_home'  => false,
                'order'         => 11,
            ],
            [
                'category_slug' => 'termotanques',
                'title'         => 'Termotanque horizontal',
                'slug'          => 'termotanque-horizontal',
                'description'   => 'Solución robusta para acumulación y suministro de agua caliente en instalaciones de gran demanda, con excelente capacidad de recuperación térmica.',
                'features'      => [
                    'Gran capacidad de almacenamiento',
                    'Excelente aislación térmica',
                    'Construcción industrial reforzada',
                    'Fácil integración al sistema existente',
                ],
                'is_catalog_root' => false,
                'show_in_home'  => false,
                'order'         => 20,
            ],
            [
                'category_slug' => 'termotanques',
                'title'         => 'Termotanque vertical',
                'slug'          => 'termotanque-vertical',
                'description'   => 'Equipo compacto y eficiente para abastecimiento constante de agua caliente, optimizando el espacio disponible en sala técnica.',
                'features'      => [
                    'Aprovechamiento vertical del espacio',
                    'Recuperación térmica eficiente',
                    'Operación confiable',
                    'Fácil mantenimiento',
                ],
                'is_catalog_root' => false,
                'show_in_home'  => false,
                'order'         => 21,
            ],
            [
                'category_slug' => 'tanques-intermediario',
                'title'         => 'Tanque intermediario horizontal',
                'slug'          => 'tanque-intermediario-horizontal',
                'description'   => 'Sistema de acumulación intermedia para estabilizar la provisión de agua caliente y acompañar instalaciones de uso intensivo.',
                'features'      => [
                    'Gran volumen útil',
                    'Estabilidad térmica',
                    'Construcción duradera',
                    'Adaptación a múltiples configuraciones',
                ],
                'is_catalog_root' => false,
                'show_in_home'  => false,
                'order'         => 30,
            ],
            [
                'category_slug' => 'tanques-intermediario',
                'title'         => 'Tanque intermediario vertical',
                'slug'          => 'tanque-intermediario-vertical',
                'description'   => 'Configuración vertical para almacenamiento eficiente de agua caliente, optimizando superficie ocupada y continuidad de servicio.',
                'features'      => [
                    'Diseño compacto',
                    'Optimización del espacio',
                    'Aislamiento térmico eficiente',
                    'Funcionamiento confiable',
                ],
                'is_catalog_root' => false,
                'show_in_home'  => false,
                'order'         => 31,
            ],
        ];

        foreach ($categoriesData as $catData) {
            $cat = EquipmentCategory::updateOrCreate(
                ['slug' => $catData['slug']],
                array_merge($catData, ['is_active' => true])
            );
        }

        Equipment::whereNull('slug')->update([
            'is_active' => false,
            'is_catalog_root' => false,
            'show_in_home' => false,
        ]);

        foreach ($equipmentData as $eqData) {
            $cat = EquipmentCategory::where('slug', $eqData['category_slug'])->first();
            if ($cat) {
                Equipment::updateOrCreate(
                    [
                        'equipment_category_id' => $cat->id,
                        'slug'                  => $eqData['slug'],
                    ],
                    [
                        'equipment_category_id' => $cat->id,
                        'title'                 => $eqData['title'],
                        'slug'                  => $eqData['slug'],
                        'description'           => $eqData['description'],
                        'features'              => $eqData['features'],
                        'is_catalog_root'       => $eqData['is_catalog_root'],
                        'show_in_home'          => $eqData['show_in_home'],
                        'order'                 => $eqData['order'],
                        'is_active'             => true,
                    ]
                );
            }
        }

        // ── 4. PROJECTS ───────────────────────────────────────────────────────
        $projectsData = [
            ['legacy_order' => '1', 'title' => 'Caldera de vapor', 'slug' => 'caldera-de-vapor', 'description' => 'Instalación de caldera de vapor industrial de alta presión.', 'order' => 'A', 'show_in_home' => true],
            ['legacy_order' => '2', 'title' => 'Termotanque horizontal', 'slug' => 'termotanque-horizontal', 'description' => 'Fabricación e instalación de termotanque horizontal de gran capacidad.', 'order' => 'AA', 'show_in_home' => true],
            ['legacy_order' => '3', 'title' => 'Caldera de agua', 'slug' => 'caldera-de-agua-2', 'description' => 'Caldera de agua caliente para sistema de calefacción central.', 'order' => 'AB', 'show_in_home' => true],
            ['legacy_order' => '4', 'title' => 'Caldera', 'slug' => 'caldera-2', 'description' => 'Revisión y puesta en marcha de caldera industrial.', 'order' => 'AC', 'show_in_home' => true],
            ['legacy_order' => '5', 'title' => 'Termotanque', 'slug' => 'termotanque', 'description' => 'Instalación y puesta en marcha de termotanque industrial.', 'order' => 'B', 'show_in_home' => false],
            ['legacy_order' => '6', 'title' => 'Caldera de agua', 'slug' => 'caldera-de-agua-1', 'description' => 'Servicio sobre caldera de agua para uso industrial y calefacción.', 'order' => 'BA', 'show_in_home' => false],
            ['legacy_order' => '7', 'title' => 'Caldera', 'slug' => 'caldera-1', 'description' => 'Trabajo realizado sobre caldera industrial con mejoras operativas.', 'order' => 'BB', 'show_in_home' => false],
            ['legacy_order' => '8', 'title' => 'Termotanque de gran volumen', 'slug' => 'termotanque-de-gran-volumen', 'description' => 'Instalación de termotanque de gran volumen para alta demanda.', 'order' => 'BC', 'show_in_home' => false],
        ];

        $projectSlugs = collect($projectsData)->pluck('slug')->all();

        Project::query()->update(['show_in_home' => false]);

        foreach ($projectsData as $data) {
            $project = Project::query()
                ->where('slug', $data['slug'])
                ->orWhere(function ($query) use ($data) {
                    $query->whereNull('slug')
                        ->where('order', $data['legacy_order']);
                })
                ->orWhere(function ($query) use ($data) {
                    $query->whereNull('slug')
                        ->where('title', $data['title'])
                        ->where('order', $data['legacy_order']);
                })
                ->first();

            if (!$project) {
                $project = new Project();
            }

            $project->fill([
                'title' => $data['title'],
                'slug' => $data['slug'],
                'description' => $data['description'],
                'order' => $data['order'],
                'show_in_home' => $data['show_in_home'],
                'is_active' => true,
            ]);

            $project->save();
        }

        Project::whereNotIn('slug', $projectSlugs)
            ->whereNotNull('slug')
            ->update([
                'show_in_home' => false,
            ]);

        // ── 5. HOME PAGE (CMS) ────────────────────────────────────────────────
        $page = Page::updateOrCreate(
            ['slug' => 'home'],
            ['name' => 'Home', 'is_active' => true]
        );

        $sections = [
            [
                'section_key' => 'hero',
                'title'       => 'Calderas y termotanques de última generación',
                'subtitle'    => null,
                'description' => null,
                'meta'        => [
                    'button_text' => 'Ver equipos',
                    'button_url'  => '/equipos',
                ],
                'order'       => 1,
            ],
            [
                'section_key' => 'equipments',
                'title'       => null,
                'subtitle'    => null,
                'description' => null,
                'meta'        => null,
                'order'       => 2,
            ],
            [
                'section_key' => 'hero_banner',
                'title'       => 'Corte y retiro de equipos en desuso',
                'subtitle'    => 'Ampliamos nuestros servicios',
                'description' => 'Realizamos el corte, desmontaje y retiro seguro de calderas e instalaciones en desuso, cumpliendo normativos y garantizando eficiencia, orden y mínimo impacto en la operación.',
                'meta'        => [
                    'button_text' => 'Consultar',
                    'button_url'  => '/contacto',
                ],
                'order'       => 3,
            ],
            [
                'section_key' => 'services',
                'title'       => 'Nuestros servicios',
                'subtitle'    => null,
                'description' => null,
                'meta'        => null,
                'order'       => 4,
            ],
            [
                'section_key' => 'about_preview',
                'title'       => 'Seguridad, eficiencia y confianza',
                'subtitle'    => null,
                'description' => 'Fabricamos las calderas más seguras, de mayor eficiencia, de mayor confiabilidad en su operación y de mayor vida útil del mercado, utilizando únicamente tecnología de punta a nivel mundial, con los mejores materiales que existen en el mercado internacional.',
                'meta'        => [
                    'years'             => 35,
                    'clients'           => 3000,
                    'projects'          => 2500,
                    'button_text'       => 'Ver más',
                    'button_url'        => '/nosotros',
                    'description_extra' => 'Justificar la Confianza Depositada es nuestro lema de ventas, con el respaldo del excelente prestigio de nuestra marca.',
                ],
                'order'       => 5,
            ],
            [
                'section_key' => 'projects',
                'title'       => 'Trabajos realizados',
                'subtitle'    => null,
                'description' => null,
                'meta'        => null,
                'order'       => 6,
            ],
        ];

        foreach ($sections as $sectionData) {
            PageSection::updateOrCreate(
                [
                    'page_id'     => $page->id,
                    'section_key' => $sectionData['section_key'],
                ],
                array_merge($sectionData, [
                    'page_id'   => $page->id,
                    'is_active' => true,
                ])
            );
        }

        $this->command->info('HomeSeeder completed successfully.');
    }
}
