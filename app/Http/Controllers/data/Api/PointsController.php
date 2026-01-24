<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MatchGame;
use App\Models\Point;
use DB;
use Log;

class PointsController extends Controller
{
    private $match;
    private $tournament_id;
    private $group_id;
    private $round_id;

    public function updatePoints(Request $request) {
        Log::info('Received payload:', $request->all());

        try {
            $matchId = $request->input('matchId');

            // Retrieve match details
            $this->match = MatchGame::findOrFail($matchId);
            $this->tournament_id = $this->match->tournament_id;
            $this->group_id = $this->match->group_id;
            $this->round_id = $this->match->round_id;

            $matchData = [
                'matchId' => $matchId,
                'winningTeamId' => $request->input('winningTeamId'),
                'losingTeamId' => $request->input('losingTeamId'),
                'isTied' => $request->input('isTied'),
                'isSuperOver' => $request->input('isSuperOver'),
                "SecondSuperOver" => $request->input('SecondSuperOver'),
                'winningTeamRunrate' => (float) $request->input('winningTeamRunrate'),
                'losingTeamRunrate' => (float) $request->input('losingTeamRunrate'),
                'WinningTeamScore' => (int) $request->input('WinningTeamScore'),
                'LosingTeamScore' => (int) $request->input('LosingTeamScore'),
                'WinningTeamOversFaced' => (float) $request->input('WinningTeamOversFaced'),
                'LosingTeamOversFaced' => (float) $request->input('LosingTeamOversFaced'),
                'teamAID' => $request->input('teamAID'),
                'teamBID' => $request->input('teamBID'),
                'eachTwoPoints' => $request->input('eachTwoPoints'),
                'group_id' => $this->group_id,
                'round_id' => $this->round_id,
            ];

            Log::info('Match data:', $matchData);

            $this->addPointsData($matchData);

            return response()->json(['message' => 'Points table updated successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error updating points table: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update points table: ' . $e->getMessage()], 500);
        }
    }

    public function addPointsData(array $matchData) {
        DB::beginTransaction();
        try {
            if ($matchData['isSuperOver'] || $matchData['SecondSuperOver']) {
                $this->addSuperOverPoints($matchData);
            } elseif ($matchData['isTied'] && $matchData['eachTwoPoints']) {
                $this->addTiedMatchPoints($matchData);
            } else {
                $this->addNormalMatchPoints($matchData);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding points: ' . $e->getMessage());
            throw $e;
        }
    }

    private function addNormalMatchPoints($matchData) {
        $winningTeamNRR = $this->calculateNRR(
            $matchData['WinningTeamScore'],
            $matchData['WinningTeamOversFaced'],
            $matchData['LosingTeamScore'],
            $matchData['LosingTeamOversFaced']
        );

        $losingTeamNRR = $this->calculateNRR(
            $matchData['LosingTeamScore'],
            $matchData['LosingTeamOversFaced'],
            $matchData['WinningTeamScore'],
            $matchData['WinningTeamOversFaced']
        );

        // Add data for winning team
        Point::create([
            'match_id' => $this->match->id,
            'team_id' => $matchData['winningTeamId'],
            'tournament_id' => $this->tournament_id,
            'group_id' => $matchData['group_id'],
            'round_id' => $matchData['round_id'],
            'matches_played' => 1,
            'wins' => 1,
            'total_points' => 2,
            'net_run_rate' => $winningTeamNRR,
        ]);

        // Add data for losing team
        Point::create([
            'match_id' => $this->match->id,
            'team_id' => $matchData['losingTeamId'],
            'tournament_id' => $this->tournament_id,
            'group_id' => $matchData['group_id'],
            'round_id' => $matchData['round_id'],
            'matches_played' => 1,
            'losses' => 1,
            'net_run_rate' => $losingTeamNRR,
        ]);
    }

    private function addTiedMatchPoints($matchData) {
        foreach ([$matchData['teamAID'], $matchData['teamBID']] as $teamId) {
            Point::create([
                'match_id' => $this->match->id,
                'team_id' => $teamId,
                'tournament_id' => $this->tournament_id,
                'group_id' => $matchData['group_id'],
                'round_id' => $matchData['round_id'],
                'matches_played' => 1,
                'matches_tied' => 1,
                'total_points' => 1,
            ]);
        }
    }

    private function addSuperOverPoints($matchData) {
        if ($matchData['winningTeamId']) {
            Point::create([
                'match_id' => $this->match->id,
                'team_id' => $matchData['winningTeamId'],
                'tournament_id' => $this->tournament_id,
                'group_id' => $matchData['group_id'],
                'round_id' => $matchData['round_id'],
                'matches_played' => 1,
                'wins' => 1,
                'total_points' => 2,
                'super_over_win' => 1,
                'is_super_over' =>1 ,
            ]);

            Point::create([
                'match_id' => $this->match->id,
                'team_id' => $matchData['losingTeamId'],
                'tournament_id' => $this->tournament_id,
                'group_id' => $matchData['group_id'],
                'round_id' => $matchData['round_id'],
                'matches_played' => 1,
                'losses' => 1,
                'super_over_loss' => 1,
                'is_super_over' => 1 ,
            ]);
        }
    }

    private function calculateNRR($score1, $overs1, $score2, $overs2) {
        if ($overs1 > 0 && $overs2 > 0) {
            return ($score1 / $overs1) - ($score2 / $overs2);
        }
        return 0;
    }
}
