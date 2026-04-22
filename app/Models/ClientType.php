<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'icon_width',
        'icon_height',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'icon_width' => 'integer',
        'icon_height' => 'integer',
    ];

    public function getIconUrlAttribute()
    {
        return $this->icon
            ? cms_media_url($this->icon)
            : null;
    }
}
