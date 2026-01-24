<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerBowlingStats extends Model
{
    use HasFactory;
    protected $table = 'player_bowling_stats';

    protected $fillable = [
        'match_id',
        'team_id',
        'player_id',
        'ball_by_ball_id',
        'player_id',
        'overs_bowled',
        'balls_bowled',
        'valid_ball_count',
        'maiden_overs',
        'runs_conceded',
        'wickets_taken',
        'economy_rate',
        'no_balls',
        'wide_balls',
        'extras_bowled',
        'extras_type',
        'extra_runs',
    ];

    public function match()
    {
        return $this->belongsTo(MatchGame::class, 'match_id');
    }

    public function player()
    {
        return $this->belongsTo(Player::class, 'player_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function ballByBall()
    {
        return $this->belongsTo(BallByBall::class, 'over_id');
    }
}
