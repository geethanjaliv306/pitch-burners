<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    //

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:teams,email|max:255',
            'phoneNumber' => 'required|string|max:20',
            'password' => 'required|string|min:5',
            'companyLogo' => 'nullable|string',
        ]);

        if ($request->filled('companyLogo')) {
            $imageData = $request->input('companyLogo');
            $imageName = 'company_logo_' . time() . '.jpg';
            $path = 'asset/images/' . $imageName;
            Storage::disk('public')->put($path, base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $imageData)));
            $validated['companyLogo'] = $path;
        }

        $team = new Team();
        $team->name = $validated['name'];
        $team->email = $validated['email'];
        $team->phone = $validated['phoneNumber'];
        $team->password = Hash::make($validated['password']);
        $team->logo = $validated['companyLogo'] ?? null;
        $team->save();

        return response()->json(['message' => 'Registration successful'], 201);
    }

}
