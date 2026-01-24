<link rel="stylesheet" href="https://mdbgo.io/marta-szymanska/mdb5-demo-pro-design-blocks/css/mdb.min.css" />
<link rel="stylesheet" href="https://mdbgo.io/marta-szymanska/mdb5-demo-pro-design-blocks/dev/css/new-prism.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
.datepicker-toggle-icon{
    display: none !important;
}
   .btn-reset {
    height: 40px !important;
    margin: 0px !important;
}
</style>
@extends('layouts.admin')

@section('content')
<div class="alert-message" id="successMessage">
    {{ session('success') }}
  </div>
    <section class="my-torunments-second-header fixed-second-header">
      <div class="container h-100">
        <div class="row h-100">
          <div class="col-12">
              <div class="title-wrap h-100">
                <h2 ><a class="my-torunments-back" href="{{ route('tournaments-round', ['tournament' => $tournament->id]) }}"></a>Schedule Matches</h2>
                 <div class="d-flex">
                    <a class="btn btn-yellow me-2" href="{{ route('allmatchesdwnld', ['tournament' => $tournament->id]) }}"><i class="fa-solid fa-download me-2"></i>Download Matches</a>

                    <a class="btn btn-yellow" href="javascript:;" data-bs-target="#scheduleMatchModal" data-bs-toggle="modal">Schedule Match</a>
                    </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="my-torunments-wrap">
      <div class="container ">
 <form method="GET" action="{{ route('tournaments.match', ['tournament_id' => $tournament->id, 'round_id' => request('round_id')]) }}" id="searchForm">
        <div class="row mb-3">
            <div class="col-md-3">
                <input type="text" name="team" class="form-control searchbox" placeholder="Search by Team Name" value="{{ request('team') }}" onkeypress="if(event.keyCode === 13) { this.form.submit(); }">
            </div>
            <div class="col-md-3">
                <input type="text" name="ground" class="form-control" placeholder="Search by Ground Name" value="{{ request('ground') }}" onkeypress="if(event.keyCode === 13) { this.form.submit(); }">
            </div> 
            <div class="col-md-2">
                <input type="date" name="date" class="form-control searchbox" value="{{ request('date') }}" onchange="this.form.submit();">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-control searchbox" onchange="this.form.submit();">
                    <option value="">Match Status</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Live</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-2">
                @if(request('team') || request('ground') || request('date') || request('status'))
                    <a href="{{ route('tournaments.match', ['tournament_id' => $tournament->id, 'round_id' => request('round_id')]) }}" class="btn btn-secondary w-100 btn-reset">Reset</a>
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
                <div class="myTorunments-head-col ground">Ground</div>
                <div class="myTorunments-head-col matchTime">Match Time</div>
                <div class="myTorunments-head-col actions">Actions</div>
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
                  <div class="myTorunments-body-col ground">{{ $match->ground_name }}</div>
                 <div class="myTorunments-body-col matchTime">
                    {{ \Carbon\Carbon::parse($match->match_date_time)->format('M d, Y h:i A') }}
                </div> 
                  <div class="myTorunments-body-col actions">
                    <div class="edit icon">
                      <i>
                        <img src="{{ asset('/uploads/images/pen.svg') }}" data-bs-toggle="modal" data-bs-target="#editMatchModal" onclick="editMatch({{ $match->id }})" />
                      </i>
                    </div>                    
                      <form action="{{ route('delete.match', $match->id) }}"  method="POST" class="d-inline m-0" onsubmit="return confirm('Are you sure you want to delete this round?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete icon" style="background: none; border: none; cursor: pointer;">
                            <i>
                                <img src="{{ asset('uploads/images/delete.png')}}" />
                            </i>
                        </button>
                    </form>
                   {{--   <a href="{{ route('match.details', $match->id) }}">
        <button class="start-match-button" 
                  @if(in_array($match->status, ['Active', 'Completed','Cancelled'])) disabled @endif>
            Start
        </button>
    </a>--}}
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
                 {{-- {{ $matches->links('pagination::bootstrap-4') }} --}}
                  {{ $matches->appends(request()->except('page'))->links('pagination::bootstrap-4') }}

              </ul>
          </nav>
      </div>
      </div>
    </section>
   

    <div class="modal fade scheduleMatchModal modalOpeninBottomtoTop-mobile" id="scheduleMatchModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="scheduleMatchModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
          <div class="modal-content">
              <div class="modal-header">
                  <h2>Schedule a Match</h2>
                  <!-- Step Navigation -->
                  <ul class="nav nav-pills">
                      <li class="nav-item">
                          <a class="nav-link active" id="nav-step1" href="#">Step 1</a>
                      </li>
                      <li class="nav-item">
                          <a class="nav-link" id="nav-step2" href="#">Step 2</a>
                      </li>
                  </ul>
              </div>
             
              <div class="modal-body">
                <div id="errorMessages" class="alert alert-danger d-none"></div>
                  <div class="schedule-match-steps">
                    <form id="scheduleMatchForm" action="{{ route('schedule.match') }}" method="POST">
                      @csrf
                      <input type="hidden" name="tournament_id" value="{{ $tournament->id }}">
                      @foreach ($rounds as $round)
                      <input type="hidden" name="round_id" value="{{ $round->id }}">
                      @endforeach
                     <div id="step1" class="step-div">
                      <div style="
                      text-align: -webkit-center;">
                       <select class="form-select w-25" id="groupSelect" name="group_id" style="margin-left: 15px">
                        <option value="">Select Groups</option>
                        @foreach ($showgroups as $group)
                            <option value="{{ $group->id }}">{{ $group->group_name }}</option>
                        @endforeach
                    </select>
                    
                  </div>
                        <h2 class="mb-2">Select Team</h2>
                        <p class="mb-4">Selecting playing squad is not required if you are just scheduling matches of your tournament currently.</p>
                          <!-- Step 1 Content -->
                          <div class="select-team-wrap mb-4">
                            <div class="selectTeam teamA">
                                <div class="top">Team 1</div>
                                <div class="bottom" id="teamAList">
                                    <!-- Team 1 list will be populated here -->
                                </div>
                            </div>
                            <div class="selectTeam teamB">
                                <div class="top">Team 2</div>
                                <div class="bottom" id="teamBList">
                                    <!-- Team 2 list will be populated here -->
                                </div>
                            </div>
                        </div>
                          <div class="step-footer">
                              <p class="btn btn-outline-gray" data-bs-dismiss="modal">Cancel</p>
                              <button class="btn btn-primary" id="next1" type="button">Next</button>
                          </div>
                      </div>
                      <div id="step2" class="step-div d-none">
                          <!-- Step 2 Content -->
                          <h2>Match Details:</h2>
                          <div class="match-details-info">
                            @foreach ($rounds as $round)
                                <div class="newtournament-item">
                                    <label for="noofovers_{{ $round->id }}" class="form-label">No of Overs*</label>
                                    <input type="text" class="form-control" id="noofovers_{{ $round->id }}" name="number_of_overs" value="{{ $round->number_of_overs }}">
                                </div>
                                <div class="newtournament-item">
                                    <label for="oversperbowler_{{ $round->id }}" class="form-label">Overs Per Bowler</label>
                                    <input type="text" class="form-control" id="oversperbowler_{{ $round->id }}" name="overs_per_bowler" value="{{ $round->overs_per_bowler }}">
                                </div>
                            @endforeach
                          <div class="newtournament-item">
                            <label for="selectRound" class="form-label">Select Ground</label>
                            <select class="form-select" id="selectRound" name="ground" aria-label="Default select example">
                              @foreach($groundArray as $id => $groundName)
                                  <option value="{{ $id }}">{{ $groundName }}</option>
                              @endforeach
                          </select>                          
                        </div>
                          <div class="newtournament-item">
                             <div class="start-end-date-item">
                              <label for="startDate" class="form-label">Start Date</label>
                             
                                <input type="datetime-local"
                                       class="form-control"
                                       id="datetimepickerExample"
                                       name="match_date_time"
                                         min="{{ \Carbon\Carbon::parse($tournament->start_date)->format('d/m/Y') }}"
                                       max="{{ \Carbon\Carbon::parse($tournament->end_date)->format('d/m/Y') }}">


                          </div>
                          </div>
                          <!-- Ball Type Section -->
                          <div class="newtournament-item">
                              <label class="mb-2">Select Ball Type</label>
                              <div class="tournament_category">
                                <div class="customRadioSelect ballTypeSelect">
                                  <input type="radio" id="ballType_1" name="type" value="Red Tennis" {{ $tournament->ball_type == 'Red Tennis' ? 'checked' : '' }} />
                                  <label for="ballType_1">Red Tennis</label>
                                </div>
                                <div class="customRadioSelect ballTypeSelect">
                                  <input type="radio" id="ballType_2" name="type" value="Green Tennis" {{ $tournament->ball_type == 'Green Tennis' ? 'checked' : '' }} />
                                  <label for="ballType_2">Green Tennis</label>
                                </div>
                                <div class="customRadioSelect ballTypeSelect">
                                    <input type="radio" id="ballType_3" name="type" value="White Ball" {{ $tournament->ball_type == 'White Ball' ? 'checked' : '' }} />
                                    <label for="ballType_3">White Ball</label>
                                  </div>
                              </div>
                          </div>
                          <!-- Tournament Category Section -->
                          <div class="newtournament-item">
                              <label class="mb-2">Tournament Category</label>
                              <div class="tournament_category">
                                <div class="customRadioSelect">
                                  <input type="radio" id="tournamentCategory_1" name="category" value="Limited Overs" {{ $tournament->tournament_category == 'Limited Overs' ? 'checked' : '' }} />
                                  <label for="tournamentCategory_1">Limited Overs</label>
                                </div>
                                <div class="customRadioSelect">
                                  <input type="radio" id="tournamentCategory_2" name="category" value="Box Cricket" {{ $tournament->tournament_category == 'Box Cricket' ? 'checked' : '' }} />
                                  <label for="tournamentCategory_2">Box Cricket</label>
                                </div>                    
                                  </div>
                              </div>
                          </div>
                          <div class="step-footer">
                              <button class="btn btn-outline-gray" id="back2" type="button">Back</button>
                              <button class="btn btn-success" id="finish" type="submit">Schedule Match</button>
                          </div>
                      </div>
                    </form>
                  </div>  
              </div>
          </div>
      </div>
    </div>

   <!-- Edit Match Modal -->
<div class="modal fade" id="editMatchModal" tabindex="-1" aria-labelledby="editMatchModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editMatchModalLabel">Edit Match</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Ensure the form doesn't have hardcoded match ID; we'll set it via JavaScript -->
        <form id="editMatchForm" action="" method="POST">
          @csrf
          @method('PUT') 

          <input type="hidden" name="match_id" id="match_id">

          <div class="mb-3">
            <label for="edit_group" class="form-label">Group</label>
            <select class="form-select" name="group_id" id="edit_group" onchange="loadGroupAndTeams(this.value)">
              <option value="">Select Group</option>
              @foreach ($showgroups as $group)
                <option value="{{ $group->id }}">{{ $group->group_name }}</option>
              @endforeach
            </select>
          </div>
          
          <div class="mb-3">
            <label for="edit_team_one" class="form-label">Team 1</label>
            <select class="form-select" name="team_one" id="edit_team_one">
              <!-- Teams will be populated dynamically -->
            </select>
          </div>
          
          <div class="mb-3">
            <label for="edit_team_two" class="form-label">Team 2</label>
            <select class="form-select" name="team_two" id="edit_team_two">
              <!-- Teams will be populated dynamically -->
            </select>
          </div>          

          <div class="mb-3">
            <label for="edit_ground" class="form-label">Ground</label>
            <select class="form-select" name="ground" id="edit_ground">
              @foreach($groundArray as $id => $groundName)
                <option value="{{ $id }}">{{ $groundName }}</option>
              @endforeach
            </select>
          </div>

         <div class="mb-3">
            <label for="edit_match_date_time" class="form-label">Match Date & Time</label>
            <input type="text"
                   class="form-control"
                   id="edit_match_date_time"
                   name="match_date_time"
                   >
        </div>

          <div class="mb-3">
            <label for="edit_overs" class="form-label">No of Overs</label>
            <input type="number" class="form-control" id="edit_overs" name="number_of_overs">
          </div>

          <div class="mb-3">
            <label for="edit_overs_per_bowler" class="form-label">Overs Per Bowler</label>
            <input type="number" class="form-control" id="edit_overs_per_bowler" name="overs_per_bowler">
          </div>

          <div class="mb-3">
            <label for="edit_ball_type" class="form-label">Ball Type</label>
            <select class="form-select" name="ball_type" id="edit_ball_type">
              <option value="Red Tennis">Red Tennis</option>
              <option value="Green Tennis">Green Tennis</option>
              <option value="White Ball">White Ball</option>
            </select>
          </div>

          <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
      </div>
    </div>
  </div>
</div>



<script type="text/javascript" src="https://mdbgo.io/marta-szymanska/mdb5-demo-pro-design-blocks/dev/js/new-prism.js"></script>
<script type="text/javascript" src="https://mdbgo.io/marta-szymanska/mdb5-demo-pro-design-blocks/dev/js/dist/mdbsnippet.min.js"></script>
<script type="text/javascript" src="https://mdbgo.io/marta-szymanska/mdb5-demo-pro-design-blocks/js/mdb.min.js"></script>
<script type="text/javascript">
  const pickerDateOptions = document.querySelector('#datetimepicker-dateOptions');
  new mdb.Datetimepicker(pickerDateOptions, {
    datepicker: { format: 'dd-mm-yyyy' },
  });

  const pickerTimeOptions = document.querySelector('#datetimepicker-timeOptions');
  new mdb.Datetimepicker(pickerTimeOptions, {
    timepicker: { format24: true },
  });
</script>
<script type="text/javascript">
    // Step 1: Go to Step 2
    document.getElementById('next1').addEventListener('click', function() {
        document.getElementById('step1').classList.add('d-none');
        document.getElementById('step2').classList.remove('d-none');
        document.getElementById('nav-step1').classList.remove('active');
        document.getElementById('nav-step2').classList.add('active');
        document.getElementById('nav-step1').classList.add('completed');
    });
    // Step 2: Go Back to Step 1
    document.getElementById('back2').addEventListener('click', function() {
        document.getElementById('step2').classList.add('d-none');
        document.getElementById('step1').classList.remove('d-none');
        document.getElementById('nav-step2').classList.remove('active');
        document.getElementById('nav-step1').classList.add('active');
        document.getElementById('nav-step2').classList.remove('completed');
    });
    // Step 2: Go to Step 3
    document.getElementById('next2').addEventListener('click', function() {
        document.getElementById('step2').classList.add('d-none');
        document.getElementById('step3').classList.remove('d-none');
        document.getElementById('nav-step2').classList.remove('active');
        document.getElementById('nav-step3').classList.add('active');
        document.getElementById('nav-step2').classList.add('completed');
    });
    // Step 3: Go Back to Step 2
    document.getElementById('back3').addEventListener('click', function() {
        document.getElementById('step3').classList.add('d-none');
        document.getElementById('step2').classList.remove('d-none');
        document.getElementById('nav-step3').classList.remove('active');
        document.getElementById('nav-step2').classList.add('active');
        document.getElementById('nav-step3').classList.remove('completed');
    });
    // Finish
    document.getElementById('finish').addEventListener('click', function() {
      document.getElementById('nav-step3').classList.add('completed');
        alert('You have completed all steps!');
    });
    $('.groupSelectTeamModal').on('show.bs.modal', function () {
      $('#scheduleMatchModal').modal('show');
    });

    $('.groupSelectTeamModal').on('hidden.bs.modal', function () {
        $('#scheduleMatchModal').modal('show');
    });
</script>
<script>
  // When Modal 2 is shown
  $('.groupSelectTeamModal').on('shown.bs.modal', function () {
  setTimeout(function() {
  $('.modal-backdrop').not('.modal-backdrop:first').remove();
  }, 100);
  });

  // When Modal 2 is hidden
  $('.groupSelectTeamModal').on('hidden.bs.modal', function () {
  setTimeout(function() {
  $('#scheduleMatchModal').modal('handleUpdate');
  }, 100);
  // Update modal 1 to ensure it appears correctly if backdrop was removed
  });
</script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('scheduleMatchForm');
    const errorMessages = document.getElementById('errorMessages');
    const dateTimeInput = document.getElementById('datetimepickerExample');
    const startDate = new Date("{{ \Carbon\Carbon::parse($tournament->start_date)->format('Y-m-d') }}");
    const endDate = new Date("{{ \Carbon\Carbon::parse($tournament->end_date)->format('Y-m-d') }}");
    const groupSelect = document.getElementById('groupSelect');
    const team1RadioButtons = document.getElementsByName('team1');
    const team2RadioButtons = document.getElementsByName('team2');
    
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        
        // Clear previous error messages
        errorMessages.innerHTML = '';
        errorMessages.classList.add('d-none');

        let errors = [];


        // Validate group selection
        if (!groupSelect.value) {
            errors.push('Please select a Group.');
        }

        // Validate Team 1 selection
        const team1Selected = Array.from(team1RadioButtons).some(radio => radio.checked);
        if (!team1Selected) {
            errors.push('Please select Team 1.');
        }

        // Validate Team 2 selection
        const team2Selected = Array.from(team2RadioButtons).some(radio => radio.checked);
        if (!team2Selected) {
            errors.push('Please select Team 2.');
        }

        // Display errors if any
        if (errors.length > 0) {
            errorMessages.innerHTML = '<ul>' + errors.map(error => `<li>${error}</li>`).join('') + '</ul>';
            errorMessages.classList.remove('d-none');
            return;
        }

        this.submit();
    });

    const modal = document.getElementById('scheduleMatchModal');
    modal.addEventListener('hide.bs.modal', function (event) {
        form.reset();
        errorMessages.classList.add('d-none'); 
    });
    
    const picker = new mdb.Datetimepicker(dateTimeInput, {
      min: startDate,
      max: endDate,
        format: 'DD/MM/YYYY, hh:mm A'
    });
});
 

// Function to format a Date object as 'DD/MM/YYYY'
function formatDate(date) {
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
}
function disableTeam(selectedTeamId) {
    // Enable all team 2 inputs first
    document.querySelectorAll('.teamB-selection').forEach(function(input) {
        input.disabled = false;
    });

    // Disable the selected team in the Team 2 list
    const teamBInput = document.getElementById('teamB_' + selectedTeamId);
    if (teamBInput) {
        teamBInput.disabled = true;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const groupSelect = document.getElementById('groupSelect');
    const teamAList = document.getElementById('teamAList');
    const teamBList = document.getElementById('teamBList');

    if (groupSelect) {
        groupSelect.addEventListener('change', function() {
            const groupId = this.value;

            // Clear existing teams
            teamAList.innerHTML = '';
            teamBList.innerHTML = '';
var baseUrl = "{{ url('/') }}";
            if (groupId) {
                // Make an AJAX request to get teams for the selected group
                fetch(baseUrl+`/teams_get?group_id=${groupId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.teams && Array.isArray(data.teams)) {
                            data.teams.forEach(team => {
                                const teamADiv = createTeamElement(team, 'team1');
                                const teamBDiv = createTeamElement(team, 'team2');
                                teamAList.appendChild(teamADiv);
                                teamBList.appendChild(teamBDiv);
                            });

                            if (data.teams.length === 0) {
                                teamAList.innerHTML = '<p>No teams found for this group.</p>';
                                teamBList.innerHTML = '<p>No teams found for this group.</p>';
                            }
                        } else {
                            console.error('Invalid data format received from server');
                            teamAList.innerHTML = '<p>Error loading teams. Please try again.</p>';
                            teamBList.innerHTML = '<p>Error loading teams. Please try again.</p>';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching teams:', error);
                        teamAList.innerHTML = '<p>Error loading teams. Please try again.</p>';
                        teamBList.innerHTML = '<p>Error loading teams. Please try again.</p>';
                    });
            }
        });
    }
});

function createTeamElement(team, teamType) {
    const div = document.createElement('div');
    div.className = 'playingsquad-remainning-item';
    div.innerHTML = `
        <input type="radio" id="${teamType}_${team.id}" name="${teamType}" value="${team.id}" ${teamType === 'team1' ? 'onclick="disableTeam(' + team.id + ')"' : 'class="teamB-selection"'} />
        <label for="${teamType}_${team.id}">
            <i><img src="/storage/${team.logo}" alt="${team.name}" /></i>
            <div class="team-name-captain">
                <h6>${team.name}</h6>
            </div>
        </label>
    `;
    return div;
}

function disableTeam(teamId) {
    var teamBRadios = document.querySelectorAll('.teamB-selection');
    teamBRadios.forEach(radio => {
        radio.disabled = (radio.value == teamId);
    });
}

function loadGroupAndTeams(groupId, selectedTeam1Id, selectedTeam2Id) {
    const teamAList = document.getElementById('edit_team_one');
    const teamBList = document.getElementById('edit_team_two');
    
    // Clear existing teams
    teamAList.innerHTML = '';
    teamBList.innerHTML = '';
var baseUrl = "{{ url('/') }}";
    // Fetch teams for the selected group
    if (groupId) {
        fetch(baseUrl+`/teams_get?group_id=${groupId}`)
            .then(response => response.json())
            .then(data => {
                if (data.teams && Array.isArray(data.teams)) {
                    data.teams.forEach(team => {
                        const teamAOption = createTeamOption(team, selectedTeam1Id);
                        const teamBOption = createTeamOption(team, selectedTeam2Id);
                        teamAList.appendChild(teamAOption);
                        teamBList.appendChild(teamBOption);
                    });

                    if (data.teams.length === 0) {
                        teamAList.innerHTML = '<option>No teams found for this group.</option>';
                        teamBList.innerHTML = '<option>No teams found for this group.</option>';
                    }
                } else {
                    console.error('Invalid data format received from server');
                    teamAList.innerHTML = '<option>Error loading teams. Please try again.</option>';
                    teamBList.innerHTML = '<option>Error loading teams. Please try again.</option>';
                }
            })
            .catch(error => {
                console.error('Error fetching teams:', error);
                teamAList.innerHTML = '<option>Error loading teams. Please try again.</option>';
                teamBList.innerHTML = '<option>Error loading teams. Please try again.</option>';
            });
    }
}


function createTeamOption(team, selectedTeamId) {
    const option = document.createElement('option');
    option.value = team.id;
    option.text = team.name;
    
    if (team.id == selectedTeamId) {
        option.selected = true;  // Pre-select the team if it matches the selected ID
    }
    
    return option;
}



function editMatch(matchId) {
    // Send an AJAX request to get the match details
    var baseUrl = "{{ url('/') }}";
    $.ajax({
        url: baseUrl+`/get-match-details/${matchId}`,
        type: 'GET',
        success: function (response) {
            // Populate the modal fields with the match details
            $('#match_id').val(response.id);
            $('#edit_match_date_time').val(response.match_date_time);

            // Pre-select the group
            $('#edit_group').val(response.group_id).trigger('change');

            // Load groups and teams dynamically
            loadGroupAndTeams(response.group_id, response.team1, response.team2);

            $('#edit_ground').val(response.ground).trigger('change');
            $('#edit_overs').val(response.number_of_overs);
            $('#edit_overs_per_bowler').val(response.overs_per_bowler);
            $('#edit_ball_type').val(response.ball_type);

            // Set the form action dynamically
            $('#editMatchForm').attr('action', `/update-match/${response.id}`);
        },
        error: function () {
            alert('Error loading match details. Please try again.');
        }
    });
}


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
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
     flatpickr("#datetimepickerExample", {
        enableTime: true,
        dateFormat: "d/m/Y, h:i K", // You can adjust the format as needed
        minDate: "{{ \Carbon\Carbon::parse($tournament->start_date)->format('d/m/Y') }}",
        maxDate: "{{ \Carbon\Carbon::parse($tournament->end_date)->format('d/m/Y') }}",
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    flatpickr("#edit_match_date_time", {
        enableTime: true,
        dateFormat: "d/m/Y, h:i K", // Matches '29/01/2025, 12:00 PM'
        minDate: "{{ \Carbon\Carbon::parse($tournament->start_date)->format('d/m/Y, h:i A') }}",
        maxDate: "{{ \Carbon\Carbon::parse($tournament->end_date)->format('d/m/Y, h:i A') }}",
    });
});
</script>
@endsection
