<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchScore extends Model
{
    use HasFactory;

    protected $fillable =[
        'match_id' ,
        'team_id' ,
        'total_runs' ,
        'total_wickets' ,
        'overs_faced',
        'run_rate',
        'extras',
        'is_batting ' ,
        'projected_score',
        'is_first_inning',
        'is_second_inning' ,
        'is_winning',
        'is_tied',
        'total_fours',
        'total_sixes',
        'total_boundaries',
    ];
}
