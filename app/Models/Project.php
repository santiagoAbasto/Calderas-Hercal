<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'cover_image',
        'order',
        'is_active',
        'show_in_home',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_in_home' => 'boolean',
    ];

    public function images()
    {
        return $this->hasMany(ProjectImage::class)->orderBy('order');
    }

    public function getCoverImageUrlAttribute()
    {
        return $this->cover_image
            ? cms_media_url($this->cover_image)
            : asset('images/placeholder.png');
    }
}
