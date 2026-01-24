<?php

namespace App\Http\Controllers\Api;

use DB;
use Log;
use App\Models\Team;
use App\Models\Point;
use App\Models\MatchGame;
use Illuminate\Http\Request;
use App\Helpers\NotificationHelper;
use App\Jobs\SendMatchNotification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

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
            $this->match = MatchGame::where('matches.id', $matchId)
            ->leftJoin('teams as teamA', 'teamA.id', '=', 'matches.teamA_id')
            ->leftJoin('teams as teamB', 'teamB.id', '=', 'matches.teamB_id')
            ->select('matches.*', 'teamA.name as teamA_name', 'teamB.name as teamB_name')->first();
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
                // 'winningTeamOvers' => $winningTeamOvers ?? $request->input('WinningTeamOversFaced') ?? '0.0',
                // 'losingTeamovers' => $losingTeamovers ?? $request->input('LosingTeamOversFaced') ?? '0.0',
            ];

            Log::info('Match data:', $matchData);

            $this->addPointsData($matchData);

            Artisan::call('db:backup', [
                'teamA' => $this->match->teamA_name,
                'teamB' => $this->match->teamB_name,
            ]);
            $output = Artisan::output();

            return response()->json(['message' => 'Points table updated successfully', 'backup_output' => $output], 200);
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
            $this->formatOvers($matchData['WinningTeamOversFaced']),
            $matchData['LosingTeamScore'],
            $matchData['LosingTeamOversFaced']
        );

        $losingTeamNRR = $this->calculateNRR(
            $matchData['LosingTeamScore'],
            $this->formatOvers($matchData['LosingTeamOversFaced']),
            $matchData['WinningTeamScore'],
            $matchData['WinningTeamOversFaced']
        );

        Log::info("Winning team NRR: $winningTeamNRR");
        Log::info("Losing team NRR: $losingTeamNRR");

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

        $text = 'match_end';

        $teamA = Team::find($matchData['winningTeamId']);
        $teamB = Team::find($matchData['losingTeamId']);

        $winningTeamOvers = $this->formatOvers($matchData['WinningTeamOversFaced']);
        $losingTeamovers = $this->formatOvers($matchData['LosingTeamOversFaced']);

        $matchdetails = [
            'WinningTeam' => $teamA->name ,
            'WinningTeamScore' =>  $matchData['WinningTeamScore'],
            'LosingTeamScore' =>  $matchData['LosingTeamScore'],
            'losingTeam' => $teamB->name,
            'WinningTeamOversFaced' =>  $winningTeamOvers ?? '0.0',
            'LosingTeamOversFaced' => $losingTeamovers ?? '0.0',
        ];

        $notificationData = NotificationHelper::prepareMatchNotification($text, $matchdetails);

        // if ($notificationData) {
        //     $notificationController = new NotificationController();
        //     $notificationController->sendPushNotification($notificationData);
        // }
        SendMatchNotification::dispatch($notificationData);
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

        $text = 'match_tied';

        $teamA = Team::find($matchData['teamAID']);
        $teamB = Team::find($matchData['teamBID']);

        $matchDetails = [
            'teamA' => $teamA->name,
            'teamB' => $teamB->name,
            'message' => "{$teamA->name} vs {$teamB->name} ended in a tie! Both teams played incredibly well, and the match concludes with no winner. It's a thrilling outcome!",
        ];

        $notificationData = NotificationHelper::prepareMatchNotification($text, $matchDetails);

        SendMatchNotification::dispatch($notificationData);

        // if ($notificationData) {
        //     $notificationController = new NotificationController();
        //     $notificationController->sendPushNotification($notificationData);
        // }
        SendMatchNotification::dispatch($notificationData);
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
        Log::info("Raw overs1: $overs1, Raw overs2: $overs2");

        if (strpos($overs1, '.') !== false) {
            list($fullOvers, $balls) = explode('.', $overs1);
            $fullOvers = (int)$fullOvers;
            $balls = (int)$balls;
            $totalOvers1 = $fullOvers + ($balls / 6);
        } else {
            $totalOvers1 = (float)$overs1;
        }

        Log::info("Converted overs1: $totalOvers1, Converted overs2: $overs2");

        if ($totalOvers1 > 0 && $overs2 > 0) {
            return ($score1 / $totalOvers1) - ($score2 / $overs2);
        }

        return 0;
    }

    private function formatOvers($decimalOvers) {
        $totalBalls = round($decimalOvers * 6);

        $overs = intdiv($totalBalls, 6);
        $remainingBalls = $totalBalls % 6;

        return $overs . "." . $remainingBalls;
    }

}
