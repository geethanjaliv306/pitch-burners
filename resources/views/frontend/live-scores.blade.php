@extends('layouts.app')

@section('content')
  {{-- <body class="addnewplayer-body"> --}}

    <section class="pitch-burners-banner">
      <img class="pitch-burners-banner-figure" src="{{ asset('uploads/images/drawing-baseball-player-with-bat-word-cricket-it.jpg')}}" />
      <div class="container h-100">
        <div class="row h-100">
          <div class="col-12 h-100 d-flex align-items-center">
            <div class="pitch-burners-info">
              <i class="icon"><img width="200" src="{{ asset('uploads/images/logo.png')}}" /></i>
              <h4>Pitch Burners Season 5, 2024</h4>
              <ul>
                <li><label class="ttencricket">T10 Cricket</label></li>
                <li>Feb 03, 2025 - Mar 10, 2025</li>
                <li>94 Teams</li>
                <li>134 Matches</li>
              </ul> 
            </div>
          </div>
        </div>
      </div>
    </section>
    <main class="main-wrapper-start pitch-burners-home d-none">
        <div class="container">
            <div class="row">
           <div class="col-12">
                <div class="fixtures-results-wrapper">
                  <div class="fixtures-results-box">
                      <div class="date-info">
                        <p class="day">Thursday</p>
                        <p class="date">08</p>
                        <p class="month">August</p>
                      </div>
                      <div class="match-info">
                        <div class="time-ground-details"><span class="time">7:00 AM</span> Don Bosco School of Excellence, Vellakinar</div>
                        <div class="match-vs-team">
                          <div class="teamA d-flex align-items-center">
                            <i><img src="{{ asset('uploads/images/dsignzmedia.ico')}}" /></i>
                            <div>
                            <h5>Dsignz Media</h5>
                            <div class="score won">
                              <p>135/1</p>
                              <span>(20.0 ov)</span>
                            </div>
                          </div>
                          </div>
                          <div class="vs"><span>VS</span></div>
                          <div class="teamB d-flex align-items-center">
                            <i><img src="{{ asset('uploads/images/erdster.png')}}" /></i>
                            <div>
                            <h5>Erdster</h5>
                            <div class="score loss">
                              <p>90/10</p>
                              <span>(18.2 ov)</span>
                            </div>
                          </div>
                          </div>
                        </div>
                        <div class="won-loss-details">Dsignzmedia Won by 7 Wickets</div>
                      </div>
                      <div class="action-info">
                        <a class="match-center" href="match-centre.html"><img width="25" height="25" src="{{ asset('uploads/images/cricket.svg')}}" />Match Centre</a>
                      </div>
                  </div>
                  <div class="fixtures-results-box">
                    <div class="date-info">
                      <p class="day">Thursday</p>
                      <p class="date">08</p>
                      <p class="month">August</p>
                    </div>
                    <div class="match-info">
                      <div class="time-ground-details"><span class="time">7:00 AM</span> Don Bosco School of Excellence, Vellakinar</div>
                      <div class="match-vs-team">
                        <div class="teamA d-flex align-items-center">
                          <i><img src="images/dsignzmedia.ico" /></i>
                          <div>
                          <h5>Accenture</h5>
                          <div class="score won">
                            <p>90/1</p>
                            <span>(20.0 ov)</span>
                          </div>
                        </div>
                        </div>
                        <div class="vs"><span>VS</span></div>
                        <div class="teamB d-flex align-items-center">
                          <i><img src="images/erdster.png" /></i>
                        <div>
                          <h5>Aceess Healthcare</h5>
                          <div class="score loss">
                            <p>82/10</p>
                            <span>(18.2 ov)</span>
                          </div>
                        </div>
                        </div>
                      </div>
                      <div class="won-loss-details">Accenture Won by 7 Wickets</div>
                    </div>
                    <div class="action-info">
                      <a class="match-center" href="match-centre.html"><img width="25" height="25" src="images/cricket.svg" />Match Centre</a>
                    </div>
                </div>
                <div class="fixtures-results-box">
                  <div class="date-info">
                    <p class="day">Thursday</p>
                    <p class="date">08</p>
                    <p class="month">August</p>
                  </div>
                  <div class="match-info">
                    <div class="time-ground-details"><span class="time">7:00 AM</span> Don Bosco School of Excellence, Vellakinar</div>
                    <div class="match-vs-team">
                      <div class="teamA d-flex align-items-center">
                        <i><img src="images/dsignzmedia.ico" /></i>
                      <div>
                        <h5>Dsignz Media</h5>
                        <div class="score won">
                          <p>135/1</p>
                          <span>(20.0 ov)</span>
                        </div>
                      </div>
                      </div>
                      <div class="vs"><span>VS</span></div>
                      <div class="teamB d-flex align-items-center">
                        <i><img src="images/erdster.png" /></i>
                      <div>
                        <h5>Erdster</h5>
                        <div class="score loss">
                          <p>90/10</p>
                          <span>(18.2 ov)</span>
                        </div>
                      </div>
                      </div>
                    </div>
                    <div class="won-loss-details">Dsignzmedia Won by 7 Wickets</div>
                  </div>
                  <div class="action-info">
                    <a class="match-center" href="match-centre.html"><img width="25" height="25" src="images/cricket.svg" />Match Centre</a>
                  </div>
              </div>
              <div class="fixtures-results-box">
                <div class="date-info">
                  <p class="day">Thursday</p>
                  <p class="date">08</p>
                  <p class="month">August</p>
                </div>
                <div class="match-info">
                  <div class="time-ground-details"><span class="time">7:00 AM</span> Don Bosco School of Excellence, Vellakinar</div>
                  <div class="match-vs-team">
                    <div class="teamA d-flex align-items-center">
                      <i><img src="images/dsignzmedia.ico" /></i>
                    <div>
                      <h5>Dsignz Media</h5>
                      <div class="score won">
                        <p>135/1</p>
                        <span>(20.0 ov)</span>
                      </div>
                    </div>
                    </div>
                    <div class="vs"><span>VS</span></div>
                    <div class="teamB d-flex align-items-center">
                      <i><img src="images/erdster.png" /></i>
                    <div>
                      <h5>Erdster</h5>
                      <div class="score loss">
                        <p>90/10</p>
                        <span>(18.2 ov)</span>
                      </div>
                    </div>
                    </div>
                  </div>
                  <div class="won-loss-details">Dsignzmedia Won by 7 Wickets</div>
                </div>
                <div class="action-info">
                  <a class="match-center" href="match-centre.html"><img width="25" height="25" src="images/cricket.svg" />Match Centre</a>
                </div>
            </div>
            <div class="fixtures-results-box">
              <div class="date-info">
                <p class="day">Thursday</p>
                <p class="date">08</p>
                <p class="month">August</p>
              </div>
              <div class="match-info">
                <div class="time-ground-details"><span class="time">7:00 AM</span> Don Bosco School of Excellence, Vellakinar</div>
                <div class="match-vs-team">
                  <div class="teamA d-flex align-items-center">
                    <i><img src="images/dsignzmedia.ico" /></i>
                    <h5>Dsignz Media</h5>
                  </div>
                  <div class="vs"><span>VS</span></div>
                  <div class="teamB d-flex align-items-center">
                    <i><img src="images/erdster.png" /></i>
                    <h5>Erdster</h5>
                  </div>
                </div>
              </div>
              <div class="action-info">
                <a class="match-center" href="match-centre.html"><img width="25" height="25" src="images/cricket.svg" />Match Centre</a>
              </div>
          </div>
                </div>
           </div>
        </div>
        </div>
    </main>
    <section class="live-scores-wrap pitch-burners-home">
      <div class="pitch-burners-home-filter">
        <div class="container h-100">
          <div class="row h-100">
            <div class="col-12 h-100 d-flex align-items-center">
              <ul>
                <li>
                  <a class="active" href="javascript:;">Live & Upcoming</a>
                </li>
                <li>
                  <a href="javascript:;">Completed</a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="livescore-box">
              <div class="livescore-box-item">
                <div class="top">
                  <div class="live">Live</div>
                  <div class="group">Group A</div>
                  <div class="ground">Bharathiyar University, Coimbatore</div>
                </div>
                <div class="centre">
                  <div class="team indicater">
                    <div class="left">
                      <i><img width="25" height="25" src="images/dsignzmedia.ico" /></i>
                      Dsignzmedia
                    </div>
                    <div class="right">
                      <div class="overs">(10)</div>
                      <div class="scores">116/1</div>
                    </div>
                  </div>
                  <div class="team">
                    <div class="left">
                      <i><img width="25" height="25" src="images/erdster.png" /></i>
                      Cognizant
                    </div>
                    <div class="right">
                      <div class="overs">(9.2)</div>
                      <div class="scores">126/1</div>
                    </div>
                  </div>
                </div>
                <div class="bottom">
                  <p>Dsignzmedia won the toss and choose the field first.</p>
                </div>
              </div>
              <div class="livescore-box-item">
                <div class="top">
                  <div class="completed">Completed</div>
                  <div class="group">Group A</div>
                  <div class="ground">Bharathiyar University, Coimbatore</div>
                </div>
                <div class="centre">
                  <div class="team indicater">
                    <div class="left">
                      <i><img width="25" height="25" src="images/dsignzmedia.ico" /></i>
                      Dsignzmedia
                    </div>
                    <div class="right">
                      <div class="overs">(10)</div>
                      <div class="scores">116/1</div>
                    </div>
                  </div>
                  <div class="team">
                    <div class="left">
                      <i><img width="25" height="25" src="images/erdster.png" /></i>
                      Cognizant
                    </div>
                    <div class="right">
                      <div class="overs">(9.2)</div>
                      <div class="scores">126/1</div>
                    </div>
                  </div>
                </div>
                <div class="bottom">
                  <p>Dsignzmedia won by 7 wickets</p>
                </div>
              </div>
              <div class="livescore-box-item">
                <div class="top">
                  <div class="live">Live</div>
                  <div class="group">Group A</div>
                  <div class="ground">Bharathiyar University, Coimbatore</div>
                </div>
                <div class="centre">
                  <div class="team indicater">
                    <div class="left">
                      <i><img width="25" height="25" src="images/dsignzmedia.ico" /></i>
                      Dsignzmedia
                    </div>
                    <div class="right">
                      <div class="overs">(10)</div>
                      <div class="scores">116/1</div>
                    </div>
                  </div>
                  <div class="team">
                    <div class="left">
                      <i><img width="25" height="25" src="images/erdster.png" /></i>
                      Cognizant
                    </div>
                    <div class="right">
                      <div class="overs">(9.2)</div>
                      <div class="scores">126/1</div>
                    </div>
                  </div>
                </div>
                <div class="bottom">
                  <p>Dsignzmedia won the toss and choose the field first.</p>
                </div>
              </div>
              <div class="livescore-box-item">
                <div class="top">
                  <div class="completed">Completed</div>
                  <div class="group">Group A</div>
                  <div class="ground">Bharathiyar University, Coimbatore</div>
                </div>
                <div class="centre">
                  <div class="team indicater">
                    <div class="left">
                      <i><img width="25" height="25" src="images/dsignzmedia.ico" /></i>
                      Dsignzmedia
                    </div>
                    <div class="right">
                      <div class="overs">(10)</div>
                      <div class="scores">116/1</div>
                    </div>
                  </div>
                  <div class="team">
                    <div class="left">
                      <i><img width="25" height="25" src="images/erdster.png" /></i>
                      Cognizant
                    </div>
                    <div class="right">
                      <div class="overs">(9.2)</div>
                      <div class="scores">126/1</div>
                    </div>
                  </div>
                </div>
                <div class="bottom">
                  <p>Dsignzmedia won by 7 wickets</p>
                </div>
              </div>
              <div class="livescore-box-item">
                <div class="top">
                  <div class="live">Live</div>
                  <div class="group">Group A</div>
                  <div class="ground">Bharathiyar University, Coimbatore</div>
                </div>
                <div class="centre">
                  <div class="team indicater">
                    <div class="left">
                      <i><img width="25" height="25" src="images/dsignzmedia.ico" /></i>
                      Dsignzmedia
                    </div>
                    <div class="right">
                      <div class="overs">(10)</div>
                      <div class="scores">116/1</div>
                    </div>
                  </div>
                  <div class="team">
                    <div class="left">
                      <i><img width="25" height="25" src="images/erdster.png" /></i>
                      Cognizant
                    </div>
                    <div class="right">
                      <div class="overs">(9.2)</div>
                      <div class="scores">126/1</div>
                    </div>
                  </div>
                </div>
                <div class="bottom">
                  <p>Dsignzmedia won the toss and choose the field first.</p>
                </div>
              </div>
              <div class="livescore-box-item">
                <div class="top">
                  <div class="completed">Completed</div>
                  <div class="group">Group A</div>
                  <div class="ground">Bharathiyar University, Coimbatore</div>
                </div>
                <div class="centre">
                  <div class="team indicater">
                    <div class="left">
                      <i><img width="25" height="25" src="images/dsignzmedia.ico" /></i>
                      Dsignzmedia
                    </div>
                    <div class="right">
                      <div class="overs">(10)</div>
                      <div class="scores">116/1</div>
                    </div>
                  </div>
                  <div class="team">
                    <div class="left">
                      <i><img width="25" height="25" src="images/erdster.png" /></i>
                      Cognizant
                    </div>
                    <div class="right">
                      <div class="overs">(9.2)</div>
                      <div class="scores">126/1</div>
                    </div>
                  </div>
                </div>
                <div class="bottom">
                  <p>Dsignzmedia won by 7 wickets</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
   

  </body>
@endsection