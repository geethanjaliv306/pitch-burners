@extends('layouts.admin')

@section('content')
<!-- CSS Files -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<div class="container mt-4" style="padding-top: 5rem;padding-bottom: 5rem">
    <div class="date-filter-section mb-4">
        <div class="date-filter-container">
            <div class="filter-wrapper">
                <div class="filter-header">
                    <div class="header-content">
                        <span class="date-label">Date Range</span>
                    </div>
                </div>
                <div class="filter-inputs">
                    <div class="date-input-wrapper">
                        <input 
                            type="text" 
                            class="form-control custom-date-input" 
                            id="startDate" 
                            name="start_date" 
                            value="{{$start_date}}" 
                            placeholder="Start Date"
                        >
                        <i class="far fa-calendar-alt calendar-icon"></i>
                    </div>
                    <div class="date-input-wrapper">
                        <input 
                            type="text" 
                            class="form-control custom-date-input" 
                            id="endDate" 
                            name="end_date" 
                            value="{{$end_date}}" 
                            placeholder="End Date"
                        >
                        <i class="far fa-calendar-alt calendar-icon"></i>
                    </div>
                    <button 
                        class="btn custom-submit-btn" 
                        id="applyFilter"
                    >
                        Submit
                    </button>
                </div>
            </div>
        </div>
    </div>
  
  <div class="skeleton-loader" style="display:block">
        <div class="stats-grid">
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="stat-card skeleton">
                        <div class="stat-icon skeleton-box"></div>
                        <div class="stat-content">
                            <span class="stat-label skeleton-text"></span>
                            <h3 class="stat-value skeleton-text-lg"></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card skeleton">
                        <div class="stat-icon skeleton-box"></div>
                        <div class="stat-content">
                            <span class="stat-label skeleton-text"></span>
                            <h3 class="stat-value skeleton-text-lg"></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card skeleton">
                        <div class="stat-icon skeleton-box"></div>
                        <div class="stat-content">
                            <span class="stat-label skeleton-text"></span>
                            <h3 class="stat-value skeleton-text-lg"></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card skeleton">
                        <div class="stat-icon skeleton-box"></div>
                        <div class="stat-content">
                            <span class="stat-label skeleton-text"></span>
                            <h3 class="stat-value skeleton-text-lg"></h3>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="stat-card skeleton">
                        <div class="stat-icon skeleton-box"></div>
                        <div class="stat-content">
                            <span class="stat-label skeleton-text"></span>
                            <h3 class="stat-value skeleton-text-lg"></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card skeleton">
                        <div class="stat-icon skeleton-box"></div>
                        <div class="stat-content">
                            <span class="stat-label skeleton-text"></span>
                            <h3 class="stat-value skeleton-text-lg"></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card skeleton">
                        <div class="stat-icon skeleton-box"></div>
                        <div class="stat-content">
                            <span class="stat-label skeleton-text"></span>
                            <h3 class="stat-value skeleton-text-lg"></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card skeleton">
                        <div class="stat-icon skeleton-box"></div>
                        <div class="stat-content">
                            <span class="stat-label skeleton-text"></span>
                            <h3 class="stat-value skeleton-text-lg"></h3>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="stat-card skeleton">
                        <div class="stat-icon skeleton-box"></div>
                        <div class="stat-content">
                            <span class="stat-label skeleton-text"></span>
                            <h3 class="stat-value skeleton-text-lg"></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card skeleton">
                        <div class="stat-icon skeleton-box"></div>
                        <div class="stat-content">
                            <span class="stat-label skeleton-text"></span>
                            <h3 class="stat-value skeleton-text-lg"></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card skeleton">
                        <div class="stat-icon skeleton-box"></div>
                        <div class="stat-content">
                            <span class="stat-label skeleton-text"></span>
                            <h3 class="stat-value skeleton-text-lg"></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card skeleton">
                        <div class="stat-icon skeleton-box"></div>
                        <div class="stat-content">
                            <span class="stat-label skeleton-text"></span>
                            <h3 class="stat-value skeleton-text-lg"></h3>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="player-highlight-card skeleton">
                        <div class="player-info">
                            <div class="player-image skeleton-box"></div>
                            <div class="player-details">
                                <div class="score-details">
                                    <span class="skeleton-text"></span>
                                    <h4 class="sk-runs skeleton-text-lg"></h4>
                                </div>
                                <div class="score-details">
                                    <span class="player-name skeleton-text"></span>
                                    <span class="label skeleton-text"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="player-highlight-card skeleton">
                        <div class="player-info">
                            <div class="player-image skeleton-box"></div>
                            <div class="player-details">
                                <div class="score-details">
                                    <span class="skeleton-text"></span>
                                    <h4 class="sk-wickets skeleton-text-lg"></h4>
                                </div>
                                <div class="score-details">
                                    <span class="player-name skeleton-text"></span>
                                    <span class="label skeleton-text"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="stats-grid" id="stats-grid-og" style="display:none">
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card primary">
                    <div class="stat-icon">
                        <img src="{{asset('uploads/match-stats/total-matches.svg')}}" alt="Total Matches" width="auto" height="80"/>
                    </div>
                    <div class="stat-content">
                        <span class="stat-label">Total Matches</span>
                        <h3 class="stat-value matches">-</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <img src="{{asset('uploads/match-stats/total-runs.svg')}}" alt="Total Runs" width="auto" height="80"/>
                    </div>
                    <div class="stat-content">
                        <span class="stat-label">Total Runs</span>
                        <h3 class="stat-value runs">-</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <img src="{{asset('uploads/match-stats/overs-bowled.svg')}}" alt="Overs Bowled" width="auto" height="80"/>
                    </div>
                    <div class="stat-content">
                        <span class="stat-label">Overs Bowled</span>
                        <h3 class="stat-value overs">-</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <img src="{{asset('uploads/match-stats/wickets.svg')}}" alt="Wickets" width="auto" height="80"/>
                    </div>
                    <div class="stat-content">
                        <span class="stat-label">Wickets</span>
                        <h3 class="stat-value wickets">-</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <img src="{{asset('uploads/match-stats/maidens.svg')}}" alt="Maidens" width="auto" height="80"/>
                    </div>
                    <div class="stat-content">
                        <span class="stat-label">Maidens</span>
                        <h3 class="stat-value maidens">-</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <img src="{{asset('uploads/match-stats/hundreds.svg')}}" alt="hundreds" width="auto" height="80"/>
                    </div>
                    <div class="stat-content">
                        <span class="stat-label">100's</span>
                        <h3 class="stat-value hundreds">-</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <img src="{{asset('uploads/match-stats/fifties.svg')}}" alt="fifties" width="auto" height="80"/>
                    </div>
                    <div class="stat-content">
                        <span class="stat-label">50’s</span>
                        <h3 class="stat-value fifties">-</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <img src="{{asset('uploads/match-stats/sixes.svg')}}" alt="sixes" width="auto" height="80"/>
                    </div>
                    <div class="stat-content">
                        <span class="stat-label">6’s</span>
                        <h3 class="stat-value sixes">-</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <img src="{{asset('uploads/match-stats/fours.svg')}}" alt="fours" width="auto" height="80"/>
                    </div>
                    <div class="stat-content">
                        <span class="stat-label">4’s</span>
                        <h3 class="stat-value fours">-</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <img src="{{asset('uploads/match-stats/wickets.svg')}}" alt="No of Five Wickets" width="auto" height="80"/>
                    </div>
                    <div class="stat-content">
                        <span class="stat-label">No of 5 wickets</span>
                        <h3 class="stat-value five-wickets">-</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <img src="{{asset('uploads/match-stats/wickets.svg')}}" alt="No of 3 wickets" width="auto" height="80"/>
                    </div>
                    <div class="stat-content">
                        <span class="stat-label">No of 3 wickets</span>
                        <h3 class="stat-value three-wickets">-</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <img src="{{asset('uploads/match-stats/catches.svg')}}" alt="Total Catches" width="auto" height="80"/>
                    </div>
                    <div class="stat-content">
                        <span class="stat-label">Total Catches</span>
                        <h3 class="stat-value catches">-</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="player-highlight-card">
                    <div class="player-info">
                        <div class="player-image">
                            <img src="{{config('constants.upload_url') . '/player_images/' }}" alt="Highest Scorer" width="80" height="80" id="scorer-img"/>
                        </div>
                        <div class="player-details">
                            <div class="score-details">
                                <span>Highest Scorer</span>
                                <h4 class="runs highest-scorer">-</h4>
                            </div>
                            <div class="score-details">
                                <span class="player-name highest-score">-</span>
                                <span class="label">Runs</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="player-highlight-card">
                    <div class="player-info">
                        <div class="player-image">
                            <img src="{{config('constants.upload_url') . '/player_images/' }}" alt="Highest Wicket Taker" width="80" height="80" id="wicket-taker-img"/>
                        </div>
                        <div class="player-details">
                            <div class="score-details">
                                <span>Highest Wicket Taker</span>
                                <h4 class="wickets highest-wicket-taker">-</h4>
                            </div>
                            <div class="score-details">
                                <span class="player-name highest-wickets">-</span>
                                <span class="label">Wkts</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --primary-purple: #614092;
    --hover-purple: #614092;
    --text-light: #666;
    --border-radius: 12px;
    --box-shadow: 0 0px 4px rgba(0,0,0,0.1);
    --text-grey: #626262
}
.date-filter-section {
    margin-bottom: 2rem;
}

.date-filter-container {
    background: #F8F9FA;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.filter-wrapper {
    display: flex;
    align-items: stretch;
}

.filter-header {
    background-color: #E7E7E7;
    padding: 0 1.5rem;
    display: flex;
    align-items: center;
    min-width: 150px;
    border-right: 1px solid #E5E7EB;
}

.header-content {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.header-content i {
    color: #614092;
    font-size: 1.25rem;
    font-family: "Saira", Arial, Helvetica, sans-serif;em;

}

.date-label {
    font-size: 1rem;
    font-weight: 500;
    color: #614092;
    white-space: nowrap;
    font-family: "Saira", Arial, Helvetica, sans-serif;em;

}

.filter-inputs {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 12px 16px;
    flex-grow: 1;
}

.date-input-wrapper {
    position: relative;
    min-width: 200px;
}

.calendar-icon {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #614092;
    pointer-events: none;
}

.custom-date-input {
    height: 45px;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    padding: 0.75rem 2.5rem 0.75rem 1rem;
    font-size: 0.875rem;
    color: #374151;
    background-color: white;
    transition: all 0.2s ease;
    width: 100%;
    cursor:pointer
}

.custom-date-input:focus {
    border-color: #614092;
    box-shadow: 0 0 0 3px rgba(97, 64, 146, 0.1);
    outline: none;
}

.custom-date-input::placeholder {
    color: #9CA3AF;
}

.custom-submit-btn {
    height: 45px;
    background: #614092;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 500;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    padding: 0 2rem;
    white-space: nowrap;
    margin:0;
    max-width:200px;
    width:100%
}

.custom-submit-btn:hover {
    background: #513279;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(97, 64, 146, 0.15);
    color:#fff
}

.custom-submit-btn:active {
    transform: translateY(0);
}

@media (max-width: 992px) {
    .filter-wrapper {
        flex-direction: column;
    }
    
    .filter-header {
        padding: 1rem 1.5rem;
        border-right: none;
        border-bottom: 1px solid #E5E7EB;
    }
    
    .filter-inputs {
        flex-direction: column;
        width: 100%;
    }
    
    .date-input-wrapper {
        width: 100%;
    }
    
    .custom-submit-btn {
        width: 100%;
    }
}

.stat-card {
    background: #F8F9FA;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    transition: all 0.3s ease;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: space-between;
   	padding: 24px 20px !important;
}

.stat-card.primary {
    background: var(--hover-purple);
}

.stat-value,
.stat-label,
.stat-card.primary .stat-value,
.stat-card.primary .stat-label {
    color: white;
}

.stat-icon {
    height: 80px;
    width: auto;
    overflow: hidden;
    margin: 0;
  	max-width:80px;
}

.stat-icon img {
    height: 100%;
    width: 100%;
    object-fit: contain;
}
  
.stat-content{
    text-align: center;
}

.stat-value {
    font-size: 46px;
    font-family:  Arial, Helvetica, sans-serif;
    font-weight: 600;
    color: var(--primary-purple);
    margin: 0;
    text-align: center

}

.stat-label {
    font-size: 16px;
    font-family: "Saira", Arial, Helvetica, sans-serif;
    color: #000;
    margin: 0;
    text-align: center;
    margin-bottom:4px;
    display:inline-flex
}


.player-highlight-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 12px;
    box-shadow: var(--box-shadow);
    height: 100%;
    transition: all 0.3s ease;
    cursor: pointer;
}

.player-highlight-card:hover {
    box-shadow: 0 8px 16px rgba(97, 64, 146, 0.1);
}

.player-info {
    display: flex;
    align-items: center;
    gap: 24px;
}

.player-image {
    flex-shrink: 0;
    border-radius: 8px;
    border: 1px solid #f8f8f8;
    height:120px;
    width:120px;
    overflow:hidden
}

.player-image img {
    object-fit: cover;
    width: 100%;
    height: 100%;
    object-position: top center;
}

.player-details {
    flex-grow: 1;
}

.player-details h4 {
    color: var(--text-light);
    font-size: 16px;
    font-family: "Saira", Arial, Helvetica, sans-serif;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.player-name {
    color: var(--primary-purple);
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 8px;
    transition: all 0.3s ease;
    font-family: "Saira", Arial, Helvetica, sans-serif;
}

.score-details {
    display: flex;
    align-items: center;
    gap: 8px;
    justify-content: space-between;
}

.score-details .runs{
    color: var(--primary-purple);
    font-size: 40px;
    font-weight: 700;
}

.score-details .wickets {
    color: var(--primary-purple);
    font-size: 40px;
    font-weight: 700;
}

.score-details .label {
    color: var(--text-light);
    font-size: 14px;
    font-weight: 500;
}

.stat-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

  
@keyframes shimmer {
    0% {
        background-position: -1000px 0;
    }
    100% {
        background-position: 1000px 0;
    }
}

.skeleton {
    background: #F8F9FA;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
}

.skeleton-box {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 1000px 100%;
    animation: shimmer 2s infinite linear;
    width: 80px;
    height: 80px;
    border-radius: 8px;
}

.skeleton-text {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 1000px 100%;
    animation: shimmer 2s infinite linear;
    height: 16px;
    width: 100px;
    display: block;
    border-radius: 4px;
}

.skeleton-text-lg {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 1000px 100%;
    animation: shimmer 2s infinite linear;
    height: 46px;
    width: 80px;
    display: block;
    border-radius: 4px;
    margin: 8px 0;
}

.skeleton .player-image {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 1000px 100%;
    animation: shimmer 2s infinite linear;
    width: 120px;
    height: 120px;
    border: none;

}

.skeleton .stat-icon {
    margin: 0;
    max-width: 80px;
}

.skeleton .score-details {
    flex-direction: column;
    align-items: flex-start;
}

.skeleton .score-details .skeleton-text {
    margin-bottom: 8px;
}

.skeleton-loader {
    width: 100%;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    const startDate = '{{$start_date}}';
    const endDate = '{{$end_date}}';
    const imgPath = '{{config('constants.upload_url') . '/player_images/'}}';
    flatpickr("#startDate", {
        dateFormat: "Y-m-d",
        allowInput: true,
        minDate: startDate,
        // maxDate: endDate,
    });
    
    flatpickr("#endDate", {
        dateFormat: "Y-m-d",
        allowInput: true,
        minDate: endDate,
        // maxDate: endDate,
    });
  
    const statsGrid = document.querySelector('#stats-grid-og');
    const skeletonLoader = document.querySelector('.skeleton-loader');


    const timeOut = setTimeout(() => {
        $('#applyFilter').trigger('click');
    }, (10));


    document.getElementById('applyFilter').addEventListener('click', function() {
        const fromDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;        
        if(!fromDate || !endDate){
            alert('Need to select start & end date!!');
            return;
        }
      
        statsGrid.style.display = 'none';
        skeletonLoader.style.display = 'block';
      
        $.ajax({
            url: '/admin/get-stats',
            type: 'GET',
            data: {
                fromDate,
                endDate,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log(response.data);
              
              setTimeout(() => {
                statsGrid.style.display = 'block';
        		skeletonLoader.style.display = 'none';
              }, (2000));
              
                statsGrid.style.display = 'block';
        		skeletonLoader.style.display = 'none';
              
                const {data} = response;
                if(data && Object.keys(data).length) {
                    const matches = document.querySelector('.matches');
                    const runs = document.querySelector('.runs');
                    const overs = document.querySelector('.overs');
                    const wickets = document.querySelector('.wickets');
                    const maidens = document.querySelector('.maidens');
                    const fifties = document.querySelector('.fifties');
                    const hundreds  = document.querySelector('.hundreds');
                    const fours  = document.querySelector('.fours');
                    const sixes  = document.querySelector('.sixes');
                    const threeWickets = document.querySelector('.three-wickets');
                    const fiveWickets = document.querySelector('.five-wickets');
                    const highestScorer = document.querySelector('.highest-scorer');
                    const highestWicketTaker = document.querySelector('.highest-wicket-taker');
                    const highestScore = document.querySelector('.highest-score');
                    const highestWickets = document.querySelector('.highest-wickets');
                    const catches = document.querySelector('.catches');
                    const highestScorerImg = document.querySelector('#scorer-img');
                    const wicketTakerImg = document.querySelector('#wicket-taker-img');

                    const {total_matches, total_runs, total_wickets, total_overs_bowled, total_maidens, three_wickets, five_wickets, highest_scorer, most_wicket_taker, total_fifties, total_hundreds, total_fours, total_sixes, total_catches} = data;
                    matches.textContent = total_matches ?? '-';
                    runs.textContent =  total_runs ?? '-';
                    overs.textContent = total_overs_bowled ?? '-';
                    wickets.textContent = total_wickets ?? '-';
                    maidens.textContent = total_maidens ?? '-';
                    fifties.textContent = total_fifties ?? '-';
                    hundreds.textContent = total_hundreds ?? '-';
                    fours.textContent = total_fours ?? '-';
                    sixes.textContent = total_sixes ?? '-';
                    threeWickets.textContent = three_wickets ?? '-';
                    fiveWickets.textContent = five_wickets ?? '-';
                    highestScore.textContent = highest_scorer && Object.keys(highest_scorer).length ? `${highest_scorer.player}` : '-';
                    highestScorer.textContent = highest_scorer && Object.keys(highest_scorer).length ? `${highest_scorer.runs}` : '-';
                    highestWickets.textContent = most_wicket_taker && Object.keys(most_wicket_taker).length ? `${most_wicket_taker.player}` : '-';
                    highestWicketTaker.textContent = most_wicket_taker && Object.keys(most_wicket_taker).length ? `${most_wicket_taker.wickets}` : '-';
                    catches.textContent = total_catches ?? '-';

                    highestScorerImg.setAttribute('src', imgPath + highest_scorer?.player_image)
                    wicketTakerImg.setAttribute('src', imgPath + most_wicket_taker?.player_image)

                    console.warn(highestScorerImg, wicketTakerImg)

                }
            },
            error: function(xhr, status, error) {
                console.error('Error saving ball data:', error);
            }
        });        
        console.log('Filtering data between:', fromDate, 'and', endDate);
    });
</script>
@endsection