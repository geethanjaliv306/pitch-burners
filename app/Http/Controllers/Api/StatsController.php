<?php

namespace App\Http\Controllers\Api;

use App\Models\Team;
use App\Models\Player;
use App\Models\Tournament;
use Illuminate\Http\Request;
use App\Models\PlayerBattingStats;
use App\Models\PlayerBowlingStats;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\TournamentTeam;

class StatsController extends Controller
{

  public function getBattingStats(Request $request)
{
    $tournamentId = $request->input('tournament_id');

    if ($tournamentId !== 'null') {
        $topBatters = DB::table(function ($query) {
            $query->select(
                'batter_id',
                'match_id',
                'team_id',
                DB::raw('SUM(runs) as total_runs'),
                DB::raw('SUM(balls_faced) as total_balls'),
                DB::raw('SUM(fours) as total_fours'),
                DB::raw('SUM(sixes) as total_sixes'),
                DB::raw('MAX(is_out) as was_out')
            )
            ->from('scoreboards')
             ->whereIn('inning' , [0, 1])
            ->groupBy('batter_id', 'match_id', 'team_id')
            ->toSql();
        }, 'match_stats')
        ->leftJoin('players', 'match_stats.batter_id', '=', 'players.id') 
        ->leftJoin('teams', 'match_stats.team_id', '=', 'teams.id')
        ->leftJoin('matches', 'match_stats.match_id', '=', 'matches.id')
        ->where('matches.tournament_id', $tournamentId)
        ->select(
            'players.name as player',
            'players.image as image',
            'teams.name as team',
            DB::raw('SUM(match_stats.total_runs) as runs'),
            DB::raw('COUNT(DISTINCT match_stats.match_id) as matches'),
            DB::raw('COUNT(DISTINCT match_stats.match_id) as innings'),
            DB::raw('
                CASE
                    WHEN SUM(match_stats.was_out) > 0 THEN 0
                    ELSE 1
                END as no
            '),
            DB::raw('MAX(match_stats.total_runs) as highest'),
            //DB::raw('ROUND(AVG(match_stats.total_runs), 2) as avg'),
            DB::raw('ROUND(
      CASE
          WHEN SUM(match_stats.was_out) = 0
              THEN SUM(match_stats.total_runs)
          ELSE SUM(match_stats.total_runs) / SUM(match_stats.was_out)
      END, 2) as avg'),
            DB::raw('SUM(match_stats.total_balls) as bf'),
            DB::raw('ROUND(SUM(match_stats.total_runs) / NULLIF(SUM(match_stats.total_balls), 0) * 100, 2) as sr'),
            DB::raw('SUM(CASE WHEN match_stats.total_runs >= 100 THEN 1 ELSE 0 END) as hundreds'),
            DB::raw('SUM(CASE WHEN match_stats.total_runs >= 50 AND match_stats.total_runs < 100 THEN 1 ELSE 0 END) as fifties'),
            DB::raw('SUM(match_stats.total_fours) as fours'),
            DB::raw('SUM(match_stats.total_sixes) as sixes')
        )
        ->groupBy('match_stats.batter_id', 'players.name', 'teams.name')
        ->having('runs', '>', 0)
        ->orderByDesc('runs')
        ->orderByDesc('sr')
        ->get();

    } else {
        $topBatters = DB::table(function ($query) {
            $query->select(
                'batter_id',
                'match_id',
                'team_id',
                DB::raw('SUM(runs) as total_runs'),
                DB::raw('SUM(balls_faced) as total_balls'),
                DB::raw('SUM(fours) as total_fours'),
                DB::raw('SUM(sixes) as total_sixes'),
                DB::raw('MAX(is_out) as was_out')
            )
            ->from('scoreboards')
            ->groupBy('batter_id', 'match_id', 'team_id')
            ->toSql();
        }, 'match_stats')
        ->leftJoin('players', 'match_stats.batter_id', '=', 'players.id')
        ->leftJoin('teams', 'match_stats.team_id', '=', 'teams.id')
        ->select(
            'players.name as player',
            'teams.name as team',
            DB::raw('SUM(match_stats.total_runs) as runs'),
            DB::raw('COUNT(DISTINCT match_stats.match_id) as matches'),
            DB::raw('COUNT(DISTINCT match_stats.match_id) as innings'),
            DB::raw('
                CASE
                    WHEN SUM(match_stats.was_out) > 0 THEN 0
                    ELSE 1
                END as no
            '),
            DB::raw('MAX(match_stats.total_runs) as highest'),
            //DB::raw('ROUND(AVG(match_stats.total_runs), 2) as avg'),
           DB::raw('ROUND(
      CASE
          WHEN SUM(match_stats.was_out) = 0
              THEN SUM(match_stats.total_runs)
          ELSE SUM(match_stats.total_runs) / SUM(match_stats.was_out)
      END, 2) as avg'),
            DB::raw('SUM(match_stats.total_balls) as bf'),
            DB::raw('ROUND(SUM(match_stats.total_runs) / NULLIF(SUM(match_stats.total_balls), 0) * 100, 2) as sr'),
            DB::raw('SUM(CASE WHEN match_stats.total_runs >= 100 THEN 1 ELSE 0 END) as hundreds'),
            DB::raw('SUM(CASE WHEN match_stats.total_runs >= 50 AND match_stats.total_runs < 100 THEN 1 ELSE 0 END) as fifties'),
            DB::raw('SUM(match_stats.total_fours) as fours'),
            DB::raw('SUM(match_stats.total_sixes) as sixes')
        )
        ->groupBy('match_stats.batter_id', 'players.name', 'teams.name')
        ->having('runs', '>', 0)
        ->orderByDesc('runs')
        ->orderByDesc('sr')
        //->take(10)
        ->get();
    }

    return response()->json($topBatters);
}


public function getBowlingStats(Request $request)
{
    $tournamentId = $request->input('tournament_id');

    $query = DB::table(DB::raw('(SELECT
                        bowler_id,
                        match_id,
                        team_id,
                        MAX(wickets) as max_wickets_taken,
                        MAX(overs_bowled) as max_overs_bowled,
                        MAX(runs_conceded) as max_runs_conceded
                    FROM bowlers_scoreboards
                    WHERE inning IN (0, 1)
                    GROUP BY bowler_id, match_id, team_id
                ) as match_stats'))
        ->leftJoin('players', 'match_stats.bowler_id', '=', 'players.id')
        ->leftJoin('teams', 'match_stats.team_id', '=', 'teams.id')
        ->select(
            'players.name as player',
            'teams.name as team',
            DB::raw('COUNT(DISTINCT match_stats.match_id) as matches'),
            DB::raw('CAST(SUM(match_stats.max_overs_bowled) AS DECIMAL(10,2)) as overs_bowled'),
            DB::raw('SUM(match_stats.max_runs_conceded) as runs'),
            DB::raw('SUM(match_stats.max_wickets_taken) as wickets'),
            DB::raw('ROUND(SUM(match_stats.max_runs_conceded * 6.0) / NULLIF(SUM(match_stats.max_overs_bowled * 6), 0), 2) as economy'),
            DB::raw('SUM(CASE WHEN match_stats.max_wickets_taken >= 3 AND match_stats.max_wickets_taken < 5 THEN 1 ELSE 0 END) as threeFer'),
            DB::raw('SUM(CASE WHEN match_stats.max_wickets_taken >= 5 THEN 1 ELSE 0 END) as fiveFer')
        )
        ->groupBy('match_stats.bowler_id', 'players.name', 'teams.name')
        ->orderByDesc('wickets')
        ->orderBy('economy');

    if ($tournamentId !== 'null') {
        $query->leftJoin('matches', 'match_stats.match_id', '=', 'matches.id')
              ->where('matches.tournament_id', $tournamentId);
    }

    $topBowlers = $query->get();

    return response()->json($topBowlers);
}




    public function getTeams()
    {
        $teams = Team::all(['name']);
        return response()->json($teams);
    }

    public function getTournaments()
    {
        $tournaments = Tournament::all(['name' , 'id']);
        return response()->json($tournaments);
    }

   	public function getLeadingBatters(Request $request)
{
    $tournamentId = $request->input('tournament_id');
    $teamName = $request->input('team'); // Add team filter

    if (($tournamentId !== 'null')) {
        $leadingBatter = DB::table(function($query) {
            $query->select(
                'batter_id',
                'match_id',
                'team_id',
                DB::raw('MAX(runs) as max_score'),
                DB::raw('MAX(balls_faced) as max_balls'),
                // DB::raw('MAX(four) as max_fours'),
                // DB::raw('MAX(six) as max_sixes'),
            )
            ->from('scoreboards')
            ->groupBy('batter_id', 'match_id', 'team_id')
            ->toSql();
        }, 'match_stats')
        ->leftjoin('matches', 'match_stats.match_id', '=', 'matches.id')
        ->leftjoin('players', 'match_stats.batter_id', '=', 'players.id')
        ->leftjoin('teams', 'match_stats.team_id', '=', 'teams.id')
        ->where('matches.tournament_id', $tournamentId);
        
        if ($teamName) {
            $leadingBatter->where('teams.name', $teamName);
        }
        
        $leadingBatter = $leadingBatter->select(
            'players.name as player',
            'players.image as PlayerImage',
            'teams.name as team',
            DB::raw('SUM(match_stats.max_score) as total_runs'),
            DB::raw('COUNT(DISTINCT match_stats.match_id) as matches_played'),
            DB::raw('MAX(match_stats.max_score) as highest_score'),
            // DB::raw('ROUND(AVG(match_stats.max_score), 2) as avg'),
            // DB::raw('SUM(match_stats.max_balls) as bf'),
            DB::raw('ROUND(SUM(match_stats.max_score) / SUM(match_stats.max_balls) * 100, 2) as strike_rate'),
            DB::raw('SUM(CASE WHEN match_stats.max_score >= 100 THEN 1 ELSE 0 END) as hundreds_count'),
            DB::raw('SUM(CASE WHEN match_stats.max_score >= 50 AND match_stats.max_score < 100 THEN 1 ELSE 0 END) as fifties_count'),
            // DB::raw('SUM(match_stats.max_fours) as fours'),
            // DB::raw('SUM(match_stats.max_sixes) as sixes')
        )
        ->groupBy('match_stats.batter_id', 'players.name', 'teams.name', 'players.image')
        ->orderByDesc('total_runs')
        ->orderByDesc('strike_rate')
        ->first();

    } else {
        $leadingBatter = DB::table(function($query) {
            $query->select(
                'batter_id',
                'match_id',
                'team_id',
                DB::raw('MAX(runs) as max_score'),
                DB::raw('MAX(balls_faced) as max_balls'),
                // DB::raw('MAX(four) as max_fours'),
                // DB::raw('MAX(six) as max_sixes'),
            )
            ->from('scoreboards')
            ->groupBy('batter_id', 'match_id', 'team_id')
            ->toSql();
        }, 'match_stats')
        ->leftjoin('players', 'match_stats.batter_id', '=', 'players.id')
        ->leftjoin('teams', 'match_stats.team_id', '=', 'teams.id');
        
        // Add team filter if provided
        if ($teamName) {
            $leadingBatter->where('teams.name', $teamName);
        }
        
        $leadingBatter = $leadingBatter->select(
            'players.name as player',
            'players.image as PlayerImage',
            'teams.name as team',
            DB::raw('SUM(match_stats.max_score) as total_runs'),
            DB::raw('COUNT(DISTINCT match_stats.match_id) as matches_played'),
            DB::raw('MAX(match_stats.max_score) as highest_score'),
            // DB::raw('ROUND(AVG(match_stats.max_score), 2) as avg'),
            // DB::raw('SUM(match_stats.max_balls) as bf'),
            DB::raw('ROUND(SUM(match_stats.max_score) / SUM(match_stats.max_balls) * 100, 2) as strike_rate'),
            DB::raw('SUM(CASE WHEN match_stats.max_score >= 100 THEN 1 ELSE 0 END) as hundreds_count'),
            DB::raw('SUM(CASE WHEN match_stats.max_score >= 50 AND match_stats.max_score < 100 THEN 1 ELSE 0 END) as fifties_count'),
            // DB::raw('SUM(match_stats.max_fours) as fours'),
            // DB::raw('SUM(match_stats.max_sixes) as sixes')
        )
        ->groupBy('match_stats.batter_id', 'players.name', 'teams.name', 'players.image')
        ->orderByDesc('total_runs')
        ->orderByDesc('strike_rate')
        ->first();
    }
    
    return response()->json($leadingBatter);
}

public function getLeadingBowlers(Request $request)
{
    $tournamentId = $request->input('tournament_id');
    $teamName = $request->input('team');

    $query = DB::table(DB::raw('(SELECT
                bowler_id,
                match_id,
                team_id,
                MAX(wickets) as max_wickets_taken,
                MAX(overs_bowled) as max_overs_bowled,
                MAX(runs_conceded) as max_runs_conceded,
                MAX(maidens) as max_maiden_overs
                FROM bowlers_scoreboards
                WHERE inning IN (0 , 1)
                GROUP BY bowler_id, match_id, team_id
            ) as match_stats'))
        ->leftJoin('matches', 'match_stats.match_id', '=', 'matches.id')
        ->leftJoin('players', 'match_stats.bowler_id', '=', 'players.id')
        ->leftJoin('teams', 'match_stats.team_id', '=', 'teams.id')
        ->select(
            'players.name as player',
            'teams.name as team',
            'players.image as PlayerImage',
            DB::raw('COUNT(DISTINCT match_stats.match_id) as matches_played'),
            DB::raw('CAST(SUM(match_stats.max_overs_bowled) AS DECIMAL(10,2)) as total_overs'),
            DB::raw('SUM(match_stats.max_runs_conceded) as total_runs_conceded'),
            DB::raw('SUM(match_stats.max_wickets_taken) as total_wickets'),
            DB::raw('SUM(match_stats.max_maiden_overs) as total_maidens'),
            DB::raw('ROUND(SUM(match_stats.max_runs_conceded * 6.0) / NULLIF(SUM(match_stats.max_overs_bowled * 6), 0), 2) as economy_rate'), // Prevent division by zero
            DB::raw('SUM(CASE WHEN match_stats.max_wickets_taken >= 3 AND match_stats.max_wickets_taken < 5 THEN 1 ELSE 0 END) as three_wicket_hauls'),
            DB::raw('SUM(CASE WHEN match_stats.max_wickets_taken >= 5 THEN 1 ELSE 0 END) as five_wicket_hauls')
        )
        ->groupBy('match_stats.bowler_id', 'players.name', 'teams.name', 'players.image');

    if ($tournamentId !== 'null') {
        $query->where('matches.tournament_id', $tournamentId);
    }
    
    if ($teamName) {
        $query->where('teams.name', $teamName);
    }

    $leadingBowlers = $query->orderByDesc('total_wickets')->orderBy('economy_rate')->first();

    return response()->json($leadingBowlers);
}


}
