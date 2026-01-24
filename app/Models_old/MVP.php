<?php
// MVP.php Model
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MVP extends Model
{
    protected $table = 'mvps';
    protected $fillable = [
        'match_id',
        'tournament_id',
        'team_id',
        'player_id',
        'runs',
        'balls_faced',
        'fours',
        'sixes',
        'strike_rate',
        'overs_bowled',
        'wickets',
        'runs_conceded',
        'economy',
        'published_at',
        'published_by'
    ];

    protected $casts = [
        'published_at' => 'datetime'
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function match()
    {
        return $this->belongsTo(MatchGame::class);
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'published_by');
    }

  	public function images()
    {
        return $this->hasMany(MatchImage::class, 'mvp_id');
    }
}