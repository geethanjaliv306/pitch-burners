<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pitch Burners</title>
    <link rel="icon" href="{{ asset('uploads/images/logo.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- <link rel="stylesheet" href="https://mdbgo.io/marta-szymanska/mdb5-demo-pro-design-blocks/css/mdb.min.css" />
    <link rel="stylesheet" href="https://mdbgo.io/marta-szymanska/mdb5-demo-pro-design-blocks/dev/css/new-prism.css" />--}}
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"  crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">
     <link href="{{ asset('css/style.css') }}?v=1.0.6" rel="stylesheet" type="text/css" />
  </head>
  <style>
    .has-filter-dropdown .dropdown .btn{
      border-radius:5px !important;
    }
    .has-filter-dropdown .dropdown .dropdown-menu{
       border-radius:5px !important;
        max-height: 300px;
    overflow-y: scroll;
    }
    @media (max-width: 565px){
      .has-filter-dropdown .dropdown .dropdown-menu{
         position: fixed !important;
    width: 100%;
    left: 0;
      right:0;
    transform: translate3d(0px, 186px, 0px) !important;
      }
}
    
    .has-filter-dropdown .dropdown .dropdown-menu li:not(:last-child){
    	border-bottom:1px solid rgba(204, 204, 204, .3);
    }
    .visit{
          color: rgba(255, 255, 255, 0.8)!important;
    text-decoration: none!important;
    font-size: 13px!important;
    position: relative!important;
      margin-right:0px!important;
          font-weight: 400 !important;
      font-family: unset !important;
    }
    .visit:after{
      content: "";
    height: 15px;
    width: 1px;
    background-color: rgba(255, 255, 255, 0.3);
    position: absolute;
    top: 0;
    bottom: 0;
    margin: auto;
    right: -8px;
    }
    .fa-users:before {
    font-size: 12px;
    color: rgb(254 254 254 / 80%);
    content: "\f0c0";
}
  </style>
   @php
          	$default_count = 155;
		 	$visitors_count = \App\Models\Visitor::count();
          @endphp
  <body class="addnewplayer-body">
    {{-- @if(request()->routeIs('coming-soon')) --}}
    <img class="bg-sticky left-position" src="{{ asset('uploads/images/batsman2.png') }}"/>
    <section class="small-header-top">
      <div class="container h-100">
        <div class="row h-100">
          <div class="col-12 h-100 d-flex align-items-center justify-content-center justify-content-md-end">
            {{-- <p>CIN:U85410TZ2023NPL029681</p> --}}
           <div class="contact-scroller d-md-none">
    <ul class="scroll-list">
      <li class="mail">
  <i class="fa-solid fa-users"></i>
        <p class="visit">Website Visitors: {{$default_count + $visitors_count}}</p>
    </li>
        <li class="mail">
            <i><img src="{{ asset('uploads/images/email.svg')}}" /></i>
            <a href="mailto:cricket@pitchburners.com">cricket@pitchburners.com</a>
        </li>
        <li class="phone">
            <i><img src="{{ asset('uploads/images/telephone.svg')}}" /></i>
            <a href="tel:+91 9962851516">+91 9962851516</a>
            <a href="tel:+91 9894568389">+91 9894568389</a>
        </li>
        <li class="insta">
            <i><img src="{{ asset('uploads/images/video.svg')}}" /></i>
            <a href="https://www.instagram.com/pitchburners_sports_foundation?igsh=bHhsdmtvbnJobjht" target="_blank">Pitchburners</a>
        </li>
    </ul> 
</div>

<!-- Normal List for Larger Screens -->
<ul class="d-none d-md-flex contact-list">
  <li class="mail">
    <i class="fa-solid fa-users"></i>
       <!-- <p class="visit">Website Visitors: {{$default_count + $visitors_count}}</p> -->
    	<p class="visit">Website Visitors: {{ $visitors_count}}</p>
    </li>
    <li class="mail">
        <i><img src="{{ asset('uploads/images/email.svg')}}" /></i>
        <a href="mailto:cricket@pitchburners.com">cricket@pitchburners.com</a>
    </li>
    <li class="phone">
        <i><img src="{{ asset('uploads/images/telephone.svg')}}" /></i>
        <a href="tel:+91 9962851516">+91 9962851516</a>
        <a href="tel:+91 9894568389">+91 9894568389</a>
    </li>
    <li class="insta">
        <i><img src="{{ asset('uploads/images/video.svg')}}" /></i>
        <a href="https://www.instagram.com/pitchburners_sports_foundation?igsh=bHhsdmtvbnJobjht" target="_blank">Pitchburners</a>
    </li>
</ul>
          </div>
        </div>
      </div>
    </section>
    {{-- @endif --}}
    <header class="fixed-header" >
      @if (!request()->is('privacy'))
      <div class="applinks desktopview">
          <a target="_blank" href="https://apps.apple.com/us/app/pitch-burners/id6740053781"><img src="{{ asset('uploads/images/app.svg')}}" /></a>
          <a target="_blank" href="https://play.google.com/store/apps/details?id=com.dsignzmedia.pitchBurnersCricketLeague"><img src="{{ asset('uploads/images/play.svg')}}" /></a>
      </div>
      @endif
      <nav class="navbar navbar-expand-xl h-100">
          <div class="container">
            {{-- @php
            $team_id = Auth::user()->team_id;
            $team = \App\Models\Team::where('id', $team_id)->first();
            $is_added = false;
            if(isset($team) && $team->is_added > 0) {
              $is_added = true;
            }
            @endphp --}}
            {{-- <a class="navbar-brand" href="{{ $is_added ? route('add-player') : route('index') }}"> --}}
                <a class="navbar-brand" href="{{ route('index') }}">
             <img width="250" src="{{ asset('uploads/images/pitchburners-new-logo1.png') }}" />
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                  {{-- @if(1 == 2 || @Auth::user()->role == 1) --}}
                <li class="nav-item">
                  <a  href="{{ route('teams.view') }}" class="nav-link {{ request()->routeIs('teams.view')|| request()->routeIs('teams.squad') ? 'active' : '' }}">Teams</a>
                </li>
                <li class="nav-item">
                  <a  href="{{ route('matches.view') }}" class="nav-link {{ request()->routeIs('matches.view')|| request()->routeIs('matches.view')|| request()->routeIs('matches.details') ? 'active' : '' }}">Matches</a>
                </li>
                @if(1 == 2 || @Auth::user()->role == 1)
                <li class="nav-item" style="display: none">
                  <a  href="{{ route('confirm-fixtures.view') }}" class="nav-link {{ request()->routeIs('confirm-fixtures.view')|| request()->routeIs('confirm-fixtures.view')|| request()->routeIs('matches.details') ? 'active' : '' }}">confirm Fixtures</a>
                </li>
                @endif
                <li class="nav-item">
                  <a  href="{{ route('standings') }}" class="nav-link {{ request()->routeIs('standings')|| request()->routeIs('standings') ? 'active' : '' }}">Standings</a>
                </li>
                <li class="nav-item">
                  <a  href="{{ route('venues') }}" class="nav-link {{ request()->routeIs('venues')? 'active' : '' }}">Venues</a>
                </li>
                <li class="nav-item">
                  <a  href="{{ route('stats') }}" class="nav-link {{ request()->routeIs('stats')? 'active' : '' }}">Stats</a>
                </li>
                <li class="nav-item">
                  <a  href="{{ route('rules') }}" class="nav-link {{ request()->routeIs('rules')? 'active' : '' }}">Rules</a>
                </li>
                <li class="nav-item">
                  <a  href="{{ route('about-us') }}" class="nav-link {{ request()->routeIs('about-us')? 'active' : '' }}">About Us</a>
                </li>
                <li class="nav-item">
                  <a  href="{{ route('gallery') }}" class="nav-link {{ request()->routeIs('gallery')? 'active' : '' }}">Gallery</a>
                </li>
                <li class="nav-item">
                  <a  href="{{ route('winners') }}" class="nav-link {{ request()->routeIs('winners')? 'active' : '' }}">Winners</a>
                </li>
                <li class="nav-item">
                  <a  href="{{ route('partners') }}" class="nav-link {{ request()->routeIs('partners')? 'active' : '' }}">Partners</a>
                </li>
                <li class="nav-item">
                  <a  href="{{ route('contact') }}" class="nav-link {{ request()->routeIs('contact')? 'active' : '' }}">Contact Us</a>
                </li>
                 {{-- @endif --}}
                @if (Auth::check())
                @else
                <li class="nav-item">
                  <a  href="{{ route('login') }}" class="nav-link {{ request()->routeIs('login')? 'active' : '' }}">Login</a>
                </li>
                @endif
              </ul>

            </div>
            @if (Auth::check())
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="user-dropdownMenu" data-bs-toggle="dropdown" aria-expanded="false">
                  {{ substr(Auth::user()->name, 0, 1) }}
                </button>
              <ul class="dropdown-menu user-dropdownMenu" aria-labelledby="user-dropdownMenu">
                  @if (Auth::check())

                      @if (Auth::user()->role == 1 )
                        <li><a class="dropdown-item" href="{{ route('index') }}">Go to Website</a></li>
                        <li><a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li><a class="dropdown-item" href="{{ route('tournaments-view') }}">My Tournaments</a></li>
                        <li><a class="dropdown-item" href="{{ route('tournaments') }}">Add a New Tournament</a></li>
                        <li><a class="dropdown-item" href="{{ route('organizer-members') }}">Organizer Admin</a></li>
                        <li><a class="dropdown-item" href="{{ route('total-teams') }}">Teams</a></li>
                        <li><a class="dropdown-item" href="{{ route('schedulematch') }}">Schedule Matches</a></li>
                        <li><a class="dropdown-item" href="{{ route('venues-admin') }}">Venues</a></li>
                      @endif

                      @if (Auth::user()->role == 0)
                        <li><a class="dropdown-item" href="{{ route('profile-edit') }}">Edit Profile</a></li>
                        <li><a class="dropdown-item" href="{{ route('add-player') }}">Add player</a></li>
                      @endif
                  @else
                      <li><a class="dropdown-item" href="{{ route('login') }}">Login</a></li>
                  @endif
                  <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">Logout</button>
                    </form>
                </li>
              </ul>
          </div>
          @endif
          </div>
        </nav>
  </header>

      @yield('content')
      @if(request()->routeIs('coming-soon'))
        <section class="our-sponsers">
          <div class="container">
            <div class="row">
              <div class="col-12">
                <h3 class="text-center mb-5">Our Sponsors</h3>
                <div class="marquee-slider">
                      @php
                      $partners = DB::table('partners')
                                        ->select('image')
                                        ->whereNull('deleted_at')
                                        ->get();
                      @endphp
                  @foreach($partners as $partner)
                          <div class="slide">
                              <img src="{{ config('constants.partners_url') . '/' . $partner->image }}" alt="Sponsor Image" />
                          </div>
                      @endforeach
                </div>
              </div>
            </div>
          </div>
        </section>
    @endif
    @if (!request()->is('privacy'))
       <section class="apps-wrap inner-page">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <h5>Tap or scan the image below to download our <strong>mobile app</strong> on your phone.</h5>
            <div class="app-deatils">
            <a target="_blank" href="https://apps.apple.com/us/app/pitch-burners/id6740053781"><img src="{{ asset('uploads/images/ios.png')}}" /></a>
            <a target="_blank" href="https://play.google.com/store/apps/details?id=com.dsignzmedia.pitchBurnersCricketLeague"><img src="{{ asset('uploads/images/andorid.png')}}" /></a>
          </div>

          </div>
        </div>
      </div>
      </section>
    @endif
        <footer>
            <div class="container h-100">
                <div class="row h-100">
                    <div class="col-12 h-100 text-center d-flex align-items-center justify-content-center">
                      <p><a target="_blank" href="{{route('terms')}}">Terms and Conditions </a></p>|
                      <p><a target="_blank" href="{{route('privacy')}}">Privacy Policy</a></p> |
                      <p>Copyright © CbePitch Burners Sports Foundation 2026 | Technology Partner <a class="link-dm" target="_blank" href="https://dsignzmedia.com/">dsignz media</a></p>
                    </div>
                </div>
            </div>
        </footer>
    @if (!request()->is('privacy'))
    <div class="footer-mobile-app">
      <p>Tap or Scan the image below to download our <strong>mobile app</strong> on your phone.</p>
      <div class="applinks">
        <a target="_blank" href="https://apps.apple.com/us/app/pitch-burners/id6740053781"><img src="{{ asset('uploads/images/app.svg')}}" /></a>
        <a target="_blank" href="https://play.google.com/store/apps/details?id=com.dsignzmedia.pitchBurnersCricketLeague"><img src="{{ asset('uploads/images/play.svg')}}" /></a>
      </div>
    </div>
    @endif
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"  crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"  crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

     <script>
      $(document).ready(function(){
        $( '#multiple-select-cityfield').select2( {
          width: '100%',
        } );
        $( '#multiple-select-groundfield' ).select2( {
          width: '100%',
        } );
      });
      </script>

  <script>$(document).ready(function(){
    $('.marquee-slider').slick({
      slidesToShow: 3,        // Number of items visible at a time
      slidesToScroll: 1,       // Number of items to scroll at a time
      autoplay: true,          // Enable auto-play
      autoplaySpeed: 0,        // Set speed for continuous scrolling
      speed: 5000,             // Speed of the scrolling (adjust as needed)
      cssEase: 'linear',       // Makes the scroll linear like a marquee
      infinite: true,          // Infinite loop
      arrows: false,           // Hide arrows if not needed
      variableWidth: true      // Adjust item width dynamically
    });
  });

</script>
    <script src="{{ asset('js/custom.js') }}" type="text/javascript"></script>
  </body>
</html>
