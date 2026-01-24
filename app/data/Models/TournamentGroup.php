<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

    class TournamentGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tournament_groups';

    protected $fillable = [
        'tournament_id',
        // 'round_type',
        'group_id',
        'team_id',
        'status',
    ];

     public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}
