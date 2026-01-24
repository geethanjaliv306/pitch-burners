<?php

namespace App\Http\Controllers\Api;

use App\Models\MatchScore;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\MatchGame;
use App\Services\DLSCalculationService;
use Illuminate\Validation\ValidationException;

class DLSController extends Controller
{
    private DLSCalculationService $dlsService;

    public function __construct(DLSCalculationService $dlsService)
    {
        $this->dlsService = $dlsService;
    }

    public function getDLSParscore(Request $request): JsonResponse
    {
        Log::info('Calculating DLS target...');
        try {
            // Validate the request
            $validated = $request->validate([
                'match_id' => 'required|integer',
                'oversChanges' => 'nullable|numeric|min:0|max:10',
            ]);

            $matchId = $validated['match_id'];
            $secondInningsOversChangedDueToRain = $validated['oversChanges'] ?? null;

            // Parse second innings data
            $secondInningsScore = $request->input('secondinningscore', '0/0');
            $secondInningsOvers = $request->input('secondinningsovers', '0/0');

            Log::info("Parsed second innings data: $secondInningsScore, $secondInningsOvers");

            // Safely parse second innings runs and wickets
            $scoreParts = explode('/', $secondInningsScore);
            $secondInningsTotalRuns = isset($scoreParts[0]) ? (int)$scoreParts[0] : 0;
            $secondInningsTotalWickets = isset($scoreParts[1]) ? (int)$scoreParts[1] : 0;

            // Safely parse overs faced and total overs
            $oversParts = explode('/', $secondInningsOvers);
            $secondInningsOversFaced = isset($oversParts[0]) ? (float)$oversParts[0] : 0.0;
            $totalOvers = isset($oversParts[1]) ? (int)$oversParts[1] : 0;

            Log::info("Parsed second innings: Runs - $secondInningsTotalRuns, Wickets - $secondInningsTotalWickets, Overs - $secondInningsOversFaced , totalOvers - $totalOvers");

            // Fetch match data
            $match = MatchScore::where('match_id', $matchId)->get()->keyBy('is_first_inning');

            // Safely get first innings data
             if (!isset($match[1])) {
                return response()->json([
                    'data' =>[
                        'success' => true,
                        'message' => 'First innings data not found',
                        'firstInningOvers' => 'firstInningOvers',
                    ]

                ], 200);
            }
            $firstInnings = $match[1];

            Log::info("First innings data: " . json_encode($firstInnings));

            // Create second innings array with correct structure
            $secondInnings = [
                'total_runs' => $secondInningsTotalRuns,
                'total_wickets' => $secondInningsTotalWickets,
                'overs_faced' => $secondInningsOversFaced,
            ];

            Log::info("secondInningsfullllllllllll" , $secondInnings);

            $oversFacedInt = (int) $secondInnings['overs_faced'];
            // Fetch match details for initial overs
            $matchDetails = MatchGame::find($matchId);
            if (!$matchDetails) {
                return $this->errorResponse('Match details not found', 200);
            }
            $initialOvers = $matchDetails->overs;
            $firstInningsRuns = $firstInnings->total_runs;

            // Revised overs due to rain
            $revisedOvers = $secondInningsOversChangedDueToRain ?? $initialOvers;

            $remainngOvers = $initialOvers - $revisedOvers ;
            $remainngOversLeft = $remainngOvers -  $oversFacedInt;
            Log::info("remainngOversLeftremainngOversLeft" , [$remainngOversLeft]);

            // Validate wickets lost
            $wicketsLost = $secondInnings['total_wickets'];
            if ($wicketsLost > 10) {
                return $this->errorResponse('Invalid number of wickets', 400);
            }

            // Calculate target using DLS
            $target = $this->dlsService->calculateTarget(
                $firstInningsRuns,
                $initialOvers,
                $revisedOvers,
                $wicketsLost,
                $remainngOversLeft
            );

            // Calculate match stats with array access
            $matchStats = $this->calculateMatchStats(
                $target,
                $secondInnings,
                $revisedOvers,
                $totalOvers ,
            );

            return response()->json([
                'success' => true,
                'data' => array_merge(
                    $matchStats,
                    [
                        'target' => $target,
                        'initial_overs' => $initialOvers,
                        'revised_overs' => $revisedOvers,
                        'first_innings_score' => $firstInningsRuns,
                        'current_score' => $secondInnings['total_runs'],
                        'overs_faced' => $secondInnings['overs_faced'],
                        'wickets_lost' => $wicketsLost,
                        'firstInningOvers' => 'firstInningOvers',
                    ]
                ),
            ], 200);
        } catch (ValidationException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        } catch (\Exception $e) {
            Log::error('DLS Calculation Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return $this->errorResponse('Error calculating DLS score: ' . $e->getMessage());
        }
    }

    private function calculateMatchStats(int $target, array $secondInnings, float $revisedOvers, float $Totalovers): array
{
    $requiredRunRate = null;
    $isWinning = false;
    $resourcesAvailable = 100;

    // Convert overs to total balls
    $convertOversToBalls = function (float $overs): int {
        $wholeOvers = (int)$overs;
        $fractionalBalls = round(($overs - $wholeOvers) * 10);
        return ($wholeOvers * 6) + $fractionalBalls;
    };

    // Convert total balls back to overs-and-balls format
    $convertBallsToOvers = function (int $balls): string {
        $overs = intdiv($balls, 6);
        $remainingBalls = $balls % 6;
        return "$overs.$remainingBalls";
    };

    // Calculate total balls
    $oversFacedInBalls = $convertOversToBalls($secondInnings['overs_faced']); // 2.0 overs -> 12 balls
    $totalOversInBalls = $convertOversToBalls($Totalovers); // 4.0 overs -> 24 balls
    $revisedOversInBalls = $convertOversToBalls($revisedOvers); // 1.4 overs -> 10 balls

    // Remaining balls after overs faced
    $remainingBalls = $totalOversInBalls - $oversFacedInBalls; // 24 - 12 = 12 balls

    // Adjust remaining balls after revised overs
    $finalRemainingBalls = $remainingBalls - $revisedOversInBalls; // 12 - 10 = 2 balls

    // Convert remaining balls to overs format
    $remainingOvers = $convertBallsToOvers($finalRemainingBalls); // 2 balls -> 0.2 overs

    Log::info("Revised Overs: $revisedOvers");
    Log::info("Target: $target");
    Log::info("Remaining Runs: " . ($target - $secondInnings['total_runs']) . ", Remaining Overs: $remainingOvers");

    // Calculate resources available
    $resourcesAvailable = $this->dlsService->getResource(floatval($remainingOvers), $secondInnings['total_wickets']);

    // Calculate required run rate
    if ($finalRemainingBalls > 0) {
        $remainingRuns = $target - $secondInnings['total_runs'];
        $requiredRunRate = round($remainingRuns / ($finalRemainingBalls / 6), 2);
    }

    // Determine if the team is winning
    $isWinning = $secondInnings['total_runs'] >= $target;

    return [
        'required_run_rate' => $requiredRunRate,
        'is_winning' => $isWinning,
        'resources_available' => $resourcesAvailable,
        'remaining_overs' => $remainingOvers,
    ];
}

    private function errorResponse(string $message, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
        ], $status);
    }
}
