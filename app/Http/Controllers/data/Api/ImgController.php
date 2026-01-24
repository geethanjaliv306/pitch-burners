<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImgController extends Controller
{
    public function generatePlayerImgPath($id, $img_name) {
        $path = config('filesystems.player_path');
        // return "$path/$id/$img_name";
        return "uploads/player_images/$img_name";
    }
    public function generateTeamImgPath($img_name) {
        $path = config('filesystems.team_path');
        // return "$path/$img_name";
        return "uploads/team_logos/$img_name";
    }
}
