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
    <!-- Page Header -->
    <section class="my-torunments-second-header fixed-second-header">
        <div class="container-fluid h-100">
            <div class="row h-100">
                <div class="col-12">
                    <div class="title-wrap h-100">
                        <h2>
                            <a class="my-torunments-back" href="{{ route('dashboard') }}"></a>
                            Most Runs
                        </h2>
                      <div class="d-flex">
                          <a href="{{ route('export-most-runs') }}" class="btn btn-success">
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
        <form method="GET" action="{{ route('most-runs') }}" id="filterForm">
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
                @if(request()->has('player_name') || request()->has('tournament_id')|| request()->has('team')||
    request()->has('player_sort') ||
    request()->has('matches_sort') ||
    request()->has('sixes_sort') ||
    request()->has('fours_sort') ||
    request()->has('fifties_sort') ||
    request()->has('strike_rate_sort') ||
    request()->has('fastest_fifty_sort') ||
    request()->has('hundreds_sort') ||
    request()->has('avg_sort') ||
    request()->has('balls_faced_sort') ||
    request()->has('runs_sort') ||
    request()->has('highest_score_sort')
)
                <div class="col-md-3 d-flex align-items-end">
                    <a href="{{ route('most-runs') }}" class="btn btn-secondary btn-reset">Reset</a>
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
                        <th> <div class="filter-wrap">Player
                            
                        <a href="{{ request()->fullUrlWithQuery(['player_sort' => 'asc']) }}" class="up filter {{ request()->input('player_sort') == 'asc' ? 'active' : '' }}"></a>
            <a href="{{ request()->fullUrlWithQuery(['player_sort' => 'desc']) }}" class="down filter {{ request()->input('player_sort') == 'desc' ? 'active' : '' }}"></a>
                           </div></th>
                        <th><div class="filter-wrap">Runs
                           
                        <a href="{{ request()->fullUrlWithQuery(['runs_sort' => 'asc']) }}" class="up filter {{ request()->input('runs_sort') == 'asc' ? 'active' : '' }}"></a>
            <a href="{{ request()->fullUrlWithQuery(['runs_sort' => 'desc']) }}" class="down filter {{ request()->input('runs_sort') == 'desc' ? 'active' : '' }}"></a>
                          </div>   </th>
                        <th><div class="filter-wrap">Matches
                          <a href="{{ request()->fullUrlWithQuery(['matches_sort' => 'asc']) }}" class="up filter {{ request()->input('matches_sort') == 'asc' ? 'active' : '' }}"></a>
            <a href="{{ request()->fullUrlWithQuery(['matches_sort' => 'desc']) }}" class="down filter {{ request()->input('matches_sort') == 'desc' ? 'active' : '' }}"></a>
     </div>   </th>
                        <th><div class="filter-wrap">Balls Faced
                       <a href="{{ request()->fullUrlWithQuery(['balls_faced_sort' => 'asc']) }}" class="up filter {{ request()->input('balls_faced_sort') == 'asc' ? 'active' : '' }}"></a>
            <a href="{{ request()->fullUrlWithQuery(['balls_faced_sort' => 'desc']) }}" class="down filter {{ request()->input('balls_faced_sort') == 'desc' ? 'active' : '' }}"></a>
     </div>   </th>
                        <th><div class="filter-wrap">Highest Score
                       <a href="{{ request()->fullUrlWithQuery(['highest_score_sort' => 'asc']) }}" class="up filter {{ request()->input('highest_score_sort') == 'asc' ? 'active' : '' }}"></a>
            <a href="{{ request()->fullUrlWithQuery(['highest_score_sort' => 'desc']) }}" class="down filter {{ request()->input('highest_score_sort') == 'desc' ? 'active' : '' }}"></a>
      </div>  </th>
                        <th><div class="filter-wrap">Average
                       <a href="{{ request()->fullUrlWithQuery(['avg_sort' => 'asc']) }}" class="up filter {{ request()->input('avg_sort') == 'asc' ? 'active' : '' }}"></a>
            <a href="{{ request()->fullUrlWithQuery(['avg_sort' => 'desc']) }}" class="down filter {{ request()->input('avg_sort') == 'desc' ? 'active' : '' }}"></a>
    </div>    </th>
                        <th><div class="filter-wrap">Strike Rate
                       <a href="{{ request()->fullUrlWithQuery(['strike_rate_sort' => 'asc']) }}" class="up filter {{ request()->input('strike_rate_sort') == 'asc' ? 'active' : '' }}"></a>
            <a href="{{ request()->fullUrlWithQuery(['strike_rate_sort' => 'desc']) }}" class="down filter {{ request()->input('strike_rate_sort') == 'desc' ? 'active' : '' }}"></a>
     </div>   </th>
                        <th><div class="filter-wrap">100s
                       <a href="{{ request()->fullUrlWithQuery(['hundreds_sort' => 'asc']) }}" class="up filter {{ request()->input('hundreds_sort') == 'asc' ? 'active' : '' }}"></a>
            <a href="{{ request()->fullUrlWithQuery(['hundreds_sort' => 'desc']) }}" class="down filter {{ request()->input('hundreds_sort') == 'desc' ? 'active' : '' }}"></a>
      </div>  </th>
                        <th><div class="filter-wrap">50s
                       <a href="{{ request()->fullUrlWithQuery(['fifties_sort' => 'asc']) }}" class="up filter {{ request()->input('fifties_sort') == 'asc' ? 'active' : '' }}"></a>
            <a href="{{ request()->fullUrlWithQuery(['fifties_sort' => 'desc']) }}" class="down filter {{ request()->input('fifties_sort') == 'desc' ? 'active' : '' }}"></a>
       </div> </th>
                       <th><div class="filter-wrap">Fastest 50

                       <a href="{{ request()->fullUrlWithQuery(['fastest_fifty_sort' => 'asc']) }}" class="up filter {{ request()->input('fastest_fifty_sort') == 'asc' ? 'active' : '' }}"></a>

            <a href="{{ request()->fullUrlWithQuery(['fastest_fifty_sort' => 'desc']) }}" class="down filter {{ request()->input('fastest_fifty_sort') == 'desc' ? 'active' : '' }}"></a>

       </div> </th>
                        <th><div class="filter-wrap">Fours
                       <a href="{{ request()->fullUrlWithQuery(['fours_sort' => 'asc']) }}" class="up filter {{ request()->input('fours_sort') == 'asc' ? 'active' : '' }}"></a>
            <a href="{{ request()->fullUrlWithQuery(['fours_sort' => 'desc']) }}" class="down filter {{ request()->input('fours_sort') == 'desc' ? 'active' : '' }}"></a>
      </div>  </th>
                        <th><div class="filter-wrap">Sixes
                       <a href="{{ request()->fullUrlWithQuery(['sixes_sort' => 'asc']) }}" class="up filter {{ request()->input('sixes_sort') == 'asc' ? 'active' : '' }}"></a>
            <a href="{{ request()->fullUrlWithQuery(['sixes_sort' => 'desc']) }}" class="down filter {{ request()->input('sixes_sort') == 'desc' ? 'active' : '' }}"></a>
       </div> </th>
                    </tr>
                </thead>
                <tbody>
                    @if($allPlayerStats->isEmpty())
                        <tr>
                            <td colspan="12">No records found</td>
                        </tr>
                    @else
                        @foreach ($allPlayerStats as $index => $stats)
                        <tr>
                            <td>{{ ($allPlayerStats->currentPage() - 1) * $allPlayerStats->perPage() + $index + 1 }}</td>
                            <td>{{ $stats->player ?? 'Unknown' }} ({{ $stats->team ?? 'Unknown' }})</td>
                            <td><strong>{{ $stats->total_runs }}</strong></td>
                            <td>{{ $stats->matches }}</td>
                            <td>{{ $stats->total_balls_faced }}</td>
                            <td>{{ $stats->highest_score }}</td>
                            <td>{{ $stats->avg }}</td>
                            <td>{{ $stats->strike_rate }}</td>
                            <td>{{ $stats->hundreds }}</td>
                            <td>{{ $stats->fifties }}</td>
                            <td>{{ $stats->fastest_fifty ?? '-' }}</td>
                            <td>{{ $stats->total_fours }}</td>
                            <td>{{ $stats->total_sixes }}</td>
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
            {{-- {{ $allPlayerStats->links('pagination::bootstrap-4') }} --}}
            {{ $allPlayerStats->appends(request()->except('page'))->links('pagination::bootstrap-4') }}

        </div>
    </div>
</section>
@endsection
