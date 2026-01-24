<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\TeamRegistrationMail;
use Illuminate\Support\Facades\Validator;

class TeamsController extends Controller
{
    public function index()
    {
        return view('frontend.register');
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:teams,email',
                'phone' => ['required', 'regex:/^[7-9][0-9]{9}$/'],
                'logo' => 'required|image|mimes:jpeg,png,jpg,svg',
                'password' => 'required|string|confirmed|min:8',
            ]);

            $imageName = time() . '.' . $request->logo->getClientOriginalExtension();
            $logoPath = $request->file('logo')->move(public_path('uploads/team_logos'), $imageName);


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

            $AdminEmail = ['sapareshan@dsignzmedia.in', 'santhoshrajan7520@gmail.com'];

            Mail::to(array_merge([$team->email], $AdminEmail))->send(new TeamRegistrationMail($team));
          
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

        $selectedTournamentId = $request->input('tournament_id');

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

        return view('frontend.teams', compact('tournaments', 'teams', 'selectedTournamentId'));
    }


    public function show($id)
    {
        $team = Team::with('players')->findOrFail($id);
        return view('frontend.teams-squad', compact('team'));
    }
}
