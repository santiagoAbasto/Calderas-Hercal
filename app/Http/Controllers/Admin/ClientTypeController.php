<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ClientTypeController extends Controller
{
    public function index(): Response
    {
        $clients = ClientType::query()
            ->orderBy('order')
            ->get()
            ->map(fn (ClientType $client) => [
                'id' => $client->id,
                'name' => $client->name,
                'slug' => $client->slug,
                'icon_url' => $client->icon_url,
                'icon_width' => $client->icon_width,
                'icon_height' => $client->icon_height,
                'order' => $client->order,
                'is_active' => (bool) $client->is_active,
            ]);

        return Inertia::render('Admin/ClientTypes/Index', [
            'clients' => $clients,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:client_types,slug',
            'icon'        => 'nullable|file|mimes:svg,png,jpg,jpeg,webp|max:2048',
            'icon_width'  => 'nullable|integer|min:1|max:1000',
            'icon_height' => 'nullable|integer|min:1|max:1000',
            'order'       => 'nullable|string|max:10',
        ]);

        $data = [
            'name'        => cms_plain_text($request->name),
            'slug'        => cms_plain_text($request->slug) ?: Str::slug(cms_plain_text($request->name)),
            'icon_width'  => $request->integer('icon_width') ?: null,
            'icon_height' => $request->integer('icon_height') ?: null,
            'order'       => cms_plain_text($request->order) ?? 'A',
            'is_active'   => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('client-types', 'public');
        }

        $client = ClientType::create($data);

        return redirect()
            ->route('admin.client-types.index')
            ->with('success', 'Tipo de cliente creado correctamente.');
    }

    public function update(Request $request, ClientType $clientType): RedirectResponse
    {
        $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:client_types,slug,' . $clientType->id,
            'icon'        => 'nullable|file|mimes:svg,png,jpg,jpeg,webp|max:2048',
            'icon_width'  => 'nullable|integer|min:1|max:1000',
            'icon_height' => 'nullable|integer|min:1|max:1000',
            'order'       => 'nullable|string|max:10',
        ]);

        $data = $request->only('icon_width', 'icon_height', 'is_active');

        if ($request->exists('name')) {
            $data['name'] = cms_plain_text($request->name);
        }

        if ($request->exists('slug')) {
            $data['slug'] = cms_plain_text($request->slug);
        }

        if ($request->exists('order')) {
            $data['order'] = cms_plain_text($request->order) ?? $clientType->order;
        }

        if (array_key_exists('name', $data) && !array_key_exists('slug', $data)) {
            $data['slug'] = Str::slug($data['name']);
        }

        if ($request->hasFile('icon')) {
            // Eliminar icono anterior si existe
            if ($clientType->icon) {
                Storage::disk('public')->delete($clientType->icon);
            }
            $data['icon'] = $request->file('icon')->store('client-types', 'public');
        }

        $clientType->update($data);

        return redirect()
            ->route('admin.client-types.index')
            ->with('success', 'Tipo de cliente actualizado correctamente.');
    }

    public function destroy(ClientType $clientType): RedirectResponse
    {
        if ($clientType->icon) {
            Storage::disk('public')->delete($clientType->icon);
        }

        $clientType->delete();

        return redirect()
            ->route('admin.client-types.index')
            ->with('success', 'Tipo de cliente eliminado correctamente.');
    }
}
