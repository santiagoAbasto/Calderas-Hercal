<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\FormSubmission;
use App\Models\Project;
use App\Models\Service;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'services' => Service::count(),
            'equipments' => Equipment::count(),
            'projects' => Project::count(),
            'submissions' => FormSubmission::count(),
            'unread_submissions' => FormSubmission::where('is_read', false)->count(),
        ];

        $recent_submissions = FormSubmission::latest()
            ->limit(5)
            ->get()
            ->map(fn (FormSubmission $submission) => [
                'id' => $submission->id,
                'type' => $submission->type,
                'name' => $submission->data['name'] ?? 'Sin nombre',
                'email' => $submission->data['email'] ?? '-',
                'message' => $submission->data['message'] ?? '-',
                'is_read' => $submission->is_read,
                'created_at' => $submission->created_at?->diffForHumans(),
            ]);

        return Inertia::render('Admin/Dashboard', compact('stats', 'recent_submissions'));
    }
}
