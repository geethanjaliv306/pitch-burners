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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function storeTournament(Request $request)
     {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|array',
            'ground' => 'required|array',
            'organiser_name' => 'required|string|max:255',
            'organiser_contact' => 'required|numeric|digits:10',
            'allow_players' => 'boolean',
            'start_date' => 'required|date_format:d/m/Y',
            'end_date' => 'required|date_format:d/m/Y|after:start_date',
            'tournament_category' => 'required|string|in:Limited Overs,Box Cricket',
            'ball_type' => 'required|string|in:Red Tennis,Green Tennis',
        ]);

            $validatedData['start_date'] = Carbon::createFromFormat('d/m/Y', $validatedData['start_date'])->format('Y-m-d');
            $validatedData['end_date'] = Carbon::createFromFormat('d/m/Y', $validatedData['end_date'])->format('Y-m-d');

            $validatedData['city'] = implode(',', $validatedData['city']);
            $validatedData['ground'] = implode(',', $validatedData['ground']);

        $tournament = Tournament::create($validatedData);

        return redirect()->route('tournaments-view')->with('success', 'Tournament created successfully!');
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
            'allow_players' => 'boolean',
            'start_date' => 'required|date_format:d/m/Y',
            'end_date' => 'required|date_format:d/m/Y|after_or_equal:start_date',
            'tournament_category' => 'required|string|in:Limited Overs,Box Cricket',
            'ball_type' => 'required|string|in:Red Tennis,Green Tennis',
        ]);
        $tournament = Tournament::findOrFail($id);
        $tournament->update([
            'name' => $request->name,
            'city' => implode(',', $request->city),
            'ground' => implode(',', $request->ground),
            'organiser_name' => $request->organiser_name,
            'organiser_contact' => $request->organiser_contact,
            'allow_players' => $request->has('allow_players') ? 1 : 0,
            'start_date' => Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d'),
            'end_date' => Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d'),
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
        ->paginate(10);

    return view('admin.my-tournaments', compact('tournaments', 'searchName', 'searchBallType', 'searchDate'));
}

    public function tournament_show($tournament_id){

        $tournament = Tournament::findOrFail($tournament_id);

        // Get all teams without pagination
        $paginatedTeams = $tournament->teams()->whereNull('tournament_teams.deleted_at')->paginate(3);

        // Count players in each team
        foreach ($paginatedTeams as $team) {
            $players_count = Player::where('players.team_id', $team->id)->count();
            $team->players_count = $players_count;
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

   public function Tournamentteams(Request $request, $tournament_id)
    {
        $tournament = Tournament::findOrFail($tournament_id);

        // Get the search query from the request
        $search = $request->input('search', '');

        // Filter paginated teams based on the search query
        $paginatedTeams = DB::table('teams')
        ->join('tournament_teams', 'teams.id', '=', 'tournament_teams.team_id')
        ->where('tournament_teams.tournament_id', $tournament_id)
        ->whereNull('tournament_teams.deleted_at')
        ->when($search, function ($query, $search) {
            $query->where('teams.name', 'LIKE', "%$search%");
        })
        ->select('teams.*', 'tournament_teams.*', 'tournament_teams.payment', 'tournament_teams.verified', 'teams.is_added')
        ->paginate(10);


        $allTeams = Team::whereIn('is_added', [1, 2])->get();

        // Add player counts to each team
        foreach ($paginatedTeams as $team) {
            $players_count = Player::where('players.team_id', $team->id)->count();
            $team->players_count = $players_count;
        }
        
        return view('admin.my-tournaments-teams', compact('tournament', 'paginatedTeams', 'allTeams', 'search'));
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
            $message = 'Access granted successfully!';
        } elseif ($team->is_added == 2) {
            $team->is_added = 1; // Revoke access
            $message = 'Access revoked successfully!';
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

        TournamentRound::create([
            'tournament_id' => $tournament_id,
            'number_of_overs' => $request->number_of_overs,
            'overs_per_bowler' => $request->overs_per_bowler,
            'type' => $request->type,
            'teams_to_qualify' => $request->teams_to_qualify
        ]);

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
                           ->where(function ($query) use ($match) {
                               $query->where('team_id', $match->teamA_id)
                                     ->orWhere('team_id', $match->teamB_id);
                           })
                           ->orderByDesc('total_points')  // Sort by total points first
                           ->orderByDesc('net_run_rate')  // Use net_run_rate as tiebreaker
                           ->take($round->teams_to_qualify)  // Fetch the number of teams specified in `teams_to_qualify`
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
                              ->where('qualified', 1)
                               ->where('payment', 1)
                            ->where('verified', 1);
                    })
                    ->whereNotIn('id', $selectedTeams)
                    ->get();
                    
                     $allTeamss = Team::whereIn('is_added', [1, 2])
                    ->whereIn('id', function ($query) use ($tournament_id) {
                        $query->select('team_id')
                              ->from('tournament_teams')
                              ->where('tournament_id', $tournament_id)
                              ->where('qualified', 1)
                               ->where('payment', 1)
                            ->where('verified', 1);
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

    public function Tournamentmatches($tournament_id, $round_id = null)
     {
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

        $matches = DB::table('schedule_matches')
            ->join('venues', 'schedule_matches.ground', '=', 'venues.id')
            ->join('teams as team1', 'schedule_matches.team1', '=', 'team1.id')
            ->join('teams as team2', 'schedule_matches.team2', '=', 'team2.id')
            ->where('schedule_matches.tournament_id', $tournament_id)
            ->where('schedule_matches.round_id', $round_id)
            ->whereNull('schedule_matches.deleted_at')
            ->select(
                'schedule_matches.*',
                'venues.name as ground_name',
                'team1.name as team_one_name',
                'team2.name as team_two_name'
            )
            ->orderBy('schedule_matches.match_date_time', 'asc')
            ->paginate(10);

        return view('admin.my-tournaments-matches', compact('groups', 'rounds', 'tournament', 'groundArray', 'matches', 'showgroups'));
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
                    'in:Red Tennis,Green Tennis'
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
                'match_date_time' => \Carbon\Carbon::createFromFormat('d/m/Y, h:i A', $validatedData['match_date_time']),
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
        // dd($request->all());
        $validatedData = $request->validate([
            'team_one' => 'required|integer',
            'team_two' => 'required|integer',
            'ground' => 'required|integer',
            'match_date_time' => 'required',
            'number_of_overs' => 'required|integer',
            'overs_per_bowler' => 'required|integer|max:'.$request->number_of_overs,
            'ball_type' => 'required|string',
        ]);

        $match->update([
            'team1' => $validatedData['team_one'],
            'team2' => $validatedData['team_two'],
            'ground' => $validatedData['ground'],
            'match_date_time' => \Carbon\Carbon::createFromFormat('d/m/Y, h:i A', $validatedData['match_date_time']),
            'number_of_overs' => $validatedData['number_of_overs'],
            'overs_per_bowler' => $validatedData['overs_per_bowler'],
            'type' => $validatedData['ball_type'],
        ]);

        return redirect()->back()->with('success', 'Match updated successfully.');
    }

   public function schedulematch_view(Request $request)
{
    $searchTeam = $request->input('team', ''); // Search team name
    $searchGround = $request->input('ground', ''); // Search ground
    $searchDate = $request->input('date', ''); // Search date

    $matches = DB::table('schedule_matches')
        ->join('venues', 'schedule_matches.ground', '=', 'venues.id')
        ->join('teams as team1', 'schedule_matches.team1', '=', 'team1.id')
        ->join('teams as team2', 'schedule_matches.team2', '=', 'team2.id')
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
        ->select(
            'schedule_matches.*',
            'venues.name as ground_name',
            'team1.name as team_one_name',
            'team2.name as team_two_name'
        )
        ->orderBy('schedule_matches.match_date_time', 'asc')
        ->paginate(10);

    return view('admin.view-schedule-matches', compact('matches', 'searchTeam', 'searchGround', 'searchDate'));
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
            'name',
            'email',
            'empid',
            'phone',
            'image',
            'batting_style',
            'bowling_style',
            'role'
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
            'image' => 'nullable|image|max:2048',
        ]);

        // Update player details except image
        $player->update($request->except('image'));

        // Handle the image upload if present
        if ($request->hasFile('image')) {
            // Get the original name of the file
            $originalName = $request->file('image')->getClientOriginalName();

            // Store the file in the specified directory and keep the original name
            $path = $request->file('image')->storeAs(
                'uploads/player_images',
                $originalName,
                'public'
            );

            // Update the image path in the database
            $player->update(['image' => basename($path)]);
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
        'name',
        'email',
        'empid',
        'phone',
        'image',
        'batting_style',
        'bowling_style',
        'role'
    )->get();

    // Fetch tournaments associated with the team
    $tournaments = DB::table('tournaments')
        ->join('tournament_teams', 'tournaments.id', '=', 'tournament_teams.tournament_id')
        ->where('tournament_teams.team_id', $team_id)
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
            'image' => 'nullable|image|max:2048',
        ]);

        // Update player details except image
        $player->update($request->except('image'));

        // Handle the image upload if present
        if ($request->hasFile('image')) {
            // Get the original name of the file
            $originalName = $request->file('image')->getClientOriginalName();

            // Store the file in the specified directory and keep the original name
            $path = $request->file('image')->storeAs(
                'uploads/player_images',
                $originalName,
                'public'
            );

            // Update the image path in the database
            $player->update(['image' => basename($path)]);
        }

        return redirect()->route('teamplayers', $player->team_id)->with('success', 'Player updated successfully.');
    }




}
