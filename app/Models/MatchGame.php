<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MatchGame extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'matches';

    protected $fillable = [
        'tournament_id',
        'schedule_match_id',
        'teamA_id',
        'teamB_id',
        'venue',
        'match_date_time',
        'type',
        'overs',
        'first_umpire',
        'second_umpire',
        'third_umpire',
        'first_scorer',
        'second_scorer',
        'toss',
        'batting',
        'bowling',
        'status',
        'schedule_match_id',
        'group_id',
        'round_id',
        'overs_per_bowler' ,
        'match_details',
    ];

    public function teamA()
    {
        return $this->belongsTo(Team::class, 'teamA_id');
    }

    public function teamB()
    {
        return $this->belongsTo(Team::class, 'teamB_id');
    }

    public function team1Details()
    {
        return $this->hasMany(Team1Detail::class, 'match_id');
    }

    public function team2Details()
    {
        return $this->hasMany(Team2Detail::class, 'match_id');
    }

}
