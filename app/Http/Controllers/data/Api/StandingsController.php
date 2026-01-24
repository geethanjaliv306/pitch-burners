<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
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
        $response = [
            'groups' => [],
            'teams' => [],
            'rounds' => [],
            'tournaments' => Tournament::all(),
            'points' => [],
            'tournamentGroups' => [],
        ];

        $tournamentId = $request->input('tournament_id');
        
        if (!$tournamentId) {
            return response()->json($response);
        }

        // Load tournament related data
        $tournamentGroups = $this->getTournamentGroups($tournamentId);
        $response['tournamentGroups'] = $tournamentGroups;
        $response['groups'] = $this->getGroups($tournamentId);
        $response['rounds'] = $this->getRounds($tournamentId);

        // Calculate points and team statistics
        $combinedPoints = $this->calculateGroupPoints($tournamentId, $response['groups']);
        $response['points'] = array_values($combinedPoints);
        $response['teams'] = $this->calculateTeamStatistics(
            Team::whereIn('id', $tournamentGroups->pluck('team_id')->unique())->get(),
            collect($response['points'])
        );

        return response()->json($response);
    }

    private function getTournamentGroups(int $tournamentId)
    {
        return TournamentGroup::where('tournament_id', $tournamentId)->get();
    }

    private function getGroups(int $tournamentId)
    {
        return Group::whereIn('id', function ($query) use ($tournamentId) {
            $query->select('group_id')
                  ->from('tournament_groups')
                  ->where('tournament_id', $tournamentId);
        })->get();
    }

    private function getRounds(int $tournamentId)
    {
        return TournamentRound::where('tournament_id', $tournamentId)->get();
    }

    private function calculateGroupPoints(int $tournamentId, $groups)
    {
        $combinedPoints = [];
        
        foreach ($groups as $group) {
            $teamIds = TournamentGroup::where('tournament_id', $tournamentId)
                                    ->where('group_id', $group->id)
                                    ->pluck('team_id');

            $combinedPoints[$group->id] = $this->getTeamPointsQuery($teamIds, $group->id, $tournamentId)
                                             ->get();
        }

        return $combinedPoints;
    }

  private function getTeamPointsQuery($teamIds, $groupId, $tournamentId)
{
    return Team::whereIn('teams.id', $teamIds)
        ->leftJoin('tournament_groups as tg', function($join) use ($groupId, $tournamentId) {
            $join->on('teams.id', '=', 'tg.team_id')
                ->where('tg.group_id', $groupId)
                ->where('tg.tournament_id', $tournamentId);
        })
        ->leftJoin('points', function ($join) use ($tournamentId) {
            $join->on('teams.id', '=', 'points.team_id')
                ->on('tg.group_id', '=', 'points.group_id')
                ->where('points.tournament_id', $tournamentId);
        })
        ->select([
            'teams.*',
            //'teams.id',
            //'teams.name',
            //'teams.created_at',
           // 'teams.updated_at',
            //'teams.deleted_at',
            'tg.group_id',
            'tg.tournament_id', 
            'points.round_id',
            DB::raw('COALESCE(SUM(points.matches_played), 0) as matches_played'),
            DB::raw('COALESCE(SUM(points.wins), 0) as wins'),
            DB::raw('COALESCE(SUM(points.losses), 0) as losses'),
            DB::raw('COALESCE(SUM(points.matches_not_played), 0) as matches_not_played'),
            DB::raw('COALESCE(SUM(points.matches_tied), 0) as matches_tied'),
            DB::raw('COALESCE(AVG(points.net_run_rate), 0) as net_run_rate'),
            DB::raw('COALESCE(SUM(points.total_points), 0) as total_points')
        ])
        ->groupBy('teams.id', 'tg.group_id', 'tg.tournament_id' , 'points.round_id') // Added tournament_id to groupBy
        ->orderBy('total_points', 'desc')
        ->orderBy('net_run_rate', 'desc');
}


    private function calculateTeamStatistics($teams, $pointsCollection)
    {
        return $teams->map(function ($team) use ($pointsCollection) {
            $teamPoints = $pointsCollection->where('team_id', $team->id);
            
            return [
                'id' => $team->id,
                'name' => $team->name,
                'totalMatches' => $teamPoints->sum('matches_played'),
                'totalWins' => $teamPoints->sum('wins'),
                'totalPoints' => $teamPoints->sum('total_points'),
                'netRunRate' => number_format(
                    $teamPoints->sum('net_run_rate'),
                    3,
                    '.',
                    ''
                ),
            ];
        })->toArray();
    }

  
   public function getgroupbasedteamdata(Request $request)
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
