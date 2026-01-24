<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutUsContent extends Model
{
    use HasFactory;
    protected $table = 'about_us_content';
    
    protected $fillable = ['banner_text', 'sub_details1', 'sub_details2', 'sub_details3', 'title1', 'title2', 'title3'];

}
