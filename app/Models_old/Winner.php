<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Winner extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'winners_cms';

    protected $fillable = ['position', 'team_name', 'prize', 'additional_info', 'tournament_id'];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class, 'tournament_id');
}
}
