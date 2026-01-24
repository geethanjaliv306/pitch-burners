<?php

namespace App\Http\Controllers\Api;

use App\Models\Team;
use App\Models\Group;
use App\Models\Tournament;
use App\Models\Point;
use Illuminate\Http\Request;
use App\Models\TournamentGroup;
use App\Models\TournamentRound;
use App\Http\Controllers\Controller;


class StandingsController extends Controller
{

    public function index(Request $request)
{
    $tournamentId = $request->input('tournament_id');
    $response = [
        'groups' => [],
        'teams' => [],
        'rounds' => [],
        'tournaments' => [],
        'points' => [],
        'tournamentGroups' => [],
    ];

    if ($tournamentId) {
        $tournamentGroups = TournamentGroup::where('tournament_id', $tournamentId)->get();
        $teamIds = $tournamentGroups->pluck('team_id')->unique()->toArray();
        $groupIds = $tournamentGroups->pluck('group_id')->unique()->toArray();

        $response['groups'] = Group::whereIn('id', $groupIds)->get();
        $response['teams'] = Team::whereIn('id', $teamIds)->get();
        $response['tournamentGroups'] = $tournamentGroups;
        $response['rounds'] = TournamentRound::where('tournament_id', $tournamentId)->get();

        $points = Point::whereIn('team_id', $teamIds)
            ->where('tournament_id', $tournamentId)
            ->get();

        $combinedPoints = [];

        foreach ($points as $point) {
            $teamId = $point->team_id;

            if (isset($combinedPoints[$teamId])) {
                $combinedPoints[$teamId]['matches_played'] += $point->matches_played;
                $combinedPoints[$teamId]['wins'] += $point->wins;
                $combinedPoints[$teamId]['losses'] += $point->losses;
                $combinedPoints[$teamId]['total_points'] += $point->total_points;
                $combinedPoints[$teamId]['matches_tied'] += $point->matches_tied;
                $combinedPoints[$teamId]['matches_not_played'] += $point->matches_not_played;
                $combinedPoints[$teamId]['net_run_rate'] += number_format($point->net_run_rate, 3, '.', '');
            } else {
                $combinedPoints[$teamId] = [
                    'team_id' => $teamId,
                    'matches_played' => $point->matches_played,
                    'wins' => $point->wins,
                    'losses' => $point->losses,
                    'total_points' => $point->total_points,
                    'matches_tied' => $point->matches_tied,
                    'matches_not_played' => $point->matches_not_played,
                    'net_run_rate' => number_format($point->net_run_rate, 3, '.', ''),
                ];
            }
        }

        $response['points'] = array_values($combinedPoints);

        $teamData = [];
        foreach ($response['teams'] as $team) {
            $teamPoints = collect($response['points'])->where('team_id', $team->id);

            $totalMatches = $teamPoints->sum('matches_played');

            $totalWins = $teamPoints->sum('wins');

            $totalPoints = $teamPoints->sum('total_points');
            $totalNetRunRate = $teamPoints->sum('net_run_rate');

            $teamData[] = [
                'id' => $team->id,
                'name' => $team->name,
                'totalMatches' => $totalMatches,
                'totalWins' => $totalWins,
                'totalPoints' => $totalPoints,
                'netRunRate' => number_format($totalNetRunRate,  3, '.', ''), // Ensure NRR is rounded correctly
            ];
        }

        $response['teams'] = $teamData;
    }

    $response['tournaments'] = Tournament::all();

    return response()->json($response);
}




}
