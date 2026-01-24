<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppVersion extends Model
{
    protected $fillable = [
        'version',
        'platform',
        'release_date',
        'is_force_update',
        'description'
    ];

    protected $casts = [
        'release_date' => 'datetime',
        'is_force_update' => 'boolean'
    ];
}
