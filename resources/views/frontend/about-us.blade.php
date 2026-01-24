@extends('layouts.app')

@section('content')
<style>
    @media (max-width: 768.98px) {
        .arrow-img{
            display: none;
        }
    }
  .teamsquad .item{
    margin-bottom:25px;
  }
</style>
<section class="about-banner">
  <div class="container h-100">
    <div class="row h-100 justify-content-end">
        <div class="col-12 col-lg-6 ">
          <div class="about-info">
            {!! $content->banner_text ?? 'No content available' !!}
          </div>
        </div>
    </div>
  </div>
</section>
<section class="vision">
  <div class="container">
    <div class="row">
      <div class="col-12 col-md-8">
        <div class="info mb-5 pb-3">
          <h3> {!! $content->title1 ?? 'No content available' !!}</h3>
          <p>{!! $content->sub_details1 ?? 'No content available' !!}</p>
        </div>
        <div class="info">
          <h3> {!! $content->title2 ?? 'No content available' !!}</h3>
          <p>{!! $content->sub_details2 ?? 'No content available' !!}</p>
        </div>
      </div>
      <div class="col-12 col-md-4">
        <svg viewBox="0 0 348 356" fill="none" xmlns="http://www.w3.org/2000/svg" class="arrow-img">
          <path d="M0 122.733L286.134 122.733C319.749 122.733 347 95.4818 347 61.8663V59.6274C347 27.2484 320.752 1 288.373 1V1C255.994 1 229.745 27.2483 229.745 59.6273L229.745 344.715" stroke="#141414" stroke-width="3"></path>
          <path class="last-path" d="M181 312.742C197.166 312.742 229.499 321.393 229.499 355.997" stroke="#141414" stroke-width="3"></path>
          <path class="last-path" d="M277.999 314.409C261.833 313.3 229.5 320.065 229.5 356" stroke="#141414" stroke-width="3"></path>
      </svg>
      </div>
    </div>
  </div>
</section>
<section class="objective">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <h3 class="text-center"> {!! $content->title3 ?? 'No content available' !!}</h3>
        <p>{!! $content->sub_details3 ?? 'No content available' !!}</p>
      </div>
    </div>
  </div>
</section>

<section class="teamsquad">
  <div class="container">
    <div class="row">
      <div class="col-12">
            <h3 class="teamsquad-title">Meet Our Team</h3>
          </div>
  @foreach($organizerMembers as $member)
      <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
        <div class="item">
      <figure>
                 <img src="{{ config('constants.upload_url') . '/organizer_images/' . $member->image }}" alt="{{ $member->name }}" />

      </figure>
          <figcaption>
              <h3>{{ $member->name }}</h3>
          </figcaption>
        </div>
      </div>
  @endforeach
    </div>
  </div>
</section>

@endsection
