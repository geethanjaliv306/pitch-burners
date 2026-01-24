@extends('layouts.admin')

@section('content')
<style>
  .start-match-button{
    width: auto;
    padding: 0 20px;
    height: 35px;
    font-size: 15px;
    text-transform: none;
    margin: 0;
    background-color: #FBC638;
    border-radius: 20px;
    border: none;
  }
  .start-match-button:disabled {
    background-color: #d6d6d6;
    cursor: not-allowed;
}
.btn-reset{
    height: 40px;
    margin: 0px;
}
  
.myTorunments-table .myTorunments-head .myTorunments-head-col.matchTime{
flex: 1 0 calc(19.3333% - 70px);
    max-width: calc(27.3333% - 0px);
}
 .myTorunments-table .myTorunments-body .myTorunments-body-col.matchTime {
    flex: 1 0 calc(26.3333% - 70px);
    max-width: calc(34.3333% - 70px);
    white-space: nowrap;
}
  @media (max-width: 768.98px) {
        .searchbox{
            margin-bottom:10px;
        }
    }
</style>
    <section class="my-torunments-second-header fixed-second-header">
      <div class="container h-100">
        <div class="row h-100">
          <div class="col-12">
              <div class="title-wrap h-100 ">
                <h2>Schedule Matches</h2>
              </div>
          </div>
        </div>
      </div>
    </section>

    <section class="my-torunments-wrap">
      <div class="container ">
        <form method="GET" action="{{ route('schedulematch') }}" id="searchForm">
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="text" name="team" class="form-control searchbox" placeholder="Search by Team Name" value="{{ $searchTeam }}" onkeypress="if(event.keyCode === 13) { this.form.submit(); }">
                </div>
                {{-- <div class="col-md-3">
                    <input type="text" name="ground" class="form-control" placeholder="Search by Ground Name" value="{{ $searchGround }}" onkeypress="if(event.keyCode === 13) { this.form.submit(); }">
                </div> --}}
                <div class="col-md-3">
                    <select name="tournament" class="form-control searchbox" onchange="this.form.submit();">
                        <option value="">Select Tournament</option>
                        @foreach($tournaments as $tournament)
                            <option value="{{ $tournament->name }}" {{ $searchTournament == $tournament->name ? 'selected' : '' }}>
                                {{ $tournament->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date" class="form-control searchbox" value="{{ $searchDate }}" onchange="this.form.submit();">
                </div>

                <div class="col-md-2">
                    <select name="status" class="form-control searchbox" onchange="this.form.submit();">
                        <option value="">Match Status</option>
                        <option value="scheduled" {{ $searchStatus == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="active" {{ $searchStatus == 'active' ? 'selected' : '' }}>Live</option>
                        <option value="completed" {{ $searchStatus == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="canceled" {{ $searchStatus == 'canceled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    @if($searchTeam || $searchGround || $searchDate || $searchTournament||$searchStatus)
                        <a href="{{ route('schedulematch') }}" class="btn btn-secondary w-100 btn-reset">Reset</a>
                    @endif
                </div>
            </div>
        </form>
        <div class="row">
          <div class="col-12">
            <div class="myTorunments-table">
              <div class="myTorunments-head">
                <div class="myTorunments-head-col sno">S.No</div>
                <div class="myTorunments-head-col matchBetween">Match Between</div>
                <div class="myTorunments-head-col ground">Tournament</div>
                <div class="myTorunments-head-col matchTime">Match Time</div>
                <div class="myTorunments-head-col actions d-none">Actions</div>
              </div>
              @if($matches->isEmpty())
              <div class="no-records-found">
                <p class="text-center m-3">No records found</p>
              </div>
            @else
              @foreach($matches as $match)
                <div class="myTorunments-body">
                  <div class="myTorunments-body-col sno">{{ $loop->iteration + ($matches->currentPage() - 1) * $matches->perPage() }}</div>
                  <div class="myTorunments-body-col matchBetween">{{ $match->team_one_name }} vs {{ $match->team_two_name }}</div>
                  <div class="myTorunments-body-col ground ">{{ $match->tournament_name }}</div>
                  <div class="myTorunments-body-col matchTime">{{ \Carbon\Carbon::parse($match->match_date_time)->format('M d, Y h:i A') }}</div>
                  <div class="myTorunments-body-col actions d-none">
                      <div class="edit icon d-none">
                          <i><img src="{{ asset('/uploads/images/pen.svg') }}" /></i>
                      </div>
                      <div class="delete icon d-none">
                        <form action="{{ route('schedulematch.delete', $match->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="border:none; background:none;" onclick="return confirm('Are you sure you want to delete this match?')">
                                <i>
                                    <img src="{{ asset('uploads/images/delete.png')}}" />
                                </i>
                            </button>
                        </form>
                    </div>
                    <a href="{{ route('match.details', $match->id) }}">
                        <button class="start-match-button"
                        @if(in_array($match->status, ['Active', 'Completed','Cancelled'])) disabled @endif>
                        Start
                    </button>
                    </a>
                  </div>
                  </div>
            @endforeach
            @endif
            </div>
          </div>
        </div>
        <div class="col-12 pagination-wrap">
          <nav>
              <ul class="pagination justify-content-center m-0">
                 {{ $matches->appends(request()->except('page'))->links('pagination::bootstrap-4') }}

              </ul>
          </nav>
      </div>
      </div>
    </section>

    @endsection
