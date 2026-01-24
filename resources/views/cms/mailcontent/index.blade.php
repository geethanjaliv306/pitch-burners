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
                       <h2>Manage Mail Content</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        {{-- <div class="row gallery-header">
            <div class="col-12">
                <h2>Manage Mail Content</h2>
            </div>
        </div> --}}

        <div class="row gallery-form-wrapper">
            <div class="col-12">
            <form action="{{ route('mail_content.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="subject">Subject:</label>
                    <input type="text" name="subject" id="subject"class="form-control" value="{{ $mailContent->subject ?? '' }}" required>
                </div>
                <br>
                <div class="form-group">
                    <label for="body_content">Body Content:</label>
                    <textarea name="body_content" id="body_content" rows="5" class="form-control" required>{{ $mailContent->body_content ?? '' }}</textarea>
                </div>
                <br>
                <button type="submit" class="btn btn-primary"style="
    justify-self: end;">Update</button>
            </form>
        </div>
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
