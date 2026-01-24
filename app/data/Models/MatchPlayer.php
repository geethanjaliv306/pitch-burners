<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchPlayer extends Model
{
    use HasFactory;
    protected $table = 'match_players';
    protected $fillable = [
        'match_id',
        'team_id',
        'striker_id',
        'non_striker_id',
        'bowler_id',
        'current_innings',
    ];
}
