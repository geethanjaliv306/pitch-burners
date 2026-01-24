@extends('layouts.app')

@section('content')

<style>
  
    .our-sponsers {
        display: none;
    }
    .test {
        border: 1px solid #4CAF50;
        background: rgba(76, 175, 80, 0.08);
    }
    .live-btn {
        border: 1px solid #4CAF50;
        color: #4CAF50;
        font-weight: 500;
        background: #fff !important;
    }
    .fixtures-results-box .action-info a {
        border-radius: 5px;
    }
    .has-filter-dropdown .dropdown .btn {
        border-radius: 5px;
    }
    .test:hover {
        background: rgba(76, 175, 80, 0.08) !important;
        background-color: unset;
    }
   .drp{
        flex: 1;
     	max-width: 250px
    }
   .drp button p{
      width: 100%;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .fixed-second-header.teams-second-header{
        height: auto;
        padding: 20px;
    }
    @media (max-width:767.98px){
    .matches-main{
       margin-top: 200px !important;
      padding:15px 0px;
    }
      
  }
 @media (min-width: 499px) and (max-width: 767.98px) {
    .select-category {
        margin-bottom: 10px;
    }
}
  .has-filter-dropdown .dropdown .btn{
    border-radius: 0px;
}
  .scroll-to-top {
  position: fixed;
  bottom: 25px;
  right: 25px;
  background: linear-gradient(242.58deg, #8542D8 0.9%, #9D3ADF 21.58%, #8542D8 64.98%, #9D3ADF 101.25%);
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  opacity: 0;
  visibility: hidden;
  transition: all 0.3s ease;
  z-index: 1000;
  border: none;
  box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.scroll-to-top.visible {
  opacity: 1;
  visibility: visible;
}

.scroll-to-top:hover {
  background-color: #ff567c;
  transform: translateY(-2px);
}

.scroll-to-top i {
  color:white;
}
    .no-team{
    font-size: 28px;
    color: #614092;
    text-align: center;
    padding: 20px 20px;
    border-radius: 10px;
    margin-top: 15px;
    margin: auto;
    top: 20%;
    font-family: 'Saira';
    font-weight: 500;
  }
   .coming-soon-wrapper {
    min-height: calc(100vh - 225px);
    display: flex;
    justify-content: center;
    background-image: url(https://pitchburners.com/uploads/images/homepage.svg);
    background-position: bottom center;
    background-repeat: no-repeat;
    background-size: contain;
position:relative;
  }
  .coming-soon-message {
    font-size: 40px;
    color: #614092;
    text-align: center;
    padding: 20px 20px;
    border-radius: 10px;
    margin-top: 15px;
    position: absolute;
    inset: 0;
    margin: auto;
    top: 10%;
    font-family: 'Saira';
    font-weight: 500;
}
  
  @media (max-width:767.98px){
    
  .fixed-second-header.teams-second-header{
    padding:0px;
   
  }
    .venuedropdown{
      margin-right:25px;
    }
  }
 .fixtures-results-box .match-vs-team .teamA i, .fixtures-results-box .match-vs-team .teamB i {
  width: 200px;
  height: auto;
  /* flex: 0 0 100px; */
  /* max-width: 1000px; */
  /* background-color: #fff; */
  /* border: 1px solid #E0E1E1; */
  display: -webkit-box;
  display: -moz-box;
  display: -ms-flexbox;
  display: -webkit-flex;
  display: flex
;
  -webkit-align-items: center;
  -moz-align-items: center;
  -ms-align-items: center;
  align-items: center;
  -webkit-justify-content: center;
  -moz-justify-content: center;
  -ms-justify-content: center;
  justify-content: center;
  -ms-flex-pack: center;
  border-radius: 10px;
  margin-right: 0px;
}
.fixtures-results-box .match-vs-team .teamA i img, .fixtures-results-box .match-vs-team .teamB i img {
 height: auto;
    max-height: 50px;
    max-width: 100%;
}
.fixtures-results-box .match-vs-team .teamA h5, .fixtures-results-box .match-vs-team .teamB h5 {
  font-family: "Saira", Arial, Helvetica, sans-serif;
  font-size: 18px;
  color: #614092;
  margin: 0 0 10px;
  text-align: center;
  font-weight: 500;
}
.fixtures-results-box .match-vs-team .teamA, .fixtures-results-box .match-vs-team .teamB {
  position: relative;
  flex: 0 0 calc((100% - 90px) / 2);
  max-width: calc((100% - 90px) / 2);
  flex-direction: column;
  gap: 10px;
}
.fixtures-results-wrapper{
  display: flex;
  flex-wrap: wrap;
  gap: 30px;
}
.fixtures-results-box{
   flex: 0 0 calc(50% - 15px);
    max-width: calc(50% - 15px);
  display: block;
  padding: 0;
  margin: 0;
  box-shadow: 0px 0px 9.39227px rgba(0, 0, 0, 0.1);
  border: 1px solid #EBEBEB;
}
.fixtures-results-box .match-info{
  border-style: solid;
  border-width: 0px 0 1px;
  border-color: #EBEBEB;
}
.fixtures-results-box .date-info, .fixtures-results-box .match-info, .fixtures-results-box .action-info{
  flex: 0 0 100%;
  max-width: 100%;
}
.fixtures-results-box .match-info, .fixtures-results-box .action-info{
  padding: 15px 0;
}
.fixtures-results-box .action-info a{
  border-radius: 8px;
}
.fixtures-results-box .match-vs-team{
  margin: 5px 0;
}
.fixtures-results-box .date-info{
  display: flex;
  flex-direction: row;
  align-items: stretch;
}
.fixtures-results-box .date-info p{
  margin: 0;
}
.fixtures-results-box .date-info .date{
  background-color: #008B74;
  padding: 5px;
  border-radius: 5px;
  color: #fff;
  width: 40px;
  height: 40px;
  line-height: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  font-size: 14px;
}
.fixtures-results-box .date-info .date small{
  display: block;
}
.fixtures-results-box .date-info .date-time,
.fixtures-results-box .date-info .location
{
  background-color: #F7F5FA;
  padding: 10px 15px;
}
.fixtures-results-box .date-info .date-time{
  flex: 0 0 44%;
  max-width: 44%;
  text-align: center;
  border-top-left-radius: 8px;
  display: flex;
  align-items: center;
  flex-direction: row-reverse;
  justify-content: start;
}
.fixtures-results-box .date-info .date-time .datetime{
  color: #4E4E4E;
  font-weight: 500;
  font-size: 15px;
}
.fixtures-results-box .date-info .date-time .datetime span{
color: #B8B8B8
}
.fixtures-results-box .date-info .location{
  flex: 0 0 56%;
  max-width: 56%;
  text-align: center;
  display: flex;
  align-items: center;
  justify-content: end;
  border-top-right-radius: 8px;
}
.fixtures-results-box .date-info .location a{
  text-decoration: none;
  color: #4EA0FF;
  font-weight: 500;
  font-size: 15px;
}
.fixtures-results-box .match-vs-team .vs:after{
  display: none;
}
.fixtures-results-box .match-vs-team .vs span{
  width: 55px;
  height: 55px;
  background-color: #FFFFFF;
  border-color: #D9D9D9;
}
.fixtures-results-box .action-info{
  padding: 15px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.fixtures-results-box .won-loss-details{
  color: #4CAF50;
  top: 0;
  position: inherit;
}
.fixtures-results-box .action-info a{
  height: 40px;
    width: 170px;
}
.fixtures-results-box .match-vs-team .teamA i, .fixtures-results-box .match-vs-team .teamB i{
  border:none;
  height:60px;
  }
  .upcomingbadge{
    width:115px;
    height:32px;
    border-radius:5px;
    background-color:#B8B8B8;
    color:#fff;
    text-transform: uppercase;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:15px;
  }
  .fixtures-results-box .date-info .date-time .datetime{
    white-space: nowrap;
    text-transform: uppercase;
  }
  .fixtures-results-box .date-info .date-time .datetime span{
    margin: 0 0px 0px 3px;
    display: inline-block;
  }
  
  @media (max-width: 1199.98px){
    .fixtures-results-box{
      flex: 0 0 100%;
    max-width: 100%;
    }
  .footer-mobile-app{
    position:relative;
    }
    
  }
   @media (max-width: 767.98px){
  .fixtures-results-box .match-vs-team .teamA, .fixtures-results-box .match-vs-team .teamB{
    max-width:100%;
    flex:0 0 100%;
     }
     .fixtures-results-box .date-info{
       padding:0px;
     }
     .fixtures-results-box .date-info .date-time , .fixtures-results-box .date-info .location{
         flex: 0 0 50%;
    max-width: 50%;
     }
  }
   @media (max-width: 565.98px){
     .fixtures-results-box .date-info .date-time, .fixtures-results-box .date-info .location{
       flex: 0 0 100%;
        max-width: 100%;
     }
     .fixtures-results-box .date-info{
       flex-direction:column;
     }
     .fixtures-results-box .date-info .date-time{
       padding-bottom:0px;
     }
     .fixtures-results-box .date-info .location{
       justify-content:start;
     }
     .fixtures-results-box .match-vs-team .teamA h5, .fixtures-results-box .match-vs-team .teamB h5{
margin: 0;
}
  }
.team-search {
    margin-top: 10px;
}
  .fixtures-results-box .match-vs-team .teamA h5, .fixtures-results-box .match-vs-team .teamB h5{
justify-content: center;
}
  .group_matches{
   font-family: "Saira", Arial, Helvetica, sans-serif;
    color: #607D8B !important;
    font-weight: 700;
  }
  .fixtures-results-box .match-vs-team .vs {
    display: flex;
    justify-content: center;
    flex-direction: column;
    align-items: center;
    gap:40px;
  }
  .has-filter-dropdown .dropdown .dropdown-menu{
    min-width:300px;
  }
  .win-team .score.loss p, .win-team .score.loss span {
    color: #042F44 !important;
}
.win-trophy {
    width: 25px;
    position: relative;
    top: -5px;
}
  .won-loss-details p{
    margin-bottom:0px;
  }
   @media (max-width: 767.98px){
     .reset{
       top:-4px;
     }
      .reset a{
       font-size:16px;
     }
      .won-loss-details p{
    line-height:20px;
  }
     .win-trophy {
       display:none;
     }
  }
 
}
</style>
<link href="https://fonts.googleapis.com/css2?family=Unlock&display=swap" rel="stylesheet">
 <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<section class="addnewplayer-title-wrap fixed-second-header teams-second-header">
    <i class="right-celebration"></i>
    <div class="container h-100">
        <div class="row h-100 d-flex align-items-center">
            <div class="col-12">
                <div class="add-teamname-wrap">
                    <div class="addteam-logo d-flex align-items-center">
                        <figcaption>
                            <h5>Fixtures & Results</h5>
                        </figcaption>
                    </div>
                   <div class="select-category d-flex align-items-center has-filter-dropdown">
    <!-- Status Dropdown -->
    <div class="dropdown statusdropdown drp">
    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <label>SELECT <span>STATUS</span></label>
        <p>{{ request('status') ?? 'All Matches' }}</p>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="{{ request()->url() }}">All Matches</a></li>
        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'Live']) }}">Live</a></li>
        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'Upcoming']) }}">Upcoming</a></li>
        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'Completed']) }}">Completed</a></li>
    </ul>
</div>

    <!-- Tournament Dropdown -->
    <div class="dropdown seasondropdown drp ">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <label>SELECT <span>TOURNAMENT</span></label>
            <p>{{ $selectedTournamentId ? $tournaments->firstWhere('id', $selectedTournamentId)->name : 'All Tournaments' }}</p>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['tournament_id' => null]) }}">All Tournaments</a></li>
            @foreach($tournaments as $tournament)
                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['tournament_id' => $tournament->id]) }}">{{ $tournament->name }}</a></li>
            @endforeach
        </ul>
    </div>
                     
                     <div class="dropdown groupdropdown drp">
    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <label>SELECT <span>GROUP</span></label>
        <p>{{ $selectedGroupId ? ($groups[$selectedGroupId]->group_name ?? 'All Groups') : 'All Groups' }}</p>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['group_id' => null]) }}">All Groups</a></li>
        @foreach($groups as $group)
            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['group_id' => $group->id]) }}">{{ $group->group_name }}</a></li>
        @endforeach
    </ul>
</div>
                     
                     
                      <div class="dropdown groupdropdown drp">
    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <label>SELECT <span>ROUND</span></label>
        <p>{{ $selectedRoundId ? ($rounds[$selectedRoundId]->type ?? 'All Rounds') : 'All Rounds' }}</p>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
        <li>
            <a class="dropdown-item {{ !$selectedRoundId ? 'active' : '' }}" 
               href="{{ request()->fullUrlWithQuery(['round_id' => null]) }}">
                All Rounds
            </a>
        </li>
        @foreach($rounds as $round)
            <li>
                <a class="dropdown-item {{ $selectedRoundId == $round->id ? 'active' : '' }}" 
                   href="{{ request()->fullUrlWithQuery(['round_id' => $round->id]) }}">
                    {{ $round->type }}
                </a>
            </li>
        @endforeach
    </ul>
</div>
                     

    <!-- Team Dropdown -->
    <div class="dropdown teamdropdown drp">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <label>SELECT <span>TEAM</span></label>
            <p>{{ $selectedTeamId ? $teams->firstWhere('id', $selectedTeamId)->name : 'All Teams' }}</p>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
           <li>
            <input type="text" class="form-control team-search" placeholder="Search teams..." onkeyup="filterTeams()">
        </li>
            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['team_id' => null]) }}">All Teams</a></li>
            @foreach($teams->sortBy('name') as $team)
                <li class="team-item"><a class="dropdown-item " href="{{ request()->fullUrlWithQuery(['team_id' => $team->id]) }}">{{ $team->name }}</a></li>
            @endforeach
        </ul>
    </div>

    <!-- Venue Dropdown -->
    <div class="dropdown venuedropdown drp">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <label>SELECT <span>VENUE</span></label>
            <p>{{ $selectedVenueId ? $venues->firstWhere('id', $selectedVenueId)->name : 'All Venues' }}</p>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['venue_id' => null]) }}">All Venues</a></li>
            @foreach($venues as $venue)
                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['venue_id' => $venue->id]) }}">{{ $venue->name }}</a></li>
            @endforeach
        </ul>
    </div>
                      @if(
    (request()->has('tournament_id') && $selectedTournamentId) ||
    (request()->has('team_id') && $selectedTeamId) ||
    (request()->has('venue_id') && $selectedVenueId) ||
    (request()->has('status') && $selectedStatus) ||
    (request()->has('group_id') && $selectedGroupId) ||
    (request()->has('round_id') && $selectedRoundId && $selectedRoundId != $rounds->keys()->first()) 
)
    <div class="reset">
        <a href="{{ request()->url() }}" class="">Reset</a>
    </div>
@endif
</div>

                </div>
            </div>
        </div>
    </div>
</section>
<main class="main-wrapper-start matches-main">
                  
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="fixtures-results-wrapper">
                     @if($filteredMatches->isEmpty())
                      <div class="coming-soon-message">
                       No Matches Available.
                      </div>
                    @else
                        @foreach($filteredMatches as $match)
                            @php
                                $match = (array) $match;
                            @endphp
                  
                     @if($match['round_id'] == 1)
                            <div class="fixtures-results-box {{ $match['status'] === 'active' ? 'test' : '' }}">
                           {{--     <div class="date-info">
                                    <p class="day">{{ \Carbon\Carbon::parse($match['match_date_time'])->format('l') }}</p>
                                    <p class="date">{{ \Carbon\Carbon::parse($match['match_date_time'])->format('d') }}</p>
                                    <p class="month">{{ \Carbon\Carbon::parse($match['match_date_time'])->format('F') }}</p>
                                </div> --}}
                              <div class="date-info">
                    <div class="date-time">
                       <p class="datetime">
        <img style="margin-right: 5px;" width="22" height="22" src="/uploads/images/schedule.svg" />
        {{ \Carbon\Carbon::parse($match['match_date_time'])->format('M d, D') }} <span>|</span> 
        {{ \Carbon\Carbon::parse($match['match_date_time'])->format('g:i A') }} <span>|</span> <span class="group_matches">{{ $groups[$match['group_id']]->group_name ?? 'N/A' }}<span>
    </p>
                                </div>
                    <div class="location">
                      <p class="day"><a target="_blank" href="https://www.google.com/maps?q={{ urlencode($match['ground_location']) }}"><img width="22" height="22" src="/uploads/images/fi_2875433.svg" />  {{ $match['ground'] }}</a></p>
                    </div>
                  </div>
                                <div class="match-info">
                                   {{-- <div class="time-ground-details">
                                        <span class="time">{{ \Carbon\Carbon::parse($match['match_date_time'])->format('g:i A') }}</span>
                                        {{ $match['ground'] }}
                                    </div> --}}
                                    <div class="match-vs-team">
                                        <div class="teamA d-flex align-items-center {{ $match['is_teamA_won'] ? 'win-team' : '' }}">
                                            <i><img src="{{ config('constants.upload_url') . '/team_logos/' . $match['teamA_image'] }}" alt="Team One Logo" /></i>
                                            <div>
                                               <h5 style="display: flex; align-items:center;">
                                                    {{ $match['teamA_name'] }}

                                                </h5>
                                                @if($match['status'] === 'scheduled')

                                                @else
                                                <div class="score loss"><p class="team1-score"> @if ($match['is_teamA_won'])
    <img class="win-trophy" width="25" height="25" src="{{ asset('uploads/images/trophy.svg') }}" />
@endif {{ $match['teamA_score'] }}</p><span>({{ $match['teamAovers'] }})</span></div>
                                                @endif
                                            </div>
                                        </div>
                                       <div class="vs"><span><img width="42" height="42" src="/uploads/images/fi_9359727.svg" /></span> </div>
                                        <div class="teamB d-flex align-items-center {{ $match['is_teamB_won'] ? 'win-team' : '' }}">
                                            <i><img src="{{ config('constants.upload_url') . '/team_logos/' . $match['teamB_image'] }}" alt="Team Two Logo" /></i>
                                            <div>
                                              <h5 style="display: flex;align-items:center;">

                                                  {{ $match['teamB_name'] }}

                                              </h5>
                                                @if($match['status'] === 'scheduled')

                                               @else
                                                <div class="score loss"><p class="team2-score"> @if ($match['is_teamB_won'])
    <img class="win-trophy" width="25" height="25" src="{{ asset('uploads/images/trophy.svg') }}" />
@endif {{ $match['teamB_score'] }}</p><span>({{ $match['teamBovers'] }})</span></div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                   {{-- <div class="won-loss-details">
                                        @if($match['status'] === 'completed')
                                              <p>
                                                {{$match['result']}}
                                              </p>
                                        @elseif($match['status'] === 'canceled')
                                            <p class="text-danger">Match Cancelled</p>
                                        @elseif($match['status'] === 'active')
                                            <span class="badge bg-success live-btn">Live</span>
                                        @elseif($match['status'] === 'scheduled')
                                            <span class="badge bg-secondary">Upcoming</span>
                                        @endif
                                    </div> --}}
                                </div>
                                <div class="action-info">
                                   <div class="won-loss-details">
                                        @if($match['status'] === 'completed')
                                              <p>
                                                {{$match['result']}}
                                              </p>
                                        @elseif($match['status'] === 'canceled')
                                            <p>  {{$match['result']}}</p>
                                        @elseif($match['status'] === 'active')
                                            <span class="badge bg-success live-btn">Live</span>
                                        @elseif($match['status'] === 'scheduled')
                                            <span class="upcomingbadge">Upcoming</span>
                                        @endif
                                    </div>
                                  <div>
                                    <a class="match-center" href="{{ route('matches.details', $match['id']) }}">
                                        <img width="25" height="25" src="{{ asset('uploads/images/cricket.svg') }}" />Match Centre
                                    </a>
                                    
                                  </div>
                                </div>
                            </div>
                              
                                @else
                  
                  <div class="knockouts-results-box" onclick="window.location='{{ route('matches.details', $match['id']) }}';" style="cursor: pointer;">
                    <div class="stages-name">
                      <p class="stage">@if($match['round_id'] == 6) ROUND ROBIN @endif {{ $match['round_name'] }}</p>
                      <p class="slot">Slot: {{ $groups[$match['group_id']]->group_name ?? 'N/A' }}</p>
                    </div>
                <p class="dates-location"> {{ \Carbon\Carbon::parse($match['match_date_time'])->format('M d, D') }} |
        {{ \Carbon\Carbon::parse($match['match_date_time'])->format('g:i A') }}, <a class="text-white" target="_blank" href="https://www.google.com/maps?q={{ urlencode($match['ground_location']) }}">{{ $match['ground'] }}</a> </p> 
                <figure class="captain-figure">
                  <div>  <div class="bottom-logo"><img src="{{ config('constants.upload_url') . '/team_logos/' . $match['teamA_image'] }}" /></div>      
   <img class="profilePic" src="{{ config('constants.upload_url') . '/player_images/' . ($match['teamA_captain']->image ?? 'dummy-avatar.png') }}" 
         alt="" />
                    </div>   
 <div>  <div class="bottom-logo"><img src="{{ config('constants.upload_url') . '/team_logos/' . $match['teamB_image'] }}" /></div>   
    <img class="profilePic" src="{{ config('constants.upload_url') . '/player_images/' . ($match['teamB_captain']->image?? 'dummy-avatar.png') }}" 
         alt="" />
    </div>



                </figure>
                <figcaption class="teamname-wrap">
                  <h3 class="teamname first"> {{ $match['teamA_name'] }}</h3>
                  <div class="teamvs">
                    <img width="100" height="100" src="/uploads/images/vs.svg" />
                  </div>
                  <h3 class="teamname second"> {{ $match['teamB_name'] }}</h3>
                </figcaption>
                    @if($match['status'] != 'scheduled')
                                       
                <div class="score-wrap">
                  <div class="scoreInfo">
                    <strong>{{ $match['teamA_score'] }}</strong>
                    <span>({{ $match['teamAovers'] }})</span>
                  </div>
                  <div class="scoreInfoVs"></div>
                  <div class="scoreInfo">
                    <strong>{{ $match['teamB_score'] }}</strong>
                    <span>({{ $match['teamBovers'] }})</span>
                  </div>
                </div>     
                                        @endif
                <p class="result-info d-flex justify-content-center"> 
                  @if($match['status'] === 'completed')
                                              <p class="result-info d-flex justify-content-center"> 
                                                {{$match['result']}}
                                              </p>
                                        @elseif($match['status'] === 'canceled')
                                            <p class="result-info d-flex justify-content-center">  {{$match['result']}}</p>
                                        @elseif($match['status'] === 'active')
                                            <span class="badge bg-success live-btn">Live</span>
                                        @elseif($match['status'] === 'scheduled')
                                            
                                        @endif
                    </p>
              </div>
                 
                  
                   
                  @endif
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
  <button class="scroll-to-top" id="scrollToTop" aria-label="Scroll to top">
  <i class="fa-solid fa-chevron-up"></i>
</button>
</main>
   <script>
document.addEventListener('DOMContentLoaded', function() {
  const scrollToTopButton = document.getElementById('scrollToTop');

  // Show/hide button based on scroll position
  window.addEventListener('scroll', function() {
    if (window.pageYOffset > 300) {
      scrollToTopButton.classList.add('visible');
    } else {
      scrollToTopButton.classList.remove('visible');
    }
  });

  // Smooth scroll to top when clicked
  scrollToTopButton.addEventListener('click', function() {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });
});
</script>
<script>
   document.addEventListener('DOMContentLoaded', () => {
        const matches = @json($filteredMatches);

                    console.warn('Matches:', matches);

    });
</script>
<script>
    function filterTeams() {
        let input = document.querySelector(".team-search").value.toLowerCase();
        let teamItems = document.querySelectorAll(".team-item");

        teamItems.forEach(item => {
            let teamName = item.textContent.toLowerCase();
            if (teamName.includes(input)) {
                item.style.display = "block";
            } else {
                item.style.display = "none";
            }
        });
    }
</script>
@endsection
