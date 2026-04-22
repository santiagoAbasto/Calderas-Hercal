<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormSubmission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FormSubmissionController extends Controller
{
    public function all(Request $request): Response
    {
        return $this->renderIndex($request, '', route('admin.submissions.index'));
    }

    public function contact(Request $request): Response
    {
        return $this->renderIndex($request, 'contacto', route('admin.submissions.contact'));
    }

    public function quote(Request $request): Response
    {
        return $this->renderIndex($request, 'presupuesto', route('admin.submissions.quote'));
    }

    protected function renderIndex(Request $request, string $forcedType, string $basePath): Response
    {
        $type = $forcedType !== '' ? $forcedType : trim((string) $request->query('type', ''));
        $search = trim((string) $request->query('search', ''));

        $query = FormSubmission::query()->latest();

        if ($type !== '') {
            $query->where('type', $type);
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('type', 'like', '%' . $search . '%')
                    ->orWhere('data', 'like', '%' . $search . '%')
                    ->orWhere('user_agent', 'like', '%' . $search . '%');
            });
        }

        /** @var LengthAwarePaginator $submissions */
        $submissions = $query
            ->paginate(15)
            ->withQueryString();

        $submissions->through(fn (FormSubmission $submission) => $this->serializeSubmissionListItem($submission));

        $types = FormSubmission::query()
            ->select('type')
            ->distinct()
            ->orderBy('type')
            ->pluck('type');

        return Inertia::render('Admin/Submissions/Index', [
            'submissions' => $submissions,
            'filters' => [
                'type' => $type,
                'search' => $search,
            ],
            'types' => $types,
            'unreadCount' => FormSubmission::where('is_read', false)->count(),
            'viewTitle' => $this->viewTitleForType($type),
            'viewDescription' => $this->viewDescriptionForType($type),
            'basePath' => $basePath,
            'lockedType' => $forcedType !== '' ? $forcedType : null,
        ]);
    }

    public function show(FormSubmission $submission): Response
    {
        if (! $submission->is_read) {
            $submission->update(['is_read' => true]);
        }

        return Inertia::render('Admin/Submissions/Show', [
            'submission' => $this->serializeSubmissionDetail($submission),
            'backUrl' => $this->backUrlForType($submission->type),
        ]);
    }

    public function markAsRead(FormSubmission $submission): RedirectResponse
    {
        $submission->update(['is_read' => true]);

        return back()->with('success', 'Solicitud marcada como leida.');
    }

    public function destroy(FormSubmission $submission): RedirectResponse
    {
        $submission->delete();

        return redirect()->route('admin.submissions.index')->with('success', 'Solicitud eliminada.');
    }

    protected function serializeSubmissionListItem(FormSubmission $submission): array
    {
        $data = is_array($submission->data) ? $submission->data : [];

        return [
            'id' => $submission->id,
            'type' => $submission->type,
            'type_label' => $this->typeLabel($submission->type),
            'summary' => $this->buildSummary($submission->type, $data),
            'contact_name' => $this->primaryContactName($data),
            'contact_email' => $this->extractEmail($data),
            'contact_phone' => $this->extractPhone($data),
            'subject' => $this->extractSubject($submission->type, $data),
            'ip' => $submission->ip,
            'is_read' => $submission->is_read,
            'created_at' => $submission->created_at?->toIso8601String(),
        ];
    }

    protected function serializeSubmissionDetail(FormSubmission $submission): array
    {
        $data = is_array($submission->data) ? $submission->data : [];
        $email = $this->extractEmail($data);
        $subject = $this->extractSubject($submission->type, $data);

        return [
            'id' => $submission->id,
            'type' => $submission->type,
            'type_label' => $this->typeLabel($submission->type),
            'fields' => $this->formatFields($submission->type, $data),
            'contact_name' => $this->primaryContactName($data),
            'contact_email' => $email,
            'contact_phone' => $this->extractPhone($data),
            'subject' => $subject,
            'reply_url' => $email ? $this->buildReplyUrl($email, $submission->type, $subject, $submission->id) : null,
            'reply_email' => $email,
            'ip' => $submission->ip,
            'user_agent' => $submission->user_agent,
            'is_read' => $submission->is_read,
            'created_at' => $submission->created_at?->toIso8601String(),
        ];
    }

    protected function formatFields(string $type, array $data): array
    {
        return collect($data)
            ->map(function ($value, $key) use ($type) {
                return [
                    'key' => $key,
                    'label' => $this->fieldLabel($type, (string) $key),
                    'value' => $this->formatFieldValue($value),
                ];
            })
            ->values()
            ->all();
    }

    protected function fieldLabel(string $type, string $key): string
    {
        $labels = [
            'contacto' => [
                'name' => 'Nombre',
                'last_name' => 'Apellido',
                'email' => 'Correo',
                'phone' => 'Teléfono',
                'message' => 'Mensaje',
            ],
            'presupuesto' => [
                'name' => 'Nombre',
                'last_name' => 'Apellido',
                'email' => 'Correo',
                'phone' => 'Teléfono',
                'message' => 'Mensaje',
                'subject_type' => 'Tipo de consulta',
                'subject_slug' => 'Identificador',
                'subject_name' => 'Servicio o equipo',
            ],
        ];

        return $labels[$type][$key] ?? str((string) $key)->replace('_', ' ')->title()->toString();
    }

    protected function formatFieldValue(mixed $value): string
    {
        if (is_array($value)) {
            return collect($value)
                ->map(fn ($item) => is_scalar($item) ? (string) $item : json_encode($item, JSON_UNESCAPED_UNICODE))
                ->implode(', ');
        }

        if (is_bool($value)) {
            return $value ? 'Sí' : 'No';
        }

        if ($value === null || $value === '') {
            return 'Sin datos';
        }

        return (string) $value;
    }

    protected function buildSummary(string $type, array $data): string
    {
        $parts = array_filter([
            $this->primaryContactName($data),
            $this->extractSubject($type, $data),
            $this->extractEmail($data),
        ]);

        return count($parts) > 0
            ? implode(' · ', $parts)
            : 'Solicitud recibida';
    }

    protected function primaryContactName(array $data): ?string
    {
        $name = trim((string) ($data['name'] ?? ''));
        $lastName = trim((string) ($data['last_name'] ?? ''));
        $fullName = trim($name . ' ' . $lastName);

        return $fullName !== '' ? $fullName : null;
    }

    protected function extractEmail(array $data): ?string
    {
        $email = trim((string) ($data['email'] ?? ''));

        return $email !== '' ? $email : null;
    }

    protected function extractPhone(array $data): ?string
    {
        $phone = trim((string) ($data['phone'] ?? ''));

        return $phone !== '' ? $phone : null;
    }

    protected function extractSubject(string $type, array $data): ?string
    {
        if ($type === 'presupuesto') {
            $subjectName = trim((string) ($data['subject_name'] ?? ''));

            if ($subjectName !== '') {
                return $subjectName;
            }

            $subjectType = trim((string) ($data['subject_type'] ?? ''));

            return $subjectType !== '' ? str($subjectType)->replace('_', ' ')->title()->toString() : null;
        }

        return null;
    }

    protected function buildReplyUrl(string $email, string $type, ?string $subjectName, int $submissionId): string
    {
        $subject = match ($type) {
            'contacto' => 'Respuesta a tu consulta',
            'presupuesto' => 'Respuesta a tu solicitud de presupuesto',
            default => 'Respuesta a tu solicitud',
        };

        if ($subjectName) {
            $subject .= ' - ' . $subjectName;
        }

        $body = "Hola,\n\nTe escribimos en respuesta a la solicitud #{$submissionId} recibida desde la web de Calderas Hercal.\n\n";

        return 'mailto:' . rawurlencode($email)
            . '?subject=' . rawurlencode($subject)
            . '&body=' . rawurlencode($body);
    }

    protected function typeLabel(string $type): string
    {
        return match ($type) {
            'contacto' => 'Contacto',
            'presupuesto' => 'Presupuesto',
            default => str($type)->replace('_', ' ')->title()->toString(),
        };
    }

    protected function viewTitleForType(string $type): string
    {
        return match ($type) {
            'contacto' => 'Solicitudes de contacto',
            'presupuesto' => 'Solicitudes de presupuesto',
            default => 'Todas las solicitudes',
        };
    }

    protected function viewDescriptionForType(string $type): string
    {
        return match ($type) {
            'contacto' => 'Mensajes que llegan desde el formulario de contacto de la web.',
            'presupuesto' => 'Consultas que llegan desde pedir presupuesto en servicios y equipos.',
            default => 'Bandeja general con todas las solicitudes recibidas desde la web.',
        };
    }

    protected function backUrlForType(string $type): string
    {
        return match ($type) {
            'contacto' => route('admin.submissions.contact'),
            'presupuesto' => route('admin.submissions.quote'),
            default => route('admin.submissions.index'),
        };
    }
}
