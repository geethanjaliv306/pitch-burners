<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScoreBoard extends Model
{
    use HasFactory;
    protected $table = "scoreboards";
    protected $fillable = [
        'match_id',
        'inning',
        'team_id',
        'batter_id',
        'bowler_id',
        'fielder_id',
        'is_out',
        'dismissal_type',
        'runs',
        'balls_faced',
        'fours',
        'sixes',
        'strike_rate'
    ];
}
