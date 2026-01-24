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
    .myTorunments-table .myTorunments-head .myTorunments-head-col.roundName, .myTorunments-table .myTorunments-head .myTorunments-head-col.teams, .myTorunments-table .myTorunments-head .myTorunments-body-col.roundName, .myTorunments-table .myTorunments-head .myTorunments-body-col.teams, .myTorunments-table .myTorunments-body .myTorunments-head-col.roundName, .myTorunments-table .myTorunments-body .myTorunments-head-col.teams, .myTorunments-table .myTorunments-body .myTorunments-body-col.roundName, .myTorunments-table .myTorunments-body .myTorunments-body-col.teams{
        flex: 1 0 55.3333% !important;
        max-width: 72.3333% !important;
    }
    .myTorunments-table .myTorunments-head .myTorunments-head-col.roundName, .myTorunments-table .myTorunments-head .myTorunments-head-col.teams, .myTorunments-table .myTorunments-head .myTorunments-body-col.roundName, .myTorunments-table .myTorunments-head .myTorunments-body-col.teams, .myTorunments-table .myTorunments-body .myTorunments-head-col.roundName, .myTorunments-table .myTorunments-body .myTorunments-head-col.teams, .myTorunments-table .myTorunments-body .myTorunments-body-col.roundName, .myTorunments-table .myTorunments-body .myTorunments-body-col.teams{
        align-items: center;
    }
     .btn-reset{
        height: 40px;
        margin: 0px;
            }
</style>

@php
// print_r($allTeams);
// exit;
@endphp
<div class="alert-message" id="successMessage">
     {{ session('success') }}
  </div>
    <section class="my-torunments-second-header fixed-second-header">
        <div class="container h-100">
            <div class="row h-100">
                <div class="col-12">
                    <div class="title-wrap h-100">
                        <h2><a class="my-torunments-back" href="{{ route('tournaments-teams', ['tournament' => $tournament->id]) }}"></a>Groups</h2>
                        <a class="btn btn-yellow" href="javascript:;" data-bs-target="#addGroupModal" data-bs-toggle="modal">Add New Groups</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="my-torunments-wrap">
        <div class="container ">
            <form method="GET" action="{{ route('tournaments-group', ['tournament_id' => $tournament->id]) }}" id="searchForm">
                <div class="row mb-3">
                    <div class="col-md-10">
                        <input type="text" name="group_name" class="form-control" placeholder="Search by Group Name" value="{{ request()->input('group_name') }}" onkeypress="if(event.keyCode === 13) { this.form.submit(); }">
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('tournaments-group.export', ['tournament_id' => $tournament->id]) }}" class="btn btn-primary w-100 m-0 gap-3" title="Export">
                            <i class="fas fa-download "></i> Export
                        </a>
                    </div>
                    {{-- <div class="col-md-4">
                        <input type="text" name="team_name" class="form-control" placeholder="Search by Team Name" value="{{ request()->input('team_name') }}" onkeypress="if(event.keyCode === 13) { this.form.submit(); }">
                    </div> --}}
                    <div class="col-md-2">
                        @if(request()->input('group_name') || request()->input('team_name'))
                            <a href="{{ route('tournaments-group', ['tournament_id' => $tournament->id]) }}" class="btn btn-secondary w-100 btn-reset">Reset</a>
                        @endif
                    </div>
                </div>
            </form>
            <div class="row">
                <div class="col-12">
                    <div class="myTorunments-table">
                        <div class="myTorunments-head">
                            <div class="myTorunments-head-col sno">S.No</div>

                            <div class="myTorunments-head-col groupName">Group Name</div>
                            <div class="myTorunments-head-col teams">Teams</div>
                            {{-- <div class="myTorunments-head-col roundName">Round Name</div> --}}
                            <div class="myTorunments-head-col actions">Actions</div>
                        </div>
                        @if($groups->isEmpty())
                        <div class="no-records-found">
                          <p class="text-center m-3">No records found</p>
                        </div>
                      @else
                        @foreach($groups as $group_id => $groupedTeams)
                            <div class="myTorunments-body">
                                <div class="myTorunments-body-col sno has-myTorunments-myGroups">{{ $loop->iteration }}</div>
                                <div class="myTorunments-body-col groupName has-myTorunments-myGroups">{{ $groupedTeams->first()->group->group_name }}</div>
                                <div class="myTorunments-body-col teams has-myTorunments-myGroups">
                                    @foreach($groupedTeams as $team)
                                        {{ $team->team->name }}@if(!$loop->last), @endif
                                    @endforeach
                                </div>
                                <div class="myTorunments-body-col actions has-myTorunments-myGroups">
                                    <div class="edit icon" data-bs-target="#editGroupModal_{{ $group_id }}" data-bs-toggle="modal">
                                        <i><img src="{{ asset('/uploads/images/pen.svg') }}" /></i>
                                    </div>
                                    <div class="delete icon" data-id="">
                                        <form action="{{ route('delete-tournaments-group', $group_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this group?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background:none; border:none; cursor:pointer;">
                                                <i><img src="{{ asset('uploads/images/delete.png')}}" alt="Delete" /></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        @endif
                    </div>
                </div>
                <div class="col-12 pagination-wrap">
                    <div class="container position-relative">

                    <a class="goToRounds btn btn-yellow " href="{{ route('tournaments-round', ['tournament' => $tournament->id]) }}">Next</a>
                    </div>
                </div>

                {{-- <div class="col-12 pagination-wrap">
                    <div class="container position-relative">
                        <a class="goToRounds btn btn-yellow {{ $groups->isEmpty() || !$newGroupAdded ? 'disabled' : '' }}" href="{{ route('tournaments-round', ['tournament' => $tournament->id]) }}" {{ $groups->isEmpty() || !$newGroupAdded ? 'aria-disabled="true"' : '' }}>
                            Next
                        </a>
                    </div>
                </div> --}}
            </div>
        </div>
    </section>

    {{-- @php
$selectedTeamIds = $groups->flatMap(function($groupedTeams) {
    return $groupedTeams->pluck('team.id');
})->unique()->toArray();
@endphp --}}

    <!-- Modal for Adding a Group -->
     <form id="groupForm" action="{{ route('tournaments-group.store') }}" method="POST">
      @csrf
      <input type="hidden" name="tournament_id" value="{{ $tournament->id }}">
      <div class="modal fade addGroupModal modalOpeninBottomtoTop-mobile" id="addGroupModal" tabindex="-1" aria-labelledby="addGroupModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-scrollable">
              <div class="modal-content">
                  <div class="modal-header">
                      <h2>Add Group</h2>
                      <div class="top">
                          {{-- <div class="field-item">
                              <label for="selectRound" class="form-label">Select Round</label>
                              <select class="form-select" id="selectRound" name="round_type" aria-label="Default select example">
                                  @foreach($rounds as $round)
                                      <option value="{{ $round->type }}">{{ $round->type }}</option>
                                  @endforeach
                              </select>
                          </div> --}}
                          <div class="field-item">
                              <label for="group_name" class="form-label">Group Name</label>
                              <input type="text" class="form-control" id="group_name" name="group_name" placeholder="Group Name(e.g. Group A or Group Stage)">
                              <span id="group_name_error" class="text-danger" style="display:none;">Group name is required.</span>
                            </div>
                          <div class="field-item d-flex align-items-center d-none">
                            <label for="search_team" class="form-label me-2">Search Team:</label>
                            <input type="search" class="form-control" id="search_team" placeholder="Search for a groups...">
                        </div>
                      </div>
                  </div>
                  <div class="modal-body">
                    <h2>Select Teams:</h2>
                    <div class="addGroupModal-wrap" id="team-list">
                        {{-- @if($hasCompletedRound && !empty($qualifiedTeams))
                        @foreach($qualifiedTeams as $team)
                                <div class="playingsquad-remainning-item">
                                    <input type="checkbox" id="groupTeams_{{ $team->id }}" name="team_ids[]" value="{{ $team->id }}" />
                                    <label for="groupTeams_{{ $team->id }}">
                                        <figure>
                                            <img src="{{ asset('/storage/uploads/team_logos/' . $team->logo) }}" alt="{{ $team->name }}" />
                                        </figure>
                                        <figcaption>{{ $team->name }}</figcaption>
                                    </label>
                                </div>
                            @endforeach
                        @else --}}
                        {{-- @foreach($allTeams as $team)
                        <div class="playingsquad-remainning-item {{ in_array($team->id, $selectedTeamIds) ? 'disabled-team' : '' }}">
                            <input type="checkbox" id="groupTeams_{{ $team->id }}" name="team_ids[]" value="{{ $team->id }}"
                                   {{ in_array($team->id, $selectedTeamIds) ? 'disabled' : '' }} />
                            <label for="groupTeams_{{ $team->id }}">
                                <figure><img src="{{ asset('/storage/uploads/team_logos/' . $team->logo) }}" alt="{{ $team->name }}" /></figure>
                                <figcaption>{{ $team->name }}</figcaption>
                            </label>
                        </div>
                    @endforeach --}}
                    @foreach($allTeams->sortBy('name') as $team)
                    <div class="playingsquad-remainning-item">
                        <input type="checkbox" id="groupTeams_{{ $team->id }}" name="team_ids[]" value="{{ $team->id }}" />
                        <label for="groupTeams_{{ $team->id }}">
                            <figure>
                                <img src="{{ config('constants.upload_url') . '/team_logos/' . $team->logo }}" alt="{{ $team->name }}" />                            </figure>
                            <figcaption>{{ $team->name }}</figcaption>
                        </label>
                    </div>
                @endforeach
                        {{-- @endif --}}
                    </div>
                    <span id="team_ids_error" class="text-danger" style="display:none;">At least one team must be selected.</span>
                </div>


                  <div class="modal-footer d-flex justify-content-center">
                      <a href="javascript:;" class="btn btn-outline-gray" data-bs-dismiss="modal">Cancel</a>
                      <button type="submit" class="btn btn-primary">Add Group</button>
                  </div>
              </div>
          </div>
      </div>
  </form>

  @foreach($groups as $group_id => $groupedTeams)
<form action="{{ route('tournaments-group.update', ['group_id' => $group_id]) }}" method="POST">
    @csrf
    @method('PUT')
    <input type="hidden" name="tournament_id" value="{{ $tournament->id }}">
    <div class="modal fade addGroupModal modalOpeninBottomtoTop-mobile" id="editGroupModal_{{ $group_id }}" tabindex="-1" aria-labelledby="editGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Edit Group</h2>
                    <div class="top">
                        {{-- <div class="field-item">
                            <label for="edit_round_type_{{ $group_id }}" class="form-label">Select Round</label>
                            <select class="form-select" id="edit_round_type_{{ $group_id }}" name="round_type">
                                @foreach($rounds as $round)
                                    <option value="{{ $round->type }}" {{ $groupedTeams->first()->round_type == $round->type ? 'selected' : '' }}>
                                        {{ $round->type }}
                                    </option>
                                @endforeach
                            </select>
                        </div> --}}
                        <div class="field-item">
                            <label for="edit_group_name_{{ $group_id }}" class="form-label">Group Name</label>
                            <input type="text" class="form-control" id="edit_group_name_{{ $group_id }}" name="group_name" value="{{ $groupedTeams->first()->group->group_name }}">
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <h2>Select Teams:</h2>
                    <div class="addGroupModal-wrap">
                        @foreach($allTeamss as $team)
                            <div class="playingsquad-remainning-item">
                                <input type="checkbox" id="edit_team_{{ $team->id }}_{{ $group_id }}" name="team_ids[]" value="{{ $team->id }}"
                                    {{ in_array($team->id, $groupTeams[$group_id]) ? 'checked' : '' }}>
                                <label for="edit_team_{{ $team->id }}_{{ $group_id }}">
                                    <figure><img src="{{ config('constants.upload_url') . '/team_logos/' . $team->logo }}" /></figure>
                                    <figcaption>{{ $team->name }}</figcaption>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <a href="javascript:;" class="btn btn-outline-gray" data-bs-dismiss="modal">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Group</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endforeach


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.js" integrity="sha512-+k1pnlgt4F1H8L7t3z95o3/KO+o78INEcXTbnoJQ/F2VqDVhWoaiVml/OEHv9HsVgxUaVW+IbiZPUJQfF/YxZw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
  $(document).ready(function() {
    const currentRoundId = "{{ $currentRoundId ?? 'default' }}"; // Set the current round ID from the backend
    let selectedTeamIds = new Set(); // Keep track of selected team IDs

    // Populate selectedTeamIds with teams already in groups for the current round, only if the round is not completed
    if (!{{ json_encode($hasCompletedRound) }}) {
        @foreach ($groups as $groupedTeams)
            @foreach ($groupedTeams as $team)
                selectedTeamIds.add("{{ $team->team_id }}");
            @endforeach
        @endforeach
    }

    // Handle search input
    $('#search_team').on('keyup', function() {
        let query = $(this).val();
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        let tournamentId = {{ $tournament->id }};

        if (query.length > 0) {
            $.ajax({
                url: "/tournaments/search-group",
                method: "POST",
                headers: {
                    'X-CSRF-Token': csrfToken
                },
                data: { queryText: query, tournamentId },
                success: function(data) {
                    $('#team-list').empty();

                    if (data.length > 0) {
                        data.forEach(function(group) {
                            // Only add the team if it's not already selected in the current round
                            if (!selectedTeamIds.has(group.id.toString()) || {{ json_encode($hasCompletedRound) }}) {
                                let teamHTML = `
                                    <div class="playingsquad-remainning-item" data-round-id="${currentRoundId}">
                                        <input type="checkbox" id="groupTeams_${group.id}" name="team_ids[]" value="${group.id}" />
                                        <label for="groupTeams_${group.id}">
                                            <figure>
                                                <img src="/storage/uploads/team_logos/${group.team_logo}" alt="${group.team_name}" />
                                            </figure>
                                            <figcaption>${group.team_name}</figcaption>
                                        </label>
                                    </div>
                                `;
                                $('#team-list').append(teamHTML);
                            }
                        });
                    } else {
                        $('#team-list').append('<div>No groups or teams found.</div>');
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        } else {
            restoreOriginalTeams();
            $('#team-list').show();
        }
    });

    // Function to restore original teams, including previously selected ones if the round is completed
    // function restoreOriginalTeams() {
    //     $('#team-list').empty();
    //     let teams = {!! json_encode($qualifiedTeams ?? $allTeams) !!};

    //     teams.forEach(function(team) {
    //         // Show all teams if the round is complete, otherwise filter out selected teams
    //         if (!selectedTeamIds.has(team.id.toString()) || {{ json_encode($hasCompletedRound) }}) {
    //             let teamHTML = `
    //                 <div class="playingsquad-remainning-item" data-round-id="${currentRoundId}">
    //                     <input type="checkbox" id="groupTeams_${team.id}" name="team_ids[]" value="${team.id}" />
    //                     <label for="groupTeams_${team.id}">
    //                         <figure>
    //                             <img src="/storage/uploads/team_logos/${team.logo}" alt="${team.name}" />
    //                         </figure>
    //                         <figcaption>${team.name}</figcaption>
    //                     </label>
    //                 </div>
    //             `;
    //             $('#team-list').append(teamHTML);
    //         }
    //     });
    // }

    // // Initial call to populate the team list based on round status
    // restoreOriginalTeams();
 });

</script>

<script>
$(document).ready(function() {
  // Hide error message when user starts typing in the group name field
  $('#group_name').on('input', function() {
    $('#group_name_error').hide();
  });

  $('#groupForm').on('submit', function(e) {
    e.preventDefault();

    // Hide previous error messages
    $('#group_name_error').hide();
    $('#team_ids_error').hide();

    var groupName = $('#group_name').val().trim();
    var selectedTeams = $('input[name="team_ids[]"]:checked').length;

    console.log('Group Name:', groupName);  
    console.log('Selected Teams:', selectedTeams);  

    var isValid = true;

    // Check if the group name is empty
    if (groupName === '') {
      $('#group_name_error').show();
      isValid = false;
    }

    // Check if no teams are selected
    if (selectedTeams === 0) {
      $('#team_ids_error').show();
      isValid = false;
    }

    // If form is valid, submit the form
    if (isValid) {
      this.submit();
    }
  });
});


    // Check if there's a success message in the session
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

@endsection
