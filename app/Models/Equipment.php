<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Equipment extends Model
{
    protected $table = 'equipments';

    protected $fillable = [
        'equipment_category_id',
        'title',
        'slug',
        'description',
        'main_image',
        'features',
        'is_catalog_root',
        'show_in_home',
        'order',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array',
        'is_catalog_root' => 'boolean',
        'show_in_home' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(EquipmentCategory::class, 'equipment_category_id');
    }

    public function images()
    {
        return $this->hasMany(EquipmentImage::class)->orderBy('order');
    }

    public function getMainImageUrlAttribute()
    {
        if ($this->main_image && Storage::disk('public')->exists($this->main_image)) {
            return cms_media_url($this->main_image);
        }

        return asset('images/placeholder-equipment.svg');
    }
}
