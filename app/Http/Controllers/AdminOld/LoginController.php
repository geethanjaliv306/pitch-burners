<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function index()
    {
        return view('frontend.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            if (Auth::attempt($request->only('email', 'password'))) {
                $user = Auth::user();

                if ($user->role == 1 || $user->role == 2) {
                    return response()->json(['success' => true, 'redirect_url' => route('dashboard')]);
                } elseif ($user->role == 0) {
                    $team = Team::where('id', $user->team_id)->first();
                    if ($team) {
                        return response()->json(['success' => true, 'redirect_url' => route('add-player')]);
                    }

                    return response()->json(['success' => false, 'message' => 'Invalid team.']);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'The password is incorrect.']);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'The email address does not exist.']);
        }
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
