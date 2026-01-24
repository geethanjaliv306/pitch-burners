<link rel="stylesheet" href="https://mdbgo.io/marta-szymanska/mdb5-demo-pro-design-blocks/css/mdb.min.css" />
<link rel="stylesheet" href="https://mdbgo.io/marta-szymanska/mdb5-demo-pro-design-blocks/dev/css/new-prism.css" />
<style>
  .sidebar{
  display: none;
}

@keyframes pulse {
  0% {
    box-shadow: 0 0 15px rgba(204, 169, 44, 0.4);
  }
  50% {
    box-shadow: 0 0 25px rgba(204, 169, 44, 0.6);
  }
  100% {
    box-shadow: 0 0 15px rgba(204, 169, 44, 0.4);
  }
}

/* Apply the pulse animation to the selected team */
.pulse-box-shadow {
  box-shadow: 0 0 15px rgba(204, 169, 44, 0.4);
  animation: pulse 2s infinite;
}
</style>
@extends('layouts.admin')

@section('content')

@csrf

<section class="startmatch-wrap">
  <div class="inner">
    <i class="bg-image"><img class="startmatch-wrap-bg" src="{{ asset('uploads/images/drawing-baseball-player-with-bat-word-cricket-it.jpg') }}" /></i>
    <div class="info">
      <h6>Want to start a match?</h6>
      <a class="btn btn-yellow" href="javascript:;" data-bs-target="#startMatchModal" data-bs-toggle="modal">Start a Match</a>
    </div>
  </div>
</section>

<div class="modal fade startMatchModal modalOpeninBottomtoTop-mobile" id="startMatchModal" tabindex="-1" aria-labelledby="startMatchModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header justify-content-center">
        <div class="top-head">
          <h1 class="modal-title text-center">Select Team</h1>
        </div>
      </div>
      <div class="modal-body text-center">
        <div class="choose-team-wrap">
          <div class="chooseteam team-a" data-team-id="{{ $match->teamOne->id }}" data-bs-target="#selectPlayerModal" data-bs-toggle="modal" data-team-category='team_A'>
            <span class="team-name d-none">{{ $match->teamOne->name }}</span>
          <img src="{{ config('constants.upload_url') . '/team_logos/' . $match->teamOne->logo }}" style="width: 80px; height: 80px; margin-right: 10px;" />

          </div>
          <div class="vs"><span>VS</span></div>
          <div class="chooseteam team-b" data-team-id="{{ $match->teamTwo->id }}" data-bs-target="#selectPlayerModal" data-bs-toggle="modal" data-team-category="team_B">
            <span class="team-name d-none">{{ $match->teamTwo->name }}</span>
           <img src="{{ config('constants.upload_url') . '/team_logos/' . $match->teamTwo->logo }}" style="width: 80px; height: 80px; margin-right: 10px;" />
          </div>
        </div>
      </div>
      <div class="alert alert-danger d-none text-center" id="team-selection-error" role="alert">
        Choose players for both teams.
      </div>
      <div class="modal-footer d-flex justify-content-center">
        <button type="button" class="btn btn-primary" id="start_btn">Start</button>
    </div>
    </div>
  </div>
</div>

<div class="modal fade selectPlayerModal modalOpeninBottomtoTop-mobile" id="selectPlayerModal" tabindex="-1" aria-labelledby="selectPlayerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="top-head">
          <i class="modal-back"  data-bs-target="#startMatchModal" data-bs-toggle="modal"><img src="{{asset('uploads/images/back-white.svg')}}" /></i>
          <h1 class="modal-title">Dsignzmedia</h1>
        </div>
        <div class="top-bottom">
          <p class="title">Select Playing Squad [11]</p>
          <div class="search-addnew-wrap">
            <div class="quicksearch">
              <input type="search" value="Search Player" />
            </div>
            <a href="javascript:;" class="add-player d-none">Add New Player</a>
          </div>
        </div>
      </div>
        <div class="modal-body text-center">
          <div class="alert alert-danger d-none" id="player-selection-error" role="alert">
            You must select exactly 11 players before proceeding.
        </div>

        <div class="playingsquad-remainning-item">
            <input type="checkbox" id="selectAllPlayers" />
            <label for="selectAllPlayers">Select All Players</label>
          </div>

           <div class="playingsquad-remainning-wrap" id="players-list">

           </div>
        </div>
        <div class="modal-footer">
            <p class="selectedplayer"></p>
            <button type="button" class="btn btn-primary" id="next_button">Next</button>
        </div>
    </div>
  </div>
</div>

<div class="modal fade selectCaptainKeeperTwelevemanModal modalOpeninBottomtoTop-mobile" id="selectCaptainKeeperTwelevemanModal" tabindex="-1" aria-labelledby="selectCaptainKeeperTwelevemanModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="top-head">
          <i class="modal-back" data-bs-target="#selectPlayerModal" data-bs-toggle="modal" ><img src="{{ asset('uploads/images/back-white.svg') }}" /></i>
          <h1 class="modal-title">Select Captain, Wicket Keeper, 12th Man</h1>
        </div>
        <div class="top-bottom">
          <div class="nav nav-tabs" id="nav-selectCaptainKeeperTwelevemantab" role="tablist">
            <button class="nav-link active" id="nav-captain-tab" data-bs-toggle="tab" data-bs-target="#nav-captain" type="button" role="tab" aria-controls="nav-captain" aria-selected="true">Captain</button>
            <button class="nav-link" id="nav-keeper-tab" data-bs-toggle="tab" data-bs-target="#nav-keeper" type="button" role="tab" aria-controls="nav-keeper" aria-selected="false">Wicket Keeper</button>
            <button class="nav-link" id="nav-twelevemen-tab" data-bs-toggle="tab" data-bs-target="#nav-twelevemen" type="button" role="tab" aria-controls="nav-twelevemen" aria-selected="false">12th Man</button>
          </div>
        </div>
      </div>
      <div class="modal-body text-center">
        <div class="alert alert-danger d-none" id="captain-wicketkeeper-error" role="alert"></div>
        <div class="tab-content" id="nav-selectCaptainKeeperTwelevemantabContent">
            <!-- Captain Selection -->
            <div class="tab-pane fade show active" id="nav-captain" role="tabpanel" aria-labelledby="nav-captain-tab">
                <div class="playingsquad-remainning-wrap" id="captain-list">
                    <!-- Captain selection list will be dynamically populated -->
                </div>
            </div>

            <!-- Wicket Keeper Selection -->
            <div class="tab-pane fade" id="nav-keeper" role="tabpanel" aria-labelledby="nav-keeper-tab">
                <div class="playingsquad-remainning-wrap" id="keeper-list">
                    <!-- Wicketkeeper selection list will be dynamically populated -->
                </div>
            </div>

            <!-- 12th Man Selection -->
            <div class="tab-pane fade" id="nav-twelevemen" role="tabpanel" aria-labelledby="nav-twelevemen-tab">
                <div class="playingsquad-remainning-wrap" id="twelfthman-list">
                    <!-- 12th Man selection list will be dynamically populated -->
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="done_btn">Done</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade startMatchModal continueOfSecModal modalOpeninBottomtoTop-mobile" id="startMatchTwoModal" tabindex="-1" aria-labelledby="startMatchTwoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <div class="startmatch-title-wrap">
          <h1 class="modal-title text-center">Start a Match</h1>
          <div class="choose-team-wrap">
            <!-- Team A -->
            <div class="chooseteam_fig_caption">
              <figure class="chooseteam team-a">
               <img width="50" src="{{ config('constants.upload_url') . '/team_logos/' . $match->teamOne->logo }}" alt="Team A Logo" id="teamALogo">
              </figure>
              <figcaption id="teamAName">{{ $match->teamOne->name }}</figcaption>
            </div>
            <div class="vs">
              <span>VS</span>
            </div>
            <!-- Team B -->
            <div class="chooseteam_fig_caption">
              <figure class="chooseteam team-b">
               <img width="50" src="{{ config('constants.upload_url') . '/team_logos/' . $match->teamTwo->logo }}" alt="Team B Logo" id="teamBLogo">
              </figure>
              <figcaption id="teamBName">{{ $match->teamTwo->name }}</figcaption>
            </div>
          </div>
        </div>
      </div>
      <!-- Modal Body -->
      <div class="modal-body text-center">
        <div class="alert alert-danger d-none" id="matchDetails-validation-error" role="alert"></div>
        <div class="match-type-info">
          <!-- Match Type -->
          <div class="matchtype-item matchtype">
            <input type="hidden" id="tournament_id" value="{{ $match->tournament_id }}">
            <input type="hidden" id="round_id" value="{{ $match->round_id }}">
            <input type="hidden" id="group_id" value="{{ $match->group_id }}">
            <input type="hidden" id="overs_per_bowler" value="{{ $match->overs_per_bowler }}">
            <input type="hidden" id="schedule_match_id" name="schedule_match_id" value="{{ $match->id }}" />
            <h3>Match Type:</h3>
            <div class="overs-inner">
              <input type="text" id="" value="{{ $match->category }}" readonly/>
            </div>
            <div class="matchtype-inner d-none">
              <div class="playingsquad-remainning-item">
                <input type="radio" id="matchType_1" name="matchType" value="Limited Overs" {{ $match->category == 'Limited Overs' ? 'checked' : '' }} />
                <label for="matchType_1">Limited Overs</label>
              </div>
              <div class="playingsquad-remainning-item">
                <input type="radio" id="matchType_2" name="matchType" value="Box Cricket" {{ $match->category == 'Box Cricket' ? 'checked' : '' }} />
                <label for="matchType_2">Box Cricket</label>
              </div>
            </div>
          </div>
          <!-- Number of Overs -->
          <div class="matchtype-item noofovers">
            <h3>No of Overs*:</h3>
            <div class="overs-inner">
              <input type="number" id="noOfOvers" name="noOfOvers" value="{{ $match->number_of_overs }}" />
            </div>
          </div>
          <!-- Ground -->
          <div class="matchtype-item">
            <h3>Ground:</h3>
            <div class="ground-inner">
              <select id="ground" name="venue_id">
                <option value="{{ $match->venue->name }}">{{ $match->venue->name }}</option>
              </select>
            </div>
          </div>
          <!-- Date & Time -->
          <div class="matchtype-item">
            <h3>Date & Time:</h3>
            <div class="datetime-inner">
              <div class="form-outline datetimepicker">
              <input type="text" class="form-control" id="matchDateTime" value="{{ $match->match_date_time }}" data-mdb-toggle="datetimepicker"/>
              </div>
            </div>
          </div>
          <!-- Match Officials -->
          <div class="matchtype-item">
            <h3>Match Officials:</h3>
            <div class="matchofficials-inner">
              <div class="matchofficials-item" data-bs-target="" data-bs-toggle="modal" id="umpire-btn">
                <figure>
                  <img src="{{ asset('uploads/images/umpire.png') }}" alt="Select Umpires" />
                </figure>
                <figcaption>Umpires</figcaption>
              </div>
              <div class="matchofficials-item" data-bs-target="" data-bs-toggle="modal" id="scorer-btn">
                <figure>
                  <img src="{{ asset('uploads/images/score.png') }}" alt="Select Scorer" />
                </figure>
                <figcaption>Scorer</figcaption>
              </div>
            </div>
          </div>
          <div class="matchtype-item">
            <h3>Who Won the Toss?</h3>
            <div class="toss-winner-inner">
              <div class="playingsquad-remainning-item">
                <input type="radio" id="tossWinner_teamA" name="tossWinner" value="{{ $match->teamOne->id }}" />
                <label for="tossWinner_teamA">{{ $match->teamOne->name }}</label>
              </div>
              <div class="playingsquad-remainning-item">
                <input type="radio" id="tossWinner_teamB" name="tossWinner" value="{{ $match->teamTwo->id }}" />
                <label for="tossWinner_teamB">{{ $match->teamTwo->name }}</label>
              </div>
            </div>
          </div>

          <div class="matchtype-item" id="tossChoiceSection" style="display: none;">
            <h3>Toss Winner Chose To:</h3>
            <div class="bat-bowl-choice-inner">
                <div class="playingsquad-remainning-item">
                    <input type="radio" id="chooseBat" name="tossChoice" value="bat" />
                    <label for="chooseBat">Bat</label>
                </div>
                <div class="playingsquad-remainning-item">
                    <input type="radio" id="chooseBowl" name="tossChoice" value="bowl" />
                    <label for="chooseBowl">Bowl</label>
                </div>
            </div>
          </div>

        </div>
      </div>
      <!-- Modal Footer -->
      <div class="modal-footer d-flex justify-content-center">
        <button type="button" class="btn btn-primary" id="startMatchButton">Start</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade selectUmpireModal modalOpeninBottomtoTop-mobile" id="selectUmpireModal" tabindex="-1" aria-labelledby="selectUmpireModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="top-head">
          <i class="modal-back"  data-bs-target="#startMatchTwoModal" data-bs-toggle="modal"><img src="{{ asset('uploads/images/back-white.svg') }}" /></i>
          <h1 class="modal-title">Select Umpires</h1>
        </div>
        <div class="top-bottom">
          <div class="nav nav-tabs" id="nav-selectUmpirestab" role="tablist">
            <button class="nav-link active" id="nav-firstUmpire-tab" data-bs-toggle="tab" data-bs-target="#nav-firstUmpire" type="button" role="tab" aria-controls="nav-firstUmpire" aria-selected="true">1st Umpire</button>
            <button class="nav-link" id="nav-secUmpire-tab" data-bs-toggle="tab" data-bs-target="#nav-secUmpire" type="button" role="tab" aria-controls="nav-secUmpire" aria-selected="false">2nd Umpire</button>
            <button class="nav-link" id="nav-thirdUmpire-tab" data-bs-toggle="tab" data-bs-target="#nav-thirdUmpire" type="button" role="tab" aria-controls="nav-thirdUmpire" aria-selected="false">3rd Umpire</button>
          </div>
        </div>
      </div>
        <div class="modal-body text-center">
          <div class="tab-content" id="nav-selectUmpirestabContent">
            <!-- First Umpire Tab -->
            <div class="tab-pane fade show active" id="nav-firstUmpire" role="tabpanel" aria-labelledby="nav-firstUmpire-tab">
                <div class="playingsquad-remainning-wrap">
                    @foreach ($users as $user)
                        <div class="playingsquad-remainning-item">
                            <input type="radio" id="firstUmpire_{{ $user->id }}" name="firstUmpire" value="{{ $user->id }}" />
                            <label for="firstUmpire_{{ $user->id }}">
                                <img src="{{ asset('uploads/images/Rectangle 810.png') }}" alt="{{ $user->name }}" />
                                {{ $user->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Second Umpire Tab -->
            <div class="tab-pane fade" id="nav-secUmpire" role="tabpanel" aria-labelledby="nav-secUmpire-tab">
                <div class="playingsquad-remainning-wrap">
                    @foreach ($users as $user)
                        <div class="playingsquad-remainning-item">
                            <input type="radio" id="secondUmpire_{{ $user->id }}" name="secondUmpire" value="{{ $user->id }}" />
                            <label for="secondUmpire_{{ $user->id }}">
                                <img src="{{ asset('uploads/images/Rectangle 810.png') }}" alt="{{ $user->name }}" />
                                {{ $user->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Third Umpire Tab -->
            <div class="tab-pane fade" id="nav-thirdUmpire" role="tabpanel" aria-labelledby="nav-thirdUmpire-tab">
                <div class="playingsquad-remainning-wrap">
                    @foreach ($users as $user)
                        <div class="playingsquad-remainning-item">
                            <input type="radio" id="thirdUmpire_{{ $user->id }}" name="thirdUmpire" value="{{ $user->id }}" />
                            <label for="thirdUmpire_{{ $user->id }}">
                                <img src="{{ asset('uploads/images/Rectangle 810.png') }}" alt="{{ $user->name }}" />
                                {{ $user->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-bs-target="#startMatchTwoModal" data-bs-toggle="modal" id="umpire-next">Next</button>
        </div>
    </div>
 </div>
</div>

<div class="modal fade selectScorerModal modalOpeninBottomtoTop-mobile" id="selectScorerModal" tabindex="-1" aria-labelledby="selectScorerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="top-head">
          <i class="modal-back"  data-bs-target="#startMatchTwoModal" data-bs-toggle="modal"><img src="{{ asset('uploads/images/back-white.svg') }}" /></i>
          <h1 class="modal-title">Select Scorer</h1>
        </div>
        <div class="top-bottom">
          <div class="nav nav-tabs" id="nav-selectScorertab" role="tablist">
            <button class="nav-link active" id="nav-firstScorer-tab" data-bs-toggle="tab" data-bs-target="#nav-firstScorer" type="button" role="tab" aria-controls="nav-firstScorer" aria-selected="true">Scorer 1</button>
            <button class="nav-link" id="nav-secondScorer-tab" data-bs-toggle="tab" data-bs-target="#nav-secondScorer" type="button" role="tab" aria-controls="nav-secondScorer" aria-selected="false">Scorer 2</button>
          </div>
        </div>
      </div>
        <div class="modal-body text-center">
          <div class="tab-content" id="nav-selectScorertabContent">
            <!-- First Scorer Tab -->
            <div class="tab-pane fade show active" id="nav-firstScorer" role="tabpanel" aria-labelledby="nav-firstScorer-tab">
                <div class="playingsquad-remainning-wrap">
                    @foreach ($users as $user)
                        <div class="playingsquad-remainning-item">
                            <input type="radio" id="firstScorer_{{ $user->id }}" name="firstScorer" value="{{ $user->id }}" />
                            <label for="firstScorer_{{ $user->id }}">
                                <img src="{{ asset('uploads/images/Rectangle 810.png') }}" alt="{{ $user->name }}" />
                                {{ $user->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Second Scorer Tab -->
            <div class="tab-pane fade" id="nav-secondScorer" role="tabpanel" aria-labelledby="nav-secondScorer-tab">
                <div class="playingsquad-remainning-wrap">
                    @foreach ($users as $user)
                        <div class="playingsquad-remainning-item">
                            <input type="radio" id="secondScorer_{{ $user->id }}" name="secondScorer" value="{{ $user->id }}" />
                            <label for="secondScorer_{{ $user->id }}">
                                <img src="{{ asset('uploads/images/Rectangle 810.png') }}" alt="{{ $user->name }}" />
                                {{ $user->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-bs-target="#startMatchTwoModal" data-bs-toggle="modal" id="scorer-next">Next</button>
        </div>
    </div>
 </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

  const pickerDateOptions = document.querySelector('#exampleDatepicker1');
  new mdb.Datetimepicker(pickerDateOptions, {
    datepicker: { format: 'dd-mm-yyyy' },
  });
</script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
      var startMatchModal = new bootstrap.Modal(document.getElementById('startMatchModal'));
      startMatchModal.show();
  });

  $('#tossChoiceSection').hide();

  $('input[name="tossWinner"]').on('change', function () {
      if ($(this).is(':checked')) {
          // Show the "Toss Winner Chose To" section when a team is selected
          $('#tossChoiceSection').show();
      } else {
          // Hide the "Toss Winner Chose To" section if no team is selected
          $('#tossChoiceSection').hide();
          $('input[name="tossChoice"]').prop('checked', false); // Reset the selected choice
      }
  });

  $(document).ready(function () {
  let selectedPlayers = [];
  let allSelectedPlayers = [];
  const maxSelection = 11;
  let currentTeamId = null;
  let selectedTeams = {
    'team_A': null,
    'team_B': null,
  };
  let currentlySelectedTeam = '';
  
   // Add initial box shadow to Team A
  $('.team-a').addClass('pulse-box-shadow');


  // Add tick mark container to both teams
//   $('.chooseteam').append('<div class="tick-mark" style="display: none; position: absolute; top: 5px; right: 5px; color: #28a745; font-size: 24px;">✓</div>');


  // Function to update displayed selected players and count
  function updateSelectedPlayers() {
    // Only display the first 11 players in the text, but keep the count of all selected players
    const displayedPlayers = selectedPlayers.slice(0, maxSelection).map(player => player.name).join(', ');
    $('.selectedplayer').text(`Selected Players (${allSelectedPlayers.length}): ` + displayedPlayers);

    // Update the player count in the modal title
    $('.title').text(`Select Playing Squad [${allSelectedPlayers.length}]`);
  }
  
  
 // Function to update team selection visual indicators
  function updateTeamSelectionUI(teamClass) {
    // Remove the pulse effect from both teams initially
    $('.team-a, .team-b').removeClass('pulse-box-shadow');

    // Add pulse effect to the current team
    if (teamClass === 'team-b' && selectedTeams['team_A']) {
      $('.team-b').addClass('pulse-box-shadow');
    } else if (teamClass === 'team-a' && !selectedTeams['team_A']) {
      $('.team-a').addClass('pulse-box-shadow'); // Apply only to Team A initially
    }
    
    // Show tick mark for selected teams
    if (selectedTeams['team_A']) {
      $('.team-a .tick-mark').show();
    }
    if (selectedTeams['team_B']) {
      $('.team-b .tick-mark').show();
    }

    // If both teams are selected, apply pulse effect to both
    if (selectedTeams['team_A'] && selectedTeams['team_B']) {
      $('.team-a, .team-b').addClass('pulse-box-shadow');
    }
}


  // Handle team selection click event
  $('.chooseteam').on('click', function () {
    currentTeamId = $(this).data('team-id');
    currentlySelectedTeam = $(this).data('team-category');
    var teamName = $(this).find('.team-name').text();

    const teamClass = $(this).hasClass('team-a') ? 'team-a' : 'team-b';
    const teamCategory = $(this).data('team-category');

    selectedTeams[teamCategory] = true;

    updateTeamSelectionUI(teamClass);

    // Update UI for team selection
    updateTeamSelectionUI($(this).hasClass('team-a') ? 'team-b' : 'team-a');

    $('#selectPlayerModal .modal-title').text(teamName);
    $('#selectAllPlayers').prop('checked', false);

    // Clear previous selected players and list
    $('#players-list').empty();
    selectedPlayers = [];
    allSelectedPlayers = [];
    updateSelectedPlayers();

    var baseUrl = "{{ url('/') }}";

    // Load players for the selected team
    $.ajax({
      url: baseUrl + '/team/players/' + currentTeamId,
      method: 'GET',
      success: function (players) {
        if (players.length > 0) {
          players.forEach(function (player) {
            $('#players-list').append(`
              <div class="playingsquad-remainning-item">
                <input type="checkbox" id="player_${player.id}" value="${player.id}" />
                <label for="player_${player.id}">
                  <img src="/pitchburners-web/storage/app/public/uploads/player_images/${player.image}" alt="${player.name}" />
                  ${player.name}
                  ${player.is_captain ? '<p>( C )</p>' : ''}
                </label>
              </div>
            `);

            // Store player data in the checkbox element
            $(`#player_${player.id}`).data('playerData', player);

            // Handle player selection/deselection
            $(`#player_${player.id}`).on('change', function () {
              const playerData = $(this).data('playerData');
              if ($(this).is(':checked')) {
                allSelectedPlayers.push(playerData);
              } else {
                allSelectedPlayers = allSelectedPlayers.filter(p => p.id !== playerData.id);
              }
              // Limit selectedPlayers to first 11 from allSelectedPlayers
              selectedPlayers = allSelectedPlayers.slice(0, maxSelection);
              updateSelectedPlayers();
            });
          });

           // Add "Select All" functionality
$('#selectAllPlayers').on('change', function () {
    if ($(this).is(':checked')) {
        // Clear previous selection
        allSelectedPlayers = [];
        selectedPlayers = [];

        // Select the first 11 players
        $('#players-list input[type="checkbox"]').each(function(index, checkbox) {
            if (index < maxSelection) {
                $(checkbox).prop('checked', true);
                const playerData = $(checkbox).data('playerData');
                allSelectedPlayers.push(playerData);
            } else {
                $(checkbox).prop('checked', false);
            }
        });
    } else {
        // Deselect all players
        allSelectedPlayers = [];
        $('#players-list input[type="checkbox"]').prop('checked', false);
    }
    selectedPlayers = allSelectedPlayers.slice(0, maxSelection); // Display only the first 11 players
    updateSelectedPlayers();
});
        } else {
          $('#players-list').html('<p>No players available for this team.</p>');
        }
      },
      error: function () {
        alert('Unable to load players. Please try again.');
      }
    });
  });

    // Handle 'Next' button click in selectPlayerModal
    $('#next_button').on('click', function() {
        if (selectedPlayers.length !== 11) {
            // Show error message
            $('#player-selection-error').removeClass('d-none').text('You must select exactly 11 players before proceeding.');
        } else {
            $('#player-selection-error').addClass('d-none'); // Hide error if correct number of players is selected
            
            selectedTeams[currentlySelectedTeam] = {
        team_id: currentTeamId,
        players: selectedPlayers.map(p => ({ id: p.id }))
      };

      // Show tick mark for the current team
      $(`.${currentlySelectedTeam === 'team_A' ? 'team-a' : 'team-b'} .tick-mark`).show();

      // Move box shadow to other team if not selected yet
      const otherTeam = currentlySelectedTeam === 'team_A' ? 'team-b' : 'team-a';
      if (!selectedTeams[otherTeam === 'team-a' ? 'team_A' : 'team_B']) {
        updateTeamSelectionUI(otherTeam);
      }

            // Populate captain, keeper, and 12th man lists in the next modal
            const captainPlayers = selectedPlayers.map(player => `
                <div class="playingsquad-remainning-item">
                    <input type="radio" id="captain_${player.id}" name="chooseCaptain" value="${player.id}" />
                    <label for="captain_${player.id}">
                        <img src="/pitchburners-web/storage/app/public/uploads/player_images/${player.image}" alt="${player.name}" />${player.name}
                    </label>
                </div>
            `);

            const keeperPlayers = selectedPlayers.map(player => `
                <div class="playingsquad-remainning-item">
                    <input type="radio" id="keeper_${player.id}" name="chooseKeeper" value="${player.id}" />
                    <label for="keeper_${player.id}">
                        <img src="/pitchburners-web/storage/app/public/uploads/player_images/${player.image}" alt="${player.name}" />${player.name}
                    </label>
                </div>
            `);

            $('#captain-list').html(captainPlayers.join(''));
            $('#keeper-list').html(keeperPlayers.join(''));
            var baseUrl = "{{ url('/') }}";
            // Find players that were not selected to be shown as 12th man options
            $.ajax({
                url:baseUrl+'/team/players/' + currentTeamId,
                method: 'GET',
                success: function(players) {
                    const twelfthManPlayers = players.filter(player => !selectedPlayers.some(selected => selected.id === player.id));
                    if (twelfthManPlayers.length > 0) {
                        const twelfthManHtml = twelfthManPlayers.map(player => `
                            <div class="playingsquad-remainning-item">
                                <input type="checkbox" id="twelfthMan_${player.id}" name="chooseTwelevemen" value="${player.id}" />
                                <label for="twelfthMan_${player.id}">
                                    <img src="/pitchburners-web/storage/app/public/uploads/player_images/${player.image}" alt="${player.name}" />${player.name}
                                </label>
                            </div>
                        `).join('');

                        $('#twelfthman-list').html(twelfthManHtml);
                    } else {
                        $('#twelfthman-list').html('<p>No unselected players available for 12th man.</p>');
                    }
                },
                error: function() {
                    alert('Unable to load players for 12th man. Please try again.');
                }
            });

            // Proceed to next modal
            $('#selectPlayerModal').modal('hide');
            $('#selectCaptainKeeperTwelevemanModal').modal('show');
        }
    });
    
    $('#done_btn').prop('disabled', true);


    // Function to check if all roles are selected
function updateDoneButtonStatus() {
    const isCaptainSelected = $('input[name="chooseCaptain"]:checked').length > 0;
    const isKeeperSelected = $('input[name="chooseKeeper"]:checked').length > 0;
    const isTwelfthManSelected = $('input[name="chooseTwelevemen"]:checked').length > 0;

    if (isCaptainSelected && isKeeperSelected && isTwelfthManSelected) {
        $('#done_btn').prop('disabled', false); // Enable button
        $('#captain-wicketkeeper-error').addClass('d-none'); // Hide validation error if it was displayed
    } else {
        $('#done_btn').prop('disabled', true); // Keep button disabled if not all roles are selected
    }
}

// Rebind event listeners when the "Select Captain, Wicket Keeper, 12th Man" modal is shown
$('#selectCaptainKeeperTwelevemanModal').on('shown.bs.modal', function() {
    updateDoneButtonStatus(); // Initialize button state

    // Attach change event listeners to the radio inputs for Captain, Keeper, and 12th Man
    $('input[name="chooseCaptain"], input[name="chooseKeeper"], input[name="chooseTwelevemen"]').off('change').on('change', updateDoneButtonStatus);
});

// Initial call to disable the "Done" button until all selections are made
$(document).ready(function () {
    $('#done_btn').prop('disabled', true); // Ensure the "Done" button is initially disabled
});


    // Display error message placeholder in the "Select Captain, Wicket Keeper, 12th Man" modal
    $('#selectCaptainKeeperTwelevemanModal').on('show.bs.modal', function() {
        if ($('#captain-selection-error').length === 0) {
            $('.modal-body.text-center').prepend('<div class="alert alert-danger d-none" id="captain-selection-error" role="alert"></div>');
        }
    });

    // Handle 'Done' button click in selectCaptainKeeperTwelevemanModal
    $('#done_btn').on('click', function() {
          const selectedCaptain = $('input[name="chooseCaptain"]:checked').val();
          const selectedKeeper = $('input[name="chooseKeeper"]:checked').val();

          // Validation: check if both captain and wicketkeeper are selected
          if (!selectedCaptain || !selectedKeeper) {
          $('#captain-wicketkeeper-error').removeClass('d-none').text('Please select both a Captain and a Wicket Keeper before proceeding.');
          return; // Prevent moving to the next modal if validation fails
      }

          // Hide error message if validation passes
          $('#captain-selection-error').addClass('d-none');

          const selectedTwelfthMen = $('input[name="chooseTwelevemen"]:checked').map(function() {
              return $(this).val();
          }).get();

          // Store the selected team details
          selectedTeams[currentlySelectedTeam] = {
              team_id: currentlySelectedTeam == 'team_A' ? "{{ $match->teamOne->id }}" : "{{ $match->teamTwo->id }}",
              captain: selectedCaptain,
              keeper: selectedKeeper,
              twelfthMen: selectedTwelfthMen,
              players: selectedPlayers.map(p => ({ id: p.id })),
          };
          selectedTwelfthMen.forEach(id => selectedTeams[currentlySelectedTeam]['players'].push({ id }));

          console.log(selectedTeams[currentlySelectedTeam]);

          $('#selectCaptainKeeperTwelevemanModal').modal('hide');
          $('#startMatchModal').modal('show');
        });

      $('#selectCaptainKeeperTwelevemanModal').on('hide.bs.modal', function(e) {
          if (!$('#captain-selection-error').hasClass('d-none')) {
              e.preventDefault();
          }
      });

      // Handle the modal back button
      $('#selectPlayerModal .modal-back').on('click', function() {
          $('#selectPlayerModal').modal('hide');
      });

      $('#umpire-btn').on('click', function() {
          $('#selectUmpireModal').modal('show');
          $('#startMatchTwoModal').modal('hide');
      });
      $('#scorer-btn').on('click', function() {
          $('#selectScorerModal').modal('show');
          $('#startMatchTwoModal').modal('hide');
      });

      $('#selectCaptainKeeperTwelevemanModal .modal-back').on('click', function() {
          $('#selectCaptainKeeperTwelevemanModal').modal('hide');
          $('#selectPlayerModal').modal('show');
      });

      $('#start_btn').on('click', function() {
      if (!selectedTeams['team_A'] || !selectedTeams['team_B']) {
          // Show the error message inside the modal
          $('#team-selection-error').removeClass('d-none');
      } else {
          // Hide the error message if the condition is met
          $('#team-selection-error').addClass('d-none');

          // Open the startMatchTwoModal
          var startMatchTwoModal = new bootstrap.Modal(document.getElementById('startMatchTwoModal'));
          startMatchTwoModal.show();
      }
    });

    $('#startMatchButton').on('click', function() {
     const matchDetails = {
        matchType: $('input[name="matchType"]:checked').val(),
        noOfOvers: $('#noOfOvers').val() || null,
        ground: $('#ground').val() || null,
        tournament_id: $('#tournament_id').val(),
        round_id: $('#round_id').val(),
        overs_per_bowler: $('#overs_per_bowler').val(),
        group_id: $('#group_id').val(),
        dateTime: $('#matchDateTime').val() || null,
        umpires: {
            firstUmpire: $('input[name="firstUmpire"]:checked').val() || null,
            secondUmpire: $('input[name="secondUmpire"]:checked').val() || null,
            thirdUmpire: $('input[name="thirdUmpire"]:checked').val() || null
        },
        scorers: {
            firstScorer: $('input[name="firstScorer"]:checked').val() || null,
            secondScorer: $('input[name="secondScorer"]:checked').val() || null
        },
        tossWinner: $('input[name="tossWinner"]:checked').val(),
        tossChoice: $('input[name="tossChoice"]:checked').val(),
        schedule_match_id: $('#schedule_match_id').val()
    };

    const teamA = selectedTeams['team_A'];
    const teamB = selectedTeams['team_B'];

    $.ajax({
        url: '{{ route("match.start") }}',
        method: 'POST',
        data: {
            matchDetails: matchDetails,
            teamA: teamA,
            teamB: teamB,
            _token: "{{ csrf_token() }}"
        },
        success: function(response) {
            alert(response.message);
            window.location.href = '/run-scorer/' + response.match_id;
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                let errorMessage = '';

                $.each(errors, function(key, value) {
                    errorMessage += value + '<br>';
                });

                $('#matchDetails-validation-error').removeClass('d-none').html(errorMessage);
            } else {
                $('#matchDetails-validation-error').removeClass('d-none').text('Failed to start match: ' + (xhr.responseJSON.message || 'Unknown error'));
            }
        }
    });
  });

  });

</script>

@endsection
