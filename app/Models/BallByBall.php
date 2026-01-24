<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BallByBall extends Model
{
    use HasFactory;

    protected $table = 'ball_by_ball';

    protected $fillable = [
        'match_id',
        'batting_team_id',
        'bowling_team_id',
        'over_number',
        'ball_number',
        'valid_ball_count',
        'is_one',
        'is_two',
        'is_three',
        'is_four',
        'is_five',
        'is_six',
        'other_runs',
        'bye_runs',
        'bowler_id',
        'striker_id',
        'non_striker_id' ,
        'fielder_id',
        'total_runs',
        'is_over_completed',
        'extra_type',
        'is_wicket',
        'wicket_type',
        'current_run_rate',
        'projected_score',
        'extra_runs',
        'dismissal_type',
        'innings_completed',
        'is_striker_on_strike',
        'total_score',
        'total_wickets',
        'total_overs',
        'display_run',
        'dismissed_batsmen',
        'ball_number_for_undo',

    ];
}
