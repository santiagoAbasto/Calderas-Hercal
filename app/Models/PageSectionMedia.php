<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageSectionMedia extends Model
{
    protected $fillable = [
        'page_section_id',
        'type',
        'file_path',
        'youtube_url',
        'title',
        'description',
        'button_text',
        'button_url',
        'alt_text',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function section()
    {
        return $this->belongsTo(PageSection::class, 'page_section_id');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getFileUrlAttribute()
    {
        return $this->file_path
            ? cms_media_url($this->file_path)
            : null;
    }

    public function getYoutubeEmbedAttribute()
    {
        if (!$this->youtube_url) return null;

        preg_match(
            '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/',
            $this->youtube_url,
            $matches
        );

        return isset($matches[1])
            ? 'https://www.youtube.com/embed/' . $matches[1]
            : null;
    }
}
