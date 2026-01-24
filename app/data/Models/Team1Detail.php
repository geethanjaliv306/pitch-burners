<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team1Detail extends Model
{
    use HasFactory;
    protected $table = 'team1_details'; 
    protected $fillable = [
        'match_id',
        'team_id',
        'player_id',
        'captain',
        'wicketkeeper',
        '12th_man',
    ];
    public function match()
    {
        return $this->belongsTo(MatchGame::class, 'match_id');
    }
    public function player()
    {
        return $this->belongsTo(Player::class, 'player_id');
    }
}
