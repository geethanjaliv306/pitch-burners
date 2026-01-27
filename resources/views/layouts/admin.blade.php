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
    <link rel="stylesheet" href="https://mdbgo.io/marta-szymanska/mdb5-demo-pro-design-blocks/dev/css/new-prism.css" /> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"  crossorigin="anonymous">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" type="text/css" />
       <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<style>

      .sidebar{
        overflow: hidden;
      }
      .dropdown {
        position: relative;
      }
      
      .dropdown.d-block {
        display: block !important;
        max-height: 200px;
        overflow-y: auto;
      }
      
      .dropdown.d-block::-webkit-scrollbar {
        width: 1px;
      }
      
      .dropdown.d-block::-webkit-scrollbar-track {
        background: #7449D3;
        border-radius: 3px;
      }
      
      .dropdown.d-block::-webkit-scrollbar-thumb {
        background: #fff;
        border-radius: 3px;
      }

      .app-notification{
        padding-bottom: 8px
      }
    </style>
 
  </head>
  <body>
    {{-- <img class="bg-sticky left-position" src="{{ asset('uploads/images/batsman2.png') }}"/> --}}
    <header class="fixed-header">
        <i class="right-celebration"></i>
        <nav class="navbar navbar-expand-xl h-100">
            <div class="container-fluid">
              @if (Auth::check())
              @if (Auth::user()->role != 0)
              <a class="navbar-brand" href="{{ route('index') }}">
                <img width="250" src="{{ asset('uploads/images/pitchburners-new-logo1.png') }}" />
              </a>
              @endif
              @endif
              <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
              </button>
              <div class="collapse navbar-collapse adminSideNavbar" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto me-5 mb-2 mb-lg-0">
                  <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{route('dashboard')}}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('tournaments-view') ? 'active' : '' }}" href="{{route('tournaments-view')}}">My Tournaments</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('tournaments') ? 'active' : '' }}" href="{{route('tournaments')}}">Add Tournament</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('organizer-members') ? 'active' : '' }}" href="{{route('organizer-members')}}">Organizer Admin</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('total-teams') ? 'active' : '' }}" href="{{route('total-teams')}}">Teams</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link {{ request()->routeIs('schedulematch') ? 'active' : '' }}" href="{{route('schedulematch')}}">Schedule Matches</a>
              </li>
                <li class="nav-item">
                  <a class="nav-link {{ request()->routeIs('venues-admin') ? 'active' : '' }}" href="{{route('venues-admin')}}">Venues</a>
              </li>
			  <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('matches.statss') ? 'active' : '' }}" href="{{ route('matches.statss') }}">Matche stats</a>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="cmsDropdownNavbar" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  CMS
                </a>
                <ul class="dropdown-menu" aria-labelledby="cmsDropdownNavbar">
                  <li><a class="dropdown-item" href="{{route('cms-about')}}">About Us</a></li>
                  <li><a class="dropdown-item" href="{{route('cms-gallery')}}">Gallery</a></li>
                  <li><a class="dropdown-item" href="{{route('cms-winners')}}">Winners</a></li>
                  <li><a class="dropdown-item" href="{{route('cms-partners')}}">Partners</a></li>
                  <li><a class="dropdown-item" href="{{route('mail_content.edit')}}">Email</a></li>
                  <li><a class="dropdown-item" href="{{ route('app-notification-content.index') }}">App Notification</a></li>
                </ul>
              </li>
                </ul>
              </div>

              @if (Auth::check())
              <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="user-dropdownMenu" data-bs-toggle="dropdown" aria-expanded="false">
                  {{ substr(Auth::user()->name, 0, 1) }}
                </button>
                <ul class="dropdown-menu user-dropdownMenu" aria-labelledby="user-dropdownMenu">
                    @if (Auth::check())
                    @if (Auth::user()->role == 1 || Auth::user()->role == 2)
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
                        <li><a class="dropdown-item" href="{{ route('add-player') }}">Squads</a></li>
                    @endif
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    @else
                        <li><a class="dropdown-item" href="{{ route('login') }}">Login</a></li>
                    @endif
                </ul>
            </div>
            @endif
            </div>
          </nav>
    </header>

         <div class="sidebar" style="padding:10px 10px;">
        <ul class="">
          <li>
            <a class=" {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{route('dashboard')}}">Dashboard
            </a>
          </li>
          <li>
            <a class=" {{ request()->routeIs('tournaments-view')|| request()->routeIs('tournaments.show') || request()->routeIs('tournaments-teams') || request()->routeIs('tournaments-round') || request()->routeIs('tournaments-group') || request()->routeIs('tournaments.match')  ? 'active' : '' }}" href="{{route('tournaments-view')}}">My Tournaments</a>
          </li>
          <li>
            <a class="{{ request()->routeIs('tournaments') ? 'active' : '' }}" href="{{route('tournaments')}}">Add Tournament</a>
          </li>
          <li>
            <a class="{{ request()->routeIs('organizer-members')|| request()->routeIs('organizer-members.create')  ? 'active' : '' }}" href="{{route('organizer-members')}}">Organizer Admin</a>
          </li>
          <li>
            <a class="{{ request()->routeIs('total-teams') || request()->routeIs('not-applied-teams') || request()->routeIs('teamplayers') ? 'active' : '' }}" href="{{route('total-teams')}}">Teams</a>
          </li>
          <li>
            <a class="{{ request()->routeIs('schedulematch') ? 'active' : '' }}" href="{{route('schedulematch')}}">Schedule Matches</a>
          </li>
          <li>
            <a class="{{ request()->routeIs('venues-admin')|| request()->routeIs('venues-admin.create')  ? 'active' : '' }}" href="{{route('venues-admin')}}">Venues</a>
          </li>
		<li class="nav-item">
            <a class="{{ request()->routeIs('matches.statss') ? 'active' : '' }}" href="{{ route('matches.statss') }}">Match stats</a>
          </li>
         <li>
            <a href="#" onclick="toggleDropdown('cmsDropdown')" class="d-flex align-items-center justify-content-between">CMS <i class="fa-solid fa-chevron-down"></i></a>
            <ul id="cmsDropdown"
                class="dropdown {{ request()->routeIs('cms-about', 'cms-gallery', 'cms-winners', 'cms-partners', 'mail_content.edit', 'app-notification-content.index') ? 'd-block' : 'd-none' }}">
              <li>
                <a class="{{ request()->routeIs('cms-about') ? 'active' : '' }}" href="{{ route('cms-about') }}">About Us</a>
              </li>
              <li >
                <a class="{{ request()->routeIs('cms-gallery') ? 'active' : '' }}" href="{{ route('cms-gallery') }}">Gallery</a>
              </li>
              <li >
                <a class="{{ request()->routeIs('cms-winners') ? 'active' : '' }}" href="{{ route('cms-winners') }}">Winners</a>
              </li>
              <li >
                <a class="{{ request()->routeIs('cms-partners') ? 'active' : '' }}" href="{{ route('cms-partners') }}">Partners</a>
              </li>
              <li >
                <a class="{{ request()->routeIs('mail_content.edit') ? 'active' : '' }}" href="{{ route('mail_content.edit') }}">Email</a>
              </li>
              <li>
                <a class="{{ request()->routeIs('app-notification-content.index') ? 'active' : 'app-notification' }}" 
                   href="{{ route('app-notification-content.index') }}">
                    App Notification
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </div>
      <div class="container-fluid next-to-sidebar">

       @yield('content')

      </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"  crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"  crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
     <script>
      $(document).ready(function(){
        $( '#multiple-select-cityfield').select2( {
          width: '100%',
        } );
        $( '#multiple-select-groundfield' ).select2( {
          width: '100%',
        } );
      });

      function toggleDropdown(id) {
        var dropdown = document.getElementById(id);
        if (dropdown.classList.contains("d-none")) {
          dropdown.classList.remove("d-none");
          dropdown.classList.add("d-block");
        } else {
          dropdown.classList.remove("d-block");
          dropdown.classList.add("d-none");
        }
      }
        </script>
    <script src="{{ asset('js/custom.js') }}" type="text/javascript"></script>
  </body>
</html>
