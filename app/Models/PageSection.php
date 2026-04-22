<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageSection extends Model
{
    protected $fillable = [
        'page_id',
        'section_key',
        'title',
        'subtitle',
        'description',
        'meta',
        'order',
        'is_active',
    ];

    protected $casts = [
        'meta' => 'array',
        'is_active' => 'boolean',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function media()
    {
        return $this->hasMany(PageSectionMedia::class)->orderBy('order');
    }

    public function activeMedia()
    {
        return $this->media()->where('is_active', true);
    }
}