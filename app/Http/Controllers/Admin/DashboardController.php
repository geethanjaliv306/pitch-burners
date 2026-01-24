<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Venue;
use App\Models\OrganizerMember;
use App\Models\Player;
use App\Models\PlayerBattingStats;
use App\Models\PlayerBowlingStats;
use App\Models\PlayerFieldingStats;
use App\Models\ScheduleMatch;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use App\Models\MatchGame;
use App\Models\ScoreBoard;
use App\Models\BowlerScoreBoard;
use Carbon\Carbon;


class DashboardController extends Controller
{
     public function index(Request $request)
{
    // Count data
    $tournamentCount = Tournament::count();
    $venueCount = Venue::count();
   $teamCount = DB::table('teams')
       ->whereNull('deleted_at') 
   // ->join('teams', 'tournament_teams.team_id', '=', 'teams.id')
    //->whereIn('teams.is_added', [1, 2])
   // ->select('tournament_teams.team_id')
   // ->groupBy('tournament_teams.team_id') // Group by unique team_id
   // ->get()
    ->count();


    $schedulematchCount = ScheduleMatch::count();
    $organizerCount = OrganizerMember::count();
    
     // Fetch all tournaments for the dropdown filter
    $tournaments = Tournament::all();

    // Initialize the query with relevant fields
    $subQuery = DB::table('player_batting_stats')
        ->select(
            'player_id',
            'match_id',
            'team_id',
            DB::raw('MAX(score) as max_score'),
            DB::raw('SUM(four) as max_fours'),
            DB::raw('SUM(six) as max_sixes'),
            DB::raw('COUNT(match_id) as matches'),
            DB::raw('MAX(balls_faced) as max_balls'),
            DB::raw('MAX(is_out) as was_out')
        )
        ->groupBy('player_id', 'match_id', 'team_id');

    // Create a query to fetch player stats
    $query = DB::table(DB::raw("({$subQuery->toSql()}) as match_stats"))
        ->mergeBindings($subQuery) // Merge bindings for the subquery
        ->join('players', 'match_stats.player_id', '=', 'players.id')
        ->join('teams', 'match_stats.team_id', '=', 'teams.id')
        ->select(
            'players.name as player',
            'players.image as image',
            'teams.name as team',
        'teams.logo as logo',
            DB::raw('SUM(match_stats.max_score) as total_runs'),
            DB::raw('SUM(match_stats.max_fours) as total_fours'),
            DB::raw('SUM(match_stats.max_sixes) as total_sixes'),
            DB::raw('SUM(match_stats.max_balls) as total_balls_faced'),
            DB::raw('COUNT(DISTINCT match_stats.match_id) as matches'),
            DB::raw('ROUND(AVG(match_stats.max_score), 2) as avg'),
            DB::raw('ROUND(SUM(match_stats.max_score) / SUM(match_stats.max_balls) * 100, 2) as strike_rate'),
            DB::raw('MAX(match_stats.max_score) as highest_score'),
            DB::raw('SUM(CASE WHEN match_stats.max_score >= 100 THEN 1 ELSE 0 END) as hundreds'),
            DB::raw('SUM(CASE WHEN match_stats.max_score >= 50 AND match_stats.max_score < 100 THEN 1 ELSE 0 END) as fifties')
        )
        ->groupBy('match_stats.player_id', 'players.name', 'teams.name','teams.logo')
        ->having('total_runs', '>', 0);


    // Fetch the results with ordering
    $allPlayerStats = $query->orderByDesc('total_runs')
        ->orderByDesc('strike_rate')
        ->select(
        'players.name as player',
        'players.image as player_image',
        'teams.name as team',
        'teams.logo as logo',
        DB::raw('SUM(match_stats.max_score) as total_runs'),
        DB::raw('SUM(match_stats.max_fours) as total_fours'),
        DB::raw('SUM(match_stats.max_sixes) as total_sixes'),
        DB::raw('ROUND(SUM(match_stats.max_score) / SUM(match_stats.max_balls) * 100, 2) as strike_rate'),
        DB::raw('MAX(match_stats.max_score) as highest_score')
    )
 ->first();
    
     // Query for all bowling stats
    $subQuery = DB::table('player_bowling_stats')
        ->select(
            'player_id',
            'match_id',
            'team_id',
            DB::raw('MAX(overs_bowled) as max_overs_bowled'),
            DB::raw('MAX(runs_conceded) as max_runs_conceded'),
            DB::raw('MAX(wickets_taken) as max_wickets_taken')
        )
        ->groupBy('player_id', 'match_id', 'team_id');

    $query = DB::table(DB::raw("({$subQuery->toSql()}) as match_stats"))
        ->mergeBindings($subQuery)
        ->join('players', 'match_stats.player_id', '=', 'players.id')
        ->join('teams', 'match_stats.team_id', '=', 'teams.id')
        ->select(
            'players.name as player',
            'players.image as player_image',
            'teams.name as team',
        'teams.logo as logo',
            DB::raw('COUNT(DISTINCT match_stats.match_id) as total_matches'),
            DB::raw('SUM(match_stats.max_overs_bowled) as total_overs_bowled'),
            DB::raw('SUM(match_stats.max_runs_conceded) as total_runs_conceded'),
            DB::raw('SUM(match_stats.max_wickets_taken) as total_wickets_taken'),
            DB::raw('ROUND(SUM(match_stats.max_runs_conceded) / SUM(match_stats.max_overs_bowled), 2) as economy_rate'),
            DB::raw('SUM(CASE WHEN match_stats.max_wickets_taken >= 3 AND match_stats.max_wickets_taken < 5 THEN 1 ELSE 0 END) as three_fers'),
            DB::raw('SUM(CASE WHEN match_stats.max_wickets_taken >= 5 THEN 1 ELSE 0 END) as five_fers')
        )
        ->groupBy('match_stats.player_id', 'players.name', 'teams.name','teams.logo')
        ->having('total_wickets_taken', '>', 0);


    // Fetch results for all bowling stats
    $allBowlingStats = $query->orderByDesc('total_wickets_taken')
        ->orderBy('economy_rate')
       ->first();


    // Get top player for most runs
    $topRunScorer = PlayerBattingStats::selectRaw('
            player_id,
            MAX(score) as total_runs
        ')
         ->with(['player.team'])
        ->groupBy('player_id')
        ->orderByDesc('total_runs')
        ->first();

    // Get top player for most wickets
    $topWicketTaker = PlayerBowlingStats::selectRaw('
            player_id,
            MAX(wickets_taken) as total_wickets
        ')
         ->with(['player.team'])
        ->groupBy('player_id')
        ->orderByDesc('total_wickets')
        ->first();

    // Get top player for most sixes
    $topSixHitter = PlayerBattingStats::selectRaw('
            player_id,
            SUM(six) as total_sixes
        ')
         ->with(['player.team'])
        ->groupBy('player_id')
        ->orderByDesc('total_sixes')
        ->having('total_sixes', '>', 0)
        ->first();

    // Get top player for most fours
    $topFourHitter = PlayerBattingStats::selectRaw('
            player_id,
            SUM(four) as total_fours
        ')
         ->with(['player.team'])
        ->groupBy('player_id')
        ->orderByDesc('total_fours')
        ->having('total_fours', '>', 0)
        ->first();

    // Get top player for most catches, excluding records with null player_id
  $topCatchesPlayer = DB::table('player_fielding_stats as pfs')
    ->join('players', 'pfs.player_id', '=', 'players.id')
    ->join('teams', 'players.team_id', '=', 'teams.id')
    ->selectRaw('
        players.name as player_name,
        players.image,
        teams.name as team_name,
        teams.logo as team_logo,
            SUM(catches + fielding_caught_behind + fielding_caught_and_bowled) as total_catches,
        SUM(CASE WHEN pfs.player_id = fielding_caught_behind THEN 1 ELSE 0 END) as wk_catch_count,
           COUNT(DISTINCT pfs.match_id) as total_matches,
           SUM(CASE WHEN pfs.player_id = catches OR pfs.player_id = fielding_caught_behind OR pfs.player_id = fielding_caught_and_bowled THEN 1 ELSE 0 END) as catch_count

    ')
    ->whereNotNull('pfs.player_id')
    ->groupBy('pfs.player_id', 'players.name', 'players.image', 'teams.name', 'teams.logo')
    ->having('catch_count', '>', 0)
    ->orderByDesc('catch_count')  // Ensure this line is correct
    ->first();




    return view('admin.dashboard', [
        'tournamentCount' => $tournamentCount,
        'venueCount' => $venueCount,
        'organizerCount' => $organizerCount,
        'teamCount' => $teamCount,
        'schedulematchCount' => $schedulematchCount,
        'topRunScorer' => $topRunScorer && $topRunScorer->total_runs > 0 ? $topRunScorer : null,
        'topWicketTaker' => $topWicketTaker && $topWicketTaker->total_wickets > 0 ? $topWicketTaker : null,
        'topSixHitter' => $topSixHitter,
        'topFourHitter' => $topFourHitter,
        'topCatchesPlayer' => $topCatchesPlayer,
      'allPlayerStats' => $allPlayerStats,
      'allBowlingStats' => $allBowlingStats,
    ]);
}


    public function organizer_view()
     {
        $organizerMembers = OrganizerMember::paginate(15);
       return view('admin.organizer-members', compact('organizerMembers'));
    }

    public function organizer_create()
     {
        return view('admin.create-organizer-members');
    }

   public function organizer_store(Request $request)
{
    $request->validate([
        'members.*.name' => 'required|string|max:255',
        'members.*.email' => 'required|email|unique:organizer_members,email',
        'members.*.phone_no' => 'required|string|max:15',
        'members.*.password' => 'required|string|min:6',
        'members.*.image' => 'nullable|image|mimes:jpeg,png,jpg,svg', // 1MB limit
    ], [
        'members.*.image.required' => 'Please upload your company logo.',
        'members.*.image.mimes' => 'Allowed file types: jpg, jpeg, png, svg.',
        'members.*.image.max' => 'The logo must not exceed 1 MB.',
    ]);

    foreach ($request->members as $member) {
        $imagePath = null;

        if (isset($member['image'])) {
            // Generate a unique filename
            $file = $member['image'];
            $imagePath = time() . '_' . $file->getClientOriginalName();

            // Move the file to the public/uploads/organizer_images directory
            $file->move(public_path('uploads/organizer_images/'), $imagePath);
        }

        // Create OrganizerMember
        OrganizerMember::create([
            'name' => $member['name'],
            'email' => $member['email'],
            'phone_no' => $member['phone_no'],
            'password' => Hash::make($member['password']),
            'image' => $imagePath,
        ]);

        // Create User
        User::create([
            'name' => $member['name'],
            'email' => $member['email'],
            'phone_no' => $member['phone_no'],
            'password' => Hash::make($member['password']),
            'image' => $imagePath,
            'role' => 2,
        ]);
    }

    return redirect()->route('organizer-members')->with('success', 'Members added successfully!');
}


   public function organizer_update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:organizer_members,email,' . $id,
        'phone_no' => 'required|string|max:15',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,svg',
    ], [
        'image.required' => 'Please upload your company logo.',
        'image.mimes' => 'Allowed file types: jpg, jpeg, png, svg.',
        'image.max' => 'The logo must not exceed 1 MB.',
    ]);

    $organizerMember = OrganizerMember::findOrFail($id);

    $imagePath = $organizerMember->image;

    if ($request->hasFile('image')) {
        // Delete the old image if it exists
        if ($organizerMember->image && file_exists(public_path('uploads/organizer_images/' . $organizerMember->image))) {
            unlink(public_path('uploads/organizer_images/' . $organizerMember->image));
        }

        // Generate a unique filename for the new image
        $file = $request->file('image');
        $imagePath = time() . '_' . $file->getClientOriginalName();

        // Move the new image to the public/uploads/organizer_images directory
        $file->move(public_path('uploads/organizer_images'), $imagePath);
    }

    $organizerMember->update([
        'name' => $request->name,
        'email' => $request->email,
        'phone_no' => $request->phone_no,
        'image' => $imagePath,
    ]);

    return redirect()->route('organizer-members')->with('success', 'Member updated successfully!');
}


    public function destroy_members($id)
{
    // Find the organizer member
    $organizerMember = OrganizerMember::findOrFail($id);

    // Update the corresponding user's role to 3
    $user = User::where('email', $organizerMember->email)->first();
    if ($user) {
        $user->update(['role' => 3]);
    }

    // Delete the organizer member
    $organizerMember->delete();

    return redirect()->route('organizer-members')->with('success', 'Member deleted successfully, and user role updated!');
}


    public function venue_view()
     {
        $venues = Venue::paginate(15);
        return view('admin.venues-admin',compact('venues'));
    }

    public function venue_create()
     {
        return view('admin.create-venues');
    }

  public function venue_store(Request $request)
{
    $request->validate([
        'venues.*.name' => 'required|string|max:255',
        'venues.*.location' => 'required|string',
        'venues.*.image' => 'nullable|image', // Validate the image
    ]);

    foreach ($request->venues as $venueData) {
        if (isset($venueData['image'])) {
            // Get the original filename
            $originalFilename = time() . '_' . $venueData['image']->getClientOriginalName();

            // Move the image to the public/uploads/venues_images directory
            $venueData['image']->move(public_path('uploads/venues_images'), $originalFilename);

            // Save only the file name in the database
            $venueData['image'] = $originalFilename;
        }

        // Save the venue with the image name
        Venue::create([
            'name' => $venueData['name'],
            'location' => $venueData['location'],
            'image' => $venueData['image'] ?? null, // Handle null if no image is provided
        ]);
    }

    return redirect()->route('venues-admin')->with('success', 'Venues added successfully.');
}



public function venue_update(Request $request, $id)
{
    // Validate the request
    $request->validate([
        'name' => 'required|string|max:255',
        'location' => 'required|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,svg', // Validate the image
    ]);

    // Find the existing venue record
    $venue = Venue::findOrFail($id);

    // Prepare data for updating the venue
    $data = [
        'name' => $request->input('name'),
        'location' => $request->input('location'),
    ];

    // Check if a new image was uploaded
    if ($request->hasFile('image')) {
        // Generate a unique filename
        $file = $request->file('image');
        $uniqueFilename = time() . '_' . $file->getClientOriginalName();

        // Move the new image to the public/uploads/venues_images directory
        $file->move(public_path('uploads/venues_images'), $uniqueFilename);
        $data['image'] = $uniqueFilename; // Save only the filename in the database

        // Delete the old image if it exists
        if ($venue->image && file_exists(public_path('uploads/venues_images/' . $venue->image))) {
            unlink(public_path('uploads/venues_images/' . $venue->image));
        }
    } else {
        // If no new image was uploaded, keep the existing image
        $data['image'] = $venue->image;
    }

    // Update the venue with the new data
    $venue->update($data);

    return redirect()->route('venues-admin')->with('success', 'Venue updated successfully.');
}


    public function venue_destroy($id)
        {
            $venue = Venue::findOrFail($id);
            $venue->delete();

            return redirect()->route('venues-admin')->with('success', 'Venue deleted successfully.');
    }

public function total_teams(Request $request)
{
    $search = $request->input('search', '');
    $selectedTournamentId = $request->input('tournament_id');

    $tournaments = Tournament::whereNull('deleted_at')
        ->orderBy('start_date', 'desc')
        ->get();

    // ✅ Use INNER JOIN when filtering by tournament
    $query = Team::query()
        ->whereNull('teams.deleted_at');

    if ($selectedTournamentId) {
        $query->join('tournament_teams', function ($join) use ($selectedTournamentId) {
            $join->on('teams.id', '=', 'tournament_teams.team_id')
                 ->where('tournament_teams.tournament_id', $selectedTournamentId)
                 ->whereNull('tournament_teams.deleted_at');
        });
    }

    if ($search) {
        $query->where('teams.name', 'LIKE', "%{$search}%");
    }

    $paginatedTeams = $query
        ->select('teams.*')
        ->groupBy('teams.id')   // ✅ critical
        ->orderByDesc('teams.created_at')
        ->paginate(20)
        ->appends($request->query());

    foreach ($paginatedTeams as $team) {
        $team->players_count = Player::where('team_id', $team->id)->count();
    }

    $total_team_count = $paginatedTeams->total();

    return view('admin.total-teams', compact(
        'paginatedTeams',
        'search',
        'total_team_count',
        'tournaments',
        'selectedTournamentId'
    ));
}


public function notAppliedTeams(Request $request)
{
    $search = $request->input('search', '');

    $notAppliedTeams = DB::table('teams')
        ->leftJoin('tournament_teams', 'teams.id', '=', 'tournament_teams.team_id')
        ->leftJoin('players', 'teams.id', '=', 'players.team_id')
        ->select(
            'teams.id',
            'teams.name',
            'teams.bonafide',
            'teams.created_at', // ✅ added
            DB::raw('COUNT(players.id) as players_count')
        )
        ->when($search, function ($query, $search) {
            $query->where('teams.name', 'LIKE', "%{$search}%");
        })
        ->whereNull('tournament_teams.team_id')
        ->whereNull('teams.deleted_at')
        ->groupBy(
            'teams.id',
            'teams.name',
            'teams.bonafide',
            'teams.created_at' // ✅ required for groupBy
        )
        ->orderByDesc('teams.created_at')
        ->paginate(10)
        ->appends($request->query());

    // ✅ Correct total count (no second query needed)
    $total_team_count = $notAppliedTeams->total();

    return view('admin.not-applied-teams', compact(
        'notAppliedTeams',
        'total_team_count',
        'search'
    ));
}

    public function update_teamname(Request $request, $id)
        {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $team = Team::findOrFail($id);
            $team->update([
                'name' => $request->input('name'),
            ]);

            return redirect()->back()->with('success', 'Team updated successfully!');
    }

   public function destroy($id)
{
    // Find the team by ID or fail if it doesn't exist
    $team = Team::findOrFail($id);

    // Update the users table where team_id matches the deleted team's ID
    DB::table('users')
        ->where('team_id', $team->id)
        ->update(['role' => 3]);

    // Delete the team
    $team->delete();

    // Redirect back with a success message
    return redirect()->back()->with('success', 'Team deleted successfully and users updated!');
}
    public function toggleIsAdded($id)
     {
        $team = Team::findOrFail($id);

        if ($team->is_added == 0) {
            return redirect()->back()->withErrors(['toggle' => 'Team has not submitted full members of their team.']);
        }

        if ($team->is_added == 1) {
            $team->is_added = 2;
            $message = 'Team status updated to "2" successfully!';
        } elseif ($team->is_added == 2) {
            $team->is_added = 1;
            $message = 'Team status updated to "1" successfully!';
        }

        $team->save();
        return redirect()->back()->with('success', $message);
    }

public function most_runs_view(Request $request)
{
// Fetch all tournaments for the dropdown filter
$tournaments = Tournament::all();
$teams = Team::all();

// Initialize the query with relevant fields
$subQuery = DB::table('player_batting_stats as pbs')
   ->join('matches', 'pbs.match_id', '=', 'matches.id')
   ->select(
       'pbs.player_id',
       'pbs.match_id',
       'pbs.team_id',
       'matches.tournament_id', // Add tournament_id to track which tournament the stats are from
       DB::raw('MAX(score) as max_score'),
       DB::raw('SUM(four) as max_fours'),
       DB::raw('SUM(six) as max_sixes'),
       DB::raw('COUNT(pbs.match_id) as matches'),
       DB::raw('MAX(balls_faced) as max_balls'),
       DB::raw('MAX(is_out) as was_out'),
   DB::raw('MIN(CASE WHEN score >= 50 THEN balls_faced ELSE NULL END) as fastest_fifty_balls')
   )
   ->when($request->filled('tournament_id'), function ($query) use ($request) {
       $query->where('matches.tournament_id', $request->tournament_id);
   })
   ->groupBy('pbs.player_id', 'pbs.match_id', 'pbs.team_id', 'matches.tournament_id');

// Create a query to fetch player stats
$query = DB::table(DB::raw("({$subQuery->toSql()}) as match_stats"))
   ->mergeBindings($subQuery)
   ->join('players', 'match_stats.player_id', '=', 'players.id')
   ->join('teams', 'match_stats.team_id', '=', 'teams.id')
   ->when($request->filled('player_name'), function ($query) use ($request) {
       $query->where('players.name', 'LIKE', '%' . $request->player_name . '%');
   })
   ->when($request->filled('team'), function ($query) use ($request) {
       $query->where('teams.name', $request->team);
   })
   ->select(
       'players.name as player',
       'players.image as image',
       'teams.name as team',
       DB::raw('SUM(match_stats.max_score) as total_runs'),
       DB::raw('SUM(match_stats.max_fours) as total_fours'),
       DB::raw('SUM(match_stats.max_sixes) as total_sixes'),
       DB::raw('SUM(match_stats.max_balls) as total_balls_faced'),
       DB::raw('COUNT(DISTINCT match_stats.match_id) as matches'),
        DB::raw('ROUND(
    CASE 
        WHEN SUM(match_stats.was_out) = 0 
            THEN SUM(match_stats.max_score) 
        ELSE SUM(match_stats.max_score) / SUM(match_stats.was_out) 
    END, 2) as avg'),
       DB::raw('ROUND(SUM(match_stats.max_score) / SUM(match_stats.max_balls) * 100, 2) as strike_rate'),
       DB::raw('MAX(match_stats.max_score) as highest_score'),
       DB::raw('SUM(CASE WHEN match_stats.max_score >= 100 THEN 1 ELSE 0 END) as hundreds'),
       DB::raw('SUM(CASE WHEN match_stats.max_score >= 50 AND match_stats.max_score < 100 THEN 1 ELSE 0 END) as fifties'),
    DB::raw('MIN(match_stats.fastest_fifty_balls) as fastest_fifty') // Fastest Fifty Balls
   )
   ->groupBy('match_stats.player_id', 'players.name', 'players.image', 'teams.name')
   ->having('total_runs', '>', 0)
   ->when($request->input('player_sort'), function ($query) use ($request) {
            $query->orderBy('players.name', $request->input('player_sort'));
        })
        ->when($request->input('matches_sort'), function ($query) use ($request) {
            $query->orderBy('matches', $request->input('matches_sort'));
        })
        ->when($request->input('runs_sort'), function ($query) use ($request) {
            $query->orderBy('total_runs', $request->input('runs_sort'));
        })
        ->when($request->input('strike_rate_sort'), function ($query) use ($request) {
            $query->orderBy('strike_rate', $request->input('strike_rate_sort'));
        })
        ->when($request->input('avg_sort'), function ($query) use ($request) {
            $query->orderBy('avg', $request->input('avg_sort'));
        })
        ->when($request->input('highest_score_sort'), function ($query) use ($request) {
            $query->orderBy('highest_score', $request->input('highest_score_sort'));
        })
        ->when($request->input('hundreds_sort'), function ($query) use ($request) {
            $query->orderBy('hundreds', $request->input('hundreds_sort'));
        })
        ->when($request->input('fifties_sort'), function ($query) use ($request) {
            $query->orderBy('fifties', $request->input('fifties_sort'));
        })
  ->when($request->input('fastest_fifty_sort'), function ($query) use ($request) {

            $query->orderBy('fastest_fifty', $request->input('fastest_fifty_sort'));

        })
   ->when($request->input('balls_faced_sort'), function ($query) use ($request) {
            $query->orderBy('total_balls_faced', $request->input('balls_faced_sort'));
        })
        ->when($request->input('fours_sort'), function ($query) use ($request) {
            $query->orderBy('total_fours', $request->input('fours_sort'));
        })
        ->when($request->input('sixes_sort'), function ($query) use ($request) {
            $query->orderBy('total_sixes', $request->input('sixes_sort'));
        })
   ->orderByDesc('total_runs')
   ->orderByDesc('strike_rate');

// Fetch the results with pagination
$allPlayerStats = $query->paginate(30);

// Return the view with player stats and filters
return view('admin.most-runs', compact('allPlayerStats', 'tournaments', 'teams'));
}
 public function most_wickets_view(Request $request)
{
    $tournaments = Tournament::all();
    $teams = Team::all();

    $subQuery = DB::table('player_bowling_stats as pbs')
        ->join('matches', 'pbs.match_id', '=', 'matches.id')
        ->select(
            'pbs.player_id',
            'pbs.match_id',
            'pbs.team_id',
            'matches.tournament_id',
            DB::raw('MAX(overs_bowled) as max_overs_bowled'),
            DB::raw('MAX(runs_conceded) as max_runs_conceded'),
            DB::raw('MAX(wickets_taken) as max_wickets_taken')
        )
        ->when($request->filled('tournament_id'), function ($query) use ($request) {
            $query->where('matches.tournament_id', $request->tournament_id);
        })
        ->groupBy('pbs.player_id', 'pbs.match_id', 'pbs.team_id', 'matches.tournament_id');

    $query = DB::table(DB::raw("({$subQuery->toSql()}) as match_stats"))
        ->mergeBindings($subQuery)
        ->join('players', 'match_stats.player_id', '=', 'players.id')
        ->join('teams', 'match_stats.team_id', '=', 'teams.id')
        ->when($request->filled('player_name'), function ($query) use ($request) {
            $query->where('players.name', 'LIKE', '%' . $request->player_name . '%');
        })
        ->when($request->filled('team'), function ($query) use ($request) {
            $query->where('teams.name', $request->team);
        })
        ->select(
            'players.name as player',
            'players.image as image',
            'teams.name as team',
            'teams.logo as logo',
            DB::raw('COUNT(DISTINCT match_stats.match_id) as total_matches'),
            DB::raw('SUM(match_stats.max_overs_bowled) as total_overs_bowled'),
            DB::raw('SUM(match_stats.max_runs_conceded) as total_runs_conceded'),
            DB::raw('SUM(match_stats.max_wickets_taken) as total_wickets_taken'),
            DB::raw('ROUND(SUM(match_stats.max_runs_conceded) / SUM(match_stats.max_overs_bowled), 2) as economy_rate'),
            DB::raw('SUM(CASE WHEN match_stats.max_wickets_taken >= 3 AND match_stats.max_wickets_taken < 5 THEN 1 ELSE 0 END) as three_fers'),
            DB::raw('SUM(CASE WHEN match_stats.max_wickets_taken >= 5 THEN 1 ELSE 0 END) as five_fers')
        )
        ->groupBy('match_stats.player_id', 'players.name', 'players.image', 'teams.name', 'teams.logo')
        ->having('total_wickets_taken', '>', 0)
        ->when($request->input('player_sort'), function ($query) use ($request) {
            $query->orderBy('players.name', $request->input('player_sort'));
        })
        ->when($request->input('matches_sort'), function ($query) use ($request) {
            $query->orderBy('total_matches', $request->input('matches_sort'));
        })
        ->when($request->input('overs_bowled_sort'), function ($query) use ($request) {
            $query->orderBy('total_overs_bowled', $request->input('overs_bowled_sort'));
        })
        ->when($request->input('runs_given_sort'), function ($query) use ($request) {
            $query->orderBy('total_runs_conceded', $request->input('runs_given_sort'));
        })
        ->when($request->input('wickets_sort'), function ($query) use ($request) {
            $query->orderBy('total_wickets_taken', $request->input('wickets_sort'));
        })
        ->when($request->input('economy_sort'), function ($query) use ($request) {
            $query->orderBy('economy_rate', $request->input('economy_sort'));
        })
        ->when($request->input('three_fer_sort'), function ($query) use ($request) {
            $query->orderBy('three_fers', $request->input('three_fer_sort'));
        })
        ->when($request->input('five_fer_sort'), function ($query) use ($request) {
            $query->orderBy('five_fers', $request->input('five_fer_sort'));
        })
        ->orderByDesc('total_wickets_taken')
        ->orderBy('economy_rate');

    $allBowlingStats = $query->paginate(30);

    return view('admin.most-wickets', compact('allBowlingStats', 'tournaments', 'teams'));
}


public function most_six_view(Request $request)
{
    $tournaments = Tournament::all();
    $teams = Team::all();

    $query = DB::table('player_batting_stats as pbs')
        ->join('matches', 'pbs.match_id', '=', 'matches.id')
        ->join('players', 'pbs.player_id', '=', 'players.id')
        ->join('teams', 'pbs.team_id', '=', 'teams.id')
        ->when($request->filled('tournament_id'), function ($query) use ($request) {
            $query->where('matches.tournament_id', $request->tournament_id);
        })
        ->when($request->filled('player_name'), function ($query) use ($request) {
            $query->where('players.name', 'LIKE', '%' . $request->player_name . '%');
        })
        ->when($request->filled('team_id'), function ($query) use ($request) { // Corrected to use team_id
            $query->where('pbs.team_id', $request->team_id);
        })
        ->select(
            'players.name as player',
            'players.image as image',
            'teams.name as team',
            DB::raw('SUM(pbs.six) as total_sixes'), // Ensure column name is correct
            DB::raw('COUNT(DISTINCT pbs.match_id) as total_matches')
        )
        ->groupBy('pbs.player_id', 'players.name', 'players.image', 'teams.name')
        ->having('total_sixes', '>', 0)
      ->when($request->input('player_sort'), function ($query) use ($request) {
            $query->orderBy('players.name', $request->input('player_sort'));
        })
      ->when($request->input('six_sort'), function ($query) use ($request) {
            $query->orderBy('total_sixes', $request->input('six_sort'));
        })
        ->orderByDesc('total_sixes');

    $allPlayerStats = $query->paginate(30);

    return view('admin.most-six', compact('allPlayerStats', 'tournaments', 'teams'));
}



public function most_four_view(Request $request)
{
    $tournaments = Tournament::all();
    $teams = Team::all();

    $query = DB::table('player_batting_stats as pbs')
        ->join('matches', 'pbs.match_id', '=', 'matches.id')
        ->join('players', 'pbs.player_id', '=', 'players.id')
        ->join('teams', 'pbs.team_id', '=', 'teams.id')
        ->when($request->filled('tournament_id'), function ($query) use ($request) {
            $query->where('matches.tournament_id', $request->tournament_id);
        })
        ->when($request->filled('player_name'), function ($query) use ($request) {
            $query->where('players.name', 'LIKE', '%' . $request->player_name . '%');
        })
        ->when($request->filled('team'), function ($query) use ($request) {
            $query->where('teams.name', $request->team);
        })
        ->select(
            'players.name as player',
            'players.image as image',
            'teams.name as team',
            DB::raw('SUM(four) as total_fours'),
            DB::raw('COUNT(DISTINCT pbs.match_id) as total_matches')
        )
        ->groupBy('pbs.player_id', 'players.name', 'players.image', 'teams.name')
        ->having('total_fours', '>', 0)
       ->when($request->input('player_sort'), function ($query) use ($request) {
            $query->orderBy('players.name', $request->input('player_sort'));
        })
      ->when($request->input('four_sort'), function ($query) use ($request) {
            $query->orderBy('total_fours', $request->input('four_sort'));
        })
        ->orderByDesc('total_fours');

    $allPlayerStats = $query->paginate(30);

    return view('admin.most-four', compact('allPlayerStats', 'tournaments', 'teams'));
}

public function most_catches_view(Request $request)
{
    $tournaments = Tournament::all();
    $teams = Team::all();

    $query = DB::table('player_fielding_stats as pfs')
        ->join('matches', 'pfs.match_id', '=', 'matches.id')
        ->join('players', 'pfs.player_id', '=', 'players.id')
        ->join('teams', 'pfs.team_id', '=', 'teams.id')
        ->when($request->filled('tournament_id'), function ($query) use ($request) {
            $query->where('matches.tournament_id', $request->tournament_id);
        })
        ->when($request->filled('player_name'), function ($query) use ($request) {
            $query->where('players.name', 'LIKE', '%' . $request->player_name . '%');
        })
        ->when($request->filled('team'), function ($query) use ($request) {
            $query->where('teams.name', $request->team);
        })
        ->select(
       'players.name as player',
       'players.image as image',
       'teams.name as team',
       DB::raw('SUM(catches + fielding_caught_behind + fielding_caught_and_bowled) as total_catches'),
        DB::raw('SUM(CASE WHEN pfs.player_id = fielding_caught_behind THEN 1 ELSE 0 END) as wk_catch_count'),
       DB::raw('SUM(CASE WHEN pfs.player_id = stumpings THEN 1 ELSE 0 END) as stump_count'),
            DB::raw('COUNT(DISTINCT pfs.match_id) as total_matches'),
            DB::raw('SUM(CASE WHEN pfs.player_id = catches OR pfs.player_id = fielding_caught_behind OR pfs.player_id = fielding_caught_and_bowled THEN 1 ELSE 0 END) as catch_count')
    
   )
   ->whereNotNull('pfs.player_id')
   ->groupBy('pfs.player_id', 'players.name', 'players.image', 'teams.name')
   ->having('catch_count', '>', 0)
       ->when($request->input('player_sort'), function ($query) use ($request) {
            $query->orderBy('players.name', $request->input('player_sort'));
        })
      ->when($request->input('catch_sort'), function ($query) use ($request) {
            $query->orderBy('catch_count', $request->input('catch_sort'));
        })
       ->when($request->input('wk_sort'), function ($query) use ($request) {
            $query->orderBy('wk_catch_count', $request->input('wk_sort'));
        })
       ->when($request->input('stump_sort'), function ($query) use ($request) {
           $query->orderBy('stump_count', $request->input('stump_sort'));
       })
   ->orderByDesc('catch_count');

    $allPlayerFieldingStats = $query->paginate(30);

    return view('admin.most-catches', compact('allPlayerFieldingStats', 'tournaments', 'teams'));
}

  // downloade player csv
public function allPlayers_csv(Request $request)
{
    $selectedTournamentId = $request->input('tournament_id');

    $query = Player::query()
        ->join('teams', 'teams.id', '=', 'players.team_id')
        ->whereNull('players.deleted_at')
        ->whereNull('teams.deleted_at');

    // ✅ Filter players by tournament (via team)
    if ($selectedTournamentId) {
        $query->join('tournament_teams', function ($join) use ($selectedTournamentId) {
            $join->on('teams.id', '=', 'tournament_teams.team_id')
                 ->where('tournament_teams.tournament_id', $selectedTournamentId)
                 ->whereNull('tournament_teams.deleted_at');
        });
    }

    $players = $query->select(
        'players.name',
        'players.email',
        'players.phone',
        'players.role',
        'players.batting_style',
        'players.bowling_style',
        'teams.name as team_name'
    )
    ->groupBy(
        'players.id',
        'players.name',
        'players.email',
        'players.phone',
        'players.role',
        'players.batting_style',
        'players.bowling_style',
        'teams.name'
    )
    ->get();

    if (!File::exists(public_path('pbsf-2025-players'))) {
        File::makeDirectory(public_path('pbsf-2025-players'), 0777, true);
    }

    $filename = public_path(
        'pbsf-2025-players/pbsf-2025-players-' . date('Y-m-d-H-i-s') . '.csv'
    );

    $handle = fopen($filename, 'w+');

    fputcsv($handle, [
        'Team Name',
        'Name',
        'Email',
        'Phone',
        'Role',
        'Batting Style',
        'Bowling Style'
    ]);

    foreach ($players as $player) {
        fputcsv($handle, [
            $player->team_name,
            $player->name,
            $player->email,
            $player->phone,
            $player->role,
            $player->batting_style,
            $player->bowling_style
        ]);
    }

    fclose($handle);

    return response()->download($filename)->deleteFileAfterSend(true);
}


public function allTeam_csv(Request $request)
{
    $selectedTournamentId = $request->input('tournament_id');

    $query = Team::query()
        ->leftJoin('tournament_teams as tt', 'tt.team_id', '=', 'teams.id')
        ->leftJoin('tournaments', 'tt.tournament_id', '=', 'tournaments.id')
        ->whereNull('teams.deleted_at');

    // ✅ Filter by selected tournament
    if ($selectedTournamentId) {
        $query->where('tt.tournament_id', $selectedTournamentId)
              ->whereNull('tt.deleted_at');
    }

    $teams = $query->select(
        'teams.name as team_name',
        'teams.email',
        'teams.phone',
        'tournaments.name as tournament_name',
        'tt.match_preference'
    )
    ->groupBy(
        'teams.id',
        'teams.name',
        'teams.email',
        'teams.phone',
        'tournaments.name',
        'tt.match_preference'
    )
    ->get();

    if (!File::exists(public_path('pbsf-2025-teams'))) {
        File::makeDirectory(public_path('pbsf-2025-teams'), 0777, true);
    }

    $matchPreferences = config('matchPreference');

    $filename = public_path(
        'pbsf-2025-teams/pbsf-2025-teams-' . date('Y-m-d-H-i-s') . '.csv'
    );

    $handle = fopen($filename, 'w+');

    fputcsv($handle, [
        'Team Name',
        'Email',
        'Phone',
        'Tournament Name',
        'Match Preference'
    ]);

    foreach ($teams as $team) {
        fputcsv($handle, [
            $team->team_name ?? '-',
            $team->email ?? '-',
            $team->phone ?? '-',
            $team->tournament_name ?? '-',
            $matchPreferences[$team->match_preference] ?? '-'
        ]);
    }

    fclose($handle);

    return response()->download($filename)->deleteFileAfterSend(true);
}


public function notapplied_csv() {
    $q = Team::query();
    $q->leftJoin('tournament_teams as tt', 'tt.team_id', '=', 'teams.id')
       ->leftJoin('tournaments', 'tt.tournament_id', '=', 'tournaments.id')
       ->whereNull('tt.team_id');

    $teams = $q->select(
        'teams.name as team_name',
        'teams.email',
        'teams.phone',
        'tournaments.name as tournament_name',
        'tt.match_preference'
    )->get();

    if (!File::exists(public_path('pbsf-2025-teams'))) {
        File::makeDirectory(public_path('pbsf-2025-teams'), $mode = 0777, true, true);
    }

    $headings = [
        'Team Name',
        'Email',
        'Phone',
        'Tournament Name',
        'Match Preference'
    ];

    $dynamicFileName = "pbsf-2025-teams-" . date("Y-m-d-H-i-s") . ".csv";
    $filename = public_path("pbsf-2025-teams/$dynamicFileName");
    $handle = fopen($filename, 'w+');

    fputcsv($handle, $headings);

    // Get match preference mapping from the config file
    $matchPreferences = config('matchPreference');

    if (count($teams) > 0) {
        foreach ($teams as $team) {
            fputcsv($handle, [
                $team->team_name ?? '-',
                $team->email ?? '-',
                $team->phone ?? '-',
                $team->tournament_name ?? '-',
                $matchPreferences[$team->match_preference] ?? '-' // Map numeric value to text or show '-'
            ]);
        }
    }
    fclose($handle);
    return Response::download($filename);
}
  public function notappliedPlayers_csv() {
    $q = Player::query();
    $q->join('teams', 'teams.id', '=', 'players.team_id')
    ->leftJoin('tournament_teams as tt', 'tt.team_id', '=', 'teams.id')
    ->whereNull('tt.team_id') // Ensure the team has not applied
    ->whereNull('players.deleted_at') // Exclude soft-deleted players
    ->whereNull('teams.deleted_at'); // Exclude players belonging to soft-deleted teams

    $players = $q->whereNull('players.deleted_at')
        ->select(
            'players.*',
            'teams.name as team_name'
        )->get();

    if (!File::exists(public_path('pbsf-2025-players'))) {
        File::makeDirectory(public_path('pbsf-2025-players'), $mode = 0777, true, true);
    }

    $headings = [
        'Team Name',
      	'Player ID',
        'Name',
        'Email',
        'Phone',
        'Role',
        'Batting Style',
        'Bowling Style'
    ];

    $dynamicFileName = "pbsf-2025-players-" . date("Y-m-d-H-i-s") . ".csv";
    $filename = public_path("pbsf-2025-players/$dynamicFileName");
    $handle = fopen($filename, 'w+');

    fputcsv($handle, $headings);
    if (count($players) > 0) {
        foreach ($players as $player) {
            fputcsv($handle, [
                $player->team_name,
              	$player->empid,
                $player->name,
                $player->email,
                $player->phone,
                $player->role,
                $player->batting_style,
                $player->bowling_style
            ]);
        }
    }

    fclose($handle);
    return Response::download($filename);
}
   public function admin_dashboard()
    {
       return view('admin.admin-dashboard');
   }
  
  public function exportMostRunsToCSV(Request $request)
{
    $subQuery = DB::table('player_batting_stats as pbs')
        ->join('matches', 'pbs.match_id', '=', 'matches.id')
        ->select(
            'pbs.player_id',
            'pbs.match_id',
            'pbs.team_id',
            'matches.tournament_id',
            DB::raw('MAX(score) as max_score'),
            DB::raw('SUM(four) as max_fours'),
            DB::raw('SUM(six) as max_sixes'),
            DB::raw('COUNT(pbs.match_id) as matches'),
            DB::raw('MAX(balls_faced) as max_balls'),
            DB::raw('MAX(is_out) as was_out'),
       DB::raw('MIN(CASE WHEN score >= 50 THEN balls_faced ELSE NULL END) as fastest_fifty_balls')
        )
        ->when($request->filled('tournament_id'), function ($query) use ($request) {
            $query->where('matches.tournament_id', $request->tournament_id);
        })
        ->groupBy('pbs.player_id', 'pbs.match_id', 'pbs.team_id', 'matches.tournament_id');

    $query = DB::table(DB::raw("({$subQuery->toSql()}) as match_stats"))
        ->mergeBindings($subQuery)
        ->join('players', 'match_stats.player_id', '=', 'players.id')
        ->join('teams', 'match_stats.team_id', '=', 'teams.id')
        ->select(
            'players.name as player',
            'teams.name as team',
            DB::raw('SUM(match_stats.max_score) as total_runs'),
            DB::raw('SUM(match_stats.max_fours) as total_fours'),
            DB::raw('SUM(match_stats.max_sixes) as total_sixes'),
            DB::raw('SUM(match_stats.max_balls) as total_balls_faced'),
            DB::raw('COUNT(DISTINCT match_stats.match_id) as matches'),
             DB::raw('ROUND(
    CASE 
        WHEN SUM(match_stats.was_out) = 0 
            THEN SUM(match_stats.max_score) 
        ELSE SUM(match_stats.max_score) / SUM(match_stats.was_out) 
    END, 2) as avg'),
            DB::raw('ROUND(SUM(match_stats.max_score) / SUM(match_stats.max_balls) * 100, 2) as strike_rate'),
            DB::raw('MAX(match_stats.max_score) as highest_score'),
            DB::raw('SUM(CASE WHEN match_stats.max_score >= 100 THEN 1 ELSE 0 END) as hundreds'),
            DB::raw('SUM(CASE WHEN match_stats.max_score >= 50 AND match_stats.max_score < 100 THEN 1 ELSE 0 END) as fifties'),
       DB::raw('MIN(match_stats.fastest_fifty_balls) as fastest_fifty') 
        )
        ->groupBy('match_stats.player_id', 'players.name', 'teams.name')
        ->having('total_runs', '>', 0)
        ->orderByDesc('total_runs')
        ->orderByDesc('strike_rate')
        ->get();

    $filename = "most_runs_export_" . date('Y-m-d') . ".csv";
    
    $handle = fopen($filename, 'w');

    // Define CSV Headings
    $headings = [
        'Player', 'Team', 'Total Runs', 'Balls Faced', 'Matches',
        'Average', 'Strike Rate', 'Highest Score', '100s', '50s', 'Fastest Fifty', 'Fours', 'Sixes'
    ];

    fputcsv($handle, $headings);

    // Write Player Stats to CSV with values wrapped in double quotes
    foreach ($query as $row) {
        fputcsv($handle, [
            $row->player,
            $row->team, // Team name is automatically enclosed in quotes by fputcsv
            $row->total_runs,
            $row->total_balls_faced,
            $row->matches,
            $row->avg,
            $row->strike_rate,
            $row->highest_score,
            $row->hundreds,
            $row->fifties,
          $row->fastest_fifty ?? '-',
            $row->total_fours,
            $row->total_sixes
        ]);
    }

    fclose($handle);

    return response()->download($filename)->deleteFileAfterSend(true);
}
public function exportMostWicketsToCSV(Request $request)
{
    $subQuery = DB::table('player_bowling_stats as pbs')
        ->join('matches', 'pbs.match_id', '=', 'matches.id')
        ->select(
            'pbs.player_id',
            'pbs.match_id',
            'pbs.team_id',
            'matches.tournament_id',
            DB::raw('MAX(overs_bowled) as max_overs_bowled'),
            DB::raw('MAX(runs_conceded) as max_runs_conceded'),
            DB::raw('MAX(wickets_taken) as max_wickets_taken')
        )
        ->when($request->filled('tournament_id'), function ($query) use ($request) {
            $query->where('matches.tournament_id', $request->tournament_id);
        })
        ->groupBy('pbs.player_id', 'pbs.match_id', 'pbs.team_id', 'matches.tournament_id');

    $query = DB::table(DB::raw("({$subQuery->toSql()}) as match_stats"))
        ->mergeBindings($subQuery)
        ->join('players', 'match_stats.player_id', '=', 'players.id')
        ->join('teams', 'match_stats.team_id', '=', 'teams.id')
        ->select(
            'players.name as player',
            'teams.name as team',
            DB::raw('COUNT(DISTINCT match_stats.match_id) as total_matches'),
            DB::raw('SUM(match_stats.max_overs_bowled) as total_overs_bowled'),
            DB::raw('SUM(match_stats.max_runs_conceded) as total_runs_conceded'),
            DB::raw('SUM(match_stats.max_wickets_taken) as total_wickets_taken'),
            DB::raw('ROUND(SUM(match_stats.max_runs_conceded) / SUM(match_stats.max_overs_bowled), 2) as economy_rate'),
            DB::raw('SUM(CASE WHEN match_stats.max_wickets_taken >= 3 AND match_stats.max_wickets_taken < 5 THEN 1 ELSE 0 END) as three_fers'),
            DB::raw('SUM(CASE WHEN match_stats.max_wickets_taken >= 5 THEN 1 ELSE 0 END) as five_fers')
        )
        ->groupBy('match_stats.player_id', 'players.name', 'teams.name')
        ->having('total_wickets_taken', '>', 0)
        ->orderByDesc('total_wickets_taken')
        ->orderBy('economy_rate')
        ->get();

    $filename = "most_wickets_export_" . date('Y-m-d') . ".csv";
    
    $handle = fopen($filename, 'w');

    // Define CSV Headings
    $headings = [
        'Player', 'Team', 'Matches', 'Overs Bowled', 'Runs Conceded', 
        'Wickets', 'Economy Rate', '3 Wicket Hauls', '5 Wicket Hauls'
    ];
    
    fputcsv($handle, $headings);

    // Write Player Stats to CSV
    foreach ($query as $row) {
        fputcsv($handle, [
            $row->player,
            $row->team, // Team name will be enclosed in quotes if necessary
            $row->total_matches,
            $row->total_overs_bowled,
            $row->total_runs_conceded,
            $row->total_wickets_taken,
            $row->economy_rate,
            $row->three_fers,
            $row->five_fers
        ]);
    }

    fclose($handle);

    return response()->download($filename)->deleteFileAfterSend(true);
}
  public function statsView()
{
    $tournament = Tournament::first();
    $start_date = explode(' ', $tournament->start_date)[0];
    $end_date = explode(' ', $tournament->end_date)[0];
    
    return view('admin.stats', compact('start_date', 'end_date'));
}

public function getMatchStats(Request $request) {
    $from = $request->input('fromDate');
    $end = $request->input('endDate');

    $date = Carbon::parse($from)->format('Y-m-d H:i:s');
    $from_date = $date;
    $end_date = Carbon::parse($end)->format('Y-m-d H:i:s');

  $total_matches = MatchGame::whereIn('matches.status', ['Completed', 'Canceled'])
           ->whereDate('matches.created_at', '>=', $from_date)
          ->whereDate('matches.created_at', '<=', $end_date)->count();
  	
  $total_runs = MatchGame::whereIn('matches.status', ['Completed', 'Canceled'])
           ->whereDate('matches.created_at', '>=', $from_date)
          ->whereDate('matches.created_at', '<=', $end_date)       
  ->leftJoin('match_scores', 'match_scores.match_id', '=', 'matches.id')->sum('match_scores.total_runs');
  
  $total_overs_bowled =  MatchGame::whereIn('matches.status', ['Completed','Canceled'])
           ->whereDate('matches.created_at', '>=', $from_date)
          ->whereDate('matches.created_at', '<=', $end_date)
  ->leftJoin('bowlers_scoreboards', 'bowlers_scoreboards.match_id', '=', 'matches.id')
  ->select(
        DB::raw('FORMAT(ROUND(SUM(bowlers_scoreboards.overs_bowled)),0) as total_overs_bowled'),
        DB::raw('SUM(bowlers_scoreboards.maidens) as total_maidens'),
        DB::raw('SUM(CASE WHEN bowlers_scoreboards.wickets >= 3 AND  bowlers_scoreboards.wickets < 5 THEN 1 ELSE 0 END) as three_wickets'),
        DB::raw('SUM(CASE WHEN bowlers_scoreboards.wickets >= 5 THEN 1 ELSE 0 END) as five_wickets'),	
  )->first();
  $topBatters = DB::table(function($query) use ($from_date, $end_date){
        $query->select(
            'batter_id as player_id',
            'match_id',
            'team_id',
            DB::raw('MAX(runs) as max_score'),
            DB::raw('MAX(balls_faced) as max_balls'),
            DB::raw('MAX(fours) as max_fours'),
            DB::raw('MAX(sixes) as max_sixes'),
            DB::raw('MAX(is_out) as was_out')
        )
        ->from('scoreboards')
        ->whereIn('inning', [0, 1]) // Filter where is_inning is 0 or 1
        ->whereDate('created_at', '>=', $from_date)
        ->whereDate('created_at', '<=', $end_date)
        ->groupBy('batter_id', 'match_id', 'team_id')
        ->toSql();
    }, 'match_stats')
    ->leftJoin('players', 'match_stats.player_id', '=', 'players.id')
    ->leftJoin('teams', 'match_stats.team_id', '=', 'teams.id')
    ->select(
        'players.name as player',
        'players.image as player_image',
        'teams.name as team',
        DB::raw('SUM(match_stats.max_score) as runs'),
        DB::raw('COUNT(DISTINCT match_stats.match_id) as matches'),
        DB::raw('COUNT(DISTINCT match_stats.match_id) as innings'),
        DB::raw('
            CASE
                WHEN SUM(match_stats.was_out) > 0 THEN 0
                ELSE 1
            END as no
        '),
        DB::raw('MAX(match_stats.max_score) as highest'),
        DB::raw('ROUND(AVG(match_stats.max_score), 2) as avg'),
        DB::raw('SUM(match_stats.max_balls) as bf'),
        DB::raw('ROUND(SUM(match_stats.max_score) / SUM(match_stats.max_balls) * 100, 2) as sr'),
        DB::raw('SUM(CASE WHEN match_stats.max_score >= 100 THEN 1 ELSE 0 END) as hundreds'),
        DB::raw('SUM(CASE WHEN match_stats.max_score >= 50 AND match_stats.max_score < 100 THEN 1 ELSE 0 END) as fifties'),
        DB::raw('SUM(match_stats.max_fours) as fours'),
        DB::raw('SUM(match_stats.max_sixes) as sixes')
    )
    ->groupBy('match_stats.player_id', 'players.name', 'teams.name', 'players.image')
    ->having('runs', '>', 0)
    ->orderByDesc('runs')
    ->limit(1)
    ->first();
  $topWicketers = DB::table(DB::raw('(SELECT
                    bowler_id,
                    match_id,
                    team_id,
                    created_at,
                    MAX(wickets) as max_wickets_taken,
                    MAX(overs_bowled) as max_overs_bowled,
                    MAX(runs_conceded) as max_runs_conceded
                FROM bowlers_scoreboards
                WHERE inning IN (0, 1)
                GROUP BY bowler_id, match_id, team_id, created_at
            ) as match_stats'))
    ->leftJoin('players', 'match_stats.bowler_id', '=', 'players.id')
    ->leftJoin('teams', 'match_stats.team_id', '=', 'teams.id')
    ->select(
        'players.name as player',
        'players.image as player_image',
        'teams.name as team',
        DB::raw('COUNT(DISTINCT match_stats.match_id) as matches'),
        DB::raw('CAST(SUM(match_stats.max_overs_bowled) AS DECIMAL(10,2)) as overs_bowled'),
        DB::raw('SUM(match_stats.max_runs_conceded) as runs'),
        DB::raw('SUM(match_stats.max_wickets_taken) as wickets'),
        DB::raw('ROUND(SUM(match_stats.max_runs_conceded * 6.0) / NULLIF(SUM(match_stats.max_overs_bowled * 6), 0), 2) as economy'),
        DB::raw('SUM(CASE WHEN match_stats.max_wickets_taken >= 3 AND match_stats.max_wickets_taken < 5 THEN 1 ELSE 0 END) as threeFer'),
        DB::raw('SUM(CASE WHEN match_stats.max_wickets_taken >= 5 THEN 1 ELSE 0 END) as fiveFer')
    )
    ->whereDate('match_stats.created_at', '>=', $from_date)
    ->whereDate('match_stats.created_at', '<=', $end_date)
    ->groupBy('match_stats.bowler_id', 'players.name', 'teams.name', 'players.image')
    ->orderByDesc('wickets')
    ->orderBy('economy')
    ->limit(1)->first();

    $batsmen_data = MatchGame::whereIn('matches.status', ['Completed', 'Canceled'])
           ->whereDate('matches.created_at', '>=', $from_date)
          ->whereDate('matches.created_at', '<=', $end_date)
    ->leftJoin('scoreboards', 'scoreboards.match_id' , '=', 'matches.id')
    ->select(
        DB::raw('SUM(CASE WHEN scoreboards.runs >= 50 AND scoreboards.runs <= 99 THEN 1 ELSE 0 END) as fifties'),
        DB::raw('SUM(CASE WHEN scoreboards.runs >= 100 THEN 1 ELSE 0 END) as hundreds'),
        DB::raw('SUM(scoreboards.fours) as fours'),
        DB::raw('SUM(scoreboards.sixes) as sixes'),
    )->limit(1)->first();

    $wickets = MatchGame::whereIn('matches.status', ['Completed', 'Canceled'])
           ->whereDate('matches.created_at', '>=', $from_date)
          ->whereDate('matches.created_at', '<=', $end_date)
    ->leftJoin('ball_by_ball as bbb', 'bbb.match_id', '=', 'matches.id')
    ->select(
        DB::raw('SUM(CASE WHEN bbb.is_wicket = 1 THEN 1 ELSE 0 END) as wickets'),
    )->limit(1)->pluck('wickets')->first();

    $total_catches = MatchGame::whereIn('matches.status', ['Completed', 'Canceled'])
           ->whereDate('matches.created_at', '>=', $from_date)
          ->whereDate('matches.created_at', '<=', $end_date)
          ->leftJoin('player_fielding_stats as fielding', 'fielding.match_id' , '=', 'matches.id')
          ->select(
            DB::raw('SUM(CASE WHEN fielding.catches != 0 OR fielding.fielding_caught_behind IS NOT NULL OR fielding.fielding_caught_and_bowled IS NOT NULL THEN 1 ELSE 0 END) as catches'),
          )->limit(1)->pluck('catches')->first();

    $data = [
        'total_matches' => $total_matches,
        'total_runs' => number_format($total_runs),
        'total_overs_bowled' => $total_overs_bowled->total_overs_bowled,
        'total_maidens' => $total_overs_bowled->total_maidens,
        'three_wickets' => $total_overs_bowled->three_wickets,
        'five_wickets' => $total_overs_bowled->five_wickets,
        'highest_scorer' => $topBatters,
        'most_wicket_taker' => $topWicketers,
        'total_fifties' => $batsmen_data->fifties,
        'total_hundreds' => $batsmen_data->hundreds,
        'total_fours' => number_format($batsmen_data->fours),
        'total_sixes' => number_format($batsmen_data->sixes),
        'total_wickets' => $wickets,
        'batsmen_data' => $batsmen_data,
        'total_catches' => $total_catches,
    ];

      return response()->json([
            'from' => $from_date,
            'end' => $end_date,
            'data' => $data,
      ]);
	}
}
