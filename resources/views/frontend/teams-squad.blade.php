@extends('layouts.app')

@section('content')
<style>
    .our-sponsers {
        display: none;
    }
  .main-wrapper-start {
    position: relative;
    min-height: calc(100vh - 450px);
}
</style>

<section class="addnewplayer-title-wrap fixed-second-header teamsaquad-second-header" style="background-image: url(/uploads/images/squadbg.jpg);">
    <div class="container h-100">
        <div class="row h-100 d-flex align-items-center">
            <div class="col-12">
                <div class="add-teamname-wrap">
                    <div class="addteam-logo d-flex align-items-center justify-content-center">
                        <figure style="overflow: hidden;">
                            <img src="{{ config('constants.upload_url') . '/team_logos/' . $team->logo }}" alt="{{ $team->name }}" />
                        </figure>
                        <figcaption>
                            <h5 class="m-0">{{ $team->name }}</h5>
                        </figcaption>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<main class="main-wrapper-start teamssquad-main">
    <div class="container">
        <div class="row">
            @php
                $categories = [
                    'Batsman' => 'Batters',
                    'Wicketkeeper' => 'Wicketkeepers',
                    'All-Rounder' => 'AllRounders',
                    'Bowler' => 'Bowlers'
                ];
                $totalPlayers = $team->players->count();
            @endphp

            @if($totalPlayers > 0)
                @foreach($categories as $role => $title)
                    @php
                        $players = $team->players->where('role', $role);
                    @endphp

                    <div class="col-12 mb-5">
                        <div class="teamsquad-title">{{ $title }}</div>
                        <div class="teamsquad-wrap">
                            @if($players->isNotEmpty())
                                @foreach($players as $player)
                                    <div class="teamsquad-item">
                                        <figure>
                                            <img src="{{ config('constants.upload_url') . '/player_images/' . $player->image }}" alt="Player Image" />
                                        </figure>
                                        <figcaption>
                                            <h3>{{ $player->name }}</h3>
                                            <p><span>{{ $player->role }}</span></p>
                                        </figcaption>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-center">No players available in this category</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-12 text-center">
                    <h3 class="no-team">No Player Data Available</h3>
                </div>
            @endif
        </div>
    </div>
</main>

@endsection
