@extends('layouts.app')

@section('content')
<style>
    .forms-wrapper {
        min-height: 100vh;
        padding: 40px 0;
        background-color: #f8f9fa;
    }
    .inner {
        max-width: 600px;
        margin: 60px auto;
        padding: 30px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      	width:100%
    }
    h3 {
        margin-bottom: 30px;
        color: #333;
    }
    .float-label-form-group {
        position: relative;
        margin-bottom: 25px;
    }
    .form-control {
        height: 50px;
        padding: 10px 15px;
    }
    .upload-btn-wrapper {
        position: relative;
        margin-bottom: 30px;
    }
    .upload-btn-wrapper .btn {
        width: 100%;
        height: 50px;
        background: #f8f9fa;
        border: 1px solid #ddd;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    .submitbtn {
        width: 100%;
        height: 50px;
        background: #ffb703;
        color: white;
        font-weight: 500;
        margin-top: 20px;
      	color:#614092;
    }
  
    .submitbtn:hover {
      background-color: #614092;
      color: #ffb703;
    }
    .error-container {
        color: #dc3545;
        font-size: 12px;
        position: absolute;
        bottom: -20px;
        left: 0;
    }
    #logo-filename {
        margin-top: 8px;
        font-size: 14px;
        color: #666;
    }
    .toast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #28a745;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        display: none;
        z-index: 1000;
    }
</style>

<section class="forms-wrapper d-flex align-items-center">
    <div class="inner">
        <h3 class="text-center">Edit Profile</h3>
        <form id="edit_profile_form" action="{{ route('profile.update', $Team->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div id="form-general-error" class="text-danger d-none"></div>

            <!-- Team Name Field -->
            <div class="float-label-form-group">
                <input type="text" name="name" id="team-name" class="form-control" placeholder="Enter your Team Name" value="{{ $Team->name }}">
                <label for="team-name">Enter your Team Name</label>
                <div class="error-container"></div>
            </div>

            <!-- Email Field -->
            <div class="float-label-form-group">
                <input type="email" name="email" id="email" class="form-control" placeholder="Enter your Email Address" value="{{ $Team->email }}">
                <label for="email">Enter your Email Address</label>
                <div class="error-container"></div>
            </div>

            <!-- Phone Number Field -->
            <div class="float-label-form-group">
                <input type="tel" name="phone" id="phone" class="form-control" placeholder="Enter your Phone Number" value="{{ $Team->phone }}">
                <label for="phone">Enter your Phone Number</label>
                <div class="error-container"></div>
            </div>

            <!-- Logo Upload -->
            <div class="upload-btn-wrapper">
                <button class="btn">Update Company Logo
                    <img class="upload-icon" width="25" height="25" src="{{ asset('uploads/images/upload.png') }}" />
                </button>
                <input type="file" name="logo" id="company-logo" accept=".png, .jpg, .jpeg, .svg" onchange="showFileName('company-logo', 'logo-filename')" />
                <div class="error-container"></div>
                <div id="logo-filename" class="file-name-container">
                    @if($Team->logo)
                        
                       <img src="{{ config('constants.upload_url') . '/team_logos/' . $Team->logo }}" lt="Team Logo" style="max-width: 150px; max-height: 150px; margin-top: 10px;" />

                    @else
                        <span>No logo uploaded</span>
                    @endif
                </div>
            </div>

            <!-- Submit Button -->
            <button class="btn submitbtn" type="submit">
                <span>Update Profile</span>
            </button>
        </form>
    </div>
</section>
@endsection
