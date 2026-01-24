<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Player extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'team_id',
        'name',
        'email',
        'empid',
        'phone',
        'image',
        'batting_style',
        'bowling_style',
        'role',
        'is_captain',
      	'ball_preferences',
    ];
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

}
