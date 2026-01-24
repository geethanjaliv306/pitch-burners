<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\MatchGame;
use App\Models\Team1Detail;
use App\Models\Team2Detail;
use App\Models\BallByBall;
use App\Models\PlayerFieldingStats;
use App\Models\PlayerBattingStats;
use App\Models\PlayerBowlingStats;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Team;
use App\Models\Player;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class ScoreController extends Controller
{
    public function index(Request $request, $match_id)
    {
        $secondinnings = $request->input('secondinnings');

        $match = MatchGame::where('id', $match_id)->first();

        // Check if the match exists
        if (!$match) {
            return response()->json(['error' => 'Match not found.'], 404);
        }

        // Check if the match exists
        if (!$match) {
            return response()->json(['error' => 'Match not found.'], 404);
        }

        if ($secondinnings) {
            // Swap the batting and bowling teams for the second innings
            $temp = $match->batting;
            $match->batting = $match->bowling;
            $match->bowling = $temp;
            $match->save();

             // Insert a new row in the `BallByBall` table with default/null values for the second innings
        BallByBall::create([
            'match_id' => $match_id,
            'batting_team_id' => $match->batting,
            'bowling_team_id' => $match->bowling,
            'over_number' => -1, // Default value for new innings
            'ball_number' => -1, // Default value for new innings
            'valid_ball_count' => 0,
            'innings_completed' => 1,
            'total_runs' => 0,
            'total_score' => 0,
            'total_overs' => 0,
            'total_wickets' => 0,
            'is_over_completed' => 0,
            'extra_type' => null,
            'is_wicket' => 0,
            'wicket_type' => null,
            'current_run_rate' => 0.0,
            'projected_score' => 0
        ]);

            // Store details for the team that just completed its innings
            $battingTeamId = $temp; // The team that just completed its innings
            $totalRuns = BallByBall::where('match_id', $match_id)
                ->where('batting_team_id', $battingTeamId)
                ->sum('total_runs');

            $totalWickets = BallByBall::where('match_id', $match_id)
                ->where('batting_team_id', $battingTeamId)
                ->sum('is_wicket');

            $totalFours = BallByBall::where('match_id', $match_id)
                ->where('batting_team_id', $battingTeamId)
                ->sum('is_four');

            $totalSixes = BallByBall::where('match_id', $match_id)
                ->where('batting_team_id', $battingTeamId)
                ->sum('is_six');

            $totalBoundaries = $totalFours + $totalSixes;

            $totalOvers = BallByBall::where('match_id', $match_id)
                ->where('batting_team_id', $battingTeamId)
                ->max('total_overs');

            // Update the team that completed the first innings
            DB::table('match_scores')
                ->where('match_id', $match_id)
                ->where('team_id', $battingTeamId)
                ->updateOrInsert(
                    [
                        'match_id' => $match_id,
                        'team_id' => $battingTeamId,
                    ],
                    [
                        'total_runs' => $totalRuns,
                        'total_wickets' => $totalWickets,
                        'total_fours' => $totalFours,
                        'total_sixes' => $totalSixes,
                        'total_boundaries' => $totalBoundaries,
                        'overs_faced' => $totalOvers,
                        'is_first_inning' => 1,
                        'is_second_inning' => 0,
                        'run_rate' => $totalOvers > 0 ? number_format($totalRuns / $totalOvers, 2) : 0, // Calculate run rate
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );

            // Insert or update the second innings details for the new batting team
            $newBattingTeamId = $match->batting; // The team that will now bat
            DB::table('match_scores')->updateOrInsert(
                [
                    'match_id' => $match_id,
                    'team_id' => $newBattingTeamId,
                ],
                [
                    'total_runs' => 0,
                    'total_wickets' => 0,
                    'total_fours' => 0,
                    'total_sixes' => 0,
                    'total_boundaries' => 0,
                    'overs_faced' => 0,
                    'is_first_inning' => 0,
                    'is_second_inning' => 1, // Set as second innings for this team
                    'run_rate' => 0, // Initialize run rate as 0
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            return response()->json(['message' => 'Second innings started, match score updated successfully'], 200);
        }

        $matchScore = DB::table('match_scores')
        ->where('match_id', $match_id)
        ->select('team_id', 'total_runs' , 'is_first_inning')
        ->first();

        $maxScore = DB::table('ball_by_ball')
        ->where('match_id', $match_id)
        ->where('innings_completed', 1)
        ->max('total_score');

        $batting_team_id = $match->batting;

        // Get the batting team name
        $batting_team_name = DB::table('teams')
            ->where('id', $batting_team_id)
            ->value('name');

        // Get the batting team's players
        if ($batting_team_id == $match->teamA_id) {
            $batters = DB::table('team1_details')
                ->join('players', 'team1_details.player_id', '=', 'players.id')
                ->where('team1_details.team_id', $match->teamA_id)
                ->where('team1_details.match_id', $match_id)
                ->where('team1_details.12th_man', '!=', 1)
                ->select('team1_details.*', 'players.name as player_name', 'players.image as player_image', 'players.id as id')
                ->get();
        } else {
            $batters = DB::table('team2_details')
                ->join('players', 'team2_details.player_id', '=', 'players.id')
                ->where('team2_details.team_id', $match->teamB_id)
                ->where('team2_details.match_id', $match_id)
                ->where('team2_details.12th_man', '!=', 1)
                ->select('team2_details.*', 'players.name as player_name', 'players.image as player_image', 'players.id as id')
                ->get();
        }

        // Get the updated bowling team ID
        $bowling_team_id = ($batting_team_id == $match->teamA_id) ? $match->teamB_id : $match->teamA_id;

        // Get the bowling team name
        $bowling_team_name = DB::table('teams')
            ->where('id', $bowling_team_id)
            ->value('name');

        // Get the bowling team's players
        if ($bowling_team_id == $match->teamA_id) {
            $bowlers = DB::table('team1_details')
                ->join('players', 'team1_details.player_id', '=', 'players.id')
                ->where('team1_details.team_id', $match->teamA_id)
                ->where('team1_details.match_id', $match_id)
                ->where('team1_details.12th_man', '!=', 1)
                ->select('team1_details.*', 'players.name as player_name', 'players.image as player_image', 'players.id as id')
                ->get();
        } else {
            $bowlers = DB::table('team2_details')
                ->join('players', 'team2_details.player_id', '=', 'players.id')
                ->where('team2_details.team_id', $match->teamB_id)
                ->where('team2_details.match_id', $match_id)
                ->where('team2_details.12th_man', '!=', 1)
                ->select('team2_details.*', 'players.name as player_name', 'players.image as player_image', 'players.id as id')
                ->get();
        }

        // Return the view with the updated data
        return view('admin.run-scorer', compact('match', 'batters', 'bowlers', 'batting_team_name', 'bowling_team_name','matchScore','maxScore'));
    }


    public function saveBallData(Request $request)
     {
        $ballData = $request->all();

          // Get the latest over number bowled by this bowler in the current match
        $lastBowlerStats = PlayerBowlingStats::where('match_id', $ballData['match_id'])
            ->where('player_id', $ballData['bowler_id'])
            ->latest('overs_bowled')
            ->first();

            if ($lastBowlerStats) {
                $lastOver = $lastBowlerStats->overs_bowled;

                if (fmod($lastOver, 1) >= 0.5) { // Check if it's the end of an over
                    $totalOvers = floor($lastOver) + 1.0; // Start a new over as an integer
                } else {
                    $totalOvers = $lastOver + 0.1; // Increment within the over
                }
            } else {
                $totalOvers = 0.1; // Start with 0.1 for the first over for this bowler
            }

            $previousWickets = DB::table('ball_by_ball')
            ->where('match_id', $ballData['match_id'])
            ->orderBy('id', 'desc')
            ->value('total_wickets') ?? 0;

        $newTotalWickets = $ballData['is_wicket'] ? $previousWickets + 1 : $previousWickets;

        // Check if the previous innings was completed
        $previousInningsCompleted = BallByBall::where('match_id', $ballData['match_id'])
        ->where('innings_completed', 1)
        ->exists();


        // Save BallByBall data and capture the generated ID
        $ball = BallByBall::create([
            'match_id' => $ballData['match_id'],
            'batting_team_id' => $ballData['batting_team_id'],
            'bowling_team_id' => $ballData['bowling_team_id'],
            'over_number' => $ballData['over_number'],
            'ball_number' => $ballData['ball_number'],
            'valid_ball_count' => $ballData['valid_ball_count'],
            'innings_completed' => $previousInningsCompleted ? 1 : 0,
            'is_one' => filter_var($ballData['is_one'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
            'is_two' => filter_var($ballData['is_two'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
            'is_three' => filter_var($ballData['is_three'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
            'is_four' => filter_var($ballData['is_four'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
            'is_five' => filter_var($ballData['is_five'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
            'is_six' => filter_var($ballData['is_six'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
            'other_runs' => $ballData['other_runs'],
            'bye_runs' => $ballData['bye_runs'],
            'wide_runs' => $ballData['wide_runs'],
            'extra_runs' => $ballData['extra_runs'],
            'no_ball_runs' => $ballData['no_ball_runs'],
            'bowler_id' => $ballData['bowler_id'],
            'striker_id' => $ballData['striker_id'],
            'non_striker_id' => $ballData['non_striker_id'],
            'fielder_id' => $ballData['fielder_id'],
           'total_runs' => $ballData['total_runs'],
           'total_score' => $ballData['total_score'],
           'total_overs' => $ballData['total_overs'],
           'total_wickets' => $newTotalWickets,
            'is_over_completed' => filter_var($ballData['is_over_completed'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
            'extra_type' => $ballData['extra_type'],
            'is_wicket' => filter_var($ballData['is_wicket'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
            'wicket_type' => $ballData['wicket_type'],
            'current_run_rate' => $ballData['current_run_rate'],
            'projected_score' => $ballData['projected_score']
        ]);

        // Save Batter data (score for this specific ball only)
        // $BatterData = [
        //     'match_id' => $ballData['match_id'],
        //     'team_id' => $ballData['batting_team_id'],
        //     'player_id' => $ballData['striker_id'],
        //     'ball_by_ball_id' => $ball->id,
        //     'score' => (int) $ballData['total_runs'], // Run count for this specific ball
        //     'is_one' => filter_var($ballData['is_one'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
        //     'is_two' => filter_var($ballData['is_two'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
        //     'is_three' => filter_var($ballData['is_three'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
        //     'is_four' => filter_var($ballData['is_four'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
        //     'is_five' => filter_var($ballData['is_five'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
        //     'is_six' => filter_var($ballData['is_six'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
        //     'other_runs' => $ballData['other_runs'],
        //     'bye_runs' => $ballData['bye_runs'],
        //     'strike_rate' => $ballData['current_run_rate'],
        //     'balls_faced' => $ballData['ball_number'],
        //     'is_out' => $ballData['is_wicket']
        // ];
        // PlayerBattingStats::create($BatterData);

        // // Save Bowler data (runs conceded for this specific ball only)
        // $BowlerData = [
        //     'match_id' => $ballData['match_id'],
        //     'team_id' => $ballData['bowling_team_id'],
        //     'ball_by_ball_id' => $ball->id,
        //     'player_id' => $ballData['bowler_id'],
        //    'overs_bowled' => $totalOvers,
        //     'maiden_overs' => 0, // Assuming data is available
        //     'runs_conceded' => (int) $ballData['total_runs'], // Runs conceded on this specific ball
        //     'wickets_taken' => $ballData['is_wicket'] ? 1 : 0,
        //     'economy_rate' => ($ballData['over_number'] > 0) ? ($ballData['total_runs'] / $ballData['over_number']) : 0,
        //     'no_balls' => $ballData['no_ball_runs'],
        //     'wide_balls' => $ballData['wide_runs'],
        //     'extras_bowled' => $ballData['extra_runs'],
        //     'valid_ball_count' => $ballData['valid_ball_count'],
        //     'balls_bowled' => $ballData['ball_number'],
        //     'extra_runs' => $ballData['extra_runs'],
        //     'extras_type' => $ballData['extra_type']
        // ];
        // PlayerBowlingStats::create($BowlerData);

        // // Save Fielding data only if fielder_id is present
        // if (!empty($ballData['fielder_id'])) {
        //     $FieldingData = [
        //         'match_id' => $ballData['match_id'],
        //         'team_id' => $ballData['bowling_team_id'],
        //         'ball_by_ball_id' => $ball->id,
        //         'player_id' => $ballData['fielder_id'],
        //         'catches' => $ballData['fielder_catches'] ?? 0,
        //         'run_outs' => $ballData['fielder_run_outs'] ?? 0,
        //         'stumpings' => $ballData['fielder_stumpings'] ?? 0,
        //         'directHit' => $ballData['fielder_direct_hit'] ?? 0,
        //         'dismissal_type' => $ballData['wicket_type'] ?? null
        //     ];
        //     PlayerFieldingStats::create($FieldingData);
        // }


        return response()->json(['message' => 'Ball data and associated stats saved successfully'], 200);
    }

    public function matchOver(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'match_id' => 'nullable',
            'team_id' => 'nullable',
        ]);

        $matchId = $validatedData['match_id'];
        $winningTeamId = $validatedData['team_id'];

        try {
            // Get the match details
            $match = MatchGame::find($matchId);

            if (!$match) {
                return response()->json(['error' => 'Match not found.'], 404);
            }

            // Calculate second innings data
            $battingTeamId = $match->batting; // The batting team for the second innings
            $totalRuns = BallByBall::where('match_id', $matchId)
                ->where('batting_team_id', $battingTeamId)
                ->sum('total_runs');

            $totalWickets = BallByBall::where('match_id', $matchId)
                ->where('batting_team_id', $battingTeamId)
                ->sum('is_wicket');

            $totalFours = BallByBall::where('match_id', $matchId)
                ->where('batting_team_id', $battingTeamId)
                ->sum('is_four');

            $totalSixes = BallByBall::where('match_id', $matchId)
                ->where('batting_team_id', $battingTeamId)
                ->sum('is_six');

            $totalBoundaries = $totalFours + $totalSixes;

            $totalOvers = BallByBall::where('match_id', $matchId)
                ->where('batting_team_id', $battingTeamId)
                ->max('total_overs');

            // Update match_scores table for the second innings
            DB::table('match_scores')->updateOrInsert(
                [
                    'match_id' => $matchId,
                    'team_id' => $battingTeamId,
                ],
                [
                    'total_runs' => $totalRuns,
                    'total_wickets' => $totalWickets,
                    'total_fours' => $totalFours,
                    'total_sixes' => $totalSixes,
                    'total_boundaries' => $totalBoundaries,
                    'overs_faced' => $totalOvers,
                    'is_first_inning' => 0,
                    'is_second_inning' => 1, // Set as second innings
                    'run_rate' => $totalOvers > 0 ? number_format($totalRuns / $totalOvers, 2) : 0, // Calculate run rate
                    'updated_at' => now(),
                ]
            );

            // Update match status in MatchGame table
            // $match->status = 'completed'; // Update status to "completed"
            $match->save();

            return response()->json(['message' => 'Match details updated successfully.'], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update match details.', 'details' => $e->getMessage()], 500);
        }
    }

}
