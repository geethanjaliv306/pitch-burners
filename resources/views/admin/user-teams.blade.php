@extends('layouts.app')

@section('content')
<style>
    .addPlayerModal input:not(input[type="checkbox"]), .addPlayerModal select {
        height: 45px;
    }
    .nameError, .emailError, .empidError, .phoneError, .batstyleError, .bowlStyleError, .roleError {
        color: red;
        font-size: 10px;
    }
    .our-sponsers{
        display: none;
    }
    .my-teams{
        font-size: 25px;
    font-family: "Saira", Arial, Helvetica, sans-serif;
    margin: 0;
    color: #fff;
    text-decoration: none;
    }
    .my-teams:hover{
        color: #fff;
    }
</style>
{{-- <body class="addnewplayer-body"> --}}
    <div class="alert-message" id="successMessage">
        Team players added successfully!
      </div>

    <section class="addnewplayer-title-wrap fixed-second-header">
        <i class="right-celebration"></i>
        <div class="container h-100">
            <div class="row h-100 d-flex align-items-center">
                <div class="col-12">
                    <div class="add-teamname-wrap">
                        <div class="addteam-logo d-flex align-items-center">
                            <figure style="overflow: hidden;">
                                <img src="{{ config('constants.upload_url') . '/team_logos/' . $team_logo->logo }}" alt="Team Logo" />
                            </figure>
                            <figcaption>
                                <h5>{{ $team->name }}</h5>
                            </figcaption>
                        </div>

                        <div class="addteam-button">
                            <a href="{{route('user-teams')}}" class="cta">My Team</a>
                            </div>
                            <div class="addteam-button">
                            <a href="{{route('user-tournaments')}}" class="cta">Tournaments</a>
                            </div>

                            <div class="addteam-button">
                                <a class="cta" href="{{route('add-player')}}">Add New Player</a>
                            </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <main class="main-wrapper-start addnewplay-main">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="player-lists-wrapper">
                        <div class="player-lists-info-header">
                            <div class="player-lists-info-head">
                                <div class="playersinfo sno head">S.No</div>
                                <div class="playersinfo profilepic head">Image</div>
                                <div class="playersinfo name head">Name</div>
                                <div class="playersinfo email head">Email ID</div>
                                <div class="playersinfo empid head">Employee ID</div>
                                <div class="playersinfo phoneno head">Phone No</div>
                                <div class="playersinfo batstyle head">Batting Style</div>
                                <div class="playersinfo bowlingstyle head">Bowling Style</div>
                                <div class="playersinfo role head">Role</div>
                            </div>
                        </div>
                        @foreach($players as $player)
                        <div class="player-lists-info">
                            <div class="player-lists-info-head">
                                <div class="playersinfo sno">{{ $loop->iteration }}</div>
                                <div class="playersinfo profilepic"><figure><img src="{{ config('constants.upload_url') . '/player_images/' . $player->image }}" alt="Player Image" /></figure></div>
                                <div class="playersinfo name">{{ $player->name }}</div>
                                <div class="playersinfo email">{{ $player->email }}</div>
                                <div class="playersinfo empid">{{ $player->empid }}</div>
                                <div class="playersinfo phoneno">{{ $player->phone }}</div>
                                <div class="playersinfo batstyle">{{ $player->batting_style }}</div>
                                <div class="playersinfo bowlingstyle">{{ $player->bowling_style }}</div>
                                <div class="playersinfo role">{{ $player->role }}</div>
                                {{-- <div class="playersinfo actions">
                                    <div class="playersinfoicon edit" onclick="openEditModal({{ json_encode($player) }})" data-bs-toggle="modal" data-bs-target="#addPlayerModal"
                                     @if($team_logo->is_added == 2)
                                        style="pointer-events: none; opacity: 0.9;"
                                    @endif>
                                        <span>Edit</span>
                                    </div>
                                        <button type="submit" class="playersinfoicon delete border-0" onclick="return confirm('Are you sure you want to delete this player?');"
                                        @if($team_logo->is_added == 2)
                                        style="pointer-events: none; opacity: 0.9;"
                                    @endif>
                                            <form action="{{ route('delete-player', $player->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                            <span>Delete</span>
                                            </form>
                                        </button>
                                </div> --}}
                            </div>
                        </div>
                        @endforeach
                        {{-- <div class="submit-section text-end mt-4">
                            <form action="{{ route('submit-team') }}" method="POST">
                                @csrf
                                <input type="hidden" name="team_id" value="{{ $team->team_id }}">
                                <button type="submit" class="btn btn-primary" id="submitTeamButton"
                                @if($players->count() < 11 || $team_logo->is_added == 2)
                                    disabled
                                @endif>
                                    Submit Team
                                </button>
                            </form>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
<script>
     @if(session('success'))
      document.getElementById('successMessage').style.display = 'block';
      setTimeout(function() {
          document.getElementById('successMessage').style.display = 'none';
      }, 3000);
  @endif
</script>
@endsection
