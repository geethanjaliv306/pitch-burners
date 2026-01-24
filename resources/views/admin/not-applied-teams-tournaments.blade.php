@extends('layouts.admin')

@section('content')
<style>
    .btn-reset {
        height: 40px;
        margin: 0;
    }
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
        border-radius: 20px;
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
        border-radius: 50%;
    }
    input:checked + .slider {
        background-color: #2196F3;
    }
    input:checked + .slider:before {
        transform: translateX(14px);
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
    .table th {
        background-color: #f1f1f1;
        color: #614092;
        font-family: "Saira", Arial, Helvetica, sans-serif;
        font-weight: 500;
    }
    .table th, .table td {
        height: 50px;
        vertical-align: middle;
        padding: 0px 20px;
        white-space: nowrap;
    }
  a{
    color: #008E9B;
    text-decoration: none;
  }
  .players-info-head {
    overflow-x: scroll;
}
 .table-responsive {
        overflow-x: auto;
    }
    @media (max-width: 768px) {
        .table th, .table td {
            font-size: 12px;
            padding: 10px;
        }
        .table th {
            font-size: 13px;
        }
      .view-teams{
        margin-top:10px !important;
      }
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
                    <h2>
                        <a class="my-torunments-back" href="{{ route('tournaments-teams', $tournament->id) }}"></a>
                        Not Applied Teams</h2>
                    <div class="d-flex">
    <a class="btn btn-yellow me-2" href="{{ route('notapplied.teams.csv', $tournament->id) }}">
        <i class="fa-solid fa-download me-2"></i>Download Teams
    </a>
    <a class="btn btn-yellow me-2" href="{{ route('notapplied.players.csv', $tournament->id) }}">
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
        <div class="d-flex" style="gap:32px;">
            <p>Total Teams: <strong style="color: black">{{ $total_team_count }}</strong></p>
        </div>
        <form method="GET" action="{{ route('tournaments-not-applied-teams', $tournament->id) }}">
            <div class="row mb-3">
                <div class="col-md-9">
                    <input type="text" name="search" class="form-control" placeholder="Search by Team Name" value="{{ $search }}">
                </div>
                <div class="col-md-3">
                    <a href="{{ route('tournaments-teams', $tournament->id) }}" class="btn btn-warning w-100 m-0 view-teams"style="
    height: 40px;
">View Applied Teams</a>
                </div>
                <div class="col-md-2 mt-2">
                    @if($search)
                        <a href="{{ route('tournaments-not-applied-teams', $tournament->id) }}" class="btn btn-secondary w-100 btn-reset">Reset</a>
                    @endif
                </div>
            </div>
        </form>
          <div class="table-responsive">
        <table class="table players-info-head">
            <thead>
                <tr>
                    <th>S.No</th>
                  <th>Members</th>
                    <th>Team Name</th>
                    <th>Match Preferences</th>
                    <th>Bonafide</th>
                    <th>Payment</th>
                    <th>Verified</th>
                    <th>Edit Access</th>
                </tr>
            </thead>
            <tbody>
                @if($notAppliedTeams->isEmpty())
                    <tr>
                        <td colspan="8" class="text-center">No records found</td>
                    </tr>
                @else
                    @foreach($notAppliedTeams as $index => $team)
                        <tr>
                            <td>{{ $notAppliedTeams->firstItem() + $index }}</td>
                            <td>
                               <div class="myTorunments-body-col name has-myTorunments-myTeam">
                               <a class="team_name" href="{{ route('teamplayers', ['team_id' => $team->id]) }}">{{ $team->name }}</a> </div></td>
                            <td>
                                        <div class="myTorunments-body-col name has-myTorunments-myTeam">
                                            {{$team->players_count}}
                                        </div>
                                    </td>
                          <td>-</td>
                            <td>
                                @if($team->bonafide)
                                    <a href="{{ config('constants.upload_url') . '/bonafide/' . $team->bonafide }}" target="_blank" class="btn btn-sm btn-info">View</a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>-</td>
                            <td>-</td>
                            <td>
                              {{--  <form action="{{ route('tournaments.teams.toggleAccess', [$tournament->id, $team->id]) }}" method="POST">
                                    @csrf
                                    <select name="access" onchange="this.form.submit()" class="form-select">
                                        <option value="1" {{ $team->is_added == 1 ? 'selected' : '' }}>Enable</option>
                                        <option value="2" {{ $team->is_added == 2 ? 'selected' : '' }}>Disable</option>
                                    </select>
                                </form> --}}
                                    -
                            </td>
                           
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
      </div>
        <!-- Pagination -->
        <div class="col-12 pagination-wrap">
            <nav>
                <ul class="pagination justify-content-center m-0">
                    {{ $notAppliedTeams->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
                </ul>
            </nav>
        </div>
    </div>
</section>

<script>
    @if(session('success'))
        document.getElementById('successMessage').style.display = 'block';

        setTimeout(function() {
            document.getElementById('successMessage').style.display = 'none';
        }, 3000);
    @endif
</script>
@endsection