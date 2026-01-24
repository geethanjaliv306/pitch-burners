<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BowlerScoreBoard extends Model
{
    use HasFactory;
    protected $table = 'bowlers_scoreboards';
    protected $fillable = [
        'match_id',
        'team_id',
        'inning',
        'bowler_id',
        'is_max_overs_bowled',
        'overs_bowled',
        'runs_conceded',
        'wickets',
        'maidens',
        'economy'
    ];
}
