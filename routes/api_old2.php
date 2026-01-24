<?php

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\TeamsController;
use App\Http\Controllers\Api\GroupsController;
use App\Http\Controllers\Api\PointsController;
use App\Http\Controllers\Api\VenuesController;
use App\Http\Controllers\Admin\MatchController;
use App\Http\Controllers\Api\MatchesController;
use App\Http\Controllers\Api\PlayersController;
use App\Http\Controllers\Api\ScoringController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\StandingsController;
use App\Http\Controllers\Api\TournamentController;
use App\Http\Controllers\Api\PushNotificationController;
use App\Http\Controllers\Api\ContinueMatchStateController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public Routes
Route::post('/team/login', [LoginController::class, 'login']);
Route::post('/team/register', [TeamsController::class, 'register']);
Route::get('/teams', [TeamsController::class, 'Teams']);
Route::get('/teams/{teamId}/team/details/players', [TeamsController::class, 'getPlayersDetailsByTeam']);
Route::get('/Match/Fixtures' , [MatchesController::class , 'MatchFixtures']);
Route::get('/checkScheduledMatchStatus/{matchId}', [MatchesController::class, 'checkMatchStatus']);
Route::post('/getSquadsPlayers', [MatchesController::class, 'getSquadsPlayers']);
Route::get('/getUptoMatchData/{matchId}', [MatchesController::class, 'getUptoMatchData']);
Route::get('/getPresentMatchData/{matchId}', [MatchesController::class, 'getPresentMatchData']);
Route::get('/venues', [VenuesController::class, 'index']);
Route::get('/groupBasedTeam', [StandingsController::class, 'index']);
Route::get('/getCurrentTeamScore/{match_id}', [MatchesController::class, 'getTeamScore']);
Route::get('/getInningStatus/{match_id}/{inning}', [MatchesController::class, 'getInningStatus']);
Route::get('/getScoreBoardUptoData/{match_id}', [MatchesController::class, 'scoreBoardUptoData']);
Route::get('/getScoreBoardPresentData/{match_id}', [MatchesController::class, 'scoreBoardPresentData']);
Route::get('/fetchSummary/{match_id}', [MatchesController::class, 'fetchSummary']);
// Route::post('/store-push-token', [PushNotificationController::class, 'store']);
Route::get('/batting-stats', [StatsController::class , 'getBattingStats']);
Route::get('/bowling-stats', [StatsController::class , 'getBowlingStats']);
Route::get('/teams/stats', [StatsController::class , 'getTeams']);
Route::get('/tournaments', [StatsController::class , 'getTournaments']);
Route::get('/leading-batters', [StatsController::class , 'getLeadingBatters']);
Route::get('/leading-bowlers', [StatsController::class , 'getLeadingBowlers']);
Route::get('/getScoreCardScoreBoardData/{match_id}', [MatchesController::class, 'scoreCardScoreBoard']);

// Routes that require authentication
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/team/logout', [LoginController::class, 'logout']);
    Route::post('/players/add/players', [PlayersController::class, 'addPlayers']);
    Route::post('/players/edit/{id}', [PlayersController::class, 'updatePlayers']);
    Route::delete('/players/delete/{id}', [PlayersController::class, 'delete']);
    Route::get('/players', [PlayersController::class, 'players']);
    Route::get('/teams/{teamId}/team/players', [TeamsController::class, 'getPlayersByTeam']);
    Route::get('/teams/{teamId}/playingelevens', [TeamsController::class, 'getPlayingElevens']);
    Route::post('/completed/players', [PlayersController::class, 'CompleteTeam']);
    Route::get('/user', function (Request $request) {return $request->user();});
    Route::get('/check/role/admin/or/user/{userId}', [LoginController::class, 'checkUserRoleAndTeamStatus']);
    Route::post('/start/store/matches', [MatchesController::class, 'storeMatchDetails']);
    Route::get('/team/details', [TeamsController::class, 'TeamDetails']);
    Route::get('teams/{teamIds}/team/players/captain', [TeamsController::class, 'getTeamCaptains']);
    Route::get('/Scheduled/Matches', [TournamentController::class, 'StartMatch']);
    Route::get('/get-team-data/{id}', [TournamentController::class, 'getTeamData']);
    Route::get('/umpires/scorers', [TournamentController::class, 'getOrganizers']);
    Route::get('/matches/{id}/getInningsDetails/{isSecondInnings}/{isSuperOver}/{isSecondInningsSuperOver}/{isSecondSuperOver}/{isSecondSuperOverSecondInnings}', [MatchesController::class, 'getInningsDetails']);
    Route::post('/ball-by-ball', [ScoringController::class, 'store']);
    // Route::get('/bowling-stats/{matchId}', [ScoringController::class, 'getBowlerStats']);
    Route::get('/bowling-stats/{matchId}/{bowlerId}', [ScoringController::class, 'getBowlerStats']);
    Route::get('/bowling/full/stats/{matchId}', [ScoringController::class, 'getallbowlersstats']);
    Route::delete('/ball-data/{matchId}', [ScoringController::class, 'deleteLastBall']);
    Route::post('/updateMatchPlayers', [MatchesController::class, 'createOrUpdateMatchPlayers']);
    Route::post('/match/score', [MatchesController::class, 'saveMatchScore']);
    Route::post('/match/superover/score', [MatchesController::class, 'saveSuperOverMatchScore']);
    Route::post('/match/superover/two/score', [MatchesController::class, 'saveSecondSuperOverMatchScore']);
    Route::get('/match/{match_id}/score', [MatchesController::class, 'getMatchScore']);
    Route::get('/match/{match_id}/superover/score', [MatchesController::class, 'getSuperOverMatchScore']);
    Route::get('/match/{match_id}/superover/two/score', [MatchesController::class, 'getSecondSuperOverMatchScore']);
    Route::post('/update-points', [PointsController::class, 'updatePoints']);
    Route::get('/match/stats/{matchId}', [MatchesController::class, 'getMatchStats']);
    Route::get('/match-status/{id}', [MatchesController::class, 'getMatchStatus']);
    Route::get('/delete/fullballdata', [MatchesController::class, 'deleteFullBallData']);
    Route::post('/change/match/status/{matchId}' , [MatchesController::class , 'changematchstatus']);
    Route::get('/match/{match_id}/active-players', [MatchesController::class , 'getActivePlayers']);
    Route::get('match/{id}/ball-by-ball', [ScoringController::class, 'fetchBallByBallData']);
    Route::post('/update/match/{id}/detail', [MatchesController::class, 'updateMatchDetails']);
    Route::post('/saveState', [ContinueMatchStateController::class, 'saveState']);
    Route::get('/getState/{match_id}', [ContinueMatchStateController::class, 'getState']);
});
