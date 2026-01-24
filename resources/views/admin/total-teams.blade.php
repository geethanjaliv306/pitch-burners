@extends('layouts.admin')

@section('content')
<style>
    .switch {
    position: relative;
    display: inline-block;
    width: 34px;
    height: 20px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
}

.slider:before {
    position: absolute;
    content: "";
    height: 14px;
    width: 14px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
}

input:checked + .slider {
    background-color: #2196F3;
}

input:focus + .slider {
    box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
    transform: translateX(14px);
}

.slider.round {
    border-radius: 34px;
}

.slider.round:before {
    border-radius: 50%;
}
#resetButton{
    margin:0px;
    height:40px;

}
  .btn-info {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    line-height: 1.5;
    border-radius: 0.2rem;
    color: #fff !important;
    background-color: #17a2b8;
    border-color: #17a2b8;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    width: auto;
}

.btn-info:hover {
    background-color: #138496;
    color: #fff !important;
}
</style>
<div class="alert-message" id="successMessage">
     {{ session('success') }}
  </div>
<section class="my-torunments-second-header fixed-second-header">
    <div class="container h-100">
        <div class="row h-100">
            <div class="col-12">
                <div class="title-wrap h-100">
                    <h2>Teams</h2>
                    <div class="d-flex">
                        <a class="btn btn-yellow me-2"
                         href="{{ route('allTeam_csv', ['tournament_id' => $selectedTournamentId]) }}">
                         <i class="fa-solid fa-download me-2"></i>Download Teams
                      </a>

                      <a class="btn btn-yellow"
                         href="{{ route('allPlayers_csv', ['tournament_id' => $selectedTournamentId]) }}">
                         <i class="fa-solid fa-download me-2"></i>Download Players
                      </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="my-torunments-wrap">
    <div class="container">
       <div>
           <p>Total Teams: <strong>{{ $total_team_count }}</strong></p>
        </div>
         <form method="GET" action="{{ route('total-teams') }}" class="row mb-3 align-items-center">

    <!-- Tournament Dropdown -->
    <div class="col-md-4">
        <select name="tournament_id" class="form-control" onchange="this.form.submit()">
            <option value="">All Tournaments</option>
            @foreach($tournaments as $tournament)
                <option value="{{ $tournament->id }}"
                    {{ $selectedTournamentId == $tournament->id ? 'selected' : '' }}>
                    {{ $tournament->name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Search -->
    <div class="col-md-4">
        <input type="text"
               name="search"
               class="form-control"
               placeholder="Search by Team Name"
               value="{{ $search }}">
    </div>

    <!-- Actions -->
    <div class="col-md-4 d-flex gap-2">
        <a href="{{ route('not-applied-teams') }}" class="btn btn-warning">
            View Not Applied Teams
        </a>

        @if($search || $selectedTournamentId)
            <a href="{{ route('total-teams') }}" class="btn btn-secondary">
                Reset
            </a>
        @endif
    </div>
</form>

        <div class="row">
            <div class="col-12">
                <div class="myTorunments-table">
                    <div class="myTorunments-head">
                        <div class="myTorunments-head-col sno has-myTorunments-myTeam">S.No</div>
                        <div class="myTorunments-head-col name  has-myTorunments-myTeam">Team Name</div>
                      {{--	<div class="myTorunments-head-col name  has-myTorunments-myTeam">Match Preference</div> --}}
                   {{--   <div class="myTorunments-head-col name  has-myTorunments-myTeam">Logo</div>--}}
                        <div class="myTorunments-head-col name  has-myTorunments-myTeam">Bonafide</div>
                        <div class="myTorunments-head-col name has-myTorunments-myTeam">Members</div>
                       <div class="myTorunments-head-col name has-myTorunments-myTeam">Actions</div>
                    </div>
                    @if($paginatedTeams->isEmpty())
                    <div class="no-records-found">
                      <p class="text-center m-3">No records found</p>
                    </div>
                  @else
                    @foreach($paginatedTeams as $index => $team)
                    <div class="myTorunments-body">
                        <div class="myTorunments-body-col sno has-myTorunments-myTeam">{{ $paginatedTeams->firstItem() + $index }}</div>
                        <div class="myTorunments-body-col name has-myTorunments-myTeam"> <a href="{{ route('teamplayers', ['team_id' => $team->id]) }}">{{ $team->name }}</a></div>
                     {{-- 	<div class="myTorunments-body-col name has-myTorunments-myTeam"><i><img src="{{ config('constants.upload_url') . '/team_logos/' . $team->logo }}"  style="width: 50px; height: 50px; border-radius: 10px;object-fit: contain;"></i></div>--}}
                        {{-- <div class="myTorunments-body-col name has-myTorunments-myTeam">{{ $team->formatted_preference }}</div> --}}
                       <div class="myTorunments-body-col name has-myTorunments-myTeam">
                            @if($team->bonafide)
                                <a href="{{ config('constants.upload_url') . '/bonafide/' .  $team->bonafide }}"
                                   target="_blank" 
                                   class="btn btn-sm btn-info" 
                                   title="View Bonafide Certificate">
                                    View
                                </a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </div>
                        <div class="myTorunments-body-col name has-myTorunments-myTeam">{{ $team->players_count }}</div>
                      <div class="myTorunments-body-col name has-myTorunments-myTeam">
    <form action="{{ route('delete-tournaments-teams', $team->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this team?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm"style="
    border-radius: 10px;
    height: 30px;
">Delete</button>
    </form>
</div>
                    </div>
                    @endforeach

                    @endif


                    <!-- Pagination Links -->
                    <div class="col-12 pagination-wrap">
                        <nav>
                            <ul class="pagination justify-content-center m-0">
                              {{ $paginatedTeams->appends(request()->except('page'))->links('pagination::bootstrap-4') }}

                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
        @if(session('success'))
            // Show the alert message
            document.getElementById('successMessage').style.display = 'block';

            // Hide the message after 3 seconds
            setTimeout(function() {
                document.getElementById('successMessage').style.display = 'none';
                // Redirect to a specific page if needed
            }, 3000);
        @endif
      </script>

@endsection
