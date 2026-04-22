<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'order',
        'show_in_home',
        'is_active',
    ];

    protected $casts = [
        'show_in_home' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function equipments()
    {
        return $this->hasMany(Equipment::class)->orderBy('order');
    }
}
