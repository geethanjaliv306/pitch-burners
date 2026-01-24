<?php

namespace App\Http\Controllers\Api;

use DB;
use Illuminate\Support\Facades\Log;
use stdClass;
use Carbon\Carbon;
use App\Models\Team;
use App\Models\Venue;
use App\Models\Player;
use App\Models\Partner;
use App\Models\MatchGame;
use App\Models\BallByBall;
use App\Models\Commentary;
use App\Models\MatchScore;
use App\Models\Tournament;
use App\Models\MatchPlayer;
use App\Models\Team1Detail;
use App\Models\Team2Detail;
use App\Models\SuperOverTwo;
use App\Models\Point;
use Illuminate\Http\Request;
use App\Models\ScheduleMatch;
use App\Models\Gallery;
use App\Models\TournamentTeam;
use App\Models\SuperOverScores;
use App\Models\PlayerBattingStats;
use App\Models\PlayerBowlingStats;
use App\Models\Penalty;
use App\Helpers\NotificationHelper;
use App\Jobs\SendMatchNotification;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\ScoringController;
use App\Http\Controllers\Api\ScoreBoardController;
use App\Http\Controllers\Api\NotificationController;
use Symfony\Component\CssSelector\Parser\Handler\CommentHandler;
use App\Models\ScoreBoard;
use Illuminate\Support\Facades\Artisan;

class MatchesController extends Controller
{
    //

   public function storeMatchDetails(Request $request)
{
    $validated = $request->validate([
        'matchType' => 'required|string',
        'noOfOvers' => 'required|integer',
        'ground' => 'required|string',
        'date' => 'required|string',
        'time' => 'required|string',
        'selectedTeamA' => 'required|array',
        'selectedTeamB' => 'required|array',
        'selectedUmpires' => 'nullable|array',
        'selectedScorers' => 'nullable|array',
        'tossWinnerId' => 'required|integer',
        'battingTeamId' => 'required|integer',
        'bowlingTeamId' => 'required|integer',
        'tournamentId' => 'required|integer',
        'scheduleMatchId' => 'required|integer',
    ]);
    Log::info("selectedTeamA: " ,  $validated['selectedTeamA'] );

    Log::info("selectedTeamB: " ,  $validated['selectedTeamB'] );
    try {
        DB::beginTransaction();

        $date = Carbon::parse($validated['date'])->format('Y-m-d');
        $time =  $validated['time'];
        $match_date_time = $date . ' ' . $time;
      
        $currentDateTime = Carbon::now()->format('Y-m-d H:i:s');


        $Matches= ScheduleMatch::find($validated['scheduleMatchId']);
        $GroupId = $Matches->group_id;
        $round_id = $Matches->round_id;
        $oversperbowler = $Matches->overs_per_bowler;

        $match = MatchGame::create([
            'teamA_id' => $validated['selectedTeamA']['teamId'],
            'teamB_id' => $validated['selectedTeamB']['teamId'],
            'venue' => $validated['ground'],
            'match_date_time' => $currentDateTime,
            'type' => $validated['matchType'],
            'overs' => $validated['noOfOvers'],
            'overs_per_bowler' => $oversperbowler,
            'toss' => $validated['tossWinnerId'],
            'batting' => $validated['battingTeamId'],
            'bowling' => $validated['bowlingTeamId'],
           'first_umpire' => $validated['selectedUmpires']['1st Umpire'] ?? $validated['selectedUmpires'][0] ?? null,
            'second_umpire' => $validated['selectedUmpires']['2nd Umpire'] ?? $validated['selectedUmpires'][1] ?? null,
            'third_umpire' => $validated['selectedUmpires']['3rd Umpire'] ?? $validated['selectedUmpires'][2] ?? null,
            'first_scorer' => $validated['selectedScorers']['Scorer 1'] ?? $validated['selectedScorers'][0] ?? null,
            'second_scorer' => $validated['selectedScorers']['Scorer 2'] ?? $validated['selectedScorers'][1] ?? null,
            'tournament_id' => $validated['tournamentId'],
            'schedule_match_id' => $validated['scheduleMatchId'],
            'status' => 'Active',
            'group_id' => $GroupId ,
            'round_id' =>$round_id,
        ]);

        $scheduleMatch = ScheduleMatch::find($match->schedule_match_id);
        // if ($scheduleMatch) {
        //     $scheduleMatch->update(['status' => 'Active']);
        // }
        $scheduleMatch->status = 'Active';
        $scheduleMatch->save();

        $teamAPlayerIds = Player::where('team_id', $validated['selectedTeamA']['teamId'])
        ->pluck('id')
        ->toArray();

       $teamBPlayerIds = Player::where('team_id', $validated['selectedTeamB']['teamId'])
        ->pluck('id')
        ->toArray();


        $captainIdTeamA = $validated['selectedTeamA']['CaptainID'];
        $wicketKeeperIdTeamA = $validated['selectedTeamA']['WicketKeeperID'];
        $twelfthManIdTeamA = $validated['selectedTeamA']['TwelththMenID'];
        // $validated['selectedTeamA']['players'] = array_merge($validated['selectedTeamA']['players'], $validated['selectedTeamA']['twelthmen']);

        $validated['selectedTeamA']['players'] = collect($validated['selectedTeamA']['players'])
        ->merge(collect($validated['selectedTeamA']['twelthmen'])
            ->filter(fn($player) => in_array($player['id'], $teamAPlayerIds))) // Only add if they belong to Team A
        ->unique('id')
        ->toArray();




        foreach ($validated['selectedTeamA']['players'] as $player) {
            Team1Detail::create([
                'team_id' =>$validated['selectedTeamA']['teamId'],
                'match_id' => $match->id,
                'player_id' => $player['id'],
                'captain' => $player['id'] === $captainIdTeamA,
                'wicketkeeper' => $player['id'] === $wicketKeeperIdTeamA,
                // '12th_man' => $player['id'] === $twelfthManIdTeamA,
                '12th_man' =>  in_array($player['id'], $twelfthManIdTeamA) ? 1 : 0,
            ]);
        }

        $captainIdTeamB = $validated['selectedTeamB']['CaptainID'];
        $wicketKeeperIdTeamB = $validated['selectedTeamB']['WicketKeeperID'];
        $twelfthManIdTeamB = $validated['selectedTeamB']['TwelththMenID'];
        // $validated['selectedTeamB']['players'] = array_merge($validated['selectedTeamB']['players'], $validated['selectedTeamB']['twelthmen']);
       $validated['selectedTeamB']['players'] = collect($validated['selectedTeamB']['players'])
        ->merge(collect($validated['selectedTeamB']['twelthmen'])
            ->filter(fn($player) => in_array($player['id'], $teamBPlayerIds))) // Only add if they belong to Team B
        ->unique('id')
        ->toArray();

        foreach ($validated['selectedTeamB']['players'] as $player) {
            Team2Detail::create([
                'team_id' =>$validated['selectedTeamB']['teamId'],
                'match_id' => $match->id,
                'player_id' => $player['id'],
                'captain' => $player['id'] === $captainIdTeamB,
                'wicketkeeper' => $player['id'] === $wicketKeeperIdTeamB,
                // '12th_man' => $player['id'] === $twelfthManIdTeamB,
                '12th_man' =>  in_array($player['id'], $twelfthManIdTeamB) ? 1 : 0,
            ]);
        }

        DB::commit();

        $teamA = Team::find($validated['selectedTeamA']['teamId']);
        $teamB = Team::find($validated['selectedTeamB']['teamId']);
        $tossWinnerTeam = $validated['tossWinnerId'] == $teamA->id ? $teamA->name : $teamB->name;
        $battingTeam = $validated['battingTeamId'] == $teamA->id ? $teamA->name : $teamB->name;

        $matchData = [
            'matchId' => $match->id,
            'TeamA' => $teamA->name,
            'TeamB' => $teamB->name,
            'tossWinner' => $tossWinnerTeam,
            'battingFirst' => $battingTeam
        ];

        $notificationData = NotificationHelper::prepareMatchNotification('match_start', $matchData);
      	\Log::info('Notification Data:', $notificationData);
        if ($notificationData) {
            \Log::info('Dispatching Notification Job');
        }else{
         \Log::info('Dispatching Notification Job Failed'); 
        }


        //if ($notificationData) {
         //   $notificationController = new NotificationController();
          //  $notificationController->sendPushNotification($notificationData);
        //}
		SendMatchNotification::dispatch($notificationData)->onQueue('match_notifications');
      


      
        return response()->json(['message' => 'Match details saved successfully.' , 'MatchId' => $match->id], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => 'Failed to save match details.', 'message' => $e->getMessage()], 500);
    }
  }

  public function MatchFixtures(){

        $matchFixtures = DB::table('schedule_matches')
        ->join('tournaments', 'tournaments.id', '=', 'schedule_matches.tournament_id')
        ->join('teams as teamA', 'teamA.id', '=', 'schedule_matches.team1')
        ->join('teams as teamB', 'teamB.id', '=', 'schedule_matches.team2')
        ->leftJoin('venues' , 'venues.id', '=' , 'schedule_matches.ground')
        ->leftJoin('groups' , 'schedule_matches.group_id', '=', 'groups.id')
        ->leftJoin('tournament_rounds', 'tournament_rounds.id', '=', 'schedule_matches.round_id')
        ->select(
            'schedule_matches.*',
            'venues.name as ground',
            'venues.location as ground_location',
            'teamA.name as teamA_name',
            'teamA.logo as teamA_image',
            'teamB.name as teamB_name',
            'teamB.logo as teamB_image',
            'tournaments.name as tournament_name',
          	'groups.group_name',
            'tournament_rounds.type as round_name',
        )
        ->orderByRaw("
            CASE schedule_matches.status
                WHEN 'scheduled' THEN 1
                ELSE 2
            END
        ")
        // ->orderByRaw("
        //     CASE schedule_matches.status
        //         WHEN 'active' THEN 1
        //         WHEN 'scheduled' THEN 2
        //         WHEN 'not yet started' THEN 3
        //         WHEN 'completed' THEN 4
        //         WHEN 'canceled' THEN 5
        //         WHEN 'postponed' THEN 6
        //         ELSE 7
        //     END
        // ")
        ->get();
        // $LIVE = 'Active';
        // $UP_COMING = 'Scheduled';
        // $COMPLETED = 'Completed';

        // $live_matches = ScheduleMatch::where('schedule_matches.status', $LIVE)
        // ->join('tournaments', 'tournaments.id', '=', 'schedule_matches.tournament_id')
        // ->join('teams as teamA', 'teamA.id', '=', 'schedule_matches.team1')
        // ->join('teams as teamB', 'teamB.id', '=', 'schedule_matches.team2')
        // ->leftJoin('venues' , 'venues.id', '=' , 'schedule_matches.ground')
        // ->select(
        //     'schedule_matches.*',
        //     'venues.name as ground',
        //     'teamA.name as teamA_name',
        //     'teamA.logo as teamA_image',
        //     'teamB.name as teamB_name',
        //     'teamB.logo as teamB_image',
        //     'tournaments.name as tournament_name'
        // )
        // ->get();
        $live = $up_coming = $completed = $cancelled = [];
        foreach($matchFixtures as $fixture) {
            $match = MatchGame::where('schedule_match_id', $fixture->id)->orderBy('id', 'desc')->first();
            $fixture->is_teamA_batting = false;
            $fixture->is_teamB_batting = false;
            $fixture->teamA_score = '0/0';
            $fixture->teamB_score = '0/0';
            $fixture->batting_score = '0/0';
            $fixture->bowling_score = '0/0';
            $fixture->matchId = null;
            $fixture->result = null;
            $fixture->status = null;
            $fixture->totalOvers = 0;
            $fixture->teamAovers = "0.0/0";
            $fixture->teamBovers = "0.0/0";
            $fixture->is_team1_data_there = false;
            $fixture->is_team2_data_there = false;
            $fixture->batting_team_id  = null;
            $fixture->bowling_team_id = null;
            $fixture->is_teamA_won = false;
            $fixture->is_teamB_won = false;
          	$fixture->teamA_captain = Player::where('team_id', $fixture->team1)->where('is_captain', 1)->select('id', 'name', 'image')->first();
			$fixture->teamB_captain = Player::where('team_id', $fixture->team2)->where('is_captain', 1)->select('id', 'name', 'image')->first();
          	

            $now = Carbon::now();
            $date_from_schedule_match = Carbon::createFromFormat('Y-m-d H:i:s', $fixture->match_date_time, null);
            $status = $now->greaterThan($date_from_schedule_match) ? 'cancelled' : 'scheduled';
            $fixture->isGreater = $now->greaterThan($date_from_schedule_match);
            if(isset($match)) {
                $scores = $this->getTeamScore($match->id);
                $status = strtolower($match->status);
                $fixture->is_teamA_batting = isset($scores['batting_team_id']) ? $scores['batting_team_id'] == $fixture->team1 : false;
                $fixture->is_teamB_batting = isset($scores['batting_team_id']) ? $scores['batting_team_id'] == $fixture->team2 : false;
                $fixture->teamA_score = $fixture->is_teamA_batting ? $scores['batting_score'] : $scores['bowling_score'];
                $fixture->teamB_score = $fixture->is_teamB_batting ? $scores['batting_score'] : $scores['bowling_score'];
                $fixture->batting_score = $scores['batting_score'];
                $fixture->bowling_score = $scores['bowling_score'];
                $fixture->matchId = $match->id;
              	$fixture->totalOvers = $match->overs;
                $fixture->teamAovers = $fixture->is_teamA_batting ? $scores['batting_team_overs'] : $scores['bowling_team_overs'];
                $fixture->teamBovers = $fixture->is_teamB_batting ? $scores['batting_team_overs'] : $scores['bowling_team_overs'];
                $fixture->currentInning = $scores['current_inning'];

                $last_ball_data = BallByBall::where('ball_by_ball.match_id', $match->id)->orderBy('ball_by_ball.id', 'desc')->first();
                $team1 = Team::where('id', $match->batting)->first();
                $team2 = Team::where('id', $match->bowling)->first();

				$team1_data = BallByBall::where('match_id', $match->id)->where('batting_team_id', $team1->id)->where('over_number', '!=', -1)->orderBy('id', 'desc')->first();
                $team2_data = BallByBall::where('match_id', $match->id)->where('batting_team_id', $team2->id)->where('over_number', '!=', -1)->orderBy('id', 'desc')->first();

                $team1_score = 0;
                $team2_score = 0;

                 if(isset($team1_data)) {
                    $team1_score =  $team1_data->total_score;
                    $fixture->teamAovers = (($fixture->is_teamA_batting)) ? $scores['batting_team_overs'] : $scores['bowling_team_overs'];
                }

                if(isset($team2_data)) {
                    $team2_score =  $team2_data->total_score;
                    $fixture->teamBovers = (($fixture->is_teamB_batting)) ? $scores['batting_team_overs'] : $scores['bowling_team_overs'];
                }

                if(strtolower($status) == 'completed') {
                    $fixture->result = $match->match_details;
                    $match_score = MatchScore::where('match_id', $match->id)->where('is_winning', '!=', 0)->orderBy('id', 'desc')->first();
                    if(isset($match_score) && $match_score->is_winning == $fixture->team1){
                        $fixture->is_teamA_won = true;
                    	$fixture->is_teamB_won = false;
                    }

                    if(isset($match_score) && $match_score->is_winning == $fixture->team2){
                        $fixture->is_teamA_won = false;
                        $fixture->is_teamB_won = true;
                    }
                }elseif(strtolower($status) == 'cancelled' || strtolower($status) == 'canceled') {
                    $fixture->result = $match->match_details;
                    $match_score = MatchScore::where('match_id', $match->id)->where('is_winning', '!=', 0)->orderBy('id', 'desc')->first();

                    if(isset($match_score) && $match_score->is_winning == $fixture->team1){
                        $fixture->is_teamA_won = true;
                    	$fixture->is_teamB_won = false;
                    }

                    if(isset($match_score) && $match_score->is_winning == $fixture->team2){
                        $fixture->is_teamA_won = false;
                        $fixture->is_teamB_won = true;
                    }
                }

                $fixture->team1_score = $team1_score;
                $fixture->team2_score = $team2_score;
                $fixture->batting_team_id  = $scores['batting_team_id'];
                $fixture->bowling_team_id = $scores['bowling_team_id'];
                $fixture->is_team1_data_there = isset($team1_data);
                $fixture->is_team2_data_there = isset($team2_data);
            }
            $fixture->status = $status;
            switch (strtolower($status)) {
                case 'scheduled':
                        $up_coming[] = $fixture;
                        break;
                case 'active':
                        $live[] = $fixture;
                        break;
                case 'completed':
                        $completed[] = $fixture;
                        break;
                case 'canceled':
                case 'cancelled':
                        $cancelled[] = $fixture;
                        break;
            }
        }
        //LIVE SORT
        usort($live, function($a, $b) {
            $a_date = strtotime($a->updated_at ?? '') ?: PHP_INT_MIN;
            $b_date = strtotime($b->updated_at ?? '') ?: PHP_INT_MIN;

            return $b_date <=> $a_date;
        });
        //COMPLETED SORT
        usort($completed, function($a, $b) {
            $a_date = strtotime($a->updated_at ?? '') ?: PHP_INT_MIN;
            $b_date = strtotime($b->updated_at ?? '') ?: PHP_INT_MIN;

            return $b_date <=> $a_date;
        });

        //UPCOMING SORT
        uasort($up_coming, function($a, $b) {
            $a_date = strtotime($a->match_date_time ?? '') ?: PHP_INT_MIN;
            $b_date = strtotime($b->match_date_time ?? '') ?: PHP_INT_MIN;

            return $a_date <=> $b_date;
        });
        //CANCELLED SORT
        uasort($cancelled, function($a, $b) {
            $a_date = strtotime($a->match_date_time ?? '') ?: PHP_INT_MIN;
            $b_date = strtotime($b->match_date_time ?? '') ?: PHP_INT_MIN;

            return $a_date <=> $b_date;
        });

        $tournamentIds = $matchFixtures->pluck('tournament_id')->toArray();
        $getSeasons = Tournament::whereIn('id', $tournamentIds)->get();
        $TournamentTeams = TournamentTeam::whereIn('tournament_id', $tournamentIds)->get();
        $teamIds = $TournamentTeams->pluck('team_id')->toArray();
        $Teams = Team::whereIn('id', $teamIds)->get();
        $Venues =Venue::all();
    
    	//$matchFixtures = $live = $up_coming = $completed = $cancelled = [];
        return response()->json(['matchFixtures' => $matchFixtures , 'getSeasons' => $getSeasons ,'Venues' => $Venues , 'Teams' => $Teams, 'live' => $live, 'upcoming' => $up_coming, 'completed' => $completed, 'cancelled' => $cancelled]);
  }

 public function getInningsDetails($id, $isSecondInnings , $isSuperOver , $isSecondInningsSuperOver, $isSecondSuperOver, $isSecondSuperOverSecondInnings){
    try {
        $match = MatchGame::find($id);
        $scheduleMatchId = $match->schedule_match_id;
        $scheduleMatch = ScheduleMatch::find($scheduleMatchId);
        $ballType = $scheduleMatch ? $scheduleMatch->type : null;
        $match->ball_type = $ballType;
        $oversPerBowler = $match->overs_per_bowler;
        if (!$match) {
            return response()->json(['error' => 'Match not found.'], 404);
        }
        $TRUTHY = 'true';
        $FALSY = 'false';
        if ($isSecondInnings == $TRUTHY && $isSuperOver == $FALSY){
            //Team 2nd innings bat
            $battingFirstTeamId = $match->bowling;
            $bowlingFirstTeamId = $match->batting;
            $innings_ball_data = [
                'match_id' => $match->id,
                'batting_team_id' => $bowlingFirstTeamId,
                'bowling_team_id' => $battingFirstTeamId,
                'over_number' => -1,
                'ball_number' => -1,
                'valid_ball_count' => 0,
                'is_wicket' => 0,
                'innings_completed' => 1,
            ];
            BallByBall::create($innings_ball_data);
            $text = 'through second innings';

            $firstInningsScore = MatchScore::where('match_id' , $match->id)->where('is_first_inning' , 1)->first();
            $firstInningsTeam = Team::findOrFail($firstInningsScore->team_id);

            if($battingFirstTeamId ==  $firstInningsScore->team_id){
                $secondInningsTeamId = $bowlingFirstTeamId;
            }else{
                $secondInningsTeamId = $battingFirstTeamId;
            }

            $SecondInningTeam = Team::findOrFail($secondInningsTeamId);

            Log::info('FirstinningTeamname' , [$firstInningsTeam->name]);
            Log::info('SecondInningTeamname' , [$SecondInningTeam->name]);

            $matchData = [
                'FirstInningTeam' => $firstInningsTeam->name ,
                'FirstInningteamImage' => $firstInningsTeam->logo,
                'FirstInningTeamID' => $firstInningsScore->team_id ,
                'SecondInningTeamID' => $secondInningsTeamId  ,
                'SecondInningTeam' => $SecondInningTeam->name ,
                'secondInningTeamimage' => $SecondInningTeam->logo,
                'FirstInningScore' => $firstInningsScore->total_runs ?? 0 ,
                'FirstInningWickets' => $firstInningsScore->total_wickets ?? 0,
                'FirStInningsovers' => $firstInningsScore->overs_faced ?? '0.0' ,
                'Match' => MatchGame::find($match->id),
            ];

            Log::info("matchData" , [$matchData]);

            $notificationData = NotificationHelper::prepareMatchNotification($text, $matchData);
                	\Log::info('Notification Data:', $notificationData);
        if ($notificationData) {
            \Log::info('Dispatching Notification Job');
        }else{
         \Log::info('Dispatching Notification Job Failed'); 
        }

            SendMatchNotification::dispatch($notificationData)->onQueue('match_notifications');

            // if ($notificationData) {
            //     $notificationController = new NotificationController();
            //     $notificationController->sendPushNotification($notificationData);
            // }

        } else if($isSuperOver == $TRUTHY && $isSecondInnings == $TRUTHY && $isSecondInningsSuperOver == $FALSY){
            //when match tied the 2nd inning team is continue batting
            $battingFirstTeamId = $match->bowling;
            $bowlingFirstTeamId = $match->batting;
            $innings_ball_data = [
                'match_id' => $match->id,
                'batting_team_id' => $battingFirstTeamId,
                'bowling_team_id' => $bowlingFirstTeamId,
                'over_number' => -1,
                'ball_number' => -1,
                'valid_ball_count' => 0,
                'is_wicket' => 0,
                'innings_completed' => 2,
            ];
            BallByBall::create($innings_ball_data);
            $text = 'through second innings and super over';

            $teamA = Team::find($battingFirstTeamId);
            $teamB = Team::find($bowlingFirstTeamId);


            Log::info("battingFirstTeamId" , [$battingFirstTeamId]);
            Log::info("bowlingFirstTeamId" , [$bowlingFirstTeamId]);

            $matchData = [
                'TeamA' => $teamA,
                'TeamB' => $teamB,
            ];

            $notificationData = NotificationHelper::prepareMatchNotification($text, $matchData);
                          	\Log::info('Notification Data:', $notificationData);
        if ($notificationData) {
            \Log::info('Dispatching Notification Job');
        }else{
         \Log::info('Dispatching Notification Job Failed'); 
        }

            // if ($notificationData) {
            //     $notificationController = new NotificationController();
            //     $notificationController->sendPushNotification($notificationData);
            // }
            SendMatchNotification::dispatch($notificationData)->onQueue('match_notifications');

        } else if($isSecondInningsSuperOver == $TRUTHY && $isSuperOver == $TRUTHY && $isSecondInnings == $TRUTHY && $isSecondSuperOver == $FALSY && $isSecondSuperOverSecondInnings== $FALSY){
            //when match tied the 1st inning team is batting for 2nd inning super over
            $battingFirstTeamId = $match->batting;
            $bowlingFirstTeamId = $match->bowling;
            $innings_ball_data = [
                'match_id' => $match->id,
                'batting_team_id' => $bowlingFirstTeamId,
                'bowling_team_id' => $battingFirstTeamId,
                'over_number' => -1,
                'ball_number' => -1,
                'valid_ball_count' => 0,
                'is_wicket' => 0,
                'innings_completed' => 3,
            ];
            BallByBall::create($innings_ball_data);
            $text = 'through all three';

            $firstInningsScore = SuperOverScores::where('match_id' , $match->id)->where('is_first_inning_super_over' , 1)->first();
            $firstInningsTeam = Team::findOrFail($firstInningsScore->team_id);

            if($battingFirstTeamId ==  $firstInningsScore->team_id){
                $secondInningsTeamId = $bowlingFirstTeamId;
            }else{
                $secondInningsTeamId = $battingFirstTeamId;
            }

            $SecondInningTeam = Team::findOrFail($secondInningsTeamId);

            Log::info('FirstinningTeamname' , [$firstInningsTeam->name]);
            Log::info('SecondInningTeamname' , [$SecondInningTeam->name]);

            $matchData = [
                'FirstInningTeam' => $firstInningsTeam->name ,
                'FirstInningteamImage' => $firstInningsTeam->logo,
                'FirstInningTeamID' => $firstInningsScore->team_id ,
                'SecondInningTeamID' => $secondInningsTeamId  ,
                'SecondInningTeam' => $SecondInningTeam->name ,
                'secondInningTeamimage' => $SecondInningTeam->logo,
                'FirstInningScore' => $firstInningsScore->total_runs ?? 0 ,
                'FirstInningWickets' => $firstInningsScore->total_wickets ?? 0,
                'FirStInningsovers' => $firstInningsScore->overs_faced ?? '0.0' ,
                'Match' => MatchGame::find($match->id),
            ];

            Log::info("matchData" , [$matchData]);

            $notificationData = NotificationHelper::prepareMatchNotification($text, $matchData);
                          	\Log::info('Notification Data:', $notificationData);
        if ($notificationData) {
            \Log::info('Dispatching Notification Job');
        }else{
         \Log::info('Dispatching Notification Job Failed'); 
        }

            SendMatchNotification::dispatch($notificationData)->onQueue('match_notifications');
            // if ($notificationData) {
            //     $notificationController = new NotificationController();
            //     $notificationController->sendPushNotification($notificationData);
            // }

        }else if($isSecondInningsSuperOver == $TRUTHY && $isSuperOver == $TRUTHY && $isSecondInnings == $TRUTHY && $isSecondSuperOver == $TRUTHY && $isSecondSuperOverSecondInnings== $FALSY){
            //when match tied the 1st inning team is batting for 2nd inning super over
            $battingFirstTeamId = $match->batting;
            $bowlingFirstTeamId = $match->bowling;
            $innings_ball_data = [
                'match_id' => $match->id,
                'batting_team_id' => $battingFirstTeamId,
                'bowling_team_id' => $bowlingFirstTeamId,
                'over_number' => -1,
                'ball_number' => -1,
                'valid_ball_count' => 0,
                'is_wicket' => 0,
                'innings_completed' => 4,
            ];
            BallByBall::create($innings_ball_data);
            $text = 'through all 4';

            $teamA = Team::find($battingFirstTeamId);
            $teamB = Team::find($bowlingFirstTeamId);

            $matchData = [
                'TeamA' => $teamA,
                'TeamB' => $teamB,
            ];

            $notificationData = NotificationHelper::prepareMatchNotification($text, $matchData);
                          	\Log::info('Notification Data:', $notificationData);
        if ($notificationData) {
            \Log::info('Dispatching Notification Job');
        }else{
         \Log::info('Dispatching Notification Job Failed'); 
        }

            // if ($notificationData) {
            //     $notificationController = new NotificationController();
            //     $notificationController->sendPushNotification($notificationData);
            // }
            SendMatchNotification::dispatch($notificationData)->onQueue('match_notifications');

        }else if($isSecondInningsSuperOver == $TRUTHY && $isSuperOver == $TRUTHY && $isSecondInnings == $TRUTHY && $isSecondSuperOver == $TRUTHY && $isSecondSuperOverSecondInnings== $TRUTHY){
            //when match tied the 1st inning team is batting for 2nd inning super over
            $battingFirstTeamId = $match->bowling;
            $bowlingFirstTeamId = $match->batting;
            $innings_ball_data = [
                'match_id' => $match->id,
                'batting_team_id' => $bowlingFirstTeamId,
                'bowling_team_id' => $battingFirstTeamId,
                'over_number' => -1,
                'ball_number' => -1,
                'valid_ball_count' => 0,
                'is_wicket' => 0,
                'innings_completed' => 5,
            ];
            BallByBall::create($innings_ball_data);
            $text = 'through all 5';


            $firstInningsScore = SuperOverTwo::where('match_id' , $match->id)->where('is_first_inning_super_over' , 1)->first();
            $firstInningsTeam = Team::findOrFail($firstInningsScore->team_id);

            if($battingFirstTeamId ==  $firstInningsScore->team_id){
                $secondInningsTeamId = $bowlingFirstTeamId;
            }else{
                $secondInningsTeamId = $battingFirstTeamId;
            }

            $SecondInningTeam = Team::findOrFail($secondInningsTeamId);

            Log::info('FirstinningTeamname' , [$firstInningsTeam->name]);
            Log::info('SecondInningTeamname' , [$SecondInningTeam->name]);

            $matchData = [
                'FirstInningTeam' => $firstInningsTeam->name ,
                'FirstInningteamImage' => $firstInningsTeam->logo,
                'FirstInningTeamID' => $firstInningsScore->team_id ,
                'SecondInningTeamID' => $secondInningsTeamId  ,
                'SecondInningTeam' => $SecondInningTeam->name ,
                'secondInningTeamimage' => $SecondInningTeam->logo,
                'FirstInningScore' => $firstInningsScore->total_runs ?? 0 ,
                'FirstInningWickets' => $firstInningsScore->total_wickets ?? 0,
                'FirStInningsovers' => $firstInningsScore->overs_faced ?? '0.0' ,
                'Match' => MatchGame::find($match->id),
            ];

            Log::info("matchData" , [$matchData]);

            $notificationData = NotificationHelper::prepareMatchNotification($text, $matchData);
                          	\Log::info('Notification Data:', $notificationData);
        if ($notificationData) {
            \Log::info('Dispatching Notification Job');
        }else{
         \Log::info('Dispatching Notification Job Failed'); 
        }

            // if ($notificationData) {
            //     $notificationController = new NotificationController();
            //     $notificationController->sendPushNotification($notificationData);
            // }
            SendMatchNotification::dispatch($notificationData)->onQueue('match_notifications');

        } else{
            //Team 1st innings bat
            $battingFirstTeamId = $match->batting;
            $bowlingFirstTeamId = $match->bowling;
            $text = 'none';
        }
        $batting_text = '';
        $bowling_text = '';
        //if its batting
        $BowlingTeamTwelthMan = [];
        $BattingTeamTwelfthMan = [];
        if ($battingFirstTeamId == $match->teamA_id) {
            $battingTeamDetails = Team1Detail::where('match_id', $id)
            //->where('12th_man', '!=', 1)
            ->get();
            $batting_text = 'team 1';

            $BowlingTeamTwelthMan = Team2Detail::where('match_id', $id)
                ->where('12th_man', 1)
                ->get();

            $BattingTeamTwelfthMan = Team1Detail::where('match_id', $id)
                ->where('12th_man', 1)
                ->get();

        } else {
            $battingTeamDetails = Team2Detail::where('match_id', $id)
            //->where('12th_man', '!=', 1)
            -> get();
            $batting_text = 'team 2';

            $BowlingTeamTwelthMan = Team1Detail::where('match_id', $id)
                ->where('12th_man', 1)
                ->get();

            $BattingTeamTwelfthMan = Team2Detail::where('match_id', $id)
                ->where('12th_man', 1)
                ->get();
        }


        //if its bowling

        if ($bowlingFirstTeamId == $match->teamA_id) {
            $bowlingTeamDetails = Team1Detail::where('match_id', $id)
            // ->where('12th_man', '!=', 1)
            ->get();

            $bowling_text = 'team 1';

            $BowlingTeamTwelthMan = Team2Detail::where('match_id', $id)
                ->where('12th_man', 1)
                ->get();

            $BattingTeamTwelfthMan = Team1Detail::where('match_id', $id)
            ->where('12th_man', 1)
            ->get();
        } else {
            $bowlingTeamDetails = Team2Detail::where('match_id', $id)
            // ->where('12th_man', '!=', 1)
            ->get();

            $bowling_text = 'team 2';

            $BowlingTeamTwelthMan = Team1Detail::where('match_id', $id)
                ->where('12th_man', 1)
                ->get();


            $BattingTeamTwelfthMan = Team2Detail::where('match_id', $id)
            ->where('12th_man', 1)
            ->get();
        }


        $battingPlayers = [];
        $playerIds = $battingTeamDetails->pluck('player_id');
        $teamIds = $battingTeamDetails->pluck('team_id');
        $players = Player::whereIn('id', $playerIds)->get()->keyBy('id');
        // $playerBattingStats = PlayerBattingStats::where('match_id', $id)
        //     // ->leftJoin('ball_by_ball' , 'ball_by_ball.id' , '=' , 'player_batting_stats.ball_by_ball_id')
        //     ->whereIn('player_id', $playerIds)
        //     ->where('is_out', 1)
        //     // ->whereNotIn('innings_completed' , [2, 3 , 4 , 5])
        //     ->get()
        //     ->keyBy('player_id');

        // $playerBattingStats = PlayerBattingStats::where('player_batting_stats.match_id', $id)
        //     ->leftJoin('ball_by_ball', 'ball_by_ball.id', '=', 'player_batting_stats.ball_by_ball_id')
        //     ->whereIn('player_batting_stats.player_id', $playerIds)
        //     ->where('player_batting_stats.is_out', 1)
        //     ->where(function($query) use ($isSuperOver, $isSecondInningsSuperOver, $isSecondSuperOver , $isSecondSuperOverSecondInnings) {
        //         if ($isSuperOver == 'true' || $isSecondInningsSuperOver == 'true' || $isSecondSuperOver == 'true' || $isSecondSuperOverSecondInnings == 'true') {
        //             $query->where('ball_by_ball.innings_completed', '>=', 2);
        //         } else {
        //             $query->where('ball_by_ball.innings_completed', 1);
        //         }
        //     })
        //     ->get()
        //     ->keyBy('player_id');

        $playerBattingStats = PlayerBattingStats::where('player_batting_stats.match_id', $id)
        ->leftJoin('ball_by_ball', 'ball_by_ball.id', '=', 'player_batting_stats.ball_by_ball_id')
        ->whereIn('player_batting_stats.player_id', $playerIds)
        ->where('player_batting_stats.is_out', 1)
        ->where(function($query) use ($isSuperOver, $isSecondInningsSuperOver, $isSecondSuperOver, $isSecondSuperOverSecondInnings) {
            $isSuperOverMode = $isSuperOver === 'true' ||
                            $isSecondInningsSuperOver === 'true' ||
                            $isSecondSuperOver === 'true' ||
                            $isSecondSuperOverSecondInnings === 'true';

            if ($isSuperOverMode) {
                $query->whereIn('ball_by_ball.innings_completed', [2, 3, 4, 5]);
            } else {
                $query->where('ball_by_ball.innings_completed', 1);
            }
        })
        ->get()
        ->keyBy('player_id');

        $battingTeam = Team::whereIn('id', $teamIds)->first();
        foreach ($battingTeamDetails as $detail) {
            $player = $players->get($detail->player_id);
            $playerBattingStat = $playerBattingStats->get($detail->player_id);
            if (!$playerBattingStat) {
                $battingPlayers[] = $player;
            }
        }
        Log::info("battingPlayers" , [$battingPlayers]) ;

        $bowlingPlayers = [];
        $playerIds = $bowlingTeamDetails->pluck('player_id');
        $teamIds = $bowlingTeamDetails->pluck('team_id');
        $players = Player::whereIn('id', $playerIds)->get();
        $bowlingTeam = Team::whereIn('id', $teamIds)->first();
        foreach ($bowlingTeamDetails as $detail) {
            $player = $players->firstWhere('id', $detail->player_id);
            if ($player) {
                $bowlingPlayers[] = $player;
            }
        }


        Log::info("bowlingTeamDetails count:", ['count' => $bowlingTeamDetails->count()]);
        Log::info("BattingTeamTwelfthMan" , [$BattingTeamTwelfthMan]);
        Log::info("BowlingTeamTwelthMan" , [$BowlingTeamTwelthMan]);

        $battingTwelfthManIds = $BattingTeamTwelfthMan->pluck('player_id')->toArray();
        $bowlingTwelfthManIds = $BowlingTeamTwelthMan->pluck('player_id')->toArray();


        // Fetch bowling players and filter by overs_bowled
        $TwooversBowledPlayers = [];
        $oversBowled = null;
            foreach ($bowlingTeamDetails as $detail) {
                $player = Player::find($detail->player_id);

                if ($isSuperOver === 'true' || $isSecondInningsSuperOver === 'true' || $isSecondSuperOver === 'true') {
                    $latestOversBowled = 0;
                    $isDisabled = false;
                } else {
                    $oversBowled = PlayerBowlingStats::where('match_id', $id)
                        ->where('player_id', $detail->player_id)
                        ->orderBy('created_at', 'DESC')
                        ->first();
                    $latestOversBowled = $oversBowled ? $oversBowled->overs_bowled : 0;
                    $isDisabled = $latestOversBowled >= $oversPerBowler;
                }


                $TwooversBowledPlayers[] = [
                    'player' => $player,
                    'oversBowled' => $latestOversBowled,
                    'isDisabled' => $isDisabled,
                    'twelfthMan' => in_array($player->id, array_merge($battingTwelfthManIds , $bowlingTwelfthManIds))
                ];
            }

             foreach ($TwooversBowledPlayers as $playerData) {
                Log::info("TwooversBowledPlayer", $playerData);
            }



            $totaloversBowled = BallByBall::where('match_id', $id)
            ->where('innings_completed', '!=', -1)
            ->orderby('total_overs', 'desc')->get('total_overs')
            ->first();

            $totalOverNumber = $totaloversBowled ? $totaloversBowled->total_overs : '0.0';
            Log::info("total_overs" , [$totalOverNumber]);

            Log::info("bowlingTeamDetails count:", ['countiffffffffffff' => count($bowlingPlayers)]);

        return response()->json([
            'battingPlayers' => $battingPlayers,
            'bowlingPlayers' => $bowlingPlayers,
            'battingTeam' => $battingTeam,
            'bowlingTeam' => $bowlingTeam,
            'isSecondInnings' => $isSecondInnings ,
            'isSuperOver' => $isSuperOver,
            'text' => $text,
            'batting' => $batting_text,
            'bowling' => $bowling_text,
            'matchDetails' => $match ,
            'TwooversBowledPlayers' => $TwooversBowledPlayers,
            // 'BattingTeamTwelfthMan' => $BattingTeamTwelfthMan ? Player::find($BattingTeamTwelfthMan->player_id) : null,
            // 'BowlingTeamTwelthMan' => $BowlingTeamTwelthMan ? Player::find($BowlingTeamTwelthMan->player_id) : null,
            'BattingTeamTwelfthMan' => $BattingTeamTwelfthMan->map(function($twelfthMan) {
                return Player::find($twelfthMan->player_id);
            }),
            'BowlingTeamTwelthMan' => $BowlingTeamTwelthMan->map(function($twelfthMan) {
                return Player::find($twelfthMan->player_id);
            }),
            'totaloversBowled' => $totalOverNumber
        ], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to fetch details.', 'message' => $e->getMessage()], 500);
    }
}
  
    public function checkMatchStatus($matchId){
        try{
            $match = MatchGame::where('matches.schedule_match_id', $matchId)
            ->join('teams as toss', 'matches.toss', '=', 'toss.id')
            ->join('teams as batting_team', 'matches.batting', '=', 'batting_team.id')
            ->join('teams as bowling_team', 'matches.bowling', '=', 'bowling_team.id')
            ->leftJoin('players as first_umpire_details', 'matches.first_umpire', '=', 'first_umpire_details.id')
            ->leftJoin('players as second_umpire_details', 'matches.second_umpire', '=', 'second_umpire_details.id')
            ->leftJoin('players as third_umpire_details', 'matches.third_umpire', '=', 'third_umpire_details.id')
            ->leftJoin('players as first_scorer_details', 'matches.first_scorer', '=', 'first_scorer_details.id')
            ->leftJoin('players as second_scorer_details', 'matches.second_scorer', '=', 'second_scorer_details.id')
            // ->leftJoin('venues' , 'venues.id', '=' , 'matches.venue')
            ->select(
                'matches.*',
                // 'venues.name as venue',
                'toss.name as toss_winner',
                'batting_team.name as batting_team',
                'bowling_team.name as bowling_team',
                'first_umpire_details.name as first_umpire_name',
                'second_umpire_details.name as second_umpire_name',
                'third_umpire_details.name as third_umpire_name',
                'first_scorer_details.name as first_scorer_name',
                'second_scorer_details.name as second_scorer_name',
            )
            ->first();
            //  $curr_over_number = $is_data_there->over_number;
            // $over = $curr_over_number + ($is_data_there->valid_ball_count / 10);
            // $over = ($over - 1) ."/" . $match_data->overs;

            if(isset($match)) {
                 $over = $match->over;
                 $match->over = "0.0/".$over;
                return response()->json(['msg' => 'found', 'status' => $match->status, 'data' => $match], 200);
            }else {
                return response()->json(['msg'=> 'not found', 'status' => 'not started', 'data' => $match], 200);
            }
        }catch(\Exception $err) {
            return response()->json(['error' => 'Failed to fetch details.', 'message' => $err->getMessage()], 500);
        }
    }
    public function getSquadsPlayers(Request $request) {
        $schedule_match_id = $request->match_id;
        $squadA_id = $request->squadA_id;
        $squadB_id = $request->squadB_id;

        $match_id = MatchGame::where('schedule_match_id', $schedule_match_id)->first();

        if($match_id) {
        $squadA_data = Team1Detail::where('team1_details.match_id', $match_id->id)
            ->where('team1_details.team_id', $squadA_id)
            ->leftJoin('players', 'team1_details.player_id', '=', 'players.id')
            ->leftJoin('teams', 'teams.id', '=', 'team1_details.team_id')
            ->leftJoin(
                DB::raw('(SELECT 
                            bowler_id, 
                            COUNT(DISTINCT match_id) as matches_played,
                            SUM(runs_conceded) as total_runs_conceded,
                            SUM(overs_bowled) as total_overs_bowled,
                            SUM(wickets) as total_wickets,
                            SUM(maidens) as total_maidens,
                            COUNT(CASE WHEN wickets >= 3 THEN 1 ELSE NULL END) as threefer,
                            COUNT(CASE WHEN wickets >= 5 THEN 1 ELSE NULL END) as fifer
                        FROM bowlers_scoreboards 
                        WHERE inning IN (0, 1) 
                        GROUP BY bowler_id) as match_stats'),
                'match_stats.bowler_id', '=', 'team1_details.player_id'
            )
            ->leftJoin(
                DB::raw('(SELECT 
                            batter_id, 
                            COUNT(DISTINCT match_id) as matches_played,
                            SUM(runs) as total_runs,
                            SUM(fours) as total_fours,
                            SUM(sixes) as total_sixes,
                            MAX(runs) as highest_score,
                            SUM(balls_faced) as total_balls_faced,
                            COUNT(CASE WHEN is_out = 1 THEN 1 ELSE NULL END) as dismissals ,
                            SUM(is_out) as was_out,
                            COUNT(CASE WHEN runs >= 50 AND runs < 100 THEN 1 ELSE NULL END) as fifties,
                            COUNT(CASE WHEN runs >= 100 THEN 1 ELSE NULL END) as hundreds,
                            COUNT(CASE WHEN is_out = 0 THEN 1 ELSE NULL END) as notOuts
                        FROM scoreboards 
                        WHERE inning IN (0, 1) 
                        GROUP BY batter_id) as match_stats_batter'),
                'match_stats_batter.batter_id', '=', 'team1_details.player_id'
            )
            ->select(
                'players.name as name',
                'players.image as imageUrl',
                'players.role as player_role',
                'team1_details.player_id',
                'team1_details.captain',
                'team1_details.wicketkeeper',
                'team1_details.12th_man',
                'teams.name as team_name',
                DB::raw(
                    'CASE 
                        WHEN team1_details.captain = 1 AND team1_details.wicketkeeper = 1 THEN "WK-Captain" 
                        WHEN team1_details.captain = 1 THEN "Captain" 
                        WHEN team1_details.wicketkeeper = 1 THEN "WicketKeeper" 
                        WHEN players.role IS NULL THEN "Player" 
                        ELSE CONCAT(UPPER(SUBSTRING(players.role, 1, 1)), LOWER(SUBSTRING(players.role, 2))) 
                    END AS role'
                ),
                'team1_details.match_id',

                // Batting Stats
                DB::raw('COALESCE(match_stats_batter.matches_played, 0) as matchesPlayed'),
                DB::raw('COALESCE(match_stats_batter.total_runs, 0) as runsScored'),
                DB::raw('COALESCE(match_stats_batter.total_fours, 0) as fours'),
                DB::raw('COALESCE(match_stats_batter.total_sixes, 0) as sixes'),
                DB::raw('COALESCE(match_stats_batter.highest_score, 0) as highestScore'),
                DB::raw('ROUND(COALESCE(match_stats_batter.total_runs, 0) / NULLIF(match_stats_batter.dismissals, 0), 2) as average'),
                DB::raw('ROUND((COALESCE(match_stats_batter.total_runs, 0) * 100) / NULLIF(match_stats_batter.total_balls_faced, 0), 2) as strikeRate'),
                DB::raw('COALESCE(match_stats_batter.fifties, 0) as fastest50'),
                DB::raw('COALESCE(match_stats_batter.hundreds, 0) as fastest100'),
                DB::raw('COALESCE(match_stats_batter.notOuts, 0) as notOuts'),


                // Bowling Stats
                DB::raw('COALESCE(match_stats.matches_played, 0) as matchesBowled'),
                DB::raw('CAST(COALESCE(match_stats.total_overs_bowled, 0) AS DECIMAL(10,2)) as oversBowled'),
                DB::raw('COALESCE(match_stats.total_runs_conceded, 0) as runsConceded'),
                DB::raw('COALESCE(match_stats.total_wickets, 0) as wicketsTaken'),
                DB::raw('COALESCE(match_stats.total_maidens, 0) as maidens'),
                DB::raw('ROUND(COALESCE(match_stats.total_runs_conceded, 0) / NULLIF(match_stats.total_overs_bowled, 0), 2) as economy'),
                DB::raw('COALESCE(match_stats.threefer, 0) as threeWicketHauls'),
                DB::raw('COALESCE(match_stats.fifer, 0) as fiveWicketHauls')
            )
            ->groupBy(
                'team1_details.player_id',
                'team1_details.captain',
                'team1_details.wicketkeeper',
                'team1_details.12th_man',
                'team1_details.match_id',
                'players.name',
                'players.image',
                'players.role',
                'teams.name',
                'match_stats_batter.matches_played',
                'match_stats_batter.total_runs',
                'match_stats_batter.total_fours',
                'match_stats_batter.total_sixes',
                'match_stats_batter.highest_score',
                'match_stats_batter.total_balls_faced',
                'match_stats_batter.dismissals',
                'match_stats.matches_played',
                'match_stats.total_overs_bowled',
                'match_stats.total_runs_conceded',
                'match_stats.total_wickets',
                'match_stats.total_maidens',
                'match_stats.threefer',
                'match_stats.fifer'
            )
            ->get();


           $squadB_data = Team2Detail::where('team2_details.match_id', $match_id->id)
            ->where('team2_details.team_id', $squadB_id)
            ->leftJoin('players', 'team2_details.player_id', '=', 'players.id')
            ->leftJoin('teams', 'teams.id', '=', 'team2_details.team_id')
            ->leftJoin(
                DB::raw('(SELECT
                            bowler_id,
                            COUNT(DISTINCT match_id) as matches_played,
                            SUM(runs_conceded) as total_runs_conceded,
                            SUM(overs_bowled) as total_overs_bowled,
                            SUM(wickets) as total_wickets,
                            SUM(maidens) as total_maidens,
                            COUNT(CASE WHEN wickets >= 3 THEN 1 ELSE NULL END) as threefer,
                            COUNT(CASE WHEN wickets >= 5 THEN 1 ELSE NULL END) as fifer
                        FROM bowlers_scoreboards
                        WHERE inning IN (0, 1)
                        GROUP BY bowler_id) as match_stats'),
                'match_stats.bowler_id', '=', 'team2_details.player_id'
            )
            ->leftJoin(
                DB::raw('(SELECT
                            batter_id,
                            COUNT(DISTINCT match_id) as matches_played,
                            SUM(runs) as total_runs,
                            SUM(fours) as total_fours,
                            SUM(sixes) as total_sixes,
                            MAX(runs) as highest_score,
                            SUM(balls_faced) as total_balls_faced,
                            COUNT(CASE WHEN is_out = 1 THEN 1 ELSE NULL END) as dismissals,
                            SUM(is_out) as was_out,
                            COUNT(CASE WHEN runs >= 50 AND runs < 100 THEN 1 ELSE NULL END) as fifties,
                            COUNT(CASE WHEN runs >= 100 THEN 1 ELSE NULL END) as hundreds,
                            COUNT(CASE WHEN is_out = 0 THEN 1 ELSE NULL END) as notOuts
                        FROM scoreboards
                        WHERE inning IN (0, 1)
                        GROUP BY batter_id) as match_stats_batter'),
                'match_stats_batter.batter_id', '=', 'team2_details.player_id'
            )
            ->select(
                'players.name as name',
                'players.image as imageUrl',
                'players.role as player_role',
                'team2_details.player_id',
                'team2_details.captain',
                'team2_details.wicketkeeper',
                'team2_details.12th_man',
                'teams.name as team_name',
                DB::raw(
                    'CASE
                        WHEN team2_details.captain = 1 AND team2_details.wicketkeeper = 1 THEN "WK-Captain"
                        WHEN team2_details.captain = 1 THEN "Captain"
                        WHEN team2_details.wicketkeeper = 1 THEN "WicketKeeper"
                        WHEN players.role IS NULL THEN "Player"
                        ELSE CONCAT(UPPER(SUBSTRING(players.role, 1, 1)), LOWER(SUBSTRING(players.role, 2)))
                    END AS role'
                ),
                'team2_details.match_id',

                // Batting Stats
                DB::raw('COALESCE(match_stats_batter.matches_played, 0) as matchesPlayed'),
                DB::raw('COALESCE(match_stats_batter.total_runs, 0) as runsScored'),
                DB::raw('COALESCE(match_stats_batter.total_fours, 0) as fours'),
                DB::raw('COALESCE(match_stats_batter.total_sixes, 0) as sixes'),
                DB::raw('COALESCE(match_stats_batter.highest_score, 0) as highestScore'),
                DB::raw('ROUND(COALESCE(match_stats_batter.total_runs, 0) / NULLIF(match_stats_batter.dismissals, 0), 2) as average'),
                DB::raw('ROUND((COALESCE(match_stats_batter.total_runs, 0) * 100) / NULLIF(match_stats_batter.total_balls_faced, 0), 2) as strikeRate'),
                DB::raw('COALESCE(match_stats_batter.fifties, 0) as fastest50'),
                DB::raw('COALESCE(match_stats_batter.hundreds, 0) as fastest100'),
                DB::raw('COALESCE(match_stats_batter.notOuts, 0) as notOuts'),

                // Bowling Stats
                DB::raw('COALESCE(match_stats.matches_played, 0) as matchesBowled'),
                DB::raw('CAST(COALESCE(match_stats.total_overs_bowled, 0) AS DECIMAL(10,2)) as oversBowled'),
                DB::raw('COALESCE(match_stats.total_runs_conceded, 0) as runsConceded'),
                DB::raw('COALESCE(match_stats.total_wickets, 0) as wicketsTaken'),
                DB::raw('COALESCE(match_stats.total_maidens, 0) as maidens'),
                DB::raw('ROUND(COALESCE(match_stats.total_runs_conceded, 0) / NULLIF(match_stats.total_overs_bowled, 0), 2) as economy'),
                DB::raw('COALESCE(match_stats.threefer, 0) as threeWicketHauls'),
                DB::raw('COALESCE(match_stats.fifer, 0) as fiveWicketHauls')
            )
            ->groupBy(
                'team2_details.player_id',
                'team2_details.captain',
                'team2_details.wicketkeeper',
                'team2_details.12th_man',
                'team2_details.match_id',
                'players.name',
                'players.image',
                'players.role',
                'teams.name',
                'match_stats_batter.matches_played',
                'match_stats_batter.total_runs',
                'match_stats_batter.total_fours',
                'match_stats_batter.total_sixes',
                'match_stats_batter.highest_score',
                'match_stats_batter.total_balls_faced',
                'match_stats_batter.dismissals',
                'match_stats.matches_played',
                'match_stats.total_overs_bowled',
                'match_stats.total_runs_conceded',
                'match_stats.total_wickets',
                'match_stats.total_maidens',
                'match_stats.threefer',
                'match_stats.fifer'
            )
            ->get();


            $squadA_playing_11 = array_filter($squadA_data->toArray(), function($data){ return $data['12th_man'] == 0; });
            $squadB_playing_11 = array_filter($squadB_data->toArray(), function($data){ return $data['12th_man'] == 0; });

            $squadA_bench = array_filter($squadA_data->toArray(), function($data){ return $data['12th_man'] == 1; });
            $squadB_bench = array_filter($squadB_data->toArray(), function($data){ return $data['12th_man'] == 1; });


            return response()->json([
                'is_found' => 1,
                'squadA_playing_11' => array_values($squadA_playing_11),
                'squadB_playing_11' => array_values($squadB_playing_11),
                'squadA_bench' => array_values($squadA_bench),
                'squadB_bench' => array_values($squadB_bench),
            ]);
        }

        return response()->json([
                'is_found' => 0,
                'squadA_playing_11' => [],
                'squadB_playing_11' => [],
                'squadA_bench' => [], 
                'squadB_bench' => [],
        ]);
    }
  
    public function getMatchBowler($match_id) {
        $match_players = MatchPlayer::where('match_id', $match_id)->orderBy('id', 'desc')->first();
        if(isset($match_players)) {
            $bowling_status = PlayerBowlingStats::
          	where('player_bowling_stats.match_id', $match_id)
            ->where('player_bowling_stats.player_id', $match_players->bowler_id)
            ->leftJoin('players', 'players.id', '=', 'player_bowling_stats.player_id')
            ->select(
				'player_bowling_stats.player_id',
              	'player_bowling_stats.match_id',
				'players.name as name',
                'player_bowling_stats.overs_bowled as overs',
                'player_bowling_stats.maiden_overs as maidens',
                'player_bowling_stats.runs_conceded as runs',
                'player_bowling_stats.wickets_taken as wickets',
                'player_bowling_stats.economy_rate as economy',
            )
            ->orderBy('player_bowling_stats.id', 'desc')->limit(1)->first();

            if(!isset($bowling_status)) {
                $bowler_data = Player::where('id', $match_players->bowler_id)->first();

                $bowler = new stdClass();
                $bowler->name = "$bowler_data->name";
                $bowler->overs = '0.0';
                $bowler->maidens = '0';
                $bowler->runs = '0';
                $bowler->wickets = '0';
                $bowler->economy = '0.00';
                return $bowler;
            }
            return $bowling_status;
        }
        $bowler = new stdClass();
        $bowler->name = 'Bowler';
        $bowler->overs = '0.0';
        $bowler->maidens = '0';
        $bowler->runs = '0';
        $bowler->wickets = '0';
        $bowler->economy = '0.00';
        return $bowler;
    }
    public function getUptoMatchData($matchId) {
        $match_id = $matchId;
        $match_players = MatchPlayer::where('match_id', $match_id)->orderBy('id', 'desc')->first();

        $batsMen = $this->getBatsMen($match_id);
        $bowler = $this->getMatchBowler($match_id);
        $over_summary = $this->fetchOverSummary($match_id);
        $undo_data = $this->getUndoData($match_id);

        return response()->json([
            'matchId' => $match_id,
            'batsMen' => array_values($batsMen),
            'bowler' => $bowler,
            'summary' => $over_summary,
            'status' => isset($match_players) ? 'found' : 'not found',
            'undoData' => $undo_data,
            ]);
    }

    public function createOrUpdateMatchPlayers(Request $request) {
        $striker = $request->strikerId;
        $non_striker = $request->nonStrikerId;
        $bowler = $request->bowlerId;
        $match_id = $request->matchId;
        $current_innings = $request->currentInnings ?? 0;
        $team_id = $request->battingTeamId;
        $bowling_team_id = $request->bowlingTeamId;

        // $team_ids = BallByBall::where('match_id', $match_id)
        // ->where('innings_completed', $current_innings)
        // ->where('over_number', "!=", -1)
        // ->orderBy('id', 'desc')
        // ->first();
        // $team_id = isset($team_ids) ? $team_ids->batting_team_id : MatchGame::find($match_id)->batting;
        // $bowling_team_id = isset($team_ids) ? $team_ids->bowling_team_id : MatchGame::find($match_id)->bowling;
      	
        if($current_innings < 5) {
            MatchPlayer::where('match_id', $match_id)->whereBetween('current_innings', [($current_innings + 1), 5])->forceDelete();
        }
      
        $match_players = MatchPlayer::where('match_id', $match_id)
        ->where('team_id', $team_id)
        ->where('current_innings', $current_innings)
        ->orderBy('id', 'desc')->first();


        $score_board_ctrl = new ScoreBoardController($match_id, $team_id, $bowling_team_id, $current_innings);
        $is_striker_exist = $score_board_ctrl->isBatsManExist($striker);
        $is_non_striker_exist = $score_board_ctrl->isBatsManExist($non_striker);
        $is_bowler_exist = $score_board_ctrl->isBowlerExist($bowler);

        $store_data = [
            'match_id' => $match_id,
            'team_id' => $team_id,
            'inning' => $current_innings,
            'runs' => 0,
            'balls_faced' => 0,
            'fours' => 0,
            'sixes' => 0,
            'strike_rate' => 0,
        ];
        if(!isset($is_striker_exist)) {
            $store_data['batter_id'] = $striker;
            $score_board_ctrl->storeScoreBoard($store_data);
        }

        if(!isset($is_non_striker_exist)) {
            $store_data['batter_id'] = $non_striker;
            $score_board_ctrl->storeScoreBoard($store_data);
        }

        if(!isset($is_bowler_exist)) {
            $bowler_data = [
                'match_id' => $match_id,
                'team_id' => $bowling_team_id,
                'inning' => $current_innings,
                'bowler_id' => $bowler,
                'is_max_overs_bowled' => 0,
                'overs_bowled' => 0.0,
                'runs_conceded' => 0,
                'wickets' => 0,
                'maidens' => 0,
                'economy' => 0.00,
            ];
            $score_board_ctrl->storeBowlerScoreBoard($bowler_data);
        }

        //UPDATING (or) CREATING MatchPlayers
        if(isset($match_players)) {
            // $old_striker = $match_players->striker_id;
            // $old_non_striker = $match_players->non_striker_id;

            // $old_batsmen = collect([$old_striker, $old_non_striker]);
            // $diff_old_batsmen = $old_batsmen->diff([$striker, $non_striker]);
            $diff_old_batsmen = collect($score_board_ctrl->getScoreBoardBatsMen($team_id, $current_innings));

            $diff_old_batsmen->each(function (array $item) use ($match_id, $team_id, $score_board_ctrl, $current_innings){
                $not_exist_in_bbb = BallByBall::where('match_id', $match_id)
                ->where('batting_team_id', $team_id)->where('striker_id', $item['batter_id'])->where('innings_completed', $current_innings)
                ->orWhere('non_striker_id', $item['batter_id'])->orWhere('dismissed_batsmen', $item['batter_id'])->doesntExist();

                $is_bbb_exist = BallByBall::where('match_id', $match_id)->where('batting_team_id', $team_id)
                ->where('innings_completed', $current_innings)->exists();
                if($is_bbb_exist && $not_exist_in_bbb) {
                    $score_board_ctrl->deleteBatsman($item['batter_id']);
                }
            });

           $score_board_ctrl->deleteBowlersWhoseNotBowled($bowler);
          
            $match_players->striker_id = $striker;
            $match_players->non_striker_id = $non_striker;
            $match_players->bowler_id = $bowler;
            $match_players->current_innings = $current_innings;
            $match_players->save();

            $batsMen = $this->getBatsMen($match_id);
            $bowler_data = $this->getMatchBowler($match_id);
            return response()->json([
                'is_table_found' => 'found',
                'match_id' => $match_id,
                'striker_id' => $striker,
                'non_striker_id' => $non_striker,
                'bowler_id' => $bowler,
                'batsMen' => $batsMen,
                'bowler' => $bowler_data,
                'current_innings' => $current_innings,
                'team_id' => $team_id,
            ]);
            exit();
        }
        MatchPlayer::create([
            'match_id' => $match_id,
            'team_id' => $team_id,
            'striker_id' => $striker,
            'non_striker_id' => $non_striker,
            'bowler_id' => $bowler,
            'current_innings' => $current_innings,
        ]);

        $batsMen = $this->getBatsMen($match_id);
        $bowler_data = $this->getMatchBowler($match_id);
        return response()->json([
            'is_table_found' => 'not found',
            'match_id' => $request->matchId,
            'striker_id' => $striker,
            'non_striker_id' => $non_striker,
            'bowler_id' => $bowler,
            'batsMen' => $batsMen,
            'bowler' => $bowler_data,
            'team_id' => $team_id,
        ]);
    }
    public function fetchOverSummary($match_id) {
        $match_id = (int) $match_id;
        $match_players = MatchPlayer::where('match_id', $match_id)->orderBy('id', 'desc')->first();
        $ball_by_ball = BallByBall::where('match_id', $match_id)->orderBy('id', 'ASC')->first();
        $match_data = MatchGame::where('matches.id', $match_id)
        ->join('teams as batting_team', 'batting_team.id', '=', 'matches.batting')
        ->join('teams as bowling_team', 'bowling_team.id', '=', 'matches.bowling')
        ->select('matches.*', 'batting_team.name as batting_team_name', 'bowling_team.name as bowling_team_name')
        ->first();

        $team_1_summary = new stdClass();
        $team_1_summary->id = isset($match_data) ? $match_data->batting : null;
        $team_1_summary->isFirstInningBatter = false;
        $team_1_summary->isSecondInningBatter = false;
        $team_1_summary->isSuperOverFirstInningBatter = false;
        $team_1_summary->isSuperOverSecondInningBatter = false;
        $team_1_summary->isSecondSuperOverFirstInningBatter = false;
        $team_1_summary->isSecondSuperOverSecondInningBatter = false;
        $team_1_summary->superOverSummary = [];
        $team_1_summary->superOverCommentary = [];
        $team_1_summary->secondSuperOverSummary = [];
        $team_1_summary->secondSuperOverCommentary = [];
        // $team_1_summary->isSecondSuperOverSecondInningBatter = false;
        $team_1_summary->overSummary = [];
        $team_1_summary->commentary = [];

        $team_2_summary = new stdClass();
        $team_2_summary->id = isset($match_data) ? $match_data->bowling : null;
        $team_2_summary->isFirstInningBatter = false;
        $team_2_summary->isSecondInningBatter = false;
        $team_2_summary->isSuperOverFirstInningBatter = false;
        $team_2_summary->isSuperOverSecondInningBatter = false;
        $team_2_summary->isSecondSuperOverFirstInningBatter = false;
        $team_2_summary->isSecondSuperOverSecondInningBatter = false;
        $team_2_summary->superOverSummary = [];
        $team_2_summary->superOverCommentary = [];
        $team_2_summary->secondSuperOverSummary = [];
        $team_2_summary->secondSuperOverCommentary = [];
        // $team_2_summary->isSecondSuperOverSecondInningBatter = false;
        $team_2_summary->overSummary = [];
        $team_2_summary->commentary = [];

        $batting_team_id = $ball_by_ball ? $ball_by_ball->batting_team_id: null;
        $bowling_team_id = $ball_by_ball ? $ball_by_ball->bowling_team_id: null;
        if(isset($match_players) && isset($ball_by_ball)) {
            $team_1_id = $team_1_summary->id;
            $team_2_id = $team_2_summary->id;
            $teams = array($team_1_id, $team_2_id);
            $total_over = $match_data->overs;
            $initial_over = 1;

            $first_innings_completed_data = BallByBall::where('match_id', $match_id)->where('innings_completed', 1)
            ->where('over_number', '=', -1)
            ->orderBy('id', 'ASC')
            ->first();
            $second_innings_completed_data = BallByBall::where('match_id', $match_id)->where('innings_completed', 2)
            ->where('over_number', '=', -1)
            ->orderBy('id', 'ASC')
            ->first();
            $superover_first_innings_completed_data = BallByBall::where('match_id', $match_id)->where('innings_completed', 3)
            ->where('over_number', '=', -1)
            ->orderBy('id', 'ASC')
            ->first();
            $superover_second_innings_completed_data = BallByBall::where('match_id', $match_id)->where('innings_completed', 4)
            ->where('over_number', '=', -1)
            ->orderBy('id', 'ASC')
            ->first();
            $second_superover_first_innings_completed_data = BallByBall::where('match_id', $match_id)->where('innings_completed', 5)
            ->where('over_number', '=', -1)
            ->orderBy('id', 'ASC')
            ->first();

            foreach($teams as $team) {
                // if($innings == 0){
                //     $createSummary = $this->createSummary($innings,$initial_over, $total_over,$team, $match_id);
                //     $team_1_summary->overSummary = $createSummary['over_summary'];
                //     $team_1_summary->commentary = $createSummary['commentary'];
                // }

                $total_innings = BallByBall::where('match_id', $match_id)->orderBy('id', 'DESC')->first();
                $total_innings = $total_innings->over_number == -1 ? $total_innings->innings_completed - 1 : $total_innings->innings_completed;
                for($inning = 0; $inning <= $total_innings; $inning++) {
                    // $inning = 3;
                    $createSummary = $this->createSummary($inning,$initial_over, $total_over,$team, $match_id);
                    if($inning == 0 && !count($team_1_summary->overSummary)) {
                        $team_1_summary->overSummary = $createSummary['over_summary'];
                        $team_1_summary->commentary = $createSummary['commentary'];
                    }
                    if($inning == 1 && !count($team_2_summary->overSummary)){
                        $team_2_summary->overSummary = $createSummary['over_summary'];
                        $team_2_summary->commentary = $createSummary['commentary'];
                    }
                    if($inning == 2 && !count($team_2_summary->superOverSummary)) {
                        $team_2_summary->superOverSummary = $createSummary['over_summary'];
                        $team_2_summary->superOverCommentary = $createSummary['commentary'];
                    }
                    if($inning == 3 && !count($team_1_summary->superOverSummary)){
                        $team_1_summary->superOverSummary = $createSummary['over_summary'];
                        $team_1_summary->superOverCommentary = $createSummary['commentary'];
                    }
                    if($inning == 4 && !count($team_1_summary->secondSuperOverSummary)) {
                        $team_1_summary->secondSuperOverSummary = $createSummary['over_summary'];
                        $team_1_summary->secondSuperOverCommentary = $createSummary['commentary'];
                    }
                    if($inning == 5 && !count($team_2_summary->secondSuperOverSummary)){
                        $team_2_summary->secondSuperOverSummary = $createSummary['over_summary'];
                        $team_2_summary->secondSuperOverCommentary = $createSummary['commentary'];
                    }
                }

                if(isset($first_innings_completed_data)) {
                    if($first_innings_completed_data->batting_team_id == $team_1_summary->id) {
                        $team_1_summary->isFirstInningBatter = true;
                    }
                    if($first_innings_completed_data->batting_team_id == $team_2_summary->id) {
                        $team_2_summary->isFirstInningBatter = true;
                    }
                }

                if(isset($second_innings_completed_data)) {
                    if($second_innings_completed_data->batting_team_id == $team_1_summary->id) {
                        $team_1_summary->isSecondInningBatter = true;
                    }
                    if($second_innings_completed_data->batting_team_id == $team_2_summary->id) {
                        $team_2_summary->isSecondInningBatter = true;
                    }
                }


                //SUPEROVER'S
                if(isset($superover_first_innings_completed_data)) {
                     if($superover_first_innings_completed_data->batting_team_id == $team_1_summary->id) {
                        $team_1_summary->isSuperOverFirstInningBatter = true;
                    }
                    if($superover_first_innings_completed_data->batting_team_id == $team_2_summary->id) {
                        $team_2_summary->isSuperOverFirstInningBatter = true;
                    }
                }
                if(isset($superover_second_innings_completed_data)) {
                     if($superover_second_innings_completed_data->batting_team_id == $team_1_summary->id) {
                        $team_1_summary->isSuperOverSecondInningBatter = true;
                    }
                    if($superover_second_innings_completed_data->batting_team_id == $team_2_summary->id) {
                        $team_2_summary->isSuperOverSecondInningBatter = true;
                    }
                }

                if(isset($second_superover_first_innings_completed_data)) {
                     if($second_superover_first_innings_completed_data->batting_team_id == $team_1_summary->id) {
                        $team_1_summary->isSecondSuperOverFirstInningBatter = true;
                    }
                    if($second_superover_first_innings_completed_data->batting_team_id == $team_2_summary->id) {
                        $team_2_summary->isSecondSuperOverFirstInningBatter = true;
                    }
              }
                if(isset($second_superover_second_innings_completed_data)) {
                     if($second_superover_second_innings_completed_data->batting_team_id == $team_1_summary->id) {
                        $team_1_summary->isSecondSuperOverSecondInningBatter = true;
                    }
                    if($second_superover_second_innings_completed_data->batting_team_id == $team_2_summary->id) {
                        $team_2_summary->isSecondSuperOverSecondInningBatter = true;
                    }
                }
            }
            return ([
                'match_id' => $match_id,
                // 'overSummary' => $over_summary,
                // 'commentary' => $commentary,
                'team1Summary' => $team_1_summary,
                'team2Summary' => $team_2_summary,
                'battingTeamId' => $batting_team_id,
                'bowlingTeamId' => $bowling_team_id,
                'battingTeamName' => $match_data->batting_team_name,
                'bowlingTeamName' => $match_data->bowling_team_name,
                // 'current_inning' => $innings
            ]);
        }
        return ([
            'match_id' => $match_id,
            // 'overSummary' => [],
            // 'commentary' => [],
            'team1Summary' => $team_1_summary,
            'team2Summary' => $team_2_summary,
            'battingTeamId' => $batting_team_id,
            'bowlingTeamId' => $bowling_team_id,
            'battingTeamName' => isset($match_data) ? $match_data->batting_team_name : null,
            'bowlingTeamName' => isset($match_data) ? $match_data->bowling_team_name : null,
            // 'current_inning' => $innings
        ]);
    }
  
    public function commentaryText($runs, $striker_id, $non_striker_id, $bowler_id, $fielder_id, $wicket_type) {
        $pos = strpos($runs, '+');
        $extra_runs = null;
        // $wicket_type = strtolower($wicket_type);
        if(isset($wicket_type) && strtolower($wicket_type) !== 'wd' && strtolower($wicket_type) !== 'nb'){
            $runs = strtolower($wicket_type);
        }else {
            if($pos !== false){
                $extra_runs = substr($runs, ($pos + 1));
                $runs = substr($runs, 0, $pos);
            }
            if((int)$runs == 5 || (int) $runs > 6) {
                $extra_runs = $runs;
                $runs = 'EXTRA_RUNS';
            }
        }

        $runs = strtolower($runs);
        $commentary_arr = config("commentary.$runs");

        if(isset($commentary_arr) && count($commentary_arr)) {
            $rand = rand(0, (count($commentary_arr) - 1));
            $rand = rand(0, $rand);
            $commentary = $commentary_arr[$rand];
            $find = ['@striker_name', '@non_striker_name', '@bowler_name', '@extra_runs', '@keeper_name'];
            $striker_name = Player::where('id', $striker_id)->first();
            $striker_name = isset($striker_name) ? $striker_name->name: 'Striker';

            $non_striker_name = Player::where('id', $non_striker_id)->first();
            $non_striker_name = isset($non_striker_name) ? $non_striker_name->name: 'Non-Striker';

            $bowler_name = Player::where('id', $bowler_id)->first();
            $bowler_name = isset($bowler_name) ? $bowler_name->name: 'Bowler';

            $fielder_name = Player::where('id', $fielder_id)->first();
            $fielder_name = isset($fielder_name) ? $fielder_name->name: 'Fielder/Keeper';

            $replace = [$striker_name, $non_striker_name, $bowler_name, $extra_runs, $fielder_name];
            $commentary = str_replace($find, $replace, $commentary);

            $outs = ['bowled', 'runout', 'retired hurt', 'retired out', 'caught behind', 'caught & bowled', 'hit wicket', 'stumped', 'run out (mankaded)', 'nb+w(runout)', 'wd+w', 'wd+w(runout)', 'caught', 'b+w(runout)','b', 'w'];
            $runsText = in_array($runs, $outs, true) ? $runs : ($runs = $extra_runs ?? $runs) . " runs";
            $deliversCommentary = "$bowler_name delivers to $striker_name , $runsText";

            return $deliversCommentary . "\n" . $commentary;
        }
        return '';
    }

    public function saveMatchScore(Request $request)
    {
        $request->validate([
            'match_id' => 'required|integer',
            'team_id' => 'required|integer',
            'total_runs' => 'required|integer',
            'total_wickets' => 'nullable',
            'overs_faced' => 'nullable',
            'extras' => 'nullable',
            'is_batting' => 'nullable',
            'projected_score' => 'nullable',
            'run_rate' => 'nullable' ,
            'secondInning' => 'nullable',
            'firstinning' => 'nullable',
            'isWinning' => 'nullable',
            'isTied' => 'nullable' ,
            'totalFours' => 'nullable',
            'totalSixes' => 'nullable' ,
        ]);

        $ExtraType = $request->extras;
        $ExtraRun = 0;
        if ($ExtraType === 'WD' || $ExtraType === 'NB') {
            $ExtraRun = 1;
        } elseif (strpos($ExtraType, 'WD+') === 0 || strpos($ExtraType, 'NB+') === 0) {
            $parts = explode('+', $ExtraType);
            $ExtraRun = 1 + (int)$parts[1];
        }
        $TotalBoundaries = $request->totalFours + $request->totalSixes;
        // Log::info("Total fours and sixes :"  . [$request->total_fours , $request->total_sixes ]);

    //    Log::info("TotalBoundaries :"  . [$TotalBoundaries]);


        // Log::info("isTied :" , [$request->isTied]);
        // Log::info("secomd inning" ,  [$request->secondInning]);

            // Log::info("isTied" ,  $request->isTied );

            // Log::info('isTied:', [$request->isTied, gettype($request->isTied)]);


        $matchScore = MatchScore::updateOrCreate(
            ['match_id' => $request->match_id, 'team_id' => $request->team_id],
            [
                'total_runs' => $request->total_runs,
                'total_wickets' => $request->total_wickets,
                'overs_faced' => $request->overs_faced,
                'extras' =>  $ExtraRun,
                'is_batting' => 1 ,
                'projected_score' => $request->projected_score,
                'run_rate' => $request->run_rate,
                'is_first_inning' => $request->firstinning === 1 ? 1 : 0, // Removed extra space
                'is_second_inning' => $request->secondInning === 1 ? 1 : 0, // Removed extra space
                'is_tied' => $request->isTied == true ? 1 : 0,
                'is_winning' => $request->isWinning,
                'total_fours' => $request->totalFours ,
                'total_sixes' => $request->totalSixes,
                'total_boundaries' => $TotalBoundaries,

            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Match score saved successfully!',
            'data' => $matchScore,
        ], 200);
    }

    public function saveSuperOverMatchScore(Request $request)
    {
        $request->validate([
            'match_id' => 'required|integer',
            'matchScoreId' => 'nullable',
            'team_id' => 'required|integer',
            'total_runs' => 'required|integer',
            'total_wickets' => 'nullable',
            'overs_faced' => 'nullable',
            'extras' => 'nullable',
            'is_batting' => 'nullable',
            'projected_score' => 'nullable',
            'run_rate' => 'nullable' ,
            'secondInning' => 'nullable',
            'firstinning' => 'nullable',
            'isWinning' => 'nullable',
            'isTied' => 'nullable' ,
        ]);

        $ExtraType = $request->extras;
        $ExtraRun = 0;
        if ($ExtraType === 'WD' || $ExtraType === 'NB') {
            $ExtraRun = 1;
        } elseif (strpos($ExtraType, 'WD+') === 0 || strpos($ExtraType, 'NB+') === 0) {
            $parts = explode('+', $ExtraType);
            $ExtraRun = 1 + (int)$parts[1];
        }

        $TotalBoundaries = $request->totalFours + $request->totalSixes;

        // Log::info("superrrrrr ovrrrrrrrrrrrrrrrrrrrrrr: " . ($request->matchScoreId));

        $matchScore = SuperOverScores::updateOrCreate(
            ['match_id' => $request->match_id, 'team_id' => $request->team_id],
            [
                //'match_score_id' => $request->matchScoreId,
                'match_score_id' => $request->matchScoreId ?? MatchScore::where('match_id', $request->match_id)->value('id'),

                'runs_scored' => $request->total_runs,
                'wickets_lost' => $request->total_wickets,
                'overs_bowled' => $request->overs_faced,
                'extras' =>  $ExtraRun,
                // 'is_batting' => 1 ,
                // 'projected_score' => $request->projected_score,
                // 'run_rate' => $request->run_rate,
                'is_first_inning_super_over' => $request->firstinning === 1 ? 1 : 0, // Removed extra space
                'is_second_inning_super_over' => $request->secondInning === 1 ? 1 : 0, // Removed extra space
                'is_tied' => $request->isTied == true ? 1 : 0,
                'is_winning' => $request->isWinning,
                'total_fours' => $request->totalFours ,
                'total_sixes' => $request->totalSixes,
                'total_boundaries' => $TotalBoundaries,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Match score saved successfully!',
            'data' => $matchScore,
        ], 200);
    }


    public function saveSecondSuperOverMatchScore(Request $request)
    {
        $request->validate([
            'match_id' => 'required|integer',
            'matchScoreId' => 'nullable',
            'team_id' => 'required|integer',
            'total_runs' => 'required|integer',
            'total_wickets' => 'nullable',
            'overs_faced' => 'nullable',
            'extras' => 'nullable',
            'is_batting' => 'nullable',
            'projected_score' => 'nullable',
            'run_rate' => 'nullable' ,
            'secondInning' => 'nullable',
            'firstinning' => 'nullable',
            'isWinning' => 'nullable',
            'isTied' => 'nullable' ,
        ]);

        $ExtraType = $request->extras;
        $ExtraRun = 0;
        if ($ExtraType === 'WD' || $ExtraType === 'NB') {
            $ExtraRun = 1;
        } elseif (strpos($ExtraType, 'WD+') === 0 || strpos($ExtraType, 'NB+') === 0) {
            $parts = explode('+', $ExtraType);
            $ExtraRun = 1 + (int)$parts[1];
        }

        $TotalBoundaries = $request->totalFours + $request->totalSixes;

        // Log::info("superrrrrr ovrrrrrrrrrrrrrrrrrrrrrr: " . ($request->matchScoreId));

        $matchScore = SuperOverTwo::updateOrCreate(
            ['match_id' => $request->match_id, 'team_id' => $request->team_id],
            [
                //'match_score_id' => $request->matchScoreId,
                'match_score_id' => $request->matchScoreId ?? MatchScore::where('match_id', $request->match_id)->value('id'),

                'runs_scored' => $request->total_runs,
                'wickets_lost' => $request->total_wickets,
                'overs_bowled' => $request->overs_faced,
                'extras' =>  $ExtraRun,
                // 'is_batting' => 1 ,
                // 'projected_score' => $request->projected_score,
                // 'run_rate' => $request->run_rate,
                'is_first_inning_super_over' => $request->firstinning === 1 ? 1 : 0, // Removed extra space
                'is_second_inning_super_over' => $request->secondInning === 1 ? 1 : 0, // Removed extra space
                'is_tied' => $request->isTied == true ? 1 : 0,
                'is_winning' => $request->isWinning,
                'total_fours' => $request->totalFours ,
                'total_sixes' => $request->totalSixes,
                'total_boundaries' => $TotalBoundaries,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Match score saved successfully!',
            'data' => $matchScore,
        ], 200);
    }


    public function getMatchScore($match_id)
    {
        $matchScores = MatchScore::where('match_id', $match_id)->get();
        $matchScoresWithTeams = [];

        // if ($matchScores->isEmpty()) {
        //     return response()->json(['message' => 'No match score found'], 404);
        // }

        foreach ($matchScores as $matchScore) {
            $teamId = $matchScore->team_id;
            $team = Team::find($teamId);

            $matchScoresWithTeams[] = [
                'match_score' => $matchScore,
                'team_name' => $team ? $team->name : 'Unknown Team'
            ];
        }

        return response()->json($matchScoresWithTeams, 200);
    }


    public function getSuperOverMatchScore($match_id)
    {
        $matchScores = SuperOverScores::where('match_id', $match_id)->get();
        $matchScoresWithTeams = [];

        if ($matchScores->isEmpty()) {
            return response()->json(['message' => 'No match score found'], 404);
        }

        foreach ($matchScores as $matchScore) {
            $teamId = $matchScore->team_id;
            $team = Team::find($teamId);

            $matchScoresWithTeams[] = [
                'match_score' => $matchScore,
                'team_name' => $team ? $team->name : 'Unknown Team'
            ];
        }

        return response()->json($matchScoresWithTeams, 200);
    }

    public function getSecondSuperOverMatchScore($match_id)
    {
        $matchScores = SuperOverTwo::where('match_id', $match_id)->get();
        $matchScoresWithTeams = [];

        if ($matchScores->isEmpty()) {
            return response()->json(['message' => 'No match score found'], 404);
        }

        foreach ($matchScores as $matchScore) {
            $teamId = $matchScore->team_id;
            $team = Team::find($teamId);

            $matchScoresWithTeams[] = [
                'match_score' => $matchScore,
                'team_name' => $team ? $team->name : 'Unknown Team'
            ];
        }

        return response()->json($matchScoresWithTeams, 200);
    }

    public function getTeamScore($match_id) {
               $is_data_there = BallByBall::where('match_id', $match_id)->orderBy('id', 'DESC')->first();
        $match_data = MatchGame::where('id', $match_id)->first();

        if(isset($match_data) && isset($is_data_there)) {
            $batting_team_id = $is_data_there->batting_team_id ;
            $bowling_team_id = $is_data_there->bowling_team_id;

            $current_inning = $is_data_there->innings_completed;
            $current_inning = $is_data_there->over_number == -1 ? $current_inning - 1 : $current_inning;

            $match_status = strtolower($match_data->status);
            $is_match_completed = $match_status === 'completed';
            if($is_match_completed) {
                $current_inning = 1;
                $is_data_there = BallByBall::where('match_id', $match_id)
                ->where('innings_completed', $current_inning)->orderBy('id', 'desc')->first();
                if(isset($is_data_there)){
                    $batting_team_id = $is_data_there->batting_team_id ;
                    $bowling_team_id = $is_data_there->bowling_team_id;
                }
            }

            if($current_inning > 1) {
                $match_data->overs = 1;
            }

            $current_run_rate = $required_run_rate = "0.00";
            $batting_total = $bowling_total = 0;
            $match_total_overs = (int) $match_data->overs;
            $match_status_text = "";
            $superover_innings = [2, 4];

            $batting_team_wickets = BallByBall::where('match_id', $match_id)->where('batting_team_id', $batting_team_id)->where('over_number', '!=', -1)->where('innings_completed', $current_inning)->where('is_wicket', 1)->get()->count();
            // $batting_team_initial = (!in_array($current_inning, $superover_innings) || count($is_new_inning_started) > 1)? BallByBall::where('match_id', $match_id)->where('batting_team_id', $batting_team_id)->where('over_number', '!=', -1)->where('innings_completed', $current_inning)->orderBy('id', 'ASC')->first() : null;
            $batting_team_initial =  BallByBall::where('match_id', $match_id)->where('batting_team_id', $batting_team_id)->where('over_number', '!=', -1)->where('innings_completed', $current_inning)->orderBy('id', 'ASC')->first();
            $batting_score = $bowling_score = "Yet To Bat";
            $batting_overs = $bowling_overs = "0.0/$match_data->overs";
            $batting_curr_overs = 0.0;
            $batting_team_name = Team::where('id', $batting_team_id)->pluck('name')->first();

            //Penalty
            $batting_penalty = Penalty::where('match_id', $match_id)->where('team_id', $batting_team_id)
            ->where('inning', $current_inning)->sum('runs') ?? 0;

            if(isset($batting_team_initial)) {
                // $total = BallByBall::where('match_id', $match_id)->where('batting_team_id', $batting_team_id)->whereBetween('id', [$batting_team_initial->id, $batting_team_final->id])->where('innings_completed', $current_inning)->sum('total_score');
                $batting_team_data = BallByBall::where('match_id', $match_id)->where('batting_team_id', $batting_team_id)->where('innings_completed', $current_inning)->where('over_number', '!=', -1)->orderBy('id', 'desc')->first();
                $batting_total = $batting_team_data->total_score ?? 0;
                $batting_total = ($batting_total + $batting_penalty);

                $batting_score = "$batting_total/$batting_team_wickets";
                $batting_overs = (isset($batting_team_data) ? $batting_team_data->total_overs : '0') . '/' . (isset($match_data) ? $match_data->overs : '0');

              $current_run_rate = number_format((float) ($batting_team_data->current_run_rate ?? 0), 2, '.');
              $batting_curr_overs = (float) ($batting_team_data->total_overs ?? 0);
            }else if(!isset($batting_team_initial) && $batting_penalty > 0) {
                $batting_score = "$batting_penalty/0";
            }

            $current_bowling_innings = $current_inning > 0 ? $current_inning - 1 : $current_inning;
            $bowling_team_wickets = BallByBall::where('match_id', $match_id)->where('batting_team_id', $bowling_team_id)->where('is_wicket', 1)->where('innings_completed', $current_bowling_innings)->where('over_number', '!=', -1)->get()->count();
            // $bowling_team_initial = (!in_array($current_inning, $superover_innings) || count($is_new_inning_started) > 1) ? BallByBall::where('match_id', $match_id)->where('batting_team_id', $bowling_team_id)->orderBy('id', 'ASC')->where('innings_completed', $current_bowling_innings)->where('over_number', '!=', -1)->first() : null;
            $bowling_team_initial = BallByBall::where('match_id', $match_id)->where('batting_team_id', $bowling_team_id)->orderBy('id', 'ASC')->where('innings_completed', $current_bowling_innings)->where('over_number', '!=', -1)->first();
            $needed_runs = 0;
            $remaning_overs = 0;
            $bowling_penalty = Penalty::where('match_id', $match_id)->where('team_id', $bowling_team_id)
            ->where('inning', $current_inning)->sum('runs') ?? 0;

            if(isset($bowling_team_initial)) {
                // $total = BallByBall::where('match_id', $match_id)->where('batting_team_id', $bowling_team_id)->whereBetween('id', [$bowling_team_initial->id, $bowling_team_final->id])->where('innings_completed', $current_bowling_innings)->sum('total_score');
                $bowling_team_data = BallByBall::where('match_id', $match_id)->where('batting_team_id', $bowling_team_id)->where('innings_completed', $current_bowling_innings)->where('over_number', '!=', -1)->orderBy('id', 'desc')->first();
                $bowling_total = isset($bowling_team_data) ? $bowling_team_data->total_score : 0;
                $bowling_total = ($bowling_total + $bowling_penalty);

                $bowling_score = "$bowling_total/$bowling_team_wickets";
                $bowling_overs = isset($bowling_team_data) ? "$bowling_team_data->total_overs/$match_data->overs" : "0.0/$match_data->overs";

                //NOTE: For DLS
                if($current_inning == 1 && isset($match_data->dls)) {
                   $dls = json_decode($match_data->dls);
                   $bowling_total = abs($dls->neededRuns - 1);
                   $match_total_overs = $dls->remainingOvers;

                   $dls->battingCurrOvers = $batting_curr_overs; 
                   $batting_curr_overs = isset($dls->currOvers) ? $dls->currOvers : '0.0';
                }

                //required_run_rate calculation
                $needed_runs = abs($bowling_total - $batting_total);
                $needed_runs = $needed_runs < 0 ? 0 : $needed_runs += 1;

                $str_batting_cur_overs = explode('.', (string) $batting_curr_overs);
                $int_part = (int) $str_batting_cur_overs[0];
                $float_part = isset($str_batting_cur_overs[1]) ? (int) $str_batting_cur_overs[1] : 0;

                // Convert the total match overs to total balls, accounting for fractional overs
                $match_total_overs = explode('.', $match_total_overs);
                $match_total_overs_int = (int) $match_total_overs[0]; // Integer part of total overs
                $match_total_overs_float = isset($match_total_overs[1]) ? (int) $match_total_overs[1] : 0; // Fractional part of total overs

                // Total balls based on match overs, considering fractional part
                $total_balls = ($match_total_overs_int * 6) + ($match_total_overs_float);

                // Calculate balls already played
                $balls_played = ($int_part * 6) + $float_part;

                // Remaining balls
                $remaining_balls =  $total_balls - $balls_played;


                $remaning_overs = ($remaining_balls) / 6;
                $remaning_overs = (float) $remaning_overs <= 0 ? 1 : (float) $remaning_overs;

                $required_run_rate = (float)($needed_runs)/($remaning_overs);
                $required_run_rate = number_format($required_run_rate, 2, '.');

                if($current_inning == 1 && isset($match_data->dls)) {
                    $balls_played = (float) $dls->battingCurrOvers === (float) $dls->battingPrevOvers ? $balls_played : ($balls_played + 1);
                    $curr_overs = floor($balls_played / 6) . "." . floor($balls_played % 6);
                    $dls->currOvers = $curr_overs;
                    $dls->battingPrevOvers = $dls->battingCurrOvers;
                    $match_data->dls = json_encode($dls);
                    $match_data->save();
                }
                if(isset($is_match_completed) &&  $is_match_completed !== true) {
                    $match_status_text = "$batting_team_name need $needed_runs runs in $remaining_balls balls";
                }else {
                    $match_status_text = $match_data->match_details;
                }
            }else if(!isset($bowling_team_initial) && $bowling_penalty > 0) {
                $bowling_score = "$bowling_penalty/0";
            }

            $is_match_completed = MatchScore::where('match_id', $match_id)->orderBy('id', 'desc')->pluck('is_winning')->first();

            if(isset($is_match_completed) && (int) $is_match_completed !== 0) {
            // if(isset($match_data) && isset($match_data->match_details)) {
                $match_status_text = $match_data->match_details;
                $required_run_rate = "0.00" ; //NOTE: The current & below value should be like this...
                $current_run_rate = "";
            }
          
            //NOTE: another logic matches without superOver
            $is_match_completed = $match_data->status === 'Completed';
            $is_batting_team_score_exist = MatchScore::where('match_id', $match_id)->where('team_id', $batting_team_id)->first();
            $is_bowling_team_score_exist = MatchScore::where('match_id', $match_id)->where('team_id', $bowling_team_id)->first();
            if($is_match_completed && isset($is_batting_team_score_exist) && isset($is_bowling_team_score_exist)) {
                $batting_score = "$is_batting_team_score_exist->total_runs/$is_batting_team_score_exist->total_wickets";
                $bowling_score = "$is_bowling_team_score_exist->total_runs/$is_bowling_team_score_exist->total_wickets";

                $batting_overs = explode('.',(string) $is_batting_team_score_exist->overs_faced);
                $bowling_overs = explode('.',(string) $is_bowling_team_score_exist->overs_faced);

                $batting_f_overs = $batting_overs[0];
                $batting_l_overs = round((float)("0.$batting_overs[1]") * 6);


                $bowling_f_overs = $bowling_overs[0];
                $bowling_l_overs = round((float)("0.$bowling_overs[1]") * 6);

                $batting_overs = $batting_f_overs . "." . $batting_l_overs;
                $bowling_overs = $bowling_f_overs . "." . $bowling_l_overs;


                $batting_overs = "$batting_overs/$match_data->overs";
                $bowling_overs = "$bowling_overs/$match_data->overs";
            }


            $over = $is_data_there->total_overs ?? '0.0';
            $over = "$over/$match_data->overs";

            return ['batting_team_id'=> $batting_team_id,
            'bowling_team_id' => $bowling_team_id,
            'batting_score' => $batting_score,
            'bowling_score' => $bowling_score,
            'batting_initial' => $batting_team_initial,
            'bowling_initial' => $bowling_team_initial,
            'curr_over' => $over,
            'batting_team_overs' => $batting_overs,
            'bowling_team_overs' => $bowling_overs,
            'is_data_there' => $is_data_there,
            'match_id' => $match_id,
            'schedule_match_id' => $match_data->schedule_match_id,
            'curr_run_rate' => $current_run_rate,
            'required_run_rate' => $required_run_rate,
            'current_inning' => $current_inning,
            'match_status_text' => $match_status_text,
            'match_total_overs' => $match_total_overs,
            'batting_curr_overs' => $batting_curr_overs,
            'dls' => $match_data->dls,
            ];
        }

        //Penalty
        $batting_penalty = Penalty::where('match_id', $match_id)->where('team_id', $match_data->batting)
        ->where('inning', 0)->sum('runs') ?? 0;

        $bowling_penalty = Penalty::where('match_id', $match_id)->where('team_id', $match_data->bowling)
        ->where('inning', 0)->sum('runs') ?? 0;

        return ['batting_team_id'=> $match_data->batting,
        'bowling_team_id' => $match_data->bowling,
        'batting_score' => $batting_penalty > 0 ? "{$batting_penalty}/0" : "Yet To Bat",
        'bowling_score' => $bowling_penalty > 0 ? "{$bowling_penalty}/0" : "Yet To Bat",
        'is_data_there' => $is_data_there,
        'curr_over' => "0.0/0",
        'batting_team_overs' => "0.0/0",
        'bowling_team_overs' => "0.0/0",
        'match_id' => $match_id,
        'schedule_match_id' => isset($match_data) ? $match_data->schedule_match_id : null,
        'curr_run_rate' => "",
        'required_run_rate' => "0.00",
        'need_runs' => 0,
        'remaning_overs' => 0,
        'current_inning' => 0,
        'match_status_text' => '',
        ];
    }
    public function fetchSummary($match_id) {
        $match_id = (int) $match_id;
        $match_players = MatchPlayer::where('match_id', $match_id)->orderBy('id','desc')->first();
        $match_data = MatchGame::where('matches.id', $match_id)
        ->join('teams as batting_team', 'batting_team.id', '=', 'matches.batting')
        ->join('teams as bowling_team', 'bowling_team.id', '=', 'matches.bowling')
        ->select('matches.*', 'batting_team.name as batting_team_name', 'bowling_team.name as bowling_team_name')
        ->first();
        $ball_by_ball = BallByBall::where('match_id', $match_id)->where('over_number','!=', -1)->orderBy('id', 'DESC')->first();

        $batting_team_id = $ball_by_ball ? $ball_by_ball->batting_team_id: null;
        $bowling_team_id = $ball_by_ball ? $ball_by_ball->bowling_team_id: null;
        $batting_team_name = $match_data ? $match_data->batting_team_name: null;
        $bowling_team_name = $match_data ? $match_data->bowling_team_name: null;
        $innings = $ball_by_ball ? $ball_by_ball->innings_completed: null;
        $curr_over_number = $ball_by_ball ? $ball_by_ball->over_number : null;
        $next_over_number = isset($curr_over_number) ? $curr_over_number + 1 : null;
        $curr_ball_data = $ball_by_ball ? BallByBall::where('match_id', $match_id)
        ->where('batting_team_id', $batting_team_id)
        ->where('over_number', '!=', -1)
        ->where('innings_completed', $innings)
        ->where('over_number', 'LIKE', "$curr_over_number")
        // ->whereRaw('over_number = ? AND is_over_completed = 0', [$curr_over_number])
        // ->orWhereRaw('over_number = ? AND is_over_completed = 1', [$next_over_number])
        ->orderBy('id', 'DESC')->first() : null;

        if(isset($match_players) && isset($curr_ball_data)) {
            $curr_over_data = BallByBall::where('match_id', $match_id)
            ->where('batting_team_id', $batting_team_id)
            ->where('over_number', '!=', -1)
            ->where('innings_completed', $innings)
            ->where('over_number', $curr_over_number)
            // ->whereRaw('over_number = ? AND is_over_completed = 0', [$curr_over_number])
            // ->orWhereRaw('over_number = ? AND is_over_completed = 1', [$next_over_number])
            ->get()->toArray();

            $total = "0/0";

            // $over = $curr_over_number == $next_over_number ? ($curr_over_number - 1) : $curr_over_number;
            // $over = $curr_over_number == 0 ? $curr_ball_data->ball_number :$curr_over_number + ($curr_ball_data->valid_ball_count / 10);
            $over = "$ball_by_ball->total_overs";
            $runs = $curr_ball_data->total_runs;
            $wicket = BallByBall::where('match_id', $match_id)->where('is_wicket', 1)->where('innings_completed', $innings)->get()->count();
            $initial = BallByBall::where('match_id', $match_id)->first();
            $final = BallByBall::where('match_id', $match_id)->orderBy('id', 'DESC')->first();
            if(isset($initial)) {
                // $total = BallByBall::where('match_id', $match_id)->whereBetween('id', [$initial->id, $final->id])->sum('total_score');
                $total = BallByBall::where('match_id', $match_id)->orderBy('id', 'desc')->pluck('total_score')->first();
                $total = "$total/$wicket";
            }
            $striker_id = $curr_ball_data->striker_id;
            $non_striker_id = $curr_ball_data->non_striker_id;
            $bowler_id = $curr_ball_data->bowler_id;

            $wickets_in_over = 0;
            $runs_in_over = 0;
            $balls = array();
            $commentary_runs = $runs;
            foreach($curr_over_data as $data) {
                    $runs_in_over += $data['total_runs'];
                    $wickets_in_over += ($data['is_wicket'] != 1 ? 0 : 1);

                    // if($data['is_one']) {
                    //     $balls[] = "1";
                    // }else if($data['is_two']) {
                    //     $balls[] = "2";
                    // }else if($data['is_three']) {
                    //     $balls[] = "3";
                    // }else if($data['is_four']){
                    //     $balls[] = "4";
                    // }else if($data['is_six']) {
                    //     $balls[] = "6";
                    // }else if($data['is_wicket']) {
                        $commentary_runs = $data['display_run'];
                        $striker_id = $data['dismissed_batsmen'];
                        $balls[] = $data['display_run'];
                    // }else {
                    //     $balls[] = "0";
                    // }
            }


            $commentary_text_data = Commentary::where('ball_by_ball_id', $curr_ball_data->id)->where('match_id', $match_id)->orderBy('id', 'desc')->first();
            $commentary_data = new stdClass();
            $commentary_data->over = $over;
            // $commentary_data->text = $this->commentaryText($commentary_runs, $striker_id, $non_striker_id, $bowler_id);
            $commentary_data->ballByBallId = isset($commentary_text_data) ? $commentary_text_data->ball_by_ball_id : 0;
            $commentary_data->text = isset($commentary_text_data) ? $commentary_text_data->commentary_text : '';
            $commentary_data->runs = $runs;
            $commentary_data->total = $total;

            $summary_data = new stdClass();
            $summary_data->over = $curr_over_number;
            $summary_data->wickets = $wickets_in_over;
            $summary_data->runs = $runs_in_over;
            $summary_data->balls = $balls;
            $summary_data->inning = $innings;

            return ([
                'match_id' => $match_id,
                'overSummary' => $summary_data,
                'commentary' => $commentary_data,
                'curr_over' => $curr_over_number,
                'next_over' => $next_over_number,
                'innings' => $innings,
                'battingTeamId' => $batting_team_id,
                'bowlingTeamId' => $bowling_team_id,
                'battingTeamName' => $batting_team_name,
                'bowlingTeamName' => $bowling_team_name,
            ]);
        }
        return ([
            'match_id' => $match_id,
            'overSummary' => new stdClass(),
            'commentary' =>  new stdClass(),
            'curr_over' => $curr_over_number,
            'next_over' => $next_over_number,
            'innings' => $innings,
            'battingTeamId' => $batting_team_id,
            'bowlingTeamId' => $bowling_team_id,
            'battingTeamName' => $batting_team_name,
            'bowlingTeamName' => $bowling_team_name,
        ]);
    }
    public function getMatchStats($matchId)
    {
        $match_players = MatchPlayer::where('match_id', $matchId)->orderBy('id','desc')->first();
        $current_inning = isset($match_players) ? $match_players->current_innings : 0;


        $stats = [
            'batting' => $this->getBattingStats($matchId),
            'bowling' => $this->getBowlingStats($matchId),
            'overByOver' => $this->getOverByOverStats($matchId, $current_inning),
            'ongoingOverDisplayRuns' => $this->getOngoingOverDisplayRuns($matchId, $current_inning),
            'getFullovers' => $this->getFullovers($matchId),
            'getStrikers' =>$this->getStrikers($matchId)
        ];

        return response()->json($stats);
    }

    private function getStrikers($matchId){

        $strikerData = DB::table('match_players')
            ->join('players', 'match_players.striker_id', '=', 'players.id')
            ->select('players.name', 'match_players.striker_id as batter_id')
            ->where('match_players.match_id', $matchId)
            ->first();

        $nonStrikerData = DB::table('match_players')
            ->join('players', 'match_players.non_striker_id', '=', 'players.id')
            ->select('players.name', 'match_players.non_striker_id as batter_id')
            ->where('match_players.match_id', $matchId)
            ->first();

       $bowler = DB::table('match_players')
            ->join('players', 'match_players.bowler_id', '=', 'players.id')
            ->select('players.name', 'match_players.bowler_id as bowler_id')
            ->where('match_players.match_id', $matchId)
            ->first();

        return [
            'strikerData' => $strikerData,
            'nonStrikerData' => $nonStrikerData,
            'bowler' => $bowler
        ];
    }

    private function getBattingStats($matchId) {
        $match_players  = MatchPlayer::where('match_id', $matchId)->orderBy('id', 'desc')->first();
        $striker_id =  isset($match_players) ? $match_players->striker_id : Null;
        $non_striker_id =  isset($match_players) ? $match_players->non_striker_id : Null;

        $striker_data =  DB::table('player_batting_stats as p1')
            ->join('players as p', 'p1.player_id', '=', 'p.id') // Join with players table
            ->select(
                'p1.player_id as batter_id',
                'p.name as name', // Get player name
                'p1.balls_faced',
                'p1.score as runs',
                'p1.four as fours',
                'p1.six as sixes',
                'p1.strike_rate'
            )
            ->where('p1.match_id', $matchId)
            ->where('p1.player_id', $striker_id)
            ->where('p1.is_out', 0)->orderBy('p1.id','DESC')->first();
        $non_striker_data =  DB::table('player_batting_stats as p1')
            ->join('players as p', 'p1.player_id', '=', 'p.id') // Join with players table
            ->select(
                'p1.player_id as batter_id',
                'p.name as name', // Get player name
                'p1.balls_faced',
                'p1.score as runs',
                'p1.four as fours',
                'p1.six as sixes',
                'p1.strike_rate'
            )
            ->where('p1.match_id', $matchId)
            ->where('p1.player_id', $non_striker_id)
            ->where('p1.is_out', 0)->orderBy('p1.id','DESC')->first();
        return [$striker_data,$non_striker_data];
    }


    private function getBowlingStats($matchId) {

        return DB::table('player_bowling_stats as p1')
            ->select(
                'p1.player_id as bowler_id',
                'p1.balls_bowled as total_balls',
                'p1.overs_bowled as overs',
                'p1.runs_conceded',
                'p1.wickets_taken as wickets',
                DB::raw('CASE WHEN p1.maiden_overs > 0 THEN 1 ELSE 0 END as maidens'),
                'p1.economy_rate as economy'
            )
            ->where('p1.match_id', $matchId)
            ->orderBy('p1.id', 'desc')
            ->limit(1)
            ->get();
    }

    private function getOverByOverStats($matchId, $current_inning){

        return DB::table('ball_by_ball as ball')
            ->where('ball.match_id', $matchId)
            ->where('ball.innings_completed', $current_inning)
            ->leftJoin('teams as batting_team', 'batting_team.id', '=', 'batting_team_id')
            ->leftJoin('teams as bowling_team', 'bowling_team.id', '=', 'bowling_team_id')
            ->select('ball.*', 'batting_team.name as batting_team_name', 'bowling_team.name as bowling_team_name')
            ->orderBy('ball.created_at' , 'desc')->first();
     }

     private function getOngoingOverDisplayRuns($matchId, $current_inning) {
        $latestOver = DB::table('ball_by_ball')
            ->where('match_id', $matchId)
            ->where('innings_completed', $current_inning)
            ->max('over_number');

        if ($latestOver === null) {
            return [];
        }

        $displayRuns = DB::table('ball_by_ball')
            ->where('match_id', $matchId)
            ->where('over_number', $latestOver)
            ->where('innings_completed', $current_inning)
            ->select('display_run')
            ->orderBy('total_overs', 'asc')
            ->get();

        return $displayRuns->pluck('display_run')->map(function($run) {
            return $run;
        })->toArray();
    }


    private function getFullovers($matchId) {

        $match = MatchGame::find($matchId);
         return $match->overs;
    }

     public function getMatchStatus(Request $request, $id) {
       $matchState = $request->input('status');

        if ($matchState == 'active') {
            $match = MatchGame::find($id);
        } else {
            $match = ScheduleMatch::find($id);
        }

    return response()->json([
        '$matchState' => $matchState,
        'MatchStatus' => $match->status,
    ]);
}




    public function getBatsMen($match_id) {
    $match_players = MatchPlayer::where('match_id', $match_id)->orderBy('id', 'desc')->first();
      if(isset($match_players)) {
            $striker_id = $match_players->striker_id;
            $non_striker_id = $match_players->non_striker_id;

            $striker_batting_status = PlayerBattingStats::
            where('player_batting_stats.player_id', $striker_id)
			->where('player_batting_stats.match_id', $match_id)
            ->where('player_batting_stats.is_out', 0)->join('players', 'players.id', '=', 'player_batting_stats.player_id')
            ->select( 'player_batting_stats.*', 'players.name as name',)->get()->toArray();

            $non_striker_batting_status = PlayerBattingStats::
            where('player_batting_stats.player_id', $non_striker_id)
			->where('player_batting_stats.match_id', $match_id)
            ->where('player_batting_stats.is_out', 0)->join('players', 'players.id', '=', 'player_batting_stats.player_id')
            ->select( 'player_batting_stats.*', 'players.name as name',)->get()->toArray();

            if(!count($striker_batting_status)) {
                $striker = Player::where('id', $striker_id)->first();

                $striker_batting_status = new stdClass();
                $striker_batting_status->name = $striker->name;
                $striker_batting_status->four = 0;
                $striker_batting_status->six = 0;
                $striker_batting_status->score = 0;
                $striker_batting_status->balls_faced = 0;
                $striker_batting_status->strike_rate = 0.00;
                $striker_batting_status->player_id = $striker_id;

                $striker_batting_status = array($striker_batting_status);
            }

            if(!count($non_striker_batting_status)) {
                $non_striker = Player::where('id', $non_striker_id)->first();

                $non_striker_batting_status = new stdClass();
                $non_striker_batting_status->name = $non_striker->name;
                $non_striker_batting_status->four = 0;
                $non_striker_batting_status->six = 0;
                $non_striker_batting_status->score = 0;
                $non_striker_batting_status->balls_faced = 0;
                $non_striker_batting_status->strike_rate = 0.00;
                $non_striker_batting_status->player_id = $non_striker_id;

                $non_striker_batting_status = array($non_striker_batting_status);
            }

            $batting_status = array_merge(($striker_batting_status), ($non_striker_batting_status));
            $batsMen = array();

            foreach($batting_status as $status) {
                $status = (object) $status;
                $player_id = $status->player_id;
                $name = $status->name;
                $four = $status->four;
                $six = $status->six;
                $score = $status->score;
                $balls_faced = $status->balls_faced;
                $strike_rate = $status->strike_rate;

                $is_ball_by_ball_exist = BallByBall::where('match_id', $match_id)->where('over_number', "!=", -1)->orderBy('id', 'DESC')->first();
                $striker_id = null;
                if(isset($is_ball_by_ball_exist)){
                    $striker_id = MatchPlayer::where('match_id', $match_id)->where('current_innings', $is_ball_by_ball_exist->innings_completed)->orderBy('id', 'desc')->pluck('striker_id')->first();
                }
                if(!isset($batsMen["$player_id"])) {
                    $player_data = new stdClass();
                    $player_data->name = $name;
                    $player_data->fours = $four ? 1 :  0;
                    $player_data->sixes = $six ? 1 : 0;
                    $player_data->runs = $score;
                    $player_data->balls = $balls_faced;
                    $player_data->strikeRate = $strike_rate;
                    $player_data->is_striker = $player_id == $striker_id;



                    $batsMen[$player_id] = $player_data;
                }else {
                    $batsMen["$player_id"]->fours = $four ? $batsMen["$player_id"]->fours += 1: $batsMen["$player_id"]->fours;
                    $batsMen["$player_id"]->sixes = $six ? $batsMen["$player_id"]->sixes += 1 : $batsMen["$player_id"]->sixes;
                    $batsMen["$player_id"]->runs = $score;
                    $batsMen["$player_id"]->balls = $balls_faced;
                    $batsMen["$player_id"]->strikeRate = $strike_rate;
                }
            }
            return array_values($batsMen);
        }
        $batsMen1 = new stdClass();
        $batsMen1->name = 'Striker';
        $batsMen1->runs = '0';
        $batsMen1->balls = '0';
        $batsMen1->fours = '0';
        $batsMen1->sixes = '0';
        $batsMen1->strikeRate = '0.00';

        $batsMen2 = new stdClass();
        $batsMen2->name = 'Non-striker';
        $batsMen2->runs = '0';
        $batsMen2->balls = '0';
        $batsMen2->fours = '0';
        $batsMen2->sixes = '0';
        $batsMen2->strikeRate = '0.00';

        return [$batsMen1, $batsMen2];
    }

    public function getPresentMatchData($matchId) {
        $match_id = $matchId;
        $match_players = MatchPlayer::where('match_id', $match_id)->orderBy('id', 'desc')->first();

        $batsMen = $this->getBatsMen($match_id);
        $bowler = $this->getMatchBowler($match_id);
        // if(isset($match_players)) {
            $over_summary = [];
            $over_summary = $this->fetchSummary($match_id);
            $undoData = $this->getUndoData($match_id);

            return ([
                'matchId' => $match_id,
                'batsMen' => array_values($batsMen),
                'bowler' => $bowler,
                'summary' => $over_summary,
                'status' => isset($match_players) ? 'found' : 'not found',
                'undoData' => $undoData,
            ]);
        // }
        // return ([
        //     'matchId' => $match_id,
        //     'batsMen' => $batsMen,
        //     'bowler' => $bowler,
        //     'summary' => ['overSummary' => array(), 'commentary' => array()],
        // ]);

    }
    public function oldScoreBoardUptoBatsMen($match_id){
        $is_stats_exist = BallByBall::where('match_id', $match_id)->where('over_number', '!=', -1)->orderBy('id', 'DESC')->first();
        $match_players = MatchPlayer::where('match_id', $match_id)->orderBy('id', 'desc')->first();
        $stats = new stdClass();
        $stats->firstInningsBatsMen = array();
        $stats->secondInningsBatsMen = array();
        $stats->superOverFirstInningsBatsMen = array();
        $stats->superOverSecondInningsBatsMen = array();
        $stats->secondSuperOverFirstInningsBatsMen = array();
        $stats->secondSuperOverSecondInningsBatsMen = array();
        if(!isset($is_stats_exist) && isset($match_players)) {
            $i = $match_players->current_innings ?? 0;
            $batting_team_batted_players = [$match_players->striker_id, $match_players->non_striker_id];
            foreach($batting_team_batted_players as $player_id) {
                $player_name = Player::where('id', $player_id)->pluck('name')->first();
                $player_stats = BallByBall::where('ball_by_ball.match_id', $match_id)->where('ball_by_ball.innings_completed', $i)
                ->where('ball_by_ball.over_number', '!=',-1)->where('ball_by_ball.striker_id', $player_id)->orderBy('ball_by_ball.id', 'DESC')
                ->leftJoin('player_batting_stats as player_stats', function ($query) use ($player_id){
                   // $query->on('player_stats.ball_by_ball_id', '=', 'ball_by_ball.id')
                   // ->where('player_stats.player_id', '=', $player_id);
                  $query->on('player_stats.player_id', '=', 'ball_by_ball.striker_id')
                  ->orderBy('player_stats.id', 'desc');
                })
                ->select('player_stats.*')
                ->first();
                $player_data = new stdClass();
                $player_data->id = $player_id;
                $player_data->name = $player_name;
                $player_data->runs = 0;
                $player_data->balls = 0;
                $player_data->strikeRate = 0;
                $player_data->fours = 0;
                $player_data->sixes = 0;
                $player_data->dismissalType = null;
                $player_data->is_striker = $match_players->striker_id == $player_id;
                if(isset($player_stats)) {
                    $player_data->runs = $player_stats->score;
                    $player_data->balls = $player_stats->balls_faced;
                    $player_data->strikeRate = $player_stats->strike_rate;
                    $player_data->fours = PlayerBattingStats::where('match_id', $match_id)->where('player_id', $player_id)->where('four', 1)->pluck('four')->count();
                    $player_data->sixes = PlayerBattingStats::where('match_id', $match_id)->where('player_id', $player_id)->where('six', 1)->pluck('four')->count();
                }
                $players_data[] = $player_data;
            }
            if($i == 0) {
                $stats->firstInningsBatsMen = $players_data;
            }
            if($i == 1){
                $stats->secondInningsBatsMen = $players_data;
            }
            if($i == 2) {
                $stats->superOverFirstInningsBatsMen = $players_data;
            }
            if($i == 3){
                $stats->superOverSecondInningsBatsMen = $players_data;
            }
            if($i == 4) {
                $stats->secondSuperOverFirstInningsBatsMen = $players_data;
            }
            if($i == 5){
                $stats->secondSuperOverSecondInningsBatsMen = $players_data;
            }
            return $stats;
            // exit();
        }
        if(isset($is_stats_exist) && isset($match_players)){
            // $total_innings = $is_stats_exist->over_number == -1 ? $is_stats_exist->innings_completed - 1 : $is_stats_exist->innings_completed;
            $total_innings = $is_stats_exist->innings_completed;
            for($i = 0; $i <= $total_innings; $i++){
                $team = BallByBall::where('match_id', $match_id)->where('innings_completed', $i)->where('over_number', '!=',-1)->orderBy('id', 'asc')->first();
                $match_players = MatchPlayer::where('match_id', $match_id)->where('current_innings', $i)->orderBy('id', 'desc')->first();
                $batting_team_id = $team->batting_team_id;

                $dismissed_batsmen = BallByBall::select('dismissed_batsmen')
                ->where('match_id', $match_id)
                ->where('batting_team_id', $batting_team_id)
                ->where('innings_completed', $i)
                ->where('over_number', '!=', -1)
                ->where('is_wicket', 1)
                ->where('dismissed_batsmen', '!=', null)
                ->groupBy('dismissed_batsmen')
                ->pluck('dismissed_batsmen')
                ->toArray();

                $batting_team_strikers = BallByBall::select('striker_id')
                ->where('match_id', $match_id)
                ->where('batting_team_id', $batting_team_id)
                ->where('innings_completed', $i)
                ->where('over_number', '!=', -1)
                ->whereNotIn('striker_id', $dismissed_batsmen)
                ->where('is_wicket', '!=', 1)
                ->groupBy('striker_id')
                ->pluck('striker_id')
                ->toArray();

                $batting_team_non_strikers = BallByBall::select('non_striker_id')
                ->where('match_id', $match_id)
                ->where('batting_team_id', $batting_team_id)
                ->where('innings_completed', $i)
                ->where('over_number', '!=', -1)
                ->whereNotIn('non_striker_id', $dismissed_batsmen)
                ->whereNotIn('non_striker_id', $batting_team_strikers)
                ->where('is_wicket', '!=', 1)
                ->groupBy('non_striker_id')
                ->pluck('non_striker_id')
                ->toArray();

                $match_player_batsmen = isset($match_players) ? [$match_players->striker_id, $match_players->non_striker_id] : [];
                $batting_team_batted_players = array_merge($dismissed_batsmen,$batting_team_strikers, $batting_team_non_strikers, $match_player_batsmen);
                $batting_team_batted_players = array_unique($batting_team_batted_players);
                // return [
                //     'dismissed_batsmen' => $dismissed_batsmen,
                //     'strikers' => $batting_team_strikers,
                //     'non_strikers' => $batting_team_non_strikers,
                //     'merged' => $batting_team_batted_players,
                // ];
                $players_data = array();
                foreach($batting_team_batted_players as $player_id) {
                    $player_name = Player::where('id', $player_id)->pluck('name')->first();

                 	$player_stats =  $player_stats = BallByBall::where('ball_by_ball.match_id', $match_id)->where('ball_by_ball.innings_completed', $i)->where('ball_by_ball.over_number', '!=',-1)->where('ball_by_ball.striker_id', $player_id)
                    ->where('batting_team_id', $batting_team_id)
                    ->orderBy('ball_by_ball.id', 'DESC')->first();

                  	// if(isset($player_stats)) {
                      $player_stats =  PlayerBattingStats::where('player_batting_stats.player_id', $player_id)
                       ->orderBy('player_batting_stats.id', 'desc')
                       //->leftJoin('players as fielder_details', 'ball_by_ball.fielder_id', '=', 'fielder_details.id')
                       ->select('player_batting_stats.*', 'player_batting_stats.ball_by_ball_id as ball_by_ball_id')
                       ->first();
                    // }

                    $player_ball_by_ball_id = BallByBall::where('match_id', $match_id)
                    ->where('innings_completed', $i)
                    ->where('over_number', '!=', -1)
                    ->where('batting_team_id', $batting_team_id)
                    ->where('striker_id', $player_id)
                    ->orWhere('non_striker_id', $player_id)
                    ->orWhere('dismissed_batsmen', $player_id)
                    ->orderBy('id', 'asc')->first();

                    $is_player_out = BallByBall::where('ball_by_ball.match_id', $match_id)->where('ball_by_ball.innings_completed', $i)->where('ball_by_ball.over_number', '!=',-1)
                    ->where('ball_by_ball.is_wicket', 1)
                    ->where('ball_by_ball.dismissed_batsmen', $player_id)
                    ->where('batting_team_id', $batting_team_id)
                    ->orderBy('ball_by_ball.id', 'asc')
                    ->leftJoin('player_fielding_stats as fielder_stats', 'fielder_stats.ball_by_ball_id', '=', 'ball_by_ball.id')
                    ->leftJoin('players as bowler_details', 'ball_by_ball.bowler_id', '=', 'bowler_details.id')
                    ->leftJoin('players as fielder_details', 'ball_by_ball.fielder_id', '=', 'fielder_details.id')
                     ->select(
                    'fielder_stats.player_id',
                    'fielder_stats.catches',
                    'fielder_stats.run_outs',
                    'fielder_stats.stumpings',
                    'fielder_stats.bowled',
                    'fielder_stats.direct_hit',
                    'fielder_stats.throwing_end_id',
                    'fielder_stats.fielding_caught_behind',
                    'fielder_stats.fielding_caught_and_bowled',
                    'fielder_stats.retired_hurt',
                    'fielder_stats.fielding_caught_and_bowled',
                    'fielder_stats.fielding_mankaded',
                    'fielder_stats.hit_wicket',
                    'fielder_stats.retired_out',
                    'ball_by_ball.fielder_id as fielder_id',
                    'ball_by_ball.bowler_id as bowler_id',
                    'bowler_details.name as bowlerName',
                    'fielder_details.name as fielderName',
                    )
                    ->first();

                    $player_data = new stdClass();
                    $player_data->ballByBallId = isset($player_ball_by_ball_id) ? $player_ball_by_ball_id->id : PHP_INT_MAX;
                    $player_data->id = $player_id;
                    $player_data->name = $player_name;
                    $player_data->runs = 0;
                    $player_data->balls = 0;
                    $player_data->strikeRate = 0;
                    $player_data->fours = 0;
                    $player_data->sixes = 0;
                    $player_data->dismissalType = null;
                    $player_data->bowlerName = null;
                    $player_data->fielderName = null;
                    $player_data->is_striker = isset($match_players) ? $match_players->striker_id == $player_id : false;
                    if(isset($is_player_out)) {
                        $is_player_out = $is_player_out->toArray();
                        // $dismissal_type = 'out';
                        $dismissal_type = 'bowled';

                        $bowler_name = $is_player_out['bowlerName'];
                        $fielder_name = $is_player_out['fielderName'];
                        foreach($is_player_out as $key => $value){
                           if($key == 'fielder_id' || $key == 'bowler_id') continue;
                           if(isset($value) && isset($is_player_out['fielder_id']) && $value == $is_player_out['fielder_id']) {
                            $dismissal_type = ($key == 'player_id') ? 'bowled': str_replace("_", " ", $key);
                           }
                        }
                        $player_data->dismissalType = $dismissal_type;
                        $player_data->bowlerName = $bowler_name;
                        $player_data->fielderName = $fielder_name;
                    }
                    if(isset($player_stats)) {
                        $player_data->ballByBallId = $player_data->ballByBallId < $player_stats->ball_by_ball_id ? $player_data->ballByBallId : $player_stats->ball_by_ball_id;
                        $player_data->runs = $player_stats->score;
                        $player_data->balls = $player_stats->balls_faced;
                        $player_data->strikeRate = $player_stats->strike_rate;
                        $player_data->fours = PlayerBattingStats::where('match_id', $match_id)->where('player_id', $player_id)->where('four', 1)->pluck('four')->count();
                        $player_data->sixes = PlayerBattingStats::where('match_id', $match_id)->where('player_id', $player_id)->where('six', 1)->pluck('four')->count();
                    }
                    $players_data[] = $player_data;
                }

                uasort($players_data, function ($a, $b) {
                    if($a->ballByBallId == $b->ballByBallId) return 0;
                    return ($a->ballByBallId < $b->ballByBallId) ? -1 : 1;
                });

                $players_data = array_values($players_data);
                if($i == 0) {
                    $stats->firstInningsBatsMen = $players_data;
                }
                if($i == 1){
                    $stats->secondInningsBatsMen = $players_data;
                }
                if($i == 2) {
                    $stats->superOverFirstInningsBatsMen = $players_data;
                }
                if($i == 3){
                    $stats->superOverSecondInningsBatsMen = $players_data;
                }
                if($i == 4) {
                    $stats->secondSuperOverFirstInningsBatsMen = $players_data;
                }
                if($i == 5){
                    $stats->secondSuperOverSecondInningsBatsMen = $players_data;
                }
            }
        }
        return  $stats;
    }
    public function scoreBoardUptoData($match_id) {
        return response()->json([
            'batting_data' => $this->scoreBoardUptoBatsMen($match_id),
            'bowler_data' => $this->scoreBoardUptoBowlersForBattingTeam($match_id),
            'fall_of_wicket' => $this->scoreBoardUptoFallOfWickets($match_id),
            'team_details' => $this->scoreBoardUptoTeamDetails($match_id),
        ]);
    }
    public function apiTest() {
        $match_id = 17;
        $batting_team_id = 2;
        $inning = 0;
        // $score_board_ctrl = new ScoreBoardController($match_id, $batting_team_id, $inning);
        // $batsMen = $this->scoreBoardUptoBatsMen($match_id);
        // $bowlers = $this->scoreBoardUptoBowlersForBattingTeam($match_id);
        // $fallOfWickets = $this->scoreBoardPresentBowlers($match_id);
        // $data = $this->scoreBoardUptoData($match_id);
        // $batsMen = $this->getTeamScore($match_id);
        // $undoData = $this->getUndoData($match_id);
        // $summary = $this->fetchOverSummary($match_id);
        // $matchFixtures = $this->MatchFixtures();
        // $check_match_status = $this->checkMatchStatus($match_id);
        // $fall_of_wickets = $this->scoreBoardUptoFallOfWickets($match_id);
        // $presentMatchData = $this->getPresentMatchData(14);
        // $getFallOfWickets = $score_board_ctrl->getFallOfWickets($match_id, $batting_team_id, $inning);
        // // $getScoreBoard = $score_board_ctrl->getScoreBoardBatsMen($match_id, $batting_team_id, $inning);
        $last_ball_data = new ScoringController();
        $bowlerStats = $last_ball_data->getallbowlersstats($match_id);
        return response()->json([
            'status' => 'working',
            // 'data' => $data,
            // 'batsMen' => $batsMen,
            // 'undoData' => $undoData,
            // 'summary' => $summary,
            // 'fixtures' => $matchFixtures,
            // 'batsMen' => $batsMen,
            // 'match_statuts' => $check_match_status,
            // 'fall_of_wickets' => $fall_of_wickets,
            // 'present Match Data' => $presentMatchData,
            // 'fow' => $getFallOfWickets,
            // 'scoreBoardBatsmen' => $getScoreBoard,
            // 'last_ball_data' => $last_ball_data->deleteLastBall($match_id),
            'bowler_stats' => $bowlerStats,

        ]);
    }
    public function createSummary($innings, $initial_over, $total_over, $team, $match_id) {
        $commentary = array();
        $over_summary = array();
        for($i = $initial_over; $i <= $total_over; $i++) {
        //   $converted_over_number = ($i * 1.0) + 0.1; // Start of the range
        //   $converted_end_over_number = ($i * 1.0) + 1.0; // End of the range
             $over_data = BallByBall::where('match_id', $match_id)
            ->where('batting_team_id', $team)
            ->where('over_number', '!=', -1)
            ->where('innings_completed', $innings)
            // ->whereBetween('over_number', [$converted_over_number, $converted_end_over_number])
            ->where('over_number', $i)
            ->get()->toArray();

            $over_data = array_values($over_data);
            if(!count($over_data)){
                //It's for to stop the loop if the current over haven't started.
                break;
            }
            uasort(($over_data), function ($a, $b) {
                    if($a['total_overs'] == $b['total_overs']) return 0;
                    return ($a['total_overs'] < $b['total_overs']) ? -1 : 1;
            });
            $wickets_in_over = 0;
            $runs_in_over = 0;
            $balls = array();
            foreach($over_data as $data) {
                // if($data['innings_completed'] <= 0) {
                    $runs_in_over += $data['total_runs'];
                    $wickets_in_over += ($data['is_wicket'] != 1 ? 0 : 1);
                    $commentary_runs = null;

                    // if($data['is_one']) {
                    //     $balls[] = "1";
                    // }else if($data['is_two']) {
                    //     $balls[] = "2";
                    // }else if($data['is_three']) {
                    //     $balls[] = "3";
                    // }else if($data['is_four']){
                    //     $balls[] = "4";
                    // }else if($data['is_six']) {
                    //     $balls[] = "6";
                    // }else if($data['is_wicket']) {
                        $commentary_runs = $data['display_run'];
                        $balls[] = $data['display_run'];
                    // }else {
                    //     $balls[] = "0";
                    // }

                    // $over = $i + ($data['valid_ball_count'] / 10);
                    $over = $data['total_overs'];
                    $runs = $data['total_runs'];

                    $is_match_stats_there = BallByBall::where('match_id', $match_id)->where('batting_team_id', $team)->where('innings_completed', $innings)->orderBy('id', 'asc')->first();
                    $total = isset($is_match_stats_there) ? BallByBall::where('match_id', $match_id)->where('batting_team_id', $team)->where('innings_completed', $innings)->where('id', $data['id'])->pluck('total_score')->first() : '0';

                    $wicket = BallByBall::where('match_id', $match_id)->where('batting_team_id', $team)->where('innings_completed', $innings)->whereBetween('id', [$is_match_stats_there->id, $data['id']])->where('is_wicket', 1)->get()->count();

                    $total = "$total/$wicket";
                    $striker_id = isset($commentary_runs) ? $data['dismissed_batsmen'] : $data['striker_id'];
                    $non_striker_id = $data['non_striker_id'];
                    $bowler_id = $data['bowler_id'];
                    $commentary_runs = isset($commentary_runs) ? $commentary_runs : $runs;

                    $commentary_text_data = Commentary::where('ball_by_ball_id', $data['id'])->where('match_id', $match_id)->orderBy('id', 'desc')->first();
                    $commentary_data = new stdClass();
                    $commentary_data->over = "$over";
                    // $commentary_data->text = $this->commentaryText($commentary_runs, $striker_id, $non_striker_id, $bowler_id);
                    $commentary_data->ballByBallId = isset($commentary_text_data) ? $commentary_text_data->ball_by_ball_id : 0;
                    $commentary_data->text = isset($commentary_text_data) ? $commentary_text_data->commentary_text : '';
                    $commentary_data->runs = "$runs";
                    $commentary_data->total = "$total";

                    $commentary[] = $commentary_data;
                }

                $summary_data = new stdClass();
                $summary_data->over = $i;
                $summary_data->wickets = $wickets_in_over;
                $summary_data->runs = $runs_in_over;
                $summary_data->balls = $balls;
                $summary_data->inning = $innings;

                $over_summary[] = $summary_data;

                if(count($over_data) < 6) { //it's for to stop the loop if the over haven't completed
                    continue;
                }
            // }
        }
        return [
            'over_summary' => $over_summary,
            'commentary' => $commentary,
            'inning' => $innings
        ];
    }
    public function getInningStatus($match_id, $inning) {
        $ball_by_ball = BallByBall::where('match_id', $match_id)->where('innings_completed', $inning)->where('over_number', -1)->first();
        if(isset($ball_by_ball)){
            $batting_completed_team_id = $ball_by_ball->batting_team_id;
            return response()->json([
                'status' => 'found',
                'battingTeam' => $batting_completed_team_id,
                'completedInning' => $inning,
            ]);
        }
        return response()->json([
            'status' => 'not found',
            'battingTeam' => null,
            'completedInning' => $inning,
        ]);
    }
    public function oldScoreBoardUptoBowlersForBattingTeam($match_id) {
        $is_stats_exist = BallByBall::where('match_id', $match_id)->where('over_number', '!=', -1)->orderBy('id', 'DESC')->first();
        $match_players = MatchPlayer::where('match_id', $match_id)->orderBy('id', 'desc')->first();
        $stats = new stdClass();
        $stats->firstInningsBowlers = array();
        $stats->secondInningsBowlers = array();
        $stats->superOverFirstInningsBowlers = array();
        $stats->superOverSecondInningsBowlers = array();
        $stats->secondSuperOverFirstInningsBowlers = array();
        $stats->secondSuperOverSecondInningsBowlers = array();

        if(!isset($is_stats_exist) && isset($match_players)) {
            $i = $match_players->current_innings ?? 0;
            // $team = BallByBall::where('match_id', $match_id)->where('innings_completed', $i)->where('over_number', '!=',-1)->orderBy('id', 'ASC')->first();
            $match_data = MatchGame::where('matches.id', $match_id)
            ->leftJoin('teams', 'teams.id', '=', 'matches.batting')
            ->select('teams.name as name','matches.overs as overs')
            ->orderBy('matches.id', 'ASC')
            ->first();
            $bowling_team_id = $match_data->bowling;
            $player_id = $match_players->bowler_id;
            $player_name = Player::where('id', $player_id)->pluck('name')->first();
            $player_stats = BallByBall::where('ball_by_ball.match_id', $match_id)->where('ball_by_ball.innings_completed', $i)->where('ball_by_ball.over_number', '!=',-1)->where('ball_by_ball.bowler_id', $player_id)->orderBy('ball_by_ball.id', 'DESC')
            ->leftJoin('player_bowling_stats as player_stats', 'player_stats.ball_by_ball_id', '=', 'ball_by_ball.id')
            ->select('player_stats.*')
            ->first();
            $player_data = new stdClass();
            $player_data->name = $player_name;
            $player_data->id = $player_id;
            $player_data->overs = 0.0;
            $player_data->maidens = 0;
            $player_data->runs = 0;
            $player_data->wickets = 0;
            $player_data->economy = 00.0;
            if(isset($player_stats)) {
                $player_data->overs = $player_stats->overs_bowled;
                $player_data->maidens =$player_stats->maiden_overs;
                $player_data->runs = $player_stats->runs_conceded;
                $player_data->wickets = $player_stats->wickets_taken;
                $player_data->economy = $player_stats->economy_rate;
            }
            $players_data[] = $player_data;
            if($i == 0) {
                $stats->firstInningsBowlers = $players_data;
            }
            if($i == 1){
                $stats->secondInningsBowlers = $players_data;
            }
            if($i == 2) {
                $stats->superOverFirstInningsBowlers = $players_data;
            }
            if($i == 3){
                $stats->superOverSecondInningsBowlers = $players_data;
            }
            if($i == 4) {
                $stats->secondSuperOverFirstInningsBowlers = $players_data;
            }
            if($i == 5){
                $stats->secondSuperOverSecondInningsBowlers = $players_data;
            }
            return $stats;
        }

        if(isset($is_stats_exist)) {
            // $total_innings = $is_stats_exist->over_number == -1 ? $is_stats_exist->innings_completed - 1 : $is_stats_exist->innings_completed;
            $total_innings = $is_stats_exist->innings_completed;
            for($i = 0; $i <= $total_innings; $i++){
                $team = BallByBall::where('match_id', $match_id)->where('innings_completed', $i)->where('over_number', '!=',-1)->orderBy('id', 'ASC')->first();
                $bowling_team_id = $team->bowling_team_id;
                $bowlers = BallByBall::where('match_id', $match_id)->where('bowling_team_id', $bowling_team_id)->where('innings_completed', $i)->where('over_number', '!=', -1)->groupBy('bowler_id')->pluck('bowler_id')->toArray();
                $match_players = MatchPlayer::where('match_id', $match_id)->where('current_innings', $i)->orderBy('id', 'desc')->first();
                $players_data = array();
                foreach($bowlers as $player_id) {
                    $player_name = Player::where('id', $player_id)->pluck('name')->first();
                    $player_ball_by_ball_id = BallByBall::where('match_id', $match_id)
                    ->where('innings_completed', $i)
                    ->where('over_number', '!=', -1)
                    ->where('bowler_id', $player_id)
                    ->orderBy('id', 'asc')->first();
                    $player_stats = BallByBall::where('ball_by_ball.match_id', $match_id)->where('ball_by_ball.innings_completed', $i)->where('ball_by_ball.over_number', '!=',-1)->where('ball_by_ball.bowler_id', $player_id)
                    ->leftJoin('player_bowling_stats as player_stats', 'player_stats.ball_by_ball_id', '=', 'ball_by_ball.id')
                    // ->orderBy('ball_by_ball.id', 'asc')
                    ->orderBy('player_stats.id', 'desc')
                    ->select('player_stats.*')
                    ->first();
                    $player_data = new stdClass();
                    $player_data->name = $player_name;
                    $player_data->ballByBallId = isset($player_ball_by_ball_id) ? $player_ball_by_ball_id->id : PHP_INT_MAX;
                    $player_data->id = $player_id;
                    $player_data->overs = 0.0;
                    $player_data->maidens = 0;
                    $player_data->runs = 0;
                    $player_data->wickets = 0;
                    $player_data->economy = 00.0;
                    if(isset($player_stats)) {
                        $player_data->overs = $player_stats->overs_bowled;
                        $player_data->maidens =$player_stats->maiden_overs;
                        $player_data->runs = $player_stats->runs_conceded;
                        $player_data->wickets = $player_stats->wickets_taken;
                        $player_data->economy = $player_stats->economy_rate;
                    }
                    $players_data[] = $player_data;
                }
                uasort(($players_data), function ($a, $b) {
                    if($a->ballByBallId == $b->ballByBallId) return 0;
                    return ($a->ballByBallId < $b->ballByBallId) ? -1 : 1;
                });
                $players_data = array_values($players_data);
                if($i == 0) {
                    $stats->firstInningsBowlers = $players_data;
                }
                if($i == 1){
                    $stats->secondInningsBowlers = $players_data;
                }
                if($i == 2) {
                    $stats->superOverFirstInningsBowlers = $players_data;
                }
                if($i == 3){
                    $stats->superOverSecondInningsBowlers = $players_data;
                }
                if($i == 4) {
                    $stats->secondSuperOverFirstInningsBowlers = $players_data;
                }
                if($i == 5){
                    $stats->secondSuperOverSecondInningsBowlers = $players_data;
                }
            }
        }
        return $stats;
    }
    public function oldScoreBoardUptoFallOfWickets($match_id){
        $is_stats_exist = BallByBall::where('match_id', $match_id)->where('over_number', '!=', -1)->orderBy('id', 'DESC')->first();
        $stats = new stdClass();
        $stats->player = '';
        $stats->firstInningsFallOfWickets = array();
        $stats->secondInningsFallOfWickets = array();
        $stats->superOverFirstInningsFallOfWickets = array();
        $stats->superOverSecondInningsFallOfWickets = array();
        $stats->secondSuperOverFirstInningsFallOfWickets = array();
        $stats->secondSuperOverSecondInningsFallOfWickets = array();
        if(isset($is_stats_exist)){
            $total_innings = $is_stats_exist->innings_completed;
            for($i = 0; $i <= $total_innings; $i++){
                $team = BallByBall::where('match_id', $match_id)->where('innings_completed', $i)->where('over_number', '!=',-1)->orderBy('id', 'ASC')->first();
                $batting_team_id = $team->batting_team_id;
                $wicketed = BallByBall::where('match_id', $match_id)->where('batting_team_id', $batting_team_id)->where('innings_completed', $i)->where('is_wicket', 1)->orderBy('id', 'asc')->pluck('dismissed_batsmen')->toArray();
                if(!count($wicketed)) {
                    continue;
                }
                    $players_data = array();

                    foreach($wicketed as $player_id) {
                        $player_name = Player::where('id', $player_id)->pluck('name')->first();
                        $player_stats = PlayerBattingStats::where('player_batting_stats.player_id', $player_id)
                        ->where('player_batting_stats.match_id', $match_id)
                        ->orderBy('player_batting_stats.id', 'DESC')
                        ->leftJoin('ball_by_ball', 'ball_by_ball.id', '=', 'player_batting_stats.ball_by_ball_id')
                        ->select(
                            'player_batting_stats.*',
                            'ball_by_ball.over_number',
                            'ball_by_ball.valid_ball_count',
                            'ball_by_ball.id as ballByBallId'
                        )->first();
                        $is_player_out = BallByBall::where('ball_by_ball.match_id', $match_id)->where('ball_by_ball.innings_completed', $team->innings_completed)->where('ball_by_ball.over_number', '!=',-1)
                            ->where('ball_by_ball.dismissed_batsmen', $player_id)->orderBy('ball_by_ball.id', 'asc')
                            ->leftJoin('player_fielding_stats as fielder_stats', 'fielder_stats.ball_by_ball_id', '=', 'ball_by_ball.id')
                            ->leftJoin('players as bowler_details', 'ball_by_ball.bowler_id', '=', 'bowler_details.id')
                            ->leftJoin('players as fielder_details', 'ball_by_ball.fielder_id', '=', 'fielder_details.id')
                            ->select(
                            'fielder_stats.player_id',
                            'fielder_stats.catches',
                            'fielder_stats.run_outs',
                            'fielder_stats.stumpings',
                            'fielder_stats.bowled',
                            'fielder_stats.direct_hit',
                            'fielder_stats.throwing_end_id',
                            'fielder_stats.fielding_caught_behind',
                            'fielder_stats.fielding_caught_and_bowled',
                            'fielder_stats.retired_hurt',
                            'fielder_stats.fielding_caught_and_bowled',
                            'fielder_stats.fielding_mankaded',
                            'fielder_stats.hit_wicket',
                            'fielder_stats.retired_out',
                            'ball_by_ball.fielder_id as fielder_id',
                            'ball_by_ball.bowler_id as bowler_id',
                            'bowler_details.name as bowlerName',
                            'fielder_details.name as fielderName',
                            )
                            ->first();
                        $player_data = new stdClass();
                        $player_data->ballByBallId = PHP_INT_MAX;
                        $player_data->id = $player_id;
                        $player_data->player = $player_name;
                        $player_data->score = 0;
                        $player_data->over = 0.0;
                        $player_data->dismissalType = 'bowled';
                        $player_data->bowlerName = null;
                        $player_data->fielderName = null;
                        if(isset($is_player_out)) {
                                $is_player_out = $is_player_out->toArray();
                                // $dismissal_type = 'out';
                                $dismissal_type = 'bowled';

                                $bowler_name = $is_player_out['bowlerName'];
                                $fielder_name = $is_player_out['fielderName'];
                                foreach($is_player_out as $key => $value){
                                    if($key == 'fielder_id' || $key == 'bowler_id') continue;
                                    if(isset($value) && isset($is_player_out['fielder_id']) && $value == $is_player_out['fielder_id']) {
                                        $dismissal_type = ($key == 'player_id') ? 'bowled': str_replace("_", " ", $key);
                                    }
                                }
                                $player_data->dismissalType = $dismissal_type;
                                $player_data->bowlerName = $bowler_name;
                                $player_data->fielderName = $fielder_name;
                            }
                        if(isset($player_stats)) {
                            // $player_data->score = $player_stats->score;
                            $player_data->ballByBallId = $player_stats->ballByBallId;
                            $total_scores = BallByBall::where('id', $player_stats->ballByBallId)->where('match_id', $match_id)->where('over_number', '!=', -1)->where('innings_completed', $i)->pluck('total_score')->first();
                            // $total_scores = BallByBall::where('match_id', $match_id)->where('over_number', '!=', -1)->where('innings_completed', $i)->where('striker_id', $player_id)->orWhere('non_striker_id', $player_id)->orderBy('id', 'desc')->pluck('total_score')->first();
                            $player_data->score = isset($total_scores) ? $total_scores : '0';
                            $player_data->over = ($player_stats->over_number - 1 ). '.' . $player_stats->valid_ball_count;
                        }
                        $players_data[] = $player_data;
                    }

                    uasort($players_data, function ($a, $b) {
                        if($a->over == $b->over) return 0;
                        return ($a->over < $b->over) ? -1 : 1;
                    });

                if($i == 0) {
                    $stats->firstInningsFallOfWickets = $players_data;
                }
                if($i == 1){
                    $stats->secondInningsFallOfWickets = $players_data;
                }
                if($i == 2) {
                    $stats->superOverFirstInningsFallOfWickets = $players_data;
                }
                if($i == 3){
                    $stats->superOverSecondInningsFallOfWickets = $players_data;
                }
                if($i == 4) {
                    $stats->secondSuperOverFirstInningsFallOfWickets = $players_data;
                }
                if($i == 5){
                    $stats->secondSuperOverSecondInningsFallOfWickets = $players_data;
                }
            }
        }
        return $stats;
    }
    public function scoreBoardUptoTeamDetails($match_id){
        $is_stats_exist = BallByBall::where('match_id', $match_id)->where('over_number', '!=', -1)->orderBy('id', 'DESC')->first();
        $match_players = MatchPlayer::where('match_id', $match_id)->orderBy('id', 'desc')->first();
        $stats = new stdClass();
        $stats->firstInningsDetails = new stdClass();
        $stats->secondInningsDetails = new stdClass();
        $stats->superOverFirstInningsDetails = new stdClass();
        $stats->superOverSecondInningsDetails = new stdClass();
        $stats->secondSuperOverFirstInningsDetails = new stdClass();
        $stats->secondSuperOverSecondInningsDetails = new stdClass();
        if(!isset($is_stats_exist) && isset($match_players)){
            $i = $match_players->current_innings ?? 0;
            $wickets = BallByBall::where('match_id', $match_id)->where('over_number', '!=', -1)->where('innings_completed', $i)->where('is_wicket', 1)->get()->count();
            $wickets = isset($wickets) ? $wickets : '0';
            // $total_scores = BallByBall::where('match_id', $match_id)->where('over_number', '!=', -1)->where('innings_completed', $i)->get()->sum('total_score');
            $total_scores = BallByBall::where('match_id', $match_id)->where('over_number', '!=', -1)->where('innings_completed', $i)->orderBy('id', 'desc')->pluck('total_score')->first();
            $total_scores = isset($total_scores) ? $total_scores : '0';

            $match_data = MatchGame::where('matches.id', $match_id)
            ->leftJoin('teams', 'teams.id', '=', 'matches.batting')
            ->select('teams.name as name','matches.overs as overs', 'matches.teamA_id', 'matches.teamB_id', 'matches.id')
            ->orderBy('matches.id', 'ASC')
            ->first();

			$stats->overs = $match_data->overs;

            $curr_over = BallByBall::where('match_id', $match_id)->where('over_number', '!=', -1)->where('innings_completed', $i)->orderBy('id', 'desc')->pluck('total_overs')->first();
            $curr_over = isset($curr_over) ? "$curr_over/$match_data->overs" : "0.0/0";

            $score = "$total_scores/$wickets";
            $extras = $this->getExtras($match_id, $i);
            $not_batted_players = $this->yetToBat($match_data, $i); 
            if($i == 0) {
                $stats->firstInningsDetails->score = $score;
                $stats->firstInningsDetails->name = $match_data->name;
                $stats->firstInningsDetails->extras = $extras;
                $stats->firstInningsDetails->curr_over = $curr_over;
                $stats->firstInningsDetails->yetToBat = $not_batted_players;
            }
            if($i == 1){
                $stats->secondInningsDetails->score = $score;
                $stats->secondInningsDetails->name = $match_data->name;
                $stats->secondInningsDetails->extras = $extras;
                $stats->secondInningsDetails->curr_over = $curr_over;
                $stats->secondInningsDetails->yetToBat = $not_batted_players;
            }
            if($i == 2) {
                $stats->superOverFirstInningsDetails->score = $score;
                $stats->superOverFirstInningsDetails->name = $match_data->name;
                $stats->superOverFirstInningsDetails->extras = $extras;
                $stats->superOverFirstInningsDetails->curr_over = $curr_over;
                $stats->superOverFirstInningsDetails->yetToBat = $not_batted_players;
            }
            if($i == 3){
                $stats->superOverSecondInningsDetails->score = $score;
                $stats->superOverSecondInningsDetails->name = $match_data->name;
                $stats->superOverSecondInningsDetails->extras = $extras;
                $stats->superOverSecondInningsDetails->curr_over = $curr_over;
                $stats->superOverSecondInningsDetails->yetToBat = $not_batted_players;
            }
            if($i == 4) {
                $stats->secondSuperOverFirstInningsDetails->score = $score;
                $stats->secondSuperOverFirstInningsDetails->name = $match_data->name;
                $stats->secondSuperOverFirstInningsDetails->extras = $extras;
                $stats->secondSuperOverFirstInningsDetails->curr_over = $curr_over;
                $stats->secondSuperOverFirstInningsDetails->yetToBat = $not_batted_players;
            }
            if($i == 5){
                $stats->secondSuperOverSecondInningsDetails->score = $score;
                $stats->secondSuperOverSecondInningsDetails->name = $match_data->name;
                $stats->secondSuperOverSecondInningsDetails->extras = $extras;
                $stats->secondSuperOverSecondInningsDetails->curr_over = $curr_over;
                $stats->secondSuperOverSecondInningsDetails->yetToBat = $not_batted_players;
            }
            return $stats;
        }
        if(isset($is_stats_exist)) {
            $match_data = MatchGame::where('matches.id', $match_id)
            ->leftJoin('teams', 'teams.id', '=', 'matches.batting')
            ->select('teams.name as name','matches.overs as overs', 'matches.teamA_id', 'matches.teamB_id', 'matches.id')
            ->orderBy('matches.id', 'ASC')
            ->first();
            $stats->overs = $match_data->overs;
            $total_innings = $is_stats_exist->innings_completed;

            for($i = 0; $i <= $total_innings; $i++){
                $wickets = BallByBall::where('match_id', $match_id)->where('over_number', '!=', -1)->where('innings_completed', $i)->where('is_wicket', 1)->get()->count();
                $wickets = isset($wickets) ? $wickets : '0';
                // $total_scores = BallByBall::where('match_id', $match_id)->where('over_number', '!=', -1)->where('innings_completed', $i)->get()->sum('total_score');
                $total_scores = BallByBall::where('match_id', $match_id)->where('over_number', '!=', -1)->where('innings_completed', $i)->orderBy('id','desc')->pluck('total_score')->first();
                $total_scores = isset($total_scores) ? $total_scores : '0';
                $match_players = MatchPlayer::where('match_id', $match_id)->where('current_innings', $i)->orderBy('id', 'desc')->first();

                $batting_team_name = BallByBall::where('ball_by_ball.match_id', $match_id)->where('ball_by_ball.over_number', '!=', -1)->where('ball_by_ball.innings_completed', $i)->orderBy('ball_by_ball.id', 'ASC')
                ->leftJoin('teams', 'teams.id', '=', 'ball_by_ball.batting_team_id')
                ->select('teams.name')->pluck('teams.name')
                ->first();
                $extras = $this->getExtras($match_id, $i);
                $score = "$total_scores/$wickets";

                $curr_over = BallByBall::where('match_id', $match_id)->where('over_number', '!=', -1)->where('innings_completed', $i)->orderBy('id', 'desc')->pluck('total_overs')->first();
            	$curr_over = isset($curr_over) ? "$curr_over/$match_data->overs" : "0.0/0";

                $not_batted_players = $this->yetToBat($match_data, $i); 

                if($i == 0) {
                    $stats->firstInningsDetails->score = $score;
                    $stats->firstInningsDetails->name = $batting_team_name;
                    $stats->firstInningsDetails->extras = $extras;
                    $stats->firstInningsDetails->curr_over = $curr_over;
                    $stats->firstInningsDetails->yetToBat = $not_batted_players;
                }
                if($i == 1){
                    $stats->secondInningsDetails->score = $score;
                    $stats->secondInningsDetails->name = $batting_team_name;
                    $stats->secondInningsDetails->extras = $extras;
                    $stats->secondInningsDetails->curr_over = $curr_over;
                    $stats->secondInningsDetails->yetToBat = $not_batted_players;
                }
                if($i == 2) {
                    $stats->superOverFirstInningsDetails->score = $score;
                    $stats->superOverFirstInningsDetails->name = $batting_team_name;
                    $stats->superOverFirstInningsDetails->extras = $extras;
                    $stats->superOverFirstInningsDetails->curr_over = $curr_over;
                    $stats->superOverFirstInningsDetails->yetToBat = $not_batted_players;
                }
                if($i == 3){
                    $stats->superOverSecondInningsDetails->score = $score;
                    $stats->superOverSecondInningsDetails->name = $batting_team_name;
                    $stats->superOverSecondInningsDetails->extras = $extras;
                    $stats->superOverSecondInningsDetails->curr_over = $curr_over;
                    $stats->superOverSecondInningsDetails->yetToBat = $not_batted_players;
                }
                if($i == 4) {
                    $stats->secondSuperOverFirstInningsDetails->score = $score;
                    $stats->secondSuperOverFirstInningsDetails->name = $batting_team_name;
                    $stats->secondSuperOverFirstInningsDetails->extras = $extras;
                    $stats->secondSuperOverFirstInningsDetails->curr_over = $curr_over;
                    $stats->secondSuperOverFirstInningsDetails->yetToBat = $not_batted_players;
                }
                if($i == 5){
                    $stats->secondSuperOverSecondInningsDetails->score = $score;
                    $stats->secondSuperOverSecondInningsDetails->name = $batting_team_name;
                    $stats->secondSuperOverSecondInningsDetails->extras = $extras;
                    $stats->secondSuperOverSecondInningsDetails->curr_over = $curr_over;
                    $stats->secondSuperOverSecondInningsDetails->yetToBat = $not_batted_players;
                }
            }
        }
        return $stats;
    }
    public function scoreBoardPresentData($match_id) {
        $inning = BallByBall::where('match_id', $match_id)->orderBy('id', 'DESC')->pluck('innings_completed')->first();
        return [
            'inning' => $inning,
            'batting_data' => $this->scoreBoardPresentBatsMen($match_id),
            'bowling_data' => $this->scoreBoardPresentBowlers($match_id),
            'fall_of_wicket' => $this->scoreBoardPresentFallOfWickets($match_id),
            'team_details' => $this->scroeBoardPresentTeamDetails($match_id),
        ];
    }
    public function oldScoreBoardPresentBatsMen($match_id) {
        $is_stats_exist = BallByBall::where('match_id', $match_id)->where('over_number', '!=', -1)->orderBy('id', 'DESC')->first();
        $match_players = MatchPlayer::where('match_id', $match_id)->orderBy('id', 'desc')->first();
        $players_data = array();
        if(isset($match_players) && isset($is_stats_exist)){
            // $players = [$is_stats_exist->striker_id, $is_stats_exist->non_striker_id];
            $players = [$match_players->striker_id, $match_players->non_striker_id];
            $inning = $is_stats_exist->innings_completed;
            foreach($players as $player_id) {
                $player_name = Player::where('id', $player_id)->pluck('name')->first();
				$player_stats =  $player_stats = BallByBall::where('ball_by_ball.match_id', $match_id)->where('ball_by_ball.innings_completed', $inning)->where('ball_by_ball.over_number', '!=',-1)->where('ball_by_ball.striker_id', $player_id)
                    ->where('batting_team_id', $is_stats_exist->batting_team_id)
                    ->orderBy('ball_by_ball.id', 'DESC')->first();

                  	// if(isset($player_stats)) {
                      $player_stats =  PlayerBattingStats::where('player_batting_stats.player_id', $player_id)
                       ->orderBy('player_batting_stats.id', 'desc')
                       //->leftJoin('players as fielder_details', 'ball_by_ball.fielder_id', '=', 'fielder_details.id')
                       ->select('player_batting_stats.*', 'player_batting_stats.ball_by_ball_id as ball_by_ball_id')
                       ->first();
                    // }

                $player_data = new stdClass();
                $player_data->id = $player_id;
                $player_data->name = $player_name;
                $player_data->runs = 0;
                $player_data->balls = 0;
                $player_data->strikeRate = 0;
                $player_data->fours = 0;
                $player_data->sixes = 0;
                $player_data->is_striker = $match_players->striker_id == $player_id;
                if(isset($player_stats)) {
                    $player_data->runs = $player_stats->score;
                    $player_data->balls = $player_stats->balls_faced;
                    $player_data->strikeRate = $player_stats->strike_rate;
                    $player_data->fours = PlayerBattingStats::where('match_id', $match_id)->where('player_id', $player_id)->where('four', 1)->pluck('four')->count();
                    $player_data->sixes = PlayerBattingStats::where('match_id', $match_id)->where('player_id', $player_id)->where('six', 1)->pluck('four')->count();
                }
                $players_data[] = $player_data;
            }
        }
        return $players_data;
    }
    public function oldScoreBoardPresentBowlers($match_id) {
        $is_stats_exist = BallByBall::where('match_id', $match_id)->where('over_number', '!=', -1)->orderBy('id', 'DESC')->first();
        $player_id = $is_stats_exist->bowler_id;
        $player_name = Player::where('id', $player_id)->pluck('name')->first();

        $player_data = new stdClass();
        $player_data->id = $player_id;
        $player_data->name = $player_name;
        $player_data->overs = 0.0;
        $player_data->maidens = 0;
        $player_data->runs = 0;
        $player_data->wickets = 0;
        $player_data->economy = 00.0;
        if($is_stats_exist) {
            $inning = $is_stats_exist->innings_completed;
            $player_stats = BallByBall::where('ball_by_ball.match_id', $match_id)->where('ball_by_ball.innings_completed', $inning)->where('ball_by_ball.over_number', '!=',-1)->where('ball_by_ball.bowler_id', $player_id)->orderBy('ball_by_ball.id', 'DESC')
            ->leftJoin('player_bowling_stats as player_stats', 'player_stats.ball_by_ball_id', '=', 'ball_by_ball.id')
            ->select('player_stats.*')
            ->first();
            if(isset($player_stats)) {
                $player_data->overs = $player_stats->overs_bowled;
                $player_data->maidens =$player_stats->maiden_overs;
                $player_data->runs = $player_stats->runs_conceded;
                $player_data->wickets = $player_stats->wickets_taken;
                $player_data->economy = $player_stats->economy_rate;
            }
        }
        return $player_data;
    }
    public function oldScoreBoardPresentFallOfWickets($match_id) {
        $team = BallByBall::where('match_id', $match_id)->where('over_number', '!=', -1)->orderBy('id', 'DESC')->first();
        $players_data = array();
        if(isset($team)){
            $batting_team_id = $team->batting_team_id;
            $i = $team->innings_completed;
            $wicketed = BallByBall::where('match_id', $match_id)->where('batting_team_id', $batting_team_id)->where('innings_completed', $i)->where('is_wicket', 1)->pluck('dismissed_batsmen')->toArray();
            if(!count($wicketed)) {
                return array();
            }

            foreach($wicketed as $player_id) {
                $player_name = Player::where('id', $player_id)->pluck('name')->first();
                $player_stats = PlayerBattingStats::where('player_batting_stats.player_id', $player_id)->orderBy('player_batting_stats.id', 'DESC')
                ->leftJoin('ball_by_ball', 'ball_by_ball.id', '=', 'player_batting_stats.ball_by_ball_id')
                ->select(
                    'player_batting_stats.*',
                    'ball_by_ball.over_number',
                    'ball_by_ball.ball_number',
                )->first();
                $player_data = new stdClass();
                $player_data->player = $player_name;
                $player_data->id = $player_id;
                $player_data->score = 0;
                $player_data->over = 0.0;
              	$player_data->runs = 0;
                $player_data->balls = 0;
                $player_data->strikeRate = 0;
                $player_data->fours = 0;
                $player_data->sixes = 0;
                $player_data->dismissalType = 'bowled';
                // $is_player_out = BallByBall::where('match_id', $match_id)->where('dismissed_batsmen', $player_id)->orderBy('id', 'desc')->first();
                 $is_player_out = BallByBall::where('ball_by_ball.match_id', $match_id)->where('ball_by_ball.innings_completed', $team->innings_completed)->where('ball_by_ball.over_number', '!=',-1)
                    ->where('ball_by_ball.dismissed_batsmen', $player_id)->orderBy('ball_by_ball.id', 'asc')
                    ->leftJoin('player_fielding_stats as fielder_stats', 'fielder_stats.ball_by_ball_id', '=', 'ball_by_ball.id')
                    ->leftJoin('players as bowler_details', 'ball_by_ball.bowler_id', '=', 'bowler_details.id')
                    ->leftJoin('players as fielder_details', 'ball_by_ball.fielder_id', '=', 'fielder_details.id')
                     ->select(
                    'fielder_stats.player_id',
                    'fielder_stats.catches',
                    'fielder_stats.run_outs',
                    'fielder_stats.stumpings',
                    'fielder_stats.bowled',
                    'fielder_stats.direct_hit',
                    'fielder_stats.throwing_end_id',
                    'fielder_stats.fielding_caught_behind',
                    'fielder_stats.fielding_caught_and_bowled',
                    'fielder_stats.retired_hurt',
                    'fielder_stats.fielding_caught_and_bowled',
                    'fielder_stats.fielding_mankaded',
                    'fielder_stats.hit_wicket',
                    'fielder_stats.retired_out',
                    'ball_by_ball.fielder_id as fielder_id',
                    'ball_by_ball.bowler_id as bowler_id',
                    'bowler_details.name as bowlerName',
                    'fielder_details.name as fielderName',
                    )
                    ->first();
                if(isset($is_player_out)) {
                        $is_player_out = $is_player_out->toArray();
                        // $dismissal_type = 'out';
                        $dismissal_type = 'bowled';

                        $bowler_name = $is_player_out['bowlerName'];
                        $fielder_name = $is_player_out['fielderName'];
                        foreach($is_player_out as $key => $value){
                          if($key == 'fielder_id' || $key == 'bowler_id') continue;
                          if(isset($value) && isset($is_player_out['fielder_id']) && $value == $is_player_out['fielder_id']) {
                            $dismissal_type = ($key == 'player_id') ? 'bowled': str_replace("_", " ", $key);
                          }
                        }
                        $player_data->dismissalType = $dismissal_type;
                        $player_data->bowlerName = $bowler_name;
                        $player_data->fielderName = $fielder_name;
                }
                if(isset($player_stats)) {
                    // $total_scores = BallByBall::where('match_id', $match_id)->where('over_number', '!=', -1)->where('innings_completed', $i)->get()->sum('total_score');
                    $total_scores = BallByBall::where('match_id', $match_id)->where('over_number', '!=', -1)->where('innings_completed', $i)->orderBy('id', 'desc')->pluck('total_score')->first() ?? '0';
                    $player_data->score = isset($total_scores) ? $total_scores : '0';
                    $player_data->over = ($player_stats->over_number - 1) ."." .$player_stats->ball_number;
					$player_data->runs = $player_stats->score;
                    $player_data->balls = $player_stats->balls_faced;
                    $player_data->strikeRate = $player_stats->strike_rate;
                    $player_data->fours = PlayerBattingStats::where('match_id', $match_id)->where('player_id', $player_id)->where('four', 1)->pluck('four')->count();
                    $player_data->sixes = PlayerBattingStats::where('match_id', $match_id)->where('player_id', $player_id)->where('six', 1)->pluck('four')->count();
                }
                $players_data[] = $player_data;
            }
        }
        return array_values($players_data);
    }
    public function scroeBoardPresentTeamDetails($match_id) {
        $is_stats_exist = BallByBall::where('match_id', $match_id)->where('over_number', '!=', -1)->orderBy('id', 'DESC')->first();
        $stats = new stdClass();
        $stats->overs = null;
        $stats->score = null;
        $stats->name = null;
        $stats->extras = null;
        $stats->curr_over = "0.0/0";
        $stats->yetToBat = null;
        if(isset($is_stats_exist)) {
            $match_data = MatchGame::where('matches.id', $match_id)
            ->leftJoin('teams as batting_team', 'batting_team.id', '=', 'matches.batting')
            ->leftJoin('teams as bowling_team', 'bowling_team.id', '=', 'matches.bowling')
            ->select('matches.*', 'batting_team.name as batting_team_name', 'bowling_team.name as bowling_team_name')
            ->orderBy('matches.id', 'ASC')->first();
            $total_overs = $match_data->overs;

            $stats->overs = $total_overs;
            $i = $is_stats_exist->innings_completed;

            $wickets = BallByBall::where('match_id', $match_id)->where('over_number', '!=', -1)->where('innings_completed', $i)->where('is_wicket', 1)->get()->count();
            $wickets = isset($wickets) ? $wickets : '0';
            // $total_scores = BallByBall::where('match_id', $match_id)->where('over_number', '!=', -1)->where('innings_completed', $i)->sum('total_score');
            $total_scores = BallByBall::where('match_id', $match_id)->where('over_number', '!=', -1)->where('innings_completed', $i)->orderBy('id','desc')->pluck('total_score')->first();
            $total_scores = isset($total_scores) ? $total_scores : '00';

            $batting_team_data = BallByBall::where('ball_by_ball.match_id', $match_id)->where('ball_by_ball.over_number', '!=', -1)->where('ball_by_ball.innings_completed', $i)->orderBy('ball_by_ball.id', 'DESC')
            ->leftJoin('teams', 'teams.id', '=', 'ball_by_ball.batting_team_id')
            ->select('teams.name', 'ball_by_ball.total_overs')
            ->first();
            $curr_over = "0.0";
            $batting_team_name = $match_data->batting_team_name;
            if(isset($batting_team_data)) {
                $curr_over = $batting_team_data->total_overs;
                $batting_team_name = $batting_team_data->name;
            }
            $curr_over = "$curr_over/$total_overs";

            $stats->score = "$total_scores/$wickets";
            $stats->name = $batting_team_name;
            $stats->curr_over = $curr_over;
            $stats->extras = $this->getExtras($match_id, $i);
            $stats->yetToBat  = $this->yetToBat($match_data, $i);
        }
        return $stats;
    }
     public function deleteFullBallData(Request $request) {
        try {
            DB::beginTransaction();

            DB::table('player_batting_stats')->delete();
            DB::table('player_bowling_stats')->delete();
            DB::table('player_fielding_stats')->delete();
            DB::table('ball_by_ball')->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'All data deleted successfully.',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete data: ' . $e->getMessage(),
            ], 500);
        }
    }

    // public function changematchstatus($matchId) {
    //     try {
    //         $match = MatchGame::find($matchId);

    //         if (!$match) {
    //             return response()->json(['message' => 'Match not found'], 404);
    //         }

    //         $scheduleMatchId = $match->schedule_match_id;
    //         $scheduleMatch = ScheduleMatch::find($scheduleMatchId);

    //         if (!$scheduleMatch) {
    //             return response()->json(['message' => 'Scheduled match not found'], 404);
    //         }

    //         $match->update(['status' => 'Completed']);
    //         $scheduleMatch->update(['status' => 'Completed']);

    //         return response()->json(['message' => 'Match status updated successfully'], 200);

    //     } catch (\Exception $e) {
    //         return response()->json(['message' => 'Failed to update match status', 'error' => $e->getMessage()], 500);
    //     }
    // }

   public function changematchstatus(Request $request , $matchId) {
        try {
            $match = MatchGame::where('matches.id', $matchId)
            ->leftJoin('teams as teamA', 'teamA.id', '=', 'matches.teamA_id')
            ->leftJoin('teams as teamB', 'teamB.id', '=', 'matches.teamB_id')
            ->select('matches.*', 'teamA.name as teamA_name', 'teamB.name as teamB_name')->first();
            $status = $request->input('status');
            $details = $request->input('details');
            $winningTeam = $request->input('winningteam') ?? '';
            $cancelstatus = ($status === 'Canceled') ? '(Canceled)' : '';

            $fullDetails = $winningTeam . ' ' . $details . ' ' . $cancelstatus;

            if (!$match) {
                return response()->json(['message' => 'Match not found'], 404);
            }
            $scheduleMatchId = $match->schedule_match_id;
            $scheduleMatch = ScheduleMatch::find($scheduleMatchId);
            if (!$scheduleMatch) {
                return response()->json(['message' => 'Scheduled match not found'], 404);
            }
            $match->update(['status' => $status , 'match_details' => $fullDetails]);
            $scheduleMatch->update(['status' => $status]);

            Artisan::call('db:backup', [
                'teamA' => $match->teamA_name,
                'teamB' => $match->teamB_name,
            ]);
            $output = Artisan::output();

            return response()->json(['message' => 'Match status updated successfully', 'backup_output' => $output], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update match status', 'error' => $e->getMessage()], 500);
        }
    }
  
    public function getActivePlayers($match_id)
    {
        try {
            // Fetch players for the given match where is_out is 0
            $players = PlayerBattingStats::where('match_id', $match_id)
                ->where('is_out', 1)
                ->get(['player_id', 'is_out']);

            return response()->json([
                'success' => true,
                'players' => $players,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }

    }
    public function getExtras($match_id, $inning) {
        $extras = new stdClass();
        $extras->total = 0;
        $extras->wides = 0;
        $extras->noBalls = 0;
        $extras->byes = 0;
        $extras->legByes= 0;
        $extras->penaltyRuns = 0;
        $match_data = MatchGame::where('id', $match_id)->first();
        if(isset($match_data)) {
            $no_balls = BallByBall::where('match_id', $match_id)
            ->where('innings_completed', $inning)
            ->where('over_number', '!=', -1)
            ->where('extra_type', 'LIKE', "NB%")
            ->orderBy('id', 'desc')
            ->pluck('extra_runs');
            $wide_balls = BallByBall::where('match_id', $match_id)
            ->where('innings_completed', $inning)
            ->where('over_number', '!=', -1)
            ->where('extra_type', 'LIKE', "WD%")
            ->orderBy('id', 'desc')
            ->pluck('display_run');
            $bye_balls = BallByBall::where('match_id', $match_id)
            ->where('innings_completed', $inning)
            ->where('over_number', '!=', -1)
            ->where('extra_type', 'LIKE', "B%")
            ->orderBy('id', 'desc')
            ->pluck('display_run');

            $leg_bye_balls = BallByBall::where('match_id', $match_id)
            ->where('innings_completed', $inning)
            ->where('over_number', '!=', -1)
            ->where('extra_type', 'LIKE', "LB%")
            ->orderBy('id', 'desc')
            ->pluck('display_run');

            $penaly_runs = Penalty::where('match_id', $match_id)
            ->where('inning', $inning)->sum('runs');

            $wide_balls->each(function ($value) use ($extras) {
                $extras->wides += $this->extrasHelper($value, true);
            });
            $no_balls->each(function ($value) use ($extras) {
                //$extras->noBalls += $this->extrasHelper($value);
                $extras->byes += ((int) $value - 1);
                $extras->noBalls += 1;
            });
            $bye_balls->each(function ($value) use ($extras){
				$value = explode('+', $value)[1];
                $extras->byes += (int) $value;
            });
          
            $leg_bye_balls->each(function ($value) use ($extras){
                $extras->legByes += $this->extrasHelper($value, false);
            });

            $extras->penaltyRuns = $penaly_runs;
            $extras->total = $extras->wides + $extras->noBalls + $extras->byes + $extras->legByes + $extras->penaltyRuns;
        }
        return $extras;
    }
    public function extrasHelper(string $extra, bool $isExtra) {
        $extra = (int) explode('+', $extra)[1];
        return $extra + ($isExtra ? 1 : 0);
    }
       public function getUndoData($match_id) {
        $match_data = MatchGame::where('matches.id', $match_id)
        ->select(
            'matches.batting as batting_team_id',
            'matches.bowling as bowling_team_id',
            )->first();
        if(isset($match_data)) {
            $latestBallByBallData = BallByBall::where('match_id', $match_id)->orderBy('id', 'desc')->first();
            if(isset($latestBallByBallData)) {
                return $latestBallByBallData;
            }
            $match_data->innings_completed = 0;
            return $match_data;
        }
        $dummyData = new stdClass();
        $dummyData->batting_team_id = -1;
        $dummyData->bowling_team_id = -1;
        $dummyData->innings_completed = 0;
        return $dummyData;
    }

    public function scoreBoardUptoBatsMen($match_id) {
        $stats = new stdClass();
        $stats->firstInningsBatsMen = array();
        $stats->secondInningsBatsMen = array();
        $stats->superOverFirstInningsBatsMen = array();
        $stats->superOverSecondInningsBatsMen = array();
        $stats->secondSuperOverFirstInningsBatsMen = array();
        $stats->secondSuperOverSecondInningsBatsMen = array();

        $match_players_inning = MatchPlayer::where('match_id', $match_id)->orderBy('id', 'asc')->pluck('current_innings')->toArray();

        if(count($match_players_inning) > 0) {
            $score_board_ctrl = new ScoreBoardController($match_id);
            foreach($match_players_inning as $inning) {
                $team_id = BallByBall::where('match_id', $match_id)
                ->where('innings_completed', $inning)
                ->where('over_number', '!=', -1)
                ->orderBy('id', 'desc')
                ->pluck('batting_team_id')
                ->first();
                $team_id = isset($team_id) ? $team_id : MatchGame::where('id', $match_id)->orderBy('id', 'desc')->pluck('batting')->first();

                $data = $score_board_ctrl->getScoreBoardBatsMen($team_id, $inning);
                switch ($inning) {
                    case 0:
                    $stats->firstInningsBatsMen = $data;
                        break;
                    case 1:
                        $stats->secondInningsBatsMen = $data;
                        break;
                    case 2:
                        $stats->superOverFirstInningsBatsMen = $data;
                        break;
                    case 3:
                        $stats->superOverSecondInningsBatsMen = $data;
                        break;
                    case 4:
                        $stats->secondSuperOverFirstInningsBatsMen = $data;
                        break;
                    case 5:
                        $stats->secondSuperOverSecondInningsBatsMen = $data;
                        break;
                }
            }
        }
        return $stats;
    }
    public function scoreBoardPresentBatsMen($match_id) {
        $match_players = MatchPlayer::where('match_id', $match_id)->orderBy('id', 'desc')->first();
        if(isset($match_players)) {
            $score_board_ctrl = new ScoreBoardController($match_id);
            return $score_board_ctrl->getScoreBoardBatsMen($match_players->team_id, $match_players->current_innings);
        }
        return [];
    }
    public function scoreBoardUptoFallOfWickets($match_id) {
        $stats = new stdClass();
        $stats->firstInningsFallOfWickets = array();
        $stats->secondInningsFallOfWickets = array();
        $stats->superOverFirstInningsFallOfWickets = array();
        $stats->superOverSecondInningsFallOfWickets = array();
        $stats->secondSuperOverFirstInningsFallOfWickets = array();
        $stats->secondSuperOverSecondInningsFallOfWickets = array();

        $match_players_inning = MatchPlayer::where('match_id', $match_id)->orderBy('id', 'asc')->pluck('current_innings')->toArray();

        if(count($match_players_inning) > 0) {
            $score_board_ctrl = new ScoreBoardController($match_id);
            foreach($match_players_inning as $inning) {
                $team_id = BallByBall::where('match_id', $match_id)
                ->where('innings_completed', $inning)
                ->where('over_number', '!=', -1)
                ->orderBy('id', 'desc')
                ->pluck('batting_team_id')
                ->first();
                $team_id = isset($team_id) ? $team_id : MatchGame::where('id', $match_id)->orderBy('id', 'desc')->pluck('batting')->first();

                $data = $score_board_ctrl->getFallOfWickets($team_id, $inning);

                switch($inning) {
                    case 0:
                        $stats->firstInningsFallOfWickets = $data;
                        break;
                    case 1:
                        $stats->secondInningsFallOfWickets = $data;
                        break;
                    case 2:
                        $stats->superOverFirstInningsFallOfWickets = $data;
                        break;
                    case 3:
                        $stats->superOverSecondInningsFallOfWickets = $data;
                        break;
                    case 4:
                        $stats->secondSuperOverFirstInningsFallOfWickets = $data;
                        break;
                    case 5:
                        $stats->secondSuperOverSecondInningsFallOfWickets = $data;
                        break;
                }
            }
        }
        return $stats;
    }
    public function scoreBoardPresentFallOfWickets($match_id) {
        $match_players = MatchPlayer::where('match_id', $match_id)->orderBy('id', 'desc')->first();
        if($match_players) {
            $score_board_ctrl = new ScoreBoardController($match_id);
            return $score_board_ctrl->getFallOfWickets($match_players->team_id, $match_players->current_innings);
        }
        return [];
    }
    public function scoreBoardUptoBowlersForBattingTeam($match_id) {
        $stats = new stdClass();
        $stats->firstInningsBowlers = array();
        $stats->secondInningsBowlers = array();
        $stats->superOverFirstInningsBowlers = array();
        $stats->superOverSecondInningsBowlers = array();
        $stats->secondSuperOverFirstInningsBowlers = array();
        $stats->secondSuperOverSecondInningsBowlers = array();

        $match_players_inning = MatchPlayer::where('match_id', $match_id)->orderBy('id', 'asc')->pluck('current_innings')->toArray();
        if(count($match_players_inning) > 0) {
            $score_board_ctrl = new ScoreBoardController($match_id);
            foreach($match_players_inning as $inning) {
                $team_id = BallByBall::where('match_id', $match_id)
                ->where('innings_completed', $inning)
                ->where('over_number', '!=', -1)
                ->orderBy('id', 'desc')
                ->pluck('bowling_team_id') //NOTE: BOWLING TEAM ID
                ->first();
                $team_id = isset($team_id) ? $team_id : MatchGame::where('id', $match_id)->orderBy('id', 'desc')->pluck('bowling')->first();

                $data = $score_board_ctrl->getScoreBoardBowlers($team_id, $inning);

                switch($inning) {
                    case 0:
                        $stats->firstInningsBowlers = $data;
                        break;
                    case 1:
                        $stats->secondInningsBowlers = $data;
                        break;
                    case 2:
                        $stats->superOverFirstInningsBowlers = $data;
                        break;
                    case 3:
                        $stats->superOverSecondInningsBowlers = $data;
                        break;
                    case 4:
                        $stats->secondSuperOverFirstInningsBowlers  = $data;
                        break;
                    case 5:
                        $stats->secondSuperOverSecondInningsBowlers = $data;
                        break;
                }
            }
        }
        return $stats;
    }
    public function scoreBoardPresentBowlers($match_id) {
        $ball_by_ball = BallByBall::where('match_id', $match_id)->orderBy('id', 'desc')->first();
        if($ball_by_ball) {
            $score_board_ctrl = new ScoreBoardController($match_id);
            return $score_board_ctrl->getScoreBoardBowlers($ball_by_ball->bowling_team_id, $ball_by_ball->innings_completed);
        }
        return [];
    }

    public function updateMatchDetails(Request $request, $matchId)
{
    $Match = MatchGame::find($matchId);
    if (!$Match) {
        return response()->json(['error' => 'Match not found'], 404);
    }

    $shedulematch = ScheduleMatch::where('id', $Match->schedule_match_id)->first();
    if (!$shedulematch) {
        return response()->json(['error' => 'Schedule match not found'], 404);
    }

    $matchDetail = $request->matchDetail;
    $status = ($matchDetail === 'Match Tied (Super Over)') ? 'Active' : 'Completed';

    try {
        $shedulematch->update(['status' => $status]);
        $Match->update(['match_details' => $matchDetail, 'status' => $status]);

        return response()->json([
            'success' => true,
            'message' => 'Match details updated',
            'data' => $Match,
            'match_details' => $matchDetail,
        ], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to update match details', 'message' => $e->getMessage()], 500);
    }
}


    public function scoreCardScoreBoard($match_id) {
        return response()->json([
            'batting_data' => $this->scoreBoardUptoBatsMen($match_id),
            'bowler_data' => $this->scoreBoardUptoBowlersForBattingTeam($match_id),
            'fall_of_wicket' => $this->scoreBoardUptoFallOfWickets($match_id),
            'team_details' => $this->scoreBoardUptoTeamDetails($match_id),
            'over_summary' => $this->getScoreBoardOverSummary($match_id),
        ]);
    }
    public function getScoreBoardOverSummary($match_id) {
        $match_id = (int) $match_id;
        $match_players = MatchPlayer::where('match_id', $match_id)->orderBy('id', 'desc')->first();
        $ball_by_ball = BallByBall::where('match_id', $match_id)->orderBy('id', 'ASC')->first();
        $match_data = MatchGame::where('matches.id', $match_id)
        ->join('teams as batting_team', 'batting_team.id', '=', 'matches.batting')
        ->join('teams as bowling_team', 'bowling_team.id', '=', 'matches.bowling')
        ->select('matches.*', 'batting_team.name as batting_team_name', 'bowling_team.name as bowling_team_name')
        ->first();

        $team_1_summary = new stdClass();
        $team_1_summary->id = isset($match_data) ? $match_data->batting : null;
        $team_1_summary->overSummary = [];

        $team_2_summary = new stdClass();
        $team_2_summary->id = isset($match_data) ? $match_data->bowling : null;
        $team_2_summary->overSummary = [];

        $batting_team_id = $ball_by_ball ? $ball_by_ball->batting_team_id: null;
        $bowling_team_id = $ball_by_ball ? $ball_by_ball->bowling_team_id: null;

        if(isset($match_players) && isset($ball_by_ball)) {
            $team_1_id = $team_1_summary->id;
            $team_2_id = $team_2_summary->id;
            $teams = array($team_1_id, $team_2_id);
            $total_over = $match_data->overs;
            $initial_over = 1;
            foreach($teams as $team) {
                // $total_innings = BallByBall::where('match_id', $match_id)->orderBy('id', 'DESC')->first();
                // $total_innings = $total_innings->over_number == -1 ? $total_innings->innings_completed - 1 : $total_innings->innings_completed;
                //NOTE: NOW ONLY FOR UPTO 2nd INNINGS
                $total_innings = 1;
                for($inning = 0; $inning <= $total_innings; $inning++) {
                    $createSummary = $this->createSummary($inning,$initial_over, $total_over,$team, $match_id);
                    if($inning == 0 && !count($team_1_summary->overSummary)) {
                        $team_1_summary->overSummary = $createSummary['over_summary'];
                    }
                    if($inning == 1 && !count($team_2_summary->overSummary)){
                        $team_2_summary->overSummary = $createSummary['over_summary'];
                    }
                }
            }
        }
        return ([
            'match_id' => $match_id,
            'team1Summary' => $team_1_summary,
            'team2Summary' => $team_2_summary,
            'battingTeamName' => isset($match_data) ? $match_data->batting_team_name : null,
            'bowlingTeamName' => isset($match_data) ? $match_data->bowling_team_name : null,
            'battingTeamId' => $batting_team_id,
            'bowlingTeamId' => $bowling_team_id,
    ]);

    }

    public function getBallByBallDataByMatchId($matchId)
    {
        \Log::info('trigger');
        $ballByBallData = BallByBall::where('match_id', $matchId)->value('match_id');

        $ballNumberForUndo = BallByBall::where('match_id', $matchId) ->orderByDesc('ball_number_for_undo')->first();

        return [
            'match_id' => $ballByBallData ?? null,
            'ball_number_for_undo' => $ballNumberForUndo ? $ballNumberForUndo->ball_number_for_undo : null,
        ];
    }

     public function storeDls(Request $request) {
            $match_id = $request->matchId;
            $dls = $request->dls;
            $status_code = 200; $msg = "Updated Dls";

            if(empty($dls)){
                $status_code = 400;
                $msg = 'Empty given in dls';
            }else{
                $match_data = MatchGame::find($match_id);
                $match_data->dls = $dls;
                $match_data->save();
            }

            return response()->json([
                'statusCode' => $status_code,
                'msg' => $msg,
                'dls' => $dls,
            ]);

        }
  
   public function galleries()
    {
        $galleries = Gallery::orderBy('id', 'desc')->get()->groupBy('title');

        return response()->json([
            'galleries' => $galleries,
        ]);
    }

   public function partners()
    {
       $partners = Partner::all()->groupBy('title');

      return response()->json([
         'partners' => $partners,
        ]);
   }
  
  
   public function updatepoints(Request $request, $matchId) {

    try {
        $winningTeamId = (int) $request->input('selectedWinner');

        if ($winningTeamId) {
            $match = MatchGame::where('matches.id', $matchId)
            ->leftJoin('teams as teamA', 'teamA.id', '=', 'matches.teamA_id')
            ->leftJoin('teams as teamB', 'teamB.id', '=', 'matches.teamB_id')
            ->select('matches.*', 'teamA.name as teamA_name', 'teamB.name as teamB_name')->first();
            
            $scheduleMatchId = $match->schedule_match_id;


            $team = Team::where('id' , $winningTeamId)->first();
            $match->update(['status' => 'Completed', 'match_details' => $team->name. ' ' . 'Won the match in walkover']);

            $scheduleMatch = ScheduleMatch::find($scheduleMatchId);
            $scheduleMatch->update(['status' => 'Completed']);

            $team1Id = $match->teamA_id;
            $team2Id = $match->teamB_id;

            $losingTeamId = ($winningTeamId === $team1Id) ? $team2Id : $team1Id;

            Point::updateOrCreate(
                [
                    'tournament_id' => $match->tournament_id,
                    'match_id' => $matchId,
                    'team_id' => $winningTeamId
                ],
                [
                    'matches_played' => 1,
                    'wins' => 1,
                    'losses' => 0,
                    'matches_not_played' => 0,
                    'matches_tied' => 0,
                    'total_points' => 2,
                    'group_id' => $match->group_id,
                    'round_id' => $match->round_id,
                ]
            );

            Point::updateOrCreate(
                [
                    'tournament_id' => $match->tournament_id,
                    'match_id' => $matchId,
                    'team_id' => $losingTeamId
                ],
                [
                    'matches_played' => 1,
                    'wins' => 0,
                    'losses' => 1,
                    'matches_not_played' => 0,
                    'matches_tied' => 0,
                    'total_points' => 0,
                    'group_id' => $match->group_id,
                    'round_id' => $match->round_id,
                ]
            );

            Artisan::call('db:backup', [
                'teamA' => $match->teamA_name,
                'teamB' => $match->teamB_name,
            ]);
            $output = Artisan::output();
        }

        return response()->json(['message' => 'Points updated successfully', 'backup_output' => $output], 200);

    } catch (\Exception $e) {
        return response()->json(['message' => 'Failed to update points', 'error' => $e->getMessage()], 500);
    }
}
public function yetToBat($match_data, $i) {
    $match_id = $match_data->id;
    $curr_inning_batting_team_id = BallByBall::where('match_id', $match_id)->where('innings_completed', $i)->where('over_number', '!=', -1)
    ->orderBy('id', 'desc')->pluck('batting_team_id')->first();
    $batted_players = isset($curr_inning_batting_team_id) ? ScoreBoard::where('match_id', $match_id)->where('inning', $i)
    ->where('team_id', $curr_inning_batting_team_id)->pluck('batter_id') : null;
    $batted_players = (!isset($batted_players) || !count($batted_players)) ? [] : $batted_players;
    $not_batted_players = [];
    if($curr_inning_batting_team_id == $match_data->teamA_id) {
        $not_batted_players = Team1Detail::where('team1_details.match_id', $match_id)->where('team1_details.team_id', $curr_inning_batting_team_id)
        ->whereNotIn('team1_details.player_id', $batted_players)
        ->leftJoin('players', function ($join) {
            $join->on('players.id', '=', 'team1_details.player_id');
        })
        ->select(
            'players.name as name', 
            'players.id',
            DB::raw('CASE WHEN team1_details.captain = 1 AND team1_details.wicketkeeper = 0 AND team1_details.12th_man = 0 THEN true ELSE false END as is_captain'),
            DB::raw('CASE WHEN team1_details.captain = 1 AND team1_details.wicketkeeper = 1 AND team1_details.12th_man = 0 THEN true ELSE false END as is_captain_wicket_keeeper'),
            DB::raw('CASE WHEN team1_details.captain = 0 AND team1_details.wicketkeeper = 1 AND team1_details.12th_man = 0 THEN true ELSE false END as is_wicket_keeper'),
            DB::raw('CASE WHEN team1_details.captain = 0 AND team1_details.wicketkeeper = 0 AND team1_details.12th_man = 1 THEN true ELSE false END as is_12th_man'),
        )->get();
    }
    else if($curr_inning_batting_team_id == $match_data->teamB_id){
        $not_batted_players = Team2Detail::where('team2_details.match_id', $match_id)->where('team2_details.team_id', $curr_inning_batting_team_id)
        ->whereNotIn('team2_details.player_id', $batted_players)
        ->leftJoin('players', function ($join) {
            $join->on('players.id', '=', 'team2_details.player_id');
        })
        ->select(
            'players.name as name', 
            'players.id',
            DB::raw('CASE WHEN team2_details.captain = 1 AND team2_details.wicketkeeper = 0 AND team2_details.12th_man = 0 THEN true ELSE false END as is_captain'),
            DB::raw('CASE WHEN team2_details.captain = 1 AND team2_details.wicketkeeper = 1 AND team2_details.12th_man = 0 THEN true ELSE false END as is_captain_wicket_keeeper'),
            DB::raw('CASE WHEN team2_details.captain = 0 AND team2_details.wicketkeeper = 1 AND team2_details.12th_man = 0 THEN true ELSE false END as is_wicket_keeper'),
            DB::raw('CASE WHEN team2_details.captain = 0 AND team2_details.wicketkeeper = 0 AND team2_details.12th_man = 1 THEN true ELSE false END as is_12th_man'),
        )->get();
    }

    return $not_batted_players;
}
}
