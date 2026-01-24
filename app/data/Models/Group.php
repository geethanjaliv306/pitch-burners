<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'groups';
    protected $fillable = [
        'group_name',
        'tournament_id'
        ];

    public function teams()
    {
        return $this->hasMany(Team::class, 'group_id');
    }

    public function teams_group()
    {
        return $this->hasMany(Team::class);
    }

}


