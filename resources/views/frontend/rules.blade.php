@extends('layouts.app')
@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Saira:wght@300;400;600&display=swap');

    :root {
        --primary-color: #008F7A;
        --secondary-color: #23004b;
        --accent-color: #F2C01F;
        --deep-purple: rgba(58, 21, 120, 1);
    }

    .our-sponsers {
        display: none;
    }

    .partners-info {
        background: linear-gradient(135deg, var(--secondary-color) 0%, var(--deep-purple) 100%);
        min-height: calc(100vh - 149px);
        color: #fff;
        padding: 50px 0;
    }

    .partners-wrap {
        background-color: rgba(255, 255, 255, 0.95);
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        padding: 40px 20px !important;
    }

    h3, h5 {
        font-family: "Saira", Arial, Helvetica, sans-serif !important;
        color: var(--secondary-color);
        border-bottom: 3px solid var(--primary-color);
        padding-bottom: 10px;
        margin-bottom: 20px;
        font-weight: 600;
    }

    .terms-ul {
        padding-left: 40px;
    }

    .terms-ul li {
        position: relative;
        font-size: 18px !important;
        font-family: "Saira", Arial, Helvetica, sans-serif !important;
        line-height: 1.8 !important;
        margin-bottom: 15px;
        color: #333;
        list-style-type: none;
        padding-left: 25px;
    }

    .terms-ul li::before {
        content: '\2022';
        color: var(--primary-color);
        font-weight: bold;
        position: absolute;
        left: 0;
        font-size: 25px;
        top:-6px
    }

    .table-responsive {
        overflow-x: auto;
        margin: 20px 0;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }

    .table {
        width: 100%;
        margin-bottom: 0;
        background-color: white;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-dark {
        background-color: var(--secondary-color);
    }

    .table th, .table td {
        white-space: nowrap;
        padding: 12px;
        border: 1px solid #ddd;
    }

    tr {
        border-width: unset !important;
    }

    .table-striped tbody tr:nth-child(even) {
        background-color: rgba(0,143,122,0.1);
    }

    a {
        color: var(--primary-color);
        text-decoration: none;
        transition: color 0.3s ease;
    }

    a:hover {
        color: var(--accent-color);
    }

    .scroll-to-top {
      position: fixed;
      bottom: 25px;
      right: 25px;
      background: linear-gradient(242.58deg, #8542D8 0.9%, #9D3ADF 21.58%, #8542D8 64.98%, #9D3ADF 101.25%);
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s ease;
      z-index: 1000;
      border: none;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    .scroll-to-top.visible {
      opacity: 1;
      visibility: visible;
    }
    .scroll-to-top:hover {
      background-color: #FF567C;
      transform: translateY(-2px);
    }
    .scroll-to-top i {
      color:white;
    }
  
    .download-section {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      border-bottom: 3px solid var(--primary-color);
      padding-bottom: 6px
  }

    .download-pdf-btn {
        font-size: 16px !important;
        font-family: "Saira", Arial, Helvetica, sans-serif !important;
        background: var(--primary-color);
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        display: inline-block;
        transition: background 0.3s ease;
    }
    .download-pdf-btn:hover {
        background: var(--secondary-color);
        color: white;
    }

    @media (max-width: 768px) {
        .partners-info{
          padding: 12px 0;
        }

        .partners-wrap {
            padding: 16px !important;
            margin: 0px;
            border-radius: 6px;
        }

        .terms-ul li {
            font-size: 16px !important;
            line-height: 1.6 !important;
        }

        .terms-ul {
            padding-left: 0px;
        }
      
        .download-section {
          flex-direction: column;
          align-items: flex-start;
          gap: 10px;
      	}

        .download-pdf-btn {
          width: 100%;
          text-align: center;
          justify-content: center;
        }
    }
    .tab-container {
    margin-bottom: 30px;
}

.tab-nav {
    display: flex;
    border-bottom: 3px solid var(--primary-color);
    margin-bottom: 0;
    background-color: transparent;
}

.tab-btn {
    background: none;
    border: none;
    padding: 15px 30px;
    font-family: "Saira", Arial, Helvetica, sans-serif !important;
    font-size: 18px;
    font-weight: 600;
    color: var(--secondary-color);
    cursor: pointer;
    transition: all 0.3s ease;
    border-bottom: 3px solid transparent;
    position: relative;
    top: 3px;
}

.tab-btn.active {
    background: var(--primary-color);
    color: white;
    border-bottom: 3px solid var(--primary-color);
}

.tab-btn:hover:not(.active) {
    background: rgba(0, 143, 122, 0.1);
    color: var(--primary-color);
}

.tab-content {
    display: none;
    animation: fadeIn 0.5s ease-in-out;
}

.tab-content.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 768px) {
    .tab-btn {
        padding: 12px 20px;
        font-size: 16px;
        top: 0;
        border-bottom: 1px solid #ddd;
      	flex:1;
        white-space: nowrap;
    }
    .tab-nav{
        overflow-x: scroll;
    }
    
    .tab-btn.active {
        border-bottom: 1px solid var(--primary-color);
    }
  
    .hide-mobile {
      display: none; 
    }
}
</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<section class="partners-info">
    <div class="container">
        <div class="partners-wrap">
          <div class="row">
            <div class="col-12">
               <div class="text-left mb-2">
                    <!-- <div class="download-section">
                  <h3 style="border: none;margin: 0;padding: 0;">PBCCL Tournament Rules:</h3>
                  <a href="{{ asset('uploads/PBCCL_Tournament_Rules.pdf') }}" class="download-pdf-btn" download>
                    <i class="fas fa-file-pdf me-2"></i>Download PDF
                  </a>
                </div> -->
                <div class="tab-container">
                 
                  <div class="download-section">
                    <h3 style="border: none;margin: 0;padding: 0;">PBCCL Tournament Rules:</h3>
                    <a id="red-pdf-btn" 
                       href="{{ asset('uploads/PBCCL_Tournament_Rules.pdf') }}" 
                       class="download-pdf-btn" 
                       download>
                      <i class="fas fa-file-pdf me-2"></i>Red Tennis Ball PDF
                    </a>
                    <a id="white-pdf-btn" 
                       href="{{ asset('uploads/PBCCL_White_Ball_Tournament_Rules.pdf') }}" 
                       class="download-pdf-btn" 
                       download 
                       style="display:none;">
                      <i class="fas fa-file-pdf me-2"></i>White Ball PDF
                    </a>
                    <a id="box-pdf-btn" 
                       href="{{ asset('uploads/PBCCL_Box_Cricket_Tournament_Rules.pdf') }}" 
                       class="download-pdf-btn" 
                       download 
                       style="display:none;">
                      <i class="fas fa-file-pdf me-2"></i>Box Cricket PDF
                    </a>
                  </div>


    
                  <div class="tab-nav">
                      <button class="tab-btn active" onclick="switchTab('red-ball', event)">Red Tennis Ball <span class="hide-mobile">Tournament</span></button>
                      <button class="tab-btn" onclick="switchTab('white-ball', event)">White Ball <span class="hide-mobile">Tournament</span></button>
                      <button class="tab-btn" onclick="switchTab('box-cricket', event)">Box Cricket <span class="hide-mobile">Tournament</span></button>
                  </div>
              </div>
			</div>
      <div id="red-ball" class="tab-content active">
                      <ul class="terms-ul">
                <li>Each team can have a maximum of <strong>20 members. No changes can be made after the submission of the squad.</strong></li>
                <li>Teams will be divided into groups (4 teams per group). The preliminary matches will be on a league basis. A team will play with every other team in the group. 2 points will be awarded to the winners. The top 2 teams from each group will move to the knockout stage. If two teams have the same points at the end of the league matches, the net run rate will be considered for selection to the knockout stage.</li>
                <li>The entrance fee for the tournament is <strong>Rs.6,000/-.</strong></li>
                <li>The original company ID card along with one Photo-ID proof (Driving License, Aadhar, PAN – if required) for the playing eleven and the substitute will be verified before the start of the match.</li>
                <li><strong>Bonafide should be shared wherever applicable. The Bonafide format should include the following details printed on the company letterhead, attested by the company HR, and shared via email from HR to <a href="mailto:cricket@pitchburners.com">cricket@pitchburners.com</a>. The same should be uploaded during squad submission.</strong></li>
                <li>
                    <strong>Bonafide Format:</strong>
                    <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>S.No</th>
                                <th>Employee Number</th>
                                <th>Employee Name</th>
                                <th>Aadhaar Number</th>
                                <th>UAN Number</th>
                                <th>Photo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>12345</td>
                                <td>Ramesh</td>
                                <td>AXDP2345</td>
                                <td>12345678</td>
                                <td>Photo of the player to be fixed and attested by Employer</td>
                            </tr>
                        </tbody>
                    </table>
                  </div>
                </li>
                <li>Players' names submitted for the tournament should match exactly with the name on their Company ID card.</li>
                <li>Hard red tennis balls will be used for the tournament. A new ball will be provided for each innings. If damaged, used balls will be replaced.</li>
                <li>Balls will not be provided for practice.</li>
              <li>Request to postpone the match is <strong>strictly not entertained</strong></li>
              <li>The team should report 30 minutes prior to the scheduled match time to sign the register and for briefing.
            In case a team does not report, points will be awarded to the team that has reported. Net run rate will be 0
            for both the teams in this scenario.</li>
              <li>No LBWs and Leg Byes. All other ICC rules will be applied in the tournament.
              </li>
            <li> <strong> A minimum of four fielders should be placed within the inner circle throughout the innings. At the
            instant of a delivery, not more 2 fielders can be placed behind the wicket.
              </strong></li>
            <li><strong>At the instance of delivery there not be more than 5 fielders on leg side.(Excluding Bowler)</strong></li>
            <li> Every player of a team must adhere to a fixed dress code. Players won’t be allowed to play if they are
            dressed in shorts and/ or sleeveless T-shirt. Shoe is compulsory.</li>
            <li><strong>Substitute is permitted to field in case of on-field injury to player with the discretion of the umpire
            as per standard rules. The substitute cannot bowl/bat.</strong></li>
            <li>To start the match, team should have minimum of 8 players. No team will be allowed to field a side of
            less than eight players. During play, if any team falls short of eight players the match will be awarded to
            the opponent.</li>
            <li> Team cannot start the match with a substitute.</li>
            <li>If the stumps have been knocked off already, the stumps must be uprooted to run out a batsman.</li>
            <li> <strong>Before the start of each game, Captains of both teams shall be made aware of all the rules. Also,
            they will have a chance to ask the Umpires about any clarifications that they may need. Once the
            match starts, no protests on the rules will be entertained during the match or after the match. If
            any team walks out of the ground on protest during the playtime, the team will be disqualified
            from the tournament.</strong></li>
            <li>In case of a Tie, Super over will be bowled. In case of Tie in Super over, another Super Over will be
            granted until the game ends without a Tie.<strong>(As per the ICC Rules)</strong></li>
            <li><strong>In case of any unavoidable circumstances, if First Batting is not completed, then the match will be
            conducted again, during the second innings, if the first 2 overs are complete, then D/L method will
            be implemented to decide the winner.</strong></li>
            <li> Umpires will decide upon the issues related to chucking on match. Umpires’ decision will be final. <strong>(A
            ball is fairly delivered in respect of the arm if, once the bowler’s arm has reached the level of the
            shoulder in the delivery swing, the elbow joint is not straightened partially or completely from that
            instant until the ball has left the hand. This definition shall not debar a bowler from flexing or
            rotating the wrist in the delivery swing.)</strong></li>
            <li><strong>If any team misbehaves or abuses the Umpires inside or outside the field during/ post the match,
            the team will be disqualified from the tournament.</strong></li>
            <li>If a player is found using foul language on the field during the play/ off the field to his player or to the
            opponent or misbehaving with the Umpires or abusing to the Umpires that player will be suspended from
            playing the current match or may be banned for the next match or from the tournament. There will be no
            replacement for such suspension.</li>
            <li> Sledging strictly not allowed on and off the field.</li>
            <li> Except the playing members or Umpire, none shall enter the field during the match. The decision of the
            Umpire will be final in all aspects during the match. No protest will be entertained against Umpire’s
            decision.</li>
            <li> The Committee may at their discretion, can postpone/alter the time of a match. In that case, the team will
            be duly notified on the change before the match.</li>
              <li>  Teams scheduled to play a dead rubber match, must not refrain from playing the match, failing which the
            company will not receive invitations for future seasons of PBCCL.</li>
            <li> Committee is not responsible for player injuries or adverse effects to player health during the
            tournament. However, first aid kits will be available at ground.</li>
            <li>  A player can represent for a team in the tournament. If it comes to the Committee’s notice that a team has
            a player who has played for another team in the tournament, then both the team will be disqualified.</li>
            <li> If a player is found to be not an employee of the company he is representing in this tournament, the entire
            team will be disqualified at any level of the tournament.</li>
            <li>  As the matches are being played inside a college campus, code of conduct must be maintained on and off
            the field. Smoking tobacco, consumption of alcohol and any sort of rude behavior are strictly prohibited.
            If it is brought to the notice of the Committee of any kind of misbehavior of an individual or a team by
            the College security/ Administration, the team will be disqualified with immediate effect. The college
            officials can take any sort of legal action on the team or on the individual who breaks the code of
            conduct.</li>
            <li>  If any rules are not covered, the decision of the tournament committee will be final and binding upon all
            concerned as per the Law of cricket.</li>
            <li> All official confirmation will be communicated through <a href="mailto:cricket@pitchburners.com">cricket@pitchburners.com</a> or the WhatsApp
            group which is created for the tournament. </li>
            </ul>
            <div class="text-left mt-4 mb-2">
                <h3>Batting & Bowling Rules:</h3>
            </div>
                        <ul class="terms-ul">
                        <li><strong> Un-sized bat will not be permitted for the tournament. No Fiber/Kerala bat will be allowed.
            Scoop and Back hole bat will not be allowed.</strong></li>
            <li> <strong>The overall length of the bat, when the lower portion of the handle is inserted, shall not be more
            than 38 in/96.52 cm.</strong></li>
            <li> <strong>The blade of the bat shall not exceed the following dimensions:
            Width: 4.25in / 10.8 cm
            Depth: 2.64in / 6.7 cm</strong></li>
            <li>A bowler can bowl maximum of only 2 overs in a match which means minimum of 5 bowlers should be
            used per side in a match</li>
            <li>A free hit will be awarded for all No ball. If the bowler delivers a beamer, he will be given a warning.
            Another beamer will result in the bowler’s disqualification. The severity will vary based on the threat to
            the batsman caused by the beamer bowled</li>
            <li> If a bowler/ wicket keeper hits the stumps accidently while delivering the ball, it will be considered as a
            No-Ball and a free hit will be granted.</li>
            <li><strong>Mankding will be considered as run out.</strong></li>
            <li> Wicket Keeper can bowl immediate next over. </li>
                          </ul>
      </div>
      <div id="white-ball" class="tab-content">
        <ul class="terms-ul">
            <li>Each team can have maximum of <strong>20 members. No changes can be made after the submission of the squad.</strong></li>
            <li>Teams will be divided into groups. The preliminary matches will be league basis. A team will play with every other team in the group. 2 points will be awarded for the winners. The top 2 teams from each group will move to the knock out stage. If in case two teams has same points at the end of league matches, net run rate will be considered for selection to the knock out stage.</li>
            <li>The entrance fee for the tournament is <strong>Rs.7,500/-.</strong></li>
            <li>The original company ID card along with one Photo-ID proof (Driving License/ Aadhar / PAN- if required) for the playing eleven and for the substitute will be verified before the start of the match.</li>
            <li><strong>Bonafide should be shared wherever is applicable. The Bonafide format should include the following details printed in the company letterhead, attested by the company HR, and shared via email from HR to <a href="mailto:cricket@pitchburners.com">cricket@pitchburners.com</a>. The same should be uploaded during squad submission.</strong></li>
            <li>
                <strong>Bonafide Format:</strong>
                <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>S.No</th>
                            <th>Employee Number</th>
                            <th>Employee Name</th>
                            <th>Aadhaar Number</th>
                            <th>UAN Number</th>
                            <th>Photo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>12345</td>
                            <td>Ramesh</td>
                            <td>AXDP2345</td>
                            <td>12345678</td>
                            <td>Photo of the player to be fixed and attested by Employer</td>
                        </tr>
                    </tbody>
                </table>
              </div>
            </li>
            <li>Players' names submitted for the tournament should match exactly with the name on their Company ID card.</li>
            <li>SF true test ball will be used for the tournament. New ball will be provided for each innings.</li>
            <li>Balls will not be provided for practice.</li>
            <li>Request to postpone the match is <strong>strictly not entertained.</strong></li>
            <li>The team should report 30 minutes prior to the scheduled match time to sign the register and for briefing. In case a team does not report, points will be awarded to the team that has reported. Net run rate will be 0 for both the teams in this scenario.</li>
            <li>All ICC rules will be applied in the tournament.</li>
            <li><strong>During the Powerplay overs (first 6 overs in a 20-over match, first 7 overs in a 25-over match): A maximum of 2 fielders can be placed outside the 30-yard circle (the inner circle). At the time the bowler delivers the ball, not more than 2 fielders (other than the wicketkeeper) are allowed behind square on the leg side.</strong></li>
            <li><strong>At the instance of delivery there not be more than 5 fielders on leg side.(Excluding Bowler)</strong></li>
            <li>Every player of a team must adhere to a fixed dress code. Players won't be allowed to play if they are dressed in shorts and/ or sleeveless T-shirt. Shoe is compulsory.</li>
            <li><strong>Substitute is permitted to field in case of on-field injury to player with the discretion of the umpire as per standard rules. The substitute cannot bowl/bat.</strong></li>
            <li>To start the match, team should have minimum of 8 players. No team will be allowed to field a side of less than eight players. During play, if any team falls short of eight players the match will be awarded to the opponent.</li>
            <li>Team cannot start the match with a substitute.</li>
            <li>If the stumps have been knocked off already, the stumps must be uprooted to run out a batsman.</li>
            <li><strong>Before the start of each game, Captains of both teams shall be made aware of all the rules. Also, they will have a chance to ask the Umpires about any clarifications that they may need. Once the match starts, no protests on the rules will be entertained during the match or after the match. If any team walks out of the ground on protest during the playtime, the team will be disqualified from the tournament.</strong></li>
            <li>In case of a Tie, Super over will be bowled. In case of Tie in Super over, another Super Over will be granted until the game ends without a Tie.<strong>(As per the ICC Rules)</strong></li>
            <li><strong>In case of any unavoidable circumstances, if First Batting is not completed, then the match will be conducted again, during the second innings, if the first 5 overs are complete, then D/L method will be implemented to decide the winner.</strong></li>
            <li>Umpires will decide upon the issues related to chucking on match. Umpires' decision will be final. <strong>(A ball is fairly delivered in respect of the arm if, once the bowler's arm has reached the level of the shoulder in the delivery swing, the elbow joint is not straightened partially or completely from that instant until the ball has left the hand. This definition shall not debar a bowler from flexing or rotating the wrist in the delivery swing.)</strong></li>
            <li><strong>If any team misbehaves or abuses the Umpires inside or outside the field during/ post the match, the team will be disqualified from the tournament.</strong></li>
            <li>If a player is found using foul language on the field during the play/ off the field to his player or to the opponent or misbehaving with the Umpires or abusing to the Umpires that player will be suspended from playing the current match or may be banned for the next match or from the tournament. There will be no replacement for such suspension.</li>
            <li>Sledging strictly not allowed on and off the field.</li>
            <li>Except the captain or Umpire, none shall enter the field during the match. The decision of the Umpire will be final in all aspects during the match. No protest will be entertained against Umpire's decision.</li>
            <li>The Committee may at their discretion, can postpone/alter the time of a match. In that case, the team will be duly notified on the change before the match.</li>
            <li>Teams scheduled to play a dead rubber match, must not refrain from playing the match, failing which the company will not receive invitations for future seasons of PBCWBL.</li>
            <li>Committee is not responsible for player injuries or adverse effects to player health during the tournament. However, first aid kits will be available at ground.</li>
            <li>A player can represent for a team in the tournament. If it comes to the Committee's notice that a team has a player who has played for another team in the tournament, then both the team will be disqualified.</li>
            <li>If a player is found to be not an employee of the company he is representing in this tournament, the entire team will be disqualified at any level of the tournament.</li>
            <li>If any rules are not covered, the decision of the tournament committee will be final and binding upon all concerned as per the Law of cricket.</li>
            <li>All official confirmation will be communicated through <a href="mailto:cricket@pitchburners.com">cricket@pitchburners.com</a> or the WhatsApp group which is created for the tournament.</li>
        </ul>
    
        <div class="text-left mt-4 mb-2">
            <h3>Batting & Bowling Rules:</h3>
        </div>
        <ul class="terms-ul">
            <li><strong>Un-sized bat will not be permitted for the tournament.</strong></li>
            <li><strong>The overall length of the bat, when the lower portion of the handle is inserted, shall not be more than 38 in/96.52 cm.</strong></li>
            <li><strong>The blade of the bat shall not exceed the following dimensions: Width: 4.25in / 10.8 cm, Depth: 2.64in / 6.7 cm</strong></li>
            <li>A bowler can bowl maximum of only <strong>4 overs</strong> in a match which means minimum of 5 bowlers should be used per side in a match if its 20 overs Match.</li>
            <li>A free hit will be awarded for all No ball. If the bowler delivers a beamer, he will be given a warning. Another beamer will result in the bowler's disqualification. The severity will vary based on the threat to the batsman caused by the beamer bowled.</li>
            <li>If a bowler/ wicket keeper hits the stumps accidently while delivering the ball, it will be considered as a No-Ball and a free hit will be granted.</li>
            <li><strong>Mankding will be considered as run out.</strong></li>
            <li>Wicket Keeper can bowl immediate next over.</li>
        </ul>
      </div>
      <div id="box-cricket" class="tab-content">
        <ul class="terms-ul">
            <li>Each team can have maximum of <strong>6+4 members. No changes can be made after the submission of the squad.</strong></li>
            <li>Teams will be divided into groups. The preliminary matches will be league basis. A team will play with every other team in the group. 2 points will be awarded for the winners. The top 2 teams from each group will move to the knock out stage. If in case two teams has same points at the end of league matches, net run rate will be considered for selection to the knock out stage </li>
            <li>The entrance fee for the tournament is <strong>Rs.3,000/-</strong> the same should be paid on or before 3rd Oct 2024.</li>
            <li> The original company ID card along with one Photo-ID proof (Driving License/ Aadhar / PAN- if required) for the playing 6+4 will be verified before the start of the match.</li>
            <li><strong>Bonafide should be shared wherever is applicable.</strong></li>
            <li><strong>Bonafide Format: Below details to be printed in the Company Letter head, and it has to be attested by the company HR and to be shared via email form the HR to <a href="mailto:cricket@pitchburners.com">cricket@pitchburners.com</a>and the same has to be uploaded via submission of the squad. </strong></li>
            <li>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>S.No</th>
                                <th>Employee Number</th>
                                <th>Employee Name</th>
                                <th>Aadhaar Number</th>
                                <th>UAN Number</th>
                                <th>Photo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>12345</td>
                                <td>Ramesh</td>
                                <td>AXDP2345</td>
                                <td>12345678</td>
                                <td>Photo of the player to be fixed and attested by Employer</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </li>
            <li>Players Name submitted for the tournament should match exactly the name in their Company ID card.</li>
            <li>Soft Yellow/Green tennis balls will be used for the tournament. No new ball concept. If Ball got damaged it will be changed based on umpire decision.</li>
            <li>Request to postpone the match is <strong>strictly not entertained.</strong></li>
            <li>The team should report 30 minutes prior to the scheduled match time to sign the register and for briefing. In case a team does not report, points will be awarded to the team that hasreported. Net run rate will be 0 for both the teams in this scenario.</li>
            <li>No LBWs and Leg Byes. All other ICC rules will be applied in the tournament.</li>
            <li>Every player of a team must adhere to a fixed dress code. Players won't be allowed to play if they are dressed in shorts and/ or sleeveless T-shirt.</li>
            <li>Substitute is permitted to field in case of injured player with the discretion of the umpire as per standard rules.Substitute players is not allowed to bowl or bat.</li>
            <li>To start the match, team should have minimum of 6 players. No team will be allowed to field a side of less than 6 players. During play, if any team falls short of 6 players the match will be awarded to the opponent.</li>
            <li>Team cannot start the match with a substitute.</li>
            <li>If the stumps have been knocked off already, the stumps must be uprooted to run out a batsman.</li>
            <li>Before the start of each game, Captains of both teams shall be made aware of all the rules. Also, they will have a chance to ask the Umpires about any clarifications that they may need. Once the match starts, no protests on the rules will be entertained during the match or after the match. If any team walks out of the ground on protest during the playtime, the team will be disqualified from the tournament. </li>
            <li>In case of a Tie, Super over will be bowled. In case of Tie in Super over, another Super Over will be granted until the game ends without a Tie.</li>
            <li>If any team misbehaves or abuses the Umpires inside or outside the field during/ post the match, the team will be disqualified from the tournament. </li>
            <li>If a player is found using foul language on the field during the play/ off the field to his player or to the opponent or misbehaving with the Umpires or abusing to the Umpires that player will be suspended from playing the current match or may be banned for the next match or from the tournament. There will be no replacement for such suspension.</li>
            <li>Sledging strictly not allowed on and off the field.</li>
            <li>Except the playing members or Umpire, none shall enter the field during the match. The decision of the Umpire will be final in all aspects during the match. No protest will be entertained against Umpire's decision.</li>
            <li>The Committee may at their discretion, can postpone/alter the time of a match. In that case, the team will be duly notified on the change before the match.</li>
            <li>Committee is not responsible for player injuries or adverse effects to player health during the tournament. However, first aid kits will be available at ground.</li>
            <li>A player can represent for a team in the tournament. If it comes to the Committee's notice that a team has a player who has played for another team in the tournament, then both the team will be disqualified.</li>
            <li>If a player is found to be not an employee of the company he is representing in this tournament, the entire team will be disqualified at any level of the tournament.</li>
            <li>As the matches are being played inside a private campus, code of conduct must be maintained on and off the field. Smoking tobacco, consumption of alcohol and any sort of rude behavior are strictly prohibited.</li>
            <li>If it is brought to the notice of the Committee of any kind of misbehavior of an individual or a team by the security/ Administration, the team will be disqualified with immediate effect. The private officials can take any sort of legal action on the team or on the individual who breaks the code of conduct.</li>
            <li>If any rules are not covered, the decision of the tournament committee will be final and binding upon all concerned as per the Law of cricket.</li>
            <li>All official confirmation will be communicated through <a href="mailto:cricket@pitchburners.com">cricket@pitchburners.com</a> or the WhatsApp group which is created for the tournament.</li>
        </ul>
    
        <div class="text-left mt-4 mb-2">
            <h3>Batting & Bowling Rules:</h3>
        </div>
        <ul class="terms-ul">
            <li>No restriction towards bat. <strong>Plastic bat alone not allowed.</strong></li>
            <li>A bowler can bowl maximum of only 1 over's in a match which means minimum of 6 bowlers should be used per side in a match, <strong>, A bowler can bowl up to 68km/hr. Anything above will be called as No ball.</strong></li>
            <li>A free hit will be awarded for all No ball. If the bowler delivers a beamer, he will be given a warning. Another beamer will result in the bowler's disqualification. The severity will vary based on the threat to the batsman caused by the beamer bowled.</li>
            <li>If a bowler/ wicket keeper hit the stumps accidently while delivering the ball, it will be considered as a No-Ball and a free hit will be granted.</li>
            <li><strong>Mankading will be considered as run out.</strong></li>
            <li>Wicket Keeper can bowl immediate next over..</li>
        </ul>
    </div>
    
      <div class="text-center">
                <h5>--- In case of any discrepancy or if any rules are not clear, we request to reach the PBCL Committee for further
            clarifications ---</h5>
      </div>
            </div>
          </div>
        </div>
    </div>
   <button class="scroll-to-top" id="scrollToTop" aria-label="Scroll to top">
  <i class="fa-solid fa-chevron-up"></i>
</button>
</section>
<script>

function switchTab(tabName, event) {
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => content.classList.remove('active'));

    const tabButtons = document.querySelectorAll('.tab-btn');
    tabButtons.forEach(btn => btn.classList.remove('active'));

    document.getElementById(tabName).classList.add('active');
    event.currentTarget.classList.add('active'); // Use currentTarget instead of target

    const redBtn = document.getElementById('red-pdf-btn');
    const whiteBtn = document.getElementById('white-pdf-btn');
    const boxBtn = document.getElementById('box-pdf-btn');

    // Reset all buttons first
    redBtn.style.display = 'none';
    whiteBtn.style.display = 'none';
    boxBtn.style.display = 'none';

    if (tabName === 'red-ball') {
        redBtn.style.display = 'inline-flex';
        redBtn.style.alignItems = 'baseline';
    } 
    else if (tabName === 'white-ball') {
        whiteBtn.style.display = 'inline-flex';
        whiteBtn.style.alignItems = 'baseline';
    } 
    else if (tabName === 'box-cricket') {
        boxBtn.style.display = 'inline-flex';
        boxBtn.style.alignItems = 'baseline';
    }
}


document.addEventListener('DOMContentLoaded', function() {
    const scrollToTopButton = document.getElementById('scrollToTop');
    
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollToTopButton.classList.add('visible');
        } else {
            scrollToTopButton.classList.remove('visible');
        }
    });
    
    scrollToTopButton.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
});
</script>
@endsection