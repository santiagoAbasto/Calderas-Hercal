<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Database\Seeder;

class ContactoPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::updateOrCreate(
            ['slug' => 'contacto'],
            ['name' => 'Contacto', 'is_active' => true]
        );

        PageSection::updateOrCreate(
            [
                'page_id' => $page->id,
                'section_key' => 'contacto_banner',
            ],
            [
                'page_id' => $page->id,
                'section_key' => 'contacto_banner',
                'title' => 'Contacto',
                'subtitle' => null,
                'description' => null,
                'meta' => [
                    'seo_description' => 'Contacto Calderas Hercal.',
                    'breadcrumb_home_text' => 'Inicio',
                    'breadcrumb_current_text' => 'Contacto',
                ],
                'order' => 1,
                'is_active' => true,
            ]
        );

        PageSection::updateOrCreate(
            [
                'page_id' => $page->id,
                'section_key' => 'contacto_content',
            ],
            [
                'page_id' => $page->id,
                'section_key' => 'contacto_content',
                'title' => 'Póngase en contacto con nosotros',
                'subtitle' => null,
                'description' => null,
                'meta' => [
                    'email_title' => 'Email',
                    'phone_title' => 'Teléfono',
                    'contact_emails' => [
                        'hercal.ter@gmail.com',
                    ],
                    'contact_phones' => [
                        ['number' => '+54 11 2116 5379'],
                        ['number' => '+54 11 3337 4083'],
                    ],
                    'first_name_label' => 'Nombre*',
                    'last_name_label' => 'Apellido*',
                    'email_label' => 'Email*',
                    'phone_label' => 'Teléfono*',
                    'message_label' => 'Mensaje',
                    'button_text' => 'Enviar mensaje',
                ],
                'order' => 2,
                'is_active' => true,
            ]
        );
    }
}
