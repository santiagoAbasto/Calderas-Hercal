<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectImageController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $request->validate([
            'image' => 'required|image|max:5120',
            'order' => 'nullable|string|max:10',
        ]);

        $path = $request->file('image')->store('projects', 'public');

        $image = ProjectImage::create([
            'project_id' => $project->id,
            'file_path'  => $path,
            'order'      => $request->order ?? 'A',
            'is_active'  => true,
        ]);

        return response()->json($image->append('image_url'), 201);
    }

    public function destroy(ProjectImage $projectImage)
    {
        if ($projectImage->file_path) {
            Storage::disk('public')->delete($projectImage->file_path);
        }

        $projectImage->delete();

        return response()->json(['ok' => true]);
    }
}
