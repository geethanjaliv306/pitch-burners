<?php

namespace App\Http\Controllers\Api;

use App\Models\Penalty;
use App\Models\MatchGame;
use App\Models\BallByBall;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PenaltyController extends Controller
{
    public function index()
    {
        $penalties = Penalty::get();
        return response()->json($penalties);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'match_id' => 'required|exists:matches,id',
            'team_id' => 'nullable|exists:teams,id',
            'runs' => 'required|integer',
            'reason' => 'required|string|max:255',
            'inning' => 'required|integer'
        ]);
        $match_id = $validated['match_id'];

        $match = MatchGame::findOrFail($match_id);

        if(strtolower($match->status) !== 'active') {
            return response()->json(['error' => "Match's not active"]);
        }

        $ballByBall = BallByBall::where('match_id', $match_id)
        ->where('over_number', '!=', -1)->orderBy('id','desc')->select('total_overs')->first();

        $validated['tournament_id'] = null;
        $penalty = Penalty::create($validated);

        return response()->json([
            'penalty' => $penalty,
            'overs' => $ballByBall,
        ], 201);
    }

    public function show(int $id, int $inning)
    {
        $penalties = Penalty::where('penalties.match_id', $id)
        ->where('inning', $inning)
        ->get();

        $teamScore = BallByBall::where('match_id', $id)
        ->where('innings_completed', $inning)
        ->orderBy('id', 'desc')->pluck('total_score')->first() ?? 0;


        foreach($penalties as $penalty) {
            $obj = new \stdClass();
            $obj->id = $penalty->team_id;
            $obj->name = $penalty->name;

            $penalty['team'] = $obj;
        }



        return response()->json([
            'teamScore' => $teamScore,
            'penalties' => $penalties,
        ]);
    }

    public function update(Request $request, Penalty $penalty)
    {
        $validated = $request->validate([
            'team_id' => 'nullable|exists:teams,id',
            'runs' => 'sometimes|integer',
            'reason' => 'sometimes|string|max:255',
        ]);


        $penalty->update($validated);

        return response()->json($penalty);
    }

    public function destroy(Penalty $penalty)
    {
        $penalty->delete();

        return response()->json(['message' => 'Penalty deleted']);
    }
}
