@extends('layouts.admin')

@section('content')
<style>
  .sidebar{
    display: none;
  }
</style>
  <section class="startmatch-wrap">
    <div class="inner">
    <i class="bg-image has-opacity"><img class="startmatch-wrap-bg" src="{{ asset('uploads/images/drawing-baseball-player-with-bat-word-cricket-it.jpg')}}" /></i>
    <div class="info">
      <h6>Hey there!</h6>
      <p>Seems like you already have created tournaments on Pitchburners. Go ahead and view your tournaments or create a new one.</p>
      <div class="d-flex btn-groups">
        <a class="btn btn-yellow" href="{{route('tournaments-view')}}">Go To My Tournaments</a>
        <a class="btn btn-yellow-outline" href="{{route('tournaments')}}">Add a New Tournament</a>
      </div>
    </div>
    </div>
  </section>
   @endsection