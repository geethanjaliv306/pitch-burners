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

    if (($tournamentId !== 'null')) {
        $topBatters = DB::table(function($query) {
            $query->select(
                'player_id',
                'match_id',
                'team_id',
                DB::raw('MAX(score) as max_score'),
                DB::raw('MAX(balls_faced) as max_balls'),
                DB::raw('MAX(four) as max_fours'),
                DB::raw('MAX(six) as max_sixes'),
                DB::raw('MAX(is_out) as was_out')
            )
            ->from('player_batting_stats')
            ->groupBy('player_id', 'match_id', 'team_id')
            ->toSql();
        }, 'match_stats')
        ->join('players', '.player_id', '=', 'players.id')
        ->join('teams', 'match_stats.team_id', '=', 'teams.id')
        ->join('tournament_teams', 'match_stats.team_id', '=', 'tournament_teams.team_id')
        ->where('tournament_teams.tournament_id', $tournamentId)
        ->select(
            'players.name as player',
            'teams.name as team',
            DB::raw('SUM(match_stats.max_score) as runs'),
            DB::raw('COUNT(DISTINCT match_stats.match_id) as matches'),
            DB::raw('COUNT(DISTINCT match_stats.match_id) as innings'),
            DB::raw('
                CASE
                    WHEN SUM(match_stats.was_out) > 0 THEN 0
                    ELSE 1
                END as no
            '),
            DB::raw('MAX(match_stats.max_score) as highest'),
            DB::raw('ROUND(AVG(match_stats.max_score), 2) as avg'),
            DB::raw('SUM(match_stats.max_balls) as bf'),
            DB::raw('ROUND(SUM(match_stats.max_score) / SUM(match_stats.max_balls) * 100, 2) as sr'),
            DB::raw('SUM(CASE WHEN match_stats.max_score >= 100 THEN 1 ELSE 0 END) as hundreds'),
            DB::raw('SUM(CASE WHEN match_stats.max_score >= 50 AND match_stats.max_score < 100 THEN 1 ELSE 0 END) as fifties'),
            DB::raw('SUM(match_stats.max_fours) as fours'),
            DB::raw('SUM(match_stats.max_sixes) as sixes')
        )
        ->groupBy('match_stats.player_id', 'players.name', 'teams.name')
        ->orderByDesc('runs')
        ->take(10)
        ->get();

    } else {
        $topBatters = DB::table(function($query) {
            $query->select(
                'player_id',
                'match_id',
                'team_id',
                DB::raw('MAX(score) as max_score'),
                DB::raw('MAX(balls_faced) as max_balls'),
                DB::raw('MAX(four) as max_fours'),
                DB::raw('MAX(six) as max_sixes'),
                DB::raw('MAX(is_out) as was_out')
            )
            ->from('player_batting_stats')
            ->groupBy('player_id', 'match_id', 'team_id')
            ->toSql();
        }, 'match_stats')
            ->join('players', 'match_stats.player_id', '=', 'players.id')
            ->join('teams', 'match_stats.team_id', '=', 'teams.id')
            ->select(
                'players.name as player',
                'teams.name as team',
                DB::raw('SUM(match_stats.max_score) as runs'),
                DB::raw('COUNT(DISTINCT match_stats.match_id) as matches'),
                DB::raw('COUNT(DISTINCT match_stats.match_id) as innings'),
                DB::raw('
                    CASE
                        WHEN SUM(match_stats.was_out) > 0 THEN 0
                        ELSE 1
                    END as no
                '),
                DB::raw('MAX(match_stats.max_score) as highest'),
                DB::raw('ROUND(AVG(match_stats.max_score), 2) as avg'),
                DB::raw('SUM(match_stats.max_balls) as bf'),
                DB::raw('ROUND(SUM(match_stats.max_score) / SUM(match_stats.max_balls) * 100, 2) as sr'),
                DB::raw('SUM(CASE WHEN match_stats.max_score >= 100 THEN 1 ELSE 0 END) as hundreds'),
                DB::raw('SUM(CASE WHEN match_stats.max_score >= 50 AND match_stats.max_score < 100 THEN 1 ELSE 0 END) as fifties'),
                DB::raw('SUM(match_stats.max_fours) as fours'),
                DB::raw('SUM(match_stats.max_sixes) as sixes')
            )
            ->groupBy('match_stats.player_id', 'players.name', 'teams.name')
            ->orderByDesc('runs')
            ->take(10)
            ->get();

    }

    return response()->json($topBatters);
}


public function getBowlingStats(Request $request )
{

    $tournamentId = $request->input('tournament_id');

    if ($tournamentId !== 'null') {
        $topBowlers = DB::table(DB::raw('(SELECT
                        player_id,
                        match_id,
                        team_id,
                        MAX(wickets_taken) as max_wickets_taken,
                        MAX(overs_bowled) as max_overs_bowled,
                        MAX(runs_conceded) as max_runs_conceded
                    FROM player_bowling_stats
                    GROUP BY player_id, match_id, team_id
                ) as match_stats'))
            ->join('players', 'match_stats.player_id', '=', 'players.id')
            ->join('teams', 'match_stats.team_id', '=', 'teams.id')
            ->join('tournament_teams', 'match_stats.team_id', '=', 'tournament_teams.team_id')
            ->where('tournament_teams.tournament_id', $tournamentId)
            ->select(
                'players.name as player',
                'teams.name as team',
                DB::raw('COUNT(DISTINCT match_stats.match_id) as matches'),
                DB::raw('CAST(SUM(match_stats.max_overs_bowled) AS DECIMAL(10,2)) as overs_bowled'),
                DB::raw('SUM(match_stats.max_runs_conceded) as runs'),
                DB::raw('SUM(match_stats.max_wickets_taken) as wickets'),
                DB::raw('ROUND(SUM(match_stats.max_runs_conceded * 6.0) / (SUM(match_stats.max_overs_bowled * 6)), 2) as economy'), // Calculate economy rate
                DB::raw('SUM(CASE WHEN match_stats.max_wickets_taken >= 3 AND match_stats.max_wickets_taken < 5 THEN 1 ELSE 0 END) as threeFer'), // Three-wicket hauls
                DB::raw('SUM(CASE WHEN match_stats.max_wickets_taken >= 5 THEN 1 ELSE 0 END) as fiveFer') // Five-wicket hauls
            )
            ->groupBy('match_stats.player_id', 'players.name', 'teams.name')
            ->orderByDesc('wickets')
            ->take(10)
            ->get();
    }
    else {
        // $topBowlers = DB::table('match_stats')
        $topBowlers = DB::table(DB::raw('(SELECT
                    player_id,
                    match_id,
                    team_id,
                    MAX(wickets_taken) as max_wickets_taken,
                    MAX(overs_bowled) as max_overs_bowled,
                    MAX(runs_conceded) as max_runs_conceded
                FROM player_bowling_stats
                GROUP BY player_id, match_id, team_id
            ) as match_stats'))
    ->join('players', 'match_stats.player_id', '=', 'players.id')
    ->join('teams', 'match_stats.team_id', '=', 'teams.id')
    ->select(
        'players.name as player',
        'teams.name as team',
        DB::raw('COUNT(DISTINCT match_stats.match_id) as matches'),
        DB::raw('CAST(SUM(match_stats.max_overs_bowled) AS DECIMAL(10,2)) as overs_bowled'),
        DB::raw('SUM(match_stats.max_runs_conceded) as runs'),
        DB::raw('SUM(match_stats.max_wickets_taken) as wickets'),
        DB::raw('ROUND(SUM(match_stats.max_runs_conceded * 6.0) / SUM(match_stats.max_overs_bowled * 6), 2) as economy'), // Calculate economy
        DB::raw('SUM(CASE WHEN match_stats.max_wickets_taken >= 3 AND match_stats.max_wickets_taken < 5 THEN 1 ELSE 0 END) as threeFer'), // Three-wicket hauls
        DB::raw('SUM(CASE WHEN match_stats.max_wickets_taken >= 5 THEN 1 ELSE 0 END) as fiveFer') // Five-wicket hauls
    )
    ->groupBy('match_stats.player_id', 'players.name', 'teams.name')
    ->orderByDesc('wickets')
    ->take(10)
    ->get();

    }

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

    if (($tournamentId !== 'null')) {
        $leadingBatter = DB::table(function($query) {
            $query->select(
                'player_id',
                'match_id',
                'team_id',
                DB::raw('MAX(score) as max_score'),
                DB::raw('MAX(balls_faced) as max_balls'),
                // DB::raw('MAX(four) as max_fours'),
                // DB::raw('MAX(six) as max_sixes'),
            )
            ->from('player_batting_stats')
            ->groupBy('player_id', 'match_id', 'team_id')
            ->toSql();
        }, 'match_stats')
        ->join('players', '.player_id', '=', 'players.id')
        ->join('teams', 'match_stats.team_id', '=', 'teams.id')
        ->join('tournament_teams', 'match_stats.team_id', '=', 'tournament_teams.team_id')
        ->where('tournament_teams.tournament_id', $tournamentId)
        ->select(
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
        ->groupBy('match_stats.player_id', 'players.name', 'teams.name' , 'players.image')
        ->orderByDesc('total_runs')
        ->first();

    } else {
        $leadingBatter = DB::table(function($query) {
            $query->select(
                'player_id',
                'match_id',
                'team_id',
                DB::raw('MAX(score) as max_score'),
                DB::raw('MAX(balls_faced) as max_balls'),
                // DB::raw('MAX(four) as max_fours'),
                // DB::raw('MAX(six) as max_sixes'),
            )
            ->from('player_batting_stats')
            ->groupBy('player_id', 'match_id', 'team_id')
            ->toSql();
        }, 'match_stats')
        ->join('players', '.player_id', '=', 'players.id')
        ->join('teams', 'match_stats.team_id', '=', 'teams.id')
        ->select(
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
        ->groupBy('match_stats.player_id', 'players.name', 'teams.name' , 'players.image')
        ->orderByDesc('total_runs')
        ->first();

    }
        return response()->json($leadingBatter);

    }


    public function getLeadingBowlers(Request $request) {

    $tournamentId = $request->input('tournament_id');

      if ($tournamentId !== 'null') {
         // Query for leading bowlers with additional stats
            $leadingBowlers = DB::table(DB::raw('(SELECT
                    player_id,
                    match_id,
                    team_id,
                    MAX(wickets_taken) as max_wickets_taken,
                    MAX(overs_bowled) as max_overs_bowled,
                    MAX(runs_conceded) as max_runs_conceded,
                    MAX(maiden_overs) as max_maiden_overs
                    FROM player_bowling_stats
                    GROUP BY player_id, match_id, team_id
                ) as match_stats'))
                    ->join('players', 'match_stats.player_id', '=', 'players.id')
                    ->join('teams', 'match_stats.team_id', '=', 'teams.id')
                    ->join('tournament_teams', 'match_stats.team_id', '=', 'tournament_teams.team_id')
                    ->where('tournament_teams.tournament_id', $tournamentId)
                    ->select(
                    'players.name as player',
                    'teams.name as team',
                    DB::raw('COUNT(DISTINCT match_stats.match_id) as matches_played'),
                    DB::raw('CAST(SUM(match_stats.max_overs_bowled) AS DECIMAL(10,2)) as total_overs'),
                    DB::raw('SUM(match_stats.max_runs_conceded) as total_runs_conceded'),
                    DB::raw('SUM(match_stats.max_wickets_taken) as total_wickets'),
                    DB::raw('SUM(match_stats.max_maiden_overs) as total_maidens'),
                    DB::raw('ROUND(SUM(match_stats.max_runs_conceded * 6.0) / SUM(match_stats.max_overs_bowled * 6), 2) as economy_rate'), // Calculate economy
                    DB::raw('SUM(CASE WHEN match_stats.max_wickets_taken >= 3 AND match_stats.max_wickets_taken < 5 THEN 1 ELSE 0 END) as three_wicket_hauls'), // Three-wicket hauls
                    DB::raw('SUM(CASE WHEN match_stats.max_wickets_taken >= 5 THEN 1 ELSE 0 END) as five_wicket_hauls') // Five-wicket hauls
                )
                ->groupBy('match_stats.player_id', 'players.name', 'teams.name')
                ->orderByDesc('total_wickets')
                ->first();
            }
                else {
                        $leadingBowlers = DB::table(DB::raw('(SELECT
                            player_id,
                            match_id,
                            team_id,
                            MAX(wickets_taken) as max_wickets_taken,
                            MAX(overs_bowled) as max_overs_bowled,
                            MAX(runs_conceded) as max_runs_conceded,
                            MAX(maiden_overs) as max_maiden_overs
                            FROM player_bowling_stats
                            GROUP BY player_id, match_id, team_id
                        ) as match_stats'))
                ->join('players', 'match_stats.player_id', '=', 'players.id')
                ->join('teams', 'match_stats.team_id', '=', 'teams.id')
                ->select(
                'players.name as player',
                'teams.name as team',
                DB::raw('COUNT(DISTINCT match_stats.match_id) as matches_played'),
                DB::raw('CAST(SUM(match_stats.max_overs_bowled) AS DECIMAL(10,2)) as total_overs'),
                DB::raw('SUM(match_stats.max_runs_conceded) as total_runs_conceded'),
                DB::raw('SUM(match_stats.max_wickets_taken) as total_wickets'),
                DB::raw('SUM(match_stats.max_maiden_overs) as total_maidens'),
                DB::raw('ROUND(SUM(match_stats.max_runs_conceded * 6.0) / SUM(match_stats.max_overs_bowled * 6), 2) as economy_rate'), // Calculate economy
                DB::raw('SUM(CASE WHEN match_stats.max_wickets_taken >= 3 AND match_stats.max_wickets_taken < 5 THEN 1 ELSE 0 END) as three_wicket_hauls'), // Three-wicket hauls
                DB::raw('SUM(CASE WHEN match_stats.max_wickets_taken >= 5 THEN 1 ELSE 0 END) as five_wicket_hauls') // Five-wicket hauls
            )
            ->groupBy('match_stats.player_id', 'players.name', 'teams.name')
            ->orderByDesc('total_wickets')
            ->first();

            }

    return response()->json($leadingBowlers);
}

}
