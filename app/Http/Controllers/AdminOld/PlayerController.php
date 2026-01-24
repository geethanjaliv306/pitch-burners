<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\Team;
use App\Models\Player;
use App\Models\Tournament;
use App\Models\TournamentTeam;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlayerController extends Controller
{
    public function index()
     {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $team = Auth::user();
        $team_id= Auth::user()->team_id;

        $team_logo = Team::where('id', $team_id)->first();
        $players = Player::where('team_id', $team_id)->get();
        return view('admin.add-new-player', compact('players', 'team','team_logo'));
    }

    public function create()
     {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $team = Auth::user();
        return view('admin.create-player', compact('team'));
    }

     public function store(Request $request)
    {
       
       
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:players,email',
                'empid' => 'required|string',
               'phone' => ['required', 'regex:/^[6-9][0-9]{9}$/'],
                'image' => 'required|image|mimes:jpg,jpeg,png',
                'batting_style' => 'nullable|string',
                'bowling_style' => 'nullable|string',
                'role' => 'required|string',
            ]);

            if (!Auth::check()) {
                return redirect()->route('login');
            }

            $team = Auth::user();
            $team_id = $team->team_id;
          
             $imagePath = null;

        // Handle the image file upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imagePath = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/player_images'), $imagePath);
        }



            $is_captain = $request->has('is_captain') ? 1 : 0;

            Player::create([
                'team_id' => $team_id,
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'empid' => $request->input('empid'),
                'phone' => $request->input('phone'),
                'image' => $imagePath,
                'batting_style' => $request->input('batting_style'),
                'bowling_style' => $request->input('bowling_style'),
                'role' => $request->input('role'),
                'is_captain' => $is_captain,
            ]);
           

            return redirect()->back()->with('success', 'Player added successfully!');
         
          
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json(['errors' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

     public function submitTeam(Request $request)
    {
        $team = Team::find($request->team_id);

        // Count the number of players in the team
        $playerCount = $team->players->count();

        // Validate player count (minimum 11 and maximum 20)
        if ($playerCount < 11) {
            return redirect()->back()->withErrors('You need at least 11 players to submit the team.');
        }

        if ($playerCount > 20) {
            return redirect()->back()->withErrors('You cannot have more than 20 players in the team.');
        }

        // Update the team status to "is_added = 1"
        $team->update(['is_added' => 1]);

        return redirect()->route('user-tournaments')->with('success', 'Team players added successfully!');
    }

    public function edit($id)
     {
        if (!Auth::check()) {
            return redirect()->route('login');
        }


        $team = Auth::user();
        $team_id= Auth::user()->team_id;

        $player = Player::where('id', $id)->where('team_id',  $team_id)->firstOrFail();
        return view('admin.edit-player', compact('player', 'team'));
    }

   public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:players,email,' . $id,
        'empid' => 'required|string|unique:players,empid,' . $id,
        'phone' => 'required|string|max:15',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'batting_style' => 'nullable|string',
        'bowling_style' => 'nullable|string',
        'role' => 'required|string',
    ]);

    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $team_id = Auth::user()->team_id;

    $player = Player::where('id', $id)->where('team_id', $team_id)->firstOrFail();

    // Handle the image file upload
    if ($request->hasFile('image')) {
        $file = $request->file('image');
        $imagePath = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/player_images'), $imagePath);
    } else {
        // If no new image is uploaded, keep the existing image
        $imagePath = $player->image;
    }

    $is_captain = $request->is_captain == 'on' ? 1 : 0;

    $player->update([
        'name' => $request->name,
        'email' => $request->email,
        'empid' => $request->empid,
        'phone' => $request->phone,
        'image' => $imagePath, // Use the existing image if no new image is uploaded
        'batting_style' => $request->batting_style,
        'bowling_style' => $request->bowling_style,
        'role' => $request->role,
        'is_captain' => $is_captain,
    ]);

    return redirect()->back()->with('success', 'Player updated successfully!');
}


    public function destroy($id)
     {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $team = Auth::user();
        $team_id= Auth::user()->team_id;

        $player = Player::where('id', $id)->where('team_id',  $team_id)->firstOrFail();
        $player->delete();

        return redirect()->back()->with('success', 'Player deleted successfully!');
    }
     public function user_teams()
     {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $team = Auth::user();
        $team_id= Auth::user()->team_id;

        $team_logo = Team::where('id', $team_id)->first();
        $players = Player::where('team_id', $team_id)->get();
        return view('admin.user-teams', compact('players', 'team','team_logo'));
    }

    public function user_tournaments()
     {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $team = Auth::user();
        $team_id= Auth::user()->team_id;

        $team_logo = Team::where('id', $team_id)->first();
        $players = Player::where('team_id', $team_id)->get();

        $tournaments = Tournament::all();
        return view('admin.user-tournaments', compact('players', 'team','team_logo','tournaments'));
    }
    public function applyTournament($tournament_id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $team_id = Auth::user()->team_id;

        // Check if the tournament and team already exist in the table
        $existingEntry = TournamentTeam::where('tournament_id', $tournament_id)
                                        ->where('team_id', $team_id)
                                        ->first();

        if ($existingEntry) {
            return redirect()->back()->with('message', 'You have already applied for this tournament.');
        }

        // Store the tournament and team in the database
        TournamentTeam::create([
            'tournament_id' => $tournament_id,
            'team_id' => $team_id,
            'qualified' => 1, // Set qualified to 1
        ]);

        return redirect()->back()->with('success', 'Successfully applied for the tournament!');
    }

}
