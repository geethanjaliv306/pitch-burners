@extends('layouts.admin')
@section('content')
<style>
  .summary-tab-content .player-of-the-match figure .right {
    border-radius:10px !important;
  }
  .team-logoss{
    object-fit:contain !important;
    object-position:center !important;
  }
</style>
<section class="startmatch-wrap dashboard-page">
<div class="row mb-4">
        <div class="col-12">
          <div class="dashboard-wrap">
            <a href="{{route('schedulematch')}}" class="dashboard-box">
              <h3>{{$schedulematchCount}}</h3>
              <h4 class="m-0">Total Matches</h4>
            </a>
            <a href="{{route('total-teams')}}" class="dashboard-box">
              <h3>{{$teamCount}}</h3>
              <h4 class="m-0">Total Teams</h4>
            </a>
            <a href="{{route('tournaments-view')}}" class="dashboard-box">
              <h3>{{$tournamentCount}}</h3>
              <h4 class="m-0">Total Tournaments</h4>
            </a>
            <a href="{{route('venues-admin')}}" class="dashboard-box">
              <h3>{{ $venueCount }}</h3>
              <h4 class="m-0">No of Venues</h4>
            </a>
            <a href="{{route('organizer-members')}}" class="dashboard-box">
              <h3>{{ $organizerCount }}</h3>
              <h4>Organizer Members</h4>
            </a>
          </div>
        </div>
        <div class="col-12 d-none">
          <a href="index.html" class="ongoing-tournment">
            <div class="innerInfo">
              <h6>Ongoing Tournament</h6>
              <p>Pitch Burners - Season 5 - Corporate Tournament - 2025</p>
            </div>
          </a>
        </div>
</div>
<div class="row">
  <div class="col-12">
   <div class="summary-tab-content dashboard-content">
     {{-- Most Runs --}}
     <div class="player-of-the-match playermatch">
       <figure>
         @if($allPlayerStats && $allPlayerStats->player_image)
         <img src="{{ config('constants.upload_url') . '/player_images/' . $allPlayerStats->player_image }}" alt="{{ $allPlayerStats->player }}" />
         @else
           <img src="{{ asset('uploads/images/register-bg.jpg') }}" alt="Default Image" />
         @endif
         <h4>Most Runs</h4>
         <div class="right">
           
           @if($allPlayerStats)
                <img class="team-logoss" src="{{ config('constants.upload_url') . '/team_logos/' . $allPlayerStats->logo }}" alt="{{ $allPlayerStats->team }} Logo" />
            @else
                <img src="{{ asset('uploads/images/dummy_logo.jpg') }}" alt="Default Team Logo" />
          @endif 
           
        </div>
       </figure>
       <figcaption>
         <div class="left text-center">
           <h6>{{ $allPlayerStats->player ?? 'Player' }}</h6>
           <h5>{{ $allPlayerStats->total_runs ?? 0 }}</h5>
           <label>Runs</label>
         </div>
         <a class="text-center" href="{{route('most-runs')}}">View Full List</a>
       </figcaption>
     </div>
     {{-- Most Wickets --}}
     <div class="player-of-the-match playermatch">
        <figure>
            <!-- Most Wickets -->
            @if($allBowlingStats && $allBowlingStats->player_image)
                <img src="{{ config('constants.upload_url') . '/player_images/' . $allBowlingStats->player_image }}" alt="{{ $allBowlingStats->player }}" />
            @else
                <img src="{{ asset('uploads/images/register-bg.jpg') }}" alt="Default Image" />
            @endif
            <h4>Most Wickets</h4>
            <div class="right">
              
               @if($allBowlingStats)
                <img  class="team-logoss" src="{{ config('constants.upload_url') . '/team_logos/' . $allBowlingStats->logo }}" alt="{{ $allBowlingStats->team }} Logo" />
            @else
                <img src="{{ asset('uploads/images/dummy_logo.jpg') }}" alt="Default Team Logo" />
          @endif 
            </div>
        </figure>
       <figcaption>
         <div class="left text-center">
           <h6>{{ $allBowlingStats->player ?? 'Player' }}</h6>
           <h5>{{ $allBowlingStats->total_wickets_taken ?? 0 }}</h5>
           <label>Wickets</label>
         </div>
         <a class="text-center" href="{{route('most-wickets')}}">View Full List</a>
       </figcaption>
     </div>
     {{-- Most Sixes --}}
     <div class="player-of-the-match bestbatter">
        <figure>
            <!-- Most Sixes -->
            @if($topSixHitter && $topSixHitter->player->image)
                <img src="{{ config('constants.upload_url') . '/player_images/' . $topSixHitter->player->image }}" alt="{{ $topSixHitter->player->name }}" />
            @else
                <img src="{{ asset('uploads/images/register-bg.jpg') }}" alt="Default Image" />
            @endif
            <h4>Most Sixes</h4>
            <div class="right">
                @if($topSixHitter && $topSixHitter->player->team && $topSixHitter->player->team->logo)
                    <img  class="team-logoss" src="{{ config('constants.upload_url') . '/team_logos/' . $topSixHitter->player->team->logo }}" alt="{{ $topSixHitter->player->team->name }} Logo" />
                @else
                    <img src="{{ asset('uploads/images/dummy_logo.jpg') }}" alt="Default Team Logo" />
                @endif
            </div>
        </figure>
       <figcaption>
         <div class="left text-center">
           <h6>{{ $topSixHitter->player->name ?? 'Player' }}</h6>
           <h5>{{ $topSixHitter->total_sixes ?? 0 }}</h5>
           <label>Sixes</label>
         </div>
         <a class="text-center" href="{{route('most-six')}}">View Full List</a>
       </figcaption>
     </div>
     {{-- Most Fours --}}
     <div class="player-of-the-match bestbatter">
        <figure>
            <!-- Most Fours -->
            @if($topFourHitter && $topFourHitter->player->image)
                <img src="{{ config('constants.upload_url') . '/player_images/' . $topFourHitter->player->image }}" alt="{{ $topFourHitter->player }}" />
            @else
                <img src="{{ asset('uploads/images/register-bg.jpg') }}" alt="Default Image" />
            @endif
            <h4>Most Fours</h4>
            <div class="right">
                @if($topFourHitter && $topFourHitter->player->team && $topFourHitter->player->team->logo)
                    <img class="team-logoss"  src="{{ config('constants.upload_url') . '/team_logos/' . $topFourHitter->player->team->logo }}" alt="{{ $topFourHitter->player->team->name }} Logo" />
                @else
                    <img src="{{ asset('uploads/images/dummy_logo.jpg') }}" alt="Default Team Logo" />
                @endif
            </div>
        </figure>
       <figcaption>
         <div class="left text-center">
           <h6>{{ $topFourHitter->player->name ?? 'Player' }}</h6>
           <h5>{{ $topFourHitter->total_fours ?? 0 }}</h5>
           <label>Fours</label>
         </div>
         <a class="text-center" href="{{route('most-four')}}">View Full List</a>
       </figcaption>
     </div>
     {{-- Most Catches --}}
    <div class="player-of-the-match bestbatter">
    <figure>
        <!-- Most Catches -->
        @if($topCatchesPlayer && !empty($topCatchesPlayer->image))
            <img src="{{ config('constants.upload_url') . '/player_images/' . $topCatchesPlayer->image }}" alt="{{ $topCatchesPlayer->player_name }}" />
        @else
            <img src="{{ asset('uploads/images/register-bg.jpg') }}" alt="Default Image" />
        @endif
        <h4>Most Catches</h4>
        <div class="right">
            @if($topCatchesPlayer && !empty($topCatchesPlayer->team_logo))
                <img  class="team-logoss" src="{{ config('constants.upload_url') . '/team_logos/' . $topCatchesPlayer->team_logo }}" alt="Team Logo" />
            @else
                <img src="{{ asset('uploads/images/dummy_logo.jpg') }}" alt="Default Team Logo" />
            @endif
        </div>
    </figure>
    <figcaption>
        <div class="left text-center">
            <h6>{{ $topCatchesPlayer->player_name ?? 'Player' }}</h6>
            <h5>{{ $topCatchesPlayer->catch_count ?? 0 }}</h5>
            <label>Catches</label>
        </div>
        <a class="text-center" href="{{ route('most-catches') }}">View Full List</a>
    </figcaption>
</div>

   </div>
  </div>
</div>
</section>
@endsection



