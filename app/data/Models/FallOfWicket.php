<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FallOfWicket extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fall_of_wickets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ball_by_ball_id',
        'match_id',
        'team_id',
        'inning',
        'wicket_number',
        'dismissed_batsmen',
        'score_at_dismissal',
        'batsmen_dismissed_by_over',
    ];

    /**
     * Get the match associated with the fall of wicket.
     */
    public function match()
    {
        return $this->belongsTo(MatchGame::class, 'match_id');
    }

    /**
     * Get the batting team associated with the fall of wicket.
     */
    public function battingTeam()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    /**
     * Get the batsman who was dismissed.
     */
    public function batsmanDismissed()
    {    
        return $this->belongsTo(Player::class, 'dismissed_batsmen');
    }

    /** 
     * Get the bowler who took the wicket.
     */
}
