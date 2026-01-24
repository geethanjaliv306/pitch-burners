@extends('layouts.admin')
@section('content')
<style>
    .disabled-team {
    opacity: 0.2;
    pointer-events: none;
}
.disabled-team label {
    cursor: not-allowed;
}
.disabled-team input[type="checkbox"] {
    pointer-events: none;
}
    .btn-yellow{
        width: auto;
    padding: 0 20px;
    height: 40px;
    font-size: 15px;
    text-transform: none;
    margin: 0;
    }
    .btn-reset{
        height:40px;
        margin:0px;
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
.table th{
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
.players-info-head{
    overflow-x: scroll;
}
.players-info-table{
    border-collapse: separate;
    border-spacing: 0 15px;
    width: 100%;
}
.has-myTorunments-myTeam .team_name{
    color: #008E9B;
    text-decoration: none;
}
  .btn{
    height:40px;
  }
 @media (max-width: 768px) {
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
                    <h2><a class="my-torunments-back" href="{{ route('tournaments.show', ['id' => $tournament->id]) }}"> </a>Teams</h2>
                   <div class="d-flex">
                        <a class="btn btn-yellow me-2" href="{{ route('alltournament_csv', ['tournament' => $tournament->id]) }}"><i class="fa-solid fa-download me-2"></i>Download Teams</a>
                        <a class="btn btn-yellow me-2" href="{{ route('alltournament_players_csv', ['tournament' => $tournament->id]) }}"><i class="fa-solid fa-download me-2"></i>Download Players</a>
                     	<a href="{{ route('export-tournament-teams-csv', ['tournament_id' => $tournament->id]) }}" class="btn btn-yellow me-2">Export Team CSVs</a>
                       <form 
    action="{{ route('sendNotificationsToAllTeams', ['tournament_id' => $tournament->id]) }}" 
    method="POST" 
    style="display: inline;" 
    onsubmit="return confirm('Are you sure you want to send mail to all team players?');">
    @csrf
    <button type="submit" class="btn btn-primary"><i class="fas fa-mail-bulk" aria-hidden="true" style="margin-right: 12px;"></i>Send All</button>
</form>

                    </div>
                    <a class="btn btn-yellow d-none" href="javascript:;" data-bs-target="#addTeamModal" data-bs-toggle="modal">Add a New Team</a>
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
        <form method="GET" action="{{ route('tournaments-teams', $tournament->id) }}" id="searchForm">
            <div class="row mb-3">
                <div class="col-md-9">
                    <input type="text" name="search" class="form-control" placeholder="Search by Team Name" value="{{ $search }}" onkeypress="if(event.keyCode === 13) { this.form.submit(); }">
                </div>
                <div class="col-md-3">
                    <a href="{{ route('tournaments-not-applied-teams', $tournament->id) }}" class="btn btn-warning w-100 m-0 view-teams">View Not Applied Teams</a>
                </div>

                <div class="col-md-2 mt-2">
                    @if($search)
                        <a href="{{ route('tournaments-teams', $tournament->id) }}" class="btn btn-secondary w-100 btn-reset">Reset</a>
                    @endif
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-12">
                <div class="players-info-head">
                    <table class="table players-info-head">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Team Name</th>
                              <th>Members</th>
                                <th>Match preferences</th>
                                <th>Bonafide</th>
                                <th>Payment</th>
                                <th>Verified</th>
                                <th>Edit Access</th>
                                <th>Test Emails</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($paginatedTeams->isEmpty())
                                <tr>
                                    <td colspan="7">
                                        <div class="no-records-found">
                                            <p class="text-center m-3">No records found</p>
                                        </div>
                                    </td>
                                </tr>
                            @else
                            @foreach($paginatedTeams as $index => $team)
                                <tr>
                                    <td>
                                        <div class="myTorunments-body-col sno has-myTorunments-myTeam">{{ $paginatedTeams->firstItem() + $index }}</div>
                                    </td>
                                    <td>
                                        <div class="myTorunments-body-col name has-myTorunments-myTeam">
                                            <a class="team_name" href="{{ route('team_players', ['team_id' => $team->team_id]) }}">{{ $team->name }}</a>
                                        </div>
                                    </td>

                                  <td>
                                      <div class="myTorunments-body-col name has-myTorunments-myTeam">
                                          @if(isset($team->green_tennis_count) && $team->green_tennis_count > 0 && $team->tournament_type === 'Green Tennis')
                                              Green Tennis: {{ $team->green_tennis_count }} <br>
                                          @endif
                                          Total Player: {{ $team->players_count }}
                                      </div>
                                  </td>

                                    <td>
                                        <div class="myTorunments-body-col name has-myTorunments-myTeam">
                                            <span>{{ $team->formatted_preference }}</span>
                                            <button class="edit-btn" data-team-id="{{ $team->team_id }}" data-preference="{{ $team->match_preference }}"style="
                                                border: none; background:none;
                                            ">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                  </td>
                                    <td>
                                        <div class="myTorunments-body-col name has-myTorunments-myTeam">
                                            @if($team->team_bonafide)
                                                <a href="{{  config('constants.upload_url') . '/bonafide/' . $team->team_bonafide }}"
                                                target="_blank" 
                                                class="btn btn-sm btn-info" 
                                                title="View Bonafide Certificate">
                                                    View
                                                </a>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="myTorunments-body-col sno has-myTorunments-myTeam">
                                            <form action="{{ route('tournaments.teams.togglePayment', [$tournament->id, $team->team_id]) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <label class="switch">
                                                    <input type="checkbox" name="payment"
                                                        onclick="return confirm('Are you sure you want to change the payment status?')"
                                                        onchange="this.form.submit()"
                                                        {{ $team->payment == 1 ? 'checked' : '' }}>
                                                    <span class="slider round"></span>
                                                </label>
                                            </form>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="myTorunments-body-col sno has-myTorunments-myTeam">
                                            <form action="{{ route('tournaments.teams.toggleVerified', [$tournament->id, $team->team_id]) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <label class="switch">
                                                    <input type="checkbox" name="verified"
                                                        onclick="return confirm('Are you sure you want to change the verification status?')"
                                                        onchange="this.form.submit()"
                                                        {{ $team->verified == 1 ? 'checked' : '' }}>
                                                    <span class="slider round"></span>
                                                </label>
                                            </form>
                                        </div>                                
                                    </td>
                                    <td>
                                        <div class="myTorunments-body-col actions has-myTorunments-myTeam">
                                            <form action="{{ route('tournaments.teams.toggleAccess', [$tournament->id, $team->team_id]) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <select name="access" onchange="this.form.submit()" class="form-select">
                                                    <option value="1" {{ $team->is_added == 1 ? 'selected' : '' }}>Enable</option>
                                                    <option value="2" {{ $team->is_added == 2 ? 'selected' : '' }}>Disable</option>
                                                </select>
                                            </form>
                                        </div>                                
                                    </td> <td>
                                        <div class="myTorunments-body-col actions has-myTorunments-myTeam">
                                            <form action="{{ route('send.team.notification', ['tournament_id' => $tournament->id, 'team_id' => $team->team_id]) }}" method="POST" style="display: inline;" 
    onsubmit="return confirm('Are you sure you want to send mail to this team players?');">
                                                @csrf
                                                <button type="submit" class="btn btn-primary">
													<i class="fas fa-envelope" aria-hidden="true" style="margin-right: 12px;"></i>
													Send
												</button>
                                            </form>
                                        </div>
                                    </td>
                                    <td>
                                       <div class="myTorunments-body-col actions has-myTorunments-myTeam">
                                         <div class="delete icon">
                                           <form action="{{ route('tournaments.teams.destroy', [$tournament->id, $team->team_id]) }}" method="POST" style="display: inline;">
                                             @csrf
                                             @method('DELETE')
                                             <button type="submit" style="border: none; background: none; cursor: pointer;" onclick="return confirm('Are you sure you want to remove this team from the tournament?')">
                                               <i>
                                                 <img src="{{ asset('uploads/images/delete.png') }}"style="width: 25px;height: 25px;" />
                                               </i>
                                             </button>
                                           </form>
                                         </div>
                                      </div>
                                    </td>
                                    <td class="d-none">
                                        <div class="myTorunments-body-col actions has-myTorunments-myTeam d-none">
                                            <div class="edit icon d-none" data-bs-toggle="modal" data-bs-target="#editTeamModal" data-team-id="{{ $team->team_id }}" data-team-name="{{ $team->name }}">
                                                <i>
                                                    <img src="{{ asset('/uploads/images/pen.svg') }}" />
                                                </i>
                                            </div>
                                            <div class="delete icon d-none">
                                                <form action="{{ route('tournaments.teams.destroy', [$tournament->id, $team->team_id]) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" style="border: none; background: none; cursor: pointer;" onclick="return confirm('Are you sure you want to remove this team from the tournament?')">
                                                        <i>
                                                            <img src="{{ asset('uploads/images/delete.png') }}" />
                                                        </i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>                                
                                    </td> 
                                </tr>
                            @endforeach 
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-12 pagination-wrap">
                <div class="container position-relative">
                    <nav>
                        <ul class="pagination justify-content-center m-0">
                        {{-- {{ $paginatedTeams->links('pagination::bootstrap-4') }} --}}
                        {{ $paginatedTeams->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
                        </ul>
                    </nav>
                    <a class="goToRounds btn btn-yellow" href="{{ route('tournaments-group', ['tournament_id' => $tournament->id]) }}">Next</a>
                </div>
            </div>
        </div>
    </div>
   <div class="modal fade" id="editPreferenceModal" tabindex="-1" aria-labelledby="editPreferenceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="updatePreferenceForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPreferenceModalLabel">Edit Match Preference</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label for="matchPreference">Match Preference</label>
                        <select name="match_preference" id="matchPreference" class="form-select">
                            <option value="1">Any Day</option>
                            <option value="2">Only Saturdays</option>
                            <option value="3">Only Sundays</option>
                            <option value="4">Only First Week</option>
                            <option value="5">Only Second Week</option>
                            <option value="6">Only Third Week</option>
                        </select>
                        <input type="hidden" id="teamId" name="team_id">
                    </div>
                    <div class="modal-footer"style="
                    justify-content: center;
                ">
                        <button type="submit" class="btn btn-primary w-50 m-0">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.js" integrity="sha512-+k1pnlgt4F1H8L7t3z95o3/KO+o78INEcXTbnoJQ/F2VqDVhWoaiVml/OEHv9HsVgxUaVW+IbiZPUJQfF/YxZw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    var tournamentId = {{ $tournament->id }};
    var selectedTeams = {!! json_encode($tournament->teams->pluck('id')) !!};
    var allTeams = {!! json_encode($allTeams) !!};
    $(document).ready(function() {
    // Handle search
    $('#search_team').on('keyup', function() {
        let query = $(this).val(); // Get the search input value
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        if (query.length > 0) {
            $.ajax({
                url: "/tournaments/search-teams", // Use the correct route
                method: "POST",
                headers: {
                        'X-CSRF-Token': csrfToken
                },
                data: { queryText: query, tournamentId }, // Send search query
                success: function(data) {
                    $('#team-list').empty(); // Clear current team list
                    if (data.length > 0) {
                        // If there are matching teams, display them
                        data.forEach(function(team) {
                            let isTeamSelected = selectedTeams.includes(team.id);
                            let teamHTML = `
                                <div class="playingsquad-remainning-item ${isTeamSelected ? 'disabled-team' : ''}" data-team-id="${team.id}">
                                    <input type="checkbox" id="groupTeams_${team.id}" name="groupTeams[]" value="${team.id}" ${isTeamSelected ? 'disabled' : ''} />
                                    <label for="groupTeams_${team.id}">
                                        <figure>
                                            <img src="/storage/uploads/team_logos/${team.logo}" alt="${team.name}" />
                                        </figure>
                                        <figcaption>${team.name}</figcaption>
                                    </label>
                                </div>
                            `;
                            $('#team-list').append(teamHTML); // Append new teams
                        });
                    } else {
                        // If no teams match, display a message
                        $('#team-list').append('<div>No teams found.</div>');
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText); // Log any errors
                }
            });
        } else {
            // If the search box is empty, restore original teams
            restoreOriginalTeams();
            $('#team-list').hide();
        }
    });
    // Function to restore original teams
    function restoreOriginalTeams() {
        $('#team-list').empty(); // Clear the list
        allTeams.forEach(function(team) {
            let isTeamSelected = selectedTeams.includes(team.id);
            let teamHTML = `
                <div class="playingsquad-remainning-item ${isTeamSelected ? 'disabled-team' : ''}" data-team-id="${team.id}">
                    <input type="checkbox" id="groupTeams_${team.id}" name="groupTeams[]" value="${team.id}" ${isTeamSelected ? 'disabled' : ''} />
                    <label for="groupTeams_${team.id}">
                        <figure>
                            <img src="/storage/uploads/team_logos/${team.logo}" alt="${team.name}" />
                        </figure>
                        <figcaption>${team.name}</figcaption>
                    </label>
                </div>
            `;
            $('#team-list').append(teamHTML); // Append original teams
        });
    }
    // Handle Select All functionality
    $('#selectAllTeams').change(function() {
        $('#team-list input[type="checkbox"]:not(:disabled)').prop('checked', this.checked);
    });
});
</script>
<script>
 document.addEventListener('DOMContentLoaded', function () {
    const selectAllCheckbox = document.getElementById('selectAllTeams');
    const teamCheckboxes = document.querySelectorAll('input[name="groupTeams[]"]');
    const editButtons = document.querySelectorAll('.edit.icon');
    const editTeamModal = new bootstrap.Modal(document.getElementById('editTeamModal'));
    const addTeamModal = new bootstrap.Modal(document.getElementById('addTeamModal'));
    @if ($errors->has('groupTeams'))
        addTeamModal.show();
    @endif
    selectAllCheckbox.addEventListener('change', function () {
        teamCheckboxes.forEach(checkbox => {
            if (!checkbox.disabled) {
                checkbox.checked = selectAllCheckbox.checked;
            }
        });
    });
    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            const teamId = button.dataset.teamId;
            const teamName = button.dataset.teamName;
            document.getElementById('editTeamId').value = teamId;
            document.getElementById('editTeamName').value = teamName;
            const form = document.getElementById('editTeamForm');
            form.action = form.action.replace('team_id_placeholder', teamId);
            editTeamModal.show();
        });
    });
});
</script>
<script>
    @if(session('success'))
          // Show the alert message
          document.getElementById('successMessage').style.display = 'block';

          // Hide the message after 3 seconds
          setTimeout(function() {
              document.getElementById('successMessage').style.display = 'none';
              // Redirect to a specific page if needed
          }, 5000);
      @endif
  </script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
    const editButtons = document.querySelectorAll('.edit-btn');
    const modal = new bootstrap.Modal(document.getElementById('editPreferenceModal'));
    const form = document.getElementById('updatePreferenceForm');

    editButtons.forEach(button => {
        button.addEventListener('click', () => {
            const teamId = button.getAttribute('data-team-id');
            const preference = button.getAttribute('data-preference');

            document.getElementById('teamId').value = teamId;
            document.getElementById('matchPreference').value = preference;

            modal.show();
        });
    });

    form.addEventListener('submit', (event) => {
        event.preventDefault();

        const teamId = document.getElementById('teamId').value;
        const matchPreference = document.getElementById('matchPreference').value;
        const tournamentId = "{{ $tournament->id }}";

        fetch(`/tournaments/${tournamentId}/teams/${teamId}/update-preference`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            body: JSON.stringify({ match_preference: matchPreference })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Refresh the page to reflect changes
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    });
});

  </script>
@endsection