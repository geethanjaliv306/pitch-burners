<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchImage extends Model
{
    protected $fillable = ['match_id', 'image_path'];

    public function match()
    {
        return $this->belongsTo(MatchGame::class, 'match_id');
    }
}