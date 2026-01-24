@extends('layouts.app')

@section('content')

<style>
  .contact-info{
    min-height: calc(100vh - 149px);
  }
</style>
<section class="contact-info">
    <div class="container">
      @if(Session::has('success'))
        <div class="alert alert-success fade-message">

          {{Session::get('success')}}
        </div>
      @endif
      <div class="row">
          <div class="col-12 mb-3">
            <h3>Contact Us</h3>
          </div>
          <div class="col-12 col-md-6">
            <form action="{{route('reachUs')}}" method="POST">
              @csrf
              <!-- Name Field -->
              <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="sender_name" value="{{old('sender_name')}}" placeholder="Enter your name" required>
              </div>
              @error('sender_name')
              <div class="alert alert-danger">{{ $message }}</div>
              @enderror
          
              <!-- Email Field -->
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="{{old('email')}}" placeholder="Enter your email" required>
              </div>
              @error('email')
              <div class="alert alert-danger">{{ $message }}</div>
              @enderror
              <!-- Contact Field with India Code -->
              <div class="mb-3">
                <label for="contact" class="form-label">Contact</label>
                <div class="input-group">
                  <span class="input-group-text">+91</span>
                  <input type="tel" class="form-control" id="contact" name="phone" value="{{old('phone')}}" placeholder="Enter your contact number" maxlength="10" required>
                </div>
              </div>
              @error('phone')
              <div class="alert alert-danger">{{ $message }}</div>
              @enderror
              <!-- Message Field (Textarea) -->
              <div class="mb-3">
                <label for="message" class="form-label">Message</label>
                <textarea class="form-control" id="message" rows="4" placeholder="Enter your message" name="message">{{old('message')}}</textarea>
              </div>
              @error('message')
              <div class="alert alert-danger">{{ $message }}</div>
              @enderror
              <!-- Preferred Way to Contact (Dropdown) -->
              <div class="mb-3">
                <label for="preferredContact" class="form-label">Preferred Way to Contact</label>
                <select class="form-select" id="preferredContact" required name="preferred_way_to_contact">
                  <option value="">Select...</option>
                  <option value="email" {{old('preferred_way_to_contact') == 'email' ? 'selected' : ''}} >Email</option>
                  <option value="phone" {{old('preferred_way_to_contact') == 'phone' ? 'selected' : ''}}>Phone</option>
                  <option value="sms" {{old('preferred_way_to_contact') == 'sms' ? 'selected' : ''}}>SMS</option>
                </select>
              </div>
              @error('preferred_way_to_contact')
              <div class="alert alert-danger">{{ $message }}</div>
              @enderror
              <!-- Submit Button -->
              <button type="submit" class="btn btn-primary">Submit</button>
            </form>
          </div>
          <div class="col-12 col-md-6">
              <ul class="socilamedia-mail">
                <li class="mail"><i><img src="{{ asset('uploads/images/email.svg')}}" /></i><a href="mailto:cricket@pitchburners.com">cricket@pitchburners.com</a></li>
                <li class="phone"><i><img src="{{ asset('uploads/images/telephone.svg')}}" /></i><a href="tel:+91 99628 51516">+91 99628 51516</a></li>
                <li class="insta"><i><img src="{{ asset('uploads/images/video.svg')}}" /></i> <a href="https://www.instagram.com/pitchburners_sports_foundation?igsh=bHhsdmtvbnJobjht" target="_blank">Pitchburners</a></li>
              </ul>
          </div>
      </div>
    </div>
  </section>
  
  <script>
    //   messsage
      document.addEventListener('DOMContentLoaded', function() {
      const message = document.querySelector('.fade-message');
      if (message) {
          setTimeout(() => {
              message.style.transition = 'opacity 1s';
              message.style.opacity = '0';
          }, 2000);
          setTimeout(() => {
              if (message) {
                  message.remove();
              }
          }, 2000);
      }
  });
  </script>
@endsection