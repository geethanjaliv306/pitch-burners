<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\ContinueMatchState;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ContinueMatchStateController extends Controller
{
    //
     public function saveState(Request $request)
        {
            $validated = $request->validate([
                'match_id' => 'required|integer',
                'state_data' => 'required|array',
            ]);

            $stateData = json_encode($validated['state_data']);
            $matchState = null;

            $matchState = ContinueMatchState::updateOrCreate(
                ['match_id' => $validated['match_id']],
                ['state_data' => $stateData]
            );

            if (!$matchState) {
                return response()->json(['success' => false, 'message' => 'No valid state data provided'], 400);
            }

            return response()->json(['success' => true, 'data' => $matchState]);
        }


        public function getState($match_id)
        {
            $state = ContinueMatchState::where('match_id', $match_id)->first();

            if (!$state) {
                return response()->json(['success' => false, 'message' => 'State not found'], 404);
            }

            $inningsdata = json_decode($state->state_data, true);

            return response()->json([
                'success' => true,
                'state_data' => [
                    'innings' => $inningsdata,
                ]
            ]);
        }

}
