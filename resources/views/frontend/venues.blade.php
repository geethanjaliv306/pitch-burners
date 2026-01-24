@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

    .venues-wrap{

        padding: 0px;

    }
.main-wrapper-start{
  min-height: calc(100vh - 149px);
  }
</style>
  {{-- <body class="addnewplayer-body"> --}}
    {{-- <img class="bg-sticky left-position" src="{{ asset('uploads/images/batsman2.svg') }}" /> --}}

    <main class="main-wrapper-start venues-main">
      <div class="container">
          <div class="row">
              <div class="col-12">
                <div class="mt-5">
                    @if($venues->isEmpty())
                    <p class="text-center">No Venues available</p>
                    @else
                </div>
                  <div class="row venues-wrap">
                      @foreach($venues as $venue)
                      <div class="col-lg-4 col-md-6 col-sm-12 venues-item">
                          <figure>
                          <img src="{{ $venue->image ? config('constants.upload_url') . '/venues_images/' . $venue->image : asset('uploads/images/ground1.jpg') }}" alt="Venue Image" />

                          </figure>
                          <figcaption>
                              <h6 class="title">{{ $venue->name }}</h6>
                              <p><a class="get-direction" target="_blank" href="https://www.google.com/maps?q={{ urlencode($venue->location) }}"><span style="margin-right: 5px;">Get Direction </span> <i class="fa-solid fa-map"></i> </a></p>
                          </figcaption>
                      </div>
                      @endforeach
                  </div>
                      @endif
              </div>
          </div>
      </div>
  </main>

@endsection
