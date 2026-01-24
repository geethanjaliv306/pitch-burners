<?php

namespace App\Http\Controllers\API;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    //

   public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    // Attempt to find the user by email
    $user = User::where('email', $credentials['email'])->first();

    // Check if the user exists and password is correct
    if ($user && Hash::check($credentials['password'], $user->password)) {

        // Check if the user has an admin role
        if ($user->role == 1 || $user->role == 2) {
            $token = $user->createToken('authToken')->plainTextToken;
            return response()->json([
                'success' => true,
                'token' => $token,
                'admin' => true,
            ]);
        }

        // For non-admin users, check team registration status
        $team = Team::where('id', $user->team_id)->first();
        if ($team) {
            $token = $user->createToken('authToken')->plainTextToken;
            return response()->json([
                'success' => true,
                'token' => $token,
                'team_status' => $team->is_added == 1 ? 'Registered' : 'Not Registered',
                'user' => true,
            ]);
        } else {
            // If no team is found for the user
            return response()->json([
                'success' => false,
                'message' => 'Team not found. Please contact the admin.',
            ], 200);
        }
    } else {
        // If no user is found or password is incorrect
        return response()->json([
            'failed' => true,
            'message' => 'Invalid credentials'
        ], 200);
    }
}



    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        return response()->json(['success' => true, 'message' => 'Logged Out']);
    }

        public function checkUserRoleAndTeamStatus($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $team = Team::where('id', $user->team_id)->first();

        // Check if the user has an admin role (role 1 or 2)
        if ($user->role == 1 || $user->role == 2) {
            return response()->json(['showMenuForAdmin' => true], 200);
        }

        // Check if the user has a non-admin role and team is not added
        if ($user->role == 0 && (!$team || $team->is_added != 1)) {
            return response()->json(['showMenuForUser' => true], 200);
        }

        // Default response if none of the above conditions are met
        return response()->json(['showMenuForAdmin' => false], 200);
    }


}
