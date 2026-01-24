<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Team;
use App\Models\Player;
use App\Models\Tournament;
use App\Mail\TournamentNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendTeamPlayersNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tournamentId;
    protected $teamId;

    /**
     * Create a new job instance.
     *
     * @param  int  $tournamentId
     * @param  int  $teamId
     * @return void
     */
    public function __construct($tournamentId, $teamId)
    {
        $this->tournamentId = $tournamentId;
        $this->teamId = $teamId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $tournament = Tournament::findOrFail($this->tournamentId);  // Corrected reference
            $team = Team::findOrFail($this->teamId);  // Corrected reference

            // Get only players from the specific team
            $players = Player::where('team_id', $this->teamId)  // Corrected reference
                            ->whereNull('deleted_at')
                            ->whereNotNull('email')
                            ->get();

            if ($players->isEmpty()) {
                return back()->with('error', "No players found in team {$team->name} to send notifications to.");
            }

            foreach ($players as $player) {
                $message = "Important update for {$player->name} regarding tournament: {$tournament->name}";

                Mail::to($player->email)->send(new TournamentNotification($tournament, $message, $player->name));
            }

            Log::info("Team notification sent", [
                'tournament_id' => $this->tournamentId,  // Corrected reference
                'team_id' => $this->teamId,  // Corrected reference
                'team_name' => $team->name,
                'player_count' => $players->count(),
                'timestamp' => now()
            ]);

            return back()->with('success', "Notifications sent to {$team->name} team players successfully!");
        } catch (\Exception $e) {
            Log::error("Error sending team notifications: " . $e->getMessage());
            return back()->with('error', 'There was an error sending notifications. Please try again later.');
        }
    }
}
