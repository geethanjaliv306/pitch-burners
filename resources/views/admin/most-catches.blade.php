@extends('layouts.admin')
@section('content')
<style>
  .btn-reset{
    width: 50%;
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
                  <h2> <a class="my-torunments-back" href="{{route('dashboard')}}"> </a>Most Catches</h2>
                </div>
            </div>
          </div>
        </div>
    </section>
<div class="container">
    <form method="GET" action="{{ route('most-catches') }}" id="filterForm">
        <div class="row">
            <div class="col-md-3">
                <label for="player_name">Player Name</label>
                <input type="text" name="player_name" id="player_name" class="form-control" placeholder="Enter player name" value="{{ request()->player_name }}">
            </div>
            <div class="col-md-3">
                <label for="tournament_id">Tournament</label>
                <select name="tournament_id" id="tournament_id" class="form-control" onchange="this.form.submit()">
                    <option value="">Select Tournament</option>
                    @foreach($tournaments as $tournament)
                        <option value="{{ $tournament->id }}" {{ request()->tournament_id == $tournament->id ? 'selected' : '' }}>{{ $tournament->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="team">Team</label>
                <select name="team" id="team" class="form-control" onchange="this.form.submit()">
                    <option value="">Select Team</option>
                    @foreach($teams as $team)
                        <option value="{{ $team->name }}" {{ request()->team == $team->name ? 'selected' : '' }}>{{ $team->name }}</option>
                    @endforeach
                </select>
            </div>
            @if(request()->has('player_name') || request()->has('tournament_id') || request()->has('team')||
    request()->has('player_sort') ||
    request()->has('catch_sort') ||  request()->has('wk_sort')  ||  request()->has('stump_sort') )
                <div class="col-md-3">
                    <a href="{{ route('most-catches') }}" class="btn btn-secondary btn-reset">Reset</a>
                </div>
            @endif
        </div>
    </form>
</div>



    <div class="row mt-4">
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>S.No</th>
                            <th><div class="filter-wrap">Player Name
                           <a href="{{ request()->fullUrlWithQuery(['player_sort' => 'asc']) }}" class="up filter {{ request()->input('player_sort') == 'asc' ? 'active' : '' }}"></a>
            <a href="{{ request()->fullUrlWithQuery(['player_sort' => 'desc']) }}" class="down filter {{ request()->input('player_sort') == 'desc' ? 'active' : '' }}"></a>
   </div> </th>
                          
                            <th><div class="filter-wrap">Total Keeper Catches
                           <a href="{{ request()->fullUrlWithQuery(['wk_sort' => 'asc']) }}" class="up filter {{ request()->input('wk_sort') == 'asc' ? 'active' : '' }}"></a>
            <a href="{{ request()->fullUrlWithQuery(['wk_sort' => 'desc']) }}" class="down filter {{ request()->input('wk_sort') == 'desc' ? 'active' : '' }}"></a>
   </div> </th>
                      {{--    <th><div class="filter-wrap">Total Stumpings
                           <a href="{{ request()->fullUrlWithQuery(['stump_sort' => 'asc']) }}" class="up filter {{ request()->input('stump_sort') == 'asc' ? 'active' : '' }}"></a>
            <a href="{{ request()->fullUrlWithQuery(['stump_sort' => 'desc']) }}" class="down filter {{ request()->input('stump_sort') == 'desc' ? 'active' : '' }}"></a>
   </div> </th>  --}}
                            <th><div class="filter-wrap">Total Catches
                           <a href="{{ request()->fullUrlWithQuery(['catch_sort' => 'asc']) }}" class="up filter {{ request()->input('catch_sort') == 'asc' ? 'active' : '' }}"></a>
            <a href="{{ request()->fullUrlWithQuery(['catch_sort' => 'desc']) }}" class="down filter {{ request()->input('catch_sort') == 'desc' ? 'active' : '' }}"></a>
   </div> </th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($allPlayerFieldingStats->isEmpty())
                        <tr>
                            <td colspan="4">No records found</td>
                        </tr>
                        @else
                        @foreach ($allPlayerFieldingStats as $index => $stats)
                        <tr>
                            <td>{{ ($allPlayerFieldingStats->currentPage() - 1) * $allPlayerFieldingStats->perPage() + $index + 1 }}</td>
                            <td>{{ $stats->player ?? 'Unknown' }} ({{ $stats->team ?? 'Unknown' }})</td>
                           <td>{{ $stats->wk_catch_count }}</td>
                     {{--  <td>{{ $stats->stump_count }}</td>  --}}
                          <td><strong>{{ $stats->catch_count }}</strong></td>
                          
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
      <div class="container mt-3">
        <div class="d-flex justify-content-center">
            {{ $allPlayerFieldingStats->links('pagination::bootstrap-4') }}
        </div>
    </div>
       {{-- <div class="col-12 pagination-wrap">
            <div class="container position-relative">
                <nav>
                    <ul class="pagination justify-content-center m-0">
                    {{ $allPlayerFieldingStats->links('pagination::bootstrap-4') }}
                    </ul>
                </nav>
            </div>
        </div>--}}
    </div>
</section>
@endsection
