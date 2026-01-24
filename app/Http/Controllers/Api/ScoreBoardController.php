<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BallByBall;
use App\Models\BowlerScoreBoard;
use App\Models\FallOfWicket;
use App\Models\ScoreBoard;
use App\Models\Team1Detail;
use App\Models\Team2Detail;
use Illuminate\Http\Request;
use DB;
use Illuminate\Database\Eloquent\Collection;

class ScoreBoardController extends Controller
{
    public function __construct(protected int | string | null $match_id = null, protected int | null $team_id = null, protected int | null $bowling_team_id = null, protected int $inning = 0) { }

    //getter and setter
    public function getTeamId() {
        return $this->team_id;
    }
    public function setTeamId(int $id) {
        $this->team_id = $id;
    }
    
    public function getFallOfWickets(int $team_id, int $inning) {
        $data = FallOfWicket::where('fall_of_wickets.match_id', $this->match_id)
        ->where('fall_of_wickets.team_id', $team_id)
        ->where('fall_of_wickets.inning', $inning)
        ->leftJoin('players as batsman', 'batsman.id', '=', 'fall_of_wickets.dismissed_batsmen')
        // ->leftJoin('matches', 'matches.id', '=', 'fall_of_wickets.match_id')
        ->select(
            'fall_of_wickets.*',
            'fall_of_wickets.score_at_dismissal as score',
            'fall_of_wickets.batsmen_dismissed_by_over as over',
            // 'matches.overs as total_over',
            'batsman.name as player',
        )
        ->get();
        return $data;
    }
    
    public function storeFallOfWickets(int $ball_by_ball_id, int $dismissed_batsmen, int|string $score, int|string|float $over) { 
        $wicket_number = 1;
        $previous_fow = FallOfWicket::where('match_id', $this->match_id)->where('inning', $this->inning)->orderBy('id', 'desc')->first();
        if(isset($previous_fow)){
            $wicket_number += $previous_fow->wicket_number;
        }
        $fall_of_wicket_data = [
            'ball_by_ball_id'=> $ball_by_ball_id,
            'match_id' => $this->match_id,
            'team_id' => $this->team_id,
            'inning' => $this->inning,
            'dismissed_batsmen' => $dismissed_batsmen,
            'wicket_number' => $wicket_number,
            'score_at_dismissal' => $score,
            'batsmen_dismissed_by_over' => $over,
        ];
        FallOfWicket::create($fall_of_wicket_data);
    }

    public function isBatsManExist(int $bats_man) {
       $isExist = ScoreBoard::where('match_id', $this->match_id)
       ->where('team_id', $this->team_id)
       ->where('inning', $this->inning)
       ->where('batter_id', $bats_man)
       ->first();

       return ($isExist);
    }

    public function storeScoreBoard(array $data): ScoreBoard {
        return ScoreBoard::create($data);
    }
    public function updateScoreBoard(int $id, array $data): ScoreBoard | null {
        $bats_man = ScoreBoard::find($id);
        if(isset($bats_man)) {
            $bats_man->update($data);
            return $bats_man;
        }
        return null;
    }
    public function deleteBatsman(int $bats_man) {
        $isExist = $this->isBatsManExist($bats_man);
        if(isset($isExist)) {
            ScoreBoard::where('id', $isExist->id)->forceDelete();
        }
    }
    public function getScoreBoardBatsMen($team_id, int $inning) {
        $batsmen =  ScoreBoard::where('scoreboards.match_id', $this->match_id)
        ->where('scoreboards.team_id', $team_id)
        ->where('scoreboards.inning', $inning)
        ->leftJoin('players as batter', 'batter.id', '=', 'scoreboards.batter_id')
        ->leftJoin('players as bowler', 'bowler.id', '=', 'scoreboards.bowler_id')
        ->leftJoin('players as fielder', 'fielder.id', '=', 'scoreboards.fielder_id')
        ->leftJoin('match_players', function($join) use ($team_id, $inning){
            $join->on('match_players.match_id', '=', 'scoreboards.match_id')
            ->where('match_players.team_id', '=', $team_id)
            ->where('match_players.current_innings', '=', $inning);
        })
        ->orderBy('scoreboards.id', 'asc') //Sorting before changing batter_id to id
        ->select(
            'scoreboards.*',
            'scoreboards.batter_id as id',
            'scoreboards.balls_faced as balls',
            'scoreboards.strike_rate as strikeRate',
            'scoreboards.dismissal_type as dismissalType',
            'batter.name as name',
            'bowler.name as bowlerName',
            'fielder.name as fielderName',
            DB::raw('CASE WHEN match_players.striker_id = scoreboards.batter_id THEN true ELSE false END as is_striker')
        )
        ->distinct()
        // ->groupBy('scoreboards.id')
        ->get();

        $team_data = Team1Detail::where('match_id' , $this->match_id)
        ->where('team_id', $team_id)->get(['player_id', 'captain', 'wicketkeeper']);

        if(!count($team_data)) {
            $team_data = Team2Detail::where('match_id' , $this->match_id)
            ->where('team_id', $team_id)->get(['player_id', 'captain', 'wicketkeeper']);
        }

        $duplicate_batsmen = collect($batsmen)->duplicatesStrict('id')->keys();
        $filtered_batsmen = collect($batsmen)->except($duplicate_batsmen);

        return array_values($filtered_batsmen->map(function ($batsman) use ($team_data){
            $data = $team_data->where('player_id', $batsman->batter_id)->first();
            $batsman->isCaptain = isset($data) ? $data->captain : 0;
            return $batsman;
        })->toArray());

    }
    public function isBowlerExist(int $bowler){
        $isExist = BowlerScoreBoard::where('match_id', $this->match_id)
        ->where('team_id', $this->bowling_team_id)
        ->where('inning', $this->inning)
        ->where('bowler_id', $bowler)
        ->first();

        return $isExist;
    }
    public function storeBowlerScoreBoard(array $bowler_data) {
        return BowlerScoreBoard::create($bowler_data);
    }
    public function updateBowlerScoreBoard(int $id, array $data):BowlerScoreBoard | null {
        $bowler = BowlerScoreBoard::find($id);
        if(isset($bowler)) {
            $bowler->update($data);
            return $bowler;
        }
        return null;
    }
    public function getScoreBoardBowlers(int $team_id, int $inning){
        $bowlers =  BowlerScoreBoard::where('bowlers_scoreboards.match_id', $this->match_id)
        ->where('bowlers_scoreboards.team_id', $team_id)
        ->where('bowlers_scoreboards.inning', $inning)
		->where('bowlers_scoreboards.deleted_at', NULL)
        ->leftJoin('players as bowler', 'bowler.id', '=', 'bowlers_scoreboards.bowler_id')
        ->select(
            'bowlers_scoreboards.*',
            'bowlers_scoreboards.bowler_id as id',
            'bowlers_scoreboards.overs_bowled as overs',
            'bowlers_scoreboards.runs_conceded as runs',
            'bowler.name as name',
        )->get();

        $duplicate_bowlers = collect($bowlers)->duplicatesStrict('id')->keys();
        return array_values(collect($bowlers)->except($duplicate_bowlers )->toArray());
    }
  
  public function deleteBowlersWhoseNotBowled(int $except) {
        BowlerScoreBoard::where('match_id', $this->match_id)->where('team_id', $this->bowling_team_id)
        ->where('overs_bowled', 0.0)->where('bowler_id','!=',$except)->update(['deleted_at' =>  now()]);

        BowlerScoreBoard::where('match_id', $this->match_id)->where('team_id', $this->bowling_team_id)
        ->where('bowler_id', $except)->update(['deleted_at' => NULL]);
    }
}
