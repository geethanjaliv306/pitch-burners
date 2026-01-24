<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tournament extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tournaments';

    protected $fillable = [
        'name',
        'city', 
        'ground', 
        'organiser_name', 
        'organiser_contact',
        'allow_players', 
        'start_date', 
        'end_date', 
        'tournament_category', 
        'ball_type'
    ];
    protected $casts = [
        'city' => 'array',
        'ground' => 'array',
    ];

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'tournament_teams');
    }
    public function rounds()
    {
        return $this->hasMany(TournamentRound::class);
    }

}
