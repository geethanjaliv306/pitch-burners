@extends('layouts.admin')

@section('content')
<div class="alert-message" id="successMessage">
     {{ session('success') }}
  </div>
<section class="my-torunments-second-header fixed-second-header">
    <div class="container-fluid h-100">
      <div class="row h-100">
        <div class="col-12">
            <div class="title-wrap h-100">
              <h2>Organizer Members</h2>
              <a class="btn btn-yellow" href="{{ route('organizer-members.create') }}">Create an Organizer Member</a>
            </div>
        </div>
      </div>
    </div>
  </section>

    <section class="my-torunments-wrap">
      <div class="container-fluid">
          <div class="row">
              <div class="col-12">
                  <div class="myTorunments-table">
                      <div class="myTorunments-head">
                          <div class="myTorunments-head-col sno">S.No</div>
                          <div class="myTorunments-head-col or_name">Name</div>
                          <div class="myTorunments-head-col or_email">Email</div>
                          {{-- <div class="myTorunments-head-col or_phone">Phone Number</div> --}}
                          <div class="myTorunments-head-col or_image">Image</div>
                          <div class="myTorunments-head-col actions">Actions</div>
                      </div>
                      @if($organizerMembers->isEmpty())
                      <div class="no-records-found">
                        <p class="text-center m-3">No records found</p>
                      </div>
                    @else
                      @foreach($organizerMembers as $index => $member)
                            <div class="myTorunments-body">
                                <div class="myTorunments-body-col sno">{{ $loop->iteration + ($organizerMembers->currentPage() - 1) * $organizerMembers->perPage() }}</div>
                                <div class="myTorunments-body-col or_name">{{ $member->name }}</div>
                                <div class="myTorunments-body-col or_email">{{ $member->email }}</div>
                                <div class="myTorunments-body-col or_image">
                                    @if($member->image)
                                      <img src="{{ config('constants.upload_url') . '/organizer_images/' . $member->image }}" alt="Member Image"  style="width: 50px; height: 50px; border-radius: 50%;object-fit: cover;border: 1px solid #ddd;object-position: top;">

                                    @else
                                        <p>No image</p>
                                    @endif
                                </div>

                                <div class="myTorunments-body-col actions">
                                    <div class="edit icon" data-bs-target="#editOraganizerMemberModal-{{ $member->id }}" data-bs-toggle="modal"
                                        data-id="{{ $member->id }}"
                                        data-name="{{ $member->name }}"
                                        data-email="{{ $member->email }}"
                                        data-phone="{{ $member->phone_no }}">
                                        <i>
                                            <a href="javascript:;"><img src="{{ asset('/uploads/images/pen.svg') }}" /></a>
                                        </i>
                                    </div>

                                    <!-- Delete Form -->
                                    <form action="{{ route('delete-organizer-member', $member->id) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete icon" style="border: none; background: none;" onclick="return confirm('Are you sure you want to delete this member?')">
                                            <i>
                                                <img src="{{ asset('/uploads/images/delete.svg') }}" />
                                            </i>
                                        </button>
                                    </form>
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
                       {{-- {{ $organizerMembers->links('pagination::bootstrap-4') }} --}}
                    {{ $organizerMembers->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
                </ul>
            </nav>
        </div>
      </div>
  </section>
    
    @foreach($organizerMembers as $index => $member)
    <div class="modal fade editOraganizerMemberModal modalOpeninBottomtoTop-mobile" id="editOraganizerMemberModal-{{ $member->id }}" tabindex="-1" aria-labelledby="editOraganizerMemberModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('organizer-members.update', $member->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                <h2>Edit Member:</h2>
                <div class="section membersSection mb-3" id="section-1">

                    <div class="form-group mb-4">
                        <label class="mb-2" for="name-{{ $member->id }}">Name:</label>
                        <input type="text" class="form-control" id="name-{{ $member->id }}" name="name" value="{{ $member->name }}" required>
                    </div>
                    <div class="form-group mb-4">
                        <label class="mb-2" for="email-{{ $member->id }}">Email:</label>
                        <input type="email" class="form-control" id="email-{{ $member->id }}" name="email" value="{{ $member->email }}" required>
                    </div>
                    <div class="form-group">
                        <label class="mb-2" for="phone-{{ $member->id }}">Phone Number:</label>
                        <input type="text" class="form-control" id="phone-{{ $member->id }}" name="phone_no" value="{{ $member->phone_no }}" required>
                    </div>
                  <div class="form-group mb-4">
    <label class="mb-2" for="image-{{ $member->id }}">Image:</label>
    <input type="file" class="form-control" name="image" id="image-{{ $member->id }}"accept=".jpg,.jpeg,.png,.svg">

    @if($member->image)
        <img src="{{ config('constants.upload_url') . '/organizer_images/' . $member->image }}" alt="Member Image" style="max-width: 40px; height: 45px;">
    @else
        <p>No image</p>
    @endif

    @if ($errors->has('image'))
        <div class="error-message text-danger">{{ $errors->first('image') }}</div>
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


    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editButtons = document.querySelectorAll('.edit.icon');
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const modal = new bootstrap.Modal(document.getElementById('editOraganizerMemberModal'));
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    const email = this.getAttribute('data-email');
                    const phone = this.getAttribute('data-phone');
                    const image = this.getAttribute('data-image');

                    document.getElementById('name-1').value = name;
                    document.getElementById('email-1').value = email;
                    document.getElementById('phone-1').value = phone;
                    document.getElementById('image').value = image;

                    modal.show();
                });
            });
        });
    </script> --}}
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
