@extends('layouts.app')

@section('content')
<style>

  .space{
   padding-bottom: 200px;
  }
  .gallery-info{
            min-height: calc(100vh - 149px);
  }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4/dist/fancybox.css" />

<section class="gallery-info">
    <div class="container">
        @if($galleries->isEmpty())
        <p class="text-center space">No Images available</p>
        @else
        @foreach($galleries as $title => $galleryItems)
            <h2 class="gallery-big-title">{{ $title }}</h2>
            <div class="grid-container mb-5">
                @foreach($galleryItems as $gallery)
                     <a href="{{ config('constants.upload_url') . '/gallery/' . $gallery->image }}" data-fancybox="gallery" class="grid-item">
                    <img src="{{ config('constants.upload_url') . '/gallery/' . $gallery->image }}" alt="{{ $title }}">
                </a>

                @endforeach
            </div>
        @endforeach
        @endif
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4/dist/fancybox.umd.js"></script>
@endsection
