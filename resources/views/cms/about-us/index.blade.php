@extends('layouts.admin')

@section('content')
<style>
    .my-torunments-wrap {
    padding: 20px 0 120px;
}
</style>
<div class="alert-message" id="successMessage">
      {{ session('success') }}
  </div>
<section class="my-torunments-wrap">
    <!-- Page Header Section -->
    <section class="my-torunments-second-header fixed-second-header">
        <div class="container h-100">
            <div class="row h-100">
                <div class="col-12">
                    <div class="title-wrap h-100">
                        <h2>About Us</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Content Update Form -->
    <div class="container">
        <form action="{{ route('cms-about-update') }}" method="POST">
            @csrf
            <div class="row">
                <!-- About Banner Section -->
                <div class="col-md-12 mb-5">
                    <h5 class="mb-3">About Banner</h5>
                    <textarea name="banner_text" rows="4" class="form-control" required>{{ $content->banner_text ?? '' }}</textarea>
                </div>

                <!-- Title 1 and Vision Section -->
                <div class="col-md-6 mb-5">
                    <h5 class="mb-3">Title 1</h5>
                    <textarea name="title1" rows="2" class="form-control mb-3" required>{{ $content->title1 ?? '' }}</textarea>
                    <textarea name="vision" rows="5" class="form-control"required>{{ $content->sub_details1 ?? '' }}</textarea>
                </div>

                <!-- Title 2 and Mission Section -->
                <div class="col-md-6 mb-5">
                    <h5 class="mb-3">Title 2</h5>
                    <textarea name="title2" rows="2" class="form-control mb-3"required>{{ $content->title2 ?? '' }}</textarea>
                    <textarea name="mission" id="mission" rows="5" class="form-control"required>{{ $content->sub_details2 ?? '' }}</textarea>
                </div>

                <!-- Title 3 and Objective Section -->
                <div class="col-md-6 mb-5">
                    <h5 class="mb-3">Title 3</h5>
                    <textarea name="title3" rows="2" class="form-control mb-3"required>{{ $content->title3 ?? '' }}</textarea>
                    <textarea name="objective" id="objective" rows="5" class="form-control"required>{{ $content->sub_details3 ?? '' }}</textarea>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary mt-3">Update Content</button>
        </form>
    </div>
</section>

<!-- TinyMCE Editor Script -->
<script src="https://cdn.tiny.cloud/1/t4c7fjxfwrnom0sg3zlz1xu85b9f9a4o0afnnh4t1cvtuxh9/tinymce/6/tinymce.min.js" referrerpolicy="strict-origin"></script>
<script>
    tinymce.init({
        selector: 'textarea[name="vision"], textarea[name="mission"], textarea[name="objective"]',
        height: 300,
        plugins: [
            'advlist autolink lists link image charmap print preview anchor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table paste code help wordcount'
        ],
        toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter ' +
                 'alignright alignjustify | bullist numlist outdent indent | removeformat | help',
        forced_root_block: 'p',
        remove_trailing_brs: true,
        force_br_newlines: false,
        force_p_newlines: true,
        entity_encoding: 'raw',
        setup: function (editor) {
            editor.on('BeforeSetContent', function (e) {
                e.content = e.content.replace(/<div/gi, '<p').replace(/<\/div>/gi, '</p>');
            });
            editor.on('PostProcess', function (e) {
                e.content = e.content.replace(/<div/gi, '<p').replace(/<\/div>/gi, '</p>');
            });
        }
    });
</script>
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
