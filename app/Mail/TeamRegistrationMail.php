<?php

namespace App\Mail;

use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TeamRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $team;

    public function __construct(Team $team)
    {

        $this->team = $team;

    }

    public function build()
    {
        return $this->view('emails.teams_registration')
                    ->subject('Team Registration Confirmation')
                    ->with([
                        'teamName' => $this->team->name,
                        'teamEmail' => $this->team->email,
                    ]);
    }
}
