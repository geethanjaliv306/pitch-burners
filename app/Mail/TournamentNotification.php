<?php

namespace App\Mail;

use App\Models\MailContent;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Tournament;

class TournamentNotification extends Mailable
{
    use Queueable, SerializesModels;

    protected $tournament;
    protected $customMessage;
    protected $playerName;

    public function __construct(Tournament $tournament, string $message, string $playerName)
    {
        $this->tournament = $tournament;
        $this->customMessage = $message;
        $this->playerName = $playerName;
    }

    public function build()
    {
        $mailContent = MailContent::first();

        $subject = str_replace(
            '{playerName}',
            $this->playerName,
            $mailContent->subject ?? 'Welcome to PBSF, {playerName}'
        );

         $email = $this->subject("$subject - " . $this->playerName)
                    ->view('emails.tournament_notification')
                    ->with([
                        'tournamentName' => strval($this->tournament->name),
                        'messageText' => str_replace('{playerName}', $this->playerName, $mailContent->body_content ?? 'Hello {playerName}'),
                        'playerName' => $this->playerName,
                    ]);

        // Add CC addresses
        $email->cc(['cricket@pitchburners.com']);
      //$email->returnPath('sapareshan@dsignzmedia.in');
                
        return $email;
    }
}
