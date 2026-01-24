<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $user;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($otp,$user)
    {
        $this->otp = $otp;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your OTP Code')->view('emails.forget_otpmail');
    }
}
