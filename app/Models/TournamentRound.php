<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TournamentRound extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tournament_rounds';

    protected $fillable = [
        'tournament_id',
        'type',
        'number_of_overs',
        'overs_per_bowler',
        'teams_to_qualify',
        'status'
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }
}
