@extends('layouts.app')

@section('content')
<style>
    .backimg{
        width: 35px;
    }
    .score-ballss{
        margin-left:0px !important;
    }
  .no_match{
        font-family: "Saira", Arial, Helvetica, sans-serif;
    font-size: 16px;
    font-weight: 500;
    margin-bottom: 40px;
  }

  .commentry-wrap .oversummary{
    border-radius:0px;
  }

  .score-span{
    font-size: 15px;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.7);
  }

  .batsmen {
    display: flex;
    flex-flow: row;
    flex: 1;
    justify-content: space-between;    
    font-weight: 500;
}
  
  .bowler{
    font-weight: 500;
  }

  .bottom{
    justify-content:center;
    gap: 100px;
  }
  #match_players_container{
    background-color: rgba(255, 111, 145, 0.1);
    border-radius: 5px;
  }
 #batsmen_container{
        border-bottom: 1px solid #ff6f91;
    padding:5px 10px;
  }
  #bowler_container{
    padding:5px 10px;
  }
  .commentry-wrap .commentry-item .left .runsperball{
    font-size:10px;
  }
  @media (max-width: 565px) {
    .matchcentre-top-header .bottom {
        font-size: 11px;
      /* display:block !important; */
    }
}
  .score{
    margin-right: 8px !important;
    margin-bottom: 0px;
}
.overs{
    margin-bottom: 0px;
}
   .matchcentre-top-header .top .teamA .score, .matchcentre-top-header .top .teamB .score {
    font-weight: 400;
    text-transform: none;
   
}
   .inning-break{
   background: linear-gradient(242.58deg, #8542D8 0.9%, #9D3ADF 21.58%, #8542D8 64.98%, #9D3ADF 101.25%);
     color:white;
    padding: 10px;
    text-align: center;
  }
#match-images-container a {
    min-height: 160px;  
    margin: 20px 10px; 
     width:160px;
     overflow:hidden;
  }

  #match-images-container img {
    width: 100%;  
    height: 200px;
    object-fit: cover;
    cursor: pointer; 
  }

  .summary-tab-content{
    display:block;
   text-align: -webkit-center;
  }

   #match-images-container{
    display: grid;
    grid-template-columns: auto auto auto auto;
  }
  .matchcentre-top-header .bottom p{
  margin-bottom: 0px;
  }
.bowlername{
    margin-bottom: 2px;
}
  @media (max-width: 767.98px) {
  .fixed-second-header.matchcentre-second-header {
    height: 135px;
  }
   .matchcentre-main {
       margin-top: 210px;
    }
    #match-images-container{
    grid-template-columns: auto auto;
  }
  .matchcentre-top-header .bottom {
    font-size: 14px;
}
      .footer-mobile-app{
      display:none;
    }
     .teamB .score{
      order:2;
    }
     .teamB .score-span{
      order:1;
    }
      .teamB .teamname{
      order:3;
    }
}
@media (max-width: 565px) {
.match-back{
    display: none;
}
.matchcentre-tab-content {
    padding: 5px 0;
}
.matchdetails-info .matchdetails-item {
    padding: 10px 0;
}
.matchdetails-info{
    font-size: 14px;
}
#match_players_container{
    font-size: 14px;
}
.commentry-wrap .oversummary .top .left p , .commentry-wrap .oversummary .top .right{
    font-size: 13px;
}
.commentry-wrap .oversummary .top{
    padding: 8px 0px;
}
.scorecard-box h5 {
    font-size: 20px !important;
}
.summary-tab-content .player-of-the-match {
max-width: 70%;
  justify-items: center;
}
.commentry-wrap .oversummary{
    margin: 15px 0px;
}
#inningsTab{
    margin-bottom: 15px;
}
.scorecard-box.bodyz{
    padding: 8px 0px;
}
.squad-wrap .title-wrap{
    margin-bottom: 20px;
    margin-top: 20px !important;
}
.squad-wrap .squad-info .left .squaditem.title, .squad-wrap .squad-info .right .squaditem.title{
    margin-bottom: 16px;
}
.squad-wrap .squad-info .left .squaditem, .squad-wrap .squad-info .right .squaditem{
    margin-bottom: 15px;
}
.squad-wrap .squad-info .left .squaditem figcaption p, .squad-wrap .squad-info .right .squaditem figcaption p{
    font-size: 13px;
}
.squad-wrap .squad-info .left .squaditem figcaption label, .squad-wrap .squad-info .right .squaditem figcaption label{
    font-size: 12px;
}
.squad-wrap {
    padding: 20px 0 20px;
}
.backimg {
    width: 25px;
}
.matchcentre-top-header .bottom {
    font-size: 10px;
   display:block !important;
  text-align:center;
}
.completed-match {
        font-size: 8px !important;
    }
  .commentry-wrap .commentry-item .left .runsperball.other{ 
    font-size: 8px; 
  }
   .commentry-wrap .oversummary .top .center{ 
    padding: 0 5px;
  }
.commentry-wrap .commentry-item .left .runsperball{  width: 32px;
    height: 32px;
 }
.commentry-wrap .commentry-item{
  height: auto; padding: 10px 0;
  }
  .matchcentre-top-header .top .teamA .score, .matchcentre-top-header .top .teamB .score{
    font-size:15px;
  }
  .score-span{ 
    font-size: 13px;
  }
}
  .matchcentre-top-header .top .teamA i, .matchcentre-top-header .top .teamB i{
    background-color: #fff;
border-radius: 10px;
    overflow:hidden;
  }
   @media (min-width: 1450px) {
  .matchcentre-top-header .top .teamA i img, .matchcentre-top-header .top .teamB i img{
     width: 100%;
        max-width: 100%;
  }
  }
 
  .add-teamname-wrap{
align-items: start;
  }
  .backIcon {  
    position: relative;
    top: 5px;
}
    @media (max-width: 767.98px) {
      .matchcentre-top-header .top .teamA .score, .matchcentre-top-header .top .teamA .teamname, .matchcentre-top-header .top .teamB .score, .matchcentre-top-header .top .teamB .teamname{ 
        text-align: center;
      }  
  }
  .matchcentre-top-header .top .teamA .teamname, .matchcentre-top-header .top .teamB .teamname {
    font-size: 15px;
}
@media (max-width: 768px){
    .matchcentre-top-header .top .teamA .teamname, .matchcentre-top-header .top .teamB .teamname {
        text-align: center;
      font-size:13px;
    }
}
@media (min-width: 992px){
    .matchcentre-top-header .top .teamA .teamname, .matchcentre-top-header .top .teamB .teamname {
        font-size: 25px;
        max-width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
}


#mvp-container{
  display: flex;
    justify-content: center;
    gap: 10px;
  }
@media (max-width: 768px){
   #mvp-container{
  display: block;
  }
}
 .summary-tab-content .player-of-the-match figcaption p{
    gap:5px;
  }

</style>
<section class="addnewplayer-title-wrap fixed-second-header matchcentre-second-header">
  <i class="right-celebration"></i>
  <div class="container h-100">
      <div class="row h-100 d-flex align-items-center">
          <div class="col-12">
              <div class="add-teamname-wrap position-relative">
                  <a href="{{
    $liveMatch
        ? ($liveMatch->status == 'Active'
            ? 'https://pitchburners.com/matches?status=Live'
            : ($liveMatch->status == 'Completed'
                ? 'https://pitchburners.com/matches?status=Completed'
                : 'https://pitchburners.com/matches?status=Upcoming'))
        : 'https://pitchburners.com/matches?status=Upcoming'
}}" class="backIcon">
     <img class="backimg"src="{{ asset('uploads/images/back.svg') }}" />
                      <span class="match-back">  Back </span>
</a>

                 <div class="addteam-logo d-flex align-items-center justify-content-center matchcentre-top-header">
                    <div class="top">
                        <div class="teamA">
                             <i class="icon">
                           <img src="{{ config('constants.upload_url') . '/team_logos/' . $match->teamOne->logo }}" alt="Team One Logo" />
                           </i>

                            <h3 class="teamname">{{ $match->teamOne->name }}</h3>
                            <p class="score" id="{{'team1-score-' . $match->team1}}">{{isset($liveMatch) && isset($liveMatch->team1_score) ? $liveMatch->team1_score : "0/0"}}</p>

                               <span class="score-span" id="{{ 'team1-over-' . $match->team1 }}"></span>

                           {{--( {{$teamOneover}})--}}
                        </div>
                        <div class="vs"><img width="35" height="35" src="/uploads/images/fi_9359727.svg" /></div>
                        <div class="teamB">
                            <!--<p class="score" id="team2-score">-->
                            <p class="score" id="{{'team2-score-' . $match->team2}}">{{isset($liveMatch) && isset($liveMatch->team2_score) ? $liveMatch->team2_score : "0/0"}}</p>

                                <span class="score-span" id="{{ 'team2-over-' . $match->team2 }}"></span>
                              {{--(  {{$teamTwoover}})--}}
                            <h3 class="teamname">{{ $match->teamTwo->name }}</h3>
                            <i class="icon">

<img src="{{ config('constants.upload_url') . '/team_logos/' . $match->teamTwo->logo }}" alt="Team Two Logo" />
                            </i>
                        </div>
                    </div>
                    <div class="bottom d-flex">

                       @if ($liveMatch)

                       			
                      @if ($liveMatch->status == 'Completed')
                         <p class="completed-match">{{$liveMatch->match_details}}</p>
                    @else
                        <div id="result_container"></div>
                    @endif

                            @if ($liveMatch->status == 'Active')
                                 <div id="curr_rate_container">
                                 </div>

                                 <div id="req_rate_container">
                                 </div>
                           @elseif ($liveMatch->status == 'Canceled')
                      <p class="completed-match">{{$liveMatch->match_details}}</p>
                            @endif

                        @else
                            Match not started yet.
                        @endif

                    </div>
                </div>

              </div>
          </div>
      </div>
  </div>
</section>

    <main class="main-wrapper-start matchcentre-main">
        <div class="container h-100">
            <div class="row h-100">
              <div class="col-12">
                <div class="matchcenter-wrap h-100">
                  <ul class="nav nav-tabs" id="matchCentreTab" role="tablist">
                    <li class="nav-item" role="presentation">
                      <button class="nav-link " id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab" aria-controls="info" aria-selected="true"><span>Info</span></button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="live-tab" data-bs-toggle="tab" data-bs-target="#live" type="button" role="tab" aria-controls="live" aria-selected="false"><span>Live</span></button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link active" id="scorecard-tab" data-bs-toggle="tab" data-bs-target="#scorecard" type="button" role="tab" aria-controls="scorecard" aria-selected="false"><span>Scorecard</span></button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link " id="squad-tab" data-bs-toggle="tab" data-bs-target="#squad" type="button" role="tab" aria-controls="squad" aria-selected="false"><span>Squad<span></button>
                    </li>
                         @if ($liveMatch)
                    <li class="nav-item " role="presentation">
                      <button class="nav-link " id="summary-tab" data-bs-toggle="tab" data-bs-target="#summary" type="button" role="tab" aria-controls="summary" aria-selected="false"><span>MVP<span></button>
                    </li>
                        @endif
                  </ul>
                  <!-- Tab panes -->
                  <div class="tab-content matchcentre-tab-content">
                    <div class="tab-pane " id="info" role="tabpanel" aria-labelledby="info-tab" tabindex="0">
                      <div class="matchdetails-info">
                          <h3>Match Details</h3>

                          {{-- Match ID and Tournament --}}
                          <div class="matchdetails-item">
                              <p class="label">Tournament:</p>
                              <p class="info">{{ $tournamentName ?? 'Tournament not available' }}</p>
                          </div>

                          {{-- Venue (Ground) --}}
                          <div class="matchdetails-item">
                              <p class="label">Venue:</p>
                              <p class="info">{{ $groundName ?? 'Venue not available' }}</p>
                          </div>

                          {{-- Date & Time --}}
                          <div class="matchdetails-item">
                              <p class="label">Date & Time:</p>
                              <p class="info">{{ \Carbon\Carbon::parse($match->match_date_time)->format('l jS F, g:i A') }}</p>
                          </div>

                          {{-- Toss Result --}}
                          <div class="matchdetails-item">
                              <p class="label">Toss:</p>
                              <p class="info">
                                  @if ($liveMatch)
                                      {{ $tossWinnerName ?? 'No toss information available' }}
                                  @else
                                      Match not started yet
                                  @endif
                              </p>
                          </div>

                         {{-- Playing XI for Team A --}}
                          <div class="matchdetails-item">
                              <p class="label">{{ $match->teamOne->name }}</p>
                              @foreach($teamAPlayers as $player)
                                  {{ $player->name }} @if($player->is_captain == 1) (C) @endif @if(!$loop->last), @endif
                              @endforeach
                          </div>

                          {{-- Playing XI for Team B --}}
                          <div class="matchdetails-item">
                              <p class="label">{{ $match->teamTwo->name }}</p>
                              @foreach($teamBPlayers as $player)
                                  {{ $player->name }} @if($player->is_captain == 1) (C) @endif @if(!$loop->last), @endif
                              @endforeach
                          </div>

                          {{-- Umpires --}}
                          <div class="matchdetails-item">
                              <p class="label">Umpires:</p>
                              <p class="info">
                                 @if ($liveMatch)
                                    @if (!empty($umpires['first_umpire']) || !empty($umpires['second_umpire']) || !empty($umpires['third_umpire']))
                                        {{ $umpires['first_umpire'] ?? 'Not selected' }},
                                        {{ $umpires['second_umpire'] ?? 'Not selected' }},
                                        {{ $umpires['third_umpire'] ?? 'Not selected' }}
                                    @else
                                        Umpires not selected
                                    @endif
                                @else
                                    Match not started yet
                                @endif

                              </p>
                          </div>

                          {{-- Scorers --}}
                          <div class="matchdetails-item">
                              <p class="label">Scorers:</p>
                              <p class="info">
                                 @if ($liveMatch)
                                      @if (!empty($scorers['first_scorer']) || !empty($scorers['second_scorer']))
                                          {{ $scorers['first_scorer'] ?? 'Not selected' }},
                                          {{ $scorers['second_scorer'] ?? 'Not selected' }}
                                      @else
                                          Scorers not selected
                                      @endif
                                  @else
                                      Match not started yet
                                  @endif

                              </p>
                          </div>
                      </div>
                    </div>

                    <div class="tab-pane" id="live" role="tabpanel" aria-labelledby="live-tab" tabindex="0">
                        @if ($liveMatch)
                      <section id="match_players_container">
                        <div id="batsmen_container">

                        </div>
                        <div id="bowler_container"></div>
                      </section>

                      <div class="commentry-wrap">
                        <div id="secondSuperOverSecondInningSummary"></div>
                      </div>
                      <div class="commentry-wrap">
                        <div id="secondSuperOverFirstInningSummary"></div>
                      </div>
                      <div class="commentry-wrap">
                        <div id="superOverSecondInningSummary"></div>
                      </div>
                      <div class="commentry-wrap">
                        <div id="superOverFirstInningSummary"></div>
                      </div>
                      <div class="commentry-wrap">
                        <div id="secondInningSummary"></div>
                      </div>
                      <div class="commentry-wrap">
                        <div id="firstInningSummary"></div>

                      </div>
                       @else
                         <div class="title-wrap text-center no_match">Stay tuned for live match update.</div>
                      @endif
                    </div>

                    <div class="tab-pane active" id="scorecard" role="tabpanel" aria-labelledby="scorecard-tab" tabindex="0">
                       @if ($liveMatch)
                      <!-- Nav tabs -->
                      <ul class="nav nav-tabs" id="inningsTab" role="tablist">
                        <li class="nav-item" role="presentation">
                          <button class="nav-link active" id="firstinnings-tab" data-bs-toggle="tab" data-bs-target="#firstinnings" type="button" role="tab" aria-controls="firstinnings" aria-selected="true"></button>
                        </li>
                        <li class="nav-item" role="presentation">
                          <button class="nav-link" id="secondinnings-tab" data-bs-toggle="tab" data-bs-target="#secondinnings" type="button" role="tab" aria-controls="secondinnings" aria-selected="false"></button>
                        </li>
                      </ul>
                      <!-- Tab panes -->
                      <div class="tab-content">
                         <div class="tab-pane active" id="firstinnings" role="tabpanel" aria-labelledby="firstinnings-tab" tabindex="0">
                          <div id="scoreBoardFirstInning"></div>
                          <div id="scoreBoardSuperOverSecondInning"></div>
                          <div id="scoreBoardSecondSuperOverFirstInning"></div>
                         </div>
                         <div class="tab-pane " id="secondinnings" role="tabpanel" aria-labelledby="secondinnings-tab" tabindex="0">
                          <div id="scoreBoardSecondInning"></div>
                          <div id="scoreBoardSuperOverFirstInning"></div>
                          <div id="scoreBoardSecondSuperOverSecondInning"></div>
                         </div>

                      </div>
                       @else
                         <div class="title-wrap text-center no_match">Scoreboard will be available once the match starts.</div>
                      @endif
                      </div>


                    <div class="tab-pane" id="squad" role="tabpanel" aria-labelledby="squad-tab" tabindex="0">
                        @if ($liveMatch)
                     <div class="squad-wrap">

        <div class="title-wrap text-center">Playing 11</div>
        <div class="squad-info">
            <div class="left">
                <!-- Team A Playing 11 -->
                <div class="squaditem title">
                    <i><img src="{{ config('constants.upload_url') . '/team_logos/' . $match->teamOne->logo }}" /></i>
                    <p>{{ $match->teamOne->name }}</p>
                </div>
                @foreach($playing11TeamA as $player)
                <div class="squaditem">
                    <figure>
                        <img src="{{ config('constants.upload_url') . '/player_images/' . $player->image }}" alt="Player Image" />
                    </figure>
                    <figcaption>
                        <p>{{ $player->name }}</p>
                        <label>{{ $player->role }}</label>
                    </figcaption>
                </div>
                @endforeach
            </div>
            <div class="center">
                <div class="vs">VS</div>
            </div>
            <div class="right">
                <!-- Team B Playing 11 -->
                <div class="squaditem title">
                    <i><img src="{{ config('constants.upload_url') . '/team_logos/' . $match->teamTwo->logo }}" /></i>
                    <p>{{ $match->teamTwo->name }}</p>
                </div>
                @foreach($playing11TeamB as $player)
                <div class="squaditem">
                    <figure>
                        <img src="{{ config('constants.upload_url') . '/player_images/' . $player->image }}" alt="Player Image" />
                    </figure>
                    <figcaption>
                        <p>{{ $player->name }}</p>
                        <label>{{ $player->role }}</label>
                    </figcaption>
                </div>
                @endforeach
            </div>
        </div>
        <!-- Bench -->
        <div class="title-wrap text-center mt-5">Bench</div>
        <div class="squad-info">
            <div class="left">
                @foreach($benchTeamA as $benchPlayer)
                <div class="squaditem">
                    <figure>
                        <img src="{{ config('constants.upload_url') . '/player_images/' . $benchPlayer->image }}" alt="Player Image" />
                    </figure>
                    <figcaption>
                        <p>{{ $benchPlayer->name }}</p>
                        <label>{{ $benchPlayer->role }}</label>
                    </figcaption>
                </div>
                @endforeach
            </div>
            <div class="center">
                <div class="vs">VS</div>
            </div>
            <div class="right">
                @foreach($benchTeamB as $benchPlayer)
                <div class="squaditem">
                    <figure>
                        <img src="{{ config('constants.upload_url') . '/player_images/' . $benchPlayer->image }}" alt="Player Image" />
                    </figure>
                    <figcaption>
                        <p>{{ $benchPlayer->name }}</p>
                        <label>{{ $benchPlayer->role }}</label>
                    </figcaption>
                </div>
                @endforeach
            </div>
        </div>
</div>
 @else
        <div class="title-wrap text-center no_match">Team lineups will be announced before the match.</div>
    @endif
                  </div>

                     <div class="tab-pane " id="summary" role="tabpanel" aria-labelledby="summary-tab" tabindex="0">
                      <div class="summary-tab-content">
    @if ($liveMatch)
        <div id="mvp-container">
            <!-- MVP details will be dynamically injected here -->
        </div>
        
        <div id="no-mvp-message" style="display: none; text-align: center;">
            <p>No Man of the Match details are available for this match.</p>
        </div>

        <h6 id="match-images-heading" style="display: none;">Match Images</h6>
        <figure id="match-images-container">
            <!-- Dynamically added images will go here -->
        </figure>
    @else
        <p class="text-center">Man of the Match details will Update Soon..</p>
    @endif
</div>
                  </div>
                  </div>
                </div>
              </div>
        </div>
        </div>
    </main>
          <!-- Display the live viewer count -->
<div id="viewer-count" style="display:none; position: fixed; bottom: 10px; left: 10px; background-color: rgba(0, 0, 0, 0.7); color: white; padding: 10px; border-radius: 5px;">
    Viewers Count: 0
</div>

<button class="scroll-to-top" id="scrollToTop" aria-label="Scroll to top">
   <i class="fa-solid fa-chevron-up"></i>
</button>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4/dist/fancybox.css" />
   <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4/dist/fancybox.umd.js"></script>

<script type="module">
  import { io } from "https://cdn.socket.io/4.8.0/socket.io.esm.min.js";
  const API = '{{$apiUrl}}';
  const SOCKET_URL = '{{$socketUrl}}';
  const matchId = '{{ isset($liveMatch) ? $liveMatch->id : -1 }}';
  const scheduleMatchId = '{{$match->id}}';
   console.log(matchId);
  const socket = io(SOCKET_URL, {
    secure: true,
    transports: ['websocket'],
    reconnection: true,
    rejectUnauthorized: false
  });
  socket.on('connect', () =>{
      console.warn('match-center socket connected')
  })
  let team1 = '{{$match->team1}}';
  let team2 = '{{$match->team2}}';
//let over1 =  {{--$teamOneover--}};
//let over2 = {{--$teamTwoover--}};
 let team1OverSummary = {
    id: team1,
    overSummary: [],
    commentary: [],
    superOverSummary: [],
    superOverCommentary: [],
    secondSuperOverSummary: [],
    secondSuperOverCommentary: [],
    isFirstInningBatter: null,
    isSecondInningBatter: null,
    isSuperOverFirstInningBatter: null,
    isSuperOverSecondInningBatter: null,
    isSecondSuperOverFirstInningBatter:null,
    isSecondSuperOverSecondInningBatter:null,
    name: '',
    score: '0/0',
        }
  let team2OverSummary = {
      id: team2,
      overSummary: [],
      commentary: [],
      superOverSummary: [],
      superOverCommentary: [],
      secondSuperOverSummary: [],
      secondSuperOverCommentary: [],
      isFirstInningBatter: null,
      isSecondInningBatter: null,
      isSuperOverFirstInningBatter: null,
      isSuperOverSecondInningBatter: null,
      isSecondSuperOverFirstInningBatter:null,
      isSecondSuperOverSecondInningBatter:null,
      name: '',
    score: '0/0',
  }
  let scoreBoardFirstInning =  {
    name: 'Batting Team',
      totalScores: '0/0',
      totalOvers: '00 ',
      batting: [],
      bowling: [],
      fallOfWickets: [],
      extras: {},
  }
  let scoreBoardSecondInning = {
    name: 'Batting Team',
      totalScores: '0/0',
      totalOvers: '00 ',
      batting: [],
      bowling: [],
      fallOfWickets: [],
      extras: {},
  }
  let scoreBoardSuperOverFirstInning = {
    name: 'Batting Team',
      totalScores: '0/0',
      totalOvers: '00 ',
      batting: [],
      bowling: [],
      fallOfWickets: [],
      extras: {},
  }
  let scoreBoardSuperOverSecondInning = {
    name: 'Batting Team',
      totalScores: '0/0',
      totalOvers: '00 ',
      batting: [],
      bowling: [],
      fallOfWickets: [],
      extras: {},
  }
  let scoreBoardSecondSuperOverFirstInning = {
    name: 'Batting Team',
      totalScores: '0/0',
      totalOvers: '00 ',
      batting: [],
      bowling: [],
      fallOfWickets: [],
      extras: {},
  }
  let scoreBoardSecondSuperOverSecondInning = {
    name: 'Batting Team',
      totalScores: '0/0',
      totalOvers: '00 ',
      batting: [],
      bowling: [],
      fallOfWickets: [],
      extras: {},
  }
 document.addEventListener('DOMContentLoaded', () => {
    if (matchId !== -1) {
        $.ajax({
            url: `${API}/mvp/${matchId}`,  // Ensure `API` is properly defined
            method: 'GET',
            success: function(response) {
                if (response.status === 'success' && response.data.length > 0) {
                    const mvpContainer = $('#mvp-container');
                    mvpContainer.empty(); // Clear any previous MVP details

                    response.data.forEach((mvpData) => {
                        const playerImage = mvpData.player.image ? mvpData.player.image : "/default-avatar.png";
                        const playerName = mvpData.player.name;
                        const team = mvpData.team.name;
                        const runs = mvpData.runs;
                        const ballsFaced = mvpData.balls_faced;
                        const fours = mvpData.fours;
                        const sixes = mvpData.sixes;
                        const oversBowled = mvpData.overs_bowled;
                        const wickets = mvpData.wickets;
                        const runsConceded = mvpData.runs_conceded;
                        const economy = mvpData.economy;

                        // MVP Card
                        const mvpHtml = `
                            <div class="player-of-the-match playermatch">
                                <figure>
                                    <img src="{{ config('constants.upload_url') }}/player_images/${playerImage}" alt="Player Image" />
                                    <h4>PLAYER OF THE MATCH</h4>
                                </figure>
                                <figcaption>
                                    <div class="left">
                                        <h6>${playerName}</h6>
                                        <label>${team}</label>
                                        <p>
                                            <span><strong>${runs} (${ballsFaced})</strong></span> |
                                            <span>${fours} - 4s</span> |
                                            <span>${sixes} - 6s</span> |
                                            <strong>${runsConceded}-${wickets} (${oversBowled})</strong>
                                        </p>
                                    </div>
                                </figcaption>
                            </div>`;

                        // Append MVP to container
                        mvpContainer.append(mvpHtml);
                    });

                    // Show MVP section
                    $('#match-images-heading').show();
                } else {
                    // Hide MVP section if no data is available
                    $('#no-mvp-message').show();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching MVP data:', error);
            }
        });
    }
});
  document.addEventListener('DOMContentLoaded', () => {

    if (matchId !== -1) {

        $.ajax({

            url: `${API}/match-images/${matchId}`,  // Make sure API is defined and matchId is available

            method: 'GET',

            success: function(data) {

                console.log('API Response:', data);

                if (data.success === true) {

                    const playerImages = data.images;  // Assuming data.images is an array of image filenames

                    console.log('playerImages:', playerImages);



                    // Clear any previous images (if needed)

                    $('#match-images-container').empty();



                    // Loop through player images and dynamically update HTML

                    playerImages.forEach(image => {

                        // Create anchor and image elements for Fancybox

                        const anchorElement = $('<a>', {

                            href: `{{ config('constants.upload_url') }}/match_images/${image}`,

                            'data-fancybox': 'gallery', // Group images under the "gallery"

                        });



                        const imageElement = $('<img>', {

                            src: `{{ config('constants.upload_url') }}/match_images/${image}`,

                            alt: 'Player Image',

                            class: 'player-image' // Add any CSS classes you need

                        });



                        // Append the image inside the anchor tag

                        anchorElement.append(imageElement);



                        // Append the anchor tag to the container

                        $('#match-images-container').append(anchorElement);

                    });



                    // Initialize Fancybox (optional, Fancybox auto-binds by default)

                    Fancybox.bind("[data-fancybox='gallery']", {});

                }

            },

            error: function(xhr, status, error) {

                // Handle any errors that may occur during the API call

                console.error('Error fetching match images:', error);

            }

        });

    }

});

        document.addEventListener('DOMContentLoaded', () => {
            if(matchId != -1){
                $.ajax({
                    url: `${API}/getUptoMatchData/${matchId}`,
                    method: 'GET',
                    success: function(data) {
                        let {batsMen, bowler, summary} = data;
                        let {team1Summary , team2Summary, battingTeamId, bowlingTeamId, battingTeamName, bowlingTeamName} = summary;

                        team1OverSummary = {
                            //id: battingTeamId,
                          	...team1OverSummary,
                            name: battingTeamName,
                            // overSummary: team2Summary.overSummary,
                            overSummary: team1Summary.overSummary,
                            commentary: team1Summary.commentary,
                            superOverSummary: team1Summary.superOverSummary,
                            superOverCommentary: team1Summary.superOverCommentary,
                            secondSuperOverSummary: team1Summary.secondSuperOverSummary,
                            secondSuperOverCommentary: team1Summary.secondSuperOverCommentary,
                            isFirstInningBatter: team1Summary.isFirstInningBatter,
                            isSecondInningBatter: team1Summary.isSecondInningBatter,
                            isSuperOverFirstInningBatter: team1Summary.isSuperOverFirstInningBatter,
                            isSuperOverSecondInningBatter: team1Summary.isSuperOverSecondInningBatter,
                            isSecondSuperOverFirstInningBatter: team1Summary.isSecondSuperOverFirstInningBatter,
                            isSecondSuperOverSecondInningBatter: team1Summary.isSecondSuperOverSecondInningBatter

                        }

                        if(team1OverSummary.overSummary.length)
                        summaryCard('#firstInningSummary', team1OverSummary, team1OverSummary.overSummary, team1OverSummary.commentary, batsMen, bowler);

                        if(team1OverSummary.superOverSummary.length)
                        summaryCard('#superOverSecondInningSummary', team1OverSummary, team1OverSummary.superOverSummary,  team1OverSummary.superOverCommentary, batsMen, bowler);

                        if(team1OverSummary.secondSuperOverSummary.length)
                        summaryCard('#secondSuperOverFirstInningSummary', team1OverSummary, team1OverSummary.secondSuperOverSummary, team1OverSummary.secondSuperOverCommentary, batsMen, bowler);

                        team2OverSummary = {
                            //id: bowlingTeamId,
                          	...team2OverSummary,
                            name: bowlingTeamName,
                            overSummary: team2Summary.overSummary,
                            commentary: team2Summary.commentary,
                            superOverSummary: team2Summary.superOverSummary,
                            superOverCommentary: team2Summary.superOverCommentary,
                            secondSuperOverSummary: team2Summary.secondSuperOverSummary,
                            secondSuperOverCommentary: team2Summary.secondSuperOverCommentary,
                            isFirstInningBatter: team2Summary.isFirstInningBatter,
                            isSecondInningBatter: team2Summary.isSecondInningBatter,
                            isSuperOverFirstInningBatter: team2Summary.isSuperOverFirstInningBatter,
                            isSuperOverSecondInningBatter: team2Summary.isSuperOverSecondInningBatter,
                            isSecondSuperOverFirstInningBatter: team2Summary.isSecondSuperOverFirstInningBatter,
                            isSecondSuperOverSecondInningBatter: team2Summary.isSecondSuperOverSecondInningBatter
                        }
                        if(team2OverSummary.overSummary.length)
                        summaryCard('#secondInningSummary', team2OverSummary, team2OverSummary.overSummary, team2OverSummary.commentary, batsMen, bowler);

                        if(team2OverSummary.superOverSummary.length)
                        summaryCard('#superOverFirstInningSummary', team2OverSummary, team2OverSummary.superOverSummary, team2OverSummary.superOverCommentary, batsMen, bowler);

                        if(team2OverSummary.secondSuperOverSummary.length)
                        summaryCard('#secondSuperOverSecondInningSummary', team2OverSummary, team2OverSummary.secondSuperOverSummary, team2OverSummary.secondSuperOverCommentary, batsMen, bowler);

                        console.warn('team1OverSummary => ', team1OverSummary)
                        console.warn('team2OverSummary => ', team2OverSummary)
                    }
                })
                $.ajax({
                  url:`${API}/getScoreBoardUptoData/${matchId}`,
                  method: 'GET',
                  success: function(data){
                    const {batting_data, bowler_data, fall_of_wicket, team_details} = data;

                    scoreBoardFirstInning = {
                      ...scoreBoardFirstInning,
                      batting: batting_data.firstInningsBatsMen,
                      bowling: bowler_data.firstInningsBowlers,
                      fallOfWickets: fall_of_wicket.firstInningsFallOfWickets,
                      name: team_details.firstInningsDetails.name,
                      totalScores: team_details.firstInningsDetails.score,
                      totalOvers: team_details.firstInningsDetails.curr_over,
                      extras: team_details.firstInningsDetails.extras,
                    }
                    if(scoreBoardFirstInning.batting.length){
                      scoreBoardCard('#scoreBoardFirstInning', scoreBoardFirstInning, scoreBoardFirstInning.batting, scoreBoardFirstInning.bowling, scoreBoardFirstInning.fallOfWickets, scoreBoardFirstInning.extras,scoreBoardFirstInning.totalScores,scoreBoardFirstInning.totalOvers);
                      const firstInningsTab = document.querySelector('#firstinnings-tab')
                      firstInningsTab.innerHTML =  team_details.firstInningsDetails.name;
                    }
                    scoreBoardSecondInning = {
                      batting: batting_data.secondInningsBatsMen,
                      bowling: bowler_data.secondInningsBowlers,
                      fallOfWickets: fall_of_wicket.secondInningsFallOfWickets,
                      name: team_details.secondInningsDetails.name,
                      totalScores: team_details.secondInningsDetails.score,
                      totalOvers: team_details.secondInningsDetails.curr_over,
                      extras: team_details.secondInningsDetails.extras,
                    }
                    if(scoreBoardSecondInning.batting.length) {
                      scoreBoardCard('#scoreBoardSecondInning', scoreBoardSecondInning, scoreBoardSecondInning.batting, scoreBoardSecondInning.bowling, scoreBoardSecondInning.fallOfWickets, scoreBoardSecondInning.extras,scoreBoardSecondInning.totalScores,scoreBoardSecondInning.totalOvers);
                      const secondInningsTab = document.querySelector('#secondinnings-tab')
                      secondInningsTab.innerHTML = team_details.secondInningsDetails.name;
                    }
                    scoreBoardSuperOverFirstInning =  {
                      batting: batting_data.superOverFirstInningsBatsMen,
                      bowling: bowler_data.superOverFirstInningsBowlers,
                      fallOfWickets: fall_of_wicket.superOverFirstInningsFallOfWickets,
                      name: team_details.superOverFirstInningsDetails.name,
                      totalScores: team_details.superOverFirstInningsDetails.score,
                      totalOvers: team_details.superOverFirstInningsDetails.curr_over,
                      extras: team_details.superOverFirstInningsDetails.extras,
                    }
                    if(scoreBoardSuperOverFirstInning.batting.length)
                      scoreBoardCard('#scoreBoardSuperOverFirstInning', scoreBoardSuperOverFirstInning, scoreBoardSuperOverFirstInning.batting, scoreBoardSuperOverFirstInning.bowling, scoreBoardSuperOverFirstInning.fallOfWickets, scoreBoardSuperOverFirstInning.extras,scoreBoardSuperOverFirstInning.totalScores,scoreBoardSuperOverFirstInning.totalOvers);
                    scoreBoardSuperOverSecondInning =  {
                      batting: batting_data.superOverSecondInningsBatsMen,
                      bowling: bowler_data.superOverSecondInningsBowlers,
                      fallOfWickets: fall_of_wicket.superOverSecondInningsFallOfWickets,
                      name: team_details.superOverSecondInningsDetails.name,
                      totalScores: team_details.superOverSecondInningsDetails.score,
                      totalOvers: team_details.superOverSecondInningsDetails.curr_over,
                      extras: team_details.superOverSecondInningsDetails.extras,
                    }
                    if(scoreBoardSuperOverSecondInning.batting.length)
                      scoreBoardCard('#scoreBoardSuperOverSecondInning', scoreBoardSuperOverSecondInning, scoreBoardSuperOverSecondInning.batting, scoreBoardSuperOverSecondInning.bowling, scoreBoardSuperOverSecondInning.fallOfWickets, scoreBoardSuperOverSecondInning.extras,scoreBoardSuperOverSecondInning.totalScores,scoreBoardSuperOverSecondInning.totalOvers);
                    scoreBoardSecondSuperOverFirstInning =  {
                      batting: batting_data.secondSuperOverFirstInningsBatsMen,
                      bowling: bowler_data.secondSuperOverFirstInningsBowlers,
                      fallOfWickets: fall_of_wicket.secondSuperOverFirstInningsFallOfWickets,
                      name: team_details.secondSuperOverFirstInningsDetails.name,
                      totalScores: team_details.secondSuperOverFirstInningsDetails.score,
                      totalOvers: team_details.secondSuperOverFirstInningsDetails.curr_over,
                      extras: team_details.secondSuperOverFirstInningsDetails.extras,
                    }
                    if(scoreBoardSecondSuperOverFirstInning.batting.length)
                      scoreBoardCard('#scoreBoardSecondSuperOverFirstInning', scoreBoardSecondSuperOverFirstInning, scoreBoardSecondSuperOverFirstInning.batting, scoreBoardSecondSuperOverFirstInning.bowling, scoreBoardSecondSuperOverFirstInning.fallOfWickets, scoreBoardSecondSuperOverFirstInning.extras,scoreBoardSecondSuperOverFirstInning.totalScores,scoreBoardSecondSuperOverFirstInning.totalOvers);
                    scoreBoardSecondSuperOverSecondInning =  {
                      batting: batting_data.secondSuperOverSecondInningsBatsMen,
                      bowling: bowler_data.secondSuperOverSecondInningsBowlers,
                      fallOfWickets: fall_of_wicket.secondSuperOverSecondInningsFallOfWickets,
                      name: team_details.secondSuperOverSecondInningsDetails.name,
                      totalScores: team_details.secondSuperOverSecondInningsDetails.score,
                      totalOvers: team_details.secondSuperOverSecondInningsDetails.curr_over,
                      extras: team_details.secondSuperOverSecondInningsDetails.extras,
                    }
                    if(scoreBoardSecondSuperOverSecondInning.batting.length)
                      scoreBoardCard('#scoreBoardSecondSuperOverSecondInning', scoreBoardSecondSuperOverSecondInning, scoreBoardSecondSuperOverSecondInning.batting, scoreBoardSecondSuperOverSecondInning.bowling, scoreBoardSecondSuperOverSecondInning.fallOfWickets, scoreBoardSecondSuperOverSecondInning.extras,scoreBoardSecondSuperOverSecondInning.totalScores,scoreBoardSecondSuperOverSecondInning.totalOvers);

                  }
                });
                $.ajax({
                  url: `${API}/getCurrentTeamScore/${matchId}`,
                  method: 'GET',
                  success: function(data) {
                    setTeamScores(data)
                  }
                });
            }else{
                console.warn('Match not yet started');
            }
        })
        const handlePresentSummaryData = (prev, overSummaryData, wantedOver) => {
            let isOverAlreadyThere = prev?.find((e) => e.over == wantedOver);
            let overSummaryDatakeys = Object.keys(overSummaryData);

            if(!isOverAlreadyThere && overSummaryDatakeys.length) {
                // console.warn('no existing over and data is there')
                return [...prev, overSummaryData];
            }
            if(!isOverAlreadyThere && !prev?.length && !overSummaryDatakeys.length) {
                // console.warn('no existing over, prev is empty and data is not there')
                return [...prev]
            }
            if(!isOverAlreadyThere && !prev?.length && overSummaryDatakeys.length) {
                // console.warn('no existing over, prev is empty and data is there')
                return [overSummaryData]
            }
            let newPrev = prev.map((e) => {
                return e.over == wantedOver ? {...e, wickets: overSummaryData.wickets, runs: overSummaryData.runs, balls: [...overSummaryData?.balls]} : {...e};
            });
            if(isOverAlreadyThere && newPrev && newPrev.length) {
                // console.warn('existing over, prev is not empty and data is there')
                return [...newPrev];
            }
            // return [...prev];
        }
        const handlePresentCommentaryData = (prev, commentaryData) => {
            // let isAlreadyThere = prev.find((e) => e.over == commentaryData.over);
            // return isAlreadyThere ? [...prev] : [...prev, commentaryData]
            return [...prev, commentaryData];
        }
        socket.on(`set-updated-match-data-${matchId}`, (matchId) => {
                $.ajax({
                    url: `${API}/getPresentMatchData/${matchId}`,
                    method: 'GET',
                    success: function(data) {
                        const presentMatchData = data;
                        let {batsMen, bowler, summary} = presentMatchData;
                        let {overSummary, commentary, innings, battingTeamId, battingTeamName}  = summary;
                        let wantedOver = overSummary.over;

                        if(innings == 0){
                            let overSummaryData = handlePresentSummaryData(team1OverSummary.overSummary, overSummary, wantedOver);
                            let commentaryData = handlePresentCommentaryData(team1OverSummary.commentary, commentary);
                            team1OverSummary = {
                                ...team1OverSummary,
                                //id: battingTeamId,
                                //name: battingTeamName,
                                overSummary: overSummaryData,
                                commentary: commentaryData,
                            }
                            summaryCard('#firstInningSummary', team1OverSummary, team1OverSummary.overSummary, team1OverSummary.commentary, batsMen, bowler);

                            // summaryCard('#firstInningSummary', team1OverSummary, team1OverSummary.overSummary, team1OverSummary.commentary);
                            console.warn('live team1OverSummary => ', team1OverSummary)
                        }
                        if(innings == 1){
                            let overSummaryData = handlePresentSummaryData(team2OverSummary.overSummary, overSummary, wantedOver);
                            let commentaryData = handlePresentCommentaryData(team2OverSummary.commentary, commentary);
                            team2OverSummary = {
                                ...team2OverSummary,
                                //id: battingTeamId,
                                //name: battingTeamName,
                                overSummary: overSummaryData,
                                commentary: commentaryData,
                            }
                            summaryCard('#secondInningSummary', team2OverSummary, team2OverSummary.overSummary, team2OverSummary.commentary, batsMen, bowler);
                        }
                        if(innings == 2){
                            let overSummaryData = handlePresentSummaryData(team2OverSummary.superOverSummary, overSummary, wantedOver);
                            let commentaryData = handlePresentCommentaryData(team2OverSummary.superOverCommentary, commentary);
                            team2OverSummary = {
                                ...team2OverSummary,
                                //id: battingTeamId,
                                //name: battingTeamName,
                                superOverSummary: overSummaryData,
                                superOverCommentary: commentaryData,
                            }
                            summaryCard('#superOverFirstInningSummary', team2OverSummary, team2OverSummary.superOverSummary, team2OverSummary.superOverCommentary, batsMen, bowler);
                        }
                        if(innings == 3){
                            let overSummaryData = handlePresentSummaryData(team1OverSummary.superOverSummary, overSummary, wantedOver);
                            let commentaryData = handlePresentCommentaryData(team1OverSummary.superOverCommentary, commentary);
                            team1OverSummary = {
                                ...team1OverSummary,
                               // id: battingTeamId,
                                //name: battingTeamName,
                                superOverSummary: overSummaryData,
                                superOverCommentary: commentaryData,
                            }
                          summaryCard('#superOverSecondInningSummary', team1OverSummary, team1OverSummary.superOverSummary, team1OverSummary.superOverCommentary, batsMen, bowler);
                        }
                        if(innings == 4){
                            let overSummaryData = handlePresentSummaryData(team1OverSummary.secondSuperOverSummary, overSummary, wantedOver);
                            let commentaryData = handlePresentCommentaryData(team1OverSummary.secondSuperOverCommentary, commentary);
                            team1OverSummary = {
                                ...team1OverSummary,
                                //id: battingTeamId,
                                //name: battingTeamName,
                                secondSuperOverSummary: overSummaryData,
                                secondSuperOverCommentary: commentaryData,
                            }
                          summaryCard('#secondSuperOverFirstInningSummary', team1OverSummary, team1OverSummary.secondSuperOverSummary, team1OverSummary.secondSuperOverCommentary, batsMen, bowler);
                        }
                        if(innings == 5){
                            let overSummaryData = handlePresentSummaryData(team2OverSummary.secondSuperOverSummary, overSummary, wantedOver);
                            let commentaryData = handlePresentCommentaryData(team2OverSummary.secondSuperOverCommentary, commentary);
                            team2OverSummary = {
                                ...team2OverSummary,
                               // id: battingTeamId,
                                //name: battingTeamName,
                                secondSuperOverSummary: overSummaryData,
                                secondSuperOverCommentary: commentaryData,
                            }
                            summaryCard('#secondSuperOverSecondInningSummary', team2OverSummary, team2OverSummary.secondSuperOverSummary, team2OverSummary.secondSuperOverCommentary, batsMen, bowler);
                        }
                        console.warn('live data => ', data)
                    }
                });
        })
        socket.on(`set-match-score-${matchId}`, (matchId) => {
            $.ajax({
              url: `${API}/getCurrentTeamScore/${matchId}`,
              method: 'GET',
              success: function(data) {
                setTeamScores(data)
              }
            });
        })
        socket.on(`updated-players-${matchId}`, (data) =>{

        })
        socket.on(`set-innings-completed-${matchId}`, (matchId) => {
          $.ajax({
            url: `${API}/getInningStatus/${matchId}/${inning}`,
            method: 'GET',
            success: function(data){
              const {battingTeam, completedInning} =  data;
              const secondInnings = [2, 4];
              if(secondInnings.includes(Number(completedInning))) {
                team1OverSummary.score = '0/0'
                team2OverSummary.score = '0/0'
              }
            }
          });
        })
        socket.on(`set-scoreboard-match-data-${matchId}`, (matchId) => {
          $.ajax({
            url: `${API}/getScoreBoardPresentData/${matchId}`,
            method: 'GET',
            success: function (data){
              let {inning, batting_data, bowling_data, fall_of_wicket, team_details} = data;
              if(inning == 0){
                 const batsMenData = batting_data.length ? batting_data : scoreBoardFirstInning.batting;
                  bowling_data = bowling_data ? [bowling_data] : null;
                  //const bowlersData = handlePresentBowlersData(scoreBoardFirstInning.bowling, bowling_data);
                  const bowlersData = bowling_data.length ? bowling_data.pop() : scoreBoardFirstInning.bowling;
                  fall_of_wicket = fall_of_wicket.length ? fall_of_wicket : scoreBoardFirstInning.fallOfWickets;
                scoreBoardFirstInning =  {
                  ...scoreBoardFirstInning,
                  batting: [...batsMenData],
                  bowling: bowlersData,
                  fallOfWickets: [...fall_of_wicket],
                  name: team_details.name,
                  totalScores: team_details.score,
                  totalOvers:team_details.curr_over,
                  extras: team_details.extras,
                }
                scoreBoardCard('#scoreBoardFirstInning', scoreBoardFirstInning, scoreBoardFirstInning.batting, scoreBoardFirstInning.bowling, scoreBoardFirstInning.fallOfWickets, scoreBoardFirstInning.extras,scoreBoardFirstInning.totalScores,scoreBoardFirstInning.totalOvers);
                const firstInningsTab = document.querySelector('#firstinnings-tab')
                      firstInningsTab.innerHTML =  team_details.name;
              }
              if(inning == 1){
               const batsMenData = batting_data.length ? batting_data : scoreBoardSecondInning.batting;
                  bowling_data = bowling_data ? [bowling_data] : null;
                  //const bowlersData = handlePresentBowlersData(scoreBoardFirstInning.bowling, bowling_data);
                  const bowlersData = bowling_data.length ? bowling_data.pop() : scoreBoardSecondInning.bowling;
                  fall_of_wicket = fall_of_wicket.length ? fall_of_wicket : scoreBoardSecondInning.fallOfWickets;
                scoreBoardSecondInning =  {
                  ...scoreBoardSecondInning,
                  batting: [...batsMenData],
                  bowling: bowlersData,
                  fallOfWickets: [...fall_of_wicket],
                  name: team_details.name,
                  totalScores: team_details.score,
                   totalOvers:team_details.curr_over,
                  extras: team_details.extras,
                }
                scoreBoardCard('#scoreBoardSecondInning', scoreBoardSecondInning, scoreBoardSecondInning.batting, scoreBoardSecondInning.bowling, scoreBoardSecondInning.fallOfWickets, scoreBoardSecondInning.extras,scoreBoardSecondInning.totalScores,scoreBoardSecondInning.totalOvers);
                const secondInningsTab = document.querySelector('#secondinnings-tab')
                      secondInningsTab.innerHTML = team_details.name;
              }
              if(inning == 2){
               const batsMenData = batting_data.length ? batting_data : scoreBoardSuperOverFirstInning.batting;
                  bowling_data = bowling_data ? [bowling_data] : null;
                 //const bowlersData = handlePresentBowlersData(scoreBoardFirstInning.bowling, bowling_data);
                  const bowlersData = bowling_data.length ? bowling_data.pop() : scoreBoardSuperOverFirstInning.bowling;
                  fall_of_wicket = fall_of_wicket.length ? fall_of_wicket : scoreBoardSuperOverFirstInning.fallOfWickets;
                scoreBoardSuperOverFirstInning =  {
                  ...scoreBoardSuperOverFirstInning,
                  batting: [...batsMenData],
                  bowling: bowlersData,
                  fallOfWickets: [...fall_of_wicket],
                   name: team_details.name,
                  totalScores: team_details.score,
                   totalOvers:team_details.curr_over,
                  extras: team_details.extras,
                }
                scoreBoardCard('#scoreBoardSuperOverFirstInning', scoreBoardSuperOverFirstInning, scoreBoardSuperOverFirstInning.batting, scoreBoardSuperOverFirstInning.bowling, scoreBoardSuperOverFirstInning.fallOfWickets, scoreBoardSuperOverFirstInning.extras,scoreBoardSuperOverFirstInning.totalScores,scoreBoardSuperOverFirstInning.totalOvers);
              }
              if(inning == 3){
               const batsMenData = batting_data.length ? batting_data : scoreBoardSuperOverSecondInning.batting;
                  bowling_data = bowling_data ? [bowling_data] : null;
                 //const bowlersData = handlePresentBowlersData(scoreBoardFirstInning.bowling, bowling_data);
                  const bowlersData = bowling_data.length ? bowling_data.pop() : scoreBoardSuperOverSecondInning.bowling;
                  fall_of_wicket = fall_of_wicket.length ? fall_of_wicket : scoreBoardSuperOverSecondInning.fallOfWickets;
                scoreBoardSuperOverSecondInning =  {
                  ...scoreBoardSuperOverSecondInning,
                  batting: [...batsMenData],
                  bowling: bowlersData,
                  fallOfWickets: [...fall_of_wicket],
                   name: team_details.name,
                  totalScores: team_details.score,
                   totalOvers:team_details.curr_over,
                  extras: team_details.extras,
                }
                scoreBoardCard('#scoreBoardSuperOverSecondInning', scoreBoardSuperOverSecondInning, scoreBoardSuperOverSecondInning.batting, scoreBoardSuperOverSecondInning.bowling, scoreBoardSuperOverSecondInning.fallOfWickets, scoreBoardSuperOverSecondInning.extras,scoreBoardSuperOverSecondInning.totalScores,scoreBoardSuperOverSecondInning.totalOvers);
              }
              if(inning == 4){
               const batsMenData = batting_data.length ? batting_data : scoreBoardSecondSuperOverFirstInning.batting;
                  bowling_data = bowling_data ? [bowling_data] : null;
                 //const bowlersData = handlePresentBowlersData(scoreBoardFirstInning.bowling, bowling_data);
                  const bowlersData = bowling_data.length ? bowling_data.pop() : scoreBoardSecondSuperOverFirstInning.bowling;
                  fall_of_wicket = fall_of_wicket.length ? fall_of_wicket : scoreBoardSecondSuperOverFirstInning.fallOfWickets;
                scoreBoardSecondSuperOverFirstInning =  {
                  ...scoreBoardSecondSuperOverFirstInning,
                  batting: [...batsMenData],
                  bowling: bowlersData,
                  fallOfWickets: [...fall_of_wicket],
                   name: team_details.name,
                  totalScores: team_details.score,
                   totalOvers:team_details.curr_over,
                  extras: team_details.extras,
                }
                scoreBoardCard('#scoreBoardSecondSuperOverFirstInning', scoreBoardSecondSuperOverFirstInning, scoreBoardSecondSuperOverFirstInning.batting, scoreBoardSecondSuperOverFirstInning.bowling, scoreBoardSecondSuperOverFirstInning.fallOfWickets, scoreBoardSecondSuperOverFirstInning.extras,scoreBoardSecondSuperOverFirstInning.totalScores,scoreBoardSecondSuperOverFirstInning.totalOvers);
              }
              if(inning == 5){
                const batsMenData = batting_data.length ? batting_data : scoreBoardSecondSuperOverSecondInning.batting;
                  bowling_data = bowling_data ? [bowling_data] : null;
                  //const bowlersData = handlePresentBowlersData(scoreBoardFirstInning.bowling, bowling_data);
                  const bowlersData = bowling_data.length ? bowling_data.pop() : scoreBoardSecondSuperOverSecondInning.bowling;
                  fall_of_wicket = fall_of_wicket.length ? fall_of_wicket : scoreBoardSecondSuperOverSecondInning.fallOfWickets;
                scoreBoardSecondSuperOverSecondInning =  {
                  ...scoreBoardSecondSuperOverSecondInning,
                  batting: [...batsMenData],
                  bowling: bowlersData,
                  fallOfWickets: [...fall_of_wicket],
                   name: team_details.name,
                  totalScores: team_details.score,
                   totalOvers:team_details.curr_over,
                  extras: team_details.extras,
                }
                scoreBoardCard('#scoreBoardSecondSuperOverSecondInning', scoreBoardSecondSuperOverSecondInning, scoreBoardSecondSuperOverSecondInning.batting, scoreBoardSecondSuperOverSecondInning.bowling, scoreBoardSecondSuperOverSecondInning.fallOfWickets, scoreBoardSecondSuperOverSecondInning.extras,scoreBoardSecondSuperOverSecondInning.totalScores,scoreBoardSecondSuperOverSecondInning.totalOvers);
              }
            }
          });
        });
function summaryCard(containerName, data, summaryData, commentaryData, batsMen, bowler) {
  
   let inningBreakText = '';
        if (data.isFirstInningBatter&& !containerName.includes('superOver')) {
        inningBreakText = '<div class="inning-break"> First Innings Break </div>';
     }
  	 if (data.isSecondInningBatter && !containerName.includes('superOver')) {
       inningBreakText = '<div class="inning-break"> Second Innings Break </div>';
    }
    //   	if(data.isSuperOverFirstInningBatter) {
    //        inningBreakText = '<div class="inning-break">Super Over - First Innings Break</div>';
    //     }
    // if (data.isSuperOverSecondInningBatter && !containerName.includes('secondSuperOver')) {
    //     inningBreakText = '<div class="inning-break">Super Over - Second Innings Break</div>';
    // }
    // if (data.isSecondSuperOverFirstInningBatter) {
    //     inningBreakText = '<div class="inning-break">Second Super Over - First Innings Break</div>';
    // }
    // if (data.isSecondSuperOverSecondInningBatter) {
    //     inningBreakText = '<div class="inning-break">Second Super Over - Second Innings Break</div>';
    // }
 
    let commentaryTemplate = (currentBall, runs, commentary, balls, index,total) => {
    // Get the value to check from the reversed balls array
    const ballValue = balls.toReversed()[index];

    // Determine the class based on ballValue
    let runsClass = ballValue == 0 ? "zero" : ballValue == 4 ? "four" :ballValue == 'W' ? "wicket" : ballValue == 6 ? "six" : "other";

      //        let formattedCommentary = commentary;
//        const match = commentary.match(/([\w\s]+)\s+to\s+([\w\s]+)\s+-\s+.*?\n(.*)/i);

// if (match) {
//     const bowlerName = match[1].trim(); // Capture bowler's name
//     const batsmanName = match[2].trim(); // Capture batsman's name
//     const remainingText = match[3].trim(); // Extract and clean up the remaining text

//     // Update formattedCommentary based on ballValue
//     if (ballValue === "W") {
//         formattedCommentary = `${bowlerName} to ${batsmanName} - Wicket!<br>${remainingText}`;
//     } else {
//         formattedCommentary = `${bowlerName} to ${batsmanName} , ${ballValue} runs<br>${remainingText}`;
//     }
// }
let formattedCommentary = commentary;

// Regex to match bowler, batsman, and the rest of the commentary
const match = commentary.match(/([\w\s]+)\s+to\s+([\w\s]+)\s*,\s*(\d+|\w+).*?\n(.*)/i);

if (match) {
    const bowlerName = match[1].trim(); // Bowler's name
    const batsmanName = match[2].trim(); // Batsman's name
    const remainingText = match[4].trim(); // Commentary after newline

    // Update formattedCommentary based on ballValue
    if (ballValue === "W") {
        formattedCommentary = `${bowlerName} to ${batsmanName} - Wicket!<br>${remainingText}`;
    } else {
        formattedCommentary = `${bowlerName} to ${batsmanName}, ${ballValue} runs<br>${remainingText}`;
    }
}
      
    return `
        <div class="commentry-item">
            <div class="left">
                <p class="ballsnumber">${(Number(currentBall)).toFixed(1)}</p>
                <p class="runsperball ${runsClass}">${ballValue}</p>
            </div>
            <div class="right">
                ${formattedCommentary}
            </div>
        </div>

    `;
};

    let summaryTemplate = (over, balls, runs) => `
        <div class="oversummary">
            <div class="top">
                <div class="left">
                    <p>End of Over ${over}</p>
                </div>
                <div class="center">
                     <p>(${Array.isArray(balls) ? balls.join(' ') : balls.replace(/,/g, ' ')})</p>
                </div>
                <div class="right">
                    Runs: ${runs}
                </div>
            </div>
        </div>
    `;

    let batsmenTemplate = batsMen.map(batsman => `
          <div class="batsmen">
            <p class="battername"> ${batsman.name} ${batsman.is_striker ? '*' : ''}</p>
            <p class="score">${batsman.runs} <span class="balls score-ballss">(${batsman.balls})</span></p>
          </div>
    `).join('');

    let bowlerTemplate = `
          <div class="bowler d-flex justify-content-between">
            <p class="bowlername">${bowler.name}</p>
            <p class="overs">${bowler.wickets}-${bowler.runs}  (${Number(bowler.overs).toFixed(1)} ov)</p>

          </div>
    `;
//  <p>Economy  ${bowler.economy}</p>
    let container = document.querySelector(containerName);
    const batsmen_container = document.querySelector('#batsmen_container');
    const bowler_container = document.querySelector('#bowler_container');

    batsmen_container.innerHTML  = batsmenTemplate;
    bowler_container.innerHTML = bowlerTemplate;

    // Clear the container before adding new content
    container.innerHTML = '';
   container.innerHTML += inningBreakText;

     // Append batsman and bowler details
    // container.innerHTML += `
    //     <div class="players-summary">
    //         <div class="batsmen-container">
    //             <h3>Batsmen</h3>
    //             ${batsmenTemplate}
    //         </div>
    //         <div class="bowler-container">
    //             <h3>Bowler</h3>
    //             ${bowlerTemplate}
    //         </div>
    //     </div>
    // `;

    // Append summary and commentary
   summaryData.toReversed().forEach((summary) => {
    container.innerHTML += summaryTemplate(summary.over, summary.balls, summary.runs);

    let filteredCommentary = commentaryData.filter((comment) =>
        (((parseFloat((comment.over).split('.').shift()) + 1)) === summary.over &&
        (!comment.over.toString().includes('.0'))) ||
        (comment.over === `${summary.over}.0`));
     
  filteredCommentary.reverse().forEach((comment, i) => {
    container.innerHTML += commentaryTemplate(
        comment.over,
        comment.runs,
        comment.text,
        summary.balls,
        i,
        comment.total
    );
    
});
   
});





}
function setTeamScores(res) {
    const { batting_score, bowling_score, batting_team_id, bowling_team_id, curr_over, curr_run_rate, required_run_rate, match_status_text, batting_team_overs, bowling_team_overs } = res;

    function updateElementWithRetries(selector, value, retries = 5, interval = 500) {
        let attempts = 0;

        const tryUpdate = () => {
            const element = document.querySelector(selector);

            if (element) {
                element.innerHTML = `${value}`;
                console.log(`Updated value for ${selector}: ${value}`);
            } else if (attempts < retries) {
                attempts++;
                console.warn(`Retry ${attempts}/${retries} for ${selector}`);
                setTimeout(tryUpdate, interval);
            } else {
                console.error(`Failed to update ${selector} after ${retries} attempts.`);
            }
        };

        tryUpdate();
    }

    // Update Team 1
    if (batting_team_id == team1OverSummary.id) {
        // Team 1 is currently batting
        team1OverSummary.score = batting_score;
        team1OverSummary.over = batting_team_overs;
        updateElementWithRetries(`#team1-score-${team1OverSummary.id}`, batting_score);
        updateElementWithRetries(`#team1-over-${team1OverSummary.id}`, `(${batting_team_overs})`);
    } else if (bowling_team_id == team1OverSummary.id) {
        // Team 1 is bowling
        team1OverSummary.score = bowling_score;
        team1OverSummary.over = bowling_team_overs;
        updateElementWithRetries(`#team1-score-${team1OverSummary.id}`, bowling_score);
        updateElementWithRetries(`#team1-over-${team1OverSummary.id}`, `(${bowling_team_overs})`);
    }

    // Update Team 2
    if (batting_team_id == team2OverSummary.id) {
        // Team 2 is currently batting
        team2OverSummary.score = batting_score;
        team2OverSummary.over = batting_team_overs;
        updateElementWithRetries(`#team2-score-${team2OverSummary.id}`, batting_score);
        updateElementWithRetries(`#team2-over-${team2OverSummary.id}`, `(${batting_team_overs})`);
    } else if (bowling_team_id == team2OverSummary.id) {
        // Team 2 is bowling
        team2OverSummary.score = bowling_score;
        team2OverSummary.over = bowling_team_overs;
        updateElementWithRetries(`#team2-score-${team2OverSummary.id}`, bowling_score);
        updateElementWithRetries(`#team2-over-${team2OverSummary.id}`, `(${bowling_team_overs})`);
    }

    // Debugging logs
    console.warn('batting score => ', batting_score);
    console.warn('bowling score => ', bowling_score);
    console.warn('batting team overs => ', batting_team_overs);
    console.warn('bowling team overs => ', bowling_team_overs);
    console.warn('batting_team_id => ', batting_team_id);
    console.warn('bowling_team_id => ', bowling_team_id);
    console.warn('team_id 1 => ', team1OverSummary.id);
    console.warn('team_id 2 => ', team2OverSummary.id);

    // Updating match result
    const match_result = document.querySelector('#result_container');
    if (match_result) {
        match_result.innerHTML = match_status_text;
    }

    // Selecting the elements for current and required run rate containers
    const currRateContainer = document.querySelector('#curr_rate_container');
    if ((!curr_run_rate || curr_run_rate.trim() === "" ) && currRateContainer) {
        currRateContainer.style.display = 'none'; // Hide if 0.00
    } else if(currRateContainer) {
        currRateContainer.style.display = 'block'; // Show otherwise
        currRateContainer.innerHTML = `Current Run Rate: <strong>${curr_run_rate}</strong>`;
    }

    const reqRateContainer = document.querySelector('#req_rate_container');
    if (parseFloat(required_run_rate) === 0.00 && reqRateContainer) {
        reqRateContainer.style.display = 'none'; // Hide if 0.00
    } else if(reqRateContainer) {
        reqRateContainer.style.display = 'block'; // Show otherwise
        reqRateContainer.innerHTML = `Required Run Rate: <strong>${required_run_rate}</strong>`;
    }

}



function scoreBoardCard(containerName, data, batsMenData, bowlerData, fallOfWickets, extras,totalScores,totalOvers) {
    @php
    	 $matchStatus = isset($liveMatch) ? $liveMatch->status : null;
     @endphp

    let defaultText = '{{$matchStatus}}' ;
  	defaultText = defaultText == "Completed" ? "Not Out" : 'Batting';
    console.warn('matchSttasu',defaultText)
    const getDismissalDetails = (player) => {
      let dismissalType = player.dismissalType?.toString()?.toLowerCase();
      switch (dismissalType) {
        case 'out':
          return `Out by ${player.bowlerName}`;
        case 'caught':
          return `c ${player.fielderName} b ${player.bowlerName}`;
        case 'bowled':
          return `b ${player.bowlerName}`;
        case 'lbw':
          return `lbw b ${player.bowlerName}`;
        case 'stumped':
          return `st ${player.fielderName} b ${player.bowlerName}`;
        case 'runout':
          return `run out ${player.fielderName}`;
        case 'hit wicket':
          return `hit wicket b ${player.bowlerName}`;
        case 'retired hurt':
          return `retired hurt`;
        case 'run out (mankaded)':
          return `run out (mankaded) ${player.fielderName}`;
        case 'retired out':
          return `retired out`;
        case 'caught behind':
          return `c behind ${player.fielderName} b ${player.bowlerName}`;
        case 'caught & bowled':
          return `c & b ${player.bowlerName}`;
        default:
          return defaultText;
      }
    };
    let extrasTemplate = (data) => `
    <div class="scorecard-box two-col extras">
          <h6>Extras ${data.total}</h6>
          <div>
            <h5 class="extrarun"></h5>
            <p>(b ${data.byes}, lb ${data.legByes}, nb ${data.noBalls}, w ${data.wides}, p ${data.penaltyRuns})</p>
          </div>
        </div>
    `
      let totalsTemplate = (totalScores,totalOvers) => `
   <div class="scorecard-box two-col total border-0">
            <h5>Total</h5>
            <div>
              <p class="score">${totalScores}</p>
              <p class="over">(${totalOvers}) Overs</p>
            </div>
          </div>
    `
  let batsMenTemplate = (player) => `
      <div class="scorecard-box bodyz">
        <div class="scoreitem batsman">
          <p class="name">${player.name} ${player.isCaptain ? '(C)' : ''}${player.is_striker ? '*' : ''}</p>
          <p class="label"><label>${getDismissalDetails(player)}</Label></p>
        </div>
        <div class="scoreitem runs">
          <p>${player.runs}</p>
        </div>
        <div class="scoreitem balls">
          <p>${player.balls}</p>
        </div>
        <div class="scoreitem fours">${player.fours}</div>
        <div class="scoreitem sixs">${player.sixes}</div>
        <div class="scoreitem strikerate">${Number(player.strikeRate).toFixed(1)}</div>
      </div>
    `;
  let bowlersTemplate = (player) => `
      <div class="scorecard-box bodyz">
        <div class="scoreitem batsman">
          <p class="name">${player.name}</p>
        </div>
        <div class="scoreitem runs">
          <p>${Number(player.overs).toFixed(1)}</p>
        </div>
        <div class="scoreitem balls">
          <p>${player.maidens}</p>
        </div>
        <div class="scoreitem fours">${player.runs}</div>
        <div class="scoreitem sixs">${player.wickets}</div>
        <div class="scoreitem strikerate">${Number(player.economy).toFixed(1)}</div>
      </div>
    `;
  let fallOfWicketsTemplate = (player, index) => `
      <div class="scorecard-box bodyz"style="
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    margin-bottom: 5px;
">
      <div class="scoreitem batsman">
        <p class="name">${player.player}</p>
      </div>
      <div class="scoreitem scorez">${player.score}/${index + 1}</div>
      <div class="scoreitem overz">${player.over}</div>
    </div>
  `;

  let container = document.querySelector(containerName);
  container.innerHTML = `
<h5>
  ${containerName
 .toLowerCase()
 .replace('#scoreboard', '')
 .replace('firstinning', '')
  .replace('secondinning', '')
  .replace('superover', 'SuperOver')}
</h5>
    <div class="scorecard-wrap">
      <div class="top">
          <div class="scorecard-box headz">
            <div class="scoreitem batsman">Batsman</div>
            <div class="scoreitem runs">R</div>
            <div class="scoreitem balls">B</div>
            <div class="scoreitem fours">4s</div>
            <div class="scoreitem sixs">6s</div>
            <div class="scoreitem strikerate">S/R</div>
          </div>
  </div>
  `;
batsMenData.forEach((e) => {
  container.innerHTML += batsMenTemplate(e);
});

container.innerHTML += `
    ${extrasTemplate(extras)}
    ${totalsTemplate(totalScores,totalOvers)}
    <div class="center">
      <div class="scorecard-box headz">
        <div class="scoreitem batsman">Bowler</div>
        <div class="scoreitem runs">O</div>
        <div class="scoreitem balls">M</div>
        <div class="scoreitem fours">R</div>
        <div class="scoreitem sixs">W</div>
        <div class="scoreitem strikerate">Eco</div>
      </div>
    </div>
`;


bowlerData.forEach(e => {
  container.innerHTML += bowlersTemplate(e);
});
container.innerHTML += `
      </div>

      <div class="bottom">
        <div class="scorecard-box headz">
          <div class="scoreitem batsman">Fall of wickets</div>
          <div class="scoreitem scorez">Score</div>
          <div class="scoreitem overz">Over</div>
        </div>
`;

fallOfWickets.forEach((e, i) => {
  container.innerHTML += fallOfWicketsTemplate(e,i);
});



  container.innerHTML += `
          </div>
        </div>
  `;
}
const handlePresentBatsMenData = (prev, data, fallOfWickets)=> {
      if(!data || !data.length) {
        return [...prev]
      }
      let isOverAlreadyThere = false;
      prev?.forEach((e) => {
          if(data.some((v)=> v.id == e.id)){
            isOverAlreadyThere = true;
          }
      });
      if(!isOverAlreadyThere && data.length) {
        // console.warn('no existing over and data is there')
        // console.warn('prev => ', prev);
        // console.warn('data => ', data);
        return [...prev, ...data];
      }
      if(!isOverAlreadyThere && !prev?.length && !data.length) {
        // console.warn('no existing over, prev is empty and data is not there')
        // console.warn('prev => ', prev);
        // console.warn('data => ', data);
        return [...prev]
      }
      if(!isOverAlreadyThere && !prev?.length && data.length) {
        // console.warn('no existing over, prev is empty and data is there')
        // console.warn('prev => ', prev);
        // console.warn('data => ', data);
        return [...data]
      }
      let newPrev = prev.map((e, prevIndex) => {
        let isAlreadyThere = data?.find((v, i) => {
          if(e.id == v.id){
            return true;
          }
        });
        let isOut = fallOfWickets.find((v) => {
          if(e.id == v.id){
            return true;
          }
        });
        if(isOut)  {
          return {...e, dismissalType: isOut.dismissalType, bowlerName: isOut.bowlerName, fielderName: isOut.fielderName, is_striker: false,
                runs: isOut.runs, balls: isOut.balls, strikeRate: isOut.strikeRate, fours: isOut.fours, sixes: isOut.sixes};
        }
        if(isAlreadyThere) {
          return {...e,runs: isAlreadyThere.runs, balls: isAlreadyThere.balls, strikeRate: isAlreadyThere.strikeRate, fours: isAlreadyThere.fours, sixes: isAlreadyThere.sixes, is_striker: isAlreadyThere.is_striker};
        }
        return {...e};
        //return isAlreadyThere ? {...e,runs: isAlreadyThere.runs, balls: isAlreadyThere.balls, strikeRate: isAlreadyThere.strikeRate, fours: isAlreadyThere.fours, sixes: isAlreadyThere.sixes} : {...e};
      });
      let notAlreadyThere = [];
      data.forEach((v, i) => {
        let find = newPrev.some((e, eIndex) => e.id == v.id);
        if(!find) {
          notAlreadyThere.push(v);
        }
      });

      if(isOverAlreadyThere && newPrev && newPrev.length) {
        // console.warn('existing over, prev is not empty and data is there')
        // console.warn('prev => ', prev);
        // console.warn('data => ', data);
        // console.warn('newPrev => ', newPrev);
        // console.warn('notAlreadThere => ', notAlreadyThere);
        return [...newPrev, ...notAlreadyThere];
      }
      return [...prev]
    }
    const handlePresentBowlersData = (prev, data ) => {
      if(!data || !data.length) {
        return [...prev]
      }
      let isOverAlreadyThere = false;
      prev?.forEach((e) => {
         isOverAlreadyThere = data.some((v)=> v.id == e.id);
      });
      if(!isOverAlreadyThere && data.length) {
        // console.warn('no existing over and data is there')
        return [...prev, ...data];
      }
      if(!isOverAlreadyThere && !prev?.length && !data.length) {
        // console.warn('no existing over, prev is empty and data is not there')
        return [...prev]
      }
      if(!isOverAlreadyThere && !prev?.length && data.length) {
        // console.warn('no existing over, prev is empty and data is there')
        return [...data]
      }
      let newPrev = prev.map((e) => {
        let isAlreadyThere = data?.find((v) => {
          return v.id == e.id
        });
          return isAlreadyThere ? {...e, runs: isAlreadyThere.runs, maidens: isAlreadyThere.maidens, economy: isAlreadyThere.economy, overs: isAlreadyThere.overs, wickets: isAlreadyThere.wickets} : {...e};
      });
      if(isOverAlreadyThere && newPrev && newPrev.length) {
        // console.warn('existing over, prev is not empty and data is there')
        return [...newPrev];
      }
      return [...prev]
    }
    
    
</script>
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
 // Function to check if there are images inside the figure
    function checkImages() {
        const figure = document.getElementById('match-images-container');
        const h6 = document.getElementById('match-images-heading');

        // Check if there are any image elements inside the figure
        if (figure.getElementsByTagName('img').length > 0) {
            h6.style.display = 'block'; // Show the h6 element
        } else {
            h6.style.display = 'none'; // Hide the h6 element
        }
    }

    // Call the function initially to check for images
    checkImages();

    // Optionally, you can set up a MutationObserver to watch for changes in the figure
    const observer = new MutationObserver(checkImages);
    const config = { childList: true, subtree: true };
    observer.observe(document.getElementById('match-images-container'), config);
</script>
       
@endsection
