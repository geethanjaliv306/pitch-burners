<?php

namespace App\Http\Controllers\Api;

use stdClass;
use Exception;
use App\Models\Team;
use App\Models\Point;
use App\Models\Player;
use App\Models\MatchGame;
use App\Models\BallByBall;
use App\Models\Commentary;
use App\Models\ScoreBoard;
use App\Models\MatchPlayer;
use App\Models\Team1Detail;
use App\Models\Team2Detail;
use App\Models\FallOfWicket;
use Illuminate\Http\Request;
use App\Jobs\ProcessBallData;
use App\Models\ScheduleMatch;
use App\Models\BowlerScoreBoard;
use App\Models\PlayerBattingStats;
use App\Models\PlayerBowlingStats;
use Illuminate\Support\Facades\DB;
use App\Models\PlayerFieldingStats;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Api\MatchesController;
use App\Http\Controllers\Api\ScoreBoardController;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use App\Models\Penalty;

class ScoringController extends Controller
{
    


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            // Ball by ball
            'match_id' => 'required',
            'batting_team_id' => 'required',
            'bowling_team_id' => 'required',
            'over_number' => 'required',
            'ball_number' => 'required',
            'valid_ball_count' => 'required',
            'is_one' => 'nullable|boolean',
            'is_two' => 'nullable|boolean',
            'is_three' => 'nullable|boolean',
            'is_four' => 'nullable|boolean',
            'is_five' => 'nullable|boolean',
            'is_six' => 'nullable|boolean',
            'other_runs' => 'nullable',
            'bye_runs' => 'nullable',
            'bowler_id' => 'required',
            'striker_id' => 'required',
            'non_striker_id' => 'required',
            'fielder_id' => 'nullable',
            'total_runs' => 'required',
            'is_over_completed' => 'nullable|boolean',
            'extra_type' => 'nullable',
            'is_wicket' => 'required|boolean',
            'wicket_type' => 'nullable',
            'total_runs_ball_by_ball' => 'nullable',
            'ball_faced_player_id' => 'nullable',
            'ProjectedScore' => 'nullable',
            'current_run_rate' => 'nullable',
            'strikerOnStrike' => 'nullable',
            'displayRunVal' => 'nullable ',
            'dismissedPlayer' => 'nullable',
            'totalscore' => 'nullable' ,
            'currentovers'=> 'nullable',
          'ballnumberforundo' => 'nullable' ,

            // Batting
            // 'score' => 'nullable',
            'score' => 'nullable|numeric',
            'batter_balls_faced' => 'nullable',
            'batter_is_out' => 'required|boolean',
            'strike_rate' => 'nullable',
            'dismissal_type' => 'nullable',
            'is_striker_on_strike' => 'nullable|boolean',
            'retiredoutplayer' => 'nullable' ,
            'RunOUtbatsmen_runscored' => 'nullable',
            'runOutPlayer' => 'nullable' ,
            'strikerOrNonstriker' => 'nullable' ,
            'non_striker_id' => 'nullable' ,
            'Dismissedplayeroutype' => 'nullable',
            'striker' => 'nullable' ,
            'nonstriker' => 'nullable' ,

            // Bowling
            'bowler_overs_bowled' => 'required',
            'bowler_maiden_overs' => 'required',
            'bowling_runs_conceded' => 'required',
            'bowling_wickets_taken' => 'required',
            'bowler_economy_rate' => 'required',
            'no_balls' => 'nullable',
            'bowler_wide_balls' => 'nullable',
            'extras_bowled' => 'nullable',
            'batsmanHitBall' => 'nullable|boolean',


            // Fielding
            'throwing_end_id' => 'nullable',
            'directHit' => 'nullable',
            'bowled' => 'nullable',
            'fielding_caught_behind' => 'nullable',
            'fielding_caught_and_bowled' => 'nullable',
            'fielding_run_outs' => 'nullable',
            'fielding_stumps' => 'nullable',
            'retired_hurt' => 'nullable',
            'fielding_mankaded' => 'nullable',
            'hit_wicket' => 'nullable',
            'retired_out' => 'nullable',
            'dismissal_type' => 'nullable',
            'fielding_catches' => 'nullable',
        ]);

        Log::info("Dismissedplayeroutype" , [$validatedData['Dismissedplayeroutype']]);
		
		$match__id = $validatedData['match_id'];
      	$is_same_ball_exist = BallByBall::where('match_id', $match__id)->where('batting_team_id', $validatedData['batting_team_id'])->where('ball_number_for_undo', ((int) $validatedData['ballnumberforundo'] + 1))->exists();
      	if($is_same_ball_exist)  {
         return response()->json([
                'message' => 'tried duplicate entry',
                'synced' => ':heavy_tick: Synched',
                'status' => 200,
              	'ball_number_for_undo' => $validatedData['ballnumberforundo'],
           	
         ], 200);
         exit();
        }
        // Start a transaction
        DB::beginTransaction();
        try {

            // ProcessBallData::dispatch($validatedData)->onQueue('ball_data');

            $ExtraType = $validatedData['extra_type'];
            $ExtraRun = 0;

            if ($ExtraType === 'WD' || $ExtraType === 'NB') {
                $ExtraRun = 1;
            } elseif (strpos($ExtraType, 'WD+') === 0 || strpos($ExtraType, 'NB+') === 0) {
                $parts = explode('+', $ExtraType);
                $ExtraRun = 1 + (int)$parts[1];  // 1 run for wide/no-ball plus the extra runs
            }

            $playerId = $validatedData['ball_faced_player_id'];

            if($playerId == 'null' || $playerId == ''){
                if($validatedData['strikerOnStrike'] == true || $validatedData['strikerOnStrike'] == 'true'){
                    $playerId = $validatedData['non_striker_id'];
                }else{
                    $playerId = $validatedData['striker_id'];
                }

            }

            if ($playerId == $validatedData['striker_id']) {
                $strikerId = $validatedData['striker_id'];
                $nonStrikerId = $validatedData['non_striker_id'];
            } else {
                $strikerId = $validatedData['non_striker_id'];
                $nonStrikerId = $validatedData['striker_id'];
            }

            if($validatedData['is_wicket'] && $playerId == 'null' || $playerId == ''){
                if($validatedData['strikerOnStrike'] == true || $validatedData['strikerOnStrike'] == 'true'){
                    $dismissedBatsmen = $validatedData['non_striker_id'];
                }else{
                    $dismissedBatsmen = $validatedData['striker_id'];
                }
            }

            // $previousTotalScore = DB::table('ball_by_ball')
            // ->where('match_id', $validatedData['match_id'])
            // ->orderBy('id', 'desc')
            // ->value('total_score') ?? 0;

            // if($ExtraType === 'WD' || $ExtraType === 'NB' || strpos($ExtraType, 'WD+') === 0 || strpos($ExtraType, 'NB+') === 0 ){
            //     $newTotalScore = $previousTotalScore + $ExtraRun;
            // }else {
            //     $newTotalScore = $previousTotalScore + $validatedData['total_runs'] +$ExtraRun;
            // }


            $previousWickets = DB::table('ball_by_ball')
                ->where('match_id', $validatedData['match_id'])
                ->orderBy('id', 'desc')
                ->value('total_wickets') ?? 0;

            $newTotalWickets = $validatedData['is_wicket'] ? $previousWickets + 1 : $previousWickets;

            $totalOvers = $validatedData['over_number'];
            Log::info("totalOversdaraaaaaaaaaaa," , [$totalOvers]);

            // if ($validatedData['valid_ball_count'] == 6) {
            //     $totalOvers .= '.0';
            // } else {
            //     $totalOvers .= '.' . $validatedData['valid_ball_count'];
            // }

            // if ($totalOvers > 0 && $totalOvers <= 1.0) {
            //     $over_number = 1;
            // } else {
            //     $over_number = ($totalOvers == floor($totalOvers)) ? floor($totalOvers) : floor($totalOvers) + 1;
            // }

            $previoustBallByBallData = DB::table('ball_by_ball')
            ->where('match_id', $validatedData['match_id'])
            ->orderBy('id', 'desc')
            ->first();

            $previousTotalScore  = isset($previoustBallByBallData) ? $previoustBallByBallData->total_score : 0;
            $previous_valid_ball_count = isset($previoustBallByBallData) ? $previoustBallByBallData->valid_ball_count : 0;

            $over_number = $validatedData['over_number'];
            $valid_ball_count = $validatedData['valid_ball_count'];
            $totalOvers = "$over_number.$valid_ball_count";

			$wicket_type = $validatedData['wicket_type'];
            if($ExtraType === 'WD' || $ExtraType === 'NB' || strpos($ExtraType, 'WD+') === 0 || strpos($ExtraType, 'NB+') === 0 || in_array(str_replace(' ', '', strtolower($wicket_type)), ['runout(mankaded)', 'retiredhurt', 'retiredout'])){
                $newTotalScore = $previousTotalScore + $ExtraRun;
                if($valid_ball_count == 0) {
                    $valid_ball_count = 1;
                    $totalOvers = "$over_number.$valid_ball_count";
                    $over_number = $over_number + 1;
                }
                if($valid_ball_count != 0 && $valid_ball_count == $previous_valid_ball_count) {
                    $valid_ball_count += 1;
                    if($valid_ball_count >= 6) {
                        $valid_ball_count = 0;
                        $totalOvers = ($over_number + 1) . ".$valid_ball_count";
                    }else  {
                        $totalOvers = "$over_number.$valid_ball_count";
                    }
                    $over_number = $over_number + 1;
                }
            }else {
                $newTotalScore = $previousTotalScore + $validatedData['total_runs'] +$ExtraRun;
                if($valid_ball_count == 6) {
                    $valid_ball_count = 0;
                }
                $totalOvers = "$over_number.$valid_ball_count";
                $over_number = strpos($totalOvers,'.0') >= 1 ? $over_number : $over_number + 1;
            }

           $lastBall = BallByBall::where('match_id', $validatedData['match_id'])
                      ->lockForUpdate()
                      ->orderBy('ball_number_for_undo', 'desc')
                      ->first();

            if ($lastBall) {
                $nextBallNumber = $lastBall->ball_number_for_undo + 1;
            } else {
                $nextBallNumber = 1;
            }

            $noballBoundary = false ;
            $noballSix = false ;

            if(($validatedData['batsmanHitBall'] === true) && substr($ExtraType , 0 , 2) === 'NB'){
               $noballruns = explode('+' , $ExtraType);
               $runCount = (int)$noballruns[1];
               Log::info("runCount: $runCount");

               if((int)$runCount === 6){
                 $noballSix= true ;
                 Log::info("noballSix inside: $noballSix");

               }else if((int)$runCount === 4){
                  $noballBoundary = true ;
               }else{
                 $noballSix = false ;
                 $noballBoundary = false ;
               }
            }

            Log::info("noballSix: $noballSix");
            Log::info("noballBoundary: $noballBoundary");

            $ballByBallTotalScore = $validatedData['totalscore'] ?? $newTotalScore;
            $bbInning = $request->current_innings ?? 0;

            $penaltyScore = Penalty::where('match_id', $validatedData['match_id'])
            ->where('inning', $bbInning)->sum('runs') ?? 0;

            $ballByBallTotalScore = abs((int) $ballByBallTotalScore - (int) $penaltyScore);

            $ballData = [
            'match_id' => $validatedData['match_id'],
            'batting_team_id' => $validatedData['batting_team_id'],
            'bowling_team_id' => $validatedData['bowling_team_id'],
            'over_number' => $over_number ,
            // 'over_number' => $validatedData['currentovers'],
            'ball_number' => $validatedData['ball_number'],
            'valid_ball_count' => $validatedData['valid_ball_count'],
            'is_one' => $validatedData['is_one'] ?? 0,
            'is_two' => $validatedData['is_two'] ?? 0,
            'is_three' => $validatedData['is_three'] ?? 0,
            'is_four' => $validatedData['is_four'] ?? 0,
            'is_five' => $validatedData['is_five'] ?? 0,
            'is_six' => $validatedData['is_six'] ?? 0,
            'other_runs' => $validatedData['other_runs'],
            'bye_runs' => $validatedData['bye_runs'],
            'bowler_id' => $validatedData['bowler_id'],
            'striker_id' =>  $strikerId,
            'non_striker_id' => $nonStrikerId,
            'fielder_id' => $validatedData['fielder_id'] ??
            $validatedData['fielding_caught_behind']
                ?? $validatedData['fielding_caught_and_bowled']
                ?? $validatedData['fielding_mankaded']
                ?? $validatedData['bowled']
                ?? $validatedData['fielder_id']
                ?? $validatedData['bowled'] ?? null,
            'total_runs' => $validatedData['total_runs'] + (int) $validatedData['RunOUtbatsmen_runscored'] + (isset($validatedData['wicket_type']) && in_array($validatedData['wicket_type'], ['NB+W', 'WD+W']) ? 1 : 0),
            'is_over_completed' => $validatedData['is_over_completed'] ?? 0,
            'extra_type' => $ExtraType,
            // 'is_wicket' =>in_array($validatedData['Dismissedplayeroutype']['dismissaltype'], ['Retired Hurt']) ?? $validatedData['is_wicket'],
            'is_wicket' => isset($validatedData['Dismissedplayeroutype']['dismissaltype']) &&
               in_array($validatedData['Dismissedplayeroutype']['dismissaltype'], ['Retired Hurt'])
                  ? 2
                  : $validatedData['is_wicket'],
            'wicket_type' => $validatedData['wicket_type'],
            // 'dismissal_type' => $validatedData['dismissal_type']
            'projected_score' =>  $validatedData['ProjectedScore'],
            'current_run_rate' =>  $validatedData['current_run_rate'],
            //'extra_runs' => $ExtraRun,
            'extra_runs' => ($validatedData['batsmanHitBall'] === true && strpos($ExtraType, 'NB+') === 0) ? ($ExtraRun  - ((int) explode('+', $ExtraType)[1])) : $ExtraRun,
            'innings_completed' => $request->current_innings ?? 0 ,
            'is_striker_on_strike' => $validatedData['strikerOnStrike'] == true ? 1 : 0 ,
            'total_score' =>  $ballByBallTotalScore,
            'total_wickets' => $newTotalWickets ,
            'total_overs' => $totalOvers ,
            // 'total_overs' => $validatedData['currentovers'],
            'display_run' => $validatedData['displayRunVal'] ,
            'dismissed_batsmen' =>$validatedData['dismissedPlayer'] ?? $dismissedBatsmen ?? null,
            'ball_number_for_undo' => $validatedData['ballnumberforundo'] + 1 ?? $nextBallNumber  ?? null,


        ];

        Log::info('ball data: ' . json_encode($ballData));

        // ProcessBallData::dispatch($validatedData)->onQueue('ball_data');
        // Create the BallByBall record
        $ball = BallByBall::create($ballData);


        $ExtraType = $validatedData['extra_type'];
        $isNoBallOrWide = $ExtraType;

        $BatterData = [
            'match_id' => $validatedData['match_id'],
            'team_id' => $validatedData['batting_team_id'],
            // 'player_id' => $playerId,
            'player_id' =>  $validatedData['retiredoutplayer'] ? $validatedData['retiredoutplayer'] : $playerId,
            'ball_by_ball_id' => $ball->id,
        ];

        Log::info('BatterData', ['player_id' => $BatterData['player_id']]);


        $previousBallData= [] ;

        if($validatedData['batter_is_out'] == 1){
            $playerId = $BatterData['player_id'] ;
            $previousBallData = DB::table('player_batting_stats')
            ->where('player_id', $playerId)
            ->where('match_id', $validatedData['match_id'])
            ->orderBy('id', 'desc')
            ->first();
        }else{
            $previousBallData = DB::table('player_batting_stats')
            ->where('player_id', $playerId)
            ->where('match_id', $validatedData['match_id'])
            ->orderBy('id', 'desc')
            ->first();
        }


        Log::info('dismissal_type', [$validatedData['dismissal_type']]);
        Log::info('batter_is_out', [$validatedData['batter_is_out']]);


        // Processes cricket ball data, handling different dismissal scenarios, runs, and batter statistics
        if ($validatedData['batter_is_out'] == 1) {
            Log::info('herecoming1');

            // Handle various dismissal types
            if (isset($validatedData['Dismissedplayeroutype']) &&
                is_array($validatedData['Dismissedplayeroutype']) &&
                isset($validatedData['Dismissedplayeroutype']['StrikerandNonstriker']) &&
                isset($validatedData['Dismissedplayeroutype']['StrikerandNonstriker']['dismissaltype']) &&
                !in_array($validatedData['Dismissedplayeroutype']['StrikerandNonstriker']['dismissaltype'], ['Wide & Stumped', 'Wide and Hit Wicket']) &&
                in_array($validatedData['Dismissedplayeroutype']['StrikerandNonstriker']['dismissaltype'],
                    ['Bowled', 'Caught', 'Caught Behind', 'Caught & Bowled', 'Stumped', 'Hit Wicket', 'LBW'])
            ) {
                $StrikerOrNonstriker = $validatedData['Dismissedplayeroutype']['StrikerandNonstriker']['Striker']
                    ?? $validatedData['Dismissedplayeroutype']['StrikerandNonstriker']['Nonstriker']
                    ?? null;

                Log::info('Outype Dismissal Processing', [
                    'StrikerOrNonstriker' => $StrikerOrNonstriker
                ]);

                // Parse score
                $scoreParts = explode('(', rtrim($StrikerOrNonstriker['score'], ')'));
                $runs = isset($scoreParts[0]) ? intval(trim($scoreParts[0])) : 0;
                $ballsFaced = isset($scoreParts[1]) ? intval(trim($scoreParts[1])) : 0;

                $strikeRate = $ballsFaced + 1 > 0
                    ? round(($runs / ($ballsFaced + 1)) * 100, 2)
                    : 0;

                $BatterData = array_merge($BatterData, [
                    'player_id' => $StrikerOrNonstriker['id'] ?? null,
                    // 'player_name' => $nonstriker['name'] ?? null,
                    'score' => $runs,
                    'balls_faced' => $ballsFaced + 1,
                    'strike_rate' => $strikeRate,
                    'is_out' => 1,
                    'dismissal_type' => $validatedData['Dismissedplayeroutype']['StrikerandNonstriker']['dismissaltype'],
                    'one' => 0,
                    'two' => 0,
                    'three' => 0,
                    'four' => 0,
                    'five' => 0,
                    'six' => 0,
                    'other_runs' => 0,
                    'bye_runs' => 0
                ]);

                // Log warning if no player ID
                if (empty($StrikerOrNonstriker['id'])) {
                    Log::warning('Outype Dismissal: No player ID found', [
                        'player_details' => $StrikerOrNonstriker
                    ]);
                }
            }
            //Retired hurt
            else if (isset($validatedData['Dismissedplayeroutype']) && is_array($validatedData['Dismissedplayeroutype'])&& isset($validatedData['Dismissedplayeroutype']['runOutPlayer'])
                && isset($validatedData['Dismissedplayeroutype']['dismissaltype']) && in_array($validatedData['Dismissedplayeroutype']['dismissaltype'], ['Retired Hurt'])
            ) {
                $StrikerOrNonstriker = $validatedData['Dismissedplayeroutype']['runOutPlayer']
                    ?? null;

                Log::info('Retired hurt Dismissal Processing', [
                    'StrikerOrNonstriker' => $StrikerOrNonstriker
                ]);

                // Parse score
                $scoreParts = explode('(', rtrim($StrikerOrNonstriker['score'], ')'));
                $runs = isset($scoreParts[0]) ? intval(trim($scoreParts[0])) : 0;
                $ballsFaced = isset($scoreParts[1]) ? intval(trim($scoreParts[1])) : 0;

                $strikeRate = $ballsFaced > 0
                    ? round(($runs / ($ballsFaced )) * 100, 2)
                    : 0;

                $BatterData = array_merge($BatterData, [
                    'player_id' => $StrikerOrNonstriker['id'] ?? null,
                    // 'player_name' => $nonstriker['name'] ?? null,
                    'score' => $runs,
                    'balls_faced' => $ballsFaced,
                    'strike_rate' => $strikeRate,
                    'is_out' => 2,
                    'dismissal_type' => $validatedData['Dismissedplayeroutype']['dismissaltype'],
                    'one' => 0,
                    'two' => 0,
                    'three' => 0,
                    'four' => 0,
                    'five' => 0,
                    'six' => 0,
                    'other_runs' => 0,
                    'bye_runs' => 0
                ]);

                // Log warning if no player ID
                if (empty($StrikerOrNonstriker['id'])) {
                    Log::warning('Outype Dismissal: No player ID found', [
                        'player_details' => $StrikerOrNonstriker
                    ]);
                }
            }
            // Run Out scenarios
            else if ( $validatedData['batter_is_out'] == 1 && $validatedData['dismissal_type'] === 'Run Out' && !isset($validatedData['Dismissedplayeroutype']['NoballOrWideWicket']) ) {
                $runOutPlayer = $validatedData['runOutPlayer'];
                $runScored = $validatedData['RunOUtbatsmen_runscored'];
                $ballFacedPlayerId = $validatedData['ball_faced_player_id'];

                Log::info('runOutPlayer', [$runOutPlayer]);

                $scoreParts = explode('(', rtrim($runOutPlayer['score'], ')'));
                $runs = isset($scoreParts[0]) ? intval($scoreParts[0]) : 0;
                $ballsFaced = isset($scoreParts[1]) ? intval($scoreParts[1]) : 0;

                $strikeRate = $ballsFaced > 0
                    ? round(($runs / $ballsFaced) * 100, 2)
                    : 0;

                if ($ballFacedPlayerId == $runOutPlayer['id']) {
                    // Same player: update score and ball count
                    $BatterData = array_merge($BatterData, [
                        'player_id' => $runOutPlayer['id'],
                        'score' => $runs + ($runScored ?? 0),
                        'balls_faced' => $ballsFaced + 1,
                        'strike_rate' => $ballsFaced + 1 > 0
                            ? round((($runs + ($runScored ?? 0)) / ($ballsFaced + 1)) * 100, 2)
                            : 0,
                        'is_out' => 1,
                        'dismissal_type' => 'RunOut' ?? null,
                        'one' => 0,
                        'two' => 0,
                        'three' => 0,
                        'four' => 0,
                        'five' => 0,
                        'six' => 0,
                        'other_runs' => 0,
                        'bye_runs' => 0,
                    ]);
                } else {
                    // Different player: do not update score or ball count
                    $BatterData = array_merge($BatterData, [
                        'player_id' => $runOutPlayer['id'],
                        'score' => $runs,
                        'balls_faced' => $ballsFaced,
                        'strike_rate' => $ballsFaced > 0
                            ? round(($runs / $ballsFaced) * 100, 2)
                            : 0,
                        'is_out' => 1,
                        'dismissal_type' => 'RunOut' ?? null,
                        'one' => 0,
                        'two' => 0,
                        'three' => 0,
                        'four' => 0,
                        'five' => 0,
                        'six' => 0,
                        'other_runs' => 0,
                        'bye_runs' => 0,
                    ]);
                }
            }

            // Retired Out scenario
            else if (($validatedData['batter_is_out'] == 1 && $validatedData['dismissal_type'] === 'Retired Out')) {
                Log::info('herecoming4retured');

                $runOutPlayer = $validatedData['runOutPlayer'];

                Log::info('runOutPlayer', [$runOutPlayer]);

                $scoreParts = explode('(', rtrim($runOutPlayer['score'], ')'));
                $runs = isset($scoreParts[0]) ? intval($scoreParts[0]) : 0;
                $ballsFaced = isset($scoreParts[1]) ? intval($scoreParts[1]) : 0;

                $strikeRate = $ballsFaced > 0
                    ? round(($runs / $ballsFaced) * 100, 2)
                    : 0;

                $BatterData = array_merge($BatterData, [
                    'player_id' => $runOutPlayer['id'],
                    'score' => $runs,
                    'balls_faced' => $ballsFaced,
                    'strike_rate' => $strikeRate,
                    'is_out' => 1,
                    'dismissal_type' => $validatedData['dismissal_type'] ?? null,
                    'one' => 0,
                    'two' => 0,
                    'three' => 0,
                    'four' => 0,
                    'five' => 0,
                    'six' => 0,
                    'other_runs' => 0,
                    'bye_runs' => 0
                ]);
            }
            // Mankaded scenario
            else if (
                isset($validatedData['Dismissedplayeroutype']) &&
                is_array($validatedData['Dismissedplayeroutype']) &&
                isset($validatedData['Dismissedplayeroutype']['StrikerandNonstriker']) &&
                isset($validatedData['Dismissedplayeroutype']['StrikerandNonstriker']['nonstriker']) &&
                isset($validatedData['Dismissedplayeroutype']['StrikerandNonstriker']['dismissaltype']) &&
                $validatedData['Dismissedplayeroutype']['StrikerandNonstriker']['dismissaltype'] === 'Run Out (Mankaded)'
            ) {
                $nonstriker = $validatedData['Dismissedplayeroutype']['StrikerandNonstriker']['nonstriker'];

                Log::info('Mankad Dismissal Processing', [
                    'nonstriker' => $nonstriker
                ]);

                // Parse score
                $scoreParts = explode('(', rtrim($nonstriker['score'], ')'));
                $runs = isset($scoreParts[0]) ? intval(trim($scoreParts[0])) : 0;
                $ballsFaced = isset($scoreParts[1]) ? intval(trim($scoreParts[1])) : 0;

                $strikeRate = $ballsFaced > 0
                    ? round(($runs / $ballsFaced) * 100, 2)
                    : 0;

                $BatterData = array_merge($BatterData, [
                    'player_id' => $nonstriker['id'] ?? null,
                    'player_name' => $nonstriker['name'] ?? null,
                    'score' => $runs,
                    'balls_faced' => $ballsFaced,
                    'strike_rate' => $strikeRate,
                    'is_out' => 1,
                    'dismissal_type' => $validatedData['Dismissedplayeroutype']['StrikerandNonstriker']['dismissaltype'],
                    'one' => 0,
                    'two' => 0,
                    'three' => 0,
                    'four' => 0,
                    'five' => 0,
                    'six' => 0,
                    'other_runs' => 0,
                    'bye_runs' => 0
                ]);

                // Log warning if no player ID
                if (empty($nonstriker['id'])) {
                    Log::warning('Mankad Dismissal: No player ID found', [
                        'player_details' => $nonstriker
                    ]);
                }
            }
          //Wide wicket when Stumped and Hit wicket
          else if (isset($validatedData['Dismissedplayeroutype']) &&
          is_array($validatedData['Dismissedplayeroutype']) &&
          isset($validatedData['Dismissedplayeroutype']['StrikerandNonstriker']) &&
          isset($validatedData['Dismissedplayeroutype']['StrikerandNonstriker']['dismissaltype']) &&
          in_array($validatedData['Dismissedplayeroutype']['StrikerandNonstriker']['dismissaltype'], ['Wide & Stumped', 'Wide and Hit Wicket'])
            ) {
                $StrikerOrNonstriker = $validatedData['Dismissedplayeroutype']['StrikerandNonstriker']['Striker'] ?? $validatedData['Dismissedplayeroutype']['StrikerandNonstriker']['Nonstriker']
                    ?? null;

                Log::info('Wide wicket when Stumped and Hit wicket Dismissal Processing', [
                    'StrikerOrNonstriker' => $StrikerOrNonstriker,
                    'fulldata' => $validatedData['Dismissedplayeroutype']['StrikerandNonstriker']['dismissaltype']
                ]);

                // Parse score
                $scoreParts = explode('(', rtrim($StrikerOrNonstriker['score'], ')')) ?? [];
                $runs = isset($scoreParts[0]) ? intval(trim($scoreParts[0])) : 0;
                $ballsFaced = isset($scoreParts[1]) ? intval(trim($scoreParts[1])) : 0;

                $strikeRate = $ballsFaced > 0
                    ? round(($runs / $ballsFaced) * 100, 2)
                    : 0;

                $BatterData = array_merge($BatterData, [
                    'player_id' => $StrikerOrNonstriker['id'] ?? null,
                    'player_name' => $StrikerOrNonstriker['name'] ?? null,
                    'score' => $runs,
                    'balls_faced' => $ballsFaced,
                    'strike_rate' => $strikeRate,
                    'is_out' => 1,
                    'dismissal_type' => $validatedData['Dismissedplayeroutype']['StrikerandNonstriker']['dismissaltype'],
                    'one' => 0,
                    'two' => 0,
                    'three' => 0,
                    'four' => 0,
                    'five' => 0,
                    'six' => 0,
                    'other_runs' => 0,
                    'bye_runs' => 0
                ]);
            // Log warning if no player ID
            if (empty($StrikerOrNonstriker['id'])) {
                Log::warning('Outype Dismissal: No player ID found', [
                    'player_details' => $StrikerOrNonstriker
                ]);
            }
        }

        //Wide Ball / No Ball / Byes Run Out
            else if (isset($validatedData['Dismissedplayeroutype']) && isset($validatedData['Dismissedplayeroutype']['NoballOrWideWicket']) &&
            is_array($validatedData['Dismissedplayeroutype']) &&
            isset($validatedData['Dismissedplayeroutype']['SelectedBatter']) &&
            isset($validatedData['Dismissedplayeroutype']['dismissalType']) &&
            in_array($validatedData['Dismissedplayeroutype']['dismissalType'], ['Run Out'])
            ) {
                $StrikerOrNonstriker = $validatedData['Dismissedplayeroutype']['SelectedBatter']
                    ?? null;

                Log::info('Wide noball or bye run wicket when Runout  wicket Dismissal Processing', [
                    'StrikerOrNonstriker' => $StrikerOrNonstriker,
                    'fulldata' => $validatedData['Dismissedplayeroutype']['SelectedBatter']
                ]);

                $batsmanHitball = !empty($validatedData['Dismissedplayeroutype']['batsmanHitBall']) &&
                ($validatedData['Dismissedplayeroutype']['batsmanHitBall'] === true ||
                 $validatedData['Dismissedplayeroutype']['batsmanHitBall'] === 'true') ? 1 : 0;
                 $batsmanrunsScoredonANoball = $batsmanHitball ? $validatedData['Dismissedplayeroutype']['runsScored'] : 0 ;
                 $ballFacedPlayerId = $validatedData['ball_faced_player_id'];

                // Parse score
                $scoreParts = explode('(', rtrim($StrikerOrNonstriker['score'], ')')) ?? [];
                $runs = isset($scoreParts[0]) ? intval(trim($scoreParts[0])) : 0;
                $ballsFaced = isset($scoreParts[1]) ? intval(trim($scoreParts[1])) : 0;

                $strikeRate = $ballsFaced > 0
                    ? round(($runs / $ballsFaced) * 100, 2)
                    : 0;

                $wicket_type = $validatedData['wicket_type'] ;

                if ($ballFacedPlayerId == $StrikerOrNonstriker['id'] && (in_array($wicket_type, ['NB+W', 'B+W', 'LB+W'], true)) && isset($batsmanHitball)) {
                    $BatterData = array_merge($BatterData, [
                        'player_id' => $StrikerOrNonstriker['id'] ?? null,
                        'player_name' => $StrikerOrNonstriker['name'] ?? null,
                        'score' => $runs + ($batsmanrunsScoredonANoball ?? 0),
                        'balls_faced' => $ballsFaced + ($batsmanHitball ? 1 : 0),
                        'strike_rate' => $ballsFaced + ($batsmanHitball ? 1 : 0) > 0
                        ? round((($runs + ($batsmanrunsScoredonANoball ?? 0)) / ($ballsFaced + ($batsmanHitball ? 1 : 0))) * 100, 2)
                        : 0,
                        'is_out' => 1,
                        'dismissal_type' => ($wicket_type ?? 'NoballOrWideOrBye') . '(Runout)',
                        'one' => 0,
                        'two' => 0,
                        'three' => 0,
                        'four' => 0,
                        'five' => 0,
                        'six' => 0,
                        'other_runs' => 0,
                        'bye_runs' => 0
                    ]);

                }else{
                    $BatterData = array_merge($BatterData, [
                        'player_id' => $StrikerOrNonstriker['id'] ?? null,
                        'player_name' => $StrikerOrNonstriker['name'] ?? null,
                        'score' => $runs,
                        'balls_faced' => $ballsFaced,
                        'strike_rate' => $strikeRate,
                        'is_out' => 1,
                        'dismissal_type' => ($wicket_type ?? 'NoballOrWideOrBye') . '(Runout)',
                        'one' => 0,
                        'two' => 0,
                        'three' => 0,
                        'four' => 0,
                        'five' => 0,
                        'six' => 0,
                        'other_runs' => 0,
                        'bye_runs' => 0
                    ]);
                }


            // Log warning if no player ID
            if (empty($StrikerOrNonstriker['id'])) {
                Log::warning('Outype Dismissal: No player ID found', [
                    'player_details' => $StrikerOrNonstriker
                ]);
            }
        }
            // Normal runs when out
            else {
                Log::info('herecoming5');
                // If no previous data exists (first ball and out)
                $BatterData = array_merge($BatterData, [
                    'score' => (int)($validatedData['score'] ?? 0),
                    'one' => $validatedData['is_one'] ?? 0,
                    'two' => $validatedData['is_two'] ?? 0,
                    'three' => $validatedData['is_three'] ?? 0,
                    'four' => $validatedData['is_four'] ?? 0,
                    'five' => $validatedData['is_five'] ?? 0,
                    'six' => $validatedData['is_six'] ?? 0,
                    'other_runs' => $validatedData['other_runs'] ?? 0,
                    'bye_runs' => $validatedData['bye_runs'] ?? 0,
                    'strike_rate' => $validatedData['score'] > 0
                        ? round(($validatedData['score'] / 1) * 100, 2)
                        : 0,
                    'balls_faced' => 1,
                    'is_out' => 1,
                    'dismissal_type' => $validatedData['dismissal_type'] ?? null,
                ]);
            }
        }
        // No ball or wide scenarios
        else if (isset($isNoBallOrWide) && $isNoBallOrWide && !$validatedData['batsmanHitBall']) {
            Log::info('herecoming6');
            // No ball or wide or bye where batsman didn't hit the ball
            if ($previousBallData) {

                $StrikerOrNonstrikerScore = [] ;
                if ($validatedData['ball_faced_player_id'] == $validatedData['striker']['id']){
                    $StrikerOrNonstrikerScore = $validatedData['striker'];
                } else{
                    $StrikerOrNonstrikerScore = $validatedData['nonstriker'];
                }

                $scoreParts = explode('(', rtrim($StrikerOrNonstrikerScore['score'], ')')) ?? [];
                $runs = isset($scoreParts[0]) ? intval(trim($scoreParts[0])) : 0;
                $ballsFaced = isset($scoreParts[1]) ? intval(trim($scoreParts[1])) : 0;

                $strikeRate = $ballsFaced > 0
                    ? round(($runs / $ballsFaced) * 100, 2)
                    : 0;

                $BatterData = array_merge($BatterData, [
                    'match_id' => $validatedData['match_id'],
                    'team_id' => $validatedData['batting_team_id'],
                    'player_id' =>  $validatedData['retiredoutplayer'] ? $validatedData['retiredoutplayer'] : $playerId,
                    'ball_by_ball_id' => $ball->id,
                    'score' => (int) $runs,
                    'other_runs' => $previousBallData->other_runs,
                    'bye_runs' => $previousBallData->bye_runs,
                    'strike_rate' => $previousBallData->strike_rate,
                    'balls_faced' => $ballsFaced,
                    'is_out' => $previousBallData->is_out,
                    'dismissal_type' => $previousBallData->dismissal_type
                ]);
            } else {
                // Fallback if no previous data exists (e.g., first ball is wide/no-ball)
                $BatterData = array_merge($BatterData, [
                    'match_id' => $validatedData['match_id'],
                    'team_id' => $validatedData['batting_team_id'],
                    'player_id' =>  $validatedData['retiredoutplayer'] ? $validatedData['retiredoutplayer'] : $playerId,
                    'ball_by_ball_id' => $ball->id,
                    'score' => 0,
                    'one' => 0,
                    'two' => 0,
                    'three' => 0,
                    'four' => 0,
                    'five' => 0,
                    'six' => 0,
                    'other_runs' => 0,
                    'bye_runs' => 0,
                    'strike_rate' => 0.00,
                    'balls_faced' => 0,
                    'is_out' => 0,
                    'dismissal_type' => null,
                ]);
            }
        }
        // Normal ball scenarios
        else {
            Log::info('herecoming7');
            Log::info($validatedData['ball_faced_player_id']);
            Log::info($validatedData['striker']['id']);

            $StrikerOrNonstrikerScore = [] ;

            if ($validatedData['ball_faced_player_id'] == $validatedData['striker']['id']){
                $StrikerOrNonstrikerScore = $validatedData['striker'];
            } else{
                $StrikerOrNonstrikerScore = $validatedData['nonstriker'];
            }

            $scoreParts = explode('(', rtrim($StrikerOrNonstrikerScore['score'], ')')) ?? [];
            $runs = isset($scoreParts[0]) ? intval(trim($scoreParts[0])) : 0;
            $ballsFaced = isset($scoreParts[1]) ? intval(trim($scoreParts[1])) : 0;

            $strikeRate = $ballsFaced > 0
                ? round(($runs / $ballsFaced) * 100, 2)
                : 0;

            $BatterData = array_merge($BatterData, [
                'score' => $runs,
                'one' => $validatedData['is_one'] ?? 0,
                'two' => $validatedData['is_two'] ?? 0,
                'three' => $validatedData['is_three'] ?? 0,
                'four' => $validatedData['is_four'] || ($noballBoundary ? 1 : 0 ) ?? 0,
                'five' => $validatedData['is_five'] ?? 0,
                'six' => $validatedData['is_six'] || ( $noballSix ? 1 : 0) ?? 0,
                'other_runs' => $validatedData['other_runs'] ?? 0,
                'bye_runs' => $validatedData['bye_runs'] ?? 0,
                'strike_rate' => $strikeRate ?? 0,
                'balls_faced' => $ballsFaced ?? 0,
                'is_out' => 0,
                'dismissal_type' => $dismissalType ?? null,
            ]);

            // $BatterData = array_merge($BatterData, [
            //     'score' => (int) ($validatedData['score'] ?? $validatedData['total_runs'] ?? 0),
            //     'one' => $validatedData['is_one'] ?? 0,
            //     'two' => $validatedData['is_two'] ?? 0,
            //     'three' => $validatedData['is_three'] ?? 0,
            //     'four' => $validatedData['is_four'] ?? 0,
            //     'five' => $validatedData['is_five'] ?? 0,
            //     'six' => $validatedData['is_six'] ?? 0,
            //     'other_runs' => $validatedData['other_runs'] ?? 0,
            //     'bye_runs' => $validatedData['bye_runs'] ?? 0,
            //     'strike_rate' => $validatedData['strike_rate'] ?? 0,
            //     'balls_faced' => $validatedData['batter_balls_faced'] ?? 0,
            //     'is_out' => $validatedData['batter_is_out'] ?? 0,
            //     'dismissal_type' => $dismissalType ?? null,
            // ]);
        }

            // Prepare Bowler data
            $BowlerData = [
                'match_id' => $validatedData['match_id'],
                'team_id' => $validatedData['bowling_team_id'],
                'ball_by_ball_id' => $ball->id, // Now using $ball->id
                'player_id' => $validatedData['bowler_id'],
                'overs_bowled' => $validatedData['bowler_overs_bowled'],
                'maiden_overs' => $validatedData['bowler_maiden_overs'],
                'runs_conceded' => $validatedData['bowling_runs_conceded'],
                'wickets_taken' => $validatedData['bowling_wickets_taken'],
                'economy_rate' => ($validatedData['bowler_overs_bowled'] > 0)
                    ? ($validatedData['bowling_runs_conceded'] / $validatedData['bowler_overs_bowled'])
                    : 0,
                'no_balls' => $validatedData['no_balls'],
                'wide_balls' => $validatedData['bowler_wide_balls'],
                'extras_bowled' => $validatedData['extras_bowled'],
                'valid_ball_count' => $validatedData['valid_ball_count'],
                'balls_bowled' => $validatedData['ball_number'],
                'extra_runs' => $ExtraRun,
                'extras_type' =>(int) $ExtraType ,   //modify to string,
               // 'innings' =>  $request->current_innings ?? 0 ,
            ];


            Log::info('currentinnings' , [$request->current_innings]) ;
            // Prepare Fielding data
            $FieldingData = [
                'match_id' => $validatedData['match_id'],
                'team_id' => $validatedData['bowling_team_id'],
                'ball_by_ball_id' => $ball->id, // Now using $ball->id
                'player_id' => $validatedData['fielding_caught_behind']
                    ?? $validatedData['fielding_caught_and_bowled']
                    ?? $validatedData['fielding_mankaded']
                    ?? $validatedData['fielding_stumps']
                    ?? $validatedData['bowled']
                    ?? $validatedData['fielder_id']
                    ?? null,
                'catches' => $validatedData['fielding_catches'] ?? 0,
                'run_outs' => $validatedData['fielding_run_outs'] ?? 0,
                'stumpings' => $validatedData['fielding_stumps'] ?? 0,
                'bowled' => $validatedData['bowled'] ?? null,
                'fielding_caught_behind' => $validatedData['fielding_caught_behind'] ?? null,
                'fielding_caught_and_bowled' => $validatedData['fielding_caught_and_bowled'] ?? null,
                'throwing_end_id' => $validatedData['throwing_end_id'] ?? null,
                'directHit' => $validatedData['directHit'] ?? 0,
                'retired_hurt' => $validatedData['retired_hurt'] ?? null,
                'fielding_mankaded' => $validatedData['fielding_mankaded'] ?? null,
                'hit_wicket' => $validatedData['hit_wicket'] ?? null,
                'retired_out' => $validatedData['retired_out'] ?? null,
                'dismissal_type' => $validatedData['dismissal_type'] ?? null,
            ];

            // Save the data
            $batter = PlayerBattingStats::create($BatterData);
            $bowler = PlayerBowlingStats::create($BowlerData);
            $fielding = PlayerFieldingStats::create($FieldingData);


            //FOR SCOREBOARD....
            $ball_by_ball_id = $ball->id;
            $match_id = $ball->match_id;
            $team_id = $ball->batting_team_id;
            $bowling_team_id = $ball->bowling_team_id;
            $inning = $ball->innings_completed;

        $striker_id = $batter->player_id;
        $non_striker_id = $ball->non_striker_id;

        $matches_ctrl = new MatchesController();
        $commentary_data = [
            'ball_by_ball_id' => $ball->id,
            'match_id' => $match_id,
            'inning' => $inning,
            'over' => $ball->over_number,
            'ball' => $ball->total_overs,
            'display_run' => $ball->display_run ?? 0,
            'total_score' => $ball->total_score,
            'striker_id' => $striker_id,
            'non_striker_id' => $non_striker_id, //it's setting wrongly and don't mind it. It's for no use..
            'bowler_id' => $ball->bowler_id,
            'fielder_id' => $ball->fielder_id,
            'commentary_text' => $matches_ctrl->commentaryText($ball->display_run, $striker_id, $non_striker_id, $ball->bowler_id, $ball->fielder_id, $batter->dismissal_type),
        ];
        $commentary = Commentary::create($commentary_data);
        $batter_id = $batter->player_id;
        $bowler_id = $bowler->player_id;

            $create_data = [
                'match_id' => $match_id,
                'team_id' => $team_id,
                'inning' => $inning,
            ];
            $bowler_create_data = [
                'match_id' => $match_id,
                'team_id' => $bowling_team_id,
                'inning' => $inning,
            ];
            $four = PlayerBattingStats::where('match_id', $match_id)->where('team_id', $team_id)->where('player_id', $batter_id)->where('four', 1)->pluck('four')->count();
            $six = PlayerBattingStats::where('match_id', $match_id)->where('team_id', $team_id)->where('player_id', $batter_id)->where('six', 1)->pluck('six')->count();
            $data = [
                'runs' => isset($batter->score) ? $batter->score : 0,
                'balls_faced' => isset($batter->balls_faced) ? $batter->balls_faced : 0,
                'fours' => $four,
                'sixes' => $six,
                'strike_rate' => isset($batter->strike_rate) ? $batter->strike_rate : 0.00,
            ];
            $out_data = [
                'is_out' => 1,
                'bowler_id' => $ball->bowler_id,
                'fielder_id' => $ball->fielder_id,
                'dismissal_type' => $batter->dismissal_type,
            ];
            $overs_per_bowler = MatchGame::find($ball->match_id)->overs_per_bowler;
            $bowler_data = [
                'bowler_id' => $bowler_id,
                'is_max_overs_bowled' => $bowler->overs_bowled >= $overs_per_bowler,
                'overs_bowled' => isset($bowler->overs_bowled) ? $bowler->overs_bowled : 0.0,
                'runs_conceded' => isset($bowler->runs_conceded) ? $bowler->runs_conceded : 0,
                'wickets' => isset($bowler->wickets_taken) ? $bowler->wickets_taken : 0,
                'maidens' => isset($bowler->maiden_overs) ? $bowler->maiden_overs : 0,
                'economy' => isset($bowler->economy_rate) ? $bowler->economy_rate : 0.00,
            ];
            //Initialized ScoreBoardController;
            $score_board_ctrl = new ScoreBoardController($match_id, $team_id,$bowling_team_id, $inning);
            $is_batter_exist = $score_board_ctrl->isBatsManExist($batter_id);
            $is_bowler_exist = $score_board_ctrl->isBowlerExist($bowler_id);

            //BATTER UPDATE OR CREATE
            if(isset($is_batter_exist)) {
                if($batter->is_out) {
                    $data = array_merge($data, $out_data);
                }
                $score_board_ctrl->updateScoreBoard($is_batter_exist->id, $data);
            }else {
                $store_data = array_merge($create_data,['batter_id' => $batter_id], $data);
                if($batter->is_out) {
                    $store_data = array_merge($store_data, $out_data);
                }
                $score_board_ctrl->storeScoreBoard($store_data);
            }
          
          	
            $curr_batters = [$ball->striker_id, $ball->non_striker_id];
            $update_data = ['is_out' => 0, 'dismissal_type' => null];
            foreach($curr_batters as $id) {
                $is_batter_exist = $score_board_ctrl->isBatsManExist($id);
                if(isset($is_batter_exist) && $is_batter_exist->dismissal_type === "Retired Hurt") {//dismissal_type = "Retired Hurt"
                    $score_board_ctrl->updateScoreBoard($is_batter_exist->id, $update_data);
                }
            }

            //UPDATE OR CREATE BOWLER
            if(isset($is_bowler_exist)) {
                $score_board_ctrl->updateBowlerScoreBoard($is_bowler_exist->id, $bowler_data);
            }
            // else {
            //     $store_data = array_merge($bowler_create_data, $bowler_data);
            //     $score_board_ctrl->storeBowlerScoreBoard($store_data);
            // }
            if($ball->is_wicket) {
                ///UPDATING DISMISSED BATSEMEN
                $ball->dismissed_batsmen = $batter->player_id;
                $ball->wicket_type = $batter->dismissal_type;
                $ball->save();
                //END OF BALL_BY_BALL

              if($wicket_type !== "RetiredHurt"){ //dismissal_type = "Retired Hurt"
                $score = $ball->total_score;
                $over = $ball->total_overs;
                $dismissedBatsmen = $batter->player_id;
                $score_board_ctrl->storeFallOfWickets($ball_by_ball_id, $dismissedBatsmen, $score, $over);
               }

            }

            // Commit the transaction if all inserts are successful
            DB::commit();

            return response()->json([
                'message' => 'Ball data saved successfully.',
                'synced' => '✔ Synched',
                'status' => 200,
              	'ballByBallId' => $ball->id,
            ], 200);

        // }

        } catch (\Exception $e) {



            DB::rollBack();
            return response()->json([
                'message' => 'Error saving ball data',
                'synced' => '✔ Not Synched',
                'status' => 400,
                'error' => $e->getMessage()
            ], 203);

            Log::error('Error saving ball data: ' . $e->getMessage());

            // return response()->json(['message' => 'Failed to save ball data', 'error' => $e->getMessage()], 500);
        }

    }


        public function getallbowlersstats($matchId){

            $bowlersStats = PlayerBowlingStats::with('player:id,name')
            ->where('match_id', $matchId)
            ->orderBy('player_id')
            ->orderBy('created_at', 'desc')
            ->get();


            $superOverStats = $bowlersStats->filter(function ($stat) {
                return $stat->innings === 2;
            });

            $response = [
                'success' => true,
                'data' => [
                    'stats' => $bowlersStats,
                    'superOverStats' =>  $superOverStats->values(),
                ],
            ];

            return response()->json($response, 200);
        }

        // public function getBowlerStats( $matchId, $bowlerId){

        //     try {
        //         $Match = MatchGame::find($matchId);

        //         $ballByBall = BallByBall::where('match_id' , $Match->id)->first();
        //         $SuperOverInnings = $ballByBall->innings_completed;

        //         $superOver = $SuperOverInnings == 3;
        //         $bowlerStats = null;

        //         Log::info($superOver ? 'Fetching Super Over stats' : 'Fetching regular stats');

        //         $bowlerStatsQuery = PlayerBowlingStats::with('player:id,name')
        //         ->leftJoin('ball_by_ball', 'ball_by_ball.id', '=', 'player_bowling_stats.ball_by_ball_id')
        //         ->where('player_bowling_stats.match_id', $matchId)
        //         ->where('player_bowling_stats.player_id', $bowlerId);

        //         if ($superOver) {
        //             $bowlerStatsQuery->where('ball_by_ball.innings_completed', 3);
        //         }

        //         $bowlerStats = $bowlerStatsQuery->orderBy('player_bowling_stats.created_at', 'desc')->first();

        //         // $bowlerStats = PlayerBowlingStats::with('player:id,name')
        //         //     ->where('match_id', $matchId)
        //         //     ->where('player_id', $bowlerId)
        //         //     ->orderBy('created_at', 'desc')
        //         //     ->first();

        //         $superOverStats = $bowlerStatsQuery->filter(function ($stat) {
        //             return $stat->innings === 3;
        //         });


        //         if (!$bowlerStats) {
        //             return response()->json(['message' => 'Bowler stats not found'], 404);
        //         }

        //         $playerName = $bowlerStats->player->name ?? 'Unknown Player';

        //         $response = [
        //             'success' => true,
        //             'data' => [
        //                 'stats' => $bowlerStats,
        //                 'player_name' => $playerName,
        //                 'natchDetails' =>$Match ,
        //                 'superOver'=> $superOver,
        //                 'superoverstats' => $superOverStats->values(),
        //             ],
        //          ];

        //         return response()->json($response, 200);
        //     } catch (\Exception $e) {
        //         return response()->json(['message' => 'Error fetching bowler stats', 'error' => $e->getMessage()], 500);
        //     }

        // }

        public function getBowlerStats($matchId, $bowlerId)
{
    try {
        // Validate Match
        $match = MatchGame::find($matchId);
        if (!$match) {
            return response()->json(['message' => 'Match not found'], 404);
        }

        // Get innings completed from BallByBall
        $ballByBall = BallByBall::where('match_id', $match->id)->first();
        if (!$ballByBall) {
            return response()->json(['message' => 'Ball-by-ball data not found'], 404);
        }

        $superOverInnings = $ballByBall->innings_completed;
        $isSuperOver = $superOverInnings == 3;

        Log::info($isSuperOver ? 'Fetching Super Over stats' : 'Fetching regular stats');

        // Query Bowler Stats
        $bowlerStatsQuery = PlayerBowlingStats::with('player:id,name')
            ->leftJoin('ball_by_ball', 'ball_by_ball.id', '=', 'player_bowling_stats.ball_by_ball_id')
            ->where('player_bowling_stats.match_id', $matchId)
            ->where('player_bowling_stats.player_id', $bowlerId);

        if ($isSuperOver) {
            $bowlerStatsQuery->where('ball_by_ball.innings_completed', 3);
        }

        $bowlerStats = $bowlerStatsQuery->orderBy('player_bowling_stats.created_at', 'desc')->first();

        if (!$bowlerStats) {
            return response()->json(['message' => 'Bowler stats not found'], 404);
        }

        // Prepare Super Over Stats if applicable
        $superOverStats = [];
        if ($isSuperOver) {
            $superOverStats = PlayerBowlingStats::with('player:id,name')
                ->where('match_id', $matchId)
                ->where('player_id', $bowlerId)
                ->whereHas('ballByBall', function ($query) {
                    $query->where('innings_completed', 3);
                })
                ->get();
        }

        $playerName = $bowlerStats->player->name ?? 'Unknown Player';

        // Response Data
        $response = [
            'success' => true,
            'data' => [
                'stats' => $bowlerStats,
                'player_name' => $playerName,
                'matchDetails' => $match,
                'isSuperOver' => $isSuperOver,
                'superOverStats' => $superOverStats,
            ],
        ];

        return response()->json($response, 200);
    } catch (\Exception $e) {
        Log::error('Error fetching bowler stats: ' . $e->getMessage());
        return response()->json(['message' => 'Error fetching bowler stats', 'error' => $e->getMessage()], 500);
    }
}


    public function deleteLastBall($matchId)
        {

            Log::info('match id: ' .  $matchId);

            DB::beginTransaction();
            try {
                $lastBall = BallByBall::where('match_id', $matchId)
                    ->orderBy('id', 'desc')
                    ->first();

                if (!$lastBall) {
                    $match_players = MatchPlayer::where('match_id', $matchId)->orderBy('id', 'desc')->first();

                    if(isset($match_players)) {
                        $scoreboard_ctrl = new ScoreBoardController($matchId, $match_players->team_id, NULL, $match_players->current_innings);
                        $batsmen = [$match_players->striker_id, $match_players->non_striker_id];
                        foreach($batsmen as $man) {
                            $isExist = $scoreboard_ctrl->isBatsManExist($man);
                            if(isset($isExist)) {
                                $data =  [
                                    'bowler_id' => NULL,
                                    'fielder_id' => NULL,
                                    'is_out' => 0,
                                    'dismissal_type' => NULL,
                                    'runs' => 0,
                                    'balls_faced' => 0,
                                    'fours' => 0,
                                    'sixes' => 0,
                                    'striker_rate' => 0.00,
                                ];
                                $scoreboard_ctrl->updateScoreBoard($isExist->id, $data);
                            }
                        }
                    }
                    DB::commit();
                    return response()->json(['message' => 'No ball data found to delete','status'=> 404], 404);
                }

                $match_status = 'Active';
                $match_game = MatchGame::where('id', $matchId)->first();
                $match_game->match_details = null;
                $match_game->status = $match_status;
                $match_game->save();
                if(isset($match_game)) {
                    $schedule_match =ScheduleMatch::where('id', $match_game->schedule_match_id)->first();
                    $schedule_match->status = $match_status;
                    $schedule_match->save();
                }

                if($lastBall->over_number == -1) {
                    $lastball_inning = $lastBall->innings_completed;
                    BallByBall::where('match_id', $matchId)->where('innings_completed', $lastball_inning)->forceDelete();
                    // MatchPlayer::where('match_id', $matchId)->where('current_innings', $lastball_inning)->forceDelete(); //need to check it's needed cuz this same functionality added in MatchesConteroller -> creatOrUpdateMatchPlayers

                    $is_last_inning_ball_exist = BallByBall::where('match_id', $matchId)->where('innings_completed', ($lastball_inning - 1))->where('over_number', '!=', -1)->orderBy('id', 'desc')->first();
                    if(isset($is_last_inning_ball_exist)) {
                        $is_last_inning_ball_exist->forceDelete();
                    }
                    DB::commit();
                    return response()->json(['message' => 'Inning last ball data deleted successfully' , 'synced' => '✔ Undo done', 'lastBall' => $lastBall, 'inning' => $lastball_inning, 'is_inning_changed' => true,'status'=> 200], 200);
                    exit();
                }
                // Force delete all related stats
               	PlayerBattingStats::where('ball_by_ball_id', $lastBall->id)->forceDelete();
                PlayerBowlingStats::where('ball_by_ball_id', $lastBall->id)->forceDelete();
                PlayerFieldingStats::where('ball_by_ball_id', $lastBall->id)->forceDelete();
                FallOfWicket::where('ball_by_ball_id', $lastBall->id)->forceDelete();
                Commentary::where('ball_by_ball_id', $lastBall->id)->forceDelete();

                // MatchGame::where('id' , $matchId)->forceDelete();
                // MatchPlayer::where('id' , $matchId)->forceDelete();
                // Team1Detail::where('id' , $matchId)->forceDelete();
                // Team2Detail::where('id' , $matchId)->forceDelete();

                // Force delete the last ball entry
               	//$lastBall->forceDelete();
              	$lastBalls = BallByBall::where('match_id', $matchId)->where('ball_number_for_undo', $lastBall->ball_number_for_undo)->delete();

                $last_ball_inning = $lastBall->innings_completed;
                $last_ball_batting_team = $lastBall->batting_team_id;
                $last_ball_bowling_team = $lastBall->bowling_team_id;
                $is_last_ball_inning_data_exist = BallByBall::where('match_id', $matchId)
                ->where('batting_team_id', $last_ball_batting_team)
                ->where('bowling_team_id', $last_ball_bowling_team)
                ->where('innings_completed', $last_ball_inning)
                ->where('over_number', '!=', -1)
                ->orderBy('id', 'desc')
                ->first();
                $scoreboard_ctrl = new ScoreBoardController($matchId, $last_ball_batting_team, $last_ball_bowling_team, $last_ball_inning);
                if(isset($is_last_ball_inning_data_exist)){
                    $dismissed_batsmen_ids = BallByBall::where('ball_by_ball.match_id', $matchId)
                    ->where('ball_by_ball.batting_team_id', $last_ball_batting_team)
                    ->where('ball_by_ball.bowling_team_id', $last_ball_bowling_team)
                    ->where('ball_by_ball.innings_completed', $last_ball_inning)
                    ->where('ball_by_ball.over_number', '!=', -1)
                    ->where('ball_by_ball.is_wicket', 1)
                    ->distinct()
                    ->pluck('ball_by_ball.dismissed_batsmen')
                    ->toArray();

                    $striker_ids = BallByBall::where('ball_by_ball.match_id', $matchId)
                    ->where('ball_by_ball.batting_team_id', $last_ball_batting_team)
                    ->where('ball_by_ball.bowling_team_id', $last_ball_bowling_team)
                    ->where('ball_by_ball.innings_completed', $last_ball_inning)
                    ->where('ball_by_ball.over_number', '!=', -1)
                    ->whereNotIn('ball_by_ball.striker_id', $dismissed_batsmen_ids)
                    ->distinct()
                    ->pluck('ball_by_ball.striker_id')
                    ->toArray();

                    $non_striker_ids = BallByBall::where('ball_by_ball.match_id', $matchId)
                    ->where('ball_by_ball.batting_team_id', $last_ball_batting_team)
                    ->where('ball_by_ball.bowling_team_id', $last_ball_bowling_team)
                    ->where('ball_by_ball.innings_completed', $last_ball_inning)
                    ->where('ball_by_ball.over_number', '!=', -1)
                    ->whereNotIn('ball_by_ball.non_striker_id', array_merge($dismissed_batsmen_ids, $striker_ids))
                    ->distinct()
                    ->pluck('ball_by_ball.non_striker_id')
                    ->toArray();

                    $batter_ids = array_merge($dismissed_batsmen_ids, $striker_ids, $non_striker_ids);
                    foreach($batter_ids as $batter_id) {
                        $is_player_exist_in_bbb = BallByBall::where('match_id', $matchId)->where('batting_team_id', $last_ball_batting_team)
                        ->where('innings_completed', $last_ball_inning)
                        ->where('over_number', '!=', -1)
                        ->where('striker_id', $batter_id)->orWhere('dismissed_batsmen', $batter_id)
                        ->orderBy('id', 'desc')->first();

                        $is_player_data_exist_in_stats = isset($is_player_exist_in_bbb) ? PlayerBattingStats::where('match_id', $matchId)
                        // ->where('team_id', $last_ball_batting_team)
                        ->where('player_id', $batter_id)
                        ->where('ball_by_ball_id', $is_player_exist_in_bbb->id)
                        // ->orderBy('id', 'desc')
                        ->select(
                            'player_batting_stats.ball_by_ball_id',
                            'player_batting_stats.score as runs',
                            'player_batting_stats.balls_faced',
                            'player_batting_stats.strike_rate',
                            'player_batting_stats.is_out',
                            'player_batting_stats.dismissal_type',
                        )
                        ->first() : null;
                        if(!isset($is_player_data_exist_in_bbb) && !isset($is_player_data_exist_in_stats)) {
                            $is_batman_exist = $scoreboard_ctrl->isBatsManExist($batter_id);

                            $data = [
                                'bowler_id' => NULL,
                                'fielder_id' => NULL,
                                'is_out' => 0,
                                'dismissal_type' => NULL,
                                'runs' => 0,
                                'balls_faced' => 0,
                                'fours' => 0,
                                'sixes' => 0,
                                'strike_rate' => 0.00,
                            ];
                            if(isset($is_batman_exist)) {
                                $scoreboard_ctrl->updateScoreBoard($is_batman_exist->id, $data);
                            }
                            continue;
                        }
                        $data = [
                            'is_out' => $is_player_data_exist_in_stats['is_out'],
                            'dismissal_type' => $is_player_data_exist_in_stats['dismissal_type'],
                            'runs' => $is_player_data_exist_in_stats['runs'],
                            'balls_faced' => $is_player_data_exist_in_stats['balls_faced'],
                            'fours' => PlayerBattingStats::where('match_id', $matchId)->where('team_id', $last_ball_batting_team)->where('player_id', $batter_id)->where('four', 1)->pluck('four')->count(),
                            'sixes' =>  PlayerBattingStats::where('match_id', $matchId)->where('team_id', $last_ball_batting_team)->where('player_id', $batter_id)->where('six', 1)->pluck('six')->count(),
                            'strike_rate' => $is_player_data_exist_in_stats['strike_rate']
                        ];
                        if($is_player_data_exist_in_stats->is_out) {
                            $ball_by_ball = $is_player_data_exist_in_stats->ball_by_ball_id;
                            $ball_by_ball = BallByBall::find($ball_by_ball);

                            $is_player_data_exist_in_stats->bowler_id = null;
                            $is_player_data_exist_in_stats->fielder_id = null;
                            if(isset($ball_by_ball)) {
                                $is_player_data_exist_in_stats->bowler_id = $ball_by_ball->bowler_id;
                                $is_player_data_exist_in_stats->fielder_id = $ball_by_ball->fielder_id;
                            }
                            $data['bowler_id'] = $ball_by_ball->bowler_id;
                            $data['fielder_id'] = $ball_by_ball->fielder_id;
                        }
                        $is_player_data_exist_in_stats->four = PlayerBattingStats::where('match_id', $matchId)->where('team_id', $last_ball_batting_team)->where('player_id', $batter_id)->where('four', 1)->pluck('four')->count();
                        $is_player_data_exist_in_stats->sixes = PlayerBattingStats::where('match_id', $matchId)->where('team_id', $last_ball_batting_team)->where('player_id', $batter_id)->where('six', 1)->pluck('six')->count();
                        $is_batman_exist = $scoreboard_ctrl->isBatsManExist($batter_id);

                        if(isset($is_batman_exist)) {
                            $scoreboard_ctrl->updateScoreBoard($is_batman_exist->id, $data);
                        }
                    }
                    $scoreboard_unwanted_players = ScoreBoard::where('match_id', $matchId)
                    ->where('inning', $last_ball_inning)
                    ->where('team_id', $last_ball_batting_team)
                    ->whereNotIn('batter_id', $batter_ids)
                    ->pluck('id')->toArray();

                    foreach($scoreboard_unwanted_players as $players) {
                        ScoreBoard::where('id', $players)->forceDelete();
                    }
                    // return ['batter_ids' => $batter_ids, 'players' => $latest_batsmen, 'un_wanted_players' => $scoreboard_unwanted_players];
                    // exit();

                    //UNDO FOR BOWLER'S
                    $upto_last_ball_bowlers = BallByBall::where('match_id', $matchId)->where('innings_completed', $last_ball_inning)
                    ->where('batting_team_id', $last_ball_batting_team) //cuz for which batting_team the bowler bowled
                    ->distinct()
                    ->pluck('bowler_id');

                    foreach($upto_last_ball_bowlers as $bowler) {
                        if(!isset($bowler)) continue;
                        $isBowlerExist = $scoreboard_ctrl->isBowlerExist($bowler);
                        if(!isset($isBowlerExist)) continue;

                        $bowling_status = PlayerBowlingStats::
                        where('player_bowling_stats.match_id', $matchId)
                        ->where('player_bowling_stats.player_id',$bowler)
                        ->select(
                            'player_bowling_stats.overs_bowled',
                            'player_bowling_stats.runs_conceded',
                            'player_bowling_stats.wickets_taken as wickets',
                            'player_bowling_stats.maiden_overs as maidens',
                            'player_bowling_stats.economy_rate as economy',
                        )->orderBy('player_bowling_stats.id', 'desc')->limit(1)->first();

                        if(!isset($bowling_status)) {
                            $bowler = new stdClass();
                            $bowler->overs_bowled = '0.0';
                            $bowler->runs_conceded = '0';
                            $bowler->wickets = '0';
                            $bowler->maidens = '0';
                            $bowler->economy = '0.00';

                            $bowling_status  = $bowler;
                        }
                        $overs_per_bowler = MatchGame::find($matchId)->overs_per_bowler;
                        $bowling_status->is_max_overs_bowled = $bowling_status->overs_bowled >= $overs_per_bowler;
                        $scoreboard_ctrl->updateBowlerScoreBoard($isBowlerExist->id, $bowling_status->toArray());
                    }

                    $unwanted_bowlers = BowlerScoreBoard::where('match_id', $matchId)->where('team_id', $last_ball_bowling_team)
                    ->where('inning', $last_ball_inning)->whereNotIn('bowler_id', $upto_last_ball_bowlers)->pluck('id')->toArray();

                    foreach($unwanted_bowlers as $id) {
                        BowlerScoreBoard::find($id)->forceDelete();
                    }
                }else {
                    $last_ball_mp = MatchPlayer::where('match_id', $matchId)->where('team_id', $last_ball_batting_team)
                    ->where('current_innings', $last_ball_inning)->first();
                    
                    if(isset($last_ball_mp)) {
                        $data = [
                            'bowler_id' => NULL,
                            'fielder_id' => NULL,
                            'is_out' => 0,
                            'dismissal_type' => NULL,
                            'runs' => 0,
                            'balls_faced' => 0,
                            'fours' => 0,
                            'sixes' => 0,
                            'strike_rate' => 0.00,
                        ];
                        
                        $batsmen = collect([$last_ball_mp->striker_id, $last_ball_mp->non_striker_id]); 
                        $batsmen->each(function (int $batter_id) use ($scoreboard_ctrl, $data){
                            $is_batman_exist = $scoreboard_ctrl->isBatsManExist($batter_id);
                            if(isset($is_batman_exist)) {
                                $scoreboard_ctrl->updateScoreBoard($batter_id, $data);
                            }
                        });
                        $data = [
                            'is_max_overs_bowled' => 0,
                            'overs_bowled' => 0,
                            'runs_conceded' => 0,
                            'wickets' => 0,
                            'maidens' => 0,
                            'economy' => 0.00,
                        ];
                        $is_bowler_exist = $scoreboard_ctrl->isBowlerExist($last_ball_mp->bowler_id);
                        if(isset($is_bowler_exist)) {
                            $scoreboard_ctrl->updateBowlerScoreBoard($is_bowler_exist->id, $data);
                        }
                    }

                }
                DB::commit();

                return response()->json(['message' => 'Last ball data deleted successfully' , 'synced' => '✔ Undo done', 'lastBall' =>$lastBall,'status'=> 200], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error deleting last ball data: ' . $e->getMessage());
                return response()->json(['message' => 'Failed to delete last ball data', 'error' => $e->getMessage(),'status'=>500], 500);
            }
        }

    public function updatePoints(Request $request) {

        Log::info('Received payload:', $request->all());



        $matchId = $request->input('matchId');

        $winningTeamId = $request->input('winningTeamId');

        $losingTeamId = $request->input('losingTeamId');

        $isTied = $request->input('isTied');

        $winningTeamRunrate = (float) $request->input('winningTeamRunrate');

        $losingTeamRunrate = (float) $request->input('losingTeamRunrate');

        $WinningTeamScore = (int) $request->input('WinningTeamScore');

        $LosingTeamScore = (int) $request->input('LosingTeamScore');

        $WinningTeamOversFaced = (float) $request->input('WinningTeamOversFaced');

        $LosingTeamOversFaced = (float) $request->input('LosingTeamOversFaced');

        $teamAId = $request->input('teamAID');

        $teamBId = $request->input('teamBID');



        Log::info('Parsed data:', [

            'matchId' => $matchId,

            'winningTeamId' => $winningTeamId,

            'losingTeamId' => $losingTeamId,

            'isTied' => $isTied,

            'winningTeamRunrate' => $winningTeamRunrate,

            'losingTeamRunrate' => $losingTeamRunrate,

            'WinningTeamScore' => $WinningTeamScore,

            'LosingTeamScore' => $LosingTeamScore,

            'WinningTeamOversFaced' => $WinningTeamOversFaced,

            'LosingTeamOversFaced' => $LosingTeamOversFaced

        ]);



        DB::beginTransaction();



        try {

            // Fetch the match to get tournament_id

            $match = MatchGame::findOrFail($matchId);



            // if (!$isTied) {

                 // Calculate Net Run Rate for both teams

                $winningTeamNRR = $WinningTeamOversFaced || $LosingTeamOversFaced > 0 ? ($WinningTeamScore / $WinningTeamOversFaced) - ($LosingTeamScore / $LosingTeamOversFaced) : 0;

                $losingTeamNRR = $WinningTeamOversFaced || $LosingTeamOversFaced > 0 ? ($LosingTeamScore / $LosingTeamOversFaced) - ($WinningTeamScore / $WinningTeamOversFaced) : 0;

            // }



            // Update points for the winning team (if not tied)

            if (!$isTied && $winningTeamId) {

                $winningTeamPoints = Point::updateOrCreate(

                    [

                         'match_id' =>$matchId,

                        'team_id' => $winningTeamId,

                        'tournament_id' => $match->tournament_id,

                    ],

                    []

                );

                $winningTeamPoints->increment('matches_played');

                $winningTeamPoints->increment('wins');

                $winningTeamPoints->increment('total_points', 2);

                $winningTeamPoints->update(['net_run_rate' => $winningTeamNRR]);

            }



            // Update points for the losing team (if not tied)

            if (!$isTied && $losingTeamId) {

                $losingTeamPoints = Point::updateOrCreate(

                    [

                        'match_id' =>$matchId,

                        'team_id' => $losingTeamId,

                        'tournament_id' => $match->tournament_id,

                    ],

                    []

                );

                $losingTeamPoints->increment('matches_played');

                $losingTeamPoints->increment('losses');



                $losingTeamPoints->update(['net_run_rate' => $losingTeamNRR]);

            }



            if ($isTied && $teamAId && $teamBId) {

                // Update points for Team A (teamAId)

                $tiePointsForTeamA = Point::updateOrCreate(

                    [

                        'match_id' => $matchId,

                        'team_id' => $teamAId,

                        'tournament_id' => $match->tournament_id,

                    ],

                    []

                );

                $tiePointsForTeamA->increment('matches_played');

                $tiePointsForTeamA->increment('matches_tied');

                $tiePointsForTeamA->increment('total_points', 1);



                // Update points for Team B (teamBId)

                $tiePointsForTeamB = Point::updateOrCreate(

                    [

                        'match_id' => $matchId,

                        'team_id' => $teamBId,

                        'tournament_id' => $match->tournament_id,

                    ],

                    []

                );

                $tiePointsForTeamB->increment('matches_played');

                $tiePointsForTeamB->increment('matches_tied');

                $tiePointsForTeamB->increment('total_points', 1);

            }



            DB::commit();

            return response()->json(['message' => 'Points table updated successfully'], 200);

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Error updating points table: ' . $e->getMessage());

            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json(['error' => 'Failed to update points table: ' . $e->getMessage()], 500);

        }

    }
        // public function deleteLastBall($matchId) // {

        //     DB::beginTransaction();
        //     try {
        //         $lastBall = BallByBall::where('match_id', $matchId)
        //             ->orderBy('id', 'desc')
        //             ->first();

        //         if (!$lastBall) {
        //             return response()->json(['message' => 'No ball data found to delete'], 404);
        //         }

        //         // Force delete all related stats
        //         PlayerBattingStats::where('ball_by_ball_id', $lastBall->id)->forceDelete();
        //         PlayerBowlingStats::where('ball_by_ball_id', $lastBall->id)->forceDelete();
        //         PlayerFieldingStats::where('ball_by_ball_id', $lastBall->id)->forceDelete();

        //         // Force delete the last ball entry
        //         $lastBall->forceDelete();

        //         DB::commit();

        //         return response()->json(['message' => 'Last ball data deleted successfully' ,'lastBall' =>$lastBall], 200);
        //     } catch (\Exception $e) {
        //         DB::rollBack();
        //         Log::error('Error deleting last ball data: ' . $e->getMessage());
        //         return response()->json(['message' => 'Failed to delete last ball data', 'error' => $e->getMessage()], 500);
        //     }
        // }

         public function fetchBallByBallData($matchId)  {

            $ballData = BallByBall::where('match_id', $matchId)->exists();

            if (!$ballData) {
                return response()->json([
                    'message' => 'Cannot undo',
                    'canUndo' => false
                ], 200);
            }

            return response()->json([
                'message' => 'Can Undo',
                'canUndo' => true
            ], 200);

        }


        public function getRetiredPlayerData(Request $request)
        {
            $request->validate([
                'player_id' => 'required|integer',
                'match_id' => 'required|integer',
            ]);

            try {
                $playerData = PlayerBattingStats::where('player_id', $request->player_id)
                ->where('match_id', $request->match_id)
                ->where('dismissal_type', 'Retired Hurt')
                ->orderBy('id', 'desc')
                ->first();

                 $player = Player::where('id' , $request->player_id)->first();

                if ($playerData) {
                    return response()->json([
                        'success' => true,
                        'data' => $playerData,
                        'Player' => $player,
                    ], 200);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Player data not found',
                ], 200);

           } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while fetching player data',
                    'error' => $e->getMessage(),
                  ], 500);
              }
          }


      public function fetchTeamAandTeamB($matchId){

          try{
              $match = MatchGame::findOrFail($matchId);

              $TeamA = $match->teamA_id;
              $TeamB = $match->teamB_id;

              $teamADetails = Team::where('id' , $TeamA)->first();
              $teamBdetails = Team::where('id' , $TeamB)->first();

              return response()->json([
                  'teamADetails' => $teamADetails ,
                  'teamBdetails' => $teamBdetails ,
              ] , 200);


          } catch (\Exception $e) {
                      return response()->json([
                          'success' => false,
                          'message' => 'An error occurred while fetching Team Data',
                          'error' => $e->getMessage(),
                      ], 500);
              }
          }

}
