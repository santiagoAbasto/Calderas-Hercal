<?php

namespace App\Support;

use App\Models\Page;
use Illuminate\Http\Request;

class SeoPageResolver
{
    protected const ROUTE_PAGE_MAP = [
        'home' => 'home',
        'nosotros' => 'nosotros',
        'servicios.index' => 'servicios',
        'servicios.show' => 'servicios',
        'equipos.index' => 'equipos',
        'equipos.show' => 'equipos',
        'proyectos.index' => 'trabajos-realizados',
        'proyectos.show' => 'trabajos-realizados',
        'clientes' => 'clientes',
        'contacto' => 'contacto',
        'contacto.store' => 'contacto',
        'presupuesto' => 'presupuesto',
        'presupuesto.store' => 'presupuesto',
    ];

    public static function resolve(Request $request): ?Page
    {
        $routeName = $request->route()?->getName();

        if (! $routeName) {
            return null;
        }

        $slug = self::ROUTE_PAGE_MAP[$routeName] ?? null;

        if (! $slug) {
            return null;
        }

        return Page::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();
    }
}
