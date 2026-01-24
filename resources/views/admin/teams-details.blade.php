@extends('layouts.admin')

@section('content')
<style>
    .edit-player, .delete-player{
        width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #e0e1e1;
    cursor: pointer;
    }
    .edit-player img, .delete-player img{
        width: 20px;
    height: auto;
    }
    .edit-player-btn, .delete-btn{
        margin: 0px;
      	padding: 0px;
    }
  
    .actions-column{
        display: flex;
        gap: 5px;
        align-items: center;
    }

    table th, table td {
        white-space: nowrap;
    }
  
    .table-responsive {
        overflow-x: auto;
    }

    @media (max-width: 768px) {
        .table {
            font-size: 0.9rem;
        }
    }
      .title-wrap {
    display: flex;
    justify-content: space-between;
    align-items: center;
    }

    .team-info label {
        margin-bottom: 0.25rem;
        color: #6c757d;
    }

    .team-info p {
        margin-bottom: 0.5rem;
        font-size: 1.1rem;
    }

    .no-logo-placeholder {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        color: #6c757d;
        border: 2px dashed #dee2e6;
    }
    </style>
<section class="my-torunments-second-header fixed-second-header">

    <div class="container h-100">
        <div class="row h-100">
            <div class="col-12">
                <div class="title-wrap h-100">
                    <h2>
                            <a class="my-torunments-back" href="{{ route('total-teams') }}"> </a>
                            {{ $team->name }}
                    </h2>
					<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#viewTeamModal">
                        View Team Details
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="my-torunments-wrap">
    <div class="container">
        <h3 class="mb-3">Applied Tournaments</h3>
        @if($tournaments->isEmpty())
            <p class="text-center">No tournaments found for this team.</p>
        @else
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Tournament Name</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tournaments as $index => $tournament)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <a href="{{ route('tournaments.show', ['id' => $tournament->tournament_id]) }}"
                                 >{{ $tournament->tournament_name }}
                                </a>
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($tournament->start_date)->format('d/m/Y') }} -
                                {{ \Carbon\Carbon::parse($tournament->end_date)->format('d/m/Y') }}
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
          </div>
        @endif
    </div>

    <div class="container">
        <h3 class="mb-3">Players</h3>
        @if($players->isEmpty())
            <p class="text-center">No players found for this team.</p>
        @else
        <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>ID</th>
                    <th>Phone</th>
                    <th>Image</th>
                    <th>Batting</th>
                    <th>Bowling</th>
                    <th>Role</th>
                    <th>Ball Preferences</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($players as $index => $player)
                    <tr>
                        <td style=" vertical-align: middle; ">{{ $index + 1 }}</td>
                        <td style=" vertical-align: middle; ">{{ $player->name }}@if($player->is_captain == 1) (C)@endif</td>
                        <td style=" vertical-align: middle; ">{{ $player->email }}</td>
                        <td style=" vertical-align: middle; ">{{ $player->empid }}</td>
                        <td style=" vertical-align: middle; ">{{ $player->phone }}</td>
                        <td style=" vertical-align: middle; ">
                            @if($player->image)
                                <img src="{{ config('constants.upload_url') . '/player_images/' . $player->image }}" alt="Player Image" style="width: 50px; height: 50px; border-radius: 50%;object-fit: cover;border: 1px solid #ddd;object-position: top;">
                            @else
                                No Image
                            @endif
                        </td>
                        <td class="text-center" style=" vertical-align: middle; ">{{ $player->batting_style ?? '-' }}</td>
                        <td class="text-center"  style=" vertical-align: middle; ">{{ $player->bowling_style ?? '-'}}</td>
                        <td style=" vertical-align: middle; ">{{ $player->role }}</td>
                        <td style=" vertical-align: middle; ">{{ $player->ball_preferences }}</td>
                        <td style=" vertical-align: middle; " class="actions-column">
                            <button class="btn edit-player-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#editPlayerModal"
                            data-id="{{ $player->id }}"
                            data-name="{{ $player->name }}"
                            data-email="{{ $player->email }}"
                            data-empid="{{ $player->empid }}"
                            data-phone="{{ $player->phone }}"
                            data-image="{{ $player->image }}"
                            data-batting-style="{{ $player->batting_style }}"
                            data-bowling-style="{{ $player->bowling_style }}"
                            data-role="{{ $player->role }}"
                            data-ball-preferences="{{ $player->ball_preferences }}">
                            <i class="edit-player">
                                <img src="{{ asset('/uploads/images/pen.svg') }}" />
                            </i>
                        </button>
                          
                        <form action="{{ route('players.player-delete', $player->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this player?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn delete-btn" type="submit">
                                <i class="delete-player">
                                    <img src="{{ asset('uploads/images/delete.png')}}" alt="Delete" />
                                </i>
                            </button>
                        </form>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
      </div>
        @endif


    </div>


    <div class="modal fade" id="editPlayerModal" tabindex="-1" aria-labelledby="editPlayerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPlayerModalLabel">Edit Player</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editPlayerForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" id="playerId" name="player_id">

                        <!-- Two-column layout -->
                        <div class="row">
                            <!-- First column -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editPlayerName" class="form-label">Player Name</label>
                                    <input type="text" class="form-control" id="editPlayerName" name="name">
                                    <span id="nameError" class="text-danger"></span>
                                </div>
                                <div class="mb-3">
                                    <label for="editPlayerEmail" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="editPlayerEmail" name="email">
                                    <span id="emailError" class="text-danger"></span>
                                </div>
                                <div class="mb-3">
                                    <label for="editPlayerEmpid" class="form-label">Employee ID</label>
                                    <input type="text" class="form-control" id="editPlayerEmpid" name="empid">
                                    <span id="empidError" class="text-danger"></span>
                                </div>
                                <div class="mb-3">
                                    <label for="editPlayerPhone" class="form-label">Phone Number</label>
                                    <input type="number" class="form-control" id="editPlayerPhone" name="phone">
                                    <span id="phoneError" class="text-danger"></span>
                                </div>
                                <div class="mb-3">
                                    <label for="editRole" class="form-label">Ball Preferences</label>
                                    <select class="form-select" id="ballPreferences" name="ball_preferences">
                                        <option value="" selected>Select Ball Type</option>
                                        <option value="All">All</option>
                                        <option value="Red Tennis">Red Tennis</option>
                                        <option value="Green Tennis">Green Tennis</option>
                                        <option value="White Ball">White Ball</option>
                                    </select>
                                    <span id="ballPreferencesError" class="text-danger"></span>
                                </div>
                            </div>

                            <!-- Second column -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editPlayerImage" class="form-label">Upload Player Image</label>
                                    <input type="file" class="form-control" id="editPlayerImage" name="image">
                                </div>
                                <div class="mb-3">
                                    <label for="editBattingStyle" class="form-label">Batting Style</label>
                                    <select class="form-select" id="editBattingStyle" name="batting_style">
                                        <option value="" selected>Select Batting Style</option>
                                        <option value="Right Hand">Right Hand Batsman</option>
                                        <option value="Left Hand">Left Hand Batsman</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="editBowlingStyle" class="form-label">Bowling Style</label>
                                    <select class="form-select" id="editBowlingStyle" name="bowling_style">
                                        <option value="" selected>Select Bowling Style</option>
                                        <option value="Right Arm Fast">Right Arm Fast Medium</option>
                                        <option value="Left Arm Fast">Left Arm Fast Medium</option>
                                        <option value="Right Arm Off">Right Arm Off Spin</option>
                                        <option value="Left Arm Off">Left Arm Off Spin</option>
                                        <option value="Right Arm Leg">Right Arm Leg Spin</option>
                                        <option value="Left Arm Leg">Left Arm Leg Spin</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="editRole" class="form-label">Player Role</label>
                                    <select class="form-select" id="editRole" name="role">
                                        <option value="" selected>Select Player Role</option>
                                        <option value="Batsman">Batsman</option>
                                        <option value="Bowler">Bowler</option>
                                        <option value="All-Rounder">All-Rounder</option>
                                        <option value="Wicketkeeper">Wicketkeeper</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
  <div class="modal fade" id="viewTeamModal" tabindex="-1" aria-labelledby="viewTeamModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewTeamModalLabel">Team Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    @if($team->logo)
                        <img src="{{ config('constants.upload_url') . '/team_logos/' . $team->logo }}" 
                             alt="{{ $team->name }}" 
                             style="width: 150px; height: 150px; border-radius: 50%;object-fit: contain;"
                             class="no-logo-placeholder">
                    @else
                        <div class="no-logo-placeholder">
                            No Logo Available
                        </div>
                    @endif
                </div>

                <div class="team-info">
                    <div class="mb-3">
                        <label class="fw-bold">Team Name:</label>
                        <p>{{ $team->name }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Email:</label>
                        <p>{{ $team->email }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Mobile Number:</label>
                        <p>{{ $team->phone }}</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    const editButtons = document.querySelectorAll('.edit-player-btn');
    const editForm = document.getElementById('editPlayerForm');

    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            const playerId = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const email = button.getAttribute('data-email');
            const empid = button.getAttribute('data-empid');
            const phone = button.getAttribute('data-phone');
            const battingStyle = button.getAttribute('data-batting-style');
            const bowlingStyle = button.getAttribute('data-bowling-style');
            const role = button.getAttribute('data-role');
            const ballPreferences = button.getAttribute('data-ball-preferences');

            // Populate modal fields
            editForm.action = `/players_edit/${playerId}`;
            document.getElementById('playerId').value = playerId;
            document.getElementById('editPlayerName').value = name;
            document.getElementById('editPlayerEmail').value = email;
            document.getElementById('editPlayerEmpid').value = empid;
            document.getElementById('editPlayerPhone').value = phone;
            document.getElementById('editBattingStyle').value = battingStyle;
            document.getElementById('editBowlingStyle').value = bowlingStyle;
            document.getElementById('editRole').value = role;
            document.getElementById('ballPreferences').value = ballPreferences;
        });
    });
});

</script>
@endsection
