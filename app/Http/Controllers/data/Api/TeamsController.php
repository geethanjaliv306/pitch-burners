<?php

namespace App\Http\Controllers\Api;

use App\Models\Team;
use App\Models\User;
use App\Models\Player;
use Illuminate\Http\Request;
use App\Mail\TeamRegistrationMail;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Api\ImgController;

class TeamsController extends Controller
{
    //

    public function register(Request $request){

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:teams,email|max:255',
            'phoneNumber' => 'required|string|max:20',
            'password' => 'required|string|min:5',
            'companyLogo' => 'nullable|string',
        ]);
        $imageName = null;

        // if ($request->filled('companyLogo')) {
        //     $imageData = $request->input('companyLogo');
        //     $imageName = 'company_logo_' . time() . '.jpg';
        //     // $path = 'asset/images/' . $imageName;
        //     $path = new ImgController();
        //     $path = $path->generateTeamImgPath($imageName);
        //     Storage::disk('public')->put($path, base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $imageData)));
        //     $validated['companyLogo'] = $path;
        // }

        if ($request->filled('companyLogo')) {
            $imageData = $request->input('companyLogo');
            $imageName = 'company_logo_' . time() . '.jpg';

            $path = 'uploads/team_logos/' . $imageName;

            Storage::disk('public')->put($path, base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $imageData)));

            $validated['companyLogo'] = $imageName ;
        }


        $plainPassword = $request->input('password');

        $team = new Team();
        $team->name = $validated['name'];
        $team->email = $validated['email'];
        $team->phone = $validated['phoneNumber'];
        $team->password = Hash::make($validated['password']);
        $team->logo = $imageName ?? null;
        $team->save();

        $user = new User;
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        // $user->role  = 1;
        $user->team_id = $team->id;
        $user->save();

        Mail::to($team->email)->send(new TeamRegistrationMail($team, $plainPassword));

        return response()->json(['message' => 'Registration successful'], 201);
    }

   public function Teams(){

     $Teams = Team::all();
    //  $user = Auth::user();
    //  $teamName = $user->name ;
    //  $TeamId = $user->team_id ;
    //  $Logo =Team::where('id' , $TeamId)->first();
    //  $TeamLogo = $Logo->logo;

     return response()->json(['message' => 'Team fetched Sucessfully', 'Teams' => $Teams]);
    }


    public function TeamDetails(){

        $Teams = Team::all();
        $user = Auth::user();
        $teamName = $user->name ;
        $TeamId = $user->team_id ;
        $Logo =Team::where('id' , $TeamId)->first();
        $TeamLogo = $Logo->logo;

        return response()->json(['message' => 'Team fetched Sucessfully', 'Teams' => $Teams , 'teamName' => $teamName ,'TeamLogo' => $TeamLogo]);
       }

    public function getPlayersByTeam($teamId)
    {
        Log::info('Team ID passed to route:', ['teamId' => $teamId]);
        $user = Auth::user();
        $TeamId = $user->id;
        $players = Player::where('team_id', $TeamId)->get();

        $teamLogo = Team::where('id', $teamId)->value('logo');

        return response()->json(['players' => $players , 'teamId' => $teamId , 'teamLogo' => $teamLogo]);
    }


    public function getPlayingElevens($teamId)
{
    Log::info('Team ID passed to route:', ['teamId' => $teamId]);

    $players = Player::where('team_id', $teamId)->get();

    $teamLogo = Team::where('id', $teamId)->value('logo');

    $teamName = Team::where('id', $teamId)->value('name');
    Log::info('Team ID passed to route:', ['teamName' => $teamName]);


    return response()->json(['players' => $players, 'teamId' => $teamId, 'teamLogo' => $teamLogo , 'teamName' , $teamName]);
}

public function getTeamCaptains($teamIds)
    {
        $teamIdsArray = explode(',', $teamIds);

        $captains = [];

        foreach ($teamIdsArray as $teamId) {
            $team = Team::find($teamId);

            if ($team) {
                $captain = $team->players()->where('is_captain', 1)->first();
                if ($captain) {
                    $captains[$teamId] = $captain;
                } else {
                    $captains[$teamId] = 'No captain';
                }
            } else {
                $captains[$teamId] = 'Team not found';
            }
        }

        return response()->json(['captains' => $captains], 200);
    }

    public function getPlayersDetailsByTeam($teamId)
    {
        $TeamId =$teamId;
        $players = Player::where('team_id', $TeamId)->get();
        $teamLogo = Team::where('id', $teamId)->value('logo');
        return response()->json(['players' => $players , 'teamId' => $teamId , 'teamLogo' => $teamLogo]);
    }

}
