@extends('layouts.app')

@section('content')
<style>
  @media (max-width: 767.98px) {
    .fixed-second-header {
        height: 250px;
    }
}
  @media (max-width: 767.98px) {
    .addnewplay-main {
        margin-top: 300px !important;
    }
}
    .addPlayerModal input:not(input[type="checkbox"]), .addPlayerModal select {
        height: 45px;
    }
    .nameError, .emailError, .empidError, .phoneError, .batstyleError, .bowlStyleError, .roleError {
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
    .apply-btn{
    font-size: 16px;
    font-weight: 700;
    color: var(--primary);
    background: #FBC638;
    }
    .addteam-button{
        display:flex;
        gap:10px;
    }
    .preference-modal {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.7);
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        z-index: 1000;
        max-width: 570px;
        width: 100%;
        opacity: 0;
        transition: all 0.3s ease-in-out;
        font-family: "Saira", Arial, Helvetica, sans-serif;
    }
    .preference-modal.show {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
    .modal-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 999;
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
    }
    .modal-backdrop.show {
        opacity: 1;
    }
    .preference-select {
        width: 100%;
        padding: 8px;
        margin: 10px 0;
        border: 1px solid #ddd;
        border-radius: 4px;
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
    .modal-buttons .confirm-btn {
        background: #FBC638;
        color: var(--primary);
    }
    .modal-buttons .cancel-btn {
        background: #f1f1f1;
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
    z-index: 1001;
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
    z-index: 1001;
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

.confirm-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

@media(max-width:767px) {
    .bonafide-row label {
        font-size: 14px;
    }

    .bonafide-title {
        font-size: 14px;
        margin-bottom: 0px !important;
    }
}
.player-lists-info-head .playersinfo.empid {
    flex: 1 0 7%;
    max-width: 27%;
}
</style>
{{-- <body class="addnewplayer-body"> --}}
    <section class="addnewplayer-title-wrap fixed-second-header">
        <i class="right-celebration"></i>
        <div class="container h-100">
            <div id="tooltipMessage"
          style="display: none;
          position: fixed;
          height: 45px;
          top: 33%;
          left: 50%;
          transform: translate(-50%, -50%);
          background: rgb(248, 215, 218);
          color: rgb(114, 28, 36);
          border-radius: 5px;
          box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 6px;
          text-align: center;
          align-items: center;
          padding: 16px;">
               Please add players and submit the team to apply for tournament.
             </div>
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
                        {{-- <div class="addteam-button">
                        <a href="{{route('user-teams')}}" class="cta">My Team</a>
                        </div> --}}
                        <div class="addteam-button">
                            <a href="{{route('user-tournaments')}}" class="cta">Tournaments</a>
                            <a class="cta" href="{{route('add-player')}}">Team Squad</a>
                            <a class="cta" href="javascript:;">Payment Info</a>
                            {{-- @if($isAppliedTournament) --}}
                                <a class="cta" href="javascript:;">Add Bonafide</a>
                             {{-- @endif --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <main class="main-wrapper-start addnewplay-main">
        <div class="container">
            <div class="modal-backdrop" id="modalBackdrop"></div>

        <!-- Preference Modal -->
        <div class="preference-modal" id="preferenceModal">
            <h4 class="mb-4">Are you sure to apply for this tournament? </h4>
            <h5 class="mb-0">Match Preferences</h5>
            <select class="preference-select" id="matchPreference">
                <option value="">Select Preference</option>
                @foreach(config('matchPreference') as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
            <div class="preference-error" style="color: red; font-size: 12px; display: none;">
                Please select your preference
            </div>
            <div class="modal-buttons">
                <button class="cancel-btn" onclick="closePreferenceModal()">Cancel</button>
                <button class="confirm-btn" id="confirmBtn"  onclick="confirmApplication()">Apply</button>
            </div>
            </div>
            <div class="row">
                <div class="col-12">
                    {{-- <div class="player-lists-wrapper">
                        <div class="player-lists-info-header">
                            <div class="player-lists-info-head">
                                <div class="playersinfo sno head">S.No</div>
                                <div class="playersinfo name head">Tournament Name</div>
                                <div class="playersinfo name head">Start Date</div>
                                <div class="playersinfo name head">End Date</div>
                                <div class="playersinfo empid head">Apply</div>
                            </div>
                        </div>
                        @foreach($tournaments as $tournament)
                        <div class="player-lists-info">
                            <div class="player-lists-info-head">
                                <div class="playersinfo sno">{{ $loop->iteration }}</div>
                                <div class="playersinfo name">{{ $tournament->name }}</div>
                                <div class="playersinfo name">{{ \Carbon\Carbon::parse($tournament->start_date)->format('d-m-Y') }}</div>
                                <div class="playersinfo name">{{ \Carbon\Carbon::parse($tournament->end_date)->format('d-m-Y') }}</div>
                                <div class="playersinfo empid">
                                    @php
                                        // Check if the current team has already applied for this tournament
                                        $alreadyApplied = \App\Models\TournamentTeam::where('tournament_id', $tournament->id)
                                                            ->where('team_id', Auth::user()->team_id)
                                                            ->exists();

                                        // Check if the current team `is_added` is not equal to 0
                                        $teamIsValid = \App\Models\Team::where('id', Auth::user()->team_id)
                                                            ->where('is_added', '!=', 3)
                                                            ->exists();
                                    @endphp

                                    @if($alreadyApplied)
                                        <!-- Disable button if already applied -->
                                        <button class="apply-btn" disabled>Applied</button>
                                    {{-- @elseif(!$teamIsValid)
                                        <!-- Show tooltip if the team is invalid -->
                                        <button class="apply-btn"
                                        title="Please add players and submit the team to apply for tournament."
                                        style="cursor: not-allowed;"
                                        onclick="showTooltipMessage(event)">
                                    Apply
                                </button> --}}
                                    {{-- @else
                                        <!-- Enable button if conditions are met -->
                                        <form id="applicationForm{{ $tournament->id }}" action="{{ route('apply-tournament', $tournament->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="match_preference" id="preferenceInput{{ $tournament->id }}">
                                            <button type="button" class="apply-btn" onclick="openPreferenceModal({{ $tournament->id }})">Apply</button>
                                        </form>
                                    @endif
                                </div>

                            </div>
                        </div>

                        @endforeach --}}
                        {{-- <div class="submit-section text-end mt-4">
                            <form action="{{ route('submit-team') }}" method="POST">
                                @csrf
                                <input type="hidden" name="team_id" value="{{ $team->team_id }}">
                                <button type="submit" class="btn btn-primary" id="submitTeamButton"
                                @if($players->count() < 11 || $team_logo->is_added == 2)
                                    disabled
                                @endif>
                                    Submit Team
                                </button>
                            </form>
                        </div>
                    </div> --}}
                    <div class="player-lists-wrapper">

                        <div class="player-lists-info-header">

                            <div class="player-lists-info-head">

                                <div class="playersinfo sno head">S.No</div>

                                <div class="playersinfo name head">Tournament Name</div>

                                <div class="playersinfo name head">Start Date</div>

                                <div class="playersinfo name head">End Date</div>

                                <div class="playersinfo empid head">Apply</div>

                            </div>

                        </div>

                        <div class="player-lists-info">
                        @foreach($tournaments as $tournament)


                            <div class="player-lists-info-head">

                                <div class="playersinfo sno">{{ $loop->iteration }}</div>

                                <div class="playersinfo name">{{ $tournament->name }}</div>

                                <div class="playersinfo name">{{ \Carbon\Carbon::parse($tournament->start_date)->format('d-m-Y') }}</div>

                                <div class="playersinfo name">{{ \Carbon\Carbon::parse($tournament->end_date)->format('d-m-Y') }}</div>

                               <div class="playersinfo empid">
    @php
        $alreadyApplied = \App\Models\TournamentTeam::where('tournament_id', $tournament->id)
                            ->where('team_id', Auth::user()->team_id)
                            ->exists();
                      
        $tournamentEnded = \Carbon\Carbon::parse($tournament->end_date)->isPast();

        $teamIsValid = \App\Models\Team::where('id', Auth::user()->team_id)
                            ->where('is_added', '!=', 0)
                            ->exists();
    @endphp

    @if($alreadyApplied)
        <button class="apply-btn" disabled>Applied</button>
    @elseif($tournamentEnded)
        <button class="apply-btn" disabled>Tournament End</button>
    @else
        <form id="applicationForm{{ $tournament->id }}" 
              action="{{ route('apply-tournament', $tournament->id) }}" 
              method="POST">
            @csrf
            <input type="hidden" name="match_preference" id="preferenceInput{{ $tournament->id }}">

            @if($tournament->flexible_dates == 1)
                {{-- Show match preference modal --}}
                <button type="button" class="apply-btn" onclick="openPreferenceModal({{ $tournament->id }})">
                    Apply
                </button>
            @else
                {{-- Directly submit without modal, match_preference stays null --}}
                <button type="submit" class="apply-btn"
                        onclick="return confirm('Are you sure you want to apply for this tournament?')">
                    Apply
                </button>
            @endif
        </form>
    @endif
</div>

                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                </div>
            </div>
        </div>

        <div class="bonafide-modal" id="bonafideModal">
            <div class="bonafide-modal-content">
                <h5 class="mb-4 bonafide-title">Upload Bonafide Certificate</h5>
                <form id="bonafideForm" enctype="multipart/form-data">
                    @csrf
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
                            <div class="file-name mt-2" id="fileName"></div>
                            <div class="validation-message" id="fileValidation"></div>
                        </div>
                    </div>
                    <div class="modal-buttons mt-4">
                        <button type="button" class="cancel-btn" onclick="closeBonafideModal()">Close</button>
                        <button type="submit" class="confirm-btn" id="uploadBtn">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>

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
            <button class="cancel-btn" onclick="closePaymentModal()">Close</button>
        </div>
    </div>
</div>

<script>
     function showTooltipMessage(event) {
        event.preventDefault(); // Prevent default button behavior
        const tooltipMessage = document.getElementById('tooltipMessage');
        tooltipMessage.style.display = 'block'; // Show the tooltip message
        setTimeout(() => {
            tooltipMessage.style.display = 'none'; // Hide the tooltip message after 3 seconds
        }, 3000);
    }

    function openPreferenceModal(tournamentId) {
        currentTournamentId = tournamentId;
        const modal = document.getElementById('preferenceModal');
        const backdrop = document.getElementById('modalBackdrop');

        backdrop.style.display = 'block';
        modal.style.display = 'block';

        void modal.offsetWidth;

        backdrop.classList.add('show');
        modal.classList.add('show');

        document.getElementById('matchPreference').value = '';
        document.querySelector('.preference-error').style.display = 'none';
    }

    function closePreferenceModal() {
        const modal = document.getElementById('preferenceModal');
        const backdrop = document.getElementById('modalBackdrop');

        modal.classList.remove('show');
        backdrop.classList.remove('show');

        setTimeout(() => {
            modal.style.display = 'none';
            backdrop.style.display = 'none';
            currentTournamentId = null;
        }, 300);
    }

    document.getElementById('matchPreference').addEventListener('change', function() {
        const confirmBtn = document.getElementById('confirmBtn');
        const errorMsg = document.querySelector('.preference-error');

        if (this.value === '') {
            confirmBtn.disabled = true;
            errorMsg.style.display = 'block';
        } else {
            confirmBtn.disabled = false;
            errorMsg.style.display = 'none';
        }
    });

    function confirmApplication() {
        const preference = document.getElementById('matchPreference').value;
        const errorMsg = document.querySelector('.preference-error');

        if (preference === '') {
            errorMsg.style.display = 'block';
            return false;
        }

        document.getElementById(`preferenceInput${currentTournamentId}`).value = preference;
        document.getElementById(`applicationForm${currentTournamentId}`).submit();
    }

    document.querySelector('a.cta:nth-child(3)').addEventListener('click', function(e) {
        e.preventDefault();
        openPaymentModal();
    });

    function openPaymentModal() {
        const modal = document.getElementById('paymentModal');
        const backdrop = document.getElementById('modalBackdrop');

        backdrop.style.display = 'block';
        modal.style.display = 'block';

        void modal.offsetWidth;

        backdrop.classList.add('show');
        modal.classList.add('show');
    }

    function closePaymentModal() {
        const modal = document.getElementById('paymentModal');
        const backdrop = document.getElementById('modalBackdrop');

        modal.classList.remove('show');
        backdrop.classList.remove('show');

        setTimeout(() => {
            modal.style.display = 'none';
            backdrop.style.display = 'none';
        }, 300);
    }

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
        const backdrop = document.getElementById('modalBackdrop');

        backdrop.style.display = 'block';
        modal.style.display = 'block';

        void modal.offsetWidth;

        backdrop.classList.add('show');
        modal.classList.add('show');
    }

    function closeBonafideModal() {
        const modal = document.getElementById('bonafideModal');
        const backdrop = document.getElementById('modalBackdrop');

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

    document.querySelector('a.cta:nth-child(4)').addEventListener('click', function(e) {
        e.preventDefault();
        openBonafideModal();
    });
</script>
@endsection
