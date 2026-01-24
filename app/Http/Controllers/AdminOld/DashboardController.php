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


class DashboardController extends Controller
{
  public function index(Request $request)
{
    // Count data
    $tournamentCount = Tournament::count();
    $venueCount = Venue::count();
   $teamCount = DB::table('tournament_teams')
    ->join('teams', 'tournament_teams.team_id', '=', 'teams.id')
    ->whereIn('teams.is_added', [1, 2])
    ->select('tournament_teams.team_id')
    ->groupBy('tournament_teams.team_id') // Group by unique team_id
    ->get()
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
        ->groupBy('match_stats.player_id', 'players.name', 'teams.name')
        ->having('total_runs', '>', 0);


    // Fetch the results with ordering
    $allPlayerStats = $query->orderByDesc('total_runs')
        ->orderByDesc('strike_rate')
        ->select(
        'players.name as player',
        'players.image as player_image',
        'teams.name as team',
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
            DB::raw('COUNT(DISTINCT match_stats.match_id) as total_matches'),
            DB::raw('SUM(match_stats.max_overs_bowled) as total_overs_bowled'),
            DB::raw('SUM(match_stats.max_runs_conceded) as total_runs_conceded'),
            DB::raw('SUM(match_stats.max_wickets_taken) as total_wickets_taken'),
            DB::raw('ROUND(SUM(match_stats.max_runs_conceded) / SUM(match_stats.max_overs_bowled), 2) as economy_rate'),
            DB::raw('SUM(CASE WHEN match_stats.max_wickets_taken >= 3 AND match_stats.max_wickets_taken < 5 THEN 1 ELSE 0 END) as three_fers'),
            DB::raw('SUM(CASE WHEN match_stats.max_wickets_taken >= 5 THEN 1 ELSE 0 END) as five_fers')
        )
        ->groupBy('match_stats.player_id', 'players.name', 'teams.name')
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
   $topCatchesPlayer = PlayerFieldingStats::selectRaw('
        player_id,
        SUM(catches) as total_catches,
        SUM(CASE WHEN player_id = catches THEN 1 ELSE 0 END) as match_count
    ')
    ->whereNotNull('player_id') // Exclude records with null player_id
    ->with(['player.team'])
    ->groupBy('player_id')
    ->orderByDesc('match_count') // Order by the number of matches where player_id matches catches
    ->having('match_count', '>', 0) // Include only players with matching conditions
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
        $organizerMembers = OrganizerMember::paginate(4);
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
        'members.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate the image
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
        $organizerMember = OrganizerMember::create([
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
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
        $organizerMember = OrganizerMember::findOrFail($id);

        $organizerMember->delete();

        return redirect()->route('organizer-members')->with('success', 'Member deleted successfully!');
    }

    public function venue_view()
     {
        $venues = Venue::paginate(4);
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
        'venues.*.location' => 'required|string|max:255',
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
        'location' => 'required|string|max:255',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate the image
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
    $search = $request->input('search', ''); // Get the search query

    // Fetch distinct teams based on search query and paginate
    $paginatedTeams = DB::table('teams')
        ->join('tournament_teams', 'teams.id', '=', 'tournament_teams.team_id')
        ->select('teams.id as team_id', 'teams.name') // Select relevant fields
        ->when($search, function ($query, $search) {
            return $query->where('teams.name', 'LIKE', "%$search%");
        })
        ->distinct() // Ensure only distinct teams are fetched
        ->paginate(10);

    // Add player counts for each team
    foreach ($paginatedTeams as $team) {
        $team->players_count = Player::where('team_id', $team->team_id)->count();
    }

    return view('admin.total-teams', compact('paginatedTeams', 'search'));
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
            $team = Team::findOrFail($id);
            $team->delete();

            return redirect()->back()->with('success', 'Team deleted successfully!');
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
        ->groupBy('match_stats.player_id', 'players.name', 'teams.name')
        ->having('total_runs', '>', 0);

    // Apply player name filter
    if ($request->filled('player_name')) {
        $query->where('players.name', 'LIKE', '%' . $request->player_name . '%');
    }

    // Apply tournament filter
    if ($request->filled('tournament_id')) {
        $query->join('tournament_teams', 'match_stats.team_id', '=', 'tournament_teams.team_id')
            ->where('tournament_teams.tournament_id', $request->tournament_id);
    }

    // Apply team filter
    if ($request->filled('team')) {
        $query->where('teams.name', $request->team);
    }

    // Fetch the results with ordering
    $allPlayerStats = $query->orderByDesc('total_runs')
        ->orderByDesc('strike_rate')
        ->paginate(30);

    // Return the view with player stats and filters
    return view('admin.most-runs', compact('allPlayerStats', 'tournaments'));
}


    public function most_wickets_view(Request $request)
{
    // Fetch all tournaments for the dropdown filter
    $tournaments = Tournament::all();

    // Subquery to aggregate bowling stats per player
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

    // Main query to fetch aggregated stats
    $query = DB::table(DB::raw("({$subQuery->toSql()}) as match_stats"))
        ->mergeBindings($subQuery) // Merge bindings for subquery
        ->join('players', 'match_stats.player_id', '=', 'players.id')
        ->join('teams', 'match_stats.team_id', '=', 'teams.id')
        ->select(
            'players.name as player',
            'players.image as image',
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
        ->having('total_wickets_taken', '>', 0);

    // Apply player name filter
    if ($request->filled('player_name')) {
        $query->where('players.name', 'LIKE', '%' . $request->player_name . '%');
    }

    // Apply tournament filter
    if ($request->filled('tournament_id')) {
        $query->join('tournament_teams', 'match_stats.team_id', '=', 'tournament_teams.team_id')
            ->where('tournament_teams.tournament_id', $request->tournament_id);
    }

    // Apply team filter
    if ($request->filled('team')) {
        $query->where('teams.name', $request->team);
    }

    // Fetch the results and paginate
    $allBowlingStats = $query->orderByDesc('total_wickets_taken')
        ->orderBy('economy_rate')
        ->paginate(30);

    // Return the view with data and filters
    return view('admin.most-wickets', compact('allBowlingStats', 'tournaments'));
}


    public function most_six_view(Request $request){
        // Fetch all tournaments for the dropdown
        $tournaments = Tournament::all();

        // Build the query
        $query = PlayerBattingStats::selectRaw('
            player_id,
            SUM(six) as total_sixes,
            COUNT(DISTINCT match_id) as total_matches
        ')
        ->groupBy('player_id')
        ->having('total_sixes', '>', 0)
        ->with('player');

        // Apply player name filter
        if ($request->filled('player_name')) {
            $query->whereHas('player', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->player_name . '%');
            });
        }

        // Apply tournament filter
        if ($request->filled('tournament_id')) {
            $query->whereHas('match', function($q) use ($request) {
                $q->where('tournament_id', $request->tournament_id);
            });
        }

        // Execute the query
       $allPlayerStats = $query->orderByDesc('total_sixes')->paginate(30);


        return view('admin.most-six', compact('allPlayerStats', 'tournaments'));
    }

    public function most_four_view(Request $request){
        // Fetch all tournaments for the dropdown
        $tournaments = Tournament::all();

        // Build the query
        $query = PlayerBattingStats::selectRaw('
            player_id,
            SUM(four) as total_fours,
            COUNT(DISTINCT match_id) as total_matches
        ')
        ->groupBy('player_id')
        ->having('total_fours', '>', 0)
        ->with('player');

        // Apply player name filter
        if ($request->filled('player_name')) {
            $query->whereHas('player', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->player_name . '%');
            });
        }

        // Apply tournament filter
        if ($request->filled('tournament_id')) {
            $query->whereHas('match', function($q) use ($request) {
                $q->where('tournament_id', $request->tournament_id);
            });
        }

        // Execute the query
      $allPlayerStats = $query->orderByDesc('total_fours')->paginate(30);


        return view('admin.most-four', compact('allPlayerStats', 'tournaments'));
    }

 public function most_catches_view(Request $request) {
    // Fetch all tournaments for the dropdown
    $tournaments = Tournament::all();

    // Build the query
    $query = PlayerFieldingStats::selectRaw('
        player_id,
        SUM(catches) as total_catches,
        COUNT(DISTINCT match_id) as total_matches,
        SUM(CASE WHEN player_id = catches THEN 1 ELSE 0 END) as catch_count
    ')
    ->whereNotNull('player_id') // Exclude records with null player_id
    ->groupBy('player_id')
    ->having('catch_count', '>', 0) // Only include players where their ID matches catches
    ->with('player');

    // Apply player name filter
    if ($request->filled('player_name')) {
        $query->whereHas('player', function($q) use ($request) {
            $q->where('name', 'like', '%' . $request->player_name . '%');
        });
    }

    // Apply tournament filter
    if ($request->filled('tournament_id')) {
        $query->whereHas('match', function($q) use ($request) {
            $q->where('tournament_id', $request->tournament_id);
        });
    }

    // Execute the query and order by total_catches in descending order
    $allPlayerFieldingStats = $query->orderByDesc('catch_count')->paginate(30);

    return view('admin.most-catches', compact('allPlayerFieldingStats', 'tournaments'));
}

}
