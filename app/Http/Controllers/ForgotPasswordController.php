<?php

namespace App\Http\Controllers;

use App\Mail\ForgetPassword;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;

class ForgotPasswordController extends Controller
{

    public function forget_password() {
        return view('frontend.forget_password');
    }


   public function sendOtp(Request $request)
    {
        $email = $request->email;

        $key = 'otp-send:' . $email;

        if (RateLimiter::tooManyAttempts($key, 3)) { // 3 OTPs per 5 minutes
            return response()->json([
                'message' => 'Too many OTP requests. Please try again later.'
            ], 429); // HTTP 429 Too Many Requests
        }

        RateLimiter::hit($key, 300); // 300 seconds = 5 minutes

        // Proceed with OTP sending logic
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'Please enter your email address',
            'email.email' => 'Please enter a valid email address',
            'email.exists' => 'This email address is not registered with us'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();
        $otp = random_int(100000, 999999);
        $user->forget_otp = $otp;
        $user->otp_created_at = now();
        $user->save();

        Mail::to($user->email)->send(new ForgetPassword($otp, $user));

        return response()->json(['message' => 'OTP has been sent to your email.']);
    }
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'Please enter your email address',
            'email.email' => 'Please enter a valid email address',
            'email.exists' => 'This email address is not registered with us'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->forget_otp !== $request->otp) {
            return response()->json(['message' => 'Invalid OTP'], 400);
        }
        $otpExpiration = \Carbon\Carbon::parse($user->otp_created_at)->addMinutes(5);

        if (now()->greaterThan($otpExpiration)) {
            return response()->json(['message' => 'OTP has expired'], 400);
        }
        return response()->json(['message' => 'OTP verified successfully', 'redirect' => route('resetpassword.form', ['email' => $request->email])]);
    }

       public function showResetPasswordForm(Request $request)
       {
           $email = $request->query('email'); 
           return view('frontend.reset-password', compact('email'));
       }


       public function resetPassword(Request $request)
        {
            $user = User::where('email', $request->email)->first();


            if ($user) {
                $user->password = Hash::make($request->new_password);
                $user->forget_otp = null; 
                $user->save();

                return redirect()->route('login')->with('status', 'Password has been reset successfully.');
            }

            return redirect()->back()->withErrors(['email' => 'User not found.']);
        }


}
