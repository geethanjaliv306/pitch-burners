<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Mail\ContactUsMail;
use App\Models\AboutUsContent;
use App\Models\ContactUs;
use App\Models\Gallery;
use App\Models\Team;
use App\Models\Group;
use App\Models\Point;
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
	$partners = Partner::all();
   // $selectedTournamentId = request('tournament_id', optional($tournaments->first())->id);
    $matchStatus = request('match_status', 'Active');
   //$selectedTournamentId = request('tournament_id', DB::table('tournaments')->orderBy('created_at', 'asc')->value('id'));
      // Get latest tournament
      $latestTournamentId = DB::table('tournaments')
          ->whereNull('deleted_at')
          ->orderBy('created_at', 'desc')
          ->value('id');

      // Use selected tournament OR latest by default
      $selectedTournamentId = request('tournament_id', $latestTournamentId);

   // $selectedTournament = $selectedTournamentId
       // ? DB::table('tournaments')->where('id', $selectedTournamentId)->first()
       // : null;
      $selectedTournament = DB::table('tournaments')
          ->where('id', $selectedTournamentId)
          ->whereNull('deleted_at')
          ->first();

     if ($selectedTournamentId) {
        // If filtered, get that tournament
        $selectedTournament = DB::table('tournaments')
            ->where('id', $selectedTournamentId)
            ->whereNull('deleted_at')
            ->first();
    } else {
        // Else get the most recent tournament
        $selectedTournament = DB::table('tournaments')
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->first();
    }
    //$selectedTournament = $selectedTournamentId ? DB::table('tournaments')->where('id', $selectedTournamentId)->first() : null;

    $tournamentTeams = $selectedTournamentId ? DB::table('tournament_teams')
        ->join('teams', 'tournament_teams.team_id', '=', 'teams.id')
       ->whereNull('tournament_teams.deleted_at')
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
      ->whereNull('matches.deleted_at')
       ->where('schedule_matches.status', '!=', 'Scheduled')
        ->where(function($query) use ($matchStatus) {
            if ($matchStatus === 'Active') {
                $query->where(function($q) {
                    $q->where('matches.status', 'Active')
                      ->orWhereNull('matches.status');
                });
            } else {
               $query->where(function($q) {
                    $q->where('matches.status', 'Completed')
                      ->orWhere('matches.status', 'Canceled');
                });
            }
        })
        ->orderByRaw("CASE WHEN matches.status = 'Active' THEN 1 ELSE 2 END")
         ->orderBy('schedule_matches.match_date_time', 'desc')
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
       'match_toss_win.id as toss',
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
      
       $matchDetails = DB::table('matches')->where('schedule_match_id', $match->id)->first();
      if ($matchDetails) {
        $matchResult = "{$matchDetails->match_details}";
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

    return view('frontend.index', compact('tournaments', 'selectedTournament', 'schedule_matches', 'tournamentTeams', 'teamCount', 'matchCount', 'matchStatus','partners'));
}

public function standings_view(Request $request)
{
    // 1. Get latest tournament (default)
    $latestTournamentId = Tournament::whereNull('deleted_at')
        ->orderBy('created_at', 'desc')
        ->value('id');

    // 2. Use selected tournament OR latest
    $tournamentId = $request->get('tournament_id', $latestTournamentId);

    $roundId = $request->get('round_id');
    $teamId = $request->get('team_id');
    $selectedGroupId = $request->get('group_id');

    // 3. Tournaments for dropdown (latest first)
    $tournaments = Tournament::whereNull('deleted_at')
        ->orderBy('created_at', 'desc')
        ->get();

    $groups = collect();
    $rounds = collect();
    $groupTeams = [];
    $teams = collect();

    if ($tournamentId) {

        /** ---------------- GROUPS ---------------- */
        $groupsQuery = Group::whereIn('id', function ($query) use ($tournamentId, $roundId) {
            $query->select('group_id')
                ->from('tournament_groups')
                ->where('tournament_id', $tournamentId)
                ->where('round_id', '!=', 7);

            if ($roundId) {
                $query->where('round_id', $roundId);
            }
        });

        if ($teamId) {
            $groupsQuery->whereIn('id', function ($query) use ($tournamentId, $teamId, $roundId) {
                $query->select('group_id')
                    ->from('tournament_groups')
                    ->where('tournament_id', $tournamentId)
                    ->where('team_id', $teamId)
                    ->where('round_id', '!=', 7);

                if ($roundId) {
                    $query->where('round_id', $roundId);
                }
            });
        }

        $groups = $groupsQuery->get();

        /** ---------------- TEAMS ---------------- */
        $teamsQuery = Team::whereIn('id', function ($query) use ($tournamentId, $roundId) {
            $query->select('team_id')
                ->from('tournament_groups')
                ->where('tournament_id', $tournamentId)
                ->where('round_id', '!=', 7);

            if ($roundId) {
                $query->where('round_id', $roundId);
            }
        });

        if ($teamId) {
            $teamsQuery->where('id', $teamId);
        }

        $teams = $teamsQuery->get();

        /** ---------------- ROUNDS ---------------- */
        $rounds = TournamentRound::where('tournament_id', $tournamentId)
            ->where('id', '!=', 7)
            ->get();

        /** ---------------- GROUP TEAMS + POINTS ---------------- */
        foreach ($groups as $group) {

            if ($selectedGroupId && $group->id != $selectedGroupId) {
                continue;
            }

            $teamIdsQuery = TournamentGroup::where('tournament_id', $tournamentId)
                ->where('group_id', $group->id)
                ->where('round_id', '!=', 7);

            if ($roundId) {
                $teamIdsQuery->where('round_id', $roundId);
            }

            $teamIds = $teamIdsQuery->pluck('team_id');

            // Teams to qualify
            $teamsToQualify = null;

            $point = Point::where('group_id', $group->id)->first();
            if ($point) {
                $round = TournamentRound::find($point->round_id);

                if (
                    $round &&
                    ($round->status == 1 || $round->status == 0) &&
                    $round->id != 7
                ) {
                    $teamsToQualify = $round->teams_to_qualify;
                }
            }

            $groupTeams[$group->id] = Team::whereIn('teams.id', $teamIds)
                ->leftJoin('points', function ($join) use ($group, $tournamentId) {
                    $join->on('teams.id', '=', 'points.team_id')
                        ->where('points.group_id', $group->id)
                        ->where('points.tournament_id', $tournamentId);
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
                ->groupBy('teams.id')
                ->orderBy('points', 'desc')
                ->orderBy('net_rr', 'desc')
                ->get();

            $group->teams_to_qualify = $teamsToQualify;
        }
    }

    return view('frontend.standings', compact(
        'tournaments',
        'teams',
        'groups',
        'groupTeams',
        'tournamentId',
        'rounds',
        'selectedGroupId',
        'teamId',
        'roundId'
    ));
}


    public function venues_list()
     {
        $venues = Venue::all();
        return view('frontend.venues',compact('venues'));
    }
  
public function stats(Request $request)
{
    // Get ball type - only use default if not explicitly set to empty
    $ballType = $request->has('ball_type') ? $request->get('ball_type') : 'Red Tennis';

    // Fetch tournaments
    $tournaments = Tournament::whereNull('deleted_at')
        ->when($ballType, function($q) use ($ballType) {
            $q->where('ball_type', $ballType);
        })
        ->orderBy('start_date', 'desc')
        ->get();

    // Fetch teams
    $teams = Team::whereExists(function ($q) use ($ballType) {
        $q->select(DB::raw(1))
          ->from('tournament_teams')
          ->join('tournaments', 'tournaments.id', '=', 'tournament_teams.tournament_id')
          ->whereColumn('teams.id', 'tournament_teams.team_id')
          ->when($ballType, function($query) use ($ballType) {
              $query->where('tournaments.ball_type', $ballType);
          });
    })->orderBy('teams.name')->get();

    // Initialize empty collections for stats
    $allPlayerStats = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
    $allBowlingStats = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
    $topBatsman = null;
    $topBowler = null;

    // Get filter values from request
    $tournamentId = $request->get('tournament');
    $playerName = $request->get('player_name');
    $team = $request->get('team');
    $category = $request->get('category', 'Batting'); // Default to Batting

    if ($category === 'Batting') {
        $subQuery = DB::table('scoreboards as sc')
            ->join('matches', 'sc.match_id', '=', 'matches.id')
            ->join('tournaments', 'tournaments.id', '=', 'matches.tournament_id')
            ->select(
                'sc.batter_id as player_id',
                'sc.match_id',
                'sc.team_id',
                DB::raw('SUM(sc.runs) as total_runs'),
                DB::raw('SUM(sc.balls_faced) as total_balls'),
                DB::raw('SUM(sc.fours) as total_fours'),
                DB::raw('SUM(sc.sixes) as total_sixes'),
                DB::raw('MAX(sc.is_out) as was_out')
            )
            ->whereIn('sc.inning', [0, 1])
            ->when($tournamentId, fn ($q) =>
                $q->where('matches.tournament_id', $tournamentId)
            )
            ->when($ballType, fn ($q) =>
                $q->where('tournaments.ball_type', $ballType)
            )
            ->groupBy('sc.batter_id', 'sc.match_id', 'sc.team_id');

        $allPlayerStats = DB::table(DB::raw("({$subQuery->toSql()}) as match_stats"))
            ->mergeBindings($subQuery)
            ->join('players', 'match_stats.player_id', '=', 'players.id')
            ->join('teams', 'match_stats.team_id', '=', 'teams.id')
            ->join('matches', 'match_stats.match_id', '=', 'matches.id')
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
                'teams.logo as logo',
                DB::raw('SUM(match_stats.total_runs) as runs'),
                DB::raw('COUNT(DISTINCT match_stats.match_id) as matches'),
                DB::raw('COUNT(DISTINCT match_stats.match_id) as innings'),
                DB::raw('CASE WHEN SUM(match_stats.was_out) > 0 THEN 0 ELSE 1 END as no'),
                DB::raw('MAX(match_stats.total_runs) as highest'),
                DB::raw('ROUND(
                    CASE
                        WHEN SUM(match_stats.was_out) = 0 THEN SUM(match_stats.total_runs)
                        ELSE SUM(match_stats.total_runs) / SUM(match_stats.was_out)
                    END, 2) as avg'),
                DB::raw('SUM(match_stats.total_balls) as bf'),
                DB::raw('ROUND(SUM(match_stats.total_runs) / NULLIF(SUM(match_stats.total_balls), 0) * 100, 2) as sr'),
                DB::raw('SUM(CASE WHEN match_stats.total_runs >= 100 THEN 1 ELSE 0 END) as hundreds'),
                DB::raw('SUM(CASE WHEN match_stats.total_runs >= 50 AND match_stats.total_runs < 100 THEN 1 ELSE 0 END) as fifties'),
                DB::raw('SUM(match_stats.total_fours) as fours'),
                DB::raw('SUM(match_stats.total_sixes) as sixes')
            )
            ->groupBy('match_stats.player_id', 'players.name', 'players.image', 'teams.name', 'teams.logo')
            ->having('runs', '>', 0)
            ->orderByDesc('runs')
            ->orderByDesc('sr')
            ->paginate(15);

        $topBatsman = $allPlayerStats->first();
    } elseif ($category === 'Bowling') {
        $subQuery = DB::table('bowlers_scoreboards as bw')
            ->join('matches', 'bw.match_id', '=', 'matches.id')
            ->join('tournaments', 'tournaments.id', '=', 'matches.tournament_id')
            ->select(
                'bw.bowler_id as player_id',
                'bw.match_id',
                'bw.team_id',
                DB::raw('MAX(bw.wickets) as max_wickets_taken'),
                DB::raw('MAX(bw.overs_bowled) as max_overs_bowled'),
                DB::raw('MAX(bw.runs_conceded) as max_runs_conceded')
            )
            ->whereIn('bw.inning', [0, 1])
            ->when($tournamentId, fn ($q) =>
                $q->where('matches.tournament_id', $tournamentId)
            )
            ->when($ballType, fn ($q) =>
                $q->where('tournaments.ball_type', $ballType)
            )
            ->groupBy('bw.bowler_id', 'bw.match_id', 'bw.team_id');

        $allBowlingStats = DB::table(DB::raw("({$subQuery->toSql()}) as match_stats"))
            ->mergeBindings($subQuery)
            ->join('players', 'match_stats.player_id', '=', 'players.id')
            ->join('teams', 'match_stats.team_id', '=', 'teams.id')
            ->join('matches', 'match_stats.match_id', '=', 'matches.id')
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
                'teams.logo as logo',
                DB::raw('COUNT(DISTINCT match_stats.match_id) as matches'),
                DB::raw('CAST(SUM(match_stats.max_overs_bowled) AS DECIMAL(10,2)) as overs_bowled'),
                DB::raw('SUM(match_stats.max_runs_conceded) as runs'),
                DB::raw('SUM(match_stats.max_wickets_taken) as wickets'),
                DB::raw('ROUND(SUM(match_stats.max_runs_conceded * 6.0) / NULLIF(SUM(match_stats.max_overs_bowled * 6), 0), 2) as economy'),
                DB::raw('SUM(CASE WHEN match_stats.max_wickets_taken >= 3 AND match_stats.max_wickets_taken < 5 THEN 1 ELSE 0 END) as threeFer'),
                DB::raw('SUM(CASE WHEN match_stats.max_wickets_taken >= 5 THEN 1 ELSE 0 END) as fiveFer')
            )
            ->groupBy('match_stats.player_id', 'players.name', 'players.image', 'teams.name', 'teams.logo')
            ->having('wickets', '>', 0)
            ->orderByDesc('wickets')
            ->orderBy('economy')
            ->paginate(15);

        $topBowler = $allBowlingStats->first();
    }

    return view('frontend.stats', compact(
        'allPlayerStats',
        'allBowlingStats',
        'tournaments',
        'teams',
        'category',
        'topBatsman',
        'topBowler',
        'ballType'
    ));
}

    public function about_us()
     {
        $content = AboutUsContent::first();
        $organizerMembers = OrganizerMember::get();
        return view('frontend.about-us', compact('content','organizerMembers'));
    }

    public function gallery()
     {
        $galleries = Gallery::orderBy('id', 'desc')->get()->groupBy('title');
    return view('frontend.gallery', compact('galleries'));
    }

    public function winners()
    {
        $winners = Winner::with('tournament')->get();
        $tournaments = Tournament::all();
        $winners = Winner::
           //whereBetween('year', [2018, 2025])->
           select('year', 'title', 'name', 'icon_name as icon' , 'season_name as season')
          ->orderBy('year', 'desc')
          ->get()
          ->groupBy('year');

          $seasonsData = [];

              foreach ($winners as $year => $records) {
                  $seasonsData[$year] = $records->map(function ($record) {
                      return [
                          'title' => $record->title,
                          'value' => $record->name,
                          'icon'  => $record->icon,
                          'season' => $record->season
                      ];
                  })->toArray();
              }

      
        return view('frontend.winners', compact('winners', 'tournaments' , 'seasonsData'));
    }

    public function partners()
     {
        $partners = Partner::all();
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
        Mail::to('cricket@pitchburners.com')->send(new ContactUsMail(
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
  
  
    public function terms()
    {
       return view('frontend.terms');
   }
   public function privacy()
   {
      return view('frontend.privacy');
  }
   public function rules()
    {
       return view('frontend.rules');
   }
  
  public function downloadTeamsMatchCount()
{
    // Fetch distinct team names and match counts
    $teams = DB::table('teams as t')
        ->join(
            DB::raw("
                (SELECT DISTINCT team1 AS team_id FROM schedule_matches 
                 WHERE DATE(match_date_time) IN ('2025-02-08', '2025-02-09')
                 UNION 
                 SELECT DISTINCT team2 AS team_id FROM schedule_matches 
                 WHERE DATE(match_date_time) IN ('2025-02-08', '2025-02-09')
                ) AS matched_teams
            "), 't.id', '=', 'matched_teams.team_id')
        ->select('t.name', DB::raw('COUNT(*) as match_count'))
        ->groupBy('t.name')
        ->get();

    // Define the file name
    $filename = 'team_match_counts_' . date('Y-m-d') . '.txt';

    // Generate file content
    $content = "Team Name | Match Count\n";
    $content .= "----------------------\n";

    foreach ($teams as $team) {
        $content .= "{$team->name} | {$team->match_count}\n";
    }

    // Store the file temporarily
    $filePath = storage_path($filename);
    file_put_contents($filePath, $content);

    // Return response for download
    return response()->download($filePath)->deleteFileAfterSend(true);
}

}
