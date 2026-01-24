@extends('layouts.admin')

@section('content')
<style>
    .btn-update-winner{
        height: 50%;
        font-size: 10px;
        margin: 10px 0px;
        width: 85%;
    }
    .btn-remove-winner{
        height: 50%;
        font-size: 10px;
        margin: 10px 0px;
    }
    .my-torunments-wrap {
    padding: 20px 0 120px;
}
</style>
<div class="alert-message" id="successMessage">
  {{ session('success') }}
  </div>
<section class="my-torunments-wrap">
    <section class="my-torunments-second-header fixed-second-header">
        <div class="container h-100">
          <div class="row h-100">
            <div class="col-12">
                <div class="title-wrap h-100 ">
                  <h2>Winners</h2>
                </div>
            </div>
          </div>
        </div>
    </section>

      <div class="container">
        <div class="row">
        <form action="{{ route('cms-winners.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-6 form-group">
                    <label for="tournament_id">Tournament</label>
                    <select class="form-control" id="tournament_id" name="tournament_id" required>
                        <option value="">Select Tournament</option>
                        @foreach($tournaments as $tournament)
                            <option value="{{ $tournament->id }}">{{ $tournament->name }}</option>
                        @endforeach
                    </select>
                </div>

            <div class="col-6 form-group">
                <label for="position">Position</label>
                <input type="text" class="form-control" id="position" name="position" required>
            </div>
            <div class="col-6 form-group">
                <label for="team_name">Team Name</label>
                <input type="text" class="form-control" id="team_name" name="team_name">
            </div>
            <div class="col-6 form-group">
                <label for="prize">Prize</label>
                <input type="text" class="form-control" id="prize" name="prize" required>
            </div>
            <div class="col-6 form-group">
                <label for="additional_info">Additional Info</label>
                <input type="text" class="form-control" id="additional_info" name="additional_info">
            </div>
        </div>
            <button type="submit" class="btn btn-primary">Add Winner</button>
        </form>

       <div class="table-responsive mt-4">
    <table class="table">
        <thead>
            <tr>
                <th>Tournaments</th>
                <th>Position</th>
                <th>Team Name</th>
                <th>Prize</th>
                <th>Additional Info</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($winners as $winner)
                <tr>
                    <form action="{{ route('cms-winners.update', $winner->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <td>
                            <select name="tournament_id" class="form-control" required>
                                @foreach($tournaments as $tournament)
                                    <option value="{{ $tournament->id }}"
                                        {{ $winner->tournament_id == $tournament->id ? 'selected' : '' }}>
                                        {{ $tournament->name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>

                        <td>
                            <textarea name="position" class="form-control" required>{{ $winner->position }}</textarea>
                        </td>
                        <td>
                            <textarea name="team_name" class="form-control">{{ $winner->team_name }}</textarea>
                        </td>
                        <td>
                            <textarea name="prize" class="form-control" required>{{ $winner->prize }}</textarea>
                        </td>
                        <td>
                            <textarea name="additional_info" class="form-control">{{ $winner->additional_info }}</textarea>
                        </td>
                        <td>
                            <button type="submit" class="btn btn-warning btn-update-winner">Update</button>
                        </form>

                        <form action="{{ route('cms-winners.destroy', $winner->id) }}" method="POST" style="display:inline-block;"onsubmit="return confirm('Are you sure you want to remove this winner?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-remove-winner">Remove</button>
                        </form>
                        </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-center">
   {{-- {{ $winners->links('pagination::bootstrap-4') }} --}}
            {{ $winners->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
</div>
        </div>
      </div>
      <script>
        // Check if there's a success message in the session
        @if(session('success'))
            // Show the alert message
            document.getElementById('successMessage').style.display = 'block';

            // Hide the message after 3 seconds
            setTimeout(function() {
                document.getElementById('successMessage').style.display = 'none';
            }, 3000);
        @endif
    </script>
@endsection
