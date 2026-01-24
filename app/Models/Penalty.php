<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penalty extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'match_id',
        'team_id',
        'inning',
        'over',
        'runs',
        'reason',
    ];

    // Relationships
    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function team() {
        return $this->belongsTo(Team::class);
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
