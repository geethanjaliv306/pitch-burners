@extends('layouts.score')

@section('content')

<style>
  .striker-title{
    margin-bottom: 0px;
    font-size: 15px;
    color: white;
  }
  .replace-player.disabled {
    pointer-events: none;
    opacity: 0.5;
}
.scorenotedApp .bottom .scores-actions-wrap .inner .actions-left {
  grid-template-rows: repeat(3, 1fr);
}

</style>

<div class="scorenotedApp">
  <div class="top">
    <div class="container h-100 position-relative">
      <div class="row h-100">
        <div class="col-12">
          <div class="top-inner">
            <div class="backwith-company-name d-flex justify-content-between">
              <a class="backer" href={{route('schedulematch')}}><img class="d-none
                me-3" src="{{ asset('uploads/images/back-white.svg') }}" width="15" height="15" /> {{$batting_team_name}}</a>
              {{-- <div class="setings"></div> --}}
              <div class="box reset-game text-white" id="resetGameBtn" style="cursor: pointer">RESET</div>
            </div>
            <div class="score-with-runrate">
              <div class="scorz">0/0 <span>(/{{$match->overs}})</span></div>
              <div class="runratez">CRR:000  PROJECTED SCORE:000 (at00.00 RPO)</div>
            </div>

            <p class="name text-white" data-bs-target="#changeStrikeModal" data-bs-toggle="modal" style="
            text-align: center;
            margin: 0px;
            ">Swap</p>

            <div class="batter-strikers">
              <div class="batterz active left" id="batter_left">
                  <figure>
                      <img src="{{ asset('uploads/images/batterz.png') }}" />
                  </figure>
                  <figcaption>
                      <div class="d-flex">
                          <p class="name" id="leftBatterName" data-bs-target="#changeStrikeModals" data-bs-toggle="modal">Striker</p>
                          <p  data-bs-target="#selectNextPlayerModal" data-bs-toggle="modal"class="striker-title">Add<i class="bi bi-plus-circle"></i></p>
                      </div>
                      <p class="scorz">0(0)</p>
                      <p class="replace-player" data-batter="left" data-bs-target="#selectNextPlayerModal" data-bs-toggle="modal">Replace</p>
                  </figcaption>
              </div>

              <div class="batterz right" id="batter_right">
                  <figure>
                      <img src="{{ asset('uploads/images/batterz.png') }}" />
                  </figure>
                  <figcaption>
                      <div class="d-flex">
                          <p class="name" id="rightBatterName" data-bs-target="#changeStrikeModals" data-bs-toggle="modal">Non - Striker</p>
                          <p  data-bs-target="#selectNextPlayersModal" data-bs-toggle="modal" class="striker-title">Add</p>
                      </div>
                      <p class="scorz">0(0)</p>
                      <p class="replace-player" data-batter="right" data-bs-target="#selectNextPlayersModal" data-bs-toggle="modal">Replace</p>
                  </figcaption>
              </div>
          </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="bottom">
    <div class="bowler-over-score">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="bowler-over-score-inner">
              <div class="topbowl">
                <div class="bowler">
                    <img src="{{ asset('uploads/images/bowler2.svg') }}" id="currentBowlerImage" />
                    <span id="currentBowlerName">Bowler</span>
                </div>
                <div class="overs mb-0">
                    <p class="mb-0">0-0-0-0</p>
                </div>
                <p class="add-bowler mb-0 text-white" data-bs-target="#selectBowlerModal" data-bs-toggle="modal">Add Bowler</p>
                <p class="replace-player mb-0 text-white" data-bs-target="#selectBowlerModal" data-bs-toggle="modal">Replace</p>
            </div>
              <div class="bottombowl">
                <div class="scoreper-ball">

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="scores-actions-wrap">
      <div class="inner">
        <div class="actions-left">
          <div class="box undo" data-bs-toggle="modal" data-bs-target="#unDoModal">UNDO</div>
          <div class="box fivemore" data-bs-toggle="modal" data-bs-target="#fiveMoreModal">5, 7</div>
          <div class="box out" data-bs-toggle="modal" data-bs-target="#outModal">OUT</div>
          <div class="box lb d-none">LB</div>
        </div>
        {{-- <div class="actions-right">
          <div class="box zero">0</div>
          <div class="box one">1</div>
          <div class="box two">2</div>
          <div class="box three">3</div>
          <div class="box four">4<span>FOUR</span></div>
          <div class="box six">6<span>SIX</span></div>
          <div class="box wide" data-bs-toggle="modal" data-bs-target="#wideRunModal">WD</div>
          <div class="box noball" data-bs-toggle="modal" data-bs-target="#noBallRunModal">NB</div>
          <div class="box byes">BYE</div>
        </div> --}}
        <div class="actions-right" id="actions-right">
          <div id="box" class="box zero">0</div>
          <div id="box" class="box one">1</div>
          <div id="box" class="box two">2</div>
          <div id="box" class="box three">3</div>
          <div id="box" class="box four">4<span>FOUR</span></div>
          <div id="box" class="box six">6<span>SIX</span></div>
          <div id="box" class="box wide" data-bs-toggle="modal" data-bs-target="#wideRunModal">WD</div>
          <div id="box" class="box noball" data-bs-toggle="modal" data-bs-target="#noBallRunModal">NB</div>
          <div id="box" class="box byes">BYE</div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade changeStrikeModal modalOpeninBottomtoTop-mobile" id="changeStrikeModal" tabindex="-1" aria-labelledby="changeStrikeModalLabel" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-body text-center">
              <h3 class="title mb-3">Change Strike?</h3>
              {{-- <p class="mb-3">Tell the players what happened in the last ball.</p>
              <div class="checkbox-wrap">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                  <label class="form-check-label" for="flexCheckDefault">
                    Declared Run
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked" checked>
                  <label class="form-check-label" for="flexCheckChecked">
                    Short Run
                  </label>
                </div>
              </div> --}}
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Not Now</button>
            <button type="button" class="btn btn-primary" onclick="changeStrike()" data-bs-dismiss="modal">Yes Sure</button>
        </div>
      </div>
  </div>
</div>

<div class="modal fade unDoModal modalOpeninBottomtoTop-mobile" id="unDoModal" tabindex="-1" aria-labelledby="unDoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-body text-center">
              <h3 class="title">Undo?</h3>
              <p>Do you want to undo last ball?</p>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
              <button type="button" class="btn btn-primary">Yes</button>
          </div>
      </div>
  </div>
</div>

<div class="modal fade fiveMoreModal modalOpeninBottomtoTop-mobile" id="fiveMoreModal" tabindex="-1" aria-labelledby="fiveMoreModalLabel" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-body text-center">
              <h3 class="title">Runs Scored by running</h3>
              <div class="inputRunner"><input type="number" /> Runs</div>
              <label>*4 and 6 will not be considered boundaries</label>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
              <button type="button" class="btn btn-primary">Yes</button>
          </div>
      </div>
  </div>
</div>

<div class="modal fade wideRunModal modalOpeninBottomtoTop-mobile" id="wideRunModal" tabindex="-1" aria-labelledby="wideRunModalLabel" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-body text-center">
              <h3 class="title">Wide Ball (1 Run)</h3>
              <div class="wideball-modal-wrap">
                <!-- Radio buttons for Wide Ball options WD+0 to WD+8 -->
                <div class="wideball-modal-item">
                  <input type="radio" id="wideball_0" name="wideball" value="0" checked />
                  <label for="wideball_0">WD+0</label>
                </div>
                <div class="wideball-modal-item">
                  <input type="radio" id="wideball_1" name="wideball" value="1" />
                  <label for="wideball_1">WD+1</label>
                </div>
                <div class="wideball-modal-item">
                  <input type="radio" id="wideball_2" name="wideball" value="2" />
                  <label for="wideball_2">WD+2</label>
                </div>
                <div class="wideball-modal-item">
                  <input type="radio" id="wideball_3" name="wideball" value="3" />
                  <label for="wideball_3">WD+3</label>
                </div>
                <div class="wideball-modal-item">
                  <input type="radio" id="wideball_4" name="wideball" value="4" />
                  <label for="wideball_4">WD+4</label>
                </div>
                <div class="wideball-modal-item">
                  <input type="radio" id="wideball_5" name="wideball" value="5" />
                  <label for="wideball_5">WD+5</label>
                </div>
                <div class="wideball-modal-item">
                  <input type="radio" id="wideball_6" name="wideball" value="6" />
                  <label for="wideball_6">WD+6</label>
                </div>
                <div class="wideball-modal-item">
                  <input type="radio" id="wideball_7" name="wideball" value="7" />
                  <label for="wideball_7">WD+7</label>
                </div>
                <div class="wideball-modal-item">
                  <input type="radio" id="wideball_8" name="wideball" value="8" />
                  <label for="wideball_8">WD+8</label>
                </div>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary">Submit</button>
          </div>
      </div>
  </div>
</div>

<div class="modal fade noBallRunModal modalOpeninBottomtoTop-mobile" id="noBallRunModal" tabindex="-1" aria-labelledby="noBallRunModalLabel" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-body text-center">
              <h3 class="title">No Ball</h3>
              <div class="hit-not-hit-option mb-3">
                <label>
                    <input type="radio" name="hitOption" value="hit"> Hit
                </label>
                <label>
                    <input type="radio" name="hitOption" value="not-hit"> Not-Hit
                </label>
            </div>
              <div class="wideball-modal-wrap">

                <!-- Radio buttons for No Ball options NB+0 to NB+8 -->
                <div class="wideball-modal-item">
                  <input type="radio" id="noball_0" name="noball" value="0" checked />
                  <label for="noball_0">NB+0</label>
                </div>
                <div class="wideball-modal-item">
                  <input type="radio" id="noball_1" name="noball" value="1" />
                  <label for="noball_1">NB+1</label>
                </div>
                <div class="wideball-modal-item">
                  <input type="radio" id="noball_2" name="noball" value="2" />
                  <label for="noball_2">NB+2</label>
                </div>
                <div class="wideball-modal-item">
                  <input type="radio" id="noball_3" name="noball" value="3" />
                  <label for="noball_3">NB+3</label>
                </div>
                <div class="wideball-modal-item">
                  <input type="radio" id="noball_4" name="noball" value="4" />
                  <label for="noball_4">NB+4</label>
                </div>
                <div class="wideball-modal-item">
                  <input type="radio" id="noball_5" name="noball" value="5" />
                  <label for="noball_5">NB+5</label>
                </div>
                <div class="wideball-modal-item">
                  <input type="radio" id="noball_6" name="noball" value="6" />
                  <label for="noball_6">NB+6</label>
                </div>
                <div class="wideball-modal-item">
                  <input type="radio" id="noball_7" name="noball" value="7" />
                  <label for="noball_7">NB+7</label>
                </div>
                <div class="wideball-modal-item">
                  <input type="radio" id="noball_8" name="noball" value="8" />
                  <label for="noball_8">NB+8</label>
                </div>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary"id="submitNoBallRun">Submit</button>
          </div>
      </div>
  </div>
</div>

<div class="modal fade byesRunModal modalOpeninBottomtoTop-mobile" id="byesRunModal" tabindex="-1" aria-labelledby="byesRunModalLabel" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-body text-center">
              <h3 class="title">Byes Runs</h3>
              <div class="wideball-modal-wrap">
                  <!-- Radio buttons for Byes options 1 to 9 -->
                  <div class="wideball-modal-item">
                      <input type="radio" id="byes_1" name="byes" value="1" checked />
                      <label for="byes_1">1</label>
                  </div>
                  <div class="wideball-modal-item">
                      <input type="radio" id="byes_2" name="byes" value="2" />
                      <label for="byes_2">2</label>
                  </div>
                  <div class="wideball-modal-item">
                      <input type="radio" id="byes_3" name="byes" value="3" />
                      <label for="byes_3">3</label>
                  </div>
                  <div class="wideball-modal-item">
                      <input type="radio" id="byes_4" name="byes" value="4" />
                      <label for="byes_4">4</label>
                  </div>
                  <div class="wideball-modal-item">
                      <input type="radio" id="byes_5" name="byes" value="5" />
                      <label for="byes_5">5</label>
                  </div>
                  <div class="wideball-modal-item">
                      <input type="radio" id="byes_6" name="byes" value="6" />
                      <label for="byes_6">6</label>
                  </div>
                  <div class="wideball-modal-item">
                      <input type="radio" id="byes_7" name="byes" value="7" />
                      <label for="byes_7">7</label>
                  </div>
                  <div class="wideball-modal-item">
                      <input type="radio" id="byes_8" name="byes" value="8" />
                      <label for="byes_8">8</label>
                  </div>
                  <div class="wideball-modal-item">
                      <input type="radio" id="byes_9" name="byes" value="9" />
                      <label for="byes_9">9</label>
                  </div>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary">Submit</button>
          </div>
      </div>
  </div>
</div>

<div class="modal fade outModal modalOpeninBottomtoTop-mobile" id="outModal" tabindex="-1" aria-labelledby="outModalLabel" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-body text-center">
              <h3 class="title">Select Out Type</h3>
              <div class="outtype-wrap">
                <div class="outtype-item bowled" data-bs-target="#selectNextPlayerModal" data-bs-toggle="modal" onclick="handleBowledOut()">
                  <figure><img src="{{ asset('uploads/images/outtype/bowled.svg') }}" /></figure>
                  <figcaption>Bowled</figcaption>
              </div>
                <div class="outtype-item caught" data-bs-target="#caughtModal" data-bs-toggle="modal">
                    <figure><img src="{{ asset('uploads/images/outtype/cricket-catch.svg') }}" /></figure>
                    <figcaption>Caught</figcaption>
                </div>
                <div class="outtype-item caught-behind" data-bs-target="#caughtBehindModal" data-bs-toggle="modal">
                  <figure><img src="{{ asset('uploads/images/outtype/runout.svg') }}" /></figure>
                  <figcaption>Caught Behind</figcaption>
                </div>
                <div class="outtype-item caughtbowled" data-bs-target="#selectNextPlayerModal" data-bs-toggle="modal" onclick="handleCaughtBowledOut()">
                  <figure><img src="{{ asset('uploads/images/outtype/runout.svg') }}" /></figure>
                  <figcaption>Caught & Bowled</figcaption>
                </div>
                <div class="outtype-item runout" data-bs-target="#runOutModal" data-bs-toggle="modal">
                  <figure><img src="{{ asset('uploads/images/outtype/runout.svg') }}" /></figure>
                  <figcaption>Run Out</figcaption>
                </div>
                <div class="outtype-item lbw d-none" data-bs-target="#selectNextPlayerModal" data-bs-toggle="modal" onclick="handleLbwOut()">
                  <figure><img src="{{ asset('uploads/images/outtype/lbw.svg') }}" /></figure>
                  <figcaption>LBW</figcaption>
                </div>
                <div class="outtype-item stumped" data-bs-target="#stumpedModal" data-bs-toggle="modal">
                  <figure><img src="{{ asset('uploads/images/outtype/runout.svg') }}" /></figure>
                  <figcaption>Stumped</figcaption>
                </div>
                <div class="outtype-item retiredhurt" data-bs-target="#retireHurtModal" data-bs-toggle="modal">
                  <figure><img src="{{ asset('uploads/images/outtype/runout.svg') }}" /></figure>
                  <figcaption>Retired Hurt</figcaption>
                </div>
                <div class="outtype-item mankaded" data-bs-target="#selectNextPlayerModal" data-bs-toggle="modal">
                  <figure><img src="{{ asset('uploads/images/outtype/runout.svg') }}" /></figure>
                  <figcaption>Run Out (Mankad)</figcaption>
                </div>
                <div class="outtype-item hitwicket" data-bs-toggle="modal" onclick="handleHitWicket()">
                  <figure><img src="{{ asset('uploads/images/outtype/runout.svg') }}" /></figure>
                  <figcaption>Hit Wicket</figcaption>
                </div>
                <!-- <div class="outtype-item absent">
                  <figure><img src="{{ asset('uploads/images/outtype/runout.svg') }}" /></figure>
                  <figcaption>Absent</figcaption>
                </div> -->
                <div class="outtype-item retiredout" data-bs-target="#retireOutModal" data-bs-toggle="modal">
                  <figure><img src="{{ asset('uploads/images/outtype/runout.svg') }}" /></figure>
                  <figcaption>Retired Out</figcaption>
                </div>
              </div>
          </div>
      </div>
  </div>
</div>

<div class="modal fade selectNextPlayerModal modalOpeninBottomtoTop-mobile" id="selectNextPlayerModal" tabindex="-1" aria-labelledby="selectNextPlayerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="top-head">
          <i class="modal-back"  data-bs-dismiss="modal" aria-label="Close"><img src="{{ asset('uploads/images/back-white.svg') }}" /></i>
          <h1 class="modal-title fs-5">Select Next Player</h1>
        </div>
        <div class="top-bottom">
          <p>Playing Squad</p>
        </div>
      </div>
      <div class="modal-body text-center">
        <div class="playingsquad-remainning-wrap">
          @foreach($batters as $player)
            <div class="playingsquad-remainning-item">
              <input type="radio" id="remainningPlayer_{{ $player->id }}" name="remainningPlayer" value="{{ $player->id }}" />
              <label for="remainningPlayer_{{ $player->id }}">
                <img src="{{ config('constants.upload_url') . '/player_images/' . $player->player_image }}" alt="Player Image" />
                {{ $player->player_name }}
              </label>
            </div>
          @endforeach
        </div>
      </div>
      <div class="modal-footer">
        <p id="currentScoreDisplay"></p>
        <button type="button" class="btn btn-primary" onclick="selectNewStriker()">Select Player</button>
      </div>
    </div>
 </div>
</div>

<div class="modal fade selectNextPlayersModal modalOpeninBottomtoTop-mobile" id="selectNextPlayersModal" tabindex="-1" aria-labelledby="selectNextPlayersModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="top-head">
          <i class="modal-back"  data-bs-dismiss="modal" aria-label="Close"><img src="{{ asset('uploads/images/back-white.svg') }}" /></i>
          <h1 class="modal-title fs-5">Select Next Player</h1>
        </div>
        <div class="top-bottom">
          <p>Playing Squad</p>
        </div>
      </div>
      <div class="modal-body text-center">
        <div class="playingsquad-remainning-wrap">
          @foreach($batters as $player)
            <div class="playingsquad-remainning-item">
              <input type="radio" id="remainningPlayer_{{ $player->id }}" name="remainningPlayers" value="{{ $player->id }}" />
              <label for="remainningPlayer_{{ $player->id }}">
                <img src="{{ config('constants.upload_url') . '/player_images/' . $player->player_image }}" alt="Player Image" />

                {{ $player->player_name }}
              </label>
            </div>
          @endforeach
        </div>
      </div>
      <div class="modal-footer">
        <p id="currentScoreDisplay"></p>
        <button type="button" class="btn btn-primary" onclick="selectNewStrikers()">Select Player</button>
      </div>
    </div>
 </div>
</div>

<div class="modal fade selectBowlerModal modalOpeninBottomtoTop-mobile" id="selectBowlerModal" tabindex="-1" aria-labelledby="selectBowlerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="top-head">
          <i class="modal-back" data-bs-dismiss="modal" aria-label="Close">
            <img src="{{ asset('uploads/images/back-white.svg') }}" />
          </i>
          <h1 class="modal-title fs-5">Select Next Bowler</h1>
        </div>
        <div class="top-bottom">
          <p>Playing Squad ({{$bowling_team_name}})</p>
        </div>
      </div>
      <div class="modal-body text-center">
        <div class="playingsquad-remainning-wrap">
          @foreach($bowlers as $player)
            <div class="playingsquad-remainning-item">
              <input type="radio" id="remainningBowler_{{ $player->id }}" name="remainningBowler" value="{{ $player->id }}" />
              <label for="remainningBowler_{{ $player->id }}"  data-fielder-id="{{$player->id}}">
                <img src="{{ config('constants.upload_url') . '/player_images/' . $player->player_image }}" alt="Player Image" />

                {{ $player->player_name}}
              </label>
            </div>
          @endforeach
        </div>
      </div>
      <div class="modal-footer">
        <p id="currentScoreDisplay"></p>
      </div>
    </div>
  </div>
</div>

<div class="modal fade caughtModal modalOpeninBottomtoTop-mobile" id="caughtModal" tabindex="-1" aria-labelledby="caughtModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="top-head">
          <i class="modal-back"  data-bs-dismiss="modal" aria-label="Close"><img src="{{ asset('uploads/images/back-white.svg') }}" /></i>
          <h1 class="modal-title fs-5">Caught</h1>
        </div>
      </div>
        <div class="modal-body text-center">
           <div class="caught-wrap">
              <h5 class="subTitle text-left">Select Fielder</h5>
                <div class="playingsquad-remainning-wrap">
                @foreach($bowlers as $player)
                <div class="playingsquad-remainning-item">
                    <input type="radio" id="caughtFielderPlayer_{{ $player->id }}" name="caughtFielderPlayer" value="{{ $player->id }}" />
                    <label for="caughtFielderPlayer_{{ $player->id }}" data-fielder-id="{{$player->id}}">
                        <img src="{{ config('constants.upload_url') . '/player_images/' . $player->player_image }}" alt="Player Image" />

                      {{ $player->player_name }}
                    </label>
                </div>
                @endforeach
              </div>
           </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="handleCaughtOut()">Out</button>
        </div>
    </div>
 </div>
</div>

<div class="modal fade caughtBowledModal modalOpeninBottomtoTop-mobile" id="caughtBowledModal" tabindex="-1" aria-labelledby="caughtBowledModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="top-head">
          <i class="modal-back"  data-bs-dismiss="modal" aria-label="Close"><img src="{{ asset('uploads/images/back-white.svg') }}" /></i>
          <h1 class="modal-title fs-5">Caught & Bowled</h1>
        </div>
      </div>
        <div class="modal-body text-center">
            <h3 class="subTitle text-left">Caught & Bowled</h3>
            <p>Bowler caught the ball after bowling it, and the batter is out.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="handleCaughtBowledOut()">Out</button>
        </div>
    </div>
  </div>
</div>

<div class="modal fade selectFielderPlayerModal modalOpeninBottomtoTop-mobile" id="selectFielderPlayerModal" tabindex="-1" aria-labelledby="selectFielderPlayerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="top-head">
          <h1 class="modal-title fs-5">Select Fielder</h1>
        </div>
      </div>
      <div class="modal-body text-center">
        <div class="playingsquad-remainning-wrap">
          @foreach($bowlers as $player)
            <div class="playingsquad-remainning-item">
              <input type="radio" id="fielderPlayer_{{ $player->id }}" name="runOutFielder" value="{{ $player->id }}" />
              <label for="fielderPlayer_{{ $player->id }}" data-fielder-id="{{$player->id}}">
                <img src="{{ asset('storage/uploads/player_images/' . $player->player_image) }}" />
                {{ $player->player_name }}
              </label>
            </div>
          @endforeach
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary">Select Fielder</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade caughtBehindModal modalOpeninBottomtoTop-mobile" id="caughtBehindModal" tabindex="-1" aria-labelledby="caughtBehindModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="top-head">
          <i class="modal-back"  data-bs-dismiss="modal" aria-label="Close"><img src="{{ asset('uploads/images/back-white.svg') }}" /></i>
          <h1 class="modal-title fs-5">Caught Behind</h1>
        </div>
      </div>
      <div class="modal-body text-center">
         <div class="caught-wrap">
            <h5 class="subTitle text-left">Select Wicket-Keeper</h5>
            @foreach($bowlers as $player)
                <div class="playingsquad-remainning-item">
                    <input type="radio" id="caughtBehindFielderPlayer_{{ $player->id }}" name="caughtBehindFielderPlayer" value="{{ $player->id }}" />
                    <label for="caughtBehindFielderPlayer_{{ $player->id }}" data-fielder-id="{{$player->id}}">
                      <img src="{{ asset('storage/uploads/player_images/' . $player->player_image) }}" />
                      {{ $player->player_name }}
                    </label>
                </div>
            @endforeach
            <p class="note-wicketkeeper-change">Note: Tap on profile to change the player.</p>
         </div>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-primary" onclick="handleCaughtBehindOut()">Out</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade overCompletionModal" id="overCompletionModal" tabindex="-1" aria-labelledby="overCompletionModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body text-center">
        <h3 class="title mb-3">Over Completed!</h3>
        <p class="mb-3">Do you want to choose the next bowler for the next over?</p>
      </div>
      <div class="modal-footer" style="justify-content: space-around;">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"style="width:40%;">Not Now</button>
        <button type="button" class="btn btn-primary" id="chooseNextBowlerBtn" data-bs-dismiss="modal"style="width:40%;">Yes</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade runOutModal modalOpeninBottomtoTop-mobile" id="runOutModal" tabindex="-1" aria-labelledby="runOutModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <div class="top-head">
          <i class="modal-back" data-bs-dismiss="modal" aria-label="Close">
            <img src="{{ asset('uploads/images/back-white.svg') }}" />
          </i>
          <h1 class="modal-title fs-5">Run Out</h1>
        </div>
      </div>
      <div class="modal-body text-center">
        <h5 class="subTitle text-left">Select Batsman Out</h5>
        <div class="playingsquad-remainning-item striker-wrap mb-2">
          <input type="radio" id="runOutBatsmanPlayer_1" name="runOutBatsmanPlayer" value="striker" />
          <label for="runOutBatsmanPlayer_1"><img src="{{ asset('uploads/images/Rectangle 810.png') }}" /> Striker</label>
        </div>
        <div class="playingsquad-remainning-item striker-wrap mb-2">
          <input type="radio" id="runOutBatsmanPlayer_2" name="runOutBatsmanPlayer" value="nonStriker" />
          <label for="runOutBatsmanPlayer_2"><img src="{{ asset('uploads/images/Rectangle 810.png') }}" /> Non-Striker</label>
        </div>

        <h5 class="subTitle text-left">Select Fielder</h5>
        <div class="playingsquad-remainning-item" data-bs-target="#selectFielderPlayerModal" data-bs-toggle="modal">
          <input type="radio" id="runOutFielder_1" name="runOutFielder" value="Fielder1" />
          <label for="runOutFielder_1"><img src="{{ asset('uploads/images/Rectangle 810.png') }}" alt="Fielder" /> Select Fielder</label>
        </div>

        <h5 class="subTitle text-left">Select Delivery Type</h5>
        <div class="deleiverytype-wrap mb-4">
          <div class="deleiverytype-modal-item">
        <input class="form-check-input" type="radio" name="deliveryType" value="legal" id="deliveryTypeLegal" checked>
        <label class="form-check-label" for="deliveryTypeLegal">Legal</label>
      </div>
      <div class="deleiverytype-modal-item">
        <input class="form-check-input" type="radio" name="deliveryType" value="wide" id="deliveryTypeWide">
        <label class="form-check-label" for="deliveryTypeWide">Wide</label>
      </div>
      <div class="deleiverytype-modal-item">
        <input class="form-check-input" type="radio" name="deliveryType" value="noBall" id="deliveryTypeNoBall">
        <label class="form-check-label" for="deliveryTypeNoBall">No Ball</label>
      </div>
      <div class="deleiverytype-modal-item">
        <input class="form-check-input" type="radio" name="deliveryType" value="byes" id="deliveryTypeByes">
        <label class="form-check-label" for="deliveryTypeByes">Byes</label>
      </div>
        </div>


      <h5 class="subTitle text-left">Runs Scored</h5>
      <div class="runsscored-wrap">
        <div class="runsscored-modal-item">
          <input type="radio" id="runscored_0" name="runScored" value="0" checked>
          <label for="runscored_0">0</label>
        </div>
        <div class="runsscored-modal-item">
          <input type="radio" id="runscored_1" name="runScored" value="1">
          <label for="runscored_1">1</label>
        </div>
        <div class="runsscored-modal-item">
          <input type="radio" id="runscored_2" name="runScored" value="2">
          <label for="runscored_2">2</label>
        </div>
        <div class="runsscored-modal-item">
          <input type="radio" id="runscored_3" name="runScored" value="3">
          <label for="runscored_3">3</label>
        </div>
        <div class="runsscored-modal-item">
          <input type="radio" id="runscored_4" name="runScored" value="4">
          <label for="runscored_4">4</label>
        </div>
        <div class="runsscored-modal-item">
          <input type="radio" id="runscored_5" name="runScored" value="5">
          <label for="runscored_5">5</label>
        </div>
      </div>
      <div id="batterCrossedContainer">
        <label>
            <input type="checkbox" name="batterCrossed" />
            Batter Crossed?
        </label>
    </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="handleRunOut()">Confirm</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal for Switching Innings -->
<div class="modal fade" id="switchInningsModal" tabindex="-1" aria-labelledby="switchInningsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="switchInningsModalLabel">First Innings Completed</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>The first innings is complete. The second innings will now begin.</p>
                <button id="startSecondInnings" class="btn btn-primary" onclick="startSecondInnings('secondinnings')">Start Second Innings</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="InningsOverModal" tabindex="-1" aria-labelledby="InningsOverModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="InningsOverModalLabel">Match Completed</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>The Match is Over.</p>
                <button id="matchOverButton" class="btn btn-primary" onclick="matchOverButton()">Match is Over</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade stumpedModal modalOpeninBottomtoTop-mobile" id="stumpedModal" tabindex="-1" aria-labelledby="stumpedModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
      <div class="modal-content">
          <div class="modal-header">
              <div class="top-head">
                  <i class="modal-back" data-bs-dismiss="modal" aria-label="Close">
                      <img src="{{ asset('uploads/images/back-white.svg') }}" />
                  </i>
                  <h1 class="modal-title fs-5">Stumped</h1>
              </div>
          </div>
          <div class="modal-body text-center">
              <div class="caught-wrap">
                  <h5 class="subTitle text-left">Select Wicket-Keeper</h5>
                  <div class="playingsquad-remainning-wrap">
                      @foreach($bowlers as $player)
                          <div class="playingsquad-remainning-item">
                              <input type="radio" id="keeperFielderPlayer_{{ $player->id }}" name="keeperFielderPlayer" value="{{ $player->id }}" />
                              <label for="keeperFielderPlayer_{{ $player->id }}" data-fielder-id="{{$player->id}}">
                                <img src="{{ config('constants.upload_url') . '/player_images/' . $player->player_image }}" alt="Player Image" />

                                  {{ $player->player_name }}
                              </label>
                          </div>
                      @endforeach
                  </div>
                  <p class="note-wicketkeeper-change">Note: Tap on profile to change the player.</p>
                  <div class="form-check custom-formcheckbox">
                      <input class="form-check-input" type="checkbox" value="" id="stumped-wideball">
                      <label class="form-check-label" for="stumped-wideball">Wide Ball</label>
                  </div>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-primary" onclick="handleStumpedOut()">Out</button>
          </div>
      </div>
  </div>
</div>

<div class="modal fade mankadedModal modalOpeninBottomtoTop-mobile" id="mankadedModal" tabindex="-1" aria-labelledby="mankadedModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <div class="top-head">
          <i class="modal-back"  data-bs-dismiss="modal" aria-label="Close"><img src="{{ asset('uploads/images/back-white.svg') }}" /></i>
          <h1 class="modal-title fs-5">Run Out (Mankaded)</h1>
        </div>
      </div>
        <div class="modal-body text-center">
           <div class="caught-wrap">
            <h5 class="subTitle text-left">Who</h5>
              <div class="playingsquad-remainning-item striker-wrap">
                <input type="radio" id="mankadedBatsmanPlayer" name="mankadedBatsmanPlayer" value="" />
                <label for="mankadedBatsmanPlayer"><span><img src="{{ asset('uploads/images/Rectangle 810.png') }}" />Dinesh Chidambaram</span> <span class="striker">Striker</span></label>
              </div>
              <h5 class="subTitle text-left">Select Fielder</h5>
              <div class="playingsquad-remainning-item">
                <input type="radio" id="mankadedFielderPlayer" name="mankadedFielderPlayer" value="" />
                <label for="mankadedFielderPlayer"><span><img src="{{ asset('uploads/images/Rectangle 810.png') }}" />Ajith Rajan</span> <span class="fielder">Bowler</span></label>
              </div>
           </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary">Out</button>
        </div>
    </div>
 </div>
</div>

<div class="modal fade retireHurtModal modalOpeninBottomtoTop-mobile" id="retireHurtModal" tabindex="-1" aria-labelledby="retireHurtModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <div class="top-head">
          <i class="modal-back" data-bs-dismiss="modal" aria-label="Close">
            <img src="{{ asset('uploads/images/back-white.svg') }}" />
          </i>
          <h1 class="modal-title fs-5">Retired Hurt</h1>
        </div>
      </div>
      <div class="modal-body text-center">
        <div class="caught-wrap">
          <h5 class="subTitle text-left">Who</h5>
          <div class="playingsquad-remainning-item striker-wrap mb-2">
            <input type="radio" id="retireBatsmanPlayer_1" name="runOutBatsmanPlayer" value="striker" />
            <label for="retireBatsmanPlayer_1">
                <img id="strikerImageDisplay" src="{{ asset('uploads/images/Rectangle 810.png') }}" alt="Striker" />
                <span id="strikerNameDisplay">Striker</span> <span class="striker">Striker</span>
            </label>
          </div>
          <div class="playingsquad-remainning-item striker-wrap mb-3">
              <input type="radio" id="retireBatsmanPlayer_2" name="runOutBatsmanPlayer" value="nonStriker" />
              <label for="retireBatsmanPlayer_2">
                  <img id="nonStrikerImageDisplay" src="{{ asset('uploads/images/Rectangle 810.png') }}" alt="Non-Striker" />
                  <span id="nonStrikerNameDisplay">Non-Striker</span> <span class="striker">Non Striker</span>
              </label>
          </div>
          {{-- <div class="form-check custom-formcheckbox">
            <input class="form-check-input" type="checkbox" value="" id="retiredhurt-dontcountball">
            <label class="form-check-label" for="retiredhurt-dontcountball">
              Don't count the ball
            </label>
          </div> --}}
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="handleRetiredHurt()">Out</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade retireOutModal modalOpeninBottomtoTop-mobile" id="retireOutModal" tabindex="-1" aria-labelledby="retireOutModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <div class="top-head">
          <i class="modal-back" data-bs-dismiss="modal" aria-label="Close">
            <img src="{{ asset('uploads/images/back-white.svg') }}" />
          </i>
          <h1 class="modal-title fs-5">Retired Out</h1>
        </div>
      </div>
      <div class="modal-body text-center">
        <div class="caught-wrap">
          <h5 class="subTitle text-left">Who</h5>
          <div class="playingsquad-remainning-item striker-wrap mb-2">
            <input type="radio" id="retireOutBatsmanPlayer_1" name="retireOutBatsmanPlayer" value="striker" />
            <label for="retireOutBatsmanPlayer_1">
                <img id="strikerImageDisplay" src="{{ asset('uploads/images/Rectangle 810.png') }}" alt="Striker" />
                <span id="strikerNameDisplay">Striker</span> <span class="striker">Striker</span>
            </label>
          </div>
          <div class="playingsquad-remainning-item striker-wrap mb-3">
              <input type="radio" id="retireOutBatsmanPlayer_2" name="retireOutBatsmanPlayer" value="nonStriker" />
              <label for="retireOutBatsmanPlayer_2">
                  <img id="nonStrikerImageDisplay" src="{{ asset('uploads/images/Rectangle 810.png') }}" alt="Non-Striker" />
                  <span id="nonStrikerNameDisplay">Non-Striker</span> <span class="striker">Non Striker</span>
              </label>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="handleRetiredOut()">Out</button>
      </div>
    </div>
  </div>
</div>


<!-- jQuery CDN -->
<script type="module">

</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.socket.io/4.8.0/socket.io.min.js" integrity="sha384-OoIbkvzsFFQAG88r+IqMAjyOtYDPGO0cqK5HF5Uosdy/zUEGySeAzytENMDynREd" crossorigin="anonymous"></script>

<script>
    const socket = io('http://192.168.29.118:3000',{
            withCredentials: true,
            transports: ['websocket'],
    });
    const API = `${location.origin}/api`;
    const WEB = location.origin;

    const matchId = '{{ $match->id }}';
        socket.on('connect', () =>{
            console.warn('run scorer socket connected')
        });
        // Initialize the playersData object
    const playersData = {
        matchId: matchId,
        strikerId: null,
        nonStrikerId: null,
        bowlerId: null,
        currentInnings: 0
    };

  function Player(name, type) {
        this.name = name;
        this.type = type;
        this.score = 0;
        this.ballsFaced = 0;
        this.ballsBowled = 0;
        this.runsConceded = 0;
        this.wickets = 0;
        this.overs = 0;
  }

    let strikerSelected = false;
    let nonStrikerSelected = false;
    let bowlerSelected = false;
    let batterToReplace = null;
    let bowlerToReplace = null;
    let allBowlers = [];

    let striker = new Player('Striker', 'batter');
    let nonStriker = new Player('Non-Striker', 'batter');
    let bowler = new Player('Bowler', 'bowler');

    let allPlayers = [striker, nonStriker, bowler];

    let totalScore = 0;
    let currentOver = 0;
    let currentBall = 0;
    let lastActions = [];
    let overDetails = [];
    const matchOvers = parseInt('{{ $match->overs }}');
    let undoConfirmed = false;
    let overCompleted = false;
    let totalWickets = 0;

    function changeStrike() {
      let leftBatter = document.querySelector('.batterz.left');
      let rightBatter = document.querySelector('.batterz.right');
      leftBatter.classList.toggle('active');
      rightBatter.classList.toggle('active');


      updateUI();
      logPlayerStats();
    }

    function updateButtonStates() {
          document.querySelectorAll('.replace-player').forEach(button => {
              const playerType = button.closest('.batterz') ?
                  (button.closest('.batterz').classList.contains('left') ? 'striker' : 'nonStriker') :
                  'bowler';

              const isSelected = (playerType === 'striker' && strikerSelected) ||
                                (playerType === 'nonStriker' && nonStrikerSelected) ||
                                (playerType === 'bowler' && bowlerSelected);

              button.style.display = isSelected ? 'block' : 'none';
    });



    document.querySelectorAll('.striker-title, .add-bowler').forEach(button => {
            const playerType = button.classList.contains('striker-title') ?
                (button.closest('.batterz').classList.contains('left') ? 'striker' : 'nonStriker') :
                'bowler';

            const isSelected = (playerType === 'striker' && strikerSelected) ||
                              (playerType === 'nonStriker' && nonStrikerSelected) ||
                              (playerType === 'bowler' && bowlerSelected);

            button.style.display = isSelected ? 'none' : 'block';
        });
    }

    updateButtonStates();

    document.querySelectorAll('.replace-player, .striker-title, .add-bowler').forEach((button) => {
    button.addEventListener('click', function () {
        if (this.classList.contains('replace-player')) {
            if (this.closest('.batterz')) {
                batterToReplace = this.closest('.batterz').classList.contains('left') ? 'left' : 'right';
            } else {
                bowlerToReplace = 'current';
            }
        } else if (this.classList.contains('striker-title')) {
            batterToReplace = this.closest('.batterz').classList.contains('left') ? 'left' : 'right';
        } else if (this.classList.contains('add-bowler')) {
            bowlerToReplace = 'current';
        }

        // Clear checked inputs for players and bowlers
        document.querySelectorAll('input[name="remainningPlayer"], input[name="remainningBowler"]').forEach((radio) => {
            radio.checked = false;
        });

        // Show only the respective modal
        if (batterToReplace) {
            $('#selectNextPlayerModal').modal('show');
        } else if (bowlerToReplace) {
            $('#selectBowlerModal').modal('show');
            $('#selectNextPlayerModal').modal('hide');
        }
    });
 });

  //   function updateBatterName(newPlayerName) {
  //   let newPlayer = allPlayers.find(p => p.name === newPlayerName);
  //   if (!newPlayer) {
  //       newPlayer = new Player(newPlayerName, 'batter');
  //       allPlayers.push(newPlayer);
  //   }

  //   if (batterToReplace === 'left') {
  //       // Replace the current striker
  //       let oldStrikerIndex = allPlayers.findIndex(p => p.name === striker.name);
  //       if (oldStrikerIndex !== -1) {
  //           allPlayers[oldStrikerIndex] = { ...striker };
  //       }
  //       striker = newPlayer;
  //       document.getElementById('leftBatterName').innerText = newPlayerName;
  //       strikerSelected = true;
  //   } else if (batterToReplace === 'right') {
  //       // Replace the current non-striker
  //       let oldNonStrikerIndex = allPlayers.findIndex(p => p.name === nonStriker.name);
  //       if (oldNonStrikerIndex !== -1) {
  //           allPlayers[oldNonStrikerIndex] = { ...nonStriker };
  //       }
  //       nonStriker = newPlayer; // Update non-striker
  //       document.getElementById('rightBatterName').innerText = newPlayerName;
  //       nonStrikerSelected = true;
  //   }

  //   updateButtonStates();
  //   updateGameState();
  //  }



  function updateBowlerName(newPlayerName, playerId) {
    console.warn('newPlayerName => ', newPlayerName)
    console.warn('allPlayer => ', allPlayers)
      let newBowler = allPlayers.find(p => p.name === newPlayerName);
      if (!newBowler) {
          newBowler = new Player(newPlayerName, 'bowler');
          allPlayers.push(newBowler);
      }

      let bowlerData = {...newBowler, id: playerId}
      bowler = bowlerData;
      let bowlerElem = document.getElementById('currentBowlerName');
      bowlerElem.innerText = newPlayerName;
      bowlerElem.dataset.playerId = playerId;
      bowlerSelected = true;
      updateButtonStates();
      updateGameState();
  }

  document.querySelectorAll('input[name="remainningPlayer"]').forEach((radio) => {
      radio.addEventListener('change', function () {
          const playerName = this.nextElementSibling.innerText;
          const playerId = this.value;
          console.warn('remaningPlayer => ', playerId)
          updateBatterName(playerName, playerId);
          $('#selectNextPlayerModal').modal('hide');
          $('#selectNextPlayersModal').modal('hide');
      });
  });

  document.querySelectorAll('input[name="remainningBowler"]').forEach((radio) => {
    radio.addEventListener('change', function () {
        const playerName = this.nextElementSibling.innerText;
        const playerId = this.value;
        updateBowlerName(playerName, playerId);

        // Close the bowler modal after selection
        $('#selectBowlerModal').modal('hide');
        $('#selectNextPlayerModal').modal('hide');
          $('#selectNextPlayersModal').modal('hide');
    });
    });

    // Ensure that modals reset their radios when opened
    jQuery('#selectNextPlayerModal, #selectNextPlayersModal, #selectBowlerModal').on('show.bs.modal', function () {
        this.querySelectorAll('input[type="radio"]').forEach((radio) => {
            radio.checked = false;
        });
    });

    document.querySelectorAll('.striker-title').forEach(title => {
        title.innerText = 'Add';
    });

    function handleBowledOut() {
        // Increment bowler's wickets and balls bowled
        bowler.wickets += 1;
        bowler.ballsBowled += 1;

        // Increment team's total wickets
        totalWickets += 1;

        // Check which batter is currently active (who got bowled out)
        let activeBatterElement = document.querySelector('.batterz.active');
        let isStrikerOut = activeBatterElement.classList.contains('left'); // If active player is on the left, it's the striker

        // Log the out event and update ball counts for the striker
        let outPlayer = isStrikerOut ? striker : nonStriker;
        outPlayer.ballsFaced += 1;  // Add a ball for the batter

        // Add W to the score per ball
        updateScorePerBall('W');

        // If the striker is out, replace the striker. Otherwise, replace the non-striker.
        batterToReplace = isStrikerOut ? 'left' : 'right';

        // Open the selectNextPlayerModal to choose a new player for the replacement
        $('#selectNextPlayerModal').modal('show');

        // Check if the over is completed (6 balls)
        currentBall += 1;
        if (currentBall >= 6) {
            overCompleted = true;
            changeStrike(); // Change the active class at the end of the over
        }
        lastActions.push({
            type: 'bowled',
            wicketType: 'bowled',
            run: 0,
            extraType: 'WICKET',
            striker: { ...striker },
            nonStriker: { ...nonStriker },
            bowler: { ...bowler },
            currentBall,
            currentOver,
            totalScore
        });

        saveCurrentBallData();
        updateGameState();
    }

    function handleCaughtOut() {
        const selectedFielderId = document.querySelector('input[name="caughtFielderPlayer"]:checked');
        if (!selectedFielderId) {
            alert('Please select a fielder.');
            return;
        }

        const fielderName = selectedFielderId.nextElementSibling.textContent.trim(); // Get the selected fielder's name
        const fielderId = selectedFielderId.nextElementSibling.dataset.fielderId;
        let activeBatterElement = document.querySelector('.batterz.active');
        let isStrikerOut = activeBatterElement.classList.contains('left'); // Check if striker is active

        // Mark the current batter as out
        let outPlayer = isStrikerOut ? striker : nonStriker;
        outPlayer.ballsFaced += 1;  // Increment the balls faced by the batter

        // Update bowler stats
        bowler.wickets += 1;
        bowler.ballsBowled += 1;  // Increment the bowler's ball count
        bowler.runsConceded += 0;  // No runs added for the wicket

        // Increment team balls count (for total team balls faced)
        currentBall += 1;
        totalWickets += 1;

        // Show Wicket in the Score Per Ball
        updateScorePerBall('W');

        // Log the wicket event
        console.log(`${fielderName} caught ${outPlayer.name} off ${bowler.name}'s bowling.`);

        // Close the caught modal
        $('#caughtModal').modal('hide'); // Close the caughtModal

        // Open the modal to select the next batter
        batterToReplace = isStrikerOut ? 'left' : 'right';
        $('#selectNextPlayerModal').modal('show');  // Open the next player selection modal

        // Check if the over is completed (6 balls)
        if (currentBall >= 6) {
            overCompleted = true;
            changeStrike(); // Change the active class at the end of the over
        }
        lastActions.push({
            type: 'caught',
            wicketType: 'caught',
            run: 0,
            extraType: 'WICKET',
            fielder: fielderName,  // Include the fielder's name here
            fielderId: fielderId,
            striker: { ...striker },
            nonStriker: { ...nonStriker },
            bowler: { ...bowler },
            currentBall,
            currentOver,
            totalScore
        });

        saveCurrentBallData();
        updateGameState();
    }

    function selectNewStriker() {
        const selectedPlayerId = document.querySelector('input[name="remainningPlayer"]:checked');
        if (!selectedPlayerId) {
            alert('Please select a player.');
            return;
        }

        const playerName = selectedPlayerId.nextElementSibling.textContent.trim();
        updateBatterName(playerName, selectedPlayerId.value);

        // After selecting the new batter, close the modal
        $('#selectNextPlayerModal').modal('hide');
        $('#selectNextPlayersModal').modal('hide');

        // Update the game state
        updateGameState();
    }
    function selectNewStrikers() {
        const selectedPlayerId = document.querySelector('input[name="remainningPlayers"]:checked');
        if (!selectedPlayerId) {
            alert('Please select a player.');
            return;
        }

        const playerName = selectedPlayerId.nextElementSibling.textContent.trim();
        updateBatterName(playerName, selectedPlayerId.value);

        // After selecting the new batter, close the modal
        $('#selectNextPlayerModal').modal('hide');
        $('#selectNextPlayersModal').modal('hide');

        // Update the game state
        updateGameState();
    }



    function updateBatterName(newPlayerName, batterId) {
        let newPlayer = allPlayers.find(p => p.name === newPlayerName);
        if (!newPlayer) {
            newPlayer = new Player(newPlayerName, 'batter');
            allPlayers.push(newPlayer);
        }

        // Replace the currently active batter based on the value of batterToReplace
        if (batterToReplace === 'left') {
            let oldStrikerIndex = allPlayers.findIndex(p => p.name === striker.name);
            if (oldStrikerIndex !== -1) {
                allPlayers[oldStrikerIndex] = { ...striker };  // Backup old striker
            }
            let strikerData = {...newPlayer, id: batterId};
            striker = strikerData;  // Update striker

            const leftBatter = document.getElementById('leftBatterName');
            leftBatter.innerText = newPlayerName;
            leftBatter.dataset.playerId = batterId;
            strikerSelected = true;
        } else if (batterToReplace === 'right') {
            let oldNonStrikerIndex = allPlayers.findIndex(p => p.name === nonStriker.name);
            if (oldNonStrikerIndex !== -1) {
                allPlayers[oldNonStrikerIndex] = { ...nonStriker };  // Backup old non-striker
            }
            let nonStrikerData = {...newPlayer, id: batterId}
            nonStriker = nonStrikerData;  // Update non-striker

            const rightBatter = document.getElementById('rightBatterName');
            rightBatter.innerText = newPlayerName;
            rightBatter.dataset.playerId = batterId;
            nonStrikerSelected = true;
        }

        updateButtonStates();
        updateGameState();
    }

    function handleCaughtBehindOut() {
        const selectedWicketKeeperId = document.querySelector('input[name="caughtBehindFielderPlayer"]:checked');
        if (!selectedWicketKeeperId) {
            alert('Please select a wicket-keeper.');
            return;
        }

        const wicketKeeperName = selectedWicketKeeperId.nextElementSibling.textContent.trim(); // Get the selected wicket-keeper's name
        const fielderId = selectedWicketKeeperId.nextElementSibling.dataset.fielderId;
        let activeBatterElement = document.querySelector('.batterz.active');
        let isStrikerOut = activeBatterElement.classList.contains('left'); // Check if striker is active

        // Mark the current batter as out
        let outPlayer = isStrikerOut ? striker : nonStriker;
        outPlayer.ballsFaced += 1;  // Increment the balls faced by the batter

        // Update bowler stats
        bowler.wickets += 1;
        bowler.ballsBowled += 1;  // Increment the bowler's ball count
        bowler.runsConceded += 0;  // No runs added for the wicket

        // Increment team balls count (for total team balls faced)
        currentBall += 1;
        totalWickets += 1;

        // Log this action for undo purposes
        lastActions.push({
            type: 'caughtBehind',
            wicketType: 'caughtBehind',
            fielder: wicketKeeperName,
            fielderId: fielderId,
            outPlayer: { ...outPlayer },
            striker: { ...striker },
            nonStriker: { ...nonStriker },
            bowler: { ...bowler },
            currentBall,
            currentOver,
            totalScore
        });

        // Show Wicket in the Score Per Ball
        updateScorePerBall('W');

        // Log the wicket event
        console.log(`${wicketKeeperName} caught behind ${outPlayer.name} off ${bowler.name}'s bowling.`);

        // Close the caughtBehind modal
        $('#caughtBehindModal').modal('hide'); // Close the caughtBehindModal

        // Open the modal to select the next batter
        batterToReplace = isStrikerOut ? 'left' : 'right';
        $('#selectNextPlayerModal').modal('show');  // Open the next player selection modal

        // Check if the over is completed (6 balls)
        if (currentBall >= 6) {
            overCompleted = true;
            changeStrike(); // Change the active class at the end of the over
        }
        saveCurrentBallData();
        updateGameState();
    }

    function selectNewWicketKeeper() {
        const selectedPlayerId = document.querySelector('input[name="caughtBehindFielderPlayer"]:checked');
        if (!selectedPlayerId) {
            alert('Please select a wicketkeeper.');
            return;
        }

        const playerName = selectedPlayerId.nextElementSibling.textContent.trim();
        updateBatterName(playerName, selectedPlayerId.value);

        // After selecting the new wicketkeeper, close the modal
        $('#caughtBehindModal').modal('hide');

        // Update the game state
        updateGameState();
    }

    function handleCaughtBowledOut() {
        // Increment bowler's wickets and balls bowled
        bowler.wickets += 1;
        bowler.ballsBowled += 1;

        // Increment team's total wickets
        totalWickets += 1;

        // Check which batter is currently active (who got caught and bowled out)
        let activeBatterElement = document.querySelector('.batterz.active');
        let isStrikerOut = activeBatterElement.classList.contains('left'); // If active player is on the left, it's the striker

        // Log the out event and update ball counts for the striker
        let outPlayer = isStrikerOut ? striker : nonStriker;
        outPlayer.ballsFaced += 1;  // Add a ball for the batter

        // Add W to the score per ball (for UI display purposes)
        updateScorePerBall('W');

        // If the striker is out, replace the striker. Otherwise, replace the non-striker.
        batterToReplace = isStrikerOut ? 'left' : 'right';

        // Open the selectNextPlayerModal to choose a new player for the replacement
        $('#selectNextPlayerModal').modal('show');


        // Check if the over is completed (6 balls)
        currentBall += 1;
        if (currentBall >= 6) {
            overCompleted = true;
            changeStrike(); // Change the active class at the end of the over
        }
        const selectedfielderId = document.querySelector('input[name="remainningBowler"]:checked');
        const fielderName = selectedfielderId.nextElementSibling.textContent.trim();
        const fielderId = selectedfielderId.nextElementSibling.dataset.fielderId;

        lastActions.push({
            type: 'caughtbowled',
            wicketType: 'caughtbowled',
            run: 0,
            extraType: 'WICKET',
            fielderId: fielderId,
            striker: { ...striker },
            nonStriker: { ...nonStriker },
            bowler: { ...bowler },
            currentBall,
            currentOver,
            totalScore
        });

        saveCurrentBallData();
        updateGameState();
    }

    function proceedToNextPlayerModal() {
        const selectedFielderId = document.querySelector('input[name="runOutFielder"]:checked');
        if (!selectedFielderId) {
            alert('Please select a fielder.');
            return;
        }

        const selectedFielderLabel = selectedFielderId.nextElementSibling;
        const selectedFielderName = selectedFielderLabel.textContent.trim();
        const selectedFielderImage = selectedFielderLabel.querySelector('img').src;

        // Update any required UI elements (optional)
        const fielderLabel = document.querySelector('#runOutFielder_1 + label');
        if (fielderLabel) {
            fielderLabel.innerHTML = `<img src="${selectedFielderImage}" alt="${selectedFielderName}" /> ${selectedFielderName}`;
        }

        // Close the current modal and show the next one immediately
        $('#runOutModal').modal('hide'); // Close the run-out modal
        $('#selectNextPlayerModal').modal('show'); // Show the modal for selecting the next player
        $('#selectNextPlayersModal').modal('hide');
    }

    document.querySelectorAll('input[name="runOutFielder"]').forEach((radio) => {
        radio.addEventListener('change', function () {
            const selectedFielderLabel = this.nextElementSibling;
            const selectedFielderName = selectedFielderLabel.textContent.trim();
            const selectedFielderImage = selectedFielderLabel.querySelector('img').src;

            // Update the fielder's name and image in the runOutModal under the Select Fielder section
            const fielderLabel = document.querySelector('#runOutModal .playingsquad-remainning-item[data-bs-target="#selectFielderPlayerModal"] label');
            if (fielderLabel) {
                fielderLabel.innerHTML = `<img src="${selectedFielderImage}" alt="${selectedFielderName}" /> ${selectedFielderName}`;
            }

            // Update the value of the selected fielder input in the runOutModal
            const fielderInput = document.querySelector('#runOutModal .playingsquad-remainning-item input[name="runOutFielder"]');
            if (fielderInput) {
                fielderInput.value = selectedFielderName;
            }

            // Close the fielder selection modal
            $('#selectFielderPlayerModal').modal('hide');

            // Reopen the run-out modal with updated fielder details
            $('#runOutModal').modal('show');
        });
    });

    function confirmFielderSelection() {
        // Close the run-out modal
        $('#runOutModal').modal('hide');

        // After confirming, open the next player selection modal for the batter
        $('#selectNextPlayerModal').modal('show');
    }

    document.querySelector('#runOutModal .btn-primary').addEventListener('click', confirmFielderSelection);

  function handleRunOut() {
    const batsmanOut = document.querySelector('input[name="runOutBatsmanPlayer"]:checked');
    const fielder = document.querySelector('input[name="runOutFielder"]:checked');
    const deliveryType = document.querySelector('input[name="deliveryType"]:checked').value;
    const runsScored = parseInt(document.querySelector('input[name="runScored"]:checked').value);
    const batterCrossed = document.querySelector('input[name="batterCrossed"]:checked');

    // Ensure both batsman and fielder are selected
    if (!batsmanOut || !fielder) {
        alert('Please select a batsman and a fielder.');
        return;
    }

    const batsmanOutValue = batsmanOut.value;
    const fielderName = fielder.nextElementSibling.textContent.trim(); // Get the selected fielder's name
    const fielderId = fielder.nextElementSibling.dataset.fielderId;
    let extraRun = 0;
    let ballTypeLabel = '';

    if (batsmanOutValue === 'striker') {
        outPlayer = striker;
        outPlayer.ballsFaced += 1;
        outPlayer.score += runsScored; // Add the runs scored before getting out
        isStrikerOut = true;
    } else {
        outPlayer = nonStriker;
        outPlayer.ballsFaced += 1;
        outPlayer.score += runsScored;
        isStrikerOut = false;
    }

    // Handle wide or no-ball cases by adding 1 run to the total score automatically
    if (deliveryType === 'wide') {
        extraRun = 1; // 1 extra run for wide delivery
        ballTypeLabel = 'WD'; // Label for wide
    } else if (deliveryType === 'noBall') {
        extraRun = 1; // 1 extra run for no-ball delivery
        ballTypeLabel = 'NB'; // Label for no-ball
    } else if (deliveryType === 'byes') {
        ballTypeLabel = 'BY'; // Label for byes
    }

    // Update total score with extra run and runs scored
    totalScore += runsScored + extraRun;

    // Increment bowler's runs conceded for non-byes deliveries
    if (deliveryType !== 'byes') {
        bowler.runsConceded += runsScored + extraRun;
    }

    // Increment the ball count only for legal or byes delivery types
    if (deliveryType === 'legal' || deliveryType === 'byes') {
        currentBall += 1;
        bowler.ballsBowled += 1;
    }

    // Display "W + [ballTypeLabel] + [runs]" in the score per ball
    const scoreLabel = `W${ballTypeLabel ? ' + ' + ballTypeLabel : ''} + ${runsScored}`;
    updateScorePerBall(scoreLabel, runsScored + extraRun);

    // Increment the wicket count for the bowler
    bowler.wickets += 1;
    totalWickets += 1;

    // Check if we need to change the strike (odd-numbered runs change strike)
    if (runsScored % 2 !== 0) {
        changeStrike();
    }
    if (batterCrossed && batterCrossed.checked) {
        if (isStrikerOut) {
            batterToReplace = 'left'; // New batter comes to striker's position
        } else {
            batterToReplace = 'right'; // New batter comes to non-striker's position
        }
    } else {
        if (isStrikerOut) {
            batterToReplace = 'right'; // New batter goes to non-striker's position
        } else {
            batterToReplace = 'left'; // New batter goes to striker's position
        }
    }

    // Replace the outgoing batsman
    batterToReplace = batsmanOutValue === 'striker' ? 'left' : 'right';

    // Open the modal to select the next player
    $('#runOutModal').modal('hide');
    $('#selectNextPlayerModal').modal('show');

    // Check if the over is completed (6 balls)
    if (currentBall >= 6) {
        overCompleted = true;
        changeStrike(); // Change strike at the end of the over
    }

    lastActions.push({
        type: 'runOut',
        wicketType: 'runOut',
        run: runsScored,
        extraType: deliveryType,
        fielder: fielderName,  // Include the fielder's name here
        fielderId: fielderId,
        striker: { ...striker },
        nonStriker: { ...nonStriker },
        batterCrossed: batterCrossed && batterCrossed.checked,
        bowler: { ...bowler },
        currentBall,
        currentOver,
        totalScore
    });

    saveCurrentBallData();
    updateGameState();
 }


    // function selectNewStriker() {
    //   const selectedPlayerId = document.querySelector('input[name="remainningPlayer"]:checked');
    //   if (!selectedPlayerId) {
    //     alert('Please select a player.');
    //     return;
    //   }

    //   const playerName = selectedPlayerId.nextElementSibling.textContent.trim();
    //   updateBatterName(playerName);

    //   // After selecting the new batter, close the modal
    //   $('#selectNextPlayerModal').modal('hide');
    //   $('#selectNextPlayersModal').modal('hide');


    //   // Update the game state
    //   updateGameState();
    // }

    // function selectNewStrikers() {
    //   const selectedPlayerId = document.querySelector('input[name="remainningPlayers"]:checked');
    //   if (!selectedPlayerId) {
    //     alert('Please select a player.');
    //     return;
    //   }

    //   const playerName = selectedPlayerId.nextElementSibling.textContent.trim();
    //   updateBatterName(playerName);

    //   // After selecting the new batter, close the modal
    //   $('#selectNextPlayerModal').modal('hide');
    //   $('#selectNextPlayersModal').modal('hide');


    //   // Update the game state
    //   updateGameState();
    // }

    function handleLbwOut() {
        // Increment bowler's wickets and balls bowled
        bowler.wickets += 1;
        bowler.ballsBowled += 1;

        // Increment team's total wickets
        totalWickets += 1;

        // Check which batter is currently active (who got out LBW)
        let activeBatterElement = document.querySelector('.batterz.active');
        let isStrikerOut = activeBatterElement.classList.contains('left'); // If active player is on the left, it's the striker

        // Log the out event and update ball counts for the striker
        let outPlayer = isStrikerOut ? striker : nonStriker;
        outPlayer.ballsFaced += 1;  // Add a ball for the batter

        // Add W to the score per ball (for UI display purposes)
        updateScorePerBall('W');

        // If the striker is out, replace the striker. Otherwise, replace the non-striker.
        batterToReplace = isStrikerOut ? 'left' : 'right';

        // Open the selectNextPlayerModal to choose a new player for the replacement
        $('#selectNextPlayerModal').modal('show');

        // Check if the over is completed (6 balls)
        currentBall += 1;
        if (currentBall >= 6) {
        overCompleted = true;
        changeStrike(); // Change the active class at the end of the over
        }

        // Log the wicket event
        lastActions.push({
        type: 'lbw',
        wicketType: 'lbw',
        run: 0,
        extraType: 'WICKET',
        striker: { ...striker },
        nonStriker: { ...nonStriker },
        bowler: { ...bowler },
        currentBall,
        currentOver,
        totalScore
        });

        saveCurrentBallData();
        updateGameState(); // Update the game state after LBW
    }

    function handleStumpedOut() {
        const selectedWicketKeeperId = document.querySelector('input[name="keeperFielderPlayer"]:checked');
        if (!selectedWicketKeeperId) {
            alert('Please select a wicket-keeper.');
            return;
        }

        const wicketKeeperName = selectedWicketKeeperId.nextElementSibling.textContent.trim(); // Get the selected wicket-keeper's name
        const fielderId = selectedWicketKeeperId.nextElementSibling.dataset.fielderId;
        const isWideBall = document.getElementById('stumped-wideball').checked; // Check if the stumped dismissal is due to a wide ball
        let activeBatterElement = document.querySelector('.batterz.active');
        let isStrikerOut = activeBatterElement.classList.contains('left'); // Check if striker is active

        // Mark the current batter as out
        let outPlayer = isStrikerOut ? striker : nonStriker;
        outPlayer.ballsFaced += isWideBall ? 0 : 1; // Increment the balls faced by the batter only if it's not a wide

        // Update bowler stats
        bowler.wickets += 1;
        if (!isWideBall) {
            bowler.ballsBowled += 1; // Increment the bowler's ball count only if it's not a wide
        }
        bowler.runsConceded += isWideBall ? 1 : 0; // Add 1 run if it's a wide

        // Increment team balls count (for total team balls faced)
        if (!isWideBall) {
            currentBall += 1;
        }
        totalWickets += 1;
        totalScore += isWideBall ? 1 : 0; // Add 1 run to the score if it's a wide

        // Show Wicket in the Score Per Ball
        const scoreLabel = isWideBall ? 'W + WD' : 'W';
        updateScorePerBall(scoreLabel);

        // Log the wicket event
        console.log(`${wicketKeeperName} stumped ${outPlayer.name} off ${bowler.name}'s bowling.`);

        // Close the stumped modal
        $('#stumpedModal').modal('hide'); // Close the stumped modal

        // Open the modal to select the next batter
        batterToReplace = isStrikerOut ? 'left' : 'right';
        $('#selectNextPlayerModal').modal('show'); // Open the next player selection modal

        // Check if the over is completed (6 balls)
        if (currentBall >= 6) {
            overCompleted = true;
            changeStrike(); // Change the active class at the end of the over
        }

        // Log the action for undo functionality
        lastActions.push({
            type: 'stumped',
            wicketType: 'stumped',
            wicketKeeper: wicketKeeperName,
            outPlayer: { ...outPlayer },
            striker: { ...striker },
            nonStriker: { ...nonStriker },
            bowler: { ...bowler },
            isWideBall: isWideBall,
            fielderId: fielderId,
            currentBall,
            currentOver,
            totalScore
        });

        saveCurrentBallData();
        updateGameState();
    }

    function handleMankadOut() {
        // Increment bowler's wickets since the non-striker is out due to Mankad
        bowler.wickets += 1;

        // Increment total wickets for the batting team
        totalWickets += 1;

        const selectedfielderId = document.querySelector('input[name="remainningBowler"]:checked');
        const fielderName = selectedfielderId.nextElementSibling.textContent.trim();
        const fielderId = selectedfielderId.nextElementSibling.dataset.fielderId;

        // Log the Mankad event for undo functionality
        lastActions.push({
            type: 'mankad',
            wicketType: 'mankad',
            striker: { ...striker },
            nonStriker: { ...nonStriker },
            bowler: { ...bowler },
            currentBall,
            fielderId: fielderId,
            currentOver,
            totalScore
        });

        // Display Wicket in the Score Per Ball
        updateScorePerBall('W + Mankad');

        // Set batterToReplace to 'right' since the non-striker is being replaced
        batterToReplace = 'right';

        // Open the modal to select the next batter for the non-striker position
        $('#selectNextPlayerModal').modal('show');

        saveCurrentBallData();
        updateGameState();
    }

    function selectNextAvailableBatter() {
        const nextBatter = allPlayers.find(player => player.type === 'batter' && player.name !== striker.name && player.name !== nonStriker.name);

        if (nextBatter) {
            striker = nextBatter;
            document.getElementById('leftBatterName').innerText = nextBatter.name;

            console.log(`New striker: ${nextBatter.name}`);
        } else {
            alert('No more available players to replace the striker.');
        }

        updateGameState();
    }

    document.querySelector('.outtype-item.mankaded').addEventListener('click', function () {
        handleMankadOut();
    });

    // function selectNewStriker() {
    //     const selectedPlayerId = document.querySelector('input[name="remainningPlayer"]:checked');
    //     if (!selectedPlayerId) {
    //         alert('Please select a player.');
    //         return;
    //     }

    //     const playerName = selectedPlayerId.nextElementSibling.textContent.trim();

    //     // Replace the non-striker with the selected player
    //     if (batterToReplace === 'right') {
    //         updateBatterName(playerName); // Update the non-striker
    //     }

    //     // After selecting the new batter, close the modal
    //     $('#selectNextPlayerModal').modal('hide');

    //     // Update the game state
    //     updateGameState();
    // }

    // function selectNewStrikers() {
    //     const selectedPlayerId = document.querySelector('input[name="remainningPlayer"]:checked');
    //     if (!selectedPlayerId) {
    //         alert('Please select a player.');
    //         return;
    //     }

    //     const playerName = selectedPlayerId.nextElementSibling.textContent.trim();

    //     // Replace the non-striker with the selected player
    //     if (batterToReplace === 'right') {
    //         updateBatterName(playerName); // Update the non-striker
    //     }

    //     // After selecting the new batter, close the modal
    //     $('#selectNextPlayerModal').modal('hide');

    //     // Update the game state
    //     updateGameState();
    // }

    function handleHitWicket() {
        // Increment bowler's wickets and balls bowled
        bowler.wickets += 1;
        bowler.ballsBowled += 1;

        // Increment team's total wickets
        totalWickets += 1;

        // Check which batter is currently active (who got hit-wicket)
        let activeBatterElement = document.querySelector('.batterz.active');
        let isStrikerOut = activeBatterElement.classList.contains('left'); // If active player is on the left, it's the striker

        // Update the batter's balls faced and log the out event
        let outPlayer = isStrikerOut ? striker : nonStriker;
        outPlayer.ballsFaced += 1;  // Add a ball for the batter

        // Display "W" for the hit-wicket event in the score per ball UI
        updateScorePerBall('W');

        // Open the selectNextPlayerModal to choose a new player for the replacement
        batterToReplace = isStrikerOut ? 'left' : 'right';
        $('#selectNextPlayerModal').modal('show');

        // Increment the current ball count and check if the over is completed
        currentBall += 1;
        if (currentBall >= 6) {
            overCompleted = true;
            changeStrike(); // Change the active class at the end of the over
        }

        // Log the action for undo functionality
        lastActions.push({
            type: 'hitWicket',
            wicketType: 'hitWicket',
            run: 0,
            extraType: 'WICKET',
            striker: { ...striker },
            nonStriker: { ...nonStriker },
            bowler: { ...bowler },
            currentBall,
            currentOver,
            totalScore
        });

        saveCurrentBallData(); // Save the ball data for future reference
        updateGameState(); // Update the game state after hit-wicket
    }

    function handleRetiredHurt() {
        const retiredPlayer = document.querySelector('input[name="runOutBatsmanPlayer"]:checked');
        if (!retiredPlayer) {
            alert('Please select the retiring player.');
            return;
        }

        const retiredPlayerType = retiredPlayer.value; // 'striker' or 'nonStriker'
        let retiredPlayerName = retiredPlayerType === 'striker' ? striker.name : nonStriker.name;

        console.log(`${retiredPlayerName} has retired hurt.`);

        // Set batterToReplace to the appropriate position
        batterToReplace = retiredPlayerType === 'striker' ? 'left' : 'right';

        // Open the modal to select the next player for replacement
        $('#selectNextPlayerModal').modal('show');

        // Close the Retired Hurt modal after opening the player selection modal
        $('#retireHurtModal').modal('hide');

        lastActions.push({
            type: 'retiredHurt',
            wicketType: 'retiredHurt',
            run: 0,
            extraType: 'WICKET',
            striker: { ...striker },
            nonStriker: { ...nonStriker },
            bowler: { ...bowler },
            currentBall,
            currentOver,
            totalScore
        });

        saveCurrentBallData();
    }

    function updateRetiredHurtModal() {
        document.getElementById('strikerNameDisplay').innerText = striker.name || 'Striker';
        document.getElementById('strikerImageDisplay').src = striker.image || '{{ asset("uploads/images/Rectangle 810.png") }}';
        document.getElementById('nonStrikerNameDisplay').innerText = nonStriker.name || 'Non-Striker';
        document.getElementById('nonStrikerImageDisplay').src = nonStriker.image || '{{ asset("uploads/images/Rectangle 810.png") }}';

        // Pre-select the striker or non-striker based on the active class
        if (document.querySelector('.batterz.left').classList.contains('active')) {
            document.getElementById('retireBatsmanPlayer_1').checked = true; // Striker is selected
            document.getElementById('retireBatsmanPlayer_2').checked = false;
        } else {
            document.getElementById('retireBatsmanPlayer_2').checked = true; // Non-striker is selected
            document.getElementById('retireBatsmanPlayer_1').checked = false;
        }
    }

    $('#retireHurtModal').on('show.bs.modal', updateRetiredHurtModal);

    function handleRetiredOut() {
        const retiredPlayer = document.querySelector('input[name="retireOutBatsmanPlayer"]:checked');
        if (!retiredPlayer) {
            alert('Please select the retiring player.');
            return;
        }

        const retiredPlayerType = retiredPlayer.value; // 'striker' or 'nonStriker'
        let retiredPlayerName = retiredPlayerType === 'striker' ? striker.name : nonStriker.name;

        console.log(`${retiredPlayerName} has retired out.`);

        // Increment the total wickets for the team
        totalWickets += 1;

        // Set batterToReplace to the appropriate position
        batterToReplace = retiredPlayerType === 'striker' ? 'left' : 'right';

        // Log the retired out action for undo functionality
        lastActions.push({
            type: 'retiredOut',
            wicketType: 'retiredOut',
            outPlayer: { ...retiredPlayerType === 'striker' ? striker : nonStriker },
            totalWickets,
            currentBall,
            currentOver,
            totalScore
        });

        // Open the modal to select the next player for replacement
        $('#selectNextPlayerModal').modal('show');

        // Close the Retired Out modal after opening the player selection modal
        $('#retireOutModal').modal('hide');

        saveCurrentBallData();
        updateGameState();
    }

    function updateRetiredOutModal() {
        document.getElementById('strikerNameDisplay').innerText = striker.name || 'Striker';
        document.getElementById('strikerImageDisplay').src = striker.image || '{{ asset("uploads/images/Rectangle 810.png") }}';
        document.getElementById('nonStrikerNameDisplay').innerText = nonStriker.name || 'Non-Striker';
        document.getElementById('nonStrikerImageDisplay').src = nonStriker.image || '{{ asset("uploads/images/Rectangle 810.png") }}';

        // Pre-select the striker or non-striker based on the active class
        if (document.querySelector('.batterz.left').classList.contains('active')) {
            document.getElementById('retireOutBatsmanPlayer_1').checked = true; // Striker is selected
            document.getElementById('retireOutBatsmanPlayer_2').checked = false;
        } else {
            document.getElementById('retireOutBatsmanPlayer_2').checked = true; // Non-striker is selected
            document.getElementById('retireOutBatsmanPlayer_1').checked = false;
        }
    }

    $('#retireOutModal').on('show.bs.modal', updateRetiredOutModal);

    function resetAllSelections() {
        // Reset player selections
        strikerSelected = false;
        nonStrikerSelected = false;
        bowlerSelected = false;
        batterToReplace = null;
        bowlerToReplace = null;

        // Reset player objects
        striker = new Player('Striker', 'batter');
        nonStriker = new Player('Non-Striker', 'batter');
        bowler = new Player('Bowler', 'bowler');

        allPlayers = [striker, nonStriker, bowler];
        allBowlers = [];

        // Reset score, overs, and ball counts
        totalScore = 0;
        totalWickets = 0;
        currentOver = 0;
        currentBall = 0;
        lastActions = [];
        overDetails = [];
        overCompleted = false;
        undoConfirmed = false;

        // Reset UI elements for CRR, Projected Score, and RPO
        document.querySelector('.runratez').innerHTML = `CRR: 0.00 PROJECTED SCORE: 0 (at 0.00 RPO)`;

        // Reset other UI elements as needed
        document.getElementById('leftBatterName').innerText = 'Striker';
        document.getElementById('rightBatterName').innerText = 'Non-Striker';
        document.getElementById('currentBowlerName').innerText = 'Bowler';
        clearUIElements(); // Reset UI for balls and overs

        // Update the UI
        updateUI();
    }

    document.addEventListener('DOMContentLoaded', updateButtonStates);
    let activePlayer;
    let nonActivePlayer;
    function updateScore(run, isExtra = false) {
        if (overCompleted) {
            resetOver(); // Resets the over if it's already marked as completed
        }

        lastActions.push({
            run,
            striker: { ...striker },
            nonStriker: { ...nonStriker },
            bowler: { ...bowler },
            currentBall,
            currentOver,
            totalScore
        });

        totalScore += run;

        // Determine which player is active (based on the active class)

        if (document.querySelector('.batterz.left').classList.contains('active')) {
            activePlayer = striker; // Left player (striker) is active
            nonActivePlayer = nonStriker
        } else {
            activePlayer = nonStriker; // Right player (non-striker) is active
            nonActivePlayer = striker;
        }

        // Update the score for the active player if it's not an extra run
        if (!isExtra) {
            activePlayer.score += run;
            activePlayer.ballsFaced += 1;
            bowler.ballsBowled += 1;
            bowler.runsConceded += run;
            // currentBall += 1;
             // Handle ball numbering
        if (currentBall < 6) {
            currentBall += 1;
        } else {
            currentBall = 0; // Reset ball count for the next over
            currentOver += 1;
        }
            updateScorePerBall(run);

            if (run % 2 !== 0) {
                changeStrike(); // Change the active batter for odd-numbered runs
            }
        }

        // Handle over completion
        if (currentBall >= 6) {
            overCompleted = true;
            $('#overCompletionModal').modal('show');
            document.getElementById('chooseNextBowlerBtn').addEventListener('click', function () {
    // Open the bowler selection modal
    $('#selectBowlerModal').modal('show');
});

$('#selectBowlerModal').on('hide.bs.modal', function () {
    changeStrike(); // Change the active class at the end of the over after bowler selection
    // Clear scorePerBall for the new bowler
    const scorePerBallDiv = document.querySelector('.scoreper-ball');
    if (scorePerBallDiv) {
        scorePerBallDiv.innerHTML = '';
    }
});

            // Only change the strike after selecting the next bowler
            $('#selectBowlerModal').on('hide.bs.modal', function () {
                changeStrike(); // Change the active class at the end of the over after bowler selection
            });
        }

        saveCurrentBallData();
        updateGameState();
    }



    function handleExtras(run, extraType = null, isByes = false) {
    if (overCompleted) {
        resetOver();
    }

    lastActions.push({
        run,
        striker: { ...striker },
        nonStriker: { ...nonStriker },
        bowler: { ...bowler },
        extraType: isByes ? 'BY' : extraType,
        currentBall,
        currentOver,
        totalScore
    });

    // Add runs to the total score
    totalScore += run;

    // Increment bowler's runs conceded for all extras, including byes
    bowler.runsConceded += run;

    if (isByes) {
        // Increment the ball count for byes (since a valid ball was bowled)
        currentBall += 1;
        bowler.ballsBowled += 1;

        // Determine which player is active (based on the active class)
        let activePlayer = document.querySelector('.batterz.left').classList.contains('active') ? striker : nonStriker;
        activePlayer.ballsFaced += 1; // Increment balls faced for the active striker

        updateScorePerBall("BY", run); // Update UI for byes

        // Check if the over is completed (6 balls)
        if (currentBall >= 6) {
            overCompleted = true;
        }

        // Change strike for odd-numbered byes
        if (run % 2 !== 0) {
            changeStrike();
        }
    } else if (extraType === "WD") {
        totalScore += run;
        bowler.runsConceded += run;

        if ((run - 1) % 2 !== 0) {
            changeStrike();
        }

        updateScorePerBall("WD", run);
    } else {
        bowler.runsConceded += run; // Handle other extras (no-ball, leg-byes, etc.)

        if (!extraType) {
            currentBall += 1;
            bowler.ballsBowled += 1;

            updateScorePerBall(run); // Update the score display per ball

            if (currentBall >= 6) {
                overCompleted = true;
            }
        } else {
            // Handle wide, no-ball, etc. (no ball count increment)
            updateScorePerBall(extraType, run);
        }
    }
    saveCurrentBallData();
    updateGameState();
}

function resetOver() {
    overDetails.push({
        overNumber: currentOver,
        bowlerName: bowler.name,
        runs: bowler.runsConceded - (bowler.overs * 6),
        balls: 6,
    });

    currentOver += 1;
    currentBall = 0;
    bowler.overs += 1;
    overCompleted = false;
    clearUIElements();
    updateGameState();

    // Clear previous scorePerBall UI
    const scorePerBallDiv = document.querySelector('.scoreper-ball');
    if (scorePerBallDiv) {
        scorePerBallDiv.innerHTML = '';
    }
}
  function undoLastAction() {
    if (undoConfirmed && lastActions.length > 0) {
        const lastAction = lastActions.pop();

        // Restore total score and player stats
        totalScore = lastAction.totalScore;
        striker = { ...lastAction.striker };
        nonStriker = { ...lastAction.nonStriker };
        bowler = { ...lastAction.bowler };

        // Handle byes
        if (lastAction.extraType === "BY") {
            totalScore -= lastAction.run;
            bowler.ballsBowled -= 1;
            striker.ballsFaced -= 1;

            if (lastAction.run % 2 !== 0) {
                changeStrike();
            }
        }

        // Handle wide
        if (lastAction.extraType === "WD") {
            totalScore -= 1; // Adjust for the wide run
            bowler.runsConceded -= 1;

            // Adjust additional runs scored during wide
            if (lastAction.run > 0) {
                totalScore -= lastAction.run;
                bowler.runsConceded -= lastAction.run;

                if (lastAction.run % 2 !== 0) {
                    changeStrike();
                }
            }
        }

        // Handle no-ball
        if (lastAction.extraType === "NB") {
            totalScore -= 1; // Adjust for the no-ball run
            bowler.runsConceded -= 1;

            // Adjust additional runs scored during no-ball
            if (lastAction.run > 0) {
                totalScore -= lastAction.run;
                bowler.runsConceded -= lastAction.run;
                striker.score -= lastAction.run;

                if (lastAction.run % 2 !== 0) {
                    changeStrike();
                }
            }
        }

        // Handle wickets
        if (lastAction.type === 'wicket') {
            totalWickets -= 1; // Decrement total wickets
            bowler.wickets -= 1; // Decrement bowler's wicket count

            // Restore the correct batter
            if (lastAction.isStrikerOut) {
                striker = { ...lastAction.striker };
            } else {
                nonStriker = { ...lastAction.nonStriker };
            }

            if (!lastAction.extraType) {
                currentBall -= 1;
                bowler.ballsBowled -= 1;
            }

            if (lastAction.strikeChanged) {
                changeStrike();
            }
        }

        // Adjust the ball count correctly
        if (!lastAction.extraType) {
            currentBall -= 1;
            if (currentBall < 0 && currentOver > 0) {
                currentOver -= 1;
                currentBall = 5; // Last ball of the previous over
                overCompleted = false;
            }
        }

        removeLastBallFromUI();

        // Recalculate the current run rate and projection
        updateRunRateAndProjection();

        saveCurrentBallData();
        updateGameState();
        undoConfirmed = false;
    }
  }

  function handleRunningRuns(run) {
    if (overCompleted) {
        resetOver();
    }

    lastActions.push({
        run,
        type: 'runningRuns',
        striker: { ...striker },
        nonStriker: { ...nonStriker },
        bowler: { ...bowler },
        currentBall,
        currentOver,
        totalScore
    });

    totalScore += run;
    striker.score += run;
    striker.ballsFaced += 1;
    bowler.ballsBowled += 1;
    bowler.runsConceded += run;
    currentBall += 1;
    updateScorePerBall(run);

    if (run % 2 !== 0) {
        changeStrike();
    }
    if (currentBall >= 6) {
        overCompleted = true;
    }

    updateGameState();
    saveCurrentBallData();  // Call this function to store the updated data in the database
 }



  function updateUI() {
    document.querySelector('.scorz').innerHTML = `${totalScore}/${totalWickets}  <span>(${currentOver}.${currentBall === 0 ? "0" : currentBall}/${matchOvers})</span>`;    document.getElementById('leftBatterName').innerText = striker.name;
    document.getElementById('rightBatterName').innerText = nonStriker.name;

    // Update the scores for both the striker and non-striker based on their object state
    let strikerElement = document.querySelector('.batterz.left .scorz');
    strikerElement.innerHTML = `${striker.score}(${striker.ballsFaced})`;

    let nonStrikerElement = document.querySelector('.batterz.right .scorz');
    nonStrikerElement.innerHTML = `${nonStriker.score}(${nonStriker.ballsFaced})`;

    let oversElement = document.querySelector('.overs');
    if (oversElement) {
        const totalOversBowledByBowler = Math.floor(bowler.ballsBowled / 6);
        const ballsInCurrentOver = bowler.ballsBowled % 6;

        oversElement.innerHTML = `${totalOversBowledByBowler}.${ballsInCurrentOver === 0 ? "0" : ballsInCurrentOver}-0-${bowler.runsConceded}-${bowler.wickets}`;
    }
  }

  function updateScorePerBall(run, extraRun = 0) {
    let label = '';
    let className = '';

    switch (run) {
        case 'WD':
            label = `${extraRun}`;
            className = 'scoreround wide';
            break;
        case 'NB':
            label = `${extraRun}`;
            className = 'scoreround noball';
            break;
        case 'W':
            label = 'WICKET';
            className = 'scoreround wicket';
            break;
        case 0:
            label = 'ZERO';
            className = 'scoreround zero';
            break;
        case 1:
            label = 'ONE';
            className = 'scoreround one';
            break;
        case 2:
            label = 'TWO';
            className = 'scoreround two';
            break;
        case 3:
            label = 'THREE';
            className = 'scoreround three';
            break;
        case 4:
            label = 'FOUR';
            className = 'scoreround four';
            break;
        case 6:
            label = 'SIX';
            className = 'scoreround six';
            break;
        default:
            label = run.toString();
            className = 'scoreround default';
            break;
    }

    const scorePerBallDiv = document.querySelector('.scoreper-ball');
    if (scorePerBallDiv) {
        scorePerBallDiv.innerHTML += `
            <div class="${className}">
                <i>${run}</i>
                <label>${label}</label>
            </div>
        `;
    }
  }

  function removeLastBallFromUI() {
      let scorePerBallDiv = document.querySelector('.scoreper-ball');
      if (scorePerBallDiv) {
          let allBalls = scorePerBallDiv.children;
          if (allBalls.length > 0) {
              scorePerBallDiv.removeChild(allBalls[allBalls.length - 1]);
          }
      }
  }

  function clearUIElements() {
      let scorePerBallDiv = document.querySelector('.scoreper-ball');
      if (scorePerBallDiv) {
          scorePerBallDiv.innerHTML = '';
      }

      let overStatusElement = document.querySelector('.over-status');
      if (overStatusElement) {
          overStatusElement.innerHTML = '';
      }
  }

  document.querySelector('.undo').addEventListener('click', function() {
      $('#unDoModal').modal('show');
  });

  document.querySelector('#unDoModal .btn-primary').addEventListener('click', function() {
      undoConfirmed = true;
      undoLastAction();
      $('#unDoModal').modal('hide');
  });

  document.querySelector('.byes').addEventListener('click', function() {
      $('#byesRunModal').modal('show');
      resetModal('byes');
  });

    document.querySelector('#byesRunModal .btn-primary').addEventListener('click', function() {
      const byesRun = parseInt(document.querySelector('input[name="byes"]:checked').value);
      if (!isNaN(byesRun) && byesRun > 0) {
          handleExtras(byesRun, false, true);
      }
      $('#byesRunModal').modal('hide');
      resetModal('byes');
  });

  document.querySelector('#wideRunModal .btn-primary').addEventListener('click', function() {
      const wideRun = parseInt(document.querySelector('input[name="wideball"]:checked').value);
      handleExtras(wideRun + 1, "WD");
      $('#wideRunModal').modal('hide');
      resetModal('wideball');
  });

   // No-Ball Modal Event Listener
    document.querySelector('#noBallRunModal').addEventListener('show.bs.modal', function () {
        resetModal('noball');
        document.querySelectorAll('input[name="hitOption"]').forEach(input => input.checked = false);
        document.querySelectorAll('input[name="noball"]').forEach(input => input.disabled = true);
        document.querySelector('#submitNoBallRun').disabled = true;
    });

    // Enabling `noball` radio buttons only if `hitOption` is selected
    document.querySelectorAll('input[name="hitOption"]').forEach(input => {
        input.addEventListener('change', function () {
            if (this.checked) {
                document.querySelectorAll('input[name="noball"]').forEach(radio => radio.disabled = false);
                document.querySelector('#submitNoBallRun').disabled = !document.querySelector('input[name="noball"]:checked');
            }
        });
    });

    // Enabling Submit Button once both hit option and no-ball run are selected
    document.querySelectorAll('input[name="noball"]').forEach(input => {
        input.addEventListener('change', function () {
            if (document.querySelector('input[name="hitOption"]:checked')) {
                document.querySelector('#submitNoBallRun').disabled = false;
            }
        });
    });

    // Submit Button Logic for No-Ball
    document.querySelector('#submitNoBallRun').addEventListener('click', function () {
        const noBallRun = parseInt(document.querySelector('input[name="noball"]:checked').value);
        const hitOption = document.querySelector('input[name="hitOption"]:checked').value;

        // Increment total score and bowler's runs for the no-ball itself
        totalScore += 1;
        bowler.runsConceded += 1;

        // Record action details for no-ball
        lastActions.push({
            type: 'noball',
            run: noBallRun,
            extraType: 'NB',
            hitOption: hitOption,
            striker: { ...striker },
            nonStriker: { ...nonStriker },
            bowler: { ...bowler },
            currentBall,
            currentOver,
            totalScore
        });

        // Determine which player is currently active (striker or non-striker)
        let activeBatter = document.querySelector('.batterz.left').classList.contains('active') ? striker : nonStriker;

        // Update runs based on hit option
        if (hitOption === 'hit') {
            // Additional runs for the hit on a no-ball
            totalScore += noBallRun;
            activeBatter.score += noBallRun;
            bowler.runsConceded += noBallRun;

            // Change strike for odd runs
            if (noBallRun % 2 !== 0) {
                changeStrike();
            }
        } else if (hitOption === 'not-hit') {
            // Add only the runs from the no-ball without incrementing striker's score
            totalScore += noBallRun;
            bowler.runsConceded += noBallRun;

            // Change strike for odd runs
            if (noBallRun % 2 !== 0) {
                changeStrike();
            }
        }

        // Update the score display per ball
        updateScorePerBall("NB", noBallRun + 1);

        // Close the modal and reset the inputs
        $('#noBallRunModal').modal('hide');
        resetModal('noball');
        updateGameState();
        saveCurrentBallData(); // Save the ball data with the updated details
    });


  document.querySelector('#fiveMoreModal .btn-primary').addEventListener('click', function() {
    const runningRuns = parseInt(document.querySelector('.inputRunner input').value);
    if (!isNaN(runningRuns) && runningRuns > 0) {
        handleRunningRuns(runningRuns);
    }
    $('#fiveMoreModal').modal('hide');
   });


  document.querySelectorAll('#actions-right #box').forEach(button => {
      button.addEventListener('click', function() {
          let run = parseInt(this.textContent);
          if (!isNaN(run)) {
              updateScore(run);
          } else if (this.classList.contains('wide')) {
              $('#wideRunModal').modal('show');
              resetModal('wideball');
          } else if (this.classList.contains('noball')) {
              $('#noBallRunModal').modal('show');
              resetModal('noball');
          }
      });
  });

  function resetModal(modalName) {
      document.querySelector(`input[name="${modalName}"][value="0"]`).checked = true;
  }

  function logPlayerStats() {
      console.log("--- Current Player Stats ---");
      console.log("strikeId: ", striker.id, "Striker:", striker.name, "- Score:", striker.score, "Balls Faced:", striker.ballsFaced);
      console.log("nonStrikerId", nonStriker.id, "Non-Striker:", nonStriker.name, "- Score:", nonStriker.score, "Balls Faced:", nonStriker.ballsFaced);
      console.log("bowlerId: ", bowler.id, "Bowler:", bowler.name, "- Runs Conceded:", bowler.runsConceded, "Balls Bowled:", bowler.ballsBowled);
      console.log("All Players:");
      allPlayers.forEach(player => {
          console.log(player.name, "- Type:", player.type, "Score:", player.score, "Balls Faced:", player.ballsFaced, "Runs Conceded:", player.runsConceded, "Balls Bowled:", player.ballsBowled);
      });
      console.log("-------------------------");
      if(striker.id && nonStriker.id && bowler.id){
        playersData.strikerId = striker.id;
        playersData.nonStrikerId = nonStriker.id;
        playersData.bowlerId = bowler.id;

        updateMatchPlayers(playersData);
      }
  }

  logPlayerStats();

  function updateLocalStorage() {
    localStorage.setItem('cricketPlayerStats', JSON.stringify(allPlayers));
    localStorage.setItem('cricketBowlerStats', JSON.stringify(allBowlers));
    localStorage.setItem('cricketGameState', JSON.stringify({
        totalScore,
        currentOver,
        currentBall,
        striker: striker.name,
        nonStriker: nonStriker.name,
        bowler: bowler.name
    }));

    const scorePerBallDiv = document.querySelector('.scoreper-ball');
    if (scorePerBallDiv) {
        localStorage.setItem('scorePerBallDetails', scorePerBallDiv.innerHTML);
    }
    localStorage.setItem('overDetails', JSON.stringify(overDetails));

    localStorage.setItem('currentBowler', JSON.stringify({
        name: bowler.name,
        ballsBowled: bowler.ballsBowled,
        runsConceded: bowler.runsConceded,
        wickets: bowler.wickets,
        overs: bowler.overs
    }));
  }

  function loadFromLocalStorage() {
    const savedPlayers = JSON.parse(localStorage.getItem('cricketPlayerStats'));
    const savedBowlers = JSON.parse(localStorage.getItem('cricketBowlerStats'));
    const savedGameState = JSON.parse(localStorage.getItem('cricketGameState'));
    const savedScorePerBallDetails = localStorage.getItem('scorePerBallDetails');
    const savedOverDetails = JSON.parse(localStorage.getItem('overDetails'));
    const savedCurrentBowler = JSON.parse(localStorage.getItem('currentBowler'));

    if (savedPlayers && savedBowlers && savedGameState) {
        allPlayers = savedPlayers.map(p => Object.assign(new Player(p.name, p.type), p));
        allBowlers = savedBowlers.map(p => Object.assign(new Player(p.name, p.type), p));
        totalScore = savedGameState.totalScore;
        currentOver = savedGameState.currentOver;
        currentBall = savedGameState.currentBall;

        striker = allPlayers.find(p => p.name === savedGameState.striker) || striker;
        nonStriker = allPlayers.find(p => p.name === savedGameState.nonStriker) || nonStriker;

        if (savedCurrentBowler) {
            bowler = new Player(savedCurrentBowler.name, 'bowler');
            bowler.ballsBowled = savedCurrentBowler.ballsBowled;
            bowler.runsConceded = savedCurrentBowler.runsConceded;
            bowler.wickets = savedCurrentBowler.wickets;
            bowler.overs = savedCurrentBowler.overs;
            document.getElementById('currentBowlerName').innerText = savedCurrentBowler.name;
        }

        const scorePerBallDiv = document.querySelector('.scoreper-ball');
        if (savedScorePerBallDetails && scorePerBallDiv) {
            scorePerBallDiv.innerHTML = savedScorePerBallDetails;
        }

        if (savedOverDetails) {
            overDetails = savedOverDetails;
        }

        updateUI();
        logPlayerStats();
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    localStorage.clear();
    resetAllSelections();
    clearUIElements();
    updateUI();

    document.getElementById('resetGameBtn').addEventListener('click', function () {
        if (confirm('Are you sure you want to reset the game? All data will be lost.')) {
            localStorage.clear();
            localStorage.removeItem('cricketPlayerStats');
            localStorage.removeItem('cricketGameState');
            localStorage.removeItem('scorePerBallDetails');
            localStorage.removeItem('overDetails');

            totalScore = 0;
            currentOver = 0;
            currentBall = 0;
            lastActions = [];
            overDetails = [];
            overCompleted = false;
            undoConfirmed = false;

            resetAllSelections();

            clearUIElements();
            updateUI();
            logPlayerStats();

            updateButtonStates();
        }
        });
  });

  function updateRunRateAndProjection() {
      const totalRuns = totalScore;
      const oversCompleted = currentOver + (currentBall / 6);

      let crr = 0;
      if (oversCompleted > 0) {
          crr = totalRuns / oversCompleted;
      }

      const projectedScore = crr * matchOvers;
      document.querySelector('.runratez').innerHTML = `CRR: ${crr.toFixed(2)} PROJECTED SCORE: ${Math.round(projectedScore)} (at ${crr.toFixed(2)} RPO)`;
  }

  function updateGameState() {
      updateUI();
      logPlayerStats();
      updateRunRateAndProjection();
      updateLocalStorage();
  }

  document.addEventListener('DOMContentLoaded', loadFromLocalStorage);

  function endInnings() {
      updateLocalStorage();

      console.log("--- Final Innings Stats ---");
      console.log("Total Score:", totalScore);
      console.log("Overs Played:", currentOver + (currentBall / 6));
      logPlayerStats();

      document.querySelectorAll('.actions-right .box').forEach(button => {
          button.disabled = true;
      });
      saveCurrentBallData();
      alert("Innings Completed!");
  }

  document.querySelector('.end-innings').addEventListener('click', endInnings);

  function saveCurrentBallData() {
    const lastAction = lastActions.length > 0 ? lastActions[lastActions.length - 1] : {};
    const extraType = ['WD', 'NB', 'BY'].includes(lastAction.extraType) ? lastAction.extraType : null;
    const wicketType = ['bowled', 'caught', 'caughtBehind', 'runOut', 'lbw', 'stumped', 'retiredHurt', 'hitWicket', 'retiredOut', 'mankad', 'caughtbowled'].includes(lastAction.type)
        ? lastAction.type
        : null;

    let fielderId = null;
    if (['caught', 'caughtBehind', 'stumped', 'runOut'].includes(lastAction.type)) {
        fielderId = lastAction.fielderId || lastAction.wicketKeeper || null;
    } else if (['caughtbowled', 'mankad'].includes(lastAction.type)) {
        fielderId = lastAction.fielderId || null;
    }

    const bowlerId = document.getElementById('currentBowlerName').dataset.playerId;
    const strikerId = document.getElementById('leftBatterName').dataset.playerId;
    const nonStrikerId = document.getElementById('rightBatterName').dataset.playerId;

    if (activePlayer === undefined) {
        activePlayer = { id: strikerId };
    }

    if (nonActivePlayer === undefined) {
        nonActivePlayer = { id: nonStrikerId };
    }

    const ball_run = lastAction.run || (lastAction.other_runs || lastAction.extra_runs) || 0;

    const overs = Math.floor((bowler.ballsBowled - 1) / 6);
    const ballsInCurrentOver = (bowler.ballsBowled - 1) % 6 + 1;

    const total_overs = `${currentOver}.${currentBall === 0 ? "0" : currentBall}`;
    const matchOvers = parseInt('{{ $match->overs }}');

    const ballData = {
        match_id: '{{ $match->id }}',
        batting_team_id: '{{ $match->batting }}',
        bowling_team_id: '{{ $match->bowling }}',
        over_number: currentOver + 1,
        ball_number: currentBall,
        valid_ball_count: bowler.ballsBowled,
        total_runs: ball_run,
        total_score: totalScore,
        is_one: lastAction.run === 1,
        is_two: lastAction.run === 2,
        is_three: lastAction.run === 3,
        is_four: lastAction.run === 4,
        is_five: lastAction.run === 5,
        is_six: lastAction.run === 6,
        other_runs: lastAction.type === 'runningRuns' ? lastAction.run : 0,
        bye_runs: lastAction.extraType === 'BY' ? lastAction.run : 0,
        extra_runs: extraType ? lastAction.run : 0,
        wide_runs: extraType === 'WD' ? lastAction.run : 0,
        no_ball_runs: extraType === 'NB' ? lastAction.run : 0,
        bowler_id: bowler.id,
        striker_id: activePlayer.id,
        non_striker_id: nonActivePlayer.id,
        fielder_id: fielderId,
        total_overs: total_overs,
        is_over_completed: currentBall === 6,
        extra_type: extraType,
        is_wicket: wicketType && wicketType !== 'retiredHurt' ? 1 : 0,
        wicket_type: wicketType,
        current_run_rate: (totalScore / (currentOver + (currentBall / 6))).toFixed(2),
        projected_score: ((totalScore / (currentOver + (currentBall / 6))) * matchOvers).toFixed(0)
    };

    let firstinningscore = {{ $matchScore->total_runs ?? 'null' }};
    let maxscores = {{ $maxScore ?? 'null' }};
    let secondinningscore = ballData.total_score;
    let secondinning = {{($matchScore->is_first_inning) ?? 'null'}}

    console.log('firstinningscore' , firstinningscore)
    console.log('secondinningscore' , secondinningscore)

    if (total_overs == matchOvers|| totalWickets == 2) {
        $("#switchInningsModal").modal('show');

    } else if (secondinning == 1) {
        console.log('secondinningisnide secind' , secondinning)
        if (secondinningscore > firstinningscore || totalWickets == 2) {
            console.log('second inning win')
            $("#InningsOverModal").modal('show');
        }
     }

    $.ajax({
        url: '/save-ball-data',
        type: 'POST',
        data: ballData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            console.log(response.fulldata);
            console.log('Ball data saved successfully:', response);
            socket.emit('set-present-match-data', matchId);
            socket.emit('update-match-score', matchId);
        },
        error: function(xhr, status, error) {
            console.error('Error saving ball data:', error);
        }
    });
  }

    //NOTE: Don't remove, It's for innings
    //  socket.on('get-match-status', (matchId) => {})
    //  socket.on('set-innings-completed', (data) => {})

    function checkAndUpdateMatchPlayers() {
        const { strikerId, nonStrikerId, bowlerId, currentInnings } = playersData;
        // Check if all values are present (not null)
        console.warn('playersData => ', playersData);
        if (strikerId && nonStrikerId && bowlerId && currentInnings) {
            // Call updateMatchPlayers only when all values are set
            updateMatchPlayers(playersData);
        }
    }
    function updateMatchPlayers(data) {
        $.ajax({
            url: `${WEB}/updateMatchPlayers`,
            method: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                console.log('players were updated => ', res)
            },
            error: function(xhr, status, error) {
                console.log('Error saving ball data:', error);
            }
        })
    }

    function matchOverButton() {
        const matchId = '{{ $match->id }}'; // Assuming match ID is available in the template
        const winningTeamId = '{{ $match->batting }}'; // Assuming the current batting team is the winner

        $.ajax({
            url: '/admin/match-over', // Endpoint to handle match completion
            type: 'POST',
            data: {
                match_id: matchId,
                team_id: winningTeamId,
                _token: $('meta[name="csrf-token"]').attr('content') // CSRF token for security
            },
            success: function (response) {
                console.log('Match details updated successfully:', response);
                alert('Match is completed. Redirecting...');
                // Redirect to another page, e.g., match summary or dashboard
                window.location.href = `/dashboard`;
            },
            error: function (xhr, status, error) {
                console.error('Error updating match details:', error);
                alert('Failed to update match details.');
            }
        });
    }


    function startSecondInnings(secondInningsData) {
        const matchId = '{{ $match->id }}';

        $.ajax({
            url: `/run-scorer/${matchId}`,
            type: 'GET',
            data: { secondinnings: secondInningsData },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                console.log('Second innings started successfully:', response);
                $('#switchInningsModal').modal('hide');
                location.reload()
            },
            error: function (xhr, status, error) {
                console.error('Error starting second innings:', error);
            }
        });
    }

</script>

@endsection
