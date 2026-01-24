@extends('layouts.app')

@section('content')
<!-- Option 1: Include in HTML -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
<style>
    :root {
    --primary-color: #008F7A;
    --secondary-color: #23004b;
    --accent-color: #F2C01F;
    --deep-purple: rgba(58, 21, 120, 1);
}
  .our-sponsers {
    display: none;
  }
  .filter-tournament{
    width: auto;
    background: unset;
    color: white;
     border-radius: 5px;
  }
   .form-select:focus{
    box-shadow: none;
  }
  a{
    text-decoration: none;
    color: unset;
  }
  a:hover{
    color: unset;
  }
  .alert-message {
        position: fixed;
        top: 10px;
        right: 10px;
        background-color: #4caf50;
        color: white;
        padding: 15px;
        border-radius: 4px;
        z-index: 9999;
        display: none; /* Initially hidden */
    }
    .bi-caret-down::before {
    content: "\f22c";
    color: white;
}
.no-tour{
   /* min-height: 150px; */
}
  
  .pitch-burners-home.live-scores-wrap{
    min-height: calc(100vh - 475px);
  }
.app-deatils {
    display: flex;
    align-items: center;
    justify-content: center;
}
.apps-wrap img{
width: 300px;
}
  .apps-wrap.inner-page{
    display:none;
  }
  
    .mobile-store-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    animation: fadeIn 0.3s ease-in-out;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    position: relative;
    background-color: #fff;
    width: 90%;
    max-width: 400px;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.close-btn {
    position: absolute;
    right: -12px;
    top: -12px;
    font-size: 20px;
    cursor: pointer;
    color: var(--secondary-color);
   	z-index: 10000; 
    background-color: #ddd;
    border-radius: 50%;
    width:32px;
    height:32px;
    text-align:center;
  	margin:0
}

.modal-body {
    text-align: center;
}

.modal-body h4 {
    color: var(--primary-color);
    margin-bottom: 10px;
    font-size: 1.5rem;
    font-family: "Saira", Arial, Helvetica, sans-serif;

}

.modal-body p {
    color: var(--secondary-color);
    margin-bottom: 20px;
    font-family: "Saira", Arial, Helvetica, sans-serif;

}

.store-buttons {
    display: flex;
    justify-content: center;
    gap: 15px;
    flex-wrap: wrap;
}

.store-img {
    max-width: 160px;
    height: auto;
}
.client-logos {
    background: #fff;
    overflow: hidden;
   /* border-top: 1px solid #eee;*/
    border-bottom: 1px solid #eee;
}

.logo-slider {
    position: relative;
    width: 100%;
    overflow: hidden;
}

.logo-track {
    display: flex;
    width: max-content;
    animation: scrollLogos 28s linear infinite;
}

.logo-slider:hover .logo-track {
    animation-play-state: paused;
}

.logo-item {
    flex: 0 0 auto;
    padding: 0 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.logo-item img {
    max-height: 110px;
    width: auto;
    /* filter: grayscale(100%); */
    /* opacity: 0.7; */
    transition: all 0.3s ease;
}

.logo-item img:hover {
    /* filter: grayscale(0%); */
    /* opacity: 1; */
    transform: scale(1.03);
}

@keyframes scrollLogos {
    from {
        transform: translateX(0);
    }
    to {
        transform: translateX(-50%);
    }
}


@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@media (min-width: 992px) {
    .mobile-store-modal {
        display: none !important;
    }
}

      .livescore-box-item{
        color:black;
  }
   .scores {
    font-size: 25px;
    font-weight: 600;
     color: rgb(120, 120, 120);
}
  .livescore-box-item .centre .team .right .overs {
    color: #9E9E9E;
    font-size: 16px;
}
  @media (max-width: 767.98px) {
    .livescore-box-item .bottom p{ 
      text-align: center;
    }
     .livescore-box-item .centre .team .right .overs {
   white-space: nowrap;
}
}
 .completed-live{
   background-color:#ccc;
   border-radius: 4px;
    display: -webkit-box;
    display: -moz-box;
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
    -webkit-align-items: center;
    -moz-align-items: center;
    -ms-align-items: center;
    align-items: center;
    -webkit-justify-content: center;
    -moz-justify-content: center;
    -ms-justify-content: center;
    justify-content: center;
    -ms-flex-pack: center;
    height: 25px;
    padding: 0 10px;
    font-size: 13px;
   color:#333;
  }
  .upcoming-live{
   background-color:#009688;
   border-radius: 4px;
    display: -webkit-box;
    display: -moz-box;
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
    -webkit-align-items: center;
    -moz-align-items: center;
    -ms-align-items: center;
    align-items: center;
    -webkit-justify-content: center;
    -moz-justify-content: center;
    -ms-justify-content: center;
    justify-content: center;
    -ms-flex-pack: center;
    height: 25px;
    padding: 0 10px;
    font-size: 13px;
   color:#fff;
  }
  .pitch-burners-home-filter ul li a.active:after{
    background: linear-gradient(242.58deg, #9D3ADF 0.9%, #8542D8 21.58%, #4857C6 64.98%, #106AB6 101.25%);
    height: 45px;
    top: 0;
    bottom: 0;
    margin: auto;
    border-radius: 4px;
    transform: skewX(-20deg);
  }
  .pitch-burners-home-filter ul li a.active span{
    position: relative;
    z-index: 1;
color: #fff; 
  }
  .partners-title {
    text-align: center;
    font-size: 50px;
    font-weight: 700;
    color: #008E9B;
    background: linear-gradient(242.58deg, #9D3ADF 0.9%, #FF5722 21.58%, #106AB6 64.98%, #106AB6 101.25%) text;
    -webkit-text-fill-color: transparent;
}
  @media (max-width: 768px){
.pitch-burners-home-filter ul{
    justify-content: center;
    width: 100%;
}
     .footer-mobile-app{
    position:relative;
    }
        .logo-item {
        padding: 0 20px;
    }

    .logo-item img {
        max-height: 40px;
    }
        .partners-title {
        font-size: 26px;
    }
}
  .result-details{
    color: #4CAF50;
    font-size: 16px;
    font-weight: 500;
    font-family: "Saira", Arial, Helvetica, sans-serif;
  }
  .ground{
    max-width: 160px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
  }
  .group_matches {
    font-family: "Saira", Arial, Helvetica, sans-serif;
    color: #607D8B !important;
    font-weight: 700;
}
   .win-team .right .scores ,.win-team .right .overs   {
    color: #042F44 !important;
}
.win-trophy {
    width: 25px;
    position: relative;
    top: -5px;
}
   @media (max-width: 767.98px){
     .win-trophy {
       display:none;
     }
     .view-upcoming{
       font-size:16px !important;
     }
     .no-team{
       line-height:35px;
     }
    
  }
   @media (max-width: 525px){
    
     .index-time {
       gap:5px;
     }
     .date{
       font-size:14px;
     }
  }
  .visitors {
    text-align: center; color: #fff;
    display: flex;
    flex-flow: row;
    justify-content:flex-start;
    align-items: center;
    width: 33.33%;
    margin: 0px auto;
    margin-top: 3rem;
  }
  #visitors_count {
    margin-left: 10px;
  }
  .view-upcoming{
    border: #614092 solid 1px;
    font-size: 20px;
    background-color: #f6efff;
    padding: 10px 20px;
    color: #614092;
    text-align: center;
    font-family: 'Saira';
    font-weight: 500;
    border-radius:10px;
    display: inline-block;
  }
  .view-upcoming:hover{
    background-color:#614092;
    color:#fff;
  }
  .no-matches{
      display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
  }
  
  .trophy-banner-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        z-index: 9999;
        animation: fadeIn 0.3s ease-in-out;
        align-items: center;
        justify-content: center;
    }

    .trophy-modal {
        background-color: transparent;
        max-width: calc(100% - 500px);
        width: 90%;
        padding: 0;
        border-radius: 12px;
    }

    .trophy-close-btn {
        right: -12px;
        top: -12px;
        background-color: #ddd;
        color: var(--secondary-color);
    }

    .trophy-modal-body {
        padding: 0;
    }

    .trophy-banner-img {
        width: 100%;
        height: auto;
        display: block;
        border-radius: 10px;
    }

    @media (max-width: 767px) {
        .trophy-modal {
            width: 95%;
          	max-width: 100%;
        }
    }
  .pitch-burners-info h4{
    font-size: 40px;
}
</style>


<section class="pitch-burners-banner ">
  <img class="pitch-burners-banner-figure" src="{{ asset('uploads/images/drawing-baseball-player-with-bat-word-cricket-it.jpg') }}" />
 <div class="container h-100 py-2 d-flex align-items-center">
    <div class="row">
      <div class="col-12">
        @if ($selectedTournament)
        <div class="pitch-burners-info">
          <i class="icon"><img width="200" src="{{ asset('uploads/images/logo.png') }}" /></i>
          <h4 class="mb-lg-3 mb-md-2 mb-sm-2 mb-2">{{ $selectedTournament->name }}</h4>
          <ul class="mb-lg-3 mb-md-2 mb-sm-2 mb-2">
            <li><label class="ttencricket">{{ $selectedTournament->ball_type }}</label></li>
            <li>{{ \Carbon\Carbon::parse($selectedTournament->start_date)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($selectedTournament->end_date)->format('M d, Y') }}</li>
           {{-- <li>96 Teams</li> --}}
          </ul>
        </div>

        <!-- Dropdown to select tournament -->
        <!-- <div class="container mb-lg-1 mb-md-0 mb-sm-0 p-0">
         <form id="filterForm" action="{{ url('/') }}" method="GET">
    <div class="form-group">
        <label for="tournament" class="text-white mb-2">Select Tournament : </label>
        <select name="tournament_id" id="tournament" class="form-select form-control filter-tournament" onchange="this.form.submit()">
            @foreach($tournaments as $tournament)
                <option value="{{ $tournament->id }}" {{ $tournament->id == $selectedTournament->id ? 'selected' : '' }}>
                    {{ $tournament->name }}
                </option>
            @endforeach
        </select>
    </div>
</form>

        </div>  -->
      </div>
    </div>
  </div>
</section>


<section class="live-scores-wrap pitch-burners-home d-none">
  <div class="pitch-burners-home-filter">
    <div class="container h-100">
      <div class="row h-100">
        <div class="col-12 h-100 d-flex align-items-center">
          <ul>
            <li>
                <a id="liveTab" class="{{ $matchStatus === 'Active' ? 'active' : '' }}" href="javascript:;" onclick="filterMatches('Active')"><span>Live</span></a>
            </li>
            <li>
                <a id="completedTab" class="{{ $matchStatus === 'Completed' ? 'active' : '' }}" href="javascript:;" onclick="filterMatches('Completed')"><span>Completed</span></a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="row">
      <div class="col-12">
        @if(count($schedule_matches) > 0)
          <div class="livescore-box">
            @foreach ($schedule_matches as $schedule_match)
              <div class="livescore-box-item">
                <a href="{{ route('matches.details', ['id' => $schedule_match->id]) }}">
                  <div class="top">
                  @php
    if ($schedule_match->match_status === 'Active') {
        $liveStatus = 'Live';
        $statusClass = 'live'; // Default class for live matches
    } elseif ($schedule_match->match_status === 'Completed') {
        $liveStatus = 'Completed';
        $statusClass = 'completed-live'; // Custom class for completed matches
    } elseif ($schedule_match->match_status === 'Canceled') {
        $liveStatus = 'Cancelled';
        $statusClass = 'completed-live'; // Custom class for canceled matches
    } else {
        $liveStatus = 'Upcoming';
        $statusClass = 'upcoming-live'; // Optional class for upcoming matches
    }
@endphp

<div class="index-time">
    <div class="{{ $statusClass }}">{{ $liveStatus }}</div>
                    <div class="date">
    {{ \Carbon\Carbon::parse($schedule_match->match_date_time)->format('M j, D | h:i A') }} | <span class="group_matches">{{$schedule_match->group_name}}</span>
</div> 
                  </div>
                    <div class="ground"><img width="22" height="22" src="/uploads/images/fi_2875433.svg">{{ $schedule_match->ground_name }}</div>
                     
                  </div>
                 <div class="centre">
                    @php
        $teamOneScore = DB::table('match_scores')
        ->where('match_id', $schedule_match->match_id)
        ->where('team_id', $schedule_match->team1)
        ->first();

    $teamTwoScore = DB::table('match_scores')
        ->where('match_id', $schedule_match->match_id)
        ->where('team_id', $schedule_match->team2)
        ->first();

    // Check if there is a winning team
    $winningTeam = DB::table('match_scores')
        ->where('match_id', $schedule_match->match_id)
        ->whereNotNull('is_winning')
        ->value('is_winning');
                   
    $isTeam1Winner = ($winningTeam == $schedule_match->team1);
    $isTeam2Winner = ($winningTeam == $schedule_match->team2);
                   
    @endphp
                    <div class="team indicater {{ $isTeam1Winner ? 'win-team' : '' }}">
                        <div class="left">
                          
                            <i>
                                <img 
                                     src="{{ config('constants.upload_url') . '/team_logos/' . $schedule_match->team1_logo }}" 
                                     alt="Team 1 Logo" />
                            </i>
                            {{ $schedule_match->match_team1_name ?? $schedule_match->scheduled_team1_name }}
                        </div>
                        <div class="right">
                             @if ($schedule_match->match_status === 'Active' || $schedule_match->match_status === 'Completed' || $schedule_match->match_status === 'Canceled')
                           
                            <div class="scores">
                               @if($isTeam1Winner)
            <img src="{{ asset('uploads/images/trophy.svg') }}" alt="Winner" class="win-trophy">
        @endif {{ $schedule_match->team1Score ?? 0 }}/{{ $schedule_match->team1Wickets ?? 0 }}
                            </div>
                           <div class="overs">({{$schedule_match->team1Overs ?? 0}} ov)</div>
                             @endif
                        </div>
                    </div>
                     <div class="team-divider">
                     <img width="42" height="42" src="/uploads/images/fi_9359727.svg">
                   </div>
                    <div class="team {{ $isTeam2Winner ? 'win-team' : '' }}">
                        <div class="left">
                            <i>
                                <img 
                                     src="{{ config('constants.upload_url') . '/team_logos/' . $schedule_match->team2_logo }}" 
                                     alt="Team 2 Logo" />
                            </i>
                            {{ $schedule_match->match_team2_name ?? $schedule_match->scheduled_team2_name }}
                        </div>
                        <div class="right">
                             @if ($schedule_match->match_status === 'Active' || $schedule_match->match_status === 'Completed' || $schedule_match->match_status === 'Canceled')
                            
                            <div class="scores">
                                @if($isTeam2Winner)
            <img src="{{ asset('uploads/images/trophy.svg') }}" alt="Winner" class="win-trophy">
        @endif {{ $schedule_match->team2Score ?? 0 }}/{{ $schedule_match->team2Wickets ?? 0 }}
                            </div>
                           <div class="overs">({{$schedule_match->team2Overs ?? 0}} ov)</div>
                             @endif
                        </div>
                    </div>
                </div>

                  <div class="bottom">
                    <p>
                          @if($schedule_match->match_status === 'Completed')
                           <span class="result-details">{{ $schedule_match->match_result ?? 'Match result not available.' }}</span>
                           @elseif($schedule_match->match_status === 'Canceled')
                            <span class="result-details">{{ $schedule_match->match_result ?? 'Match result not available.' }}</span>
                         @elseif($schedule_match->toss_win)
                            <strong>{{ $schedule_match->toss_win }}</strong> won the toss and chose to
                            <strong>{{ $schedule_match->batting_team_name == $schedule_match->toss ? 'Bat' : 'Bowl' }}</strong> first.
                         @else
                            Match not started yet
                         @endif
                    </p>
                  </div>
                </a>
              </div>
            @endforeach
          </div>
        @else
          <div class="no-matches">
              <p class="no-team">
                  No {{ $matchStatus === 'Completed' ? 'completed' : 'live' }} Matches Right Now. 
                  Check Back Soon for the Action!
              </p>
             <a href="{{route('matches.view')}}" class="view-upcoming">Click here to view upcoming matches</a>
          </div>
        @endif
      </div>
    </div>
  </div>
</section>
<section class="about-picth-burners">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <h3>We are Pitch Burners</h3>
            <p>Cricket is a very popular game and is played at both national and international levels. We organize corporate tournament every year since 2018. We have conducted the cricket tournament last year in nov, 2018 It was a great success. As an encouragement to participants we sponsored Bat and refreshments to the teams during the tournament. As our first milestone, we started conducting cricket tournaments for corporate in Coimbatore.</p>
            <a class="home-learnmore" href="{{route('about-us')}}">Learn More</a>
          </div>
        </div>
      </div>
    </section>
<!-- Auto Scrolling Logos Section -->
<section class="client-logos py-4">
      <div class="container">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h3 class="partners-title">Our Partners</h3>
            </div>
        </div>
    </div>
    <div class="logo-slider">
        <div class="logo-track">

            {{-- SET 1 --}}
            @foreach($partners as $partner)
                <a 
                    href="{{ $partner->link ? $partner->link : 'javascript:void(0)' }}"
                    target="{{ $partner->link ? '_blank' : '_self' }}"
                    class="logo-item"
                >
                    <img 
                        src="{{ config('constants.upload_url') . '/partners/' . $partner->image }}"
                        alt="{{ $partner->name ?? 'Partner Logo' }}"
                    >
                </a>
            @endforeach

            {{-- SET 2 (DUPLICATE FOR INFINITE LOOP) --}}
            @foreach($partners as $partner)
                <a 
                    href="{{ $partner->link ? $partner->link : 'javascript:void(0)' }}"
                    target="{{ $partner->link ? '_blank' : '_self' }}"
                    class="logo-item"
                >
                    <img 
                        src="{{ config('constants.upload_url') . '/partners/' . $partner->image }}"
                        alt="{{ $partner->name ?? 'Partner Logo' }}"
                    >
                </a>
            @endforeach
        </div>
    </div>
</section>


<section class="apps-wrap">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <h5>Get the official Pitch Burners Mobile App</h5>
<p>Stay updated with the latest match scores and highlights on the go. Get all the action at your fingertips by downloading the official Pitch Burners app.</p>
<p>Available on both App store and Playstore.</p>
          <div class="app-deatils">
            <a target="_blank" href="https://play.google.com/store/apps/details?id=com.dsignzmedia.pitchBurnersCricketLeague"><img src="uploads/images/andorid.png" /></a>
            <a target="_blank" href="https://apps.apple.com/us/app/pitch-burners/id6740053781"><img src="uploads/images/ios.png" /></a>
          </div>
          @php
          	$default_count = 250;
		 	$visitors_count = \App\Models\Visitor::count();
          @endphp
          {{--<h4 class="visitors">Website Visitors: <span id="visitors_count" style="transistion .3s ease;">{{$default_count + $visitors_count}}</span></h4>--}}
        </div>
      </div>
    </div>
   </section>
@else
<div class="container no-tour" style="margin-top:150px;">
  <div class="alert alert-info">
    <p class="m-0">No tournaments available. Please check back later.</p>
  </div>
</div>
@endif
    <div class="mobile-store-modal d-none" id="storeModal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <div class="modal-body">
            <h4>Get Our Mobile App</h4>
            <p>Download our app for a better experience</p>
            <div class="store-buttons">
                <a href="https://apps.apple.com/us/app/pitch-burners/id6740053781" class="store-link">
                    <img src="{{ asset('uploads/images/appstore.svg') }}" alt="Download on App Store" class="store-img">
                </a>
                <a href="https://play.google.com/store/apps/details?id=com.dsignzmedia.pitchBurnersCricketLeague" class="store-link">
                    <img src="{{ asset('uploads/images/playstore.svg') }}" alt="Get it on Play Store" class="store-img">
                </a>
            </div>
        </div>
    </div>
</div>

<div class="trophy-banner-modal d-none" id="trophyBannerModal">
  <div class="modal-content trophy-modal">
      <span class="close-btn trophy-close-btn">&times;</span>
      <div class="modal-body trophy-modal-body">
        <img src="https://pitchburners.com/uploads/images/PBCCL%20-%20Banner.jpg" alt="Team won the final" class="trophy-banner-img">
      </div>
  </div>
</div>

<script>
  @if(session('success'))
      document.getElementById('successMessage').style.display = 'block';
      setTimeout(function() {
          document.getElementById('successMessage').style.display = 'none';
      }, 3000);
  @endif

  function filterMatches(status) {
    document.getElementById('liveTab').classList.remove('active');
    document.getElementById('completedTab').classList.remove('active');
    document.getElementById(status === 'Active' ? 'liveTab' : 'completedTab').classList.add('active');
    document.getElementById('matchStatus').value = status;
    document.getElementById('filterForm').submit();
  }
  
    document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('storeModal');
    const closeBtn = document.querySelector('.close-btn');
    const HOUR_IN_MS = 3600000;
    const MODAL_SHOWN_KEY = 'modalLastShown';
    
    function showModal() {
        if (window.innerWidth < 992) {
            modal.style.display = 'flex';
          	
            localStorage.setItem(MODAL_SHOWN_KEY, Date.now().toString());
        }
    }

    function hideModal() {
        modal.style.display = 'none';
      
    }

    function shouldShowModal() {
        const lastShown = localStorage.getItem(MODAL_SHOWN_KEY);
        if (!lastShown) return true;
        
        const timeDiff = Date.now() - parseInt(lastShown);
        return timeDiff >= HOUR_IN_MS;
    }

    if (shouldShowModal()) {
        showModal();
    }

    setInterval(() => {
        if (shouldShowModal()) {
            showModal();
        }
    }, 60000);

     if (closeBtn) {
        closeBtn.addEventListener('click', function () {
            console.log("Close button clicked"); // For debugging
            hideModal();
        });
    }

    // Close modal if clicking outside the modal content
    window.addEventListener('click', function (event) {
        if (event.target === modal) {
            hideModal();
        }
    });
});
  //const visitors = document.querySelector('#visitors_count');
  //const visitorsCount = Number(visitors?.textContent);
  
  //visitors.style.marginLeft = visitorsCount > 999 ? '15px' : '10px';
                               
  //let delay = 0;           
  //let animationCount = 0;
  //let visitorsTimeout = setTimeout(countAnimation, delay);
  
  //function countAnimation() {
    //if(animationCount == visitorsCount) {
      //clearTimeout(visitorsTimeout);
      //return;
    //}
	//visitors.textContent = ++animationCount;
    
    //visitorsTimeout = setTimeout(countAnimation, delay);
  //}
  
  document.addEventListener('DOMContentLoaded', function() {
  function showTrophyBanner() {
      document.getElementById('trophyBannerModal').style.display = 'flex';
  }
  
  function hideTrophyBanner() {
      document.getElementById('trophyBannerModal').style.display = 'none';
  }

  if (window.location.pathname === '/' || window.location.pathname === '') {
      showTrophyBanner(); 
  }
  
  const trophyCloseBtn = document.querySelector('.trophy-close-btn');
  if (trophyCloseBtn) {
      trophyCloseBtn.addEventListener('click', function() {
          hideTrophyBanner();
      });
  }
  
  const trophyModal = document.getElementById('trophyBannerModal');
  if (trophyModal) {
      trophyModal.addEventListener('click', function(event) {
          if (event.target === trophyModal) {
              hideTrophyBanner();
          }
      });
  }
});
document.querySelectorAll('.logo-item img').forEach(logo => {
    logo.addEventListener('mouseenter', () => {
        document.querySelector('.logo-track').style.animationPlayState = 'paused';
    });

    logo.addEventListener('mouseleave', () => {
        document.querySelector('.logo-track').style.animationPlayState = 'running';
    });
});

</script>

@endsection
