<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Database\Seeder;

class NosotrosSeeder extends Seeder
{
    public function run(): void
    {
        // ── Página ────────────────────────────────────────────────────────────
        $page = Page::updateOrCreate(
            ['slug' => 'nosotros'],
            ['name' => 'Nosotros', 'is_active' => true]
        );

        // ── Secciones ─────────────────────────────────────────────────────────
        $sections = [

            // Banner principal (imagen / video local / YouTube)
            [
                'section_key' => 'nosotros_banner',
                'title'       => 'Nosotros',
                'subtitle'    => null,
                'description' => null,
                'meta'        => null,   // El admin sube el media desde el panel
                'order'       => 1,
            ],

            // Sección about: imagen + texto
            [
                'section_key' => 'nosotros_about',
                'title'       => 'Seguridad, eficiencia y confianza',
                'subtitle'    => null,
                'description' => "Calderas Hercal es un grupo industrial dedicado a la fabricación de calderas de tubos de humo para generación de vapor y agua sobrecalentada, generadores de agua caliente de consumo, calentadores de aceite térmico y sus equipos auxiliares.\n\nFabricamos las calderas más seguras, de mayor eficiencia, de mayor confiabilidad en su operación y de mayor vida útil del mercado, utilizando únicamente tecnología de punta a nivel mundial, con los mejores materiales que existen en el mercado internacional.\n\nJustificar la Confianza Depositada es nuestro lema de ventas, con el respaldo del excelente prestigio de nuestra marca.",
                'meta'        => null,   // El admin sube la imagen desde el panel
                'order'       => 2,
            ],

            // Misión
            [
                'section_key' => 'nosotros_mision',
                'title'       => 'Misión',
                'subtitle'    => null,
                'description' => 'Brindar soluciones eficientes en calderas y sistemas termomecánicos, ofreciendo productos y servicios confiables que optimicen el funcionamiento de cada instalación, priorizando la calidad técnica, la seguridad y la satisfacción de nuestros clientes.',
                'meta'        => null,
                'order'       => 3,
            ],

            // Visión
            [
                'section_key' => 'nosotros_vision',
                'title'       => 'Visión',
                'subtitle'    => null,
                'description' => 'Consolidarnos como una empresa referente en soluciones de calefacción y generación de calor, reconocida por su trayectoria, innovación y capacidad para adaptarse a las necesidades del sector industrial y residencial.',
                'meta'        => null,
                'order'       => 4,
            ],

            // Valores
            [
                'section_key' => 'nosotros_valores',
                'title'       => 'Valores',
                'subtitle'    => null,
                'description' => 'Calidad en cada trabajo, compromiso con el cliente, responsabilidad técnica, mejora continua, trabajo en equipo, innovación en soluciones energéticas y transparencia en cada servicio que brindamos.',
                'meta'        => null,
                'order'       => 5,
            ],
        ];

        foreach ($sections as $data) {
            PageSection::updateOrCreate(
                [
                    'page_id'     => $page->id,
                    'section_key' => $data['section_key'],
                ],
                array_merge($data, [
                    'page_id'   => $page->id,
                    'is_active' => true,
                ])
            );
        }

        $this->command->info('NosotrosSeeder completed successfully.');
    }
}
