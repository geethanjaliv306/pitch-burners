@extends('layouts.admin')
@section('content')
<style>
    .btn-reset {
        width: 100%;
        height: 43px;
    }
   .filter {
        cursor: pointer;
        font-size: 16px;
        padding: 0 2px;
        color: white;
    }

    .filter.up::before {
        content: '\2191'; /* Unicode character for up arrow */
        font-size: 14px;
    }

    .filter.down::before {
        content: '\2193'; /* Unicode character for down arrow */
        font-size: 14px;
    }

    .filter.active {
        color: #0d6efd; /* Blue color for active sorting */
    }

    th a {
        text-decoration: none; /* Remove underline from links */
        display: inline-block; /* Ensure better alignment with text */
    }

    th {
        position: relative;
        vertical-align: middle;
        text-align: center;
        white-space:nowrap;
    }

    .filter-wrap {
        display: flex;
        justify-content: center;
        gap: 10px;
    }
</style>

<section class="startmatch-wrap dashboard-page">
    <section class="startmatch-wrap dashboard-page">
        <section class="my-torunments-second-header fixed-second-header">
            <div class="container-fluid h-100">
                <div class="row h-100">
                    <div class="col-12">
                        <div class="title-wrap h-100">
                            <h2>
                                <a class="my-torunments-back" href="{{ route('dashboard') }}"></a>
                                Most Wickets
                            </h2>
                           <div class="d-flex">
                            <a href="{{ route('export-most-wickets') }}" class="btn btn-success">
                                <i class="fas fa-download me-2"></i> Export
                            </a>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Filter Section -->
        <div class="container mt-3">
            <form method="GET" action="{{ route('most-wickets') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="player_name" class="form-label">Player Name</label>
                        <input type="text" name="player_name" id="player_name" class="form-control" placeholder="Enter player name" value="{{ request()->player_name }}">
                    </div>
                    <div class="col-md-3">
                        <label for="tournament_id" class="form-label">Tournament</label>
                        <select name="tournament_id" id="tournament_id" class="form-control" onchange="this.form.submit()">
                            <option value="">Select Tournament</option>
                            @foreach($tournaments as $tournament)
                                <option value="{{ $tournament->id }}" {{ request()->tournament_id == $tournament->id ? 'selected' : '' }}>{{ $tournament->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="team_name" class="form-label">Team Name</label>
                        <select name="team" id="team_name" class="form-control" onchange="this.form.submit()">
                            <option value="">Select Team</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->name }}" {{ request()->team == $team->name ? 'selected' : '' }}>{{ $team->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if(request()->has('player_name') || request()->has('tournament_id') || request()->has('team')|| 
    request()->has('player_sort') || 
    request()->has('matches_sort') || 
    request()->has('overs_bowled_sort') || 
    request()->has('runs_given_sort') || 
    request()->has('wickets_sort') || 
    request()->has('economy_sort') || 
    request()->has('three_fer_sort') || 
    request()->has('five_fer_sort')
)
                    <div class="col-md-3 d-flex align-items-end">
                        <a href="{{ route('most-wickets') }}" class="btn btn-secondary btn-reset">Reset</a>
                    </div>
                    @endif
                </div>
            </form>
        </div>

        <!-- Stats Table -->
        <div class="container mt-4">
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                   <thead class="table-dark">
    <tr>
        <th>S.No</th>
        <th>
           <div class="filter-wrap"> Player
            <a href="{{ request()->fullUrlWithQuery(['player_sort' => 'asc']) }}" class="up filter {{ request()->input('player_sort') == 'asc' ? 'active' : '' }}"></a>
            <a href="{{ request()->fullUrlWithQuery(['player_sort' => 'desc']) }}" class="down filter {{ request()->input('player_sort') == 'desc' ? 'active' : '' }}"></a>
       </div>   </th>
       <th>
           <div class="filter-wrap"> Wickets
            <a href="{{ request()->fullUrlWithQuery(['wickets_sort' => 'asc']) }}" class="up filter {{ request()->input('wickets_sort') == 'asc' ? 'active' : '' }}"></a>
            <a href="{{ request()->fullUrlWithQuery(['wickets_sort' => 'desc']) }}" class="down filter {{ request()->input('wickets_sort') == 'desc' ? 'active' : '' }}"></a>
       </div>   </th>
        <th>
          <div class="filter-wrap">  Matches
            <a href="{{ request()->fullUrlWithQuery(['matches_sort' => 'asc']) }}" class="up filter {{ request()->input('matches_sort') == 'asc' ? 'active' : '' }}"></a>
            <a href="{{ request()->fullUrlWithQuery(['matches_sort' => 'desc']) }}" class="down filter {{ request()->input('matches_sort') == 'desc' ? 'active' : '' }}"></a>
    </div>      </th>
        <th>
          <div class="filter-wrap">  Overs Bowled
            <a href="{{ request()->fullUrlWithQuery(['overs_bowled_sort' => 'asc']) }}" class="up filter {{ request()->input('overs_bowled_sort') == 'asc' ? 'active' : '' }}"></a>
            <a href="{{ request()->fullUrlWithQuery(['overs_bowled_sort' => 'desc']) }}" class="down filter {{ request()->input('overs_bowled_sort') == 'desc' ? 'active' : '' }}"></a>
      </div>    </th>
        <th>
          <div class="filter-wrap">  Runs Given
            <a href="{{ request()->fullUrlWithQuery(['runs_given_sort' => 'asc']) }}" class="up filter {{ request()->input('runs_given_sort') == 'asc' ? 'active' : '' }}"></a>
            <a href="{{ request()->fullUrlWithQuery(['runs_given_sort' => 'desc']) }}" class="down filter {{ request()->input('runs_given_sort') == 'desc' ? 'active' : '' }}"></a>
      </div>    </th>
       
        <th>
          <div class="filter-wrap">  Economy
            <a href="{{ request()->fullUrlWithQuery(['economy_sort' => 'asc']) }}" class="up filter {{ request()->input('economy_sort') == 'asc' ? 'active' : '' }}"></a>
            <a href="{{ request()->fullUrlWithQuery(['economy_sort' => 'desc']) }}" class="down filter {{ request()->input('economy_sort') == 'desc' ? 'active' : '' }}"></a>
        </div>  </th>
        <th>
         <div class="filter-wrap">   3-Fer
            <a href="{{ request()->fullUrlWithQuery(['three_fer_sort' => 'asc']) }}" class="up filter {{ request()->input('three_fer_sort') == 'asc' ? 'active' : '' }}"></a>
            <a href="{{ request()->fullUrlWithQuery(['three_fer_sort' => 'desc']) }}" class="down filter {{ request()->input('three_fer_sort') == 'desc' ? 'active' : '' }}"></a>
       </div>   </th>
        <th>
          <div class="filter-wrap">  5-Fer
            <a href="{{ request()->fullUrlWithQuery(['five_fer_sort' => 'asc']) }}" class="up filter {{ request()->input('five_fer_sort') == 'asc' ? 'active' : '' }}"></a>
            <a href="{{ request()->fullUrlWithQuery(['five_fer_sort' => 'desc']) }}" class="down filter {{ request()->input('five_fer_sort') == 'desc' ? 'active' : '' }}"></a>
        </div>  </th>
    </tr>
</thead>

                    <tbody>
                        @if($allBowlingStats->isEmpty())
                            <tr>
                                <td colspan="9">No records found</td>
                            </tr>
                        @else
                            @foreach ($allBowlingStats as $index => $stats)
                            <tr>
                               <td>{{ ($allBowlingStats->currentPage() - 1) * $allBowlingStats->perPage() + $index + 1 }}</td>
                                <td>{{ $stats->player ?? 'Unknown' }}({{ $stats->team ?? 'Unknown' }})</td>
                              <td><strong>{{ $stats->total_wickets_taken }}</strong></td>
                                <td>{{ $stats->total_matches }}</td>
                                <td>{{ $stats->total_overs_bowled }}</td>
                                <td>{{ $stats->total_runs_conceded }}</td>
                                
                                <td>{{ $stats->economy_rate }}</td>
                                <td>{{ $stats->three_fers }}</td>
                                <td>{{ $stats->five_fers }}</td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="container mt-3">
            <div class="d-flex justify-content-center">
                {{ $allBowlingStats->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </section>
</section>
@endsection
