<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'subtitle',
        'description',
        'image',
        'order',
        'show_in_home',
        'is_active',
    ];

    protected $casts = [
        'show_in_home' => 'boolean',
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getImageUrlAttribute()
    {
        return $this->image
            ? cms_media_url($this->image)
            : asset('images/placeholder.png');
    }
}
