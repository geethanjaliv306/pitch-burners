<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduleMatch extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'schedule_matches';

    protected $fillable = [
        'tournament_id',
        'round_id',
        'group_id',
        'team1',
        'team2',
        'number_of_overs',
        'overs_per_bowler',
        'type',
        'category',
        'ground',
        'match_date_time',
        'status',
    ];


    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function teamOne()
    {
        return $this->belongsTo(Team::class, 'team1');
    }

    public function teamTwo()
    {
        return $this->belongsTo(Team::class, 'team2');
    }
    public function venue()
    {
        return $this->belongsTo(Venue::class, 'ground');
    }

}
