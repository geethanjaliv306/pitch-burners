<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Mail\ContactUsMail;
use App\Models\AboutUsContent;
use App\Models\ContactUs;
use App\Models\Gallery;
use App\Models\Team;
use App\Models\Group;
use App\Models\BallByBall;
use App\Models\TournamentRound;
use App\Models\OrganizerMember;
use App\Models\MatchGame;
use App\Models\PlayerBattingStats;
use App\Models\PlayerBowlingStats;
use App\Models\Partner;
use App\Models\ScheduleMatch;
use App\Models\Tournament;
use App\Models\TournamentGroup;
use App\Models\Venue;
use App\Models\Winner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
class FrontendDetailsController extends Controller
{
   public function index() {
    $tournaments = DB::table('tournaments')
        ->whereNull('deleted_at')
        ->get();

   // $selectedTournamentId = request('tournament_id', optional($tournaments->first())->id);
    $matchStatus = request('match_status', 'Active');
    $selectedTournamentId = request('tournament_id', DB::table('tournaments')->orderBy('created_at', 'desc')->value('id'));

    $selectedTournament = $selectedTournamentId 
        ? DB::table('tournaments')->where('id', $selectedTournamentId)->first() 
        : null;

    //$selectedTournament = $selectedTournamentId ? DB::table('tournaments')->where('id', $selectedTournamentId)->first() : null;

    $tournamentTeams = $selectedTournamentId ? DB::table('tournament_teams')
        ->join('teams', 'tournament_teams.team_id', '=', 'teams.id')
        ->where('tournament_teams.tournament_id', $selectedTournamentId)
        ->select('teams.id', 'teams.name', 'teams.logo')
        ->get() : collect();
    $teamCount = $tournamentTeams->count();

    $schedule_matches = $selectedTournamentId ? DB::table('schedule_matches')
        ->join('teams as scheduled_team1', 'schedule_matches.team1', '=', 'scheduled_team1.id')
        ->join('teams as scheduled_team2', 'schedule_matches.team2', '=', 'scheduled_team2.id')
        ->join('venues', 'schedule_matches.ground', '=', 'venues.id')
        ->leftJoin('groups', 'schedule_matches.group_id', '=', 'groups.id')
        ->leftJoin('matches', 'schedule_matches.id', '=', 'matches.schedule_match_id')
        ->leftJoin('teams as match_team1', 'matches.teamA_id', '=', 'match_team1.id')
        ->leftJoin('teams as match_team2', 'matches.teamB_id', '=', 'match_team2.id')
        ->leftJoin('teams as match_toss_win', 'matches.toss', '=', 'match_toss_win.id')
        ->where('schedule_matches.tournament_id', $selectedTournamentId)
        ->whereNull('schedule_matches.deleted_at')
        ->where(function($query) use ($matchStatus) {
            if ($matchStatus === 'Active') {
                $query->where(function($q) {
                    $q->where('matches.status', 'Active')
                      ->orWhereNull('matches.status');
                });
            } else {
                $query->where('matches.status', 'Completed');
            }
        })
        ->orderByRaw("CASE WHEN matches.status = 'Active' THEN 1 ELSE 2 END")
         ->orderBy('schedule_matches.match_date_time', 'asc') 
        ->select(
            'schedule_matches.*',
            'scheduled_team1.name as scheduled_team1_name',
            'scheduled_team2.name as scheduled_team2_name',
            'venues.name as ground_name',
            'groups.group_name as group_name',
            'matches.status as match_status',
            DB::raw('IFNULL(match_team1.name, scheduled_team1.name) as match_team1_name'),
            DB::raw('IFNULL(match_team2.name, scheduled_team2.name) as match_team2_name'),
            DB::raw('COALESCE(match_team1.logo, scheduled_team1.logo) as team1_logo'),
            DB::raw('COALESCE(match_team2.logo, scheduled_team2.logo) as team2_logo'),
            'match_toss_win.name as toss_win',
            'matches.batting as batting_team_name',
            'matches.id as match_id'
        )
        ->get()
         : collect();

    // Calculate match results for completed matches
    foreach ($schedule_matches as $match) {
        $matchResult = null;
        $teamOneScore = DB::table('match_scores')->where('match_id', $match->match_id)->where('team_id', $match->team1)->first();
        $teamTwoScore = DB::table('match_scores')->where('match_id', $match->match_id)->where('team_id', $match->team2)->first();

        if ($teamOneScore && $teamTwoScore) {
            if ($teamOneScore->total_runs == $teamTwoScore->total_runs) {
                $matchResult = "The match between {$match->scheduled_team1_name} and {$match->scheduled_team2_name} ended in a draw.";
            } elseif ($teamOneScore->is_first_inning == 1 && $teamTwoScore->is_second_inning == 1) {
                if ($teamOneScore->total_runs > $teamTwoScore->total_runs) {
                    $runDifference = $teamOneScore->total_runs - $teamTwoScore->total_runs;
                    $matchResult = "{$match->scheduled_team1_name} beat {$match->scheduled_team2_name} by {$runDifference} runs.";
                } else {
                    $wicketsRemaining = 10 - $teamTwoScore->total_wickets;
                    $matchResult = "{$match->scheduled_team2_name} beat {$match->scheduled_team1_name} by {$wicketsRemaining} wickets.";
                }
            } elseif ($teamTwoScore->is_first_inning == 1 && $teamOneScore->is_second_inning == 1) {
                if ($teamTwoScore->total_runs > $teamOneScore->total_runs) {
                    $runDifference = $teamTwoScore->total_runs - $teamOneScore->total_runs;
                    $matchResult = "{$match->scheduled_team2_name} beat {$match->scheduled_team1_name} by {$runDifference} runs.";
                } else {
                    $wicketsRemaining = 10 - $teamOneScore->total_wickets;
                    $matchResult = "{$match->scheduled_team1_name} beat {$match->scheduled_team2_name} by {$wicketsRemaining} wickets.";
                }
            }
        }

        $match->match_result = $matchResult;
    }
    
    foreach ($schedule_matches as $match) {
        if ($match->match_id) {
            // Team 1 data
        $team1Ongoing = BallByBall::where('match_id', $match->match_id)
            ->where('batting_team_id', $match->team1)
            ->where('innings_completed', 0)
            ->orderBy('id', 'desc')
            ->first();

        $team1Completed = BallByBall::where('match_id', $match->match_id)
            ->where('batting_team_id', $match->team1)
            ->where('innings_completed', 1)
            ->orderBy('id', 'desc')
            ->first();

        if ($team1Ongoing) {
            // Show ongoing innings for Team 1
            $match->team1Score = $team1Ongoing->total_score ?? 0;
            $match->team1Wickets = $team1Ongoing->total_wickets ?? 0;
            $match->team1Overs = $team1Ongoing->total_overs ?? 0;
        } elseif ($team1Completed) {
            // Show completed innings for Team 1
            $match->team1Score = $team1Completed->total_score ?? 0;
            $match->team1Wickets = $team1Completed->total_wickets ?? 0;
            $match->team1Overs = $team1Completed->total_overs ?? 0;
        } else {
            // Default values if no data found
            $match->team1Score = 0;
            $match->team1Wickets = 0;
            $match->team1Overs = 0;
        }

        // Team 2 data
        $team2Ongoing = BallByBall::where('match_id', $match->match_id)
            ->where('batting_team_id', $match->team2)
            ->where('innings_completed', 0)
            ->orderBy('id', 'desc')
            ->first();

        $team2Completed = BallByBall::where('match_id', $match->match_id)
            ->where('batting_team_id', $match->team2)
            ->where('innings_completed', 1)
            ->orderBy('id', 'desc')
            ->first();

        if ($team2Ongoing) {
            // Show ongoing innings for Team 2
            $match->team2Score = $team2Ongoing->total_score ?? 0;
            $match->team2Wickets = $team2Ongoing->total_wickets ?? 0;
            $match->team2Overs = $team2Ongoing->total_overs ?? 0;
        } elseif ($team2Completed) {
            // Show completed innings for Team 2
            $match->team2Score = $team2Completed->total_score ?? 0;
            $match->team2Wickets = $team2Completed->total_wickets ?? 0;
            $match->team2Overs = $team2Completed->total_overs ?? 0;
        } else {
            // Default values if no data found
            $match->team2Score = 0;
            $match->team2Wickets = 0;
            $match->team2Overs = 0;
        }
    } else {
        // Default values when no live match
        $match->team1Score = 0;
        $match->team1Wickets = 0;
        $match->team1Overs = 0;
        $match->team2Score = 0;
        $match->team2Wickets = 0;
        $match->team2Overs = 0;
    }
    }

    $matchCount = $schedule_matches->count();

    return view('frontend.index', compact('tournaments', 'selectedTournament', 'schedule_matches', 'tournamentTeams', 'teamCount', 'matchCount', 'matchStatus'));
}


public function standings_view(Request $request)
{
    // Fetch the first tournament if it exists, or set $tournamentId to null if no tournaments are available
    $tournament = Tournament::first();
    $tournamentId = $request->get('tournament_id') ?? ($tournament ? $tournament->id : null);

    // Get all tournaments
    $tournaments = Tournament::all();

    // Initialize variables
    $groups = collect();
    $rounds = collect();
    $groupTeams = [];
    $selectedGroupId = $request->get('group_id'); // Capture selected group ID
    $selectedRoundId = $request->get('round_id'); // Capture selected round ID

    if ($tournamentId) {
        // Fetch rounds for the selected tournament
        $rounds = TournamentRound::where('tournament_id', $tournamentId)->get();

        // Filter groups by selected round ID, if provided
        $groupsQuery = Group::whereIn('id', function ($query) use ($tournamentId) {
            $query->select('group_id')
                  ->from('tournament_groups')
                  ->where('tournament_id', $tournamentId);
        });

        if ($selectedRoundId) {
            $groupsQuery->whereHas('rounds', function ($query) use ($selectedRoundId) {
                $query->where('round_id', $selectedRoundId);
            });
        }

        $groups = $groupsQuery->get();

        foreach ($groups as $group) {
            if ($selectedGroupId && $group->id != $selectedGroupId) {
                continue; // Skip groups not matching the selected group ID
            }

            $teamIds = TournamentGroup::where('tournament_id', $tournamentId)
                                      ->where('group_id', $group->id)
                                      ->pluck('team_id');

            // Fetch all teams in the group, including those without points
            $groupTeams[$group->id] = Team::whereIn('teams.id', $teamIds)
                                          ->leftJoin('points', function ($join) use ($group, $tournamentId, $selectedRoundId) {
                                              $join->on('teams.id', '=', 'points.team_id')
                                                   ->where('points.group_id', $group->id)
                                                   ->where('points.tournament_id', $tournamentId);
                                              if ($selectedRoundId) {
                                                  $join->where('points.round_id', $selectedRoundId);
                                              }
                                          })
                                          ->select(
                                              'teams.*',
                                              DB::raw('COALESCE(SUM(points.matches_played), 0) as played'),
                                              DB::raw('COALESCE(SUM(points.wins), 0) as wins'),
                                              DB::raw('COALESCE(SUM(points.losses), 0) as losses'),
                                              DB::raw('COALESCE(SUM(points.matches_not_played), 0) as nr'),
                                              DB::raw('COALESCE(SUM(points.matches_tied), 0) as tied'),
                                              DB::raw('COALESCE(AVG(points.net_run_rate), 0) as net_rr'),
                                              DB::raw('COALESCE(SUM(points.total_points), 0) as points')
                                          )
                                          ->groupBy('teams.id') // Ensures unique teams
                                          ->orderBy('points', 'desc')
                                          ->orderBy('net_rr', 'desc')
                                          ->get();
        }
    }

    return view('frontend.standings', compact('tournaments', 'groups', 'groupTeams', 'tournamentId', 'rounds', 'selectedGroupId', 'selectedRoundId'));
}


    public function venues_list()
     {
        $venues = Venue::all();
        return view('frontend.venues',compact('venues'));
    }

      public function stats(Request $request)
    {
        // Fetch available tournaments and teams for the filters
        $tournaments = Tournament::all();
        $teams = Team::all();

        // Initialize empty collections for stats
        $allPlayerStats = collect();
        $allBowlingStats = collect();

        // Get filter values from request
        $tournamentId = $request->get('tournament');
        $playerName = $request->get('player_name');
        $team = $request->get('team');
        $category = $request->get('category', 'Batting'); // Default to Batting

        // Prepare the player stats query based on the category
        if ($category === 'Batting') {
            $subQuery = DB::table('player_batting_stats')
                ->select(
                    'player_id',
                    'match_id',
                    'team_id',
                    DB::raw('MAX(score) as max_score'),
                    DB::raw('MAX(balls_faced) as max_balls'),
                    DB::raw('SUM(four) as max_fours'),
                    DB::raw('SUM(six) as max_sixes'),
                    DB::raw('MAX(is_out) as was_out')
                )
                ->groupBy('player_id', 'match_id', 'team_id');

            if ($tournamentId) {
                $allPlayerStats = DB::table(DB::raw("({$subQuery->toSql()}) as match_stats"))
                    ->mergeBindings($subQuery) // Bindings required for the subquery
                    ->join('players', 'match_stats.player_id', '=', 'players.id')
                    ->join('teams', 'match_stats.team_id', '=', 'teams.id')
                    ->join('tournament_teams', 'match_stats.team_id', '=', 'tournament_teams.team_id')
                    ->where('tournament_teams.tournament_id', $tournamentId)
                     ->when($tournamentId, function ($query) use ($tournamentId) {
            $query->join('tournament_teams as tt', 'match_stats.team_id', '=', 'tt.team_id')
                ->where('tt.tournament_id', $tournamentId);
        })
        ->when($team, function ($query) use ($team) {
            $query->where('teams.name', $team);
        })
        ->when($playerName, function ($query) use ($playerName) {
            $query->where('players.name', 'LIKE', "%$playerName%");
        })
                    ->select(
                        'players.name as player',
                          'players.image as image', 
                        'teams.name as team',
                        DB::raw('SUM(match_stats.max_score) as runs'),
                        DB::raw('COUNT(DISTINCT match_stats.match_id) as matches'),
                        DB::raw('COUNT(DISTINCT match_stats.match_id) as innings'),
                        DB::raw('CASE WHEN SUM(match_stats.was_out) > 0 THEN 0 ELSE 1 END as no'),
                        DB::raw('MAX(match_stats.max_score) as highest'),
                        DB::raw('ROUND(AVG(match_stats.max_score), 2) as avg'),
                        DB::raw('SUM(match_stats.max_balls) as bf'),
                        DB::raw('ROUND(SUM(match_stats.max_score) / SUM(match_stats.max_balls) * 100, 2) as sr'),
                        DB::raw('SUM(CASE WHEN match_stats.max_score >= 100 THEN 1 ELSE 0 END) as hundreds'),
                        DB::raw('SUM(CASE WHEN match_stats.max_score >= 50 AND match_stats.max_score < 100 THEN 1 ELSE 0 END) as fifties'),
                        DB::raw('SUM(match_stats.max_fours) as fours'),
                        DB::raw('SUM(match_stats.max_sixes) as sixes')
                    )
                    ->groupBy('match_stats.player_id', 'players.name', 'teams.name')
                    ->having('runs', '>', 0)
                    ->orderByDesc('runs')
                    ->orderByDesc('sr')
                    ->take(20)
                    ->get();
            } else {
                $allPlayerStats = DB::table(DB::raw("({$subQuery->toSql()}) as match_stats"))
                    ->mergeBindings($subQuery) // Bindings required for the subquery
                    ->join('players', 'match_stats.player_id', '=', 'players.id')
                    ->join('teams', 'match_stats.team_id', '=', 'teams.id')
                     ->when($tournamentId, function ($query) use ($tournamentId) {
            $query->join('tournament_teams as tt', 'match_stats.team_id', '=', 'tt.team_id')
                ->where('tt.tournament_id', $tournamentId);
        })
        ->when($team, function ($query) use ($team) {
            $query->where('teams.name', $team);
        })
        ->when($playerName, function ($query) use ($playerName) {
            $query->where('players.name', 'LIKE', "%$playerName%");
        })
                    ->select(
                        'players.name as player',
                          'players.image as image', 
                        'teams.name as team',
                        DB::raw('SUM(match_stats.max_score) as runs'),
                        DB::raw('COUNT(DISTINCT match_stats.match_id) as matches'),
                        DB::raw('COUNT(DISTINCT match_stats.match_id) as innings'),
                        DB::raw('CASE WHEN SUM(match_stats.was_out) > 0 THEN 0 ELSE 1 END as no'),
                        DB::raw('MAX(match_stats.max_score) as highest'),
                        DB::raw('ROUND(AVG(match_stats.max_score), 2) as avg'),
                        DB::raw('SUM(match_stats.max_balls) as bf'),
                        DB::raw('ROUND(SUM(match_stats.max_score) / SUM(match_stats.max_balls) * 100, 2) as sr'),
                        DB::raw('SUM(CASE WHEN match_stats.max_score >= 100 THEN 1 ELSE 0 END) as hundreds'),
                        DB::raw('SUM(CASE WHEN match_stats.max_score >= 50 AND match_stats.max_score < 100 THEN 1 ELSE 0 END) as fifties'),
                        DB::raw('SUM(match_stats.max_fours) as fours'),
                        DB::raw('SUM(match_stats.max_sixes) as sixes')
                    )
                    ->groupBy('match_stats.player_id', 'players.name', 'teams.name')
                    ->having('runs', '>', 0)
                    ->orderByDesc('runs')
                    ->orderByDesc('sr')
                    ->take(20)
                    ->get();
            }
        } elseif ($category === 'Bowling') {
            $subQuery = DB::table('player_bowling_stats')
                ->select(
                    'player_id',
                    'match_id',
                    'team_id',
                    DB::raw('MAX(wickets_taken) as max_wickets_taken'),
                    DB::raw('MAX(overs_bowled) as max_overs_bowled'),
                    DB::raw('MAX(runs_conceded) as max_runs_conceded')
                )
                ->groupBy('player_id', 'match_id', 'team_id');

            if ($tournamentId) {
                $allBowlingStats = DB::table(DB::raw("({$subQuery->toSql()}) as match_stats"))
                    ->mergeBindings($subQuery) // Bindings required for the subquery
                    ->join('players', 'match_stats.player_id', '=', 'players.id')
                    ->join('teams', 'match_stats.team_id', '=', 'teams.id')
                    ->join('tournament_teams', 'match_stats.team_id', '=', 'tournament_teams.team_id')
                     ->when($tournamentId, function ($query) use ($tournamentId) {
            $query->join('tournament_teams as tt', 'match_stats.team_id', '=', 'tt.team_id')
                ->where('tt.tournament_id', $tournamentId);
        })
        ->when($team, function ($query) use ($team) {
            $query->where('teams.name', $team);
        })
        ->when($playerName, function ($query) use ($playerName) {
            $query->where('players.name', 'LIKE', "%$playerName%");
        })
                    ->where('tournament_teams.tournament_id', $tournamentId)
                    ->select(
                        'players.name as player',
                          'players.image as image', 
                        'teams.name as team',
                        DB::raw('COUNT(DISTINCT match_stats.match_id) as matches'),
                        DB::raw('CAST(SUM(match_stats.max_overs_bowled) AS DECIMAL(10,2)) as overs_bowled'),
                        DB::raw('SUM(match_stats.max_runs_conceded) as runs'),
                        DB::raw('SUM(match_stats.max_wickets_taken) as wickets'),
                        DB::raw('ROUND(SUM(match_stats.max_runs_conceded * 6.0) / SUM(match_stats.max_overs_bowled * 6), 2) as economy'),
                        DB::raw('SUM(CASE WHEN match_stats.max_wickets_taken >= 3 AND match_stats.max_wickets_taken < 5 THEN 1 ELSE 0 END) as threeFer'),
                        DB::raw('SUM(CASE WHEN match_stats.max_wickets_taken >= 5 THEN 1 ELSE 0 END) as fiveFer')
                    )
                    ->groupBy('match_stats.player_id', 'players.name', 'teams.name')
                     ->having('runs', '>', 0)
                    ->orderByDesc('wickets')
                  ->orderBy('economy')
                    ->take(20)
                    ->get();
            } else {
                $allBowlingStats = DB::table(DB::raw("({$subQuery->toSql()}) as match_stats"))
                    ->mergeBindings($subQuery) // Bindings required for the subquery
                    ->join('players', 'match_stats.player_id', '=', 'players.id')
                    ->join('teams', 'match_stats.team_id', '=', 'teams.id')
                     ->when($tournamentId, function ($query) use ($tournamentId) {
            $query->join('tournament_teams as tt', 'match_stats.team_id', '=', 'tt.team_id')
                ->where('tt.tournament_id', $tournamentId);
        })
        ->when($team, function ($query) use ($team) {
            $query->where('teams.name', $team);
        })
        ->when($playerName, function ($query) use ($playerName) {
            $query->where('players.name', 'LIKE', "%$playerName%");
        })
                    ->select(
                        'players.name as player',
                          'players.image as image', 
                        'teams.name as team',
                        DB::raw('COUNT(DISTINCT match_stats.match_id) as matches'),
                        DB::raw('CAST(SUM(match_stats.max_overs_bowled) AS DECIMAL(10,2)) as overs_bowled'),
                        DB::raw('SUM(match_stats.max_runs_conceded) as runs'),
                        DB::raw('SUM(match_stats.max_wickets_taken) as wickets'),
                        DB::raw('ROUND(SUM(match_stats.max_runs_conceded * 6.0) / SUM(match_stats.max_overs_bowled * 6), 2) as economy'),
                        DB::raw('SUM(CASE WHEN match_stats.max_wickets_taken >= 3 AND match_stats.max_wickets_taken < 5 THEN 1 ELSE 0 END) as threeFer'),
                        DB::raw('SUM(CASE WHEN match_stats.max_wickets_taken >= 5 THEN 1 ELSE 0 END) as fiveFer')
                    )
                    ->groupBy('match_stats.player_id', 'players.name', 'teams.name')
                     ->having('runs', '>', 0)
                    ->orderByDesc('wickets')
                  ->orderBy('economy')
                    ->take(20)
                    ->get();
            }
        }

        // Return the view with stats data and filters
        return view('frontend.stats', compact('allPlayerStats', 'allBowlingStats', 'tournaments', 'teams', 'category'));
    }

    public function about_us()
     {
        $content = AboutUsContent::first();
        $organizerMembers = OrganizerMember::get();
        return view('frontend.about-us', compact('content','organizerMembers'));
    }

    public function gallery()
     {
        $galleries = Gallery::all()->groupBy('title');
        return view('frontend.gallery', compact('galleries'));
    }

    public function winners()
    {
        $winners = Winner::with('tournament')->get();
        $tournaments = Tournament::all();
        return view('frontend.winners', compact('winners', 'tournaments'));
    }

    public function partners()
     {
        $partners = Partner::all()->groupBy('title');
        return view('frontend.partners', compact('partners'));
    }

    public function contact()
     {
        return view('frontend.contact');
    }

    public function createContactUs(Request $request) {
        $request->validate([
            'sender_name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|numeric|digits:10',
            'message' => 'required|max:255',
            'preferred_way_to_contact' => 'required',
        ]);
        $name = $request->input('sender_name');
        $email = $request->input('email');
        $phone = $request->input('phone');
        $message = $request->input('message');
        $preferred_way = $request->input('preferred_way_to_contact');
        ContactUs::create([
            'name' => $name,
            'email' => $email,
            'phone' => (integer) $phone,
            'message' => $message,
            'preferred_way_to_contact' => $preferred_way
        ]);
        Mail::to('ranjith1012@yopmail.com')->send(new ContactUsMail(
            $name,
            $email,
            $phone,
            $message,
            $preferred_way,
        ));
        return redirect()->back()->with('success', "Thanks for contacting us, Team will contact  soon");
    }
  
    public function comingsoon(){

          return view('admin.comingsoon');
      }
}
