<?php

namespace App\Mail;

use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TeamRegistrationAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public $team;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Team $team)
    {
        $this->team = $team;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
	     $teamEmail = trim($this->team->email);
      	 if (str_contains($teamEmail, 'yopmail.com')){
           	     $teamEmail = str_replace(['@', '.'], [' [at] ', ' [dot] '], $teamEmail);
         } 
        return $this->view('emails.teams_registration_admin')
                    ->subject('New Team Registered - '. $this->team->name)
                    ->with([
                        'teamName' => $this->team->name,
                        'teamEmail' => $teamEmail,
                        'teamPhoneNumber' => $this->team->phone,
                    ]);
    }
}
