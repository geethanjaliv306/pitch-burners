<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerFieldingStats extends Model
{
    use HasFactory;
    protected $table = 'player_fielding_stats';

    protected $fillable = [
        'match_id',
        'team_id',
        'ball_by_ball_id',
        'player_id',
        'catches',
        'run_outs',
        'stumpings',
        'bowled' ,
        'direct_hit',
        'throwing_end_id' ,
        'fielding_caught_behind',
        'fielding_caught_and_bowled',
        'retired_hurt',
        'fielding_mankaded',
        'hit_wicket',
        'retired_out',
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
