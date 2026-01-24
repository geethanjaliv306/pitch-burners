<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerBattingStats extends Model
{
    use HasFactory;

    protected $table = 'player_batting_stats';

    protected $fillable = [
        'match_id',
        'team_id',
        'player_id',
        'ball_by_ball_id',
        'score',
        'one',
        'two',
        'three',
        'four',
        'five',
        'six',
        'other_runs',
        'bye_runs',
        'strike_rate',
        'balls_faced',
        'is_out',
        'dismissal_type',
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
