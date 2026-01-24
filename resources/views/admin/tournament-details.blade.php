@extends('layouts.admin')

@section('content')
<style>
    .myTorunments-table .myTorunments-body .myTorunments-body-col.has-myTorunments-rounds.actions {
        flex: 1 0 26%;
    }
    .myTorunments-table .myTorunments-head .myTorunments-head-col.has-myTorunments-rounds.actions {
        flex: 1 0 26%;
    }
    .myTorunments-table .myTorunments-head .myTorunments-head-col.name.has-myTorunments-rounds, .myTorunments-table .myTorunments-head .myTorunments-body-col.name.has-myTorunments-rounds, .myTorunments-table .myTorunments-body .myTorunments-head-col.name.has-myTorunments-rounds, .myTorunments-table .myTorunments-body .myTorunments-body-col.name.has-myTorunments-rounds {
        flex: 0 0 35%;
        max-width: 50%;
    }
    .my-torunments-wrap {
        padding: 0px !important;
    }
    .torunment-teams {
        margin-top: 180px !important;
        margin-bottom: 15px;
    }
    .torunment-groups {
        margin-top: 0px !important;
        margin-bottom: 15px;
    }
    .torunment-rounds {
        margin-top: 5px !important;
        margin-bottom: 15px;
    }
    .myTorunments-table .myTorunments-head .myTorunments-head-col.roundName, .myTorunments-table .myTorunments-head .myTorunments-head-col.teams, .myTorunments-table .myTorunments-head .myTorunments-body-col.roundName, .myTorunments-table .myTorunments-head .myTorunments-body-col.teams, .myTorunments-table .myTorunments-body .myTorunments-head-col.roundName, .myTorunments-table .myTorunments-body .myTorunments-head-col.teams, .myTorunments-table .myTorunments-body .myTorunments-body-col.roundName, .myTorunments-table .myTorunments-body .myTorunments-body-col.teams {
        align-items: center;
    }
    .myTorunments-table .myTorunments-head .myTorunments-head-col.has-myTorunments-myTeam, .myTorunments-table .myTorunments-head .myTorunments-body-col.has-myTorunments-myTeam, .myTorunments-table .myTorunments-body .myTorunments-head-col.has-myTorunments-myTeam, .myTorunments-table .myTorunments-body .myTorunments-body-col.has-myTorunments-myTeam {
        flex: 0 0 91%;
        max-width: 25%;
    }
    .myTorunments-table .myTorunments-head .myTorunments-head-col.roundName, .myTorunments-table .myTorunments-head .myTorunments-head-col.teams, .myTorunments-table .myTorunments-head .myTorunments-body-col.roundName, .myTorunments-table .myTorunments-head .myTorunments-body-col.teams, .myTorunments-table .myTorunments-body .myTorunments-head-col.roundName, .myTorunments-table .myTorunments-body .myTorunments-head-col.teams, .myTorunments-table .myTorunments-body .myTorunments-body-col.roundName, .myTorunments-table .myTorunments-body .myTorunments-body-col.teams {
        flex: 1 0 61.3333% !important;
        max-width: 72.3333% !important;
    }
    .see-more {
        width: 120px;
        float: inline-end;
        height: 30px;
    }
    .pagination .page-item.active .page-link {
        z-index: auto;
    }
    .torunment-teams {
        margin-top: 0px !important;
        margin-bottom: 0px !important;
    }
    .tournament-card {
        border-radius: 8px;
        padding: 0px;
        margin: 10px;
        width: 48%;
        box-shadow: 0 8px 24px rgba(219, 224, 241, 0.7);
    }

    @media (max-width: 1024px) {
        .tournament-card {
        width: 96%;
        }
    }
    .tour-head{
        font-size: 20px;
        font-weight: 500;
        padding: 10px 20px 10px 10px;
        margin-bottom: 0px;
    }
    .add-img{
        /* margin-bottom: 10px; */
    }

    .see-listing{
        font-weight: 500;
        padding: 10px 20px 10px 10px;
        margin-bottom: 0px;
    }
    .see-listing a{
        text-decoration: none;
    }
.schedule_link:hover{
    text-decoration: none;
}
</style>

<section class="my-torunments-second-header fixed-second-header">
    <div class="container-fluid h-100">
        <div class="row h-100">
            <div class="col-12">
                <div class="title-wrap h-100">
                    <h2>{{$tournament->name}}</h2>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="row" style="margin-top: 150px !important;">
    <div class="col-6 tournament-card mb-4">
        <section class="my-torunments-wrap torunment-teams">
            <div >
                <div >
                    <div >
                        <div class="d-flex justify-content-between align-items-center">
                            <h2 class="tour-head">Teams</h2>
                            <a href="{{ route('tournaments-teams', $tournament->id) }}" style="text-align: end;" class="mx-2">
                                <img class="add-img"src="{{ asset('/uploads/images/icons8-plus.svg') }}" />
                                {{-- <button id="seeMoreBtn" class="btn btn-primary see-more">
                                    Add Teams
                                </button> --}}
                            </a>
                        </div>
                        <div class="myTorunments-table">
                            @if(count($paginatedTeams) > 0)
                                <div class="myTorunments-head">
                                    <div class="myTorunments-head-col sno has-myTorunments-myTeam" style="font-size: 15px">S.No</div>
                                    <div class="myTorunments-head-col name has-myTorunments-myTeam" style="font-size: 15px">Team Name</div>
                                    <div class="myTorunments-head-col name has-myTorunments-myTeam" style="font-size: 15px;white-space: nowrap;">Match Preference</div>
                                    <div class="myTorunments-head-col members has-myTorunments-myTeam" style="font-size: 15px">Members</div>
                                </div>

                                @foreach($paginatedTeams as $index => $team)
                                    <div class="myTorunments-body">
                                        <div class="myTorunments-body-col sno has-myTorunments-myTeam">{{ ($paginatedTeams->currentPage() - 1) * $paginatedTeams->perPage() + $index + 1 }}</div>
                                        <div class="myTorunments-body-col name has-myTorunments-myTeam"> <a href="{{ route('team_players', ['team_id' => $team->team_id]) }}">{{ $team->name }}</a></div>
                                        <div class="myTorunments-body-col name has-myTorunments-myTeam">{{ $team->formatted_preference }}</div>
                                        <div class="myTorunments-body-col members has-myTorunments-myTeam" style="text-align: left;white-space: nowrap;">
                                            @if(isset($team->green_tennis_count) && $team->green_tennis_count > 0 && $team->tournament_type === 'Green Tennis')
                                                Green Tennis: {{ $team->green_tennis_count }} <br>
                                            @endif
                                            Total Player: {{ $team->players_count }}
                                        </div>
                                    </div>
                                @endforeach

                                @if(count($paginatedTeams) >= 3)
                                    <div class="text-end see-listing">
                                        <a href="{{ route('tournaments-teams', $tournament->id) }}" class="">See Full List</a>
                                    </div>
                                @endif
                            @else
                                <div style="background-color: #f1f1f1; color:#614092; height: 50px; text-align: center; display: flex; align-items: center; justify-content: center; padding:40px 0px 40px 0px;">
                                    <p style="margin-bottom: 0px">
                                        "No teams available yet! &nbsp; Want to Add a new one? &nbsp; Head over to the&nbsp;
                                        <a href="{{ route('tournaments-teams', $tournament->id) }}" style="text-decoration:none;">
                                            <strong>'Add Teams' </strong>
                                        </a>page."
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="col-6 tournament-card mb-4">
        <section class="my-torunments-wrap torunment-groups">
            <div class="">
                <div class="">
                    <div class="">
                        <div class="myTorunments-table">
                            <div class="d-flex justify-content-between align-items-center">
                                <h2 class="tour-head">Groups</h2>
                                <a href="{{ route('tournaments-group', $tournament->id) }}" style="text-align: end;" class="mx-2">
                                    <img class="add-img"src="{{ asset('/uploads/images/icons8-plus.svg') }}" />
                                    {{-- <button id="seeMoreBtn" class="btn btn-primary see-more">
                                        Add Groups
                                    </button> --}}
                                </a>
                            </div>
                            @if(count($groups) > 0)
                                <div class="myTorunments-head">
                                    <div class="myTorunments-head-col sno">S.No</div>
                                    <div class="myTorunments-head-col groupName">Name</div>
                                    <div class="myTorunments-head-col teams">Teams</div>
                                </div>
                               @foreach($groups->take(3) as $group_id => $groupedTeams)
                                    <div class="myTorunments-body">
                                        <div class="myTorunments-body-col sno has-myTorunments-myGroups">{{ $loop->iteration }}</div>
                                        <div class="myTorunments-body-col groupName has-myTorunments-myGroups">{{ $groupedTeams->first()->group->group_name }}</div>
                                        <div class="myTorunments-body-col teams has-myTorunments-myGroups">
                                            @foreach($groupedTeams as $team)
                                                {{ $team->team->name }}@if(!$loop->last), @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach

                                 @if(count($groups) > 3)
                                    <div class="text-end see-listing">
                                        <a href="{{ route('tournaments-group', $tournament->id) }}" class="">See Full List</a>
                                    </div>
                                @endif
                            @else
                                <div style="background-color: #f1f1f1; color:#614092; height: 50px; text-align: center; display: flex; align-items: center; justify-content: center;  padding:40px 0px 40px 0px;">
                                    <p style="margin-bottom: 0px">
                                        "No Group available yet! &nbsp; Want to Add a new one? &nbsp; Head over to the&nbsp;
                                        <a href="{{ route('tournaments-group', $tournament->id) }}" style="text-decoration:none;">
                                            <strong>'Add Group' </strong>
                                        </a>page ."
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="col-8 tournament-card mb-4">
        <section class="my-torunments-wrap torunment-rounds">
            <div class="">
                <div class="">
                    <div class="">
                        <div class="myTorunments-table">
                            <div class="d-flex justify-content-between align-items-center">
                                <h2 class="tour-head">Rounds</h2>
                                <a href="{{ route('tournaments-round', $tournament->id) }}" style="text-align: end;" class="mx-2">
                                    <img class="add-img"src="{{ asset('/uploads/images/icons8-plus.svg') }}" />
                                    {{-- <button id="seeMoreBtn" class="btn btn-primary see-more">
                                        Add Rounds
                                    </button> --}}
                                </a>
                            </div>
                            @if(count($rounds) > 0)
                                <div class="myTorunments-head">
                                    <div class="myTorunments-head-col sno has-myTorunments-rounds">S.No</div>
                                    <div class="myTorunments-head-col name has-myTorunments-rounds">Round</div>
                                    {{-- <div class="myTorunments-head-col qualify has-myTorunments-rounds">To qualify</div> --}}
                                    <div class="myTorunments-head-col actions has-myTorunments-rounds">Actions</div>
                                    <div class="myTorunments-head-col actions has-myTorunments-rounds">Teams Qualify</div>
                                </div>
                                @foreach ($rounds as $round)
                                    <div class="myTorunments-body">
                                        <div class="myTorunments-body-col sno has-myTorunments-rounds">{{ $loop->iteration }}</div>
                                        <div class="myTorunments-body-col name has-myTorunments-rounds">{{ $round->type }}</div>
                                        {{-- <div class="myTorunments-body-col qualify has-myTorunments-rounds">{{ $round->teams_to_qualify }}</div> --}}
                                        <div class="myTorunments-body-col actions has-myTorunments-rounds">

                                            <!-- Edit Button -->
                                            <div class="edit icon d-none" data-bs-toggle="modal" data-bs-target="#editRoundsModal"
                                                 data-id="{{ $round->id }}"
                                                 data-type="{{ $round->type }}"
                                                 data-number_of_overs="{{ $round->number_of_overs }}"
                                                 data-overs_per_bowler="{{ $round->overs_per_bowler }}"
                                                 data-teams_to_qualify="{{ $round->teams_to_qualify }}">
                                                <i>
                                                    <img src="{{ asset('/uploads/images/pen.svg') }}" />
                                                </i>
                                            </div>

                                            <!-- Delete Button -->
                                            <form action="{{ route('delete-tournaments-round', $round->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this round?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="delete icon d-none" style="background: none; border: none; cursor: pointer;">
                                                    <i><img src="{{ asset('uploads/images/delete.png')}}" alt="Delete" /></i>
                                                </button>
                                            </form>

                                            <!-- Schedule Button -->
                                            <div class="schedule icon"  @if($round->status == 1) style="pointer-events: none; opacity: 0.5;" @endif>
                                                <i>
                                                    <a href="{{ route('tournaments.match', ['tournament_id' => $tournament->id, 'round_id' => $round->id]) }}" class="schedule_link">
                                                        <button class="btn btn-primary" style="width: 163px;height: 35px;">Schedule Matches</button>
                                                    </a>
                                                </i>
                                            </div>

                                        </div>

                                        <!-- Toggle Completed Status -->
                                        <div class="myTorunments-body-col actions has-myTorunments-rounds">
                                             {{ $round->teams_to_qualify }}
                                        </div>
                                    </div>
                                @endforeach

                                @if(count($rounds) >= 3)
                                    <div class="text-end see-listing">
                                        <a href="{{ route('tournaments-round', $tournament->id) }}" class="">See Full List</a>
                                    </div>
                                @endif
                            @else
                                <div style="background-color: #f1f1f1; color:#614092; height: 50px; text-align: center; display: flex; align-items: center; justify-content: center; padding:40px 0px 40px 0px;">
                                    <p style="margin-bottom: 0px">
                                        "No Rounds available yet! &nbsp; Want to Add a new one? &nbsp; Head over to the&nbsp;
                                        <a href="{{ route('tournaments-round', $tournament->id) }}" style="text-decoration:none;">
                                            <strong>'Add Rounds' </strong>
                                        </a>page ."
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

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
                        <input type="text" class="form-control" id="type" name="type">
                        @error('type')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="number_of_overs" class="form-label">No of Overs:</label>
                        <input type="number" class="form-control" id="number_of_overs" name="number_of_overs">
                        @error('number_of_overs')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="overs_per_bowler" class="form-label">Overs Per Bowler:</label>
                        <input type="number" class="form-control" id="overs_per_bowler" name="overs_per_bowler">
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

            var form = document.getElementById('edit-round-form');
            form.action = '/tournaments-round-update/' + roundId;

            form.querySelector('#type').value = roundType;
            form.querySelector('#edit_number_of_overs').value = numberOfOvers;
            form.querySelector('#edit_overs_per_bowler').value = oversPerBowler;
            form.querySelector('#edit_teams_to_qualify').value = teamsToQualify;
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
</script>
@endsection
