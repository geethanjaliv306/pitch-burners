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
        $isAppliedTournament = TournamentTeam::where('team_id', $team_id)->exists();
        return view('admin.add-new-player', compact('players', 'team','team_logo','isAppliedTournament'));
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
               //'email' => 'required|email|unique:teams,email',
                'email' => 'required|email',
                'empid' => 'required|string',
                'phone' => 'required|string|max:15',
                //'image' => 'required|image|mimes:jpg,jpeg,png|max:1000',
                //'batting_style' => 'required|string',
                //'bowling_style' => 'required|string',
                //'role' => 'required|string',
                'ball_preferences' => 'required|string',
            ]);

            if (!Auth::check()) {
                return redirect()->route('login');
            }

           
            $team = Auth::user();
            $team_id = $team->team_id;
      		
            if ($request->hasFile('image')) {
                $image = $request->file('image');

                // Generate a unique file name for the image
                $imageName = time() . '.' . $image->getClientOriginalExtension();

                // Store the image in the public/uploads folder
                $image->move(public_path('uploads/player_images'), $imageName);
                $imagePath = $imageName;

            }else {
                // Set the placeholder static image path 
                $imagePath = '/dummy-avatar.png';  // Ensure this file exists in the folder
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
                'ball_preferences' => $request->input('ball_preferences')
            ]);

            return redirect()->route('add-player')->with('success', 'Player added successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json(['errors' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

  public function checkEmail(Request $request)
{
    $email = $request->email;
    $playerId = $request->player_id;

    $exists = Player::where('email', $email)
        ->when($playerId, function ($query, $playerId) {
            return $query->where('id', '!=', $playerId);
        })
        ->exists();

    return response()->json(['success' => !$exists]);
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
            // 'email' => 'required|email|unique:players,email,' . $request->player_id,
          'email' => 'required|email', 
            'empid' => 'required|string',
            'phone' => 'required|string|max:15',
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
            'batting_style' => 'nullable|string',
            'bowling_style' => 'nullable|string',
            'role' => 'required|string',
        ]);

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $team = Auth::user();
        $team_id= Auth::user()->team_id;

        $player = Player::where('id', $id)->where('team_id',  $team_id)->firstOrFail();
      
		  $imagePath = $player->image;

         if ($request->hasFile('image')) {
             $image = $request->file('image');
             $imageName = time() . '.' . $image->getClientOriginalExtension();
             $image->move(public_path('uploads/player_images'), $imageName);
             $imagePath = $imageName;
         } elseif (!$imagePath) {
             // Set default image only if no existing or new image
             $imagePath = 'dummy-avatar.png';
         }

        $is_captain = $request->is_captain == 'on' ? 1 : 0;

        $player->update([
            'name' => $request->name,
            'email' => $request->email,
            'empid' => $request->empid,
            'phone' => $request->phone,
            'image' => $imagePath,
            'batting_style' => $request->batting_style,
            'bowling_style' => $request->bowling_style,
            'role' => $request->role,
            'is_captain' => $is_captain,
          	'ball_preferences' => $request->ball_preferences,
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
      
        $emailParts = explode('@', $player->email);
        $randomDigits = mt_rand(100, 999); // Generate a random 3-digit number
        if (count($emailParts) === 2) {
            $updatedEmail = $emailParts[0] . '_deleted' . $randomDigits . '@' . $emailParts[1];
        } else {
            $updatedEmail = $player->email . '_deleted' . $randomDigits;
        }

        $player->update(['email' => $updatedEmail]);
      
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
      	//$isAppliedTournament = TournamentTeam::where('team_id', $team_id)->exists();
        return view('admin.user-tournaments', compact('players', 'team','team_logo','tournaments'));
    }
    public function applyTournament($tournament_id, Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
      
      
      
        $team_id = Auth::user()->team_id;
      
       

        $existingEntry = TournamentTeam::where('tournament_id', $tournament_id)
                                        ->where('team_id', $team_id)
                                        ->first();

       // if ($existingEntry) {
            //return redirect()->back()->with('message', 'You have already applied for this tournament.');
       // }
      
     
        
      
        $team = Team::where('id', $team_id)->first();
        $team->is_added = 1;
        $team->save();
      
     
      //return response()->json(['whoami' => 'krish', 'team_id' => $team->id]);

      $test=  TournamentTeam::create([
            'tournament_id' => $tournament_id,
            'team_id' => $team_id,
            'qualified' => 1,
            'match_preference' => $request->match_preference , 
    
   
        ]);
      
       //print_r( $test);
      //exit;

        return redirect()->back()->with('success', 'Successfully applied for the tournament!');
    }
}
