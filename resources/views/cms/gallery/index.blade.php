@extends('layouts.admin')

@section('content')
<style>
    .gallery-header {
        padding-bottom: 15px;
        border-bottom: 2px solid #ddd;
    }
    .gallery-title {
        font-size: 1.5rem;
        margin-bottom: 20px;
    }
    .form-group label {
        font-weight: bold;
        margin-top: 15px;
    }
    .gallery-item {
        position: relative;
        margin-bottom: 20px;
        text-align: center;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 10px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }
    .gallery-item img {
        width: 100%;
        height: auto;
        border-radius: 5px;
        margin-bottom: 10px;
    }
    .delete-gallery, .update-gallery {
        width: auto;
        padding: 8px;
    }
    .delete-gallery {
        margin-top: 10px;
        background-color: #dc3545;
        border-color: #dc3545;
    }
    .update-gallery {
        background-color: #17a2b8;
        border-color: #17a2b8;
    }
    .gallery-form-wrapper {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 40px;
    }
    .btn-primary {
        margin-top: 20px;
    }
    .add-more-images {
        margin-top: 20px;
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
                        <h2>Manage Gallery</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
    {{--    <div class="row gallery-header">
            <div class="col-12">
                <h2>Manage Gallery</h2>
            </div>
        </div>--}}

        <div class="row gallery-form-wrapper">
            <div class="col-12">
                <form action="{{ route('gallery-store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="title">Gallery Title:</label>
                        <input type="text" name="title" id="title" class="form-control" placeholder="Enter gallery title" required>
                    </div>
                    <div class="form-group">
                        <label for="images">Upload Images:</label>
                        <input type="file" name="images[]" id="images" class="form-control" accept=".jpg,.jpeg,.png,.svg" multiple required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Images</button>
                </form>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-12">
                <h3 class="gallery-title">Gallery</h3>
            </div>

            @foreach($galleries as $title => $galleryItems)
                <div class="col-12 gallery-form-wrapper">
                    <form action="{{ route('gallery-update-title', $galleryItems->first()->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="form-group">
                            <h4>Gallery Title: </h4>
                            <input type="text" name="title" value="{{ $title }}" class="form-control mb-3" required>
                            <button type="submit" class="btn btn-info update-gallery">Update Title</button>
                        </div>
                    </form>

                    <div class="row">
                        @foreach($galleryItems as $gallery)
                            <div class="col-md-3">
                                <div class="gallery-item">
                                   <img src="{{ config('constants.upload_url') . '/gallery/' . $gallery->image }}" alt="{{ $title }}">

                                    <form action="{{ route('gallery-delete', $gallery->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger delete-gallery">Delete</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <form action="{{ route('gallery-add-images', $title) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group add-more-images">
                            <label for="images">Add More Images for "{{ $title }}":</label>
                            <input type="file" name="images[]" class="form-control"  accept=".jpg,.jpeg,.png,.svg" multiple required>
                        </div>
                        <button type="submit" class="btn btn-success">Add More Images</button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
</section>
<script>
    document.querySelectorAll('.delete-gallery').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault(); // Prevent the form submission
            const confirmed = confirm('Are you sure you want to delete this image?');
            if (confirmed) {
                // Submit the form if confirmed
                this.closest('form').submit();
            }
        });
    });
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
