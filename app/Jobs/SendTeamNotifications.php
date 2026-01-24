<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Team;
use App\Models\Tournament;
use App\Mail\TournamentNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendTeamNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $tournamentId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tournamentId)
    {
        $this->tournamentId = $tournamentId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $tournament = Tournament::findOrFail($this->tournamentId);

            // Get teams associated with the tournament
            $teams = Team::whereIn('id', function ($query) {
                $query->select('team_id')
                    ->from('tournament_teams')
                    ->where('tournament_id', $this->tournamentId);
            })->with(['players' => function ($query) {
                $query->select('id', 'team_id', 'email', 'name')
                    ->whereNull('deleted_at')
                    ->whereNotNull('email');
            }])->get();

            if ($teams->isEmpty()) {
                Log::info("No teams found for tournament {$tournament->name}.");
                return;
            }

            foreach ($teams as $team) {
                $players = $team->players;

                if ($players->isEmpty()) {
                    Log::info("No players found for team {$team->name} in tournament {$tournament->name}.");
                    continue;
                }

                foreach ($players as $player) {
                    $message = "Important update for {$player->name} regarding tournament: {$tournament->name}";

                    Mail::to($player->email)->send(new TournamentNotification($tournament, $message, $player->name));
                }

                Log::info("Notifications sent to players of team {$team->name}.", [
                    'team_id' => $team->id,
                    'team_name' => $team->name,
                    'player_count' => $players->count(),
                    'timestamp' => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Error in sending notifications: {$e->getMessage()}");
        }
    }
}
