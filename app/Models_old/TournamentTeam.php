<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TournamentTeam extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tournament_teams';

    protected $fillable = [
        'tournament_id',
        'team_id',
        'qualified',
        'payment',
        'verified',
        'match_preference',
         'team_bonafide'
    ];
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

}
