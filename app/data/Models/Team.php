<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;

class Team extends Model implements AuthenticatableContract
{
    use SoftDeletes, Authenticatable;

    protected $table = 'teams';

    protected $fillable = [
        'group_id',
        'tournament_id',
        'name',
        'email',
        'phone',
        'logo',
        // 'favicon',
        'password',
        'is_added',
    ];

    protected $hidden = [
        'password',
    ];

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function matchesAsTeamA()
    {
        return $this->hasMany(MatchGame::class, 'teamA');
    }

    public function matchesAsTeamB()
    {
        return $this->hasMany(MatchGame::class, 'teamB');
    }
    
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

}
