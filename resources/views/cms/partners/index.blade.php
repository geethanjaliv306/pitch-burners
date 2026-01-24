@extends('layouts.admin')

@section('content')
<style>
    .delete-partner, .update-partner {
        width: auto;
        padding: 10px;
    }
    .delete-partner {
        background-color: #dc3545;
        border-color: #dc3545;
        margin-top: 10px;
    }
    .update-partner {
        background-color: #17a2b8;
        border-color: #17a2b8;
    }
    .partner-item {
        position: relative;
        margin-bottom: 20px;
        text-align: center;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 10px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }
    .partner-item img {
        width: 100%;
        height: auto;
        border-radius: 5px;
        margin-bottom: 10px;
    }
    .gallery-form-wrapper {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 40px;
    }
    .form-group label {
        font-weight: bold;
        margin-top: 15px;
    }
    .btn-primary, .btn-success {
        margin-top: 20px;
    }
    .section-header {
        padding-bottom: 15px;
        border-bottom: 2px solid #ddd;
    }
    .btn{
        width: auto;
        height: 30px;
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
                    <div class="title-wrap h-100">
                        <h2>Manage Partners</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
      {{--  <div class="row section-header">
            <div class="col-12">
                <h2>Manage Partners</h2>
            </div>
        </div> --}}

        <div class="row gallery-form-wrapper">
            <div class="col-12">
                <form action="{{ route('partners.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="title">Partner Title:</label>
                        <input type="text" name="title" id="title" class="form-control" placeholder="Enter partner title" required>
                    </div>
                  <div class="form-group">
                        <label for="link">Partner Link:</label>
                        <input type="url" name="link" id="link" class="form-control" placeholder="Enter partner website link (optional)">
                    </div>
                    <div class="form-group">
                        <label for="images">Upload Images:</label>
                        <input type="file" name="images[]" id="images" class="form-control" accept=".jpg,.jpeg,.png,.svg" multiple required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Partner</button>
                </form>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-12">
                <h3 class="gallery-title">Partners List</h3>
            </div>

            @foreach($partners as $title => $partnerItems)
                <div class="col-12 gallery-form-wrapper">
                   <form action="{{ route('partners.update-title', $partnerItems->first()->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>Partner Title:</label>
                            <input type="text" name="title" value="{{ $title }}" class="form-control mb-3">
                        </div>
                        <div class="form-group">
                            <label>Partner Link:</label>
                            <input type="url" name="link" value="{{ $partnerItems->first()->link }}" class="form-control mb-3">
                        </div>
                        <button type="submit" class="btn btn-info update-partner">Update Title & Link</button>
                    </form>

                    <div class="row">
                        @foreach($partnerItems as $partner)
                            <div class="col-md-3">
                                <div class="partner-item">
                                                                       <img src="{{ config('constants.upload_url') . '/partners/' . $partner->image }}" alt="{{ $title }}">

                                    <form action="{{ route('partners.delete', $partner->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this image?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger delete-partner">Delete</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <form action="{{ route('partners.add-images', $title) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group add-more-images">
                            <label for="images">Add More Images for "{{ $title }}":</label>
                            <input type="file" name="images[]" class="form-control" accept=".jpg,.jpeg,.png,.svg" multiple required>
                        </div>
                        <button type="submit" class="btn btn-success">Add More Images</button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
</section>
<script>
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
