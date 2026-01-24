<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MVP;
use App\Models\MatchGame;
use App\Models\ScoreBoard;
use App\Models\BowlerScoreBoard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class MVPController extends Controller
{
    public function publishMVP(Request $request)
{

    $validated = $request->validate([
        'match_id' => 'required|integer|exists:matches,id',
        'team_id' => 'required|integer|exists:teams,id',
        'player_id' => 'required|integer|exists:players,id',
        'mvp_id' => 'nullable|integer|exists:mvps,id',
    ]);
      
    $match = MatchGame::findOrFail($validated['match_id']);


    $battingStats = ScoreBoard::where([
        'match_id' => $validated['match_id'],
        'team_id' => $validated['team_id'],
        'batter_id' => $validated['player_id']
    ])->first();

    $bowlingStats = BowlerScoreBoard::where([
        'match_id' => $validated['match_id'],
        'team_id' => $validated['team_id'],
        'bowler_id' => $validated['player_id']
    ])->first();


    $mvpData = [
        'match_id' => $validated['match_id'],
        'tournament_id' => $match->tournament_id,
        'team_id' => $validated['team_id'],
        'player_id' => $validated['player_id'],
        'runs' => $battingStats->runs ?? 0,
        'balls_faced' => $battingStats->balls_faced ?? 0,
        'fours' => $battingStats->fours ?? 0,
        'sixes' => $battingStats->sixes ?? 0,
        'strike_rate' => $battingStats->strike_rate ?? 0,
        'overs_bowled' => $bowlingStats->overs_bowled ?? 0,
        'wickets' => $bowlingStats->wickets ?? 0,
        'runs_conceded' => $bowlingStats->runs_conceded ?? 0,
        'economy' => $bowlingStats->economy ?? 0,
        'published_at' => now(),
        'published_by' => 1 ,
    ];

    try {
      
        if (isset($validated['mvp_id'])) {
            
            $mvp = MVP::findOrFail($validated['mvp_id']);
            //$mvp = DB::table('mvps')->where('id', $validated['mvp_id'])->firstOrFail();

            $mvp->update($mvpData);
            $message = 'MVP updated successfully';
        } else {
            // Create new MVP
            $mvp = MVP::create($mvpData);
            $message = 'MVP published successfully';
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $mvp->load(['player', 'team'])
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Something went wrong: ' . $e->getMessage(),
        ], 500);
    }
}

    public function getMVP($matchId)
    {
        $mvps = MVP::where('match_id', $matchId)
                   ->with(['player', 'team'])
                   ->orderBy('created_at', 'desc')
                   ->get();

        if ($mvps->isEmpty()) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'No MVPs found for this match'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $mvps
        ]);
    }
}