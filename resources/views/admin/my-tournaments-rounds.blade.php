@extends('layouts.admin')

@section('content')
<style>
    .myTorunments-table .myTorunments-body .myTorunments-body-col.has-myTorunments-rounds.actions {
        flex: 0 0 30%;
    }
    .myTorunments-table .myTorunments-head .myTorunments-head-col.has-myTorunments-rounds.actions{
        flex: 0 0 30%;
    }
    .myTorunments-table .myTorunments-head .myTorunments-head-col.name.has-myTorunments-rounds, .myTorunments-table .myTorunments-head .myTorunments-body-col.name.has-myTorunments-rounds, .myTorunments-table .myTorunments-body .myTorunments-head-col.name.has-myTorunments-rounds, .myTorunments-table .myTorunments-body .myTorunments-body-col.name.has-myTorunments-rounds {
        flex: 0 0 35%;
        max-width: 50%;
    }
       .btn-reset{
        height: 40px;
        margin: 0px;
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
                        <h2><a class="my-torunments-back" href="{{ route('tournaments-group', ['tournament_id' => $tournament->id]) }}"></a>Rounds</h2>
                        <a class="btn btn-yellow" 
                                id="addRoundButton" 
                                title="{{ $hasIncompleteRound ? 'Complete the current round to add a new one.' : '' }}" 
                                style="{{ $hasIncompleteRound ? 'cursor: not-allowed;' : '' }}">
                                    Add New Round
                        </a>                                                   
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="my-torunments-wrap">
        <div class="container">
             <form method="GET" action="{{ route('tournaments-round', ['tournament' => $tournament->id]) }}" id="searchForm">
            <div class="row mb-3">
                <div class="col-md-10">
                    <input type="text" name="round_name" class="form-control" placeholder="Search by Round Name" value="{{ $searchRoundName }}" onkeypress="if(event.keyCode === 13) { this.form.submit(); }">
                </div>
                <!--<div class="col-md-4">-->
                <!--    <input type="number" name="teams_to_qualify" class="form-control" placeholder="Search by Teams to Qualify" value="{{ $searchTeamsToQualify }}" onkeypress="if(event.keyCode === 13) { this.form.submit(); }">-->
                <!--</div>-->
                <div class="col-md-2">
                    @if($searchRoundName || $searchTeamsToQualify)
                        <a href="{{ route('tournaments-round', ['tournament' => $tournament->id]) }}" class="btn btn-secondary w-100 btn-reset">Reset</a>
                    @endif
                </div>
            </div>
        </form>
            <div class="row">
                <div class="col-12">
                    <div class="myTorunments-table">
                        <div class="myTorunments-head">
                            <div class="myTorunments-head-col sno has-myTorunments-rounds">S.No</div>
                            <div class="myTorunments-head-col name has-myTorunments-rounds">Round</div>
                            <div class="myTorunments-head-col qualify has-myTorunments-rounds">No of teams to qualify</div>
                            <div class="myTorunments-head-col actions has-myTorunments-rounds">Actions</div>
                            <div class="myTorunments-head-col complete has-myTorunments-rounds">Completed</div>
                        </div>
                        @if($rounds->isEmpty())
                        <div class="no-records-found">
                          <p class="text-center m-3">No records found</p>
                        </div>
                      @else
                        @foreach ($rounds as $round)
                            <div class="myTorunments-body">
                                <div class="myTorunments-body-col sno has-myTorunments-rounds">{{ $loop->iteration }}</div>
                                <div class="myTorunments-body-col name has-myTorunments-rounds">{{ $round->type }}</div>
                                <div class="myTorunments-body-col qualify has-myTorunments-rounds">{{ $round->teams_to_qualify }}</div>
                                <div class="myTorunments-body-col actions has-myTorunments-rounds">
    <!-- Edit Round Button -->
    <div class="edit icon" data-bs-toggle="modal" data-bs-target="#editRoundsModal"
        data-id="{{ $round->id }}"
        data-type="{{ $round->type }}"
        data-number_of_overs="{{ $round->number_of_overs }}"
        data-overs_per_bowler="{{ $round->overs_per_bowler }}"
        data-teams_to_qualify="{{ $round->teams_to_qualify }}"
        @if($round->status == 1) style="pointer-events: none; opacity: 0.5;" @endif>
        <i>
            <img src="{{ asset('/uploads/images/pen.svg') }}" />
        </i>
    </div>

    <!-- Delete Round Form -->
    <form action="{{ route('delete-tournaments-round', $round->id) }}" method="POST" class="d-inline" 
        onsubmit="return confirm('Are you sure you want to delete this round?');"
        @if($round->status == 1) style="pointer-events: none; opacity: 0.5;" @endif>
        @csrf
        @method('DELETE')
        <button type="submit" class="delete icon" style="background: none; border: none; cursor: pointer;"
            @if($round->status == 1) disabled @endif>
            <i><img src="{{ asset('uploads/images/delete.png')}}" alt="Delete" /></i>
        </button>
    </form>

    <!-- Schedule Round Button -->
    <div class="schedule icon">
        <i  @if($round->status == 1) style="pointer-events: none; opacity: 0.5;" @endif>
            <a href="{{ route('tournaments.match', ['tournament_id' => $tournament->id, 'round_id' => $round->id]) }}">
                <img src="{{ asset('/uploads/images/event.svg') }}" />
            </a>
        </i>
    </div>
</div>

                                <div class="myTorunments-body-col complete has-myTorunments-rounds">
                                    <form action="{{ route('toggle-status', $round->id) }}" method="POST" style="display: inline;"onsubmit="Are you sure the round is completed? Ready to add groups for the next round.">
                                        @csrf
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" {{ $round->status ? 'checked' : '' }}>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                        @endif
                    </div>
                    <div class="col-12 pagination-wrap">
                        <div class="container position-relative">
                            <a class="goToGroups d-none" href="{{ route('tournaments-group', ['tournament_id' => $tournament->id]) }}">Next</a>
                        </div>
                    </div>
            </div>
        </div>
    </section>

    <!-- Add Round Modal -->
    <div class="modal fade roundsModal modalOpeninBottomtoTop-mobile {{ $errors->any() ? 'show' : '' }}" id="roundsModal" tabindex="-1" aria-labelledby="roundsModalLabel" aria-hidden="true" style="{{ $errors->any() ? 'display: block;' : '' }}">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Add Round</h2>
                </div>
                <div class="modal-body">
                    <form action="{{ route('tournaments-round.store', $tournament->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="type" class="form-label">Select Round:</label>
                            <input type="text" class="form-control" id="type" name="type"required >
                            @error('type')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        </div>
                        <div class="mb-3">
                            <label for="number_of_overs" class="form-label">No of Overs:</label>
                            <input type="number" class="form-control" id="number_of_overs" name="number_of_overs"required >
                            @error('number_of_overs')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        </div>
                        <div class="mb-3">
                            <label for="overs_per_bowler" class="form-label">Overs Per Bowler:</label>
                            <input type="number" class="form-control" id="overs_per_bowler" name="overs_per_bowler"required >
                            @error('overs_per_bowler')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        </div>
                        <div class="mb-3">
                            <label for="teams_to_qualify" class="form-label">Teams to Qualify:</label>
                            <input type="number" class="form-control" id="teams_to_qualify" name="teams_to_qualify" required>
                            @error('teams_to_qualify')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>                        
                        <div class="modal-footer d-flex justify-content-center">
                            <button type="button" class="btn btn-outline-gray" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Round</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

   <!-- Edit Round Modal -->
    <div class="modal fade roundsModal modalOpeninBottomtoTop-mobile" id="editRoundsModal" tabindex="-1" aria-labelledby="editRoundsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Edit Round</h2>
                </div>
                <div class="modal-body">
                    <form id="edit-round-form" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="type" class="form-label">Select Round:</label>
                            <input type="text" class="form-control" id="type" name="type" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_number_of_overs" class="form-label">No of Overs:</label>
                            <input type="number" class="form-control" id="edit_number_of_overs" name="number_of_overs" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_overs_per_bowler" class="form-label">Overs Per Bowler:</label>
                            <input type="number" class="form-control" id="edit_overs_per_bowler" name="overs_per_bowler" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_teams_to_qualify" class="form-label">Teams to Qualify:</label>
                            <input type="number" class="form-control" id="edit_teams_to_qualify" name="teams_to_qualify" required>
                        </div>                        
                        <div class="modal-footer d-flex justify-content-center">
                            <button type="button" class="btn btn-outline-gray" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Round</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
   document.addEventListener('DOMContentLoaded', function () {
    @if ($errors->any())
                var roundsModal = new bootstrap.Modal(document.getElementById('roundsModal'));
                roundsModal.show();
            @endif
    var editRoundsModal = document.getElementById('editRoundsModal');

    editRoundsModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var roundId = button.getAttribute('data-id');
        var roundType = button.getAttribute('data-type');
        var numberOfOvers = button.getAttribute('data-number_of_overs');
        var oversPerBowler = button.getAttribute('data-overs_per_bowler');
        var teamsToQualify = button.getAttribute('data-teams_to_qualify');
 var baseUrl = "{{ url('/') }}";
        var form = document.getElementById('edit-round-form');
        form.action = baseUrl+'/tournaments-round-update/' + roundId;

        form.querySelector('#type').value = roundType;
        form.querySelector('#edit_number_of_overs').value = numberOfOvers;
        form.querySelector('#edit_overs_per_bowler').value = oversPerBowler;
        form.querySelector('#edit_teams_to_qualify').value = teamsToQualify;
    });
});
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.form-check-input').forEach(function (checkbox) {
        checkbox.addEventListener('change', function (event) {
            if (this.checked) {
                // Show confirmation dialog for toggling on
                if (!confirm('Are you sure the round is completed? Ready to add groups for the next round.')) {
                    // Revert checkbox if user cancels
                    this.checked = false;
                    return;
                }
            }

            // Submit the form after confirmation
            this.closest('form').submit();
        });
    });
});
document.addEventListener('DOMContentLoaded', function () {
    var hasIncompleteRound = @json($hasIncompleteRound);
    var addRoundButton = document.getElementById('addRoundButton');
    
    if (hasIncompleteRound) {
        // Initialize the tooltip
        var tooltip = new bootstrap.Tooltip(addRoundButton, {
            title: 'Complete the current round to add a new one.',
            placement: 'top'
        });
        
        // Show the tooltip when the button is clicked
        addRoundButton.addEventListener('click', function (event) {
            event.preventDefault(); // Prevent any default action (like opening the modal)
            tooltip.show();
            
            // Hide the tooltip after 2 seconds
            setTimeout(function () {
                tooltip.hide();
            }, 2000);
        });
    } else {
        // Allow the modal to open if there are no incomplete rounds
        addRoundButton.addEventListener('click', function () {
            var roundsModal = new bootstrap.Modal(document.getElementById('roundsModal'));
            roundsModal.show();
        });
    }
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
