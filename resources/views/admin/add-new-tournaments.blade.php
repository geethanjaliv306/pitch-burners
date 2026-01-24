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
                  <h6 class="text-center title">Add a New Tournament</h6>
                </div>
                <div class="col-12">
                  <form action="{{ route('store.tournament') }}" method="POST">
                    @csrf
                    <div class="newtournament-item mb-4">
                      <label for="tournamentName" class="form-label">Tournament Name</label>
                      <input type="text" class="form-control" id="tournamentName" name="name" value="{{ old('name') }}">
                      @error('name')
                      <span class="text-danger">{{ $message }}</span>
                      @enderror
                    </div>
                    <div class="newtournament-item mb-4">
                      <label for="multiple-select-cityfield" class="form-label">City</label>
                      <select class="form-select" id="multiple-select-cityfield" name="city[]" multiple >
                        @foreach(config('tournaments.cities') as $value => $city)
                            <option value="{{ $value }}" {{ in_array($value, old('city', [])) ? 'selected' : '' }}>{{ $city }}</option>
                        @endforeach
                      </select>
                      @error('city')
                      <span class="text-danger">{{ $message }}</span>
                      @enderror
                    </div>
                    <div class="newtournament-item mb-4">
                      <label for="multiple-select-groundfield" class="form-label">Ground</label>
                      <select class="form-select" id="multiple-select-groundfield" name="ground[]" multiple >
                        @foreach($venues as $id => $name)
                            <option value="{{ $id }}" {{ in_array($id, old('ground', [])) ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                      </select>
                      @error('ground')
                      <span class="text-danger">{{ $message }}</span>
                      @enderror
                    </div>
                   <div class="newtournament-item mb-4">
                        <label for="organiserName" class="form-label">Organiser Name</label>
                        <input type="text" class="form-control" id="organiserName" name="organiser_name" value="Pitch Burners"  value="{{ old('organiser_name') }}">
                        @error('organiser_name')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                      </div>

                      <div class="newtournament-item mb-4">
                        <label for="organiserContact" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="organiserContact" name="organiser_contact" value="{{ old('organiser_contact') }}">
                        @error('organiser_contact')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                      </div>

                     <div class="newtournament-item mb-4">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="flexCheckAllowPlayers" name="flexible_dates" value="{{ old('flexible_dates') }}">
                        <label class="form-check-label" for="flexCheckAllowPlayers">
                          Flexible Dates
                        </label>
                      </div>
                    </div>
                    <div class="newtournament-item mb-4 start-end-date-wrap">
                      <div class="start-end-date-item ">
                        <label for="startDate" class="form-label">Start Date</label>
                        <div class="form-outline datepicker">
                        <input type="text" class="form-control" id="startDate" name="start_date" data-mdb-toggle="datepicker" value="{{ old('start_date') }}">
                        </div>
                        @error('start_date')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                      </div>
                      <div class="start-end-date-item">
                        <label for="endDate" class="form-label">End Date</label>
                        <div class="form-outline datepicker">
                        <input type="text" class="form-control" id="endDate" name="end_date" data-mdb-toggle="datepicker" value="{{ old('end_date') }}">
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
                          <input type="radio" id="tournamentCategory_1" name="tournament_category" value="Limited Overs" checked />
                          <label for="tournamentCategory_1">Limited Overs</label>
                        </div>
                        <div class="customRadioSelect">
                          <input type="radio" id="tournamentCategory_2" name="tournament_category" value="Box Cricket" />
                          <label for="tournamentCategory_2">Box Cricket</label>
                        </div>
                      </div>
                    </div>
                    <div class="newtournament-item mb-4">
                      <label class="mb-2">Select Ball Type</label>
                      <div class="tournament_category">
                        <div class="customRadioSelect ballTypeSelect">
                          <input type="radio" id="ballType_1" name="ball_type" value="Red Tennis" checked />
                          <label for="ballType_1">Red Tennis</label>
                        </div>
                        <div class="customRadioSelect ballTypeSelect">
                          <input type="radio" id="ballType_2" name="ball_type" value="Green Tennis" />
                          <label for="ballType_2">Green Tennis</label>
                        </div>
                         <div class="customRadioSelect ballTypeSelect">
                            <input type="radio" id="ballType_3" name="ball_type" value="White Ball" />
                            <label for="ballType_3">White Ball</label>
                          </div>
                      </div>
                    </div>
                    <div class="col-12 col-md-12">
                      <div class="addtouranment-btn-lists">
                        <input class="btn btn-outline-gray" type="reset" value="Cancel" />
                        <input class="btn btn-primary" type="submit" value="Publish"/>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="container d-none">

        <div class="row">
          <div class="col-12">
            <div class="congratulations-start-match">
              <img width="100" src="images/check-mark-button-joypixels.gif" />
              <h5>CONGRATULATIONS!</h5>
              <p>Your tournament is ready. Go to your tournament page to add teams and schedule matches of your tournament.</p>
              <a href="javascript:;" class="btn btn-primary">Yes, Take Me There</a>
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
