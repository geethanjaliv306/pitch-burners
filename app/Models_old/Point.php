<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Point extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'points';

    protected $fillable = [
        'tournament_id',
        'match_id',
        'team_id',
        'matches_played',
        'wins',
        'losses',
        'super_over_win' ,
        'super_over_loss' ,
        'is_super_over',
        'matches_not_played',
        'matches_tied',
        'net_run_rate',
        'total_points',
        'group_id',
        'round_id',

    ];
}
