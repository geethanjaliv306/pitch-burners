<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Api\ImgController;
use App\Models\Team;
use App\Models\User;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PlayersController extends Controller
{
    //

    public function addPlayers(Request $request) {

        Log::info("playerImage" , [$request->input('playerImage')]);

        $validatedData = $request->validate([
            'playerName' => 'required|string|max:255',
            'email' => 'required|email|unique:players,email',
            'employeeID' => 'required|string|max:255',
            'phoneNumber' => 'required|string|max:15',
            'battingStyle' => 'required|string',
            'bowlingStyle' => 'required|string',
            'playerRole' => 'required|string',
            'playerImage' => 'nullable|string',
            // 'isCaptain' => 'boolean',
        ]);

        $player = new Player();
        $player->team_id = Auth::user()->id;
        $player->name = $validatedData['playerName'];
        $player->email = $validatedData['email'];
        $player->empid = $validatedData['employeeID'];
        $player->phone = $validatedData['phoneNumber'];
        $player->batting_style = $validatedData['battingStyle'];
        $player->bowling_style = $validatedData['bowlingStyle'];
        $player->role = $validatedData['playerRole'];
        // $player->image = $validated['playerImage'] ?? null;
        $player->is_captain = $request->input('isCaptain') ? 1 : 0;


        $isCaptain = $request->input('isCaptain');
        $player->is_captain = filter_var($isCaptain, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;


        // if ($request->filled('playerImage')) {
        //     $imageData = $request->input('playerImage');
        //     $imageName = 'player_image_' . time() . '.jpg';
        //     // $path = 'asset/images/' . $imageName;
        //     $path = new ImgController();
        //     $path = $path->generatePlayerImgPath($player->team_id, $imageName);
        //     Storage::disk('public')->put($path, base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $imageData)));
        //     $validated['playerImage'] = $path;
        //     $player->image = $path;
        // }

        if ($request->filled('playerImage')) {
            $imageData = $request->input('playerImage');
            $imageName = 'player_image_' . time() . '.jpg';

            $path = 'uploads/player_images/' . $imageName;

            Storage::disk('public')->put($path, base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $imageData)));

            $validated['playerImage'] = $imageName;
            $player->image = $imageName; // Save only the file name
        }


        $player->save();

        return response()->json(['message' => 'Player added successfully', 'player' => $player], 201);
   }

   public function updatePlayers(Request $request, $id)
   {

       $player = Player::findOrFail($id);

       $player->name = $request->input('playerName');
       $player->email = $request->input('email');
       $player->empid = $request->input('employeeID');
       $player->phone = $request->input('phoneNumber');
       $player->batting_style = $request->input('battingStyle');
       $player->bowling_style = $request->input('bowlingStyle');
       $player->role = $request->input('playerRole');
       $isCaptain = $request->input('isCaptain');
       $isCaptain = $request->input('isCaptain');
       $player->is_captain = filter_var($isCaptain, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

    //    if ($request->filled('playerImage')) {
    //        if ($player->image) {
    //            Storage::disk('public')->delete($player->image);
    //        }

    //        $imageData = $request->input('playerImage');
    //        $imageName = 'player_image_' . time() . '.jpg';
    //     //    $path = 'asset/images/' . $imageName;
    //          $path = new ImgController();
    //          $path = $path->generatePlayerImgPath($player->team_id, $imageName);
    //        Storage::disk('public')->put($path, base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $imageData)));
    //        $player->image = $path;
    //    }

    if ($request->filled('playerImage')) {
        Log::info($request->filled('playerImage'));
        if ($player->image) {
            $oldImagePath = public_path('uploads/player_images/' . $player->image);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        $imageData = $request->input('playerImage');
        $imageName = 'player_image_' . time() . '.jpg';
        $path = 'uploads/player_images/' . $imageName;

        file_put_contents(public_path($path), base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $imageData)));

        $validated['playerImage'] = $imageName;
        $player->image = $imageName;
    }




        $player->save();

        return response()->json([
            'success' => true,
            'message' => 'Player updated successfully',
            'player' => true

        ]);
   }

   public function delete($id)
   {
       if (!Auth::check()) {
           return response()->json(['error' => 'Unauthorized'], 401);
       }
       $player = Player::find($id);

       if (!$player) {
           return response()->json(['error' => 'Player not found'], 404);
       }

       $player->delete();

       return response()->json(['message' => 'Player deleted successfully']);
   }

   public function players(){

        $user = Auth::user();
        //$team = Team::find($user->id);
        $TeamId = $user->team_id;
        $Players = Player::where('team_id' , $TeamId)->get();


        return response()->json(['message' => 'Player fetched Sucessfully', 'players' => $Players , 'user' => $TeamId , ] , 201);

  }

  public function CompleteTeam(Request $request) {
    Log::info('Request received', [
        'input' => $request->all()
    ]);

    $teamIds = $request->input('teamIds');

    if (is_array($teamIds) && !empty($teamIds)) {
        foreach ($teamIds as $teamId) {
            Team::where('id', $teamId)->update(['is_added' => 1]);
        }
    } else {
        return response()->json(['message' => 'Invalid team IDs provided.'], 400);
    }

        $user = $request->user();
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        } else {
            return response()->json(['message' => 'No authenticated user found.'], 401);
        }

    return response()->json(['message' => 'User roles updated successfully.'], 200);
}

// public function getAvatarFormattedAttribute()
//     {
//         //return $this->avatar ? url("images/avatar/".$this->id."/".$this->avatar) : url(config('koala.default_avatar'));
//         return $this->avatar ? config('koala.s3imagebaseurl'). "/images/avatar/".$this->id."/".$this->avatar : url(config('koala.default_avatar'));
//     }
//     public function getAvatarOriginalAttribute() {
//         return $this->avatar ? config('koala.s3imagebaseurl'). "/images/avatar/".$this->id."/".$this->avatar : '';
//     }
//     public function getAvatarThumbnailAttribute() {
//         return $this->avatar ? config('koala.s3imagebaseurl'). "/images/avatar/".$this->id."/thumbnail/".$this->avatar : '';
//     }

}
