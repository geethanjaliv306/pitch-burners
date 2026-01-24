<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizerMember extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'organizer_members';
    protected $fillable = ['name', 'email', 'phone_no', 'password','image'];
}
