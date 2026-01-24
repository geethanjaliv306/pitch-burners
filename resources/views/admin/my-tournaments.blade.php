@extends('layouts.admin')

@section('content')
<style>
    .btn-reset{
        height: 40px;
        margin: 0px;
    }
    @media (max-width: 768.98px) {
        .searchbox{
            margin-bottom:10px;
        }
    }
</style>
<div class="alert-message" id="successMessage">
   {{ session('success') }}
</div>
    <section class="my-torunments-second-header fixed-second-header">
      <div class="container-fluid h-100">
        <div class="row h-100">
          <div class="col-12">
              <div class="title-wrap h-100">
                <h2>Tournaments</h2>
                <a class="btn btn-yellow" href="{{ route('tournaments') }}">Add a New Tournament</a>
              </div>
          </div>
        </div>
      </div>
    </section>
    <section class="my-torunments-wrap">
      <div class="container-fluid next-to-sidebar">
           <form method="GET" action="{{ route('tournaments-view') }}" id="searchForm">
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="text" name="name" class="form-control searchbox" placeholder="Search by Tournament Name" value="{{ $searchName }}" onkeypress="if(event.keyCode === 13) { this.form.submit(); }">
                </div>
                <div class="col-md-3">
                    <input type="text" name="ball_type" class="form-control searchbox" placeholder="Search by Ball Type" value="{{ $searchBallType }}" onkeypress="if(event.keyCode === 13) { this.form.submit(); }">
                </div>
                <div class="col-md-3">
                    <input type="date" name="date" class="form-control searchbox" value="{{ $searchDate }}" onchange="this.form.submit();">
                </div>
                <div class="col-md-3">
                    @if($searchName || $searchBallType || $searchDate)
                        <a href="{{ route('tournaments-view') }}" class="btn btn-secondary w-100 btn-reset">Reset</a>
                    @endif
                </div>
            </div>
        </form>
        <div class="row">
          <div class="col-12">
            <div class="myTorunments-table">
              <div class="myTorunments-head">
                <div class="myTorunments-head-col sno">S.No</div>
                <div class="myTorunments-head-col name">Name</div>
                <div class="myTorunments-head-col ballType">Ball Type</div>
                <div class="myTorunments-head-col fromDate">From Date</div>
                <div class="myTorunments-head-col endDate">End Date</div>
                <div class="myTorunments-head-col actions">Actions</div>
              </div>
          @if($tournaments->isEmpty())
            <div class="no-records-found">
              <p class="text-center m-3">No records found</p>
            </div>
          @else
              @foreach($tournaments as $index => $tournament)
              <div class="myTorunments-body">
                  <div class="myTorunments-body-col sno">{{ $index + 1 }}</div>
                  <div class="myTorunments-body-col name">
                    <a href="{{ route('tournaments.show', $tournament->id) }}">{{ $tournament->name }}</a>
                  </div>
                  <div class="myTorunments-body-col ballType">{{ $tournament->ball_type }}</div>
                  <div class="myTorunments-body-col fromDate">{{ \Carbon\Carbon::parse($tournament->start_date)->format('M d, Y') }}</div>
                  <div class="myTorunments-body-col endDate">{{ \Carbon\Carbon::parse($tournament->end_date)->format('M d, Y') }}</div>
                  <div class="myTorunments-body-col actions">
                    <div class="edit icon">
                      <a href="{{ route('edit-tournament', $tournament->id) }}">
                        <i>
                        <img src="{{ asset('uploads/images/pen.svg')}}" />
                      </i>
                    </a>
                    </div>
                    <div class="delete icon">
                      <form action="{{ route('delete-tournament', $tournament->id) }}" method="POST">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="border-0" onclick="return confirm('Are you sure you want to delete this tournament?')">
                              <i><img src="{{ asset('uploads/images/delete.png') }}" /></i>
                          </button>
                      </form>
                  </div>
                    <div class="settings icon">
                      <div class="dropdown">
                        <i class="dropdown-toggle" id="tournamentsettings-dropdownMenu" data-bs-toggle="dropdown" aria-expanded="false">
                          <img src="{{ asset('uploads/images/settings.png')}}" />
                        </i>
                        <ul class="dropdown-menu tournamentsettings-dropdownMenu" aria-labelledby="tournamentsettings-dropdownMenu">
                          <li><a class="dropdown-item" href="{{ route('tournaments-teams', $tournament->id) }}">Teams</a></li>
                          <li><a class="dropdown-item" href="{{ route('tournaments-group', $tournament->id) }}">Groups</a></li>
                          <li><a class="dropdown-item" href="{{ route('tournaments-round', $tournament->id) }}">Rounds</a></li>
                        </ul>
                      </div>
                    </div>
                  </div>
              </div>
              @endforeach
          @endif
           <div class="col-12 pagination-wrap">
            <div class="container position-relative">
                <nav>
                    <ul class="pagination justify-content-center m-0">
                     {{-- {{ $tournaments->links('pagination::bootstrap-4') }} --}}
                    {{ $tournaments->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
                    </ul>
                </nav>
            </div>
        </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <script>
      // Check if there's a success message in the session
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
