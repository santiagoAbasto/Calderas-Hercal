<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EquipmentImageController extends Controller
{
    public function store(Request $request, Equipment $equipment)
    {
        $request->validate([
            'image' => 'required|image|max:5120',
            'order' => 'nullable|string|max:10',
        ]);

        $path = $request->file('image')->store('equipments', 'public');

        $image = EquipmentImage::create([
            'equipment_id' => $equipment->id,
            'file_path'    => $path,
            'order'        => $request->order ?? 'A',
            'is_active'    => true,
        ]);

        return response()->json($image->append('image_url'), 201);
    }

    public function destroy(EquipmentImage $equipmentImage)
    {
        if ($equipmentImage->file_path) {
            Storage::disk('public')->delete($equipmentImage->file_path);
        }

        $equipmentImage->delete();

        return response()->json(['ok' => true]);
    }
}
