@extends('layouts.app')

@section('content')
<style>
  .our-sponsers {
    display: none;
  }
 .q-indicator {
    display: inline-block;
    font-size: 12px;
    margin-left: 4px;
    color: #666;
}
.last-qualified {
    border-bottom: 2px solid #614092;
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
  @media (min-width: 499px) and (max-width: 767.98px) {
    
       .add-teamname-wrap .addteam-logo {
         margin-top:0px;
   }
}
  @media (max-width: 767.98px){
.add-teamname-wrap .addteam-logo {
margin-top: 5px;
}
    .reset a {
      font-size:16px;
    }
     .footer-mobile-app{
       position:relative;
  }
     .qualifiedteams .btn {
       padding:5px !important;
     }
  }
  .has-filter-dropdown .dropdown .dropdown-menu{
    min-width:300px;
  }
  .stangings-teams-item .info.team .favicon-logo {
    height: 35px;
    object-fit: contain;
}
   .stangings-teams-item .info.pos .qualify {
    background-color:#8642d9db;
    color:white;
  }
  .stangings-teams-item .info.pos .eliminated {
    position: absolute;
    width: 25px;
    height: 25px;
    border-radius: 50%;
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
    background-color: #e0e1e1;
    border: 1px solid #ccc;
    font-size: 12px;
    left: 8px;
    font-family: "Saira", Arial, Helvetica, sans-serif;
    font-weight: 600;
}
  .qualifiedteams .btn{
  padding:15px !important;
    background-color:#3A1079 !important;
    color:#FBC638 !important;
    border:2px solid #FBC638 !important;
  }
  @media (max-width: 767.98px) {
    .reset {
        top: -9%;
    }
     .qualifiedteams .btn{
  padding:10px !important;
    }
}
</style>

<section class="addnewplayer-title-wrap fixed-second-header teams-second-header">
  <div class="container h-100">
    <div class="row h-100 d-flex align-items-center">
      <div class="col-12">
        <div class="add-teamname-wrap">
          <div class="addteam-logo d-flex align-items-center standings-select-wrap">
            <figcaption>
              <h5>Standings</h5>
            </figcaption>
              </div>
            <div class="select-category d-flex align-items-center has-filter-dropdown">

              
                  
            <div class="dropdown qualifiedteams d-none">
              <a href="{{ route('standings', array_merge(request()->all(), ['qualified' => 1])) }}" class="btn btn-secondary">
                <p> Qualified Teams</p>
              </a>
            </div>
              
              
              <div class="dropdown seasondropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  <label>SELECT <span>TOURNAMENT</span></label>
                  <p>{{ $tournaments->firstWhere('id', $tournamentId)->name ?? 'Select Tournament' }}</p>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                  @forelse($tournaments as $tournament)
                    <li>
                      <a class="dropdown-item" href="{{ route('standings', ['tournament_id' => $tournament->id]) }}">
                        {{ $tournament->name }}
                      </a>
                    </li>
                  @empty
                    <li><p class="dropdown-item">No tournaments available</p></li>
                  @endforelse
                </ul>
              </div>

                 <div class="dropdown seasondropdown">
    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <label>SELECT <span>TEAM</span></label>
        <p>{{ $teamId ? $teams->firstWhere('id', $teamId)->name ?? 'All Teams' : 'All Teams' }}</p>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
        <li>
            <input type="text" class="form-control team-search" placeholder="Search teams..." onkeyup="filterTeams()">
        </li>
      <li>
                    <a class="dropdown-item" href="{{ route('standings') }}">
                        All Teams
                    </a>
                </li>
        <div id="teamList">
            @forelse($teams->sortBy('name') as $team)
                <li class="team-item">
                    <a class="dropdown-item" href="{{ route('standings', ['team_id' => $team->id]) }}">
                        {{ $team->name }}
                    </a>
                </li>
            @empty
               
            @endforelse
        </div>
    </ul>
</div>
             

             <div class="dropdown teamdropdown">
    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <label>SELECT <span>GROUP</span></label>
        <p>{{ $selectedGroupId ? $groups->firstWhere('id', $selectedGroupId)->group_name : 'All Groups' }}</p>
    </button>
    <div class="dropdown-menu dropdown-menu-end">
        <div class="bottom-desc">
            <ul>
                <li>
                    <a class="dropdown-item" href="{{ route('standings', ['tournament_id' => $tournamentId]) }}">
                        All Groups
                    </a>
                </li>
                @foreach($groups as $group)
                    <li>
                        <a class="dropdown-item" href="{{ route('standings', ['tournament_id' => $tournamentId, 'group_id' => $group->id]) }}">
                            {{ $group->group_name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
 <div class="dropdown leveldropdown">
    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <label>SELECT <span>ROUND</span></label>
        <p id="selectedRound">{{ $rounds->firstWhere('id', request('round_id'))->type ?? 'All Rounds' }}</p>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="{{ route('standings', ['tournament_id' => request('tournament_id')]) }}">All Rounds</a></li>
        @foreach($rounds as $round)
            <li>
                <a class="dropdown-item" href="{{ route('standings', ['tournament_id' => request('tournament_id'), 'round_id' => $round->id]) }}">
                    {{ $round->type }}
                </a>
            </li>
        @endforeach
    </ul>
</div>

                   @if(count(request()->all()) > 0)
                      <div class="reset">
                        <a href="{{ request()->url() }}" class="">Reset</a>
                        </div>
                    @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<main class="main-wrapper-start standings-main">
   @if($groups->isEmpty())
  <div class="coming-soon-wrapper">
      <div class="coming-soon-message">
        Check this out once the tournament begins...
      </div>
    </div>
@else
  <div class="container">
    <div class="row">
      <div class="col-12">
     
    <div class="standings-wrap">
        @foreach($groups as $group)
      @if(request()->has('qualified'))
        @php
            // Ensure group data exists
            $groupTeamCount = isset($groupTeams[$group->id]) ? count($groupTeams[$group->id]) : 0;
            // Each team should have played matches with every other team: totalTeams - 1
            $matchesRequired = $groupTeamCount > 0 ? $groupTeamCount - 1 : 0;
            // Check if every team in the group has played at least the required matches
            $allTeamsCompletedMatches = $groupTeamCount > 0 && collect($groupTeams[$group->id])->every(function($team) use ($matchesRequired) {
                return $team->played >= $matchesRequired;
            });
        @endphp
        @continue(!$allTeamsCompletedMatches)
    @endif
            @if(!$selectedGroupId || $group->id == $selectedGroupId)
                <div class="standings-box" id="group-{{ $group->id }}">
                    <div class="stangings-stage-title">{{ $group->group_name }}</div>
                    <div class="stangings-teams-lists-wrap">
                        <div class="stangings-teams-lists-head">
                            <div class="stangings-teams-item headz">
                                <div class="info pos head">POS</div>
                                <div class="info team head">TEAM</div>
                                <div class="info played head">PLAYED</div>
                                <div class="info won head">WON</div>
                                <div class="info lost head">LOST</div>
                                <div class="info nr head">N/R</div>
                                <div class="info tied head">TIED</div>
                                <div class="info netrr head">NET RR</div>
                                <div class="info points head">POINTS</div>
                            </div>
                        </div>
                      <div class="stangings-teams-lists-body">
    @if(isset($groupTeams[$group->id]) && count($groupTeams[$group->id]) > 0)
          @php
    // Get the total number of teams in the group
    $totalTeams = count($groupTeams[$group->id]); 
    $matchesRequired = $totalTeams - 1; // Each team should play every other team once

    // Check if all teams in the group have completed their matches
    $allTeamsCompletedMatches = collect($groupTeams[$group->id])->every(fn($team) => $team->played >= $matchesRequired);

    // Determine the number of teams that qualify
    $teamsToQualify = $group->teams_to_qualify ?? 0;
@endphp

@foreach($groupTeams[$group->id] as $team)
    @php
        $isQualified = $loop->iteration <= $teamsToQualify; // Only top teams qualify
        $isLastQualified = $loop->iteration == $teamsToQualify; // The last team that qualifies
         $isEliminated = $allTeamsCompletedMatches && !$isQualified;
    @endphp
    <div class="stangings-teams-item bodz {{ ($isLastQualified && $allTeamsCompletedMatches) ? 'last-qualified' : '' }}">
        <div class="info pos bodyy has-qualify">
            @if($allTeamsCompletedMatches)
    @if($isQualified)
        <span class="qualify">Q</span>
    @else
        <span class="eliminated">E</span>
    @endif
@endif
            {{ $loop->iteration }}
        </div>
        <div class="info team bodyy">
            <img class="favicon-logo" src="{{ config('constants.upload_url') . '/team_logos/' . $team->logo }}" alt="{{ $team->name }}" />
            {{ $team->name }}
        </div>
        <div class="info played bodyy">{{ $team->played }}</div>
        <div class="info won bodyy">{{ $team->wins }}</div>
        <div class="info lost bodyy">{{ $team->losses }}</div>
        <div class="info nr bodyy">{{ $team->nr }}</div>
        <div class="info tied bodyy">{{ $team->tied }}</div>
        <div class="info netrr bodyy">{{ number_format($team->net_rr, 3) }}</div>
      <div class="info points bodyy"><strong>{{ $team->points }}</strong></div>
    </div>
@endforeach
    @else
        <div class="mt-5">
            <div class="alert alert-info">
                <p>No teams available in this group.</p>
            </div>
        </div>
    @endif
</div>


                    </div>
                </div>
            @endif
        @endforeach
    </div>
@endif

      </div>
    </div>
  </div>
</main>
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
