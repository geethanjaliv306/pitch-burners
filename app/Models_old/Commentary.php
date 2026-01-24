<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commentary extends Model
{
    use HasFactory;
    protected $table = 'commentaries';
    protected $fillable = [
        'ball_by_ball_id',
        'match_id',
        'inning',
        'over',
        'ball',
        'display_run',
        'total_score',
        'striker_id',
        'non_striker_id',
        'bowler_id',
        'fielder_id',
        'commentary_text',
    ];
}
