<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Project;

class ProjectController extends Controller
{
    public function index()
    {
        $page = Page::where('slug', 'trabajos-realizados')
            ->where('is_active', true)
            ->with(['sections' => function ($query) {
                $query->where('is_active', true)->orderBy('order');
            }, 'sections.activeMedia'])
            ->first();

        $projects = Project::where('is_active', true)
            ->whereNotNull('slug')
            ->with('images')
            ->orderBy('order')
            ->get();

        return view('web.projects.index', compact('page', 'projects'));
    }

    public function show($id)
    {
        $project = Project::with('images')
            ->where('is_active', true)
            ->findOrFail($id);

        return view('web.projects.show', compact('project'));
    }
}
