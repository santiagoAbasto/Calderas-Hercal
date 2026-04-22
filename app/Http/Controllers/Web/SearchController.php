<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->string('q'));

        $services = collect();
        $equipments = collect();
        $projects = collect();

        if ($q !== '') {
            $like = '%' . $q . '%';

            $services = Service::query()
                ->where('is_active', true)
                ->where(function ($query) use ($like) {
                    $query->where('title', 'like', $like)
                        ->orWhere('subtitle', 'like', $like)
                        ->orWhere('description', 'like', $like);
                })
                ->orderBy('order')
                ->get();

            $equipments = Equipment::query()
                ->where('is_active', true)
                ->whereNotNull('slug')
                ->where(function ($query) use ($like) {
                    $query->where('title', 'like', $like)
                        ->orWhere('description', 'like', $like)
                        ->orWhere('features', 'like', $like);
                })
                ->orderBy('order')
                ->get();

            $projects = Project::query()
                ->with(['images' => fn ($query) => $query->where('is_active', true)->orderBy('order')])
                ->where('is_active', true)
                ->where(function ($query) use ($like) {
                    $query->where('title', 'like', $like)
                        ->orWhere('description', 'like', $like);
                })
                ->orderBy('order')
                ->get();
        }

        return view('web.search.index', [
            'q' => $q,
            'services' => $services,
            'equipments' => $equipments,
            'projects' => $projects,
            'totalResults' => $services->count() + $equipments->count() + $projects->count(),
        ]);
    }
}
