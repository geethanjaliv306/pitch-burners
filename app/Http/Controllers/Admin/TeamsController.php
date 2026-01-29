<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Team;
use App\Models\Player;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Mail\TeamRegistrationMail;
use App\Mail\TeamRegistrationAdminMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class TeamsController extends Controller
{
    public function index()
    {
       if(Auth::check()) {
            $user = Auth::user();
            if ($user->role == 1 || $user->role == 2) {
                // return response()->json(['success' => true, 'redirect_url' => route('dashboard')]);
                return redirect('dashboard');
            } elseif ($user->role == 0) {
                    // return response()->json(['success' => true, 'redirect_url' => route('add-player')]);
                return redirect('teams-squad');
            }
        }
        return view('frontend.register');
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:teams,email',
                'phone' => 'required|string|max:15',
                //'logo' => 'required|image|mimes:jpeg,png,jpg,svg',
				//'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:1',
                'password' => 'required|string|confirmed|min:8',
            ]);

        //    $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
        //         'secret'   => '6Ld8cacqAAAAAA295CV28wYl7knmpWyzokGtNIGK',
        //         'response' => $request->input('g-recaptcha-response'),
        //         'remoteip' => $request->ip(),
        //     ]);

        //     if (!$response->json('success')) {
        //         return redirect()->route('register')->withErrors(['captcha' => 'Failed to validate captcha.']);
        //     }

           // $logoPath = $request->file('logo') ? $request->file('logo')->store('uploads/team_logos', 'public') : null;
          
        
            if ($request->hasFile('logo')) {
                  $image = $request->file('logo');

                  // Generate a unique file name for the image
                  $imageName = time() . '.' . $image->getClientOriginalExtension();

                  // Store the image in the public/uploads folder
                  $image->move(public_path('uploads/team_logos'), $imageName);
                  $logoPath = $imageName;

              }
               else {
                // Set the placeholder static image path 
                $logoPath = 'dummy-avatar.png'; 
              }

            $team = Team::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'logo' => $logoPath ? basename($logoPath) : null,
                'password' => Hash::make($validatedData['password']),
            ]);

            User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'role' => 0,
                'team_id'=> $team->id,
            ]);

            $teamName = $validatedData['name'] ;

            // $AdminEmail = ['team@dsignzmedia.in', 'pitchburnerscricketfoundation@gmail.com', 'santhoshrajan7520@gmail.com' , 'sapareshan@dsignzmedia.in' , 'cricket@pitchburners.com'];
            $AdminEmail = ['sapareshan@dsignzmedia.in','bvishwa33@gmail.com','shyamsundarj27@gmail.com'];
			//$AdminEmail = ['santhoshrajan7520@gmail.com' , 'sapareshan@dsignzmedia.in', 'al71one2002@gmail.com'];
          //$AdminEmail = ['ranjith@dsignzmedia.in'];
          
            Mail::to($team->email)->send(new TeamRegistrationMail($team));
			Mail::to($AdminEmail)->send(new TeamRegistrationAdminMail($team));

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'redirect_url' => route('login')]);
            }

            return redirect()->route('login')->with('success', 'Registration successful!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => ['email' => 'This email is already registered.']], 422);
            }
            return back()->withInput()->withErrors(['email' => 'This email is already registered.']);
        }
    }
  
      public function team_view(Request $request)
    {
        $tournaments = Tournament::all();

       // $selectedTournamentId = $request->input('tournament_id', $tournaments->first()->id ?? null);
       // $defaultTournamentId = Tournament::where('id', 13)->exists() ? 13 : null;
          $defaultTournamentId = Tournament::orderBy('start_date', 'desc')->value('id');
          $selectedTournamentId = $request->input('tournament_id', $defaultTournamentId);

        $query = Team::join('tournament_teams', 'teams.id', '=', 'tournament_teams.team_id')
            ->whereNull('teams.deleted_at')
            ->whereNull('tournament_teams.deleted_at')
            ->select('teams.*');

        if ($selectedTournamentId) {
            $query->where('tournament_teams.tournament_id', $selectedTournamentId);
        }

        $teams = $query->get();

        return view('frontend.teams', compact('tournaments', 'teams', 'selectedTournamentId'));
    }



   public function team_view1(Request $request)
    {
     
        $tournaments = Tournament::all();

        $selectedTournamentId = $request->input('tournament_id');
       //$selectedTournamentId = $request->input('tournament_id', $tournaments->first()->id ?? null);

        if ($selectedTournamentId) {
            $teams = Team::join('tournament_teams', 'teams.id', '=', 'tournament_teams.team_id')
                ->where('tournament_teams.tournament_id', $selectedTournamentId)
                ->select('teams.*')
               ->distinct()
                ->get();
        } else {
            $teams = Team::join('tournament_teams', 'teams.id', '=', 'tournament_teams.team_id')
                ->select('teams.*')
               ->distinct()
                ->get();
        }
         foreach ($teams as $team) {
        // Fetch players excluding "1737721046.jpg"
        $players = Player::where('team_id', $team->id)
            ->whereNull('deleted_at')
            ->where('image', '!=', '1737721046.jpg')
            ->inRandomOrder()
            ->get(['image', 'name']);

        // If fewer than 3 valid players exist, allow "1737721046.jpg"
        if ($players->count() < 3) {
            $players = Player::where('team_id', $team->id)
                ->whereNull('deleted_at')
                ->inRandomOrder()
                ->get(['image', 'name']);
        }

        // Select 3 players, ensuring diversity in images
        $selectedPlayers = [];
        $imageCount = [];

        foreach ($players as $player) {
            // Count occurrences of each image
            $imageCount[$player->image] = ($imageCount[$player->image] ?? 0) + 1;

            if (count($selectedPlayers) < 2) {
                $selectedPlayers[] = $player;
            } else {
                // Ensure the third player has a different image if possible
                if (!in_array($player->image, array_column($selectedPlayers, 'image')) || count($selectedPlayers) < 3) {
                    $selectedPlayers[] = $player;
                }
            }

            if (count($selectedPlayers) === 3) {
                break;
            }
        }

        $team->players = collect($selectedPlayers);
    }
    

        return view('frontend.teams', compact('tournaments', 'teams', 'selectedTournamentId'));
    }

    public function show($id)
    {
        $team = Team::with('players')->findOrFail($id);
        return view('frontend.teams-squad', compact('team'));
    }
  
   public function profileEdit(){

          $TeamId = Auth::user()->team_id;
          $Team= Team::where('id' , $TeamId)->first();

        return view('frontend.profile-update' , compact('Team'));
    }


  public function profileUpdate(Request $request, $id)
    {

            // Validate the incoming request
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:teams,email,' . $id,
                'phone' => 'nullable|string|max:15',
                'logo' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            ]);

            // Find the team by ID or throw a 404 error
            $team = Team::findOrFail($id);

            // Handle image upload
            if ($request->hasFile('logo')) {
                $image = $request->file('logo');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                // Delete old image if it exists and isn't the default
                if ($team->logo && $team->logo !== 'dummy-avatar.png') {
                    $oldImagePath = public_path('uploads/team_logos/' . $team->logo);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                // Move new image
                $image->move(public_path('uploads/team_logos'), $imageName);
                $team->logo = $imageName;
            } elseif (!$team->logo) {
                $team->logo = 'dummy-avatar.png';
            }

            // Update team details
            $team->fill([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
            ])->save();

            // Update associated user if exists
            if ($user = User::where('team_id', $team->id)->first()) {
                $user->update([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                ]);
            }


            return redirect()->route('user-tournaments')->with('success', 'Profile updated successfully.');
    }



}
