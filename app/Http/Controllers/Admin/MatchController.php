<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\MatchesController;
use App\Http\Controllers\Controller;

use App\Models\MatchGame;
use App\Models\OrganizerMember;
use App\Models\Player;
use App\Models\ScheduleMatch;
use App\Models\Team1Detail;
use App\Models\TournamentTeam;
use App\Models\Team2Detail;
use App\Models\Team;
use App\Models\BallByBall;
use App\Models\MatchScore;
use App\Models\Tournament;
use App\Models\User;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use DB;

class MatchController extends Controller
{
    protected string $API_URL;
    protected string $SOCKET_BASE_URL;
    protected string $SOCKET_PORT;
    protected string $SOCKET_URL;

    public $base_url;
    public  $socket_port;

    public function __construct() {
        $this->socket_port = 3000;
        //$this->base_url = explode(':', url('/'));
       $this->base_url = 'https://socketpitchburners.geohomefinder.com';

        $this->API_URL = url('/api');
        $this->SOCKET_BASE_URL = env('SOCKET_URL', $this->base_url);
        $this->SOCKET_PORT = env('SOCKET_PORT', $this->socket_port);
        $this->SOCKET_URL = $this->SOCKET_BASE_URL. ":" . $this->SOCKET_PORT;
    }
    public function index($match_id)
     {
        $match = ScheduleMatch::with(['teamOne.players', 'teamTwo.players', 'venue'])
                    ->where('id', $match_id)
                    ->firstOrFail();

        $teamAPlayers = Player::where('team_id', $match->teamOne->id)->get();
        $teamBPlayers = Player::where('team_id', $match->teamTwo->id)->get();

        $users = User::where('role', 2)->get();

        return view('admin.startmatch', [
            'match' => $match,
            'teamAPlayers' => $teamAPlayers,
            'teamBPlayers' => $teamBPlayers,
            'users' => $users,
            'tournament_id' => $match->tournament_id
        ]);
    }

    public function getPlayers($team_id)
     {
        $players = Player::where('team_id', $team_id)->get();
        return response()->json($players);
    }

    public function startMatch(Request $request)
     {
        $validatedData = $request->validate([
            'matchDetails.matchType' => 'required|string',
           'matchDetails.noOfOvers' => 'nullable|numeric|min:1',
            'matchDetails.ground' => 'nullable|string',
            'matchDetails.dateTime' => 'nullable|date',
            'matchDetails.tossWinner' => 'required|exists:teams,id',
            'matchDetails.tossChoice' => 'required|in:bat,bowl',
            'matchDetails.tournament_id' => 'required|exists:tournaments,id',
            'matchDetails.schedule_match_id' => 'required',
            'matchDetails.round_id' => 'required',
            'matchDetails.group_id' => 'required',
            'matchDetails.overs_per_bowler' => 'required',
        ], [
            'matchDetails.matchType.required' => 'Match type is required.',
            'matchDetails.matchType.string' => 'Match type must be a valid string.',
            'matchDetails.noOfOvers.numeric' => 'Number of overs must be a numeric value.',
            'matchDetails.noOfOvers.min' => 'Number of overs must be at least 1.',
            'matchDetails.ground.string' => 'Ground name must be a valid string.',
            'matchDetails.dateTime.date' => 'Please provide a valid date and time.',
            'matchDetails.dateTime.after_or_equal' => 'Match date and time must be today or in the future.',
            'matchDetails.tossWinner.required' => 'Please select a team that won the toss.',
            'matchDetails.tossChoice.required' => 'Please select the choice of toss (Bat or Bowl).',
            'matchDetails.tossChoice.in' => 'Toss choice must be either "bat" or "bowl".',
        ]);


        $tossWinnerId = $validatedData['matchDetails']['tossWinner'];
        $tossChoice = $validatedData['matchDetails']['tossChoice'];

        $battingTeamId = $tossChoice === 'bat' ? $tossWinnerId : ($tossWinnerId == $request->teamA['team_id'] ? $request->teamB['team_id'] : $request->teamA['team_id']);
        $bowlingTeamId = $battingTeamId == $request->teamA['team_id'] ? $request->teamB['team_id'] : $request->teamA['team_id'];

        $matchGame = MatchGame::create([
            'tournament_id' => $validatedData['matchDetails']['tournament_id'],
            'schedule_match_id' => $validatedData['matchDetails']['schedule_match_id'],
            'round_id' => $validatedData['matchDetails']['round_id'],
            'overs_per_bowler' => $validatedData['matchDetails']['overs_per_bowler'],
            'group_id' => $validatedData['matchDetails']['group_id'],
            'teamA_id' => $request->teamA['team_id'],
            'teamB_id' => $request->teamB['team_id'],
            'venue' => $validatedData['matchDetails']['ground'] ?? null,
            'match_date_time' => $validatedData['matchDetails']['dateTime'] ?? null,
            'type' => $validatedData['matchDetails']['matchType'],
            'overs' => $validatedData['matchDetails']['noOfOvers'] ?? null,
            'first_umpire' => $request['matchDetails']['umpires']['firstUmpire'] ?? null,
            'second_umpire' => $request['matchDetails']['umpires']['secondUmpire'] ?? null,
            'third_umpire' => $request['matchDetails']['umpires']['thirdUmpire'] ?? null,
            'first_scorer' => $request['matchDetails']['scorers']['firstScorer'] ?? null,
            'second_scorer' => $request['matchDetails']['scorers']['secondScorer'] ?? null,
            'toss' => $tossWinnerId,
            'batting' => $battingTeamId,
            'bowling' => $bowlingTeamId,
            'status' => 'Active',
        ]);

           $schedulematch = ScheduleMatch::where('id', $validatedData['matchDetails']['schedule_match_id'])
          ->update(['status' => 'Active']);


        foreach (['teamA', 'teamB'] as $teamKey) {
            $teamDetailsClass = $teamKey === 'teamA' ? Team1Detail::class : Team2Detail::class;

            foreach ($request->{$teamKey}['players'] as $player) {
                $teamDetailsClass::create([
                    'match_id' => $matchGame->id,
                    'team_id' => $request->{$teamKey}['team_id'],
                    'player_id' => $player['id'],
                    'captain' => $player['id'] == $request->{$teamKey}['captain'] ? 1 : 0,
                    'wicketkeeper' => $player['id'] == $request->{$teamKey}['keeper'] ? 1 : 0,
                    '12th_man' => in_array($player['id'], $request->{$teamKey}['twelfthMen']) ? 1 : 0,
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Match started successfully!', 'match_id' => $matchGame->id]);
    }


 public function match_view(Request $request)
{
    $apiData = Cache::remember('match_fixtures_api_data', 300, function () {
        $matches = new MatchesController();
        return $matches->MatchFixtures()->getData();
    });

    // Convert to collections
    $matches     = collect($apiData->matchFixtures);
    $tournaments = collect($apiData->getSeasons);
    $venues      = collect($apiData->Venues);
    $teams       = collect($apiData->Teams);

    // Filters
    $selectedTournamentId = $request->get('tournament_id');
    if ($selectedTournamentId === '') {
    $selectedTournamentId = null;
   }
    $selectedTeamId       = $request->get('team_id');
    $selectedVenueId      = $request->get('venue_id');
    $selectedStatus       = $request->get('status');
    $selectedGroupId      = $request->get('group_id');
    $selectedRoundId      = $request->get('round_id'); // ✅ no default, stays null until chosen
// Only auto-select if NO filters are applied at all
    if (!$request->has('tournament_id') && !$request->hasAny(['team_id', 'venue_id', 'status', 'group_id', 'round_id']) && $tournaments->isNotEmpty()) {
        $selectedTournamentId = $tournaments->sortByDesc('start_date')->first()->id;
    }
    // ✅ Groups (filter by tournament if selected)
    $groupsQuery = DB::table('groups')
        ->select('id', 'group_name', 'tournament_id')
        ->whereNull('deleted_at');

    if ($selectedTournamentId) {
        $groupsQuery->where('tournament_id', $selectedTournamentId);
    }

    $groups = $groupsQuery->get()->keyBy('id');

    // ✅ Rounds (filter by tournament if selected)
    $roundsQuery = DB::table('tournament_rounds')
        ->select('id', 'type', 'tournament_id')
        ->whereNull('deleted_at')
        ->orderByDesc('id');

    if ($selectedTournamentId) {
        $roundsQuery->where('tournament_id', $selectedTournamentId);
    }

    $rounds = $roundsQuery->get()->keyBy('id');

    // ✅ FIXED: Build teams from matches instead of team.tournament_id
    if ($selectedTournamentId) {

        $teamIds = $matches
            ->where('tournament_id', (int)$selectedTournamentId)
            ->flatMap(function ($match) {
                return [
                    (int)$match->team1,
                    (int)$match->team2
                ];
            })
            ->unique()
            ->values();

        $teams = $teams->filter(function ($team) use ($teamIds) {
            return $teamIds->contains((int)$team->id);
        })->values();
    }


    // ✅ Optional: preload selected venue
    $selectedVenue = $selectedVenueId
        ? $venues->firstWhere('id', (int) $selectedVenueId)
        : null;

    // ✅ Filter matches
    $filteredMatches = $matches->filter(function ($match) use (
        $selectedTournamentId, $selectedTeamId, $selectedVenue, $selectedStatus,
        $selectedGroupId, $selectedRoundId
    ) {
        $match = (array) $match;

        if ($selectedStatus) {
            if (
                ($selectedStatus === 'Live' && $match['status'] !== 'active') ||
                ($selectedStatus === 'Completed' && !in_array($match['status'], ['completed', 'canceled'])) ||
                ($selectedStatus === 'Upcoming' && $match['status'] !== 'scheduled')
            ) {
                return false;
            }
        }

        if ($selectedTournamentId && $match['tournament_id'] != $selectedTournamentId) {
            return false;
        }

        if ($selectedTeamId && !in_array((int)$selectedTeamId, [
            (int)$match['team1'],
            (int)$match['team2']
        ])) {
            return false;
        }

        if ($selectedVenue && !str_contains(strtolower($match['ground']), strtolower($selectedVenue->name))) {
            return false;
        }

        if ($selectedGroupId && $match['group_id'] != $selectedGroupId) {
            return false;
        }

        if ($selectedRoundId && $match['round_id'] != $selectedRoundId) {
            return false;
        }

        return true;
    });

    // ✅ Sort matches
    $statusPriority = ['active' => 1, 'scheduled' => 2, 'completed' => 3, 'canceled' => 3];

    $filteredMatches = $filteredMatches->sortBy(function ($match) use ($statusPriority) {
        $status = $match->status;
        $priority = $statusPriority[$status] ?? 4;

        switch ($status) {
            case 'active':
                $timestamp = -Carbon::parse($match->match_date_time)->timestamp;
                break;
            case 'scheduled':
                $timestamp = Carbon::parse($match->match_date_time)->timestamp;
                break;
            case 'completed':
            case 'canceled':
                $timestamp = -Carbon::parse($match->updated_at)->timestamp;
                break;
            default:
                $timestamp = Carbon::parse($match->match_date_time)->timestamp;
        }

        return [$priority, $timestamp];
    });

    return view('frontend.matches', compact(
        'filteredMatches',
        'tournaments',
        'venues',
        'groups',
        'rounds',
        'teams',
        'selectedTournamentId',
        'selectedTeamId',
        'selectedVenueId',
        'selectedStatus',
        'selectedGroupId',
        'selectedRoundId'
    ));
}


public function match_details($id)
{
    $match = ScheduleMatch::with(['teamOne', 'teamTwo', 'venue', 'tournament'])->findOrFail($id);

    $ScheduleMatch = ScheduleMatch::find($id);
    $liveMatch = MatchGame::where('matches.schedule_match_id', $id)
    ->leftJoin('tournaments', 'matches.tournament_id', '=', 'tournaments.id')
    ->leftJoin('match_scores as team1_score', function($join){
        $join->on('team1_score.team_id', '=' , 'matches.teamA_id')
        ->where('team1_score.match_id', '=', 'matches.id');
    })
    ->leftJoin('match_scores as team2_score', function($join){
        $join->on('team2_score.team_id', '=' , 'matches.teamA_id')
        ->where('team2_score.match_id', '=', 'matches.id');
    })
    ->select(
        'matches.*',
        'tournaments.name as tournament_name',
        'team1_score.total_runs as team1_score',
        'team2_score.total_runs as team2_score',
    )
    ->first();

    $teamAPlayers = Player::where('team_id', $match->team1)->get();
    $teamBPlayers = Player::where('team_id', $match->team2)->get();

    $playing11TeamA = collect();
    $playing11TeamB = collect();
    $benchTeamA = collect();
    $benchTeamB = collect();
    $umpires = collect();
    $scorers = collect();
    $tossWinnerName = null;
    $tournamentName = null;
    $groundName = null;

    if ($liveMatch) {
        $team1PlayerIds = Team1Detail::where('match_id', $liveMatch->id)
            ->where('team_id', $ScheduleMatch->team1)
            ->where('12th_man', '0')
            ->pluck('player_id')
            ->toArray();

        $team2PlayerIds = Team2Detail::where('match_id', $liveMatch->id)
            ->where('team_id', $ScheduleMatch->team2)
            ->where('12th_man', '0')
            ->pluck('player_id')
            ->toArray();

        $playing11TeamA = Player::whereIn('id', $team1PlayerIds)->get();
        $playing11TeamB = Player::whereIn('id', $team2PlayerIds)->get();

        $benchTeamAplayers = Team1Detail::where('match_id', $liveMatch->id)
            ->where('team_id', $ScheduleMatch->team1)
            ->where('12th_man', '1')
            ->pluck('player_id')
            ->toArray();
        $benchTeamBplayers = Team2Detail::where('match_id', $liveMatch->id)
            ->where('team_id', $ScheduleMatch->team2)
            ->where('12th_man', '1')
            ->pluck('player_id')
            ->toArray();

        $benchTeamA = Player::whereIn('id', $benchTeamAplayers)->get();
        $benchTeamB = Player::whereIn('id', $benchTeamBplayers)->get();

        $umpireIds = array_filter([$liveMatch->first_umpire, $liveMatch->second_umpire, $liveMatch->third_umpire]);
        $scorerIds = array_filter([$liveMatch->first_scorer, $liveMatch->second_scorer]);

        $umpires = [
    'first_umpire' => $liveMatch->first_umpire,
    'second_umpire' => $liveMatch->second_umpire,
    'third_umpire' => $liveMatch->third_umpire,
];

$scorers = [
    'first_scorer' => $liveMatch->first_scorer,
    'second_scorer' => $liveMatch->second_scorer,
];

        $tossWinner = Team::find($liveMatch->toss);
        if ($tossWinner) {
            $tossWinnerName = $tossWinner->name;
        }
    }

    if ($match->tournament_id) {
        $tournament = Tournament::find($match->tournament_id);
        if ($tournament) {
            $tournamentName = $tournament->name;

            // Fetch all matches for the tournament ordered by creation date
            $matchesInTournament = ScheduleMatch::where('tournament_id', $match->tournament_id)
                ->orderBy('created_at')
                ->get();

            // Find the current match's position in the tournament
            $matchNumber = $matchesInTournament->search(function ($item) use ($match) {
                return $item->id === $match->id;
            }) + 1; // Adding 1 because search() returns a zero-based index
        }
    }

    if ($match->ground) {
        $ground = Venue::find($match->ground);
        if ($ground) {
            $groundName = $ground->name;
        }
    }
$matchResult = null;
$teamOneScore = null;
$teamTwoScore = null;

if ($liveMatch) {
    $teamOneScore = MatchScore::where('match_id', $liveMatch->id)->where('team_id', $match->team1)->first();
    $teamTwoScore = MatchScore::where('match_id', $liveMatch->id)->where('team_id', $match->team2)->first();
}


$teamOneover ='0/0';
  $teamTwoover = '0/0';
if ($liveMatch) {
    // Get Team 1's total score and overs
    $teamOneData = BallByBall::
        where('match_id', $liveMatch->id)
        ->where('batting_team_id', $match->team1)
      	->where('over_number', '!=', -1)
        ->orderBy('id', 'desc')
        ->first();

    // Get Team 2's total score and overs
    $teamTwoData = BallByBall::
        where('match_id', $liveMatch->id)
        ->where('batting_team_id', $match->team2)
      	->where('over_number', '!=', -1)
        ->orderBy('id', 'desc')
        ->first();
  if(isset($teamOneData)){
    $teamOneover = "$teamOneData->total_overs/$liveMatch->overs";
  }
  if(isset($teamTwoData)) {
    $teamTwoover = "$teamTwoData->total_overs/$liveMatch->overs";
  }
}


if ($teamOneScore && $teamTwoScore) {
    if ($teamOneScore->total_runs == $teamTwoScore->total_runs) {
        // Match Draw
        $matchResult = "The match between {$match->teamOne->name} and {$match->teamTwo->name} ended in a draw.";
    } elseif ($teamOneScore->is_first_inning == 1 && $teamTwoScore->is_second_inning == 1) {
        if ($teamOneScore->total_runs > $teamTwoScore->total_runs) {
            // Team One wins by run margin
            $runDifference = $teamOneScore->total_runs - $teamTwoScore->total_runs;
            $matchResult = "{$match->teamOne->name} beat {$match->teamTwo->name} by {$runDifference} runs.";
        } else {
            // Team Two wins by wickets remaining
            $wicketsRemaining = 10 - $teamTwoScore->total_wickets;
            $matchResult = "{$match->teamTwo->name} beat {$match->teamOne->name} by {$wicketsRemaining} wickets.";
        }
    } elseif ($teamTwoScore->is_first_inning == 1 && $teamOneScore->is_second_inning == 1) {
        if ($teamTwoScore->total_runs > $teamOneScore->total_runs) {
            // Team Two wins by run margin
            $runDifference = $teamTwoScore->total_runs - $teamOneScore->total_runs;
            $matchResult = "{$match->teamTwo->name} beat {$match->teamOne->name} by {$runDifference} runs.";
        } else {
            // Team One wins by wickets remaining
            $wicketsRemaining = 10 - $teamOneScore->total_wickets;
            $matchResult = "{$match->teamOne->name} beat {$match->teamTwo->name} by {$wicketsRemaining} wickets.";
        }
    }
  }
  $apiUrl = (string) $this->API_URL;
  $socketUrl = $this->SOCKET_URL;
    return view('frontend.match-centre', compact(
        'match',
        'liveMatch',
        'playing11TeamA',
        'teamAPlayers',
        'teamBPlayers',
        'playing11TeamB',
        'benchTeamA',
        'benchTeamB',
        'umpires',
        'scorers',
        'tossWinnerName',
        'tournamentName',
        'groundName',
        'matchResult',
        'teamOneover',
        'teamTwoover',
        'apiUrl',
        'socketUrl',
    ));
}
  
  public function confirm_fixtures(Request $request)
{
   $apiUrl = 'https://pitchburners.com/api/Match/Fixtures';

   // return response()->json(['url' => $apiUrl]);
   // Fetch API data
   // $response = Http::get($apiUrl);

   // if ($response->successful()) {
   //     $apiData = $response->json(); // Decode the API response
   // } else {
   //     abort(500, 'Failed to fetch match data from the API.');
   // }

   $matches = new MatchesController();
   $apiData = $matches->webMatchFixtures(['hide_match' => 0])->getData();

   // return $apiData->matchFixtures;
   // Extract necessary data
   $matches = collect($apiData->matchFixtures);
   $tournaments = collect($apiData->getSeasons);
   $venues = collect($apiData->Venues);
   $teams = collect($apiData->Teams);

   // Selected Filters
   $selectedTournamentId = $request->get('tournament_id');
   $selectedTeamId = $request->get('team_id');
   $selectedVenueId = $request->get('venue_id');
   $selectedStatus = $request->get('status'); // Default to 'Live'

   // Filter matches based on selected filters
   $filteredMatches = $matches->filter(function ($match) use ($selectedTournamentId, $selectedTeamId, $selectedVenueId, $selectedStatus,$venues)  {
       $statusMatch = true;
       $tournamentMatch = true;
       $teamMatch = true;
       $venueMatch = true;

       $match = (array) $match;
       // Filter by status
       if ($selectedStatus === 'Live') {
           $statusMatch = $match['status'] === 'active';
       } elseif ($selectedStatus === 'Completed') {
          // $statusMatch = $match['status'] === 'completed';
           $statusMatch = $match['status'] === 'completed' || $match['status'] === 'canceled';
} elseif ($selectedStatus === 'Upcoming') {
           $statusMatch = $match['status'] === 'scheduled';
       }

       // Filter by tournament
       if ($selectedTournamentId) {
           $tournamentMatch = $match['tournament_id'] == $selectedTournamentId;
       }

       // Filter by team
       if ($selectedTeamId) {
           $teamMatch = $match['team1'] == $selectedTeamId || $match['team2'] == $selectedTeamId;
       }

       // Filter by venue
       if ($selectedVenueId) {
           $selectedVenue = $venues->firstWhere('id', (int)$selectedVenueId);
           if ($selectedVenue) {
               $venueMatch = str_contains(
                   strtolower($match['ground']),
                   strtolower($selectedVenue->name)
               );
           }
       }

       return $statusMatch && $tournamentMatch && $teamMatch && $venueMatch;
   });

   $filteredMatches = $filteredMatches->sortBy(function ($match) {
       // Assign priority based on match status
       $statusPriority = [
           'active' => 1,      // Live matches first
           'scheduled' => 2,   // Upcoming matches second
           'completed' => 3,   // Completed matches third
           'canceled' => 3,    // Canceled matches same as completed
       ];

       // Get status priority (default to last if unknown)
       $priority = $statusPriority[$match->status] ?? 4;

       if ($match->status === 'active') {
           // Live Matches → Sort by `match_date_time` in DESC order (latest first)
           $timestamp = -\Carbon\Carbon::parse($match->match_date_time)->timestamp;
       } elseif ($match->status === 'scheduled') {
           // Upcoming Matches → Sort by `match_date_time` in ASC order (earliest first)
           $timestamp = \Carbon\Carbon::parse($match->match_date_time)->timestamp;
       } elseif ($match->status === 'completed' || $match->status === 'canceled') {
           // Completed & Canceled → Sort by `updated_at` in DESC order (latest first)
           $timestamp = -\Carbon\Carbon::parse($match->updated_at)->timestamp;
       } else {
           // Default sorting if status is unknown
           $timestamp = \Carbon\Carbon::parse($match->match_date_time)->timestamp;
       }

       return [$priority, $timestamp];
   });


   // Pass data to the view
   return view('frontend.confirm-fixtures', compact(
       'filteredMatches',
       'tournaments',
       'venues',
       'teams',
       'selectedTournamentId',
       'selectedTeamId',
       'selectedVenueId',
       'selectedStatus'
   ));
}

   public function match_view_insta(Request $request)
     {
        // API URL
        $apiUrl = 'https://pitchburners.com/api/Match/Fixtures';

        // return response()->json(['url' => $apiUrl]);
        // Fetch API data
        // $response = Http::get($apiUrl);

        // if ($response->successful()) {
        //     $apiData = $response->json(); // Decode the API response
        // } else {
        //     abort(500, 'Failed to fetch match data from the API.');
        // }

        $matches = new MatchesController();
        $apiData = $matches->MatchFixtures()->getData();

        // return $apiData->matchFixtures;
        // Extract necessary data
        $matches = collect($apiData->matchFixtures);
        $tournaments = collect($apiData->getSeasons);
        $venues = collect($apiData->Venues);
        $teams = collect($apiData->Teams);
      
       $groups = DB::table('groups')->select('id', 'group_name')
          ->wherenull('deleted_at')
         ->get()->keyBy('id');
     
     $rounds = DB::table('tournament_rounds')
    ->select('id', 'type')
       ->wherenull('deleted_at')
    ->orderBy('id', 'desc') 
    ->get()
    ->keyBy('id');

        // Selected Filters
        $selectedTournamentId = $request->get('tournament_id');
        $selectedTeamId = $request->get('team_id');
        $selectedVenueId = $request->get('venue_id');
        $selectedStatus = $request->get('status'); // Default to 'Live'
      $selectedGroupId = $request->get('group_id');
     $selectedRoundId = $request->get('round_id', $rounds->keys()->first()); 

        // Filter matches based on selected filters
        $filteredMatches = $matches->filter(function ($match) use ($selectedTournamentId, $selectedTeamId, $selectedVenueId, $selectedStatus, $selectedGroupId,$selectedRoundId, $venues) {
            $statusMatch = true;
            $tournamentMatch = true;
            $teamMatch = true;
            $venueMatch = true;
            $groupMatch = true;
          $roundMatch = true;

            $match = (array) $match;
            // Filter by status
            if ($selectedStatus === 'Live') {
                $statusMatch = $match['status'] === 'active';
            } elseif ($selectedStatus === 'Completed') {
               // $statusMatch = $match['status'] === 'completed';
                $statusMatch = $match['status'] === 'completed' || $match['status'] === 'canceled';
     } elseif ($selectedStatus === 'Upcoming') {
                $statusMatch = $match['status'] === 'scheduled';
            }

            // Filter by tournament
            if ($selectedTournamentId) {
                $tournamentMatch = $match['tournament_id'] == $selectedTournamentId;
            }

            // Filter by team
            if ($selectedTeamId) {
                $teamMatch = $match['team1'] == $selectedTeamId || $match['team2'] == $selectedTeamId;
            }

            // Filter by venue
            if ($selectedVenueId) {
            $selectedVenue = $venues->firstWhere('id', (int)$selectedVenueId);
            if ($selectedVenue) {
                $venueMatch = str_contains(
                    strtolower($match['ground']),
                    strtolower($selectedVenue->name)
                );
            }
        }
          
            if ($selectedGroupId) {
            $groupMatch = $match['group_id'] == $selectedGroupId;
        }
           if ($selectedRoundId) {
            $roundMatch = $match['round_id'] == $selectedRoundId;
        }

            return $statusMatch && $tournamentMatch && $teamMatch && $venueMatch && $groupMatch && $roundMatch;
        });
      
     $filteredMatches = $filteredMatches->sortBy(function ($match) {
    // Assign priority based on match status
    $statusPriority = [
        'active' => 1,      // Live matches first
        'scheduled' => 2,   // Upcoming matches second
        'completed' => 3,   // Completed matches third
        'canceled' => 3,    // Canceled matches same as completed
    ];

    // Get status priority (default to last if unknown)
    $priority = $statusPriority[$match->status] ?? 4;

    if ($match->status === 'active') {
        // Live Matches → Sort by `match_date_time` in DESC order (latest first)
        $timestamp = -\Carbon\Carbon::parse($match->match_date_time)->timestamp;
    } elseif ($match->status === 'scheduled') {
        // Upcoming Matches → Sort by `match_date_time` in ASC order (earliest first)
        $timestamp = \Carbon\Carbon::parse($match->match_date_time)->timestamp;
    } elseif ($match->status === 'completed' || $match->status === 'canceled') {
        // Completed & Canceled → Sort by `updated_at` in DESC order (latest first)
        $timestamp = -\Carbon\Carbon::parse($match->updated_at)->timestamp;
    } else {
        // Default sorting if status is unknown
        $timestamp = \Carbon\Carbon::parse($match->match_date_time)->timestamp;
    }

    return [$priority, $timestamp];
});

      //return print_r($filteredMatches);

        // Pass data to the view
        return view('frontend.matches-insta', compact(
            'filteredMatches',
            'tournaments',
            'venues',
          'groups',
          'rounds',
            'teams',
            'selectedTournamentId',
            'selectedTeamId',
            'selectedVenueId',
            'selectedStatus',
          'selectedGroupId',
          'selectedRoundId'
        ));
    }

}
