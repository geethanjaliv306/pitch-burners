{{-- <link rel="stylesheet" href="https://mdbgo.io/marta-szymanska/mdb5-demo-pro-design-blocks/css/mdb.min.css" />
<link rel="stylesheet" href="https://mdbgo.io/marta-szymanska/mdb5-demo-pro-design-blocks/dev/css/new-prism.css" /> --}}

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

@extends('layouts.admin')
@section('content')
<section class="add-new-torunments-wrap">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="inner">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-center title">Edit Tournament</h6>
                        </div>
                        <div class="col-12">
                            <form action="{{ route('update-tournament', $tournament->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="newtournament-item mb-4">
                                    <label for="tournamentName" class="form-label">Tournament Name*</label>
                                    <input type="text" class="form-control" id="tournamentName" name="name" value="{{ old('name', $tournament->name) }}">
                                    @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="newtournament-item mb-4">
                                    <label for="multiple-select-cityfield" class="form-label">City</label>
                                    <select class="form-select" id="multiple-select-cityfield" name="city[]" multiple>
                                        @foreach(config('tournaments.cities') as $value => $city)
                                        <option value="{{ $value }}" {{ in_array($value, old('city', $tournament->city)) ? 'selected' : '' }}>
                                            {{ $city }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('city')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="newtournament-item mb-4">
                                    <label for="multiple-select-groundfield" class="form-label">Ground</label>
                                    <select class="form-select" id="multiple-select-groundfield" name="ground[]" multiple>
                                        @foreach($venues as $id => $venue)
                                        <option value="{{ $id }}" {{ in_array($id, old('ground', $tournament->ground)) ? 'selected' : '' }}>
                                            {{ $venue }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('ground')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="newtournament-item mb-4">
                                    <label for="organiserName" class="form-label">Organiser Name</label>
                                    <input type="text" class="form-control" id="organiserName" name="organiser_name" value="{{ old('organiser_name', $tournament->organiser_name) }}">
                                </div>
                                <div class="newtournament-item mb-4">
                                    <label for="organiserContact" class="form-label">Organiser Contact</label>
                                    <input type="text" class="form-control" id="organiserContact" name="organiser_contact" value="{{ old('organiser_contact', $tournament->organiser_contact) }}">
                                </div>
                              
                              <div class="newtournament-item mb-4">
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="flexCheckAllowPlayers"
                                        name="flexible_dates"
                                        value="1"
                                        @if(old('flexible_dates', $tournament->flexible_dates) == 1) checked @endif
                                    >
                                    <label class="form-check-label" for="flexCheckAllowPlayers">
                                        Flexible Dates
                                    </label>
                                </div>
                            </div>
                               
                                <div class="newtournament-item mb-4 start-end-date-wrap">
                                    <div class="start-end-date-item">
                                        <label for="startDate" class="form-label">Start Date</label>
                                        <div class="form-outline datepicker">
                                            <input type="text" class="form-control" id="startDate" name="start_date" value="{{ old('start_date', \Carbon\Carbon::parse($tournament->start_date)->format('d-m-Y')) }}" data-mdb-toggle="datepicker">
                                        </div>
                                        @error('start_date')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="start-end-date-item">
                                        <label for="endDate" class="form-label">End Date</label>
                                        <div class="form-outline datepicker">
                                            <input type="text" class="form-control" id="endDate" name="end_date" value="{{ old('end_date', \Carbon\Carbon::parse($tournament->end_date)->format('d-m-Y')) }}" data-mdb-toggle="datepicker">
                                        </div>
                                        @error('end_date')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="newtournament-item mb-4">
                                    <label class="mb-2">Tournament Category</label>
                                    <div class="tournament_category">
                                        <div class="customRadioSelect">
                                            <input type="radio" id="tournamentCategory_1" name="tournament_category" value="Limited Overs" {{ old('tournament_category', $tournament->tournament_category) === 'Limited Overs' ? 'checked' : '' }}>
                                            <label for="tournamentCategory_1">Limited Overs</label>
                                        </div>
                                        <div class="customRadioSelect">
                                            <input type="radio" id="tournamentCategory_2" name="tournament_category" value="Box Cricket" {{ old('tournament_category', $tournament->tournament_category) === 'Box Cricket' ? 'checked' : '' }}>
                                            <label for="tournamentCategory_2">Box Cricket</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="newtournament-item mb-4">
                                    <label class="mb-2">Select Ball Type</label>
                                    <div class="tournament_category">
                                        <div class="customRadioSelect ballTypeSelect">
                                            <input type="radio" id="ballType_1" name="ball_type" value="Red Tennis" {{ old('ball_type', $tournament->ball_type) === 'Red Tennis' ? 'checked' : '' }}>
                                            <label for="ballType_1">Red Tennis</label>
                                        </div>
                                        <div class="customRadioSelect ballTypeSelect">
                                            <input type="radio" id="ballType_2" name="ball_type" value="Green Tennis" {{ old('ball_type', $tournament->ball_type) === 'Green Tennis' ? 'checked' : '' }}>
                                            <label for="ballType_2">Green Tennis</label>
                                        </div>
                                      <div class="customRadioSelect ballTypeSelect">
                                            <input type="radio" id="ballType_3" name="ball_type" value="White Ball" {{ old('ball_type', $tournament->ball_type) === 'White Ball' ? 'checked' : '' }}>
                                            <label for="ballType_3">White Ball</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-12">
                                    <div class="addtouranment-btn-lists">
                                      {{--  <a href="{{ route('tournaments') }}" class="btn btn-outline-gray">Cancel</a> --}}
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
{{-- <script type="text/javascript" src="https://mdbgo.io/marta-szymanska/mdb5-demo-pro-design-blocks/dev/js/new-prism.js"></script>
<script type="text/javascript" src="https://mdbgo.io/marta-szymanska/mdb5-demo-pro-design-blocks/dev/js/dist/mdbsnippet.min.js"></script>
<script type="text/javascript" src="https://mdbgo.io/marta-szymanska/mdb5-demo-pro-design-blocks/js/mdb.min.js"></script>
<script type="text/javascript">
    const pickerDateOptions = document.querySelector('#datetimepicker-dateOptions');
    new mdb.Datetimepicker(pickerDateOptions, {
        datepicker: {
            format: 'dd-mm-yyyy'
        },
    });
    const pickerTimeOptions = document.querySelector('#datetimepicker-timeOptions');
    new mdb.Datetimepicker(pickerTimeOptions, {
        timepicker: {
            format24: true
        },
    });
    const pickerDateOptions = document.querySelector('#exampleDatepicker1');
    new mdb.Datetimepicker(pickerDateOptions, {
        datepicker: {
            format: 'dd-mm-yyyy'
        },
    });
</script> --}}
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    flatpickr("#startDate", {
        dateFormat: "d-m-Y",
        allowInput: true,
        minDate: "today"
    });

    flatpickr("#endDate", {
        dateFormat: "d-m-Y",
        allowInput: true,
        minDate: "today"
    });
</script>
@endsection