@extends('layouts.app')
@section('content')
<style>
    .btn-reset{
    width: auto;
    height: 35px;
    margin: 0px;
    border-radius: 7px;
  }
  .stats-wrapper{
    min-height: calc(100vh - 149px);
  }
  @media (max-width: 767.98px) {
  .stats-wrapper .stats-banner .banner-info figure img.figs {
    width: 250px !important;
  }
   
  }
   .player_name{
      text-align:left;
    }
  .pagination{
    display:flex;
    flex-wrap:wrap;
  }
  .footer-mobile-app{
    display:none;
  }
</style>
<section class="stats-wrapper">
    <div class="stats-banner">
      <div class="container h-100">
        <div class="row h-100">
          <div class="col-12 d-flex align-items-center">
            <div class="banner-info">
              <figure id="battingBanner">
                <img class="no-one" src="{{ asset('uploads/images/number-1-svgrepo-com.svg')}}" />

                <img class="figs" src="{{ config('constants.upload_url') . '/player_images/' . ($topBatsman->image ?? 'register-bg.jpg') }}" />            </figure>
            <figcaption id="battingCaption">
                <div>
                    <h3>{{ ucwords(strtolower($topBatsman->player?? 'Player')) }} ({{ $topBatsman->team ?? 'No Team' }})</h3>
                   {{--  <i>
                     @if($allPlayerStats->first()->logo) 
                          <img src="{{ config('constants.upload_url') . '/team_logos/' . $allPlayerStats->first()->logo }}" alt="Team Logo" />
                      @else
                          <img src="{{ asset('uploads/images/dummy_logo.jpg') }}" alt="Default Team Logo" /> 
                      @endif 
                  </i>--}}
                </div>
                <ul>
                    <li>
                        <p>{{ $topBatsman->runs ?? 0 }}</p>
                        <label>Runs</label>
                    </li>
                    <li>
                        <p>{{ $topBatsman->matches ?? 0 }}</p>
                        <label>Matches</label>
                    </li>
                    <li>
                        <p>{{ $topBatsman->avg ?? 0 }}</p>
                        <label>Average</label>
                    </li>
                    <li>
                        <p>{{ $topBatsman->sr ?? 0 }}</p>
                        <label>Strike Rate</label>
                    </li>
                    <li>
                        <p>{{ $topBatsman->highest ?? 0 }}</p>
                        <label>Hs. Score</label>
                    </li>
                    <li>
                        <p>{{ $topBatsman->fifties ?? 0 }}/{{ $topBatsman->hundreds ?? 0 }}</p>
                        <label>50s/100s</label>
                    </li>
                </ul>
            </figcaption>

            <!-- Add bowling details -->
            <figure id="bowlingBanner" style="display: none;">
                <img class="no-one" src="{{ asset('uploads/images/number-1-svgrepo-com.svg')}}" />
                <img class="figs" src="{{ config('constants.upload_url') . '/player_images/' . ($topBowler->image ?? 'register-bg.jpg') }}" />
                            </figure>
            <figcaption id="bowlingCaption" style="display: none;">
                <div>
                    <h3>{{ ucwords(strtolower($topBowler->player ?? 'Player')) }} ({{ $topBowler->team ?? 'No Team' }})</h3>
               {{--     <i><img src="{{ asset('uploads/images/dummy_logo.jpg')}}" /></i>  --}}
                </div> 
                <ul>
                    <li>
                        <p>{{ $topBowler->wickets ?? 0 }}</p>
                        <label>Wickets</label>
                    </li>
                    <li>
                        <p>{{ $topBowler->matches ?? 0 }}</p>
                        <label>Matches</label>
                    </li>
                    <li>
                        <p>{{ $topBowler->economy ?? 0 }}</p>
                        <label>Economy</label>
                    </li>
                    <li>
                        <p>{{ $topBowler->threeFer ?? 0 }}</p>
                        <label>3-Fers</label>
                    </li>
                    <li>
                        <p>{{ $topBowler->fiveFer ?? 0 }}</p>
                        <label>5-Fers</label>
                    </li>
                </ul>
            </figcaption>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="container">
        <form method="GET" action="{{ route('stats') }}" id="filterForm">
            <input type="hidden" name="category" id="selectedCategory" value="{{ $category }}">
            <div class="row g-3 mb-4 filter-row">
                <div class="col-12 col-lg-2">
                    <select class="form-select auto-submit" id="categorySelect" name="category">
                        <option value="Batting" {{ $category === 'Batting' ? 'selected' : '' }}>Batting</option>
                        <option value="Bowling" {{ $category === 'Bowling' ? 'selected' : '' }}>Bowling</option>
                    </select>
                </div>

                <div class="col-12 col-lg-3">
                    <select class="form-select auto-submit" name="tournament" id="seasonSelect" aria-label="Select Season">
                        <option value="" selected>All Tournaments</option>
                        @foreach ($tournaments as $tournament)
                            <option value="{{ $tournament->id }}" {{ request()->get('tournament') == $tournament->id ? 'selected' : '' }}>
                                {{ $tournament->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-lg-2">
                    <select class="form-select auto-submit" name="team" id="teamSelect" aria-label="Select Team">
                        <option value="" selected>All Teams</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->name }}" {{ request()->get('team') == $team->name ? 'selected' : '' }}>
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-lg-2">
                    <select name="ball_type" class="form-select auto-submit" id="ballTypeSelect">
                        <option value="" {{ request('ball_type') === '' && request()->has('ball_type') ? 'selected' : '' }}>
                            All Ball Types
                        </option>

                        <option value="Green Tennis" {{ request('ball_type') === 'Green Tennis' ? 'selected' : '' }}>
                            Box Cricket Tournament
                        </option>

                        <option value="Red Tennis" {{ !request()->has('ball_type') || request('ball_type') === 'Red Tennis' ? 'selected' : '' }}>
                            Red Tennis Ball Tournament
                        </option>

                        <option value="White Ball" {{ request('ball_type') === 'White Ball' ? 'selected' : '' }}>
                            White Ball Tournament
                        </option>
                    </select>
                </div>
                <div class="col-12 col-lg-2">
                    <input type="text" class="form-control auto-submit" name="player_name" id="searchPlayer" placeholder="🔍 Search By Player Name" value="{{ request()->get('player_name') }}">
                </div>

                <div class="col-12 col-lg-1">
                    <button type="button" class="btn btn-secondary btn-reset" id="resetFilters" style="display: none;">Reset</button>
                </div>
            </div>
        </form>

      <div id="battingStats" class="row">
        <div class="col-12">
            <div class="table-responsive">
               <table class="table table-bordered table-hover text-center align-middle">
    <thead class="table-dark">
        <tr>
            <th>S.No</th>
            <th>PLAYER</th>
            <th>RUNS</th>
            <th>MATCHES</th>
            <th>BALLS FACED</th>
            <th>HIGHEST SCORE</th>
            <th>AVERAGE</th>
            <th>STRIKE RATE</th>
            <th>100s</th>
            <th>50s</th>
            <th>FOURS</th>
            <th>SIXES</th>
        </tr>
    </thead>
    <tbody>
        @if ($allPlayerStats->isEmpty())
            <tr>
                <td colspan="12" class="text-center">No records found</td>
            </tr>
        @else
            @foreach ($allPlayerStats as $index => $stats)
            <tr>
                 <td>{{ ($allPlayerStats->currentPage() - 1) * $allPlayerStats->perPage() + $index + 1 }}</td>
               <td class="player_name">
    {{ ucwords(strtolower($stats->player ?? 'Player')) }} 
    ({{ $stats->team ?? 'No Team' }})
</td>

              <td><strong>{{ $stats->runs }}</strong></td>
                <td>{{ $stats->matches }}</td>
                <td>{{ $stats->bf }}</td>
                <td>{{ $stats->highest }}</td>
                <td>{{ $stats->avg }}</td>
                <td>{{ $stats->sr }}</td>
                <td>{{ $stats->hundreds }}</td>
                <td>{{ $stats->fifties }}</td>
                <td>{{ $stats->fours }}</td>
                <td>{{ $stats->sixes }}</td>
            </tr>
            @endforeach
        @endif
    </tbody>
</table>

            </div>
           <div class="container mt-3">
        <div class="d-flex justify-content-center">
            {{ $allPlayerStats->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
</div>
    </div>
        </div>
      </div>

       <!-- Bowling Stats Table -->
      <div id="bowlingStats" class="row" style="display:none;">
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>S.No</th>
                            <th>PLAYER</th>
                            <th>WICKETS</th>
                            <th>MATCHES</th>
                            <th>OVERS BOWLED</th>
                            <th>RUNS GIVEN</th>
                            <th>ECONOMY</th>
                            <th>3-FER</th>
                            <th>5-FER</th>
                        </tr>
                    </thead>
                    <tbody> 
                      @if ($allBowlingStats->isEmpty())
                          <tr>
                              <td colspan="12" class="text-center">No records found</td>
                          </tr>
                      @else
                        @foreach ($allBowlingStats as $index => $stats)
                        <tr>
                          <td>{{ ($allBowlingStats->currentPage() - 1) * $allBowlingStats->perPage() + $index + 1 }}</td>
                           <td class="player_name">
    {{ ucwords(strtolower($stats->player ?? 'Player')) }} 
    ({{ $stats->team ?? 'No Team' }})
</td>

                          <td><strong>{{ $stats->wickets }}</strong></td>
                            <td>{{ $stats->matches }}</td>
                            <td>{{ $stats->overs_bowled }}</td>
                            <td>{{ $stats->runs }}</td>
                            <td>{{ $stats->economy }}</td>
                            <td>{{ $stats->threeFer }}</td>
                            <td>{{ $stats->fiveFer }}</td>
                        </tr>
                        @endforeach
                       @endif
                    </tbody>
                </table>
            </div>
   <div class="container mt-3">
        <div class="d-flex justify-content-center">
            {{ $allBowlingStats->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
</div>
    </div>
        </div>
      </div>

    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const categorySelect = document.getElementById('categorySelect');
    const battingStats = document.getElementById('battingStats');
    const bowlingStats = document.getElementById('bowlingStats');
    const selectedCategoryInput = document.getElementById('selectedCategory');
    const filterForm = document.getElementById('filterForm');
    const resetButton = document.getElementById('resetFilters');

    // Banner elements
    const battingBanner = document.getElementById('battingBanner');
    const battingCaption = document.getElementById('battingCaption');
    const bowlingBanner = document.getElementById('bowlingBanner');
    const bowlingCaption = document.getElementById('bowlingCaption');

    // Show the correct table and banner based on initial selection
    const setDisplay = (selectedCategory) => {
        if (selectedCategory === 'Bowling') {
            battingStats.style.display = 'none';
            bowlingStats.style.display = 'block';
            battingBanner.style.display = 'none';
            battingCaption.style.display = 'none';
            bowlingBanner.style.display = 'block';
            bowlingCaption.style.display = 'block';
        } else {
            battingStats.style.display = 'block';
            bowlingStats.style.display = 'none';
            battingBanner.style.display = 'block';
            battingCaption.style.display = 'block';
            bowlingBanner.style.display = 'none';
            bowlingCaption.style.display = 'none';
        }
    };

    // Initialize the display based on the current category value
    setDisplay(categorySelect.value);

    // Listen for category selection changes
    categorySelect.addEventListener('change', function () {
        selectedCategoryInput.value = categorySelect.value;
        filterForm.submit(); // Automatically submit the form when the category changes
    });

    // Automatically submit the form on any other filter change
    document.querySelectorAll('.auto-submit').forEach(element => {
        element.addEventListener('change', function() {
            filterForm.submit(); // Automatically submit the form on change
        });
    });

    // Show reset button if any filter is applied
    const showResetButton = () => {
        if (
            filterForm.querySelector('input[name="player_name"]').value ||
            filterForm.querySelector('select[name="tournament"]').value ||
            filterForm.querySelector('select[name="team"]').value ||
            filterForm.querySelector('select[name="ball_type"]').value
        ) {
            resetButton.style.display = 'block';
        } else {
            resetButton.style.display = 'none';
        }
    };

    showResetButton(); // Initialize the reset button display

    // Reset all filters
    resetButton.addEventListener('click', function () {
    filterForm.querySelectorAll('select').forEach(select => select.value = '');
    filterForm.querySelector('input[name="player_name"]').value = '';
    filterForm.submit();
    });
});
document.getElementById('ballTypeSelect')?.addEventListener('change', function () {
    const form = document.getElementById('filterForm');

    // Reset dependent filters
    form.querySelector('select[name="tournament"]').value = '';
    form.querySelector('select[name="team"]').value = '';

    form.submit();
});
</script>
@endsection
