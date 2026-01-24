<?php

namespace App\Http\Controllers;

use App\Models\FallOfWicket;
use Illuminate\Http\Request;

class ScoreBoardController extends Controller
{
    static function getFallOfWickets($match_id, $team_id, $inning) {
        $data = FallOfWicket::where('fall_of_wickets.match_id', $match_id)
        ->where('fall_of_wickets.team_id', $team_id)
        ->where('fall_of_wickets.inning', $inning)
        ->leftJoin('players as batsman', 'batsman.id', '=', 'fall_of_wickets.dismissed_batsmen')
        ->leftJoin('matches', 'matches.id', '=', 'fall_of_wickets.match_id')
        ->select(
            'fall_of_wickets.*',
            'fall_of_wickets.score_at_dismissal as score',
            'fall_of_wickets.batsmen_dismissed_by_over as over',
            'matches.overs as total_over',
            'batsman.name as player',
        )
        ->get();
        return $data;
    }
    
    static function storeFallOfWickets($ball_by_ball_id, $match_id, $team_id, $inning, $dismissed_batsmen, $score, $over) { 
        $wicket_number = 1;
        $previous_fow = FallOfWicket::where('match_id', $match_id)->where('inning', $inning)->orderBy('id', 'desc')->first();
        if(isset($previous_fow)){
            $wicket_number += $previous_fow->wicket_number;
        }
        $fall_of_wicket_data = [
            'ball_by_ball_id'=> $ball_by_ball_id,
            'match_id' => $match_id,
            'team_id' => $team_id,
            'inning' => $inning,
            'dismissed_batsmen' => $dismissed_batsmen,
            'wicket_number' => $wicket_number,
            'score_at_dismissal' => $score,
            'batsmen_dismissed_by_over' => $over,
        ];
        FallOfWicket::create($fall_of_wicket_data);
    }
}
