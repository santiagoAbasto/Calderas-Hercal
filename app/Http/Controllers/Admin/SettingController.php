<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function whatsapp(): Response
    {
        $value = Setting::get('whatsapp', '');

        return Inertia::render('Admin/Settings/Whatsapp', [
            'whatsapp' => [
                'value' => $value,
                'preview_url' => $value
                    ? 'https://wa.me/'.preg_replace('/\D/', '', $value)
                    : null,
            ],
        ]);
    }

    public function updateWhatsapp(Request $request)
    {
        $data = $request->validate([
            'value' => ['nullable', 'string', 'max:40', 'regex:/^[0-9+\s\-\(\)]+$/'],
        ], [
            'value.regex' => 'Ingresa un número válido con código de país.',
        ]);

        Setting::updateOrCreate(
            ['key' => 'whatsapp'],
            [
                'value' => cms_plain_text($data['value'] ?? null),
                'group' => 'contact',
            ]
        );

        return back()->with('success', 'Whatsapp flotante actualizado.');
    }

    /**
     * Retorna todos los settings agrupados por 'group'.
     */
    public function index()
    {
        $settings = Setting::orderBy('group')->orderBy('key')->get();

        $grouped = $settings->groupBy('group');

        return response()->json($grouped);
    }

    /**
     * Crea o actualiza un setting por su key.
     *
     * Request body:
     *   { key: string, value: mixed, group: string }
     *
     * Para actualizar múltiples a la vez, se puede enviar un array:
     *   { settings: [ { key, value, group }, ... ] }
     */
    public function upsert(Request $request)
    {
        $request->validate([
            'settings'         => 'sometimes|array',
            'settings.*.key'   => 'required|string|max:255',
            'settings.*.value' => 'nullable',
            'settings.*.group' => 'nullable|string|max:100',
            // Soporte single-item también
            'key'   => 'required_without:settings|string|max:255',
            'value' => 'nullable',
            'group' => 'nullable|string|max:100',
        ]);

        if ($request->has('settings')) {
            $results = [];
            foreach ($request->settings as $item) {
                $results[] = Setting::updateOrCreate(
                    ['key' => $item['key']],
                    [
                        'value' => cms_plain_text(isset($item['value']) ? (string) $item['value'] : null),
                        'group' => cms_plain_text($item['group'] ?? null),
                    ]
                );
            }
            return response()->json($results);
        }

        $setting = Setting::updateOrCreate(
            ['key' => $request->key],
            [
                'value' => cms_plain_text($request->filled('value') ? (string) $request->value : null),
                'group' => cms_plain_text($request->group),
            ]
        );

        return response()->json($setting);
    }
}
