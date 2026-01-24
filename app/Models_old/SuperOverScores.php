<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuperOverScores extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'super_over_scores';

    protected $fillable = [
        'match_id',
        'team_id',
        'match_score_id',
        'runs_scored',
        'wickets_lost',
        'overs_bowled',
        'extras',
        'is_winning',
        'is_tied',
        'is_first_inning_super_over',
        'is_second_inning_super_over',
        'total_fours' ,
        'total_sixes' ,
        'total_boundaries',

    ];
}
