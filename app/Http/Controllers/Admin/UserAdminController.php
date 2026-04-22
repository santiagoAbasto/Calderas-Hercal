<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class UserAdminController extends Controller
{
    public function index(): Response
    {
        $principalId = User::query()->min('id');

        $users = User::query()
            ->orderBy('id')
            ->get()
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar_url' => $user->avatar_url,
                'can_access_admin' => $user->can_access_admin,
                'created_at' => optional($user->created_at)->format('d/m/Y H:i'),
                'is_principal' => $principalId === $user->id,
            ]);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'principalId' => $principalId,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'can_access_admin' => ['nullable', 'boolean'],
        ]);

        $data = [
            'name' => cms_plain_text($validated['name']),
            'email' => cms_email($validated['email']),
            'can_access_admin' => (bool) ($validated['can_access_admin'] ?? true),
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ];

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('users', 'public');
        }

        User::create($data);

        return back()->with('success', 'Usuario administrador creado correctamente.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_avatar' => ['nullable', 'boolean'],
            'can_access_admin' => ['nullable', 'boolean'],
        ]);

        $canAccessAdmin = (bool) ($validated['can_access_admin'] ?? $user->can_access_admin);
        $principalId = User::query()->min('id');

        if ($principalId === $user->id && ! $canAccessAdmin) {
            throw ValidationException::withMessages([
                'user' => 'El usuario principal no puede perder acceso al panel.',
            ]);
        }

        if (auth()->id() === $user->id && ! $canAccessAdmin) {
            throw ValidationException::withMessages([
                'user' => 'No puedes quitarte tu propio acceso al panel desde esta pantalla.',
            ]);
        }

        $data = [
            'name' => cms_plain_text($validated['name']),
            'email' => cms_email($validated['email']),
            'can_access_admin' => $canAccessAdmin,
        ];

        if ($request->boolean('remove_avatar') && $user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $data['avatar'] = null;
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $data['avatar'] = $request->file('avatar')->store('users', 'public');
        }

        $user->update($data);

        if (! empty($validated['password'])) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        return back()->with('success', 'Usuario administrador actualizado correctamente.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $principalId = User::query()->min('id');

        if ($principalId === $user->id) {
            throw ValidationException::withMessages([
                'user' => 'El usuario principal no puede eliminarse.',
            ]);
        }

        if (auth()->id() === $user->id) {
            throw ValidationException::withMessages([
                'user' => 'No puedes eliminar tu propia cuenta desde esta pantalla.',
            ]);
        }

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return back()->with('success', 'Usuario administrador eliminado correctamente.');
    }
}
