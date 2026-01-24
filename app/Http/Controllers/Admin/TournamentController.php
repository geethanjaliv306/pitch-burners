<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\Team;
use App\Models\Group;
use App\Models\ScheduleMatch;
use App\Models\Venue;
use App\Models\MatchGame;
use App\Models\Point;
use App\Models\TournamentTeam;
use App\Models\Tournament;
use App\Models\TournamentRound;
use App\Models\TournamentGroup;
use App\Mail\TournamentNotification;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendTeamNotifications;
use App\Jobs\SendTeamPlayersNotifications;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use ZipArchive;

class TournamentController extends Controller
{
    public function index()
     {
        return view('admin.admin');
    }

    public function addTournament()
     {
        $venues = Venue::pluck('name', 'id');
        return view('admin.add-new-tournaments',compact('venues'));
    }

     // TournamentController.php
public function storeTournament(Request $request)
{
    try {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|array',
            'ground' => 'required|array',
            'organiser_name' => 'required|string|max:255',
            'organiser_contact' => 'required|numeric|digits:10',
            'flexible_dates' => 'boolean',
            'start_date' => 'required',
            'end_date' => 'required|after:start_date',
            'tournament_category' => 'required|string|in:Limited Overs,Box Cricket',
            'ball_type' => 'required|string|in:Red Tennis,Green Tennis,White Ball',
        ], [
            'name.required' => 'Tournament name is required',
            'city.required' => 'Please select at least one city',
            'ground.required' => 'Please select at least one ground',
            'organiser_contact.digits' => 'Phone number must be 10 digits',
            'start_date.date_format' => 'Start date must be in dd/mm/yyyy format',
            'end_date.after' => 'End date must be after start date',
        ]);

        $validatedData['start_date'] = Carbon::createFromFormat('d-m-Y', $validatedData['start_date'])->format('Y-m-d');
        $validatedData['end_date'] = Carbon::createFromFormat('d-m-Y', $validatedData['end_date'])->format('Y-m-d');

        $validatedData['city'] = implode(',', $validatedData['city']);
        $validatedData['ground'] = implode(',', $validatedData['ground']);

        $tournament = Tournament::create($validatedData);

        return redirect()->route('tournaments-view')
            ->with('success', 'Tournament created successfully!');

    } catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()->back()
            ->withErrors($e->validator)
            ->withInput();
    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'An error occurred while creating the tournament.')
            ->withInput();
    }
}
    public function edit($id)
     {
        $venues = Venue::pluck('name', 'id');
        $tournament = Tournament::findOrFail($id);

        $tournament->city = explode(',', $tournament->city);
        $tournament->ground = explode(',', $tournament->ground);
        return view('admin.edit-tournaments', compact('tournament','venues'));
    }

    public function update(Request $request, $id)
     {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|array',
            'ground' => 'required|array',
            'organiser_name' => 'required|string|max:255',
             'organiser_contact' => 'required|numeric|digits:10',
            'flexible_dates' => 'boolean',
            'start_date' => 'required',
            'end_date' => 'required|after_or_equal:start_date',
            'tournament_category' => 'required|string|in:Limited Overs,Box Cricket',
            'ball_type' => 'required|string|in:Red Tennis,Green Tennis,White Ball',
        ]);
        $tournament = Tournament::findOrFail($id);
        $tournament->update([
            'name' => $request->name,
            'city' => implode(',', $request->city),
            'ground' => implode(',', $request->ground),
            'organiser_name' => $request->organiser_name,
            'organiser_contact' => $request->organiser_contact,
            'flexible_dates' => $request->has('flexible_dates') ? 1 : 0,
            'start_date' => Carbon::createFromFormat('d-m-Y', $request->start_date)->format('Y-m-d'),
            'end_date' => Carbon::createFromFormat('d-m-Y', $request->end_date)->format('Y-m-d'),
            'tournament_category' => $request->tournament_category,
            'ball_type' => $request->ball_type,
        ]);

        return redirect()->route('tournaments-view')->with('success', 'Tournament updated successfully!');
    }

    public function destroy($id)
     {
        $tournament = Tournament::findOrFail($id);
        $tournament->delete(); // Soft delete
        return redirect()->route('tournaments-view')->with('success', 'Tournament deleted successfully');
    }

   public function viewtournament(Request $request)
{
    // Get search filters
    $searchName = $request->input('name', '');
    $searchBallType = $request->input('ball_type', '');
    $searchDate = $request->input('date', '');

    // Query tournaments with filters
    $tournaments = Tournament::when($searchName, function ($query, $searchName) {
            $query->where('name', 'LIKE', "%$searchName%");
        })
        ->when($searchBallType, function ($query, $searchBallType) {
            $query->where('ball_type', 'LIKE', "%$searchBallType%");
        })
        ->when($searchDate, function ($query, $searchDate) {
            $query->whereDate('start_date', '<=', $searchDate)
                  ->whereDate('end_date', '>=', $searchDate);
        })
        ->paginate(20);

    return view('admin.my-tournaments', compact('tournaments', 'searchName', 'searchBallType', 'searchDate'));
}

    public function tournament_show($tournament_id){

        $tournament = Tournament::findOrFail($tournament_id);

        // Get all teams without pagination
        // $paginatedTeams = $tournament->teams()->whereNull('tournament_teams.deleted_at')->paginate(3);
        $paginatedTeams = DB::table('teams')
        ->join('tournament_teams', 'teams.id', '=', 'tournament_teams.team_id')
            ->where('tournament_teams.tournament_id', $tournament_id)
            ->whereNull('tournament_teams.deleted_at')
            ->whereNull('teams.deleted_at')
            ->paginate(3);

        // Count players in each team
        foreach ($paginatedTeams as $team) {
            $players_count = Player::where('players.team_id', $team->team_id)->count();

            $green_tennis_count = Player::where('players.team_id', $team->team_id)
                ->where('players.ball_preferences', 'Green Tennis')
                ->whereNull('players.deleted_at')
                ->count();

            $tournament = Tournament::findOrFail($tournament_id);
            $team->tournament_type = $tournament->ball_type;

            $team->players_count = $players_count;
            $team->green_tennis_count = $green_tennis_count;

            if ($team->match_preference) {
                $team->formatted_preference = config('matchPreference')[$team->match_preference] ?? 'Not Set';
            } else {
                $team->formatted_preference = 'Not Set';
            }
        }

        $rounds = $tournament->rounds;
        $teams = $tournament->teams;
        $hasIncompleteRound = $tournament->rounds()->where('status', 0)->exists();

        // Get groups and group teams
        $groups = TournamentGroup::where('tournament_id', $tournament_id)
            ->with('group', 'team')
            ->get()
            ->groupBy('group_id');

        $groupTeams = [];
        foreach ($groups as $group_id => $groupedTeams) {
            $groupTeams[$group_id] = $groupedTeams->pluck('team_id')->toArray();
        }

        // Return the view with the data
        return view('admin.tournament-details', compact('tournament', 'paginatedTeams', 'rounds', 'teams', 'groups', 'groupTeams', 'hasIncompleteRound'));
    }
  
   public function sendNotificationsToAllTeams($tournamentId)
    {
        try {

            SendTeamNotifications::dispatch($tournamentId);

            return back()->with('success', "Notifications are sent successfully");
        } catch (\Exception $e) {
            \Log::error("Error sending notifications to all teams: " . $e->getMessage());
            return back()->with('error', 'There was an error sending notifications. Please try again later.');
        }
    }
  
  
    public function sendNotification($tournamentId, $teamId)
    {
        SendTeamPlayersNotifications::dispatch($tournamentId, $teamId);

        return back()->with('success', 'Notifications are sent successfully');
    }

  public function Tournamentteams(Request $request, $tournament_id)
{
    $tournament = Tournament::findOrFail($tournament_id);

    // Get the search query from the request
    $search = $request->input('search', '');

    $query = DB::table('teams')
        ->join('tournament_teams', 'teams.id', '=', 'tournament_teams.team_id')
        ->where('tournament_teams.tournament_id', $tournament_id)
        ->whereNull('tournament_teams.deleted_at')
        ->whereNull('teams.deleted_at');

    // apply search
    $query->when($search, function ($q, $search) {
        $q->where('teams.name', 'LIKE', "%{$search}%");
    });

    // IMPORTANT: alias teams.id as team_id to avoid column collision with tournament_teams.id
    $paginatedTeams = $query->select(
            'teams.id as team_id',
            'teams.name',
            'teams.is_added',
            'teams.bonafide as team_bonafide',
            'tournament_teams.payment',
            'tournament_teams.verified',
            'tournament_teams.match_preference'
        )
        ->paginate(20);

    $total_team_count = DB::table('teams')
        ->join('tournament_teams', 'teams.id', '=', 'tournament_teams.team_id')
        ->where('tournament_teams.tournament_id', $tournament_id)
        ->whereNull('tournament_teams.deleted_at')
        ->whereNull('teams.deleted_at')
        ->count();

    $allTeams = Team::whereIn('is_added', [1, 2])->get();

    // Add player counts to each team — use the aliased team_id
    foreach ($paginatedTeams as $team) {
        $total_players = Player::where('team_id', $team->team_id)->count();

        $green_tennis_players = Player::where('team_id', $team->team_id)
            ->where('ball_preferences', 'Green Tennis')
            ->count();

        $team->tournament_type = $tournament->ball_type;

        $team->players_count = $total_players;
        $team->green_tennis_count = $green_tennis_players;

        $team->formatted_preference = $team->match_preference
            ? (config('matchPreference')[$team->match_preference] ?? 'Not Set')
            : 'Not Set';
    }

    return view('admin.my-tournaments-teams', compact(
        'tournament',
        'paginatedTeams',
        'allTeams',
        'search',
        'total_team_count'
    ));
}



   public function Tournamentteams1(Request $request, $tournament_id)
    {
        $tournament = Tournament::findOrFail($tournament_id);

        // Get the search query from the request
        $search = $request->input('search', '');

        // Filter paginated teams based on the search query
        $paginatedTeams = DB::table('teams')
        ->join('tournament_teams', 'teams.id', '=', 'tournament_teams.team_id')
        ->where('tournament_teams.tournament_id', $tournament_id)
        ->whereNull('tournament_teams.deleted_at')
          
      ->whereNull('teams.deleted_at')
        ->when($search, function ($query, $search) {
            $query->where('teams.name', 'LIKE', "%$search%");
        })
        ->select('teams.*', 'tournament_teams.*', 'tournament_teams.payment', 'tournament_teams.verified', 'teams.is_added', 'teams.bonafide as team_bonafide')
        ->paginate(20);
     

        $total_team_count = DB::table('teams')
        ->join('tournament_teams', 'teams.id', '=', 'tournament_teams.team_id')
        ->where('tournament_teams.tournament_id', $tournament_id)
        ->whereNull('tournament_teams.deleted_at')
        ->whereNull('teams.deleted_at')
        ->count();
     
        $allTeams = Team::whereIn('is_added', [1, 2])->get();

        // Add player counts to each team
        foreach ($paginatedTeams as $team) {
            $total_players = Player::where('team_id', $team->id)->count();

            $green_tennis_players = Player::where('team_id', $team->id)
                ->where('ball_preferences', 'Green Tennis')
                ->count();

            $tournament = Tournament::findOrFail($tournament_id);
            $team->tournament_type = $tournament->ball_type;

            $team->players_count = $total_players;
            $team->green_tennis_count = $green_tennis_players;

            if ($team->match_preference) {
                $team->formatted_preference = config('matchPreference')[$team->match_preference] ?? 'Not Set';
            } else {
                $team->formatted_preference = 'Not Set';
            }
        }
        return view('admin.my-tournaments-teams', compact('tournament', 'paginatedTeams', 'allTeams', 'search','total_team_count'));
    }
  
    public function notAppliedTeams(Request $request, $tournament_id)
{
    $tournament = Tournament::findOrFail($tournament_id);

    // Get the search query from the request
    $search = $request->input('search', '');

    // Fetch teams not applied to the tournament
    $notAppliedTeams = Team::whereNotIn('id', function ($query) use ($tournament_id) {
            $query->select('team_id')
                  ->from('tournament_teams')
                  ->where('tournament_id', $tournament_id)
                  ->whereNull('deleted_at');
        })
        ->whereNull('deleted_at')
        ->when($search, function ($query, $search) {
            $query->where('name', 'LIKE', "%$search%");
        })
        ->paginate(20);

    $total_team_count = Team::whereNotIn('id', function ($query) use ($tournament_id) {
        $query->select('team_id')
              ->from('tournament_teams')
              ->where('tournament_id', $tournament_id)
              ->whereNull('deleted_at');
    })
              ->count();

              foreach ($notAppliedTeams as $team) {
                $team->players_count = Player::where('team_id', $team->team_id)->count();
             }

    return view('admin.not-applied-teams-tournaments', compact('tournament', 'notAppliedTeams', 'search', 'total_team_count'));
}

  
   public function updateMatchPreference(Request $request, $tournament_id, $team_id)
{
    $request->validate([
        'match_preference' => 'required|in:1,2,3,4,5,6', // Validating match preference values
    ]);

    // Update the match preference in the tournament_teams table
    DB::table('tournament_teams')
        ->where('tournament_id', $tournament_id)
        ->where('team_id', $team_id)
        ->update(['match_preference' => $request->input('match_preference')]);

    return response()->json(['success' => true, 'message' => 'Match preference updated successfully']);
}

     public function togglePayment($tournament_id, $team_id)
    {
        $team = TournamentTeam::where('tournament_id', $tournament_id)
            ->where('team_id', $team_id)
            ->firstOrFail();

        // Toggle payment status
        $team->payment = $team->payment == 1 ? 0 : 1;
        $team->save();

        return redirect()->back()->with('success', 'Payment status updated successfully!');
    }

    public function toggleVerified($tournament_id, $team_id)
    {
        $team = TournamentTeam::where('tournament_id', $tournament_id)
            ->where('team_id', $team_id)
            ->firstOrFail();

        // Toggle verified status
        $team->verified = $team->verified == 1 ? 0 : 1;
        $team->save();

        return redirect()->back()->with('success', 'Verification status updated successfully!');
    }

    public function toggleAccess($tournament_id, $team_id)
    {
        $team = Team::findOrFail($team_id);

        // Check if the team has submitted full members
        if ($team->is_added == 0) {
            return redirect()->back()->withErrors(['access' => 'Team has not submitted full members of their team.']);
        }

        // Toggle the is_added status
       if ($team->is_added == 1) {
            $team->is_added = 2; // Grant access
            $message = 'Access revoked successfully!';
        } elseif ($team->is_added == 2) {
            $team->is_added = 1; // Revoke access
            $message = 'Access granted successfully!';
        }

        $team->save();

        return redirect()->back()->with('success', $message);
    }




    public function searchTeams(Request $request){
        // Search for teams matching the query
        $teams = Team::where('name', 'LIKE', '%' . $request->queryText . '%')->get();

        return response()->json($teams);
    }

    public function storeTeams(Request $request, $tournament_id)
         {
            $request->validate([
                'groupTeams' => 'required|array',
                'groupTeams.*' => 'exists:teams,id',
            ]);

            $tournament = Tournament::findOrFail($tournament_id);
            $currentTeams = $tournament->teams->pluck('id')->toArray();

            foreach ($request->input('groupTeams') as $team_id) {
                if (!in_array($team_id, $currentTeams)) {
                    TournamentTeam::create([
                        'tournament_id' => $tournament_id,
                        'team_id' => $team_id,
                    ]);
                    // Team::where('id', $team_id)->update(['tournament_id' => $tournament_id]);
                }
            }

            return redirect()->back()->with('success', 'Teams added successfully!');
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

    public function destroy_teams($tournament_id, $team_id)
     {
        $tournamentTeam = TournamentTeam::where('tournament_id', $tournament_id)
                        ->where('team_id', $team_id)
                        ->first();

        if ($tournamentTeam) {

            $tournamentTeam->delete();

            return redirect()->back()->with('success', 'Team removed from tournament successfully');
        } else {
            return redirect()->back()->withErrors('Team not found in the tournament');
        }
    }

   public function Tournamentrounds(Request $request, $tournament_id)
{
    $tournament = Tournament::findOrFail($tournament_id);
    $roundsQuery = $tournament->rounds();

    // Get search filters
    $searchRoundName = $request->input('round_name', '');
    $searchTeamsToQualify = $request->input('teams_to_qualify', '');

    // Apply filters
    if ($searchRoundName) {
        $roundsQuery->where('type', 'LIKE', "%$searchRoundName%");
    }

    if ($searchTeamsToQualify) {
        $roundsQuery->where('teams_to_qualify', $searchTeamsToQualify);
    }

    $rounds = $roundsQuery->get();

    $hasIncompleteRound = $tournament->rounds()->where('status', 0)->exists();

    $groups = TournamentGroup::where('tournament_id', $tournament_id)
        ->with('group', 'team')
        ->get()
        ->groupBy('group_id');

    $groupTeams = [];
    foreach ($groups as $group_id => $groupedTeams) {
        $groupTeams[$group_id] = $groupedTeams->pluck('team_id')->toArray();
    }

    return view('admin.my-tournaments-rounds', compact('tournament', 'rounds', 'groups', 'groupTeams', 'hasIncompleteRound', 'searchRoundName', 'searchTeamsToQualify'));
}


    public function StoreRounds(Request $request, $tournament_id)
     {
        $request->validate([
            'number_of_overs' => 'required|integer',
            'overs_per_bowler' => 'required|integer',
            'type' => 'required|string',
            'teams_to_qualify' => 'required|integer'
        ]);

         $round = TournamentRound::create([
            'tournament_id' => $tournament_id,
            'number_of_overs' => $request->number_of_overs,
            'overs_per_bowler' => $request->overs_per_bowler,
            'type' => $request->type,
            'teams_to_qualify' => $request->teams_to_qualify
        ]);
      
          TournamentGroup::where('tournament_id', $tournament_id)
        ->whereNull('round_id')
        ->update(['round_id' => $round->id]);

        return redirect()->route('tournaments-round', $tournament_id)->with('success', 'Round added successfully.');
    }

    public function updaterounds(Request $request, $round_id)
     {
        $request->validate([
            'number_of_overs' => 'required|integer',
            'overs_per_bowler' => 'required|integer',
            'type' => 'required|string',
            'teams_to_qualify' => 'required|integer'
        ]);

        $round = TournamentRound::findOrFail($round_id);
        $round->update([
            'number_of_overs' => $request->number_of_overs,
            'overs_per_bowler' => $request->overs_per_bowler,
            'type' => $request->type,
            'teams_to_qualify' => $request->teams_to_qualify
        ]);

        return redirect()->route('tournaments-round', $round->tournament_id)->with('success', 'Round updated successfully.');
    }

    public function destroy_round($id){

        $round = TournamentRound::findOrFail($id);
        $tournamentId = $round->tournament_id;

        $round->delete();

        return redirect()->route('tournaments-round', $tournamentId)->with('success', 'Round deleted successfully.');
    }

    public function toggleStatus($round_id)
     {
        $round = TournamentRound::findOrFail($round_id);
    $round->status = !$round->status;  // Toggle the round status
    $round->save();

    if ($round->status) {
        // When toggling on, process to qualify teams

        // Fetch matches related to the tournament and round
        $matches = MatchGame::where('tournament_id', $round->tournament_id)
                            ->where('round_id', $round_id)
                            ->get();

        // Container for qualified teams per group
        $qualified_teams = [];

        foreach ($matches as $match) {
            $group_id = $match->group_id;

            // Fetch the top N teams for each group from the points table based on `teams_to_qualify`
          $points = Point::where('tournament_id', $round->tournament_id)
                ->where('group_id', $group_id)
                ->selectRaw('team_id, SUM(total_points) as total_points, MAX(net_run_rate) as net_run_rate')
                ->groupBy('team_id')
                ->orderByDesc('total_points') // Sort by total points
                ->orderByDesc('net_run_rate') // Use net_run_rate as tiebreaker
                ->take($round->teams_to_qualify) // Fetch the number of teams specified in `teams_to_qualify`
                ->get();

            foreach ($points as $point) {
                $qualified_teams[$group_id][] = $point->team_id;
            }
        }

        // Flatten the array and make unique to avoid duplicates
        $qualified_teams_list = array_unique(array_merge(...array_values($qualified_teams)));

            // Update the TournamentGroups table:
            // foreach ($qualified_teams as $group_id => $team_ids) {
            //     TournamentGroup::where('tournament_id', $round->tournament_id)
            //                    ->where('group_id', $group_id)
            //                    ->whereIn('team_id', $team_ids)
            //                    ->update(['status' => 1]);
            // }

            // Update `qualified` status in `tournament_teams`
            TournamentTeam::where('tournament_id', $round->tournament_id)
                          ->whereNotIn('team_id', $qualified_teams_list)
                          ->update(['qualified' => 0]);
            TournamentTeam::where('tournament_id', $round->tournament_id)
                          ->whereIn('team_id', $qualified_teams_list)
                          ->update(['qualified' => 1]);

            TournamentGroup::where('tournament_id', $round->tournament_id)
            ->whereIn('team_id', $qualified_teams_list)
            ->update(['status' => 0]);

            // $selectedTeams = TournamentGroup::where('tournament_id',  $round->tournament_id)
            //               ->where('status', 1)
            //               ->update(['status' => 0]);

            return redirect()->route('tournaments-group', ['tournament_id' => $round->tournament_id])
                             ->with('qualified_teams', $qualified_teams_list)
                             ->with('success', 'Round status updated successfully.');
        } else {
            // When toggling off, reset team statuses

            // Set all teams in `TournamentGroup` and `TournamentTeam` for this round to inactive
            // TournamentGroup::where('tournament_id', $round->tournament_id)
            //                ->update(['status' => 0]);

            TournamentTeam::where('tournament_id', $round->tournament_id)
                          ->update(['qualified' => 1]);

                          TournamentGroup::where('tournament_id', $round->tournament_id)
                       ->update(['status' => 1]);

            // Clear session data for qualified teams
            session()->forget('qualified_teams');

            return redirect()->back()->with('success', 'Round status updated successfully.');
        }
    }



    public function Tournamentgroup(Request $request, $tournament_id)
     {

        // print_r($tournament_id);
        // exit;
        $tournament = Tournament::findOrFail($tournament_id);
        $rounds = $tournament->rounds;
 $searchGroupName = $request->input('group_name', '');
        $searchTeamName = $request->input('team_name', '');

        $selectedTeams = TournamentGroup::where('tournament_id', $tournament_id)
        ->where('status', 1)
        ->pluck('team_id')
        ->toArray();

        // Fetch all teams associated with the tournament
        // $allTeams = Team::whereIn('is_added', [1, 2])
        //     ->whereIn('id', function ($query) use ($tournament_id) {
        //         $query->select('team_id')
        //             ->from('tournament_teams')->whereNoIn('team_id' , [$selectTeam])
        //             ->where('tournament_id', $tournament_id)->where('qualified' , 1);
        //     })
        //     ->get();

        $allTeams = Team::whereIn('is_added', [1, 2])
                    ->whereIn('id', function ($query) use ($tournament_id) {
                        $query->select('team_id')
                              ->from('tournament_teams')
                              ->where('tournament_id', $tournament_id)
                              ->where('qualified', 1);
                               //->where('payment', 1)
                            //->where('verified', 1);
                    })
                    ->whereNotIn('id', $selectedTeams)
                    ->get();

                     $allTeamss = Team::whereIn('is_added', [1, 2])
                    ->whereIn('id', function ($query) use ($tournament_id) {
                        $query->select('team_id')
                              ->from('tournament_teams')
                              ->where('tournament_id', $tournament_id)
                              ->where('qualified', 1);
                             //  ->where('payment', 1)
                           // ->where('verified', 1);
                    })
                    // ->whereNotIn('id', $selectedTeams)
                    ->get();

        //    print_r(count($allTeams));
        //     exit;
            // Check if there is a completed round
        $hasCompletedRound = $rounds->where('status', 1)->count() > 0;
        $qualifiedTeams = [];

        if ($hasCompletedRound) {
            // Use session data or fetch from the database if not in session
            if (session()->has('qualified_teams')) {
                $qualifiedTeamIds = session('qualified_teams');
                $qualifiedTeams = Team::whereIn('id', $qualifiedTeamIds)->get();
            } else {
                $qualifiedTeamIds = $this->getQualifiedTeamsForNextRound($tournament_id);
                $qualifiedTeams = Team::whereIn('id', $qualifiedTeamIds)->get();
                session(['qualified_teams' => $qualifiedTeamIds]);
            }
        } else {
            session()->forget('qualified_teams');
        }

        // Fetch groups and map teams for each group
        $groups = TournamentGroup::where('tournament_id', $tournament_id)
        ->when($searchGroupName, function ($query, $searchGroupName) {
            $query->whereHas('group', function ($subQuery) use ($searchGroupName) {
                $subQuery->where('group_name', 'LIKE', "%$searchGroupName%");
            });
        })
        ->when($searchTeamName, function ($query, $searchTeamName) {
            $query->whereHas('team', function ($subQuery) use ($searchTeamName) {
                $subQuery->where('name', 'LIKE', "%$searchTeamName%");
            });
        })
        ->with('group', 'team')
        ->get()
        ->groupBy('group_id');

        $groupTeams = [];
        foreach ($groups as $group_id => $groupedTeams) {
            $groupTeams[$group_id] = $groupedTeams->pluck('team_id')->toArray();
        }

        return view('admin.my-tournaments-groups', compact(
            'tournament',
            'rounds',
            'groups',
            'groupTeams',
            'allTeams',
            'allTeamss',
            'qualifiedTeams',
            'hasCompletedRound'
        ));
    }

    private function getQualifiedTeamsForNextRound($tournament_id)
    {
        return DB::table('tournament_groups')
            ->where('tournament_id', $tournament_id)
            ->where('status', 1)
            ->pluck('team_id')
            ->toArray();
    }


    // public function searchGroups(Request $request) {

    //     $queryText = $request->queryText;
    //     $tournamentId = $request->tournamentId;

    //     $groups = TournamentGroup::where('tournament_groups.tournament_id', $tournamentId)
    //     ->join('teams', 'teams.id', '=', 'tournament_groups.team_id')
    //     ->whereHas('team', function($query) use ($queryText) {
    //         $query->where('name', 'LIKE', '%' . $queryText . '%');
    //     })
    //     ->get();

    //     $results = $groups->map(function($group) {
    //         return [
    //             'id' => $group->id,
    //             'group_name' => $group->group ? $group->group->name : null,
    //             'team_name' => $group->team ? $group->team->name : null,
    //             'team_logo' => $group->team ? $group->team->logo : null, // Add logo to the response
    //         ];
    //     });

    //     return response()->json($results);
    // }

    public function storeGroup(Request $request)
     {
        $request->validate([
            'tournament_id' => 'required|exists:tournaments,id',
            // 'round_type' => 'required|string',
            'group_name' => 'required|string',
            'team_ids' => 'required|array'
        ]);

        $group = Group::create([
            'group_name' => $request->group_name,
            'tournament_id' => $request->tournament_id,
        ]);

        foreach ($request->team_ids as $team_id) {
            TournamentGroup::create([
                'tournament_id' => $request->tournament_id,
                // 'round_type' => $request->round_type,
                'group_id' => $group->id,
                'team_id' => $team_id,
                'status' => '1'
            ]);

            Team::where('id', $team_id)->update([
                'group_id' => $group->id ,
            ]);
        }

        return redirect()->back()->with('success', 'Group added successfully!');
    }

    public function updateGroup(Request $request, $group_id)
     {
        $request->validate([
            'tournament_id' => 'required|exists:tournaments,id',
            // 'round_type' => 'required|string',
            'group_name' => 'required|string',
            'team_ids' => 'required|array'
        ]);

        $group = Group::findOrFail($group_id);
        $group->update([
            'group_name' => $request->group_name,
        ]);

        $currentTeams = TournamentGroup::where('group_id', $group_id)->pluck('team_id')->toArray();

        $newTeams = $request->team_ids;

        $teamsToRemove = array_diff($currentTeams, $newTeams);

        $teamsToAdd = array_diff($newTeams, $currentTeams);

        foreach ($teamsToRemove as $team_id) {
            Team::where('id', $team_id)->update([
                'group_id' => null
            ]);

            TournamentGroup::where('group_id', $group_id)
                ->where('team_id', $team_id)
                ->delete();
        }

        foreach ($teamsToAdd as $team_id) {
            $tournamentGroup = TournamentGroup::withTrashed()
                ->where('tournament_id', $request->tournament_id)
                ->where('group_id', $group_id)
                ->where('team_id', $team_id)
                ->first();

            if ($tournamentGroup) {
                $tournamentGroup->restore();
                // $tournamentGroup->update(['round_type' => $request->round_type]);
            } else {
                TournamentGroup::create([
                    'tournament_id' => $request->tournament_id,
                    // 'round_type' => $request->round_type,
                    'group_id' => $group_id,
                    'team_id' => $team_id,
                     'status' => '1'
                ]);
            }

            Team::where('id', $team_id)->update([
                'group_id' => $group->id ,
            ]);
        }

        return redirect()->back()->with('success', 'Group updated successfully!');
    }

    public function deleteGroup($id)
     {
        $group = Group::findOrFail($id);
        $tournamentId = $group->tournament_id;

        // Update teams to set group_id to null
        Team::where('group_id', $id)->update(['group_id' => null]);

        // Delete related entries in tournament_groups table
        TournamentGroup::where('group_id', $id)->delete();

        // Delete the group itself
        $group->delete();

        // Fetch updated tournament teams (excluding deleted ones)
        $tournamentTeams = TournamentTeam::where('tournament_id', $tournamentId)
            ->whereHas('team', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->get();

        return redirect()->back()
            ->with('success', 'Group deleted successfully!')
            ->with('tournamentTeams', $tournamentTeams);
    }

   public function Tournamentmatches(Request $request, $tournament_id, $round_id = null)
{
    $searchTeam = $request->input('team', ''); // Search team name
    $searchGround = $request->input('ground', ''); // Search ground
    $searchDate = $request->input('date', ''); // Search date
    $searchStatus = $request->input('status', ''); // Search status

    $tournament = Tournament::findOrFail($tournament_id);

    if ($round_id) {
        $rounds = $tournament->rounds()->where('id', $round_id)->get();
    } else {
        $rounds = $tournament->rounds;
    }

    $groups = TournamentGroup::where('tournament_id', $tournament_id)
                ->with('team')
                ->get()
                ->groupBy('group_id');

    $completedRoundIds = TournamentRound::where('tournament_id', $tournament_id)
        ->where('status', 1)
        ->where('id', '!=', $round_id)
        ->pluck('id')
        ->toArray();

    $excludedGroupIds = DB::table('schedule_matches')
        ->whereIn('round_id', $completedRoundIds)
        ->pluck('group_id')
        ->toArray();

    $showgroups = Group::where('tournament_id', $tournament_id)
        ->whereNotIn('id', $excludedGroupIds)
        ->get();

    $grounds = Tournament::select('ground')->distinct()->pluck('ground');
    $groundIds = [];

    foreach ($grounds as $ground) {
        $groundIds = array_merge($groundIds, explode(',', $ground));
    }

    $groundIds = array_unique($groundIds);
    $groundArray = Venue::whereIn('id', $groundIds)->pluck('name', 'id')->toArray();

    // **Query for Matches with Search & Filters**
    $matchesQuery = DB::table('schedule_matches')
        ->join('venues', 'schedule_matches.ground', '=', 'venues.id')
        ->join('teams as team1', 'schedule_matches.team1', '=', 'team1.id')
        ->join('teams as team2', 'schedule_matches.team2', '=', 'team2.id')
        ->where('schedule_matches.tournament_id', $tournament_id)
        ->whereNull('schedule_matches.deleted_at');

    if ($round_id) {
        $matchesQuery->where('schedule_matches.round_id', $round_id);
    }

    if (!empty($searchTeam)) {
        $matchesQuery->where(function ($query) use ($searchTeam) {
            $query->where('team1.name', 'LIKE', "%{$searchTeam}%")
                  ->orWhere('team2.name', 'LIKE', "%{$searchTeam}%");
        });
    }

    if (!empty($searchGround)) {
        $matchesQuery->where('venues.name', 'LIKE', "%{$searchGround}%");
    }

    if (!empty($searchDate)) {
        $matchesQuery->whereDate('schedule_matches.match_date_time', $searchDate);
    }

    if (!empty($searchStatus)) {
        $matchesQuery->where('schedule_matches.status', $searchStatus);
    }

    $matches = $matchesQuery->select(
        'schedule_matches.*',
        'venues.name as ground_name',
        'team1.name as team_one_name',
        'team2.name as team_two_name'
    )->orderBy('schedule_matches.match_date_time', 'asc')
    ->paginate(20);

    return view('admin.my-tournaments-matches', compact(
        'groups',
        'rounds',
        'tournament',
        'groundArray',
        'matches',
        'showgroups',
        'searchTeam',
        'searchGround',
        'searchDate',
        'searchStatus'
    ));
}

    public function storeMatch(Request $request)
        {
            $tournament = Tournament::findOrFail($request->tournament_id);

            $validatedData = $request->validate([
                'tournament_id' => [
                    'required',
                    'integer',
                ],
                'round_id' => [
                    'required',
                    'integer',
                ],
                'group_id' => [
                    'required',
                    'integer',
                ],
                'team1' => [
                    'required',
                    'integer',
                ],
                'team2' => [
                    'required',
                    'integer',
                ],
                'number_of_overs' => [
                    'required',
                    'numeric',
                    'min:1'
                ],
                'overs_per_bowler' => [
                    'required',
                    'numeric',
                    'min:1',
                    function ($attribute, $value, $fail) use ($request) {
                        if ($value > $request->number_of_overs) {
                            $fail('Overs per bowler cannot be greater than the number of overs.');
                        }
                    }
                ],
                'type' => [
                    'required',
                    'string',
                    'in:Red Tennis,Green Tennis,White Ball'
                ],
                'category' => [
                    'required',
                    'string',
                    'in:Limited Overs,Box Cricket'
                ],
                'ground' => [
                    'required',
                    'integer',
                ],
                'match_date_time' => [
                    'required',
                ],
            ]);
      
      $matchDateTime = \Carbon\Carbon::createFromFormat('d/m/Y, h:i A', $validatedData['match_date_time'])->format('Y-m-d H:i:s');

            $data = ScheduleMatch::create([
                'tournament_id' => $validatedData['tournament_id'],
                'round_id' => $validatedData['round_id'],
                'group_id' => $validatedData['group_id'],
                'team1' => $validatedData['team1'],
                'team2' => $validatedData['team2'],
                'number_of_overs' => $validatedData['number_of_overs'],
                'overs_per_bowler' => $validatedData['overs_per_bowler'],
                'type' => $validatedData['type'],
                'category' => $validatedData['category'],
                'ground' => $validatedData['ground'],
                'status' => 'Scheduled',
                 'match_date_time' => $matchDateTime,
            ]);

            return redirect()->route('tournaments.match', [
                'tournament_id' => $validatedData['tournament_id'],
                'round_id' => $validatedData['round_id'],
            ])->with('success', 'Match scheduled successfully.');
    }

    public function deleteMatch($id)
     {
        $match = ScheduleMatch::findOrFail($id);
        $tournament_id = $match->tournament_id;
        $round_id = $match->round_id;

        $match->delete();

        return redirect()->route('tournaments.match', [
            'tournament_id' => $tournament_id,
            'round_id' => $round_id
        ])->with('success', 'Match deleted successfully.');
    }

    public function getMatchDetails($id)
    {
        $match = ScheduleMatch::findOrFail($id);

        // Return the match details as JSON
        return response()->json([
            'id' => $match->id,
            'group_id' => $match->group_id,  // Include group ID
            'team1' => $match->team1,
            'team2' => $match->team2,
            'ground' => $match->ground,
            'match_date_time' => \Carbon\Carbon::parse($match->match_date_time)->format('d/m/Y, h:i A'),
            'number_of_overs' => $match->number_of_overs,
            'overs_per_bowler' => $match->overs_per_bowler,
            'ball_type' => $match->type,
        ]);
    }

      public function updateMatch(Request $request, $id)
{
    $match = ScheduleMatch::findOrFail($id);

    \Log::info('Incoming Request:', $request->all());

    // Log the match_date_time value
    \Log::info('Match Date Time:', ['match_date_time' => $request->input('match_date_time')]);

    // Validate the incoming request data
    $validatedData = $request->validate([
        'team_one' => 'required|integer',
        'team_two' => 'required|integer',
        'ground' => 'required|integer',
        'match_date_time' => 'required',
        'number_of_overs' => 'required|integer',
        'overs_per_bowler' => 'required|integer|max:' . $request->number_of_overs,
        'ball_type' => 'required|string',
    ]);

    // Parse the date using the correct format
    $matchDateTime = \Carbon\Carbon::createFromFormat('d/m/Y, h:i A', $validatedData['match_date_time']);

    // Update the match record with validated data
    $match->update([
        'team1' => $validatedData['team_one'],
        'team2' => $validatedData['team_two'],
        'ground' => $validatedData['ground'],
        'match_date_time' => $matchDateTime,
        'number_of_overs' => $validatedData['number_of_overs'],
        'overs_per_bowler' => $validatedData['overs_per_bowler'],
        'type' => $validatedData['ball_type'],
    ]);

    // Redirect back with success message
    return redirect()->back()->with('success', 'Match updated successfully.');
}


  public function schedulematch_view(Request $request)
{
    $searchTeam = $request->input('team', ''); // Search team name
    $searchGround = $request->input('ground', ''); // Search ground
    $searchDate = $request->input('date', ''); // Search date
    $searchStatus = $request->input('status', ''); // Search status
    $searchTournament = $request->input('tournament', ''); // Search tournament name
 $tournaments = DB::table('tournaments')
    ->whereNull('tournaments.deleted_at')
        ->select('id', 'name')
        ->get();
    $matches = DB::table('schedule_matches')
        ->join('venues', 'schedule_matches.ground', '=', 'venues.id')
        ->join('teams as team1', 'schedule_matches.team1', '=', 'team1.id')
        ->join('teams as team2', 'schedule_matches.team2', '=', 'team2.id')
        ->join('tournaments', 'schedule_matches.tournament_id', '=', 'tournaments.id') // Join tournaments table
        ->whereNull('schedule_matches.deleted_at')
        ->when($searchTeam, function ($query, $searchTeam) {
            $query->where(function ($subQuery) use ($searchTeam) {
                $subQuery->where('team1.name', 'LIKE', "%$searchTeam%")
                         ->orWhere('team2.name', 'LIKE', "%$searchTeam%");
            });
        })
        ->when($searchGround, function ($query, $searchGround) {
            $query->where('venues.name', 'LIKE', "%$searchGround%");
        })
        ->when($searchDate, function ($query, $searchDate) {
            $query->whereDate('schedule_matches.match_date_time', $searchDate);
        })
        ->when($searchStatus, function ($query, $searchStatus) {
            $query->where('schedule_matches.status', $searchStatus);
        })
        ->when($searchTournament, function ($query, $searchTournament) {
            $query->where('tournaments.name', 'LIKE', "%$searchTournament%");
        }) // Add tournament search condition
        ->select(
            'schedule_matches.*',
            'venues.name as ground_name',
            'team1.name as team_one_name',
            'team2.name as team_two_name',
            'tournaments.name as tournament_name' // Select tournament name
        )
        ->orderBy('schedule_matches.match_date_time', 'asc')
        ->paginate(20);

    return view('admin.view-schedule-matches', compact('matches', 'searchTeam', 'searchGround', 'searchDate', 'searchStatus', 'searchTournament','tournaments'));
}



    public function deleteschedulematch($id)
     {
        $match = ScheduleMatch::findOrFail($id);

        $match->delete();

        return redirect()->route('schedulematch')->with('success', 'Match deleted successfully');
    }

    public function getTeamsByGroup(Request $request)
     {
        $groupId = $request->query('group_id');

        // Fetch the team IDs related to the specified group ID from the tournament_groups table.
        $teamIds = TournamentGroup::where('group_id', $groupId)->pluck('team_id');

        // Fetch the teams using the retrieved team IDs.
        $teams = Team::whereIn('id', $teamIds)->get();

        return response()->json([
            'teams' => $teams
        ]);
    }
 public function team_players($team_id)
    {
        // Fetch the team details
        $team = Team::findOrFail($team_id);

        // Fetch players associated with the team
        $players = Player::where('team_id', $team_id)->select(
            'id',
          'is_captain',
            'name',
            'email',
            'empid',
            'phone',
            'image',
            'batting_style',
            'bowling_style',
            'role',
            'ball_preferences'
        )->get();

        $tournamentId = DB::table('tournament_teams')
    ->where('team_id', $team_id)
    ->value('tournament_id');

    return view('admin.team-players', compact('team', 'players', 'tournamentId'));

    }

    public function update_players(Request $request, Player $player)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'empid' => 'required|string|max:50',
            'phone' => 'required|numeric',
            'batting_style' => 'nullable|string',
            'bowling_style' => 'nullable|string',
            'role' => 'nullable|string',
            'image' => 'nullable|image',
            'ball_preferences' => 'nullable|string',
        ]);

        // Update player details except image
        $player->update($request->except('image'));

        // Handle the image upload if present
         if ($request->hasFile('image')) {
            $image = $request->file('image');

            // Generate a unique file name for the image
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            // Store the image in the public/uploads folder
            $image->move(public_path('uploads/player_images'), $imageName);
            $imagePath = $imageName;

            // Update the image path in the database
            $player->update(['image' => basename($imagePath)]);
        }


        return redirect()->route('team_players', $player->team_id)->with('success', 'Player updated successfully.');
    }

     public function teamplayers($team_id)
{
    // Fetch the team details
    $team = Team::findOrFail($team_id);

    // Fetch players associated with the team
    $players = Player::where('team_id', $team_id)->select(
        'id',
      'is_captain',
        'name',
        'email',
        'empid',
        'phone',
        'image',
        'batting_style',
        'bowling_style',
        'role',
        'ball_preferences'
    )->get();

    // Fetch tournaments associated with the team
    $tournaments = DB::table('tournaments')
        ->join('tournament_teams', 'tournaments.id', '=', 'tournament_teams.tournament_id')
        ->where('tournament_teams.team_id', $team_id)
       ->whereNull('tournament_teams.deleted_at')
        ->select('tournaments.name as tournament_name', 'tournaments.id as tournament_id','tournaments.*')
        ->get();

    return view('admin.teams-details', compact('team', 'players', 'tournaments'));
}


    public function updateplayers(Request $request, Player $player)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'empid' => 'required|string|max:50',
            'phone' => 'required|numeric',
            'batting_style' => 'nullable|string',
            'bowling_style' => 'nullable|string',
            'role' => 'nullable|string',
            'image' => 'nullable|image',
            'ball_preferences' => 'nullable|string',
        ]);

        // Update player details except for the image
    $player->update($request->except('image'));

    // Handle the image upload if present
    if ($request->hasFile('image')) {
        // Get the file
        $image = $request->file('image');
        
        // Generate a unique name for the image to avoid conflicts
        $imageName = time() . '.' . $image->getClientOriginalExtension();

      $image->move(public_path('uploads/player_images'), $imageName);
            $imagePath = $imageName;

            // Update the image path in the database
            $player->update(['image' => basename($imagePath)]);
    }

    return redirect()->route('teamplayers', $player->team_id)->with('success', 'Player updated successfully.');
}
      public function uploadBonafide(Request $request)
    {
    $request->validate([
        'team_bonafide' => 'required|mimes:pdf|max:5120'
    ]);

    try {
       
         if ($request->hasFile('team_bonafide')) {
                $file = $request->file('team_bonafide');

                // Generate a unique file name for the image
                $fileName = time() . '.' . $file->getClientOriginalExtension();

                // Store the image in the public/uploads folder
                $file->move(public_path('uploads/bonafide'), $fileName);
                $filePath = $fileName;
           
           
            $user = Auth::user();
            $team = Team::find($user->team_id);
            if (isset($team)) {
                $team->bonafide = $fileName;
                $team->save();
            }
           
           return response()->json([
                'success' => true,
                'message' => 'Bonafide certificate uploaded successfully',
                'filename' => $fileName
            ]);

            }
      
       
        
        return response()->json([
            'success' => false,
            'message' => 'No file uploaded'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error uploading file: ' . $e->getMessage()
        ]);
    }
}

  
  
public function alltournament_csv(Request $request, $tournament_id) {
    $teams = TournamentTeam::where('tournament_teams.tournament_id', $tournament_id)
        ->join('tournaments', 'tournaments.id', '=', 'tournament_teams.tournament_id')
        ->join('teams', 'teams.id', '=', 'tournament_teams.team_id')
       ->whereNull('teams.deleted_at')
        ->select(
            'tournaments.name as tournament_name',
            'teams.name as team_name',
            'teams.email as team_email',
            'teams.phone as team_phone',
            'tournament_teams.payment',
            'tournament_teams.verified',
            'tournament_teams.match_preference'
        )
        ->get();

    if (!File::exists(public_path('pbsf-2025-tournament'))) {
        File::makeDirectory(public_path('pbsf-2025-tournament'), $mode = 0777, true, true);
    }

    $headings = [
        'Tournament Name',
        'Team Name',
        'Email',
        'Phone',
        'Match Preference',
        'Payment',
        'Verified',
    ];

    $dynamicFileName = "pbsf-2025-teams-tournament-$tournament_id-" . date("Y-m-d-H-i-s") . ".csv";
    $filename = public_path("pbsf-2025-tournament/$dynamicFileName");
    $handle = fopen($filename, 'w+');

    fputcsv($handle, $headings);

    // Fetch match preference mapping from config
    $matchPreferences = config('matchPreference');

    if ($teams->count() > 0) {
        foreach ($teams as $team) {
            fputcsv($handle, [
                $team->tournament_name ?? '-',
                $team->team_name ?? '-',
                $team->team_email ?? '-',
                $team->team_phone ?? '-',
                $matchPreferences[$team->match_preference] ?? '-', // Convert match preference to readable text
                $team->payment ? 'Yes': 'No',
                $team->verified ? 'Yes' : 'No', // Convert verified to "Yes"/"No"
            ]);
        }
    }
    fclose($handle);

    return Response::download($filename);
}
public function alltournament_players_csv(Request $request, $tournament_id) {
    // Fetch player details through tournament_teams
    $players = DB::table('players')
        ->join('teams', 'players.team_id', '=', 'teams.id')
        ->join('tournament_teams', 'teams.id', '=', 'tournament_teams.team_id')
        ->join('tournaments', 'tournament_teams.tournament_id', '=', 'tournaments.id')
        ->where('tournament_teams.tournament_id', $tournament_id)
       ->whereNull('players.deleted_at')
        ->select(
            'tournaments.name as tournament_name',
            'teams.name as team_name',
      		'players.empid as player_id',
            'players.name as player_name',
            'players.email as player_email',
            'players.phone as player_phone'
        )
        ->get();

    // Check and create directory if it doesn't exist
    if (!File::exists(public_path('pbsf-2025-tournament'))) {
        File::makeDirectory(public_path('pbsf-2025-tournament'), $mode = 0777, true, true);
    }

    $headings = [
        'Tournament Name',
        'Team Name',
      	'Player ID',
        'Player Name',
        'Player Email',
        'Player Phone',
    ];

    $dynamicFileName = "pbsf-2025-players-tournament-$tournament_id-" . date("Y-m-d-H-i-s") . ".csv";
    $filename = public_path("pbsf-2025-tournament/$dynamicFileName");
    $handle = fopen($filename, 'w+');

    fputcsv($handle, $headings);

    if ($players->count() > 0) {
        foreach ($players as $player) {
            fputcsv($handle, [
                $player->tournament_name ?? '-',
                $player->team_name ?? '-',
              	$player->player_id ?? '-',
                $player->player_name ?? '-',
                $player->player_email ?? '-',
                $player->player_phone ?? '-',
            ]);
        }
    }

    fclose($handle);

    return Response::download($filename);
}
  public function notapplied_teams_csv(Request $request, $tournament_id) {
    // Fetch teams that are not applied to the tournament
    $teams = DB::table('teams')
        ->leftJoin('tournament_teams', function ($join) use ($tournament_id) {
            $join->on('teams.id', '=', 'tournament_teams.team_id')
                 ->where('tournament_teams.tournament_id', '=', $tournament_id);
        })
        ->whereNull('tournament_teams.team_id') // Only fetch teams not linked to the tournament
      ->whereNull('teams.deleted_at')
        ->select(
            'teams.name as team_name',
            'teams.email as team_email',
            'teams.phone as team_phone'
        )
        ->get();

    // Check and create directory if it doesn't exist
    if (!File::exists(public_path('pbsf-2025-tournament'))) {
        File::makeDirectory(public_path('pbsf-2025-tournament'), $mode = 0777, true, true);
    }

    $headings = [
        'Team Name',
        'Email',
        'Phone',
    ];

    $dynamicFileName = "pbsf-2025-notapplied-teams-tournament-$tournament_id-" . date("Y-m-d-H-i-s") . ".csv";
    $filename = public_path("pbsf-2025-tournament/$dynamicFileName");
    $handle = fopen($filename, 'w+');

    fputcsv($handle, $headings);

    if ($teams->count() > 0) {
        foreach ($teams as $team) {
            fputcsv($handle, [
                $team->team_name ?? '-',
                $team->team_email ?? '-',
                $team->team_phone ?? '-',
            ]);
        }
    }

    fclose($handle);

    return Response::download($filename);
}

public function notapplied_players_csv(Request $request, $tournament_id) {
    // Fetch players belonging to not-applied teams
    $players = DB::table('players')
        ->join('teams', 'players.team_id', '=', 'teams.id')
        ->leftJoin('tournament_teams', function ($join) use ($tournament_id) {
            $join->on('teams.id', '=', 'tournament_teams.team_id')
                 ->where('tournament_teams.tournament_id', '=', $tournament_id);
        })
        ->whereNull('tournament_teams.team_id') // Only fetch players from teams not linked to the tournament
        ->whereNull('players.deleted_at')
        ->select(
            'teams.name as team_name',
      		'players.empid as player_id',
            'players.name as player_name',
            'players.email as player_email',
            'players.phone as player_phone'
        )
        ->get();

    // Check and create directory if it doesn't exist
    if (!File::exists(public_path('pbsf-2025-tournament'))) {
        File::makeDirectory(public_path('pbsf-2025-tournament'), $mode = 0777, true, true);
    }

    $headings = [
        'Team Name',
      	'Player ID',
        'Player Name',
        'Player Email',
        'Player Phone',
    ];

    $dynamicFileName = "pbsf-2025-notapplied-players-tournament-$tournament_id-" . date("Y-m-d-H-i-s") . ".csv";
    $filename = public_path("pbsf-2025-tournament/$dynamicFileName");
    $handle = fopen($filename, 'w+');

    fputcsv($handle, $headings);

    if ($players->count() > 0) {
        foreach ($players as $player) {
            fputcsv($handle, [
                $player->team_name ?? '-',
              	$player->player_id ?? '-',
                $player->player_name ?? '-',
                $player->player_email ?? '-',
                $player->player_phone ?? '-',
            ]);
        }
    }

    fclose($handle);

    return Response::download($filename);
}

public function exportTournamentGroups($tournament_id) 
{
    $q = TournamentGroup::query();
    $q->leftJoin('groups', 'tournament_groups.group_id', '=', 'groups.id')
      ->leftJoin('teams', 'tournament_groups.team_id', '=', 'teams.id')
      ->leftJoin('tournament_teams', function($join) {
          $join->on('tournament_groups.team_id', '=', 'tournament_teams.team_id')
              ->on('tournament_groups.tournament_id', '=', 'tournament_teams.tournament_id');
      })
      ->leftJoin('tournaments', 'tournament_groups.tournament_id', '=', 'tournaments.id')
      ->where('tournament_groups.tournament_id', $tournament_id)
   	  ->orderBy('groups.group_name', 'asc');

    $groups = $q->select(
        'groups.group_name',
        'teams.name as team_name',
        'tournament_teams.match_preference'
    )->get();

    // Create directory if it doesn't exist
    if (!File::exists(public_path('tournament-groups'))) {
        File::makeDirectory(public_path('tournament-groups'), $mode = 0777, true, true);
    }

    $headings = [
        'Group Name',
        'Team Name',
        'Match Preference'
    ];

    $dynamicFileName = "tournament-groups-" . date("Y-m-d-H-i-s") . ".csv";
    $filename = public_path("tournament-groups/$dynamicFileName");
    $handle = fopen($filename, 'w+');

    fputcsv($handle, $headings);

    // Get match preference mapping from the config file
    $matchPreferences = config('matchPreference');

    if (count($groups) > 0) {
        foreach ($groups as $group) {
            fputcsv($handle, [
                $group->group_name ?? '-',
                $group->team_name ?? '-',
                $matchPreferences[$group->match_preference] ?? '-'
            ]);
        }
    }

    fclose($handle);
    return Response::download($filename);
}
public function allmatchesdwnld(Request $request)
{
    $tournament_id = $request->query('tournament'); // Get tournament ID from query string

    if (!$tournament_id) {
        return redirect()->back()->with('error', 'Tournament ID is required.');
    }

    $matches = DB::table('schedule_matches')
        ->join('venues', 'schedule_matches.ground', '=', 'venues.id')
        ->join('teams as team1', 'schedule_matches.team1', '=', 'team1.id')
        ->join('teams as team2', 'schedule_matches.team2', '=', 'team2.id')
        ->leftJoin('groups', 'schedule_matches.group_id', '=', 'groups.id')
        ->where('schedule_matches.tournament_id', $tournament_id)
        ->whereNull('schedule_matches.deleted_at')
        ->select(
            'venues.name as venue',
            'schedule_matches.match_date_time as match_date',
            'team1.name as team_one',
            'team2.name as team_two',
            'groups.group_name as group_name'
        ) // Prevents duplicate rows
        ->orderBy('schedule_matches.match_date_time', 'asc')
        ->get();

    // Create directory if not exists
    $directory = public_path('pbsf-2025-tournament');
    if (!File::exists($directory)) {
        File::makeDirectory($directory, 0777, true, true);
    }

    $headings = ['Venue', 'Match Date', 'Team 1', 'Team 2', 'Group Name'];

    $dynamicFileName = "pbsf-2025-scheduled-matches-$tournament_id-" . date("Y-m-d-H-i-s") . ".csv";
    $filename = $directory . "/$dynamicFileName";
    $handle = fopen($filename, 'w+');

    fputcsv($handle, $headings);

    if ($matches->count() > 0) {
        foreach ($matches as $match) {
            fputcsv($handle, [
                $match->venue ?? '-',
                !empty($match->match_date) ? Carbon::parse($match->match_date)->format('Y-m-d h:i A') : '-', // 12-hour format with AM/PM
                $match->team_one ?? '-',
                $match->team_two ?? '-',
                $match->group_name ?? '-',
            ]);
        }
    }
    fclose($handle);

    return Response::download($filename);
}
  
  public function exportTeamsCsv1($tournament_id) {
    $teams = DB::table('teams')
        ->join('tournament_teams', 'teams.id', '=', 'tournament_teams.team_id')
        ->where('tournament_teams.tournament_id', $tournament_id)
        ->select('teams.id', 'teams.name')
        ->get();

    $exportDir = public_path('tournament-team-exports');
    if (!File::exists($exportDir)) {
        File::makeDirectory($exportDir, $mode = 0777, true, true);
    }
    $headings = [
        'S.No', 'Emp ID', 'Player Name', 
        'League 1', 'League 2', 'League 3', 
        'Pre Quarter', 'Quarter', 'Semi', 'Final'
    ];

    foreach ($teams as $team) {
        $players = DB::table('players')
            ->join('teams', 'players.team_id', '=', 'teams.id')
            ->join('tournament_teams', 'teams.id', '=', 'tournament_teams.team_id')
            ->where('teams.id', $team->id)
            ->where('tournament_teams.tournament_id', $tournament_id)
            ->select(
                'players.empid as player_id', 
                'players.name as player_name',
            )
            ->get();

        $filename = Str::slug($team->name) . '.csv';
        $filepath = $exportDir . '/' . $filename;
        $handle = fopen($filepath, 'w+');
        fputcsv($handle, $headings);

        $serialNo = 1;
        foreach ($players as $player) {
            fputcsv($handle, [
                $serialNo++,
                $player->player_id ?? '-',
                $player->player_name ?? '-',
                '', // League 1
                '', // League 2
                '', // League 3
                '', // Pre Quarter
                '', // Quarter
                '', // Semi
              	'', // Final
            ]);
        }

        fclose($handle);
    }
    $zipFilename = 'tournament_team_exports_' . date('Y-m-d-H-i-s') . '.zip';
    $zip = new ZipArchive;
    $zipPath = $exportDir . '/' . $zipFilename;
    
    if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
        $files = File::files($exportDir);
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) == 'csv') {
                $zip->addFile($file, basename($file));
            }
        }
        $zip->close();
    }
    return response()->download($zipPath)->deleteFileAfterSend(true);
}

public function exportTeamsCsv($tournament_id) {
   
    $teams = DB::table('teams')
        ->leftJoin('tournament_teams', 'teams.id', '=', 'tournament_teams.team_id')
        ->where('tournament_teams.tournament_id', $tournament_id)
        ->whereNull('tournament_teams.deleted_at')
        ->whereNull('teams.deleted_at')
        ->select('teams.id', 'teams.name') 
        ->distinct()
        ->get();

 
    $exportDir = public_path('tournament-team-exports');
    
   
    if (!File::exists($exportDir)) {
        File::makeDirectory($exportDir, 0777, true, true);
    } else {
        // Clear the directory to avoid old CSVs being included
        File::cleanDirectory($exportDir);
    }

   
    $headings = [
        'S.No', 'Emp ID', 'Player Name', 'League 1', 'League 2', 'League 3', 'Pre Quarter', 'Quarter', 'Semi', 'Final'
    ];

   
    foreach ($teams as $team) {
        
        $players = Player::where('team_id', $team->id)
                         ->select('empid as player_id', 'name as player_name')
                         ->get();
        
        
        $filename = Str::slug($team->name) . '-' . $team->id . '.csv';
        $filepath = $exportDir . '/' . $filename;
        
       
        $handle = fopen($filepath, 'w+');
        fputcsv($handle, $headings); 
        
        $serialNo = 1;
        foreach ($players as $player) {
            
            fputcsv($handle, [
                $serialNo++, 
                $player->player_id ?? '-', 
                $player->player_name ?? '-', 
                '', '', '', '', '', '', ''
            ]);
        }

        fclose($handle); 
    }

   
    $zipFilename = 'tournament_team_exports_' . date('Y-m-d-H-i-s') . '.zip';
    $zipPath = $exportDir . '/' . $zipFilename;
    $zip = new ZipArchive;

    
    if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
       
        $files = File::files($exportDir);
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) == 'csv') {
                $zip->addFile($file, basename($file));
            }
        }
        $zip->close(); 
    }

    // Return the zip file as a downloadable response
    return response()->download($zipPath)->deleteFileAfterSend(true);
}

    public function deleteplayer($id){
        $player = Player::findOrFail($id);
        $player->delete();
        return redirect()->back()->with('success', 'Player deleted successfully.');
    }

}
