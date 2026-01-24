@extends('layouts.app')

@section('content')
<style>
    .addPlayerModal input:not(input[type="checkbox"]), .addPlayerModal select {
        height: 45px;
    }
    .nameError, .emailError, .empidError, .phoneError, .batstyleError, .bowlStyleError, .ballPreferencesError, .roleError {
        color: red;
        font-size: 10px;
    }
    .our-sponsers{
        display: none;
    }
     .my-teams{
        font-size: 25px;
    font-family: "Saira", Arial, Helvetica, sans-serif;
    margin: 0;
    color: #fff;
    text-decoration: none;
    }
    .my-teams:hover{
        color: #fff;
    }
    .addteam-button{
        display:flex;
        gap:10px;
    }
    
    .float-label-form-group label {
    top: 12px;
}

.modal.left .modal-body, .modal.right .modal-body {
    padding: 15px 15px;
    padding-top: 0px;
}
  .player-lists-info-header {
    position: sticky;
    top: 30px;
    z-index: 1;
}
  .note-container {
    border: 1px solid #f67474;
    background-color: #ffecec;
    color: #333;
    padding: 7px;
    border-radius: 5px;
    text-align: center;
}

.note-header {
    font-weight: bold;
    font-size: 16px;
    color: #b73232;
}

.note-content {
    font-size: 14px;
    line-height: 1.5;
    color: #333;
}

.note-link {
    color: #0056b3;
    text-decoration: none;
}

.note-link:hover {
    text-decoration: none;
    color: #003d99;
}
.cta-title{
  transform: skewX(15deg);
  }
  
  input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
 -webkit-appearance: none;
 margin: 0;
}
  .modal-backdrop {
    z-index: 1000;
}

.payment-modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.7);
    background: white;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    z-index: 1050; /* Increased z-index to be above backdrop */
    max-width: 600px;
    width: 90%;
    opacity: 0;
    transition: all 0.3s ease-in-out;
    font-family: "Saira", Arial, Helvetica, sans-serif;
}

.payment-modal.show {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
}

.modal-backdrop-custom {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1040;
    display: none;
}

.modal-backdrop-custom.show {
    display: block;
}

.payment-info {
    margin: 20px 0;
}

.payment-row {
    margin-bottom: 10px;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}

.payment-row:last-child {
    border-bottom: none;
}

.payment-row label {
    font-weight: 600;
    color: #333;
    display: block;
    margin-bottom: 5px;
}

.payment-row p {
    margin: 0;
    color: #666;
}

.payment-row i {
    font-size: 15px;
    text-align: center;
    color: #333;
    width: 100%;
    display: block;
}

.modal-buttons {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 15px;
}
.modal-buttons button {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-family: "Saira", Arial, Helvetica, sans-serif;
}

.modal-buttons .cancel-btn {
    background: #f1f1f1;
}
   .bonafide-modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.7);
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1050;
            max-width: 600px;
            width: 90%;
            opacity: 0;
            transition: all 0.3s ease-in-out;
            font-family: "Saira", Arial, Helvetica, sans-serif;
        }

        .bonafide-modal.show {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }

        /* Modal Content Styles */
        .bonafide-info {
            margin: 20px 0;
        }

        .bonafide-row {
            margin-bottom: 15px;
        }

        .bonafide-row label {
            font-weight: 600;
            color: #333;
            display: block;
            margin-bottom: 10px;
        }

        .file-upload-wrapper {
            position: relative;
            width: 100%;
        }

        .upload-status {
            height: 3px;
            background: #f0f0f0;
            border-radius: 3px;
            margin-top: 8px;
            overflow: hidden;
            display: none;
        }

        .progress-bar {
            height: 100%;
            background: #FBC638;
            transition: width 0.3s ease;
        }

        .file-name {
            font-size: 14px;
            color: #666;
            margin-top: 8px;
        }

        .validation-message {
            margin-top: 8px;
            font-size: 12px;
            display: none;
        }

        .validation-message.error {
            color: #dc3545;
        }

        .validation-message.success {
            color: #28a745;
        }

        /* Modal Buttons */
        .modal-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 15px;
        }

        .modal-buttons button {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-family: inherit;
        }

        .modal-buttons .cancel-btn {
            background: #f1f1f1;
        }

        .modal-buttons .confirm-btn {
            background: #FBC638;
            color: #000;
        }

        .confirm-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        /* Responsive Styles */
        @media(max-width:767px) {
            .bonafide-row label {
                font-size: 14px;
            }
            
            .bonafide-title {
                font-size: 14px;
                margin-bottom: 0px !important;
            }

            .bonafide-modal {
                padding: 15px;
            }
        }

@media(max-width:767px){
    .payment-row label{
        font-size: 14px;
    }

    .payment-row p {
        font-size: 14px
    }

    .payment-row i {
        font-size: 14px
    }

    .payment-info {
        margin: 10px 0;
    }

    .payment-modal {
        padding: 10px
    }

    .payment-title{
        font-size: 14px;
        margin-bottom: 0px !important; 
    }

    .addteam-button{
        margin: 20px 0px;
        flex-wrap: wrap;
    }

    .addteam-button .cta {
        flex: 1;
        padding: 10px !important;
        white-space: nowrap;
        align-items: center;
        justify-content: center;
    }
}
   .spinner-border {

    vertical-align: middle;

    margin-right: 5px;

}
</style>
{{-- <body class="addnewplayer-body"> --}}

    <section class="addnewplayer-title-wrap fixed-second-header">
        <i class="right-celebration"></i>
        <div class="container h-100">
            <div class="row h-100 d-flex align-items-center">
                <div class="col-12">
                    <div class="add-teamname-wrap">
                        <div class="addteam-logo d-flex align-items-center">
                            <figure style="overflow: hidden;">
                                <img src="{{ config('constants.upload_url') . '/team_logos/' . $team_logo->logo }}" alt="Team Logo" />
                                
                            </figure>
                            <figcaption>
                                <h5>{{ $team->name }}</h5>
                            </figcaption>
                        </div>
                        

                            
                        <div class="addteam-button">
                             <a href="{{route('user-tournaments')}}" class="cta"><span class="cta-title">Tournaments</span></a>
                             @if($players->count() < 20)
                              <a class="cta" href="javascript:;" 
                                 onclick="addNewPlayer()" 
                                 @if($team_logo->is_added == 2) 
                                     style="pointer-events: none;" 
                                     title="Contact Admin for any Edits"
                                 @endif>
                                  <span>
                                      @if($team_logo->is_added == 2)
                                         Contact Admin for any Edits
                                      @else

                                              Add New Player

                                      @endif
                                  </span>
                              </a>
                      		@else
                      			<a class="cta" href="javascript:;" >Contact Admin for any Edits</a>
                      		@endif
                          <a class="cta" href="javascript:void(0);" onclick="showPaymentModal()">Payment Info</a>
                          @if($isAppliedTournament)
                              <a class="cta" href="javascript:;" id="openBonafideForm">Add Bonafide</a>
                          @endif
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
    </section>

    <main class="main-wrapper-start addnewplay-main">
        <div class="container">
            <div class="row">
                <div class="col-12">
                   
                    <div class="player-lists-wrapper">
                      @if($players->count() > 50 )
                      <div class="note-container my-3">
    <p class="note-header m-0">
        Note:
    </p>
    <p class="note-content m-0">
        Please add a minimum of 11 and a maximum of 20 players in teams and click <strong>Submit</strong> to finalize the team.
    </p>
    <p class="note-content m-0">
        In tournaments, tournament details are displayed. Click <a href="{{route('user-tournaments')}}" class="note-link">Apply</a> to register for the tournament.
    </p>
</div>
                       @endif
                     @if (session('success'))
                      <div id="successMessage" class="alert alert-success text-center">
                          {{ session('success') }}
                      </div>
                  @endif


                        <div class="player-lists-info-header">
                            <div class="player-lists-info-head">
                                <div class="playersinfo sno head">S.No</div>
                                <div class="playersinfo profilepic head">Image</div>
                                <div class="playersinfo name head">Name</div>
                                <div class="playersinfo email head">Email ID</div>
                                <div class="playersinfo empid head">Employee ID</div>
                                <div class="playersinfo phoneno head">Phone No</div>
                                <div class="playersinfo batstyle head">Batting Style</div>
                                <div class="playersinfo bowlingstyle head">Bowling Style</div>
                                <div class="playersinfo role head">Role</div>
                                <div class="playersinfo actions head" @if($team_logo->is_added == 2)
                                    style="display:none;"
                                @endif>Actions</div>
                            </div>
                        </div>
                        @foreach($players as $player)
                        <div class="player-lists-info" is_captain_selected="{{ $player->is_captain }}">
                            <div class="player-lists-info-head">
                                <div class="playersinfo sno">{{ $loop->iteration }}</div>
                                                               <div class="playersinfo profilepic"><figure><img src="{{ config('constants.upload_url') . '/player_images/' . $player->image }}" alt="Player Image" /></figure></div>

                                <div class="playersinfo name">{{ $player->name }}</div>
                                <div class="playersinfo email">{{ $player->email }}</div>
                                <div class="playersinfo empid">{{ $player->empid }}</div>
                                <div class="playersinfo phoneno">{{ $player->phone }}</div>
                                <div class="playersinfo batstyle">{{ $player->batting_style ?? '-' }}</div>
                                <div class="playersinfo bowlingstyle">{{ $player->bowling_style  ?? '-' }}</div>
                                <div class="playersinfo role">{{ $player->role }}</div>
                                <div class="playersinfo actions" @if($team_logo->is_added == 2)
                                    style="display:none;"
                                @endif>
                                    <div class="playersinfoicon edit" onclick="openEditModal({{ json_encode($player) }})" 
                                     @if($team_logo->is_added == 2) 
                                        style="pointer-events: none; opacity: 0.9;" 
                                    @endif>
                                        <span>Edit</span>
                                    </div>
                                  
                                  <div class="playersinfoicon delete">
                                        <form id="deletePlayerForm_{{ $player->id }}" action="{{ route('delete-player', $player->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="delete-btn"
                                                style="outline: 0; border: 0; appearance: none; padding: 0;"
                                                onclick="showDeleteModal({{ $player->id }})">
                                                <span>Delete</span>
                                            </button>
                                        </form>
                                    </div>
                                  @if($team_logo->is_added == 2)
                                        style="pointer-events: none; opacity: 0.9;"
                                    @endif
                                       
                                </div>
                            </div>
                        </div>
                        @endforeach
                        <div class="submit-section text-end mt-4">
                            {{--   <form action="{{ route('submit-team') }}" method="POST">
                                @csrf
                                <input type="hidden" name="team_id" value="{{ $team->team_id }}">
                                <button type="submit" class="btn btn-primary" id="submitTeamButton" 
                                  @if($players->count() < 11 || $players->count() > 20 || $team_logo->is_added == 2)
                                    disabled
                                @endif>
                                    Submit Team
                                </button>
                            </form>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </main>
	<div class="modal fade" id="deletePlayerModal" tabindex="-1" aria-labelledby="deletePlayerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deletePlayerModalLabel">Delete Player</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this player?
                </div>
                <div class="modal-footer" style="flex-direction: row;flex-wrap: nowrap; gap: 20px;">
                    <button type="button" class="btn btn-secondary my-0" data-bs-dismiss="modal">No</button>
                    <button type="button" class="btn btn-danger my-0" id="confirmDelete">Yes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scrollable modal -->
    <div class="modal right fade addPlayerModal" id="addPlayerModal" tabindex="-1" aria-labelledby="addPlayerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-center" id="modalTitle">Add Player</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                     <form id="playerForm" action="{{ route('storeplayer') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="player_id" id="player_id">
                        <input type="hidden" name="team_id" value="{{ $team->team_id }}">
                        <div class="float-label-form-group">

                            <input type="text" class="form-control" id="addplayerName" name="name" placeholder="">
                            <label for="addplayerName" class="form-label">Player Name</label>

                        </div> <span id="nameError" class="nameError"></span>
                        <div class="float-label-form-group">
                            <input type="email" class="form-control" id="addplayerEmail" name="email" placeholder="">
                            <label for="addplayerEmail" class="form-label">Email address</label>

                        </div> <span id="emailError" class="emailError"></span>
                        <div class="float-label-form-group">
                            <input type="text" class="form-control" id="addplayerEmpid" name="empid" placeholder="">
                            <label for="addplayerEmpid" class="form-label">Employee ID</label>

                        </div><span id="empidError" class="empidError"></span>
                        <div class="float-label-form-group mb-3">
                            <input type="number" class="form-control" id="addplayerPhone" name="phone" placeholder="">
                            <label for="addplayerPhone" class="form-label">Phone Number</label>

                        </div> <div id="phoneError" class="phoneError"></div>
                        <div class="mb-3">
                            {{-- <label for="fileInput" class="form-label">Upload Player Image</label> --}}
                            <div class="dropzone" id="dropzone">Drag and drop an image or click to upload</div>
                            <input type="file" id="fileInput" name="image" style="display: none;">
                        </div>
                       <div id="imageError" class="phoneError mb-2"></div>
                       

                         <div class="float-label-form-group mb-2">
                            {{-- <label for="role" class="form-label">Player Role</label> --}}
                            <select class="form-select" id="role" name="role">
                                <option value="" selected>Select Player Role</option>
                                <option value="Batsman">Batsman</option>
                                <option value="Bowler">Bowler</option>
                                <option value="All-Rounder">All-Rounder</option>
                                <option value="Wicketkeeper">Wicketkeeper</option>
                            </select>

                        </div><div id="roleError" class="roleError"></div>
                        <div class="float-label-form-group mb-4" id="battingStyleWrapper">
                            <select class="form-select" id="battingStyle" name="batting_style">
                                <option value="" selected>Select Batting Style</option>
                                <option value="Right Hand">Right Hand Batsman</option>
                                <option value="Left Hand">Left Hand Batsman</option>
                            </select>
                            <div id="batstyleError" class="batstyleError"></div>
                        </div>
                        <div class="float-label-form-group mb-4" id="bowlingStyleWrapper">
                            <select class="form-select" id="bowlingStyle" name="bowling_style">
                                <option value="" selected>Select Bowling Style</option>
                                <option value="Right Arm Fast">Right Arm Fast Medium</option>
                                <option value="Left Arm Fast">Left Arm Fast Medium</option>
                                <option value="Right Arm Off">Right Arm Off Spin</option>
                                <option value="Left Arm Off">Left Arm Off Spin</option>
                                <option value="Right Arm Leg">Right Arm Leg Spin</option>
                                <option value="Left Arm Leg">Left Arm Leg Spin</option>
                            </select>
                            <div id="bowlStyleError" class="bowlStyleError"></div>
                        </div>
                       
                       <div class="float-label-form-group mb-2">
                            <select class="form-select" id="ballPreferences" name="ball_preferences">
                                <option value="" selected>Select Ball Type</option>
                                <option value="All">All</option>
                                <option value="Red Tennis">Red Tennis</option>
                                <option value="Green Tennis">Green Tennis</option>
                                <option value="White Ball">White Ball</option>
                            </select>
                        </div>
                        <div id="ballPreferencesError" class="ballPreferencesError"></div>

                        <div class="form-check h-0">
                            <input class="form-check-input" type="checkbox" id="flexCheckChecked" name="is_captain" style="height: 18px">
                            <label class="form-check-label" for="flexCheckChecked" id="is_captain_label">
                                Captain
                            </label>
                        </div>
                    </div>
                                        <div id="generalError" class=" text-center generalError"></div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="submitButton">

    <span class="spinner-border spinner-border-sm d-none" id="submitSpinner" role="status" aria-hidden="true"></span>

    Save Player

</button>


                    </div>
                </form>
            </div>
        </div>
    </div>
         <!-- Payment Info Modal -->
    <div class="modal-backdrop-custom" id="customBackdrop"></div>
    <div class="payment-modal" id="paymentModal">
        <div class="payment-modal-content">
            <h5 class="mb-4 payment-title">Please make your payments to the following account:</h5>
            <div class="payment-info">
                <div class="payment-row">
                    <label>Bank Name:</label>
                    <p> South Indian Bank, NGGO Colony Branch</p>
                </div>
                <div class="payment-row">
                    <label>Account Number:</label>
                    <p>0206053000041882</p>
                </div>
                <div class="payment-row">
                    <label>Account Holder Name:</label>
                    <p>R Vipin</p>
                </div>
                <div class="payment-row">
                    <label>IFSC Code:</label>
                    <p>SIBL0000206</p>
                </div>
                <div class="payment-row">
                    <label>UPI Payment Details:</label>
                    <p>GPay/PhonePe: 8122012060 (Vipin Raj)</p>
                </div>
                <div class="payment-row">
                    <label>Registration Fees:</label>
                    <p>₹6000</p>
                </div>
                <div class="payment-row text-align-center">
                    <i>Ensure to mention your team name in the payment reference for identification.</i>
                </div>
            </div>
            <div class="modal-buttons">
                <button class="cancel-btn" id="closePaymentModal" onclick="closePaymentModal()">Close</button>
            </div>
        </div>
    </div>
      <div class="modal-backdrop-custom" id="bonafideBackdrop"></div>
    <div class="bonafide-modal" id="bonafideModal">
        <div class="bonafide-modal-content">
            <h5 class="mb-4 bonafide-title">Upload Bonafide Certificate</h5>
            <form id="bonafideForm" enctype="multipart/form-data">
                <div class="bonafide-info">
                    <div class="bonafide-row">
                        <label>Bonafide Certificate (PDF only)*</label>
                        <div class="file-upload-wrapper">
                            <input type="file" 
                                   id="bonafideFile" 
                                   name="team_bonafide" 
                                   class="form-control" 
                                   accept=".pdf"
                                   required>
                            <div class="upload-status">
                                <div class="progress-bar" id="uploadProgress" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="file-name" id="fileName"></div>
                        <div class="validation-message" id="fileValidation"></div>
                    </div>
                </div>
                <div class="modal-buttons">
                    <button type="button" class="cancel-btn" id="cancelBonafideBtn">Close</button>
                    <button type="submit" class="confirm-btn" id="uploadBtn">Upload</button>
                </div>
            </form>
        </div>
    </div>
</body>

<script>

document.getElementById('role').addEventListener('change', function () {
    const role = this.value;
    const battingStyleWrapper = document.getElementById('battingStyleWrapper');
    const bowlingStyleWrapper = document.getElementById('bowlingStyleWrapper');

    // Reset visibility
    battingStyleWrapper.style.display = 'none';
    bowlingStyleWrapper.style.display = 'none';

    // Show relevant fields based on role
    if (role === 'Batsman' || role === 'Wicketkeeper') {
        battingStyleWrapper.style.display = 'block';
    } else if (role === 'Bowler') {
        bowlingStyleWrapper.style.display = 'block';
    } else if (role === 'All-Rounder') {
        battingStyleWrapper.style.display = 'block';
        bowlingStyleWrapper.style.display = 'block';
    }
});

// Initialize visibility on page load based on the current value
document.addEventListener('DOMContentLoaded', () => {
    const roleDropdown = document.getElementById('role');
    if (roleDropdown) {
        roleDropdown.dispatchEvent(new Event('change'));
    }
});

let captain_selected = false;

function openEditModal(player) {
    // Set the form action for editing
    $('#playerForm').attr('action', '/update-player/' + player.id);

    // Update the modal title
    $('#modalTitle').text('Edit Player');

    // Populate the fields with existing player data
    $('#player_id').val(player.id);
    $('#addplayerName').val(player.name);
    $('#addplayerEmail').val(player.email);
    $('#addplayerEmpid').val(player.empid);
    $('#addplayerPhone').val(player.phone);
    $('#battingStyle').val(player.batting_style);
    $('#bowlingStyle').val(player.bowling_style);
    $('#role').val(player.role);
    $('#ballPreferences').val(player.ball_preferences);
  
     updateStyleFieldsVisibility(player.role); // Explicitly update the visibility of style fields

    // Clear dropzone text initially
    $('#dropzone').text('Drag and drop an image or click to upload');

    // Display existing image if available
    if (player.image) {
        const imageUrl = `{{ config('constants.upload_url') }}/player_images/${player.image}`;
        $('#dropzone').html(`
            <img src="${imageUrl}" alt="Player Image" style="max-width: 100%; height: auto; border-radius: 5px;">
        `);
    } else {
        $('#dropzone').text('Drag and drop an image or click to upload');
    }

    // Handle captain selection
    if (player.is_captain == 0) {
        $('input[name="is_captain"]').prop('checked', false);
        if (captain_selected == false) {
            $('input[name="is_captain"]').prop('disabled', false);
        } else {
            $('input[name="is_captain"]').prop('disabled', true);
            $('#is_captain_label').html(`
                <span style="color: red; font-size: 10px;">* Captain has already been selected.</span>
            `);
        }
    } else {
        captain_selected = true;
        $('input[name="is_captain"]').prop('checked', true);
        $('input[name="is_captain"]').prop('disabled', false);
    }

    // Show the modal
    $('#addPlayerModal').modal('show');
}


let container = document.querySelectorAll('div[is_captain_selected]')
container.forEach(e => {
    let is_captain_selected = e.getAttribute('is_captain_selected')
    if(is_captain_selected == 1) {
        captain_selected = true;
    }
})
 

function addNewPlayer() {
    $('#addPlayerModal').modal('show');
    $('#modalTitle').text('Add New Player');
   $('#playerForm').attr('action', '{{ route('storeplayer') }}'); // Reset to the store route
    $('#player_id').val("");
    $('#addplayerName').val("");
    $('#addplayerEmail').val("");
    $('#addplayerEmpid').val("");
    $('#addplayerPhone').val("");
    $('#fileInputImage').val("");
    $('#fileInput').val("");
    $('#battingStyle').val("");
    $('#bowlingStyle').val("");
    $('#role').val("");
  $('#dropzone').text('Drag and drop an image or click to upload');
    $('input[name="is_captain"]').prop('checked', false);
    if(captain_selected == false) {
        $('input[name="is_captain"]').prop('disabled', false);
    } else {
        $('input[name="is_captain"]').prop('disabled', true);
        $('#is_captain_label').html(`<span style="color: red; font-size: 10px;">* Captain has been already selected.</span>`);
    }
}

const form = document.getElementById('playerForm');
const names = document.getElementById('addplayerName');
const email = document.getElementById('addplayerEmail');
const empid = document.getElementById('addplayerEmpid');
const phone = document.getElementById('addplayerPhone');
const battingStyle = document.getElementById('battingStyle');
const bowlingStyle = document.getElementById('bowlingStyle');
const role = document.getElementById('role');
const ballPreferences = document.getElementById('ballPreferences');

const namesError = document.getElementById('nameError');
const emailError = document.getElementById('emailError');
const empidError = document.getElementById('empidError');
const phoneError = document.getElementById('phoneError');
const batstyleError = document.getElementById('batstyleError');
const bowlstyleError = document.getElementById('bowlStyleError');
const ballPreferencesError = document.getElementById('ballPreferencesError');
const roleError = document.getElementById('roleError');
const generalError = document.getElementById('generalError');
const allowedFormats = ['image/jpeg', 'image/png', 'image/jpg' , 'image/webp'];
const MB = 1024;
const sizeLimit = 1;
const convertToMB = (size) => (size/MB/MB).toFixed(4);
const imageSizeError = `Image field should be less than or equal to ${sizeLimit} MB.`;

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    let isValid = true;
    generalError.innerHTML = "";
  
   const submitButton = document.getElementById('submitButton');

    const submitSpinner = document.getElementById('submitSpinner');

    

    submitButton.disabled = true; // Disable button

    submitSpinner.classList.remove('d-none'); 

    if (names.value === "" || names.value === null) {
        e.preventDefault();
        namesError.innerHTML = "Name field is required";
        isValid = false;
    } else {
        namesError.innerHTML = "";
    }

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email.value === "") {
        e.preventDefault();
        emailError.innerHTML = "Email field is required";
        isValid = false;
    } else if (!emailPattern.test(email.value)) {
        e.preventDefault();
        emailError.innerHTML = "Please enter a valid email address";
        isValid = false;
    } else {
        //try {
            // Perform AJAX call to check email uniqueness
          //  const response = await fetch('/check-email', {
            //    method: 'POST',
              //  headers: {
                //    'Content-Type': 'application/json',
                  //  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                //},
                //body: JSON.stringify({ email: email.value,player_id: document.getElementById('player_id').value }),
            //});

            //const data = await response.json();
            //if (!data.success) {
              //  emailError.innerHTML = "This email already exists.";
                //isValid = false;
            //} else {
              //  emailError.innerHTML = ""; // Clear error if email is unique
            //}
        //} catch (error) {
          //  console.error('Error:', error);
            //emailError.innerHTML = "An error occurred while checking email.";
            //isValid = false;
        //}
      emailError.innerHTML = "";
    }

    if (empid.value === "") {
        e.preventDefault();
        empidError.innerHTML = "Employee ID field is required";
        isValid = false;
    } else {
        empidError.innerHTML = "";
    }

    if (phone.value === "") {
    e.preventDefault();
    phoneError.innerHTML = "Phone number field is required";
    isValid = false;
    } else if (!/^[6-9][0-9]{9}$/.test(phone.value)) {
        e.preventDefault();
        phoneError.innerHTML = "Phone number must be valid and be 10 digits long";
        isValid = false;
    } else {
        phoneError.innerHTML = "";
    }
  
   const fileInput = document.getElementById('fileInput'); // Ensure you have an input with this ID
   const file = fileInput.files[0]; // Access the first file
   if(file){
        const size = file.size;
        if(convertToMB(size) > sizeLimit) {
            e.preventDefault(); // Prevent form submission
            imageError.innerHTML = imageSizeError;
            isValid = false;
        }
    }
   // if (!file) { // Check if no file is selected
   //     e.preventDefault(); // Prevent form submission
    //    imageError.innerHTML = "Image field is required"; // Display an error message
    //    isValid = false;
    //}

    // if (battingStyle.value == "") {
    //     e.preventDefault();
    //     batstyleError.innerHTML = "Batting style field is required";
    //     isValid = false;
    // } else {
    //     batstyleError.innerHTML = "";
    // }

    // if (bowlingStyle.value == "") {
    //     e.preventDefault();
    //     bowlstyleError.innerHTML = "Bowling style field is required";
    //     isValid = false;
    // } else {
    //     bowlstyleError.innerHTML = "";
    // }

    if (role.value == "") {
        e.preventDefault();
        roleError.innerHTML = "Player role field is required";
        isValid = false;
    } else {
        roleError.innerHTML = "";
    }
  
    if (ballPreferences.value == "") {
        e.preventDefault();
        ballPreferencesError.innerHTML = "Player ball Preferences field is required";
        isValid = false;
    } else {
        ballPreferencesError.innerHTML = "";
    }

    if (!isValid) {
        // Show general error message
        generalError.innerHTML = "Please fill all the required fields correctly.";
        generalError.style.color = "red";
        generalError.style.fontSize = "12px";
        generalError.style.marginBottom = "10px";
      
        submitButton.disabled = false;

        submitSpinner.classList.add('d-none');

        // Prevent form submission
        e.preventDefault();
        return false;
    } 
form.submit();
});

// Dropzone functionality
const dropzone = document.getElementById('dropzone');
const fileInput = document.getElementById('fileInput');
  

// dropzone.addEventListener('click', () => {
//     fileInput.click();
// });
  
  fileInput.addEventListener('change', (e) => {
    const file = fileInput.files[0];
    validateFile(file);
});

dropzone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropzone.classList.add('dragover');
});

dropzone.addEventListener('dragleave', () => {
    dropzone.classList.remove('dragover');
});

dropzone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropzone.classList.remove('dragover');
    fileInput.files = e.dataTransfer.files;
    updateDropzoneText();
});

fileInput.addEventListener('change', updateDropzoneText);

function updateDropzoneText() {
    if (fileInput.files.length > 0) {
        dropzone.textContent = `File selected: ${fileInput.files[0].name}`;
    } else {
        dropzone.textContent = 'Drag and drop an image or click to upload';
    }
}

// Initialize captain selection status
document.addEventListener('DOMContentLoaded', () => {
    let captainElements = document.querySelectorAll('div[is_captain_selected="1"]');
    if (captainElements.length > 0) {
        captain_selected = true;
        let captainCheckbox = document.querySelector('input[name="is_captain"]');
        if (captainCheckbox) {
            captainCheckbox.disabled = true;
            document.getElementById('is_captain_label').innerHTML = '<span style="color: red; font-size: 10px;">* Captain has been already selected.</span>';
        }
    }
});
  
  function validateFile(file) {
    if (file) {
        const size = file.size;
        if (!allowedFormats.includes(file.type)) {
            generalError.innerHTML = "Unsupported file format. Please upload a JPG, PNG, or JPEG image.";
            generalError.style.color = "red";
            fileInput.value = '';
            dropzone.textContent = 'Drag and drop an image or click to upload';
        } else if(convertToMB(size) > sizeLimit) {
            imageError.innerHTML = imageSizeError;
        } else {
            imageError.innerHTML = "";
            generalError.innerHTML = "";
            dropzone.textContent = `File selected: ${file.name}`;
        }
    } else {
        dropzone.textContent = 'Drag and drop an image or click to upload';
    }
}
  
  function updateStyleFieldsVisibility(role) {
    const battingStyleWrapper = document.getElementById('battingStyleWrapper');
    const bowlingStyleWrapper = document.getElementById('bowlingStyleWrapper');

    // Reset visibility
    battingStyleWrapper.style.display = 'none';
    bowlingStyleWrapper.style.display = 'none';

    // Show relevant fields based on role
    if (role === 'Batsman' || role === 'Wicketkeeper') {
        battingStyleWrapper.style.display = 'block';
    } else if (role === 'Bowler') {
        bowlingStyleWrapper.style.display = 'block';
    } else if (role === 'All-Rounder') {
        battingStyleWrapper.style.display = 'block';
        bowlingStyleWrapper.style.display = 'block';
    }
}

  setTimeout(() => {
        const message = document.getElementById('successMessage');
        if (message) {
            message.style.display = 'none';
        }
    }, 3000);
  
  let deletePlayerId = null;
    function showDeleteModal(playerId) {
        deletePlayerId = playerId;
        const deleteModal = new bootstrap.Modal(document.getElementById('deletePlayerModal'));
        deleteModal.show();
    }
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (deletePlayerId) {
                document.getElementById(`deletePlayerForm_${deletePlayerId}`).submit();
            }
            const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deletePlayerModal'));
            deleteModal.hide();
        });
    });
  
  function showPaymentModal() {
        const modal = document.getElementById('paymentModal');
        const backdrop = document.getElementById('customBackdrop');
        
        modal.style.display = 'block';
        backdrop.style.display = 'block';
        
        setTimeout(() => {
            modal.classList.add('show');
            backdrop.classList.add('show');
        }, 10);

        backdrop.addEventListener('click', closePaymentModal);
        document.addEventListener('keydown', handleEscapeKey);
    }

    function closePaymentModal() {
        const modal = document.getElementById('paymentModal');
        const backdrop = document.getElementById('customBackdrop');
        
        modal.classList.remove('show');
        backdrop.classList.remove('show');
        
        setTimeout(() => {
            modal.style.display = 'none';
            backdrop.style.display = 'none';
        }, 300); 

        backdrop.removeEventListener('click', closePaymentModal);
        document.removeEventListener('keydown', handleEscapeKey);
    }

    function handleEscapeKey(event) {
        if (event.key === 'Escape') {
            closePaymentModal();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const closeBtn = document.getElementById('closePaymentModal');
        if (closeBtn) {
            closeBtn.addEventListener('click', closePaymentModal);
        }
    });
  
      function showToast(message, type = 'success') {
        Toastify({
            text: message,
            duration: 3000,
            gravity: "top",
            position: "right",
            style: {
                background: type === 'success' ? "#28a745" : "#dc3545",
            }
        }).showToast();
    }
    
    function validateFile(file) {
        const validationMessage = document.getElementById('fileValidation');
        const uploadBtn = document.getElementById('uploadBtn');
        
        if (!file) {
            showValidationMessage('Please select a file', 'error');
            return false;
        }
        
        if (file.type !== 'application/pdf') {
            showValidationMessage('Please upload PDF files only', 'error');
            return false;
        }
        
        if (file.size > 5 * 1024 * 1024) { // 5MB
            showValidationMessage('File size should not exceed 5MB', 'error');
            return false;
        }
        
        showValidationMessage('File is valid', 'success');
        return true;
    }
    
    function showValidationMessage(message, type) {
        const validationElement = document.getElementById('fileValidation');
        validationElement.textContent = message;
        validationElement.style.display = 'block';
        validationElement.className = 'validation-message ' + type;
        document.getElementById('uploadBtn').disabled = (type === 'error');
    }
    
    document.getElementById('bonafideFile').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const fileName = document.getElementById('fileName');
        
        if (file) {
            fileName.textContent = 'Selected file: ' + file.name;
            validateFile(file);
        } else {
            fileName.textContent = '';
            showValidationMessage('No file selected', 'error');
        }
    });
    
    document.getElementById('bonafideForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const uploadBtn = document.getElementById('uploadBtn');
        const progressBar = document.getElementById('uploadProgress');
        const uploadStatus = document.querySelector('.upload-status');
        
        if (!validateFile(formData.get('team_bonafide'))) {
            return;
        }
        
        uploadBtn.disabled = true;
        uploadBtn.textContent = 'Uploading...';
        uploadStatus.style.display = 'block';
        
        const xhr = new XMLHttpRequest();
        
        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                progressBar.style.width = percentComplete + '%';
            }
        });
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                uploadBtn.disabled = false;
                uploadBtn.textContent = 'Upload';
                
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        showToast('Bonafide certificate uploaded successfully');
                        closeBonafideModal();
                    } else {
                        showToast(response.message || 'Upload failed', 'error');
                    }
                } else {
                    showToast('Error uploading file', 'error');
                }
                
                // Reset progress
                setTimeout(() => {
                    progressBar.style.width = '0%';
                    uploadStatus.style.display = 'none';
                }, 1000);
            }
        };
        
        xhr.open('POST', '{{ route("upload-bonafide") }}', true);
        xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
        xhr.send(formData);
    });
    
    function openBonafideModal() {
        const modal = document.getElementById('bonafideModal');
        const backdrop = document.getElementById('bonafideBackdrop'); // Changed to correct ID
        
        modal.style.display = 'block';
        backdrop.style.display = 'block';
        
        setTimeout(() => {
            modal.classList.add('show');
            backdrop.classList.add('show');
        }, 10);
    }
    
    function closeBonafideModal() {
        const modal = document.getElementById('bonafideModal');
        const backdrop = document.getElementById('bonafideBackdrop'); // Changed to correct ID
        
        modal.classList.remove('show');
        backdrop.classList.remove('show');
        
        setTimeout(() => {
            modal.style.display = 'none';
            backdrop.style.display = 'none';
            document.getElementById('bonafideForm').reset();
            document.getElementById('fileName').textContent = '';
            document.getElementById('fileValidation').style.display = 'none';
            document.getElementById('uploadProgress').style.width = '0%';
            document.querySelector('.upload-status').style.display = 'none';
            document.getElementById('uploadBtn').disabled = false;
        }, 300);
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('openBonafideForm').addEventListener('click', function(e) {
            e.preventDefault();
            openBonafideModal();
        });
        
        document.getElementById('cancelBonafideBtn').addEventListener('click', function() {
            closeBonafideModal();
        });
        
        document.getElementById('bonafideBackdrop').addEventListener('click', function() {
            closeBonafideModal();
        });
        
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeBonafideModal();
            }
        });
    })

  
  
</script>

@endsection
