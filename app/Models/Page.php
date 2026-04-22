<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'robots',
        'og_image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function sections()
    {
        return $this->hasMany(PageSection::class)->orderBy('order');
    }

    public function activeSections()
    {
        return $this->sections()->where('is_active', true);
    }

    public function getOgImageUrlAttribute(): ?string
    {
        return $this->og_image
            ? cms_media_url($this->og_image)
            : null;
    }
}
