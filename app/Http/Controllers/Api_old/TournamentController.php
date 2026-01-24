<?php

namespace App\Http\Controllers\Api;

use App\Models\Team;
use Illuminate\Http\Request;
use App\Models\ScheduleMatch;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\OrganizerMember;
use App\Models\Venue;

class TournamentController extends Controller
{
    //


      public function StartMatch() {
      $ScheduledMatches = DB::table('schedule_matches')
          ->leftJoin('teams as teamA', 'teamA.id', '=', 'schedule_matches.team1')
          ->leftJoin('teams as teamB', 'teamB.id', '=', 'schedule_matches.team2')
          ->leftJoin('venues', 'venues.id', '=', 'schedule_matches.ground')
          ->leftJoin('matches', 'matches.schedule_match_id', '=', 'schedule_matches.id')
          ->select(
              'schedule_matches.*',
              'teamA.name as teamA_name',
              'teamA.logo as teamA_image',
              'teamB.name as teamB_name',
              'teamB.logo as teamB_image',
              'venues.name as ground_name',
              'matches.status as match_status',
              'matches.id as match_id',
              'schedule_matches.status as schedule_match_status'
          )
          ->whereIn('schedule_matches.status', ['Active', 'Scheduled'])   
          ->orderBy('match_date_time', 'desc')
          ->get();

      //return response()->json(['ScheduledMatches' => $ScheduledMatches]);
	    return response()->json(['ScheduledMatches' => []], 203);
  }


    public function getTeamData($id)
    {
        try {
            $scheduleMatch = ScheduleMatch::find($id);

            if (!$scheduleMatch) {
                return response()->json(['error' => 'Match not found'], 404);
            }

            $teamA = Team::where('id', $scheduleMatch->team1)->first();
            $teamB = Team::where('id', $scheduleMatch->team2)->first();

            $ground =Venue::find($scheduleMatch->ground);
            $GroundName= $ground->name;

            if ($teamA && $teamB) {
                return response()->json([
                    'teamA_logo' => $teamA->logo,
                    'teamB_logo' => $teamB->logo,
                    'teamA_name' => $teamA->name,
                    'teamB_name' => $teamB->name,
                    'teamAId' => $scheduleMatch->team1,
                    'teamBId' => $scheduleMatch->team2,
                    'noOfOvers' => $scheduleMatch->number_of_overs,
                    'matchtype' => $scheduleMatch->type,
                    'category' => $scheduleMatch->category,
                    'ground' => $GroundName,
                    'match_date_time' => $scheduleMatch->match_date_time,
                    'tournament_id' => $scheduleMatch->tournament_id,
                ]);
            } else {
                return response()->json(['error' => 'One or both teams not found'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching data'], 500);
        }
    }


    public function getOrganizers(){

       $UmpiresandScorers = OrganizerMember::all();

       return response()->json([
             'UmpiresandScorers' => $UmpiresandScorers
       ]);

    }

}
