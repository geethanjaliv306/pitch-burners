<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContinueMatchState extends Model
{
    use HasFactory;

    protected $table = 'continue_match_states' ;

    protected $fillable = [
        'match_id',
        'state_data',
        'innings'
    ];
}
