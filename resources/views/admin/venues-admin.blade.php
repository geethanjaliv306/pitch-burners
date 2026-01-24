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
                <h2>Match Venue</h2>
                <a class="btn btn-yellow" href="{{ route('venues-admin.create') }}">Create a Venue</a>
              </div>
          </div>
        </div>
      </div>
    </section>
    <section class="my-torunments-wrap">
      <div class="container ">
        <div class="row">
          <div class="col-12">
            <div class="myTorunments-table">
              <div class="myTorunments-head">
                <div class="myTorunments-head-col sno">S.No</div>
                <div class="myTorunments-head-col vn_name">Venue Name</div>
                <div class="myTorunments-head-col vn_location">Map Location</div>
                <div class="myTorunments-head-col vn_image">Image</div>
                <div class="myTorunments-head-col actions">Actions</div>
              </div>
              @if($venues->isEmpty())
              <div class="no-records-found">
                <p class="text-center m-3">No records found</p>
              </div>
            @else
              @foreach($venues as $index => $venue)
                  <div class="myTorunments-body">
                    <div class="myTorunments-body-col sno h-auto">{{ $loop->iteration + ($venues->currentPage() - 1) * $venues->perPage() }}</div>
                      <div class="myTorunments-body-col vn_name h-auto">{{ $venue->name }}</div>
                      <div class="myTorunments-body-col vn_location h-auto">
                          <a href="https://www.google.com/maps?q={{ urlencode($venue->location) }}" target="_blank">{{ $venue->location }}</a>
                      </div>
                      <div class="myTorunments-body-col vn_image h-auto">
                          @if($venue->image)
                             <img src="{{ config('constants.upload_url')  . '/venues_images/' .  $venue->image }}" alt="Venue Image" style="width:40px; height:40px;">
                          @else
                             <img src="{{asset('uploads/images/ground1.jpg')}}"style="width:40px; height:40px;">
                          @endif
                      </div>
                      <div class="myTorunments-body-col actions h-auto">
                          <div class="edit icon" data-bs-target="#editOraganizerMemberModal-{{ $venue->id }}" data-bs-toggle="modal">
                              <i>
                                  <a href="javascript:;"><img src="{{ asset('/uploads/images/pen.svg') }}" /></a>
                              </i>
                          </div>
                          <div class="delete icon">
                              <form action="{{ route('venues.destroy', $venue->id) }}" method="POST" style="display:inline-block;">
                                  @csrf
                                  @method('DELETE')
                                  <button type="submit" style="border:none; background:none;" onclick="return confirm('Are you sure you want to delete this venue?')">
                                      <i><img src="{{ asset('/uploads/images/delete.svg') }}" alt="Delete" /></i>
                                  </button>
                              </form>
                          </div>
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
                   {{-- {{ $venues->links('pagination::bootstrap-4') }} --}}
                  {{ $venues->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
              </ul>
          </nav>
      </div>
      </div>
    </section>
   

    <!-- Edit Venue Modals -->
    @foreach($venues as $venue)
  <div class="modal fade editOraganizerMemberModal modalOpeninBottomtoTop-mobile" id="editOraganizerMemberModal-{{ $venue->id }}" tabindex="-1" aria-labelledby="editOraganizerMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
          <form method="POST" action="{{ route('venues-admin.update', $venue->id) }}" enctype="multipart/form-data">
              @csrf
              @method('PUT')
              <div class="modal-body">
                <h2>Edit Venue</h2>
                <div class="section membersSection mb-3" id="section-1">
                  <div class="form-group mb-4">
                      <label class="mb-2" for="name-{{ $venue->id }}">Venue Name:</label>
                      <input type="text" class="form-control" id="name-{{ $venue->id }}" name="name" value="{{ $venue->name }}" required>
                  </div>
                  <div class="form-group mb-4">
                      <label class="mb-2" for="location-{{ $venue->id }}">Map Location</label>
                      <input type="text" class="form-control" id="location-{{ $venue->id }}" name="location" value="{{ $venue->location }}" required>
                  </div>
                  <div class="form-group">
                    <label class="mb-2" for="image">Image:</label>
                    <input type="file" class="form-control" name="image" id="image"accept=".jpg,.jpeg,.png,.svg">
                
                    @if($venue->image)
                        <div class="mt-2">
                                                       <img src="{{ config('constants.upload_url') . '/venues_images/' . $venue->image }}" alt="Venue Image" style="max-width: 100px; height: auto;">

                            <input type="hidden" name="existing_image" value="{{ $venue->image }}">
                        </div>
                    @else
                        <img src="{{asset('uploads/images/ground1.jpg')}}"style="width:40px; height:40px;">
                    @endif
                </div>                
              </div>
              </div>
              <div class="modal-footer d-flex justify-content-center">
                  <button type="button" class="btn btn-outline-gray" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary">Submit</button>
              </div>
          </form>
      </div>
    </div>
  </div>
@endforeach
<script>
  // Check if there's a success message in the session
  @if(session('success'))
      // Show the alert message
      document.getElementById('successMessage').style.display = 'block';
      
      // Hide the message after 3 seconds
      setTimeout(function() {
          document.getElementById('successMessage').style.display = 'none';
          // Redirect to a specific page if needed
      }, 3000);
  @endif
</script>
@endsection
