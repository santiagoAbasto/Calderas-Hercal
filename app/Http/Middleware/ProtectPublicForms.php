<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ProtectPublicForms
{
    public function handle(Request $request, Closure $next): Response
    {
        $honeypotField = (string) config('security.forms.honeypot_field', 'company');
        $honeypotValue = trim((string) $request->input($honeypotField));

        if ($honeypotValue !== '') {
            Log::warning('Blocked public form submission by honeypot.', [
                'route' => $request->route()?->getName(),
                'ip' => $request->ip(),
                'email' => Str::lower(trim((string) $request->input('email'))),
            ]);

            return $this->fakeSuccessResponse();
        }

        $email = Str::lower(trim((string) $request->input('email')));
        $key = sprintf(
            'public-form:%s:%s:%s',
            $request->route()?->getName() ?? $request->path(),
            $email !== '' ? $email : 'guest',
            $request->ip()
        );

        $maxAttempts = (int) config('security.forms.max_attempts', 5);
        $decaySeconds = (int) config('security.forms.decay_seconds', 60);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            Log::warning('Blocked public form submission by form rate limiter.', [
                'route' => $request->route()?->getName(),
                'ip' => $request->ip(),
                'email' => $email,
            ]);

            throw ValidationException::withMessages([
                'email' => 'Has realizado demasiados envios. Intenta nuevamente en unos minutos.',
            ]);
        }

        RateLimiter::hit($key, $decaySeconds);

        return $next($request);
    }

    protected function fakeSuccessResponse(): RedirectResponse
    {
        return redirect()->back()->with('success', 'Tu solicitud fue enviada correctamente. Te contactaremos a la brevedad.');
    }
}
