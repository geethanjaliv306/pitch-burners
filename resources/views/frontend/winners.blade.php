@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>

    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        background-color: #f5f5f5;
        overflow-x: hidden;
        scroll-behavior: smooth;
    }

    .tournament-container {
        padding: 2rem;
        width: 100%;
        min-height: calc(100vh - 60px); 
        position: relative;
        box-sizing: border-box;
        padding-top: 8rem;
    }

    .season-slider {
        max-width: 1200px;
        width: 100%;
        margin: 0 auto;
        position: relative;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .season-header {
        text-align: center;
        margin-bottom: 2rem;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        z-index: 1;
    }

    .season-title {
        font-size: 2.5rem;
        color: #614092;
        font-weight: bold;
        margin: 0;
        font-family: "Saira", Arial, sans-serif;
        display: inline-block;
    }

    .nav-button {
        width: 48px;
        height: 48px;
        border: none;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #614092;
        color: white;
        z-index: 10;
        box-shadow: 0 4px 8px rgba(97, 64, 146, 0.2);
    }

    .nav-button:hover {
        background: #755aa7;
    }

    .nav-button:disabled {
        background: #ccc;
        cursor: not-allowed;
        opacity: 0.7;
    }

    .slider-container {
        background: #614092;
        border-radius: 24px;
        padding: 2rem;
        position: relative;
        box-shadow: 0 10px 30px rgba(97, 64, 146, 0.2);
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        overflow-y: auto;
        margin-bottom: 6rem 
    }

    .season-content {
        position: absolute;
        width: 100%;
        height: 100%;
        opacity: 1;
        transition: transform 0.5s ease, opacity 0.5s ease;
        left: 0;
        top: 0;
        display: flex;
        flex-direction: column;
    }

    .season-content.new {
        position: absolute;
        width: 100%;
    }

    .awards-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        overflow-y: auto;
        padding-right: 10px;
        min-height: 0; 
        padding: 20px;
    }

    .awards-grid::-webkit-scrollbar {
        width: 2px;
    }

    .awards-grid::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 4px;
    }

    .awards-grid::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 4px;
    }

    .awards-grid::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.4);
    }

    .awards-grid {
        scrollbar-width: thin;
        scrollbar-color: rgba(255, 255, 255, 0.3) rgba(255, 255, 255, 0.1);
    }

    .award-item {
        position: relative;
        display: flex;
        align-items: center;
        padding: 1.5rem;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        transition: all 0.3s ease;
        backdrop-filter: blur(5px);
        height: 80px;
    }

    .award-item:hover {
        background: rgba(255, 255, 255, 0.15);
    }

    .award-icon {
        margin-right: 1.5rem;
        color: #FFD700;
        font-size: 2rem;
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .award-info {
        flex: 1;
        min-width: 0;
    }

    .award-info h3 {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.875rem;
        font-weight: 500;
        margin: 0 0 0.25rem 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .award-info p {
        color: white;
        font-weight: 600;
        font-size: 1.125rem;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .season-content {
        position: absolute;
    width: 100%;
    height: 100%;
    opacity: 1;
    transition: transform 0.5s ease, opacity 0.5s ease;
    left: 0;
    top: 0;
    }

    .season-content.sliding-left {
        transform: translateX(-100%);
        opacity: 0;
    }

    .season-content.sliding-right {
        transform: translateX(100%);
        opacity: 0;
    }

    .no-data-message{
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #ffff;
        gap: 16px;
        width: 100%;
    }

    .no-data-text{
        margin: 0
    }

    .fa-four {
        position: relative;
        font-style: normal;
    }
    .fa-four:before {
        content: "4";
        font-weight: bold;
    }
    
    .fa-six {
        position: relative;
        font-style: normal;
    }
    .fa-six:before {
        content: "6";
        font-weight: bold;
    }

    footer {
        height: 60px;
        border-top: 1px solid rgba(255, 255, 255, 0.6);
        position: fixed;
        width: 100%;
        bottom: 0;
    }

    #seasonNumber{
        min-width: 50px;
        display: inline-block;
    }

    .tournament-tabs {
         display: flex;
        /* gap: 20px; */
        margin-bottom: 15px;
        justify-content: center;
        margin-bottom: 50px;
    }

    .tab-btn {
        padding: 12px 25px;
        background: transparent;
        border: none;
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
        color: #1E1147;
        border-bottom: 2px solid #00897b;
        white-space: nowrap;
    }

    .tab-btn.active {
        background: #00897B;
        color: white;
        /* border-radius: 5px; */
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    @media (max-width: 768px) {
        .tournament-container {
            padding: 1rem;
            padding-top: 8rem;
        }

        .awards-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
            padding: 12px;
        }
        
        .season-title {
            font-size: 14px;
        }

        .slider-container {
            padding: 1rem;
            height: 100%;
            border-radius: 8px
        }
        
        .award-item {
            height: 60px;
            padding: 6px;
        }

        .award-icon {
            width: 40px;
            height: 40px;
            font-size: 1.5rem;
            margin-right: 1rem;
        }

        .nav-button {
            width: 40px;
            height: 40px;
        }

        .award-info p{
            font-size: 14px
        }
    }

    @media (max-width: 1000px) {
        .tournament-tabs{
            overflow-x: scroll;
            justify-content: flex-start;
        }
        .tab-btn{
            padding: 12px 20px;
            font-size: 16px;
            top: 0;
            border-bottom: 1px solid #ddd;
            flex: 1;
            white-space: nowrap;
        }
    }

</style>

<div class="tournament-container">
    <div class="tournament-tabs">
        <button class="tab-btn active" data-tab="red">Red Tennis Ball Tournament</button>
        <button class="tab-btn" data-tab="white">White Ball Tournament</button>
        <button class="tab-btn" data-tab="box">Box Cricket Tournament</button>
    </div>
    <div class="tab-content active" id="tab-red">
        <div class="season-slider">
            <div class="season-header">
                <button class="nav-button" id="prevSeason" type="button">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <h2 class="season-title">PBCCL Season <span id="seasonNumber">VI</span> - <span id="currentYear">2024</span></h2>
                <button class="nav-button" id="nextSeason" type="button">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            <div class="slider-container">
                <div class="season-content" id="seasonContent"></div>
            </div>
        </div>
    </div>

    <div class="tab-content" id="tab-white">
        <div class="season-slider">
            <div class="season-header">
                <button class="nav-button" id="prevSeasonWhite" type="button">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <h2 class="season-title">
                    White Ball Season <span id="seasonNumberWhite">I</span> -
                    <span id="currentYearWhite">2024</span>
                </h2>
                <button class="nav-button" id="nextSeasonWhite" type="button">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            <div class="slider-container">
                <div class="season-content" id="seasonContentWhite"></div>
            </div>
        </div>
    </div>
    
    <!-- BOX CRICKET -->
    <div class="tab-content" id="tab-box">
        <div class="season-slider">
            <div class="season-header">
                <button class="nav-button" id="prevSeasonBox" type="button">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <h2 class="season-title">
                    Box Cricket Season <span id="seasonNumberBox">I</span> -
                    <span id="currentYearBox">2025</span>
                </h2>
                <button class="nav-button" id="nextSeasonBox" type="button">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            <div class="slider-container">
                <div class="season-content" id="seasonContentBox"></div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const seasonsData = {
        2025:[
            { title: 'Winners', value: 'Gilbarco GVR Champs', icon: 'fa-trophy' },
            { title: 'Runners', value: 'Kavin Engineering', icon: 'fa-medal' },
            { title: '3rd Place', value: 'Exterro Cricket Coubty', icon: 'fa-award' },
            { title: '4th Place', value: 'Spartans 2.0', icon: 'fa-flag-checkered' },
            { title: '5th Place', value: 'VAF', icon: 'fa-map' },
            { title: 'Fair Play Award', value: 'HoffenSoft', icon: 'fa-handshake' },
            { title: 'Emerging Team', value: 'Ventra Health', icon: 'fa-star' },
            { title: 'Best Batsman', value: 'Sakthi Ganesh (Spartans 2.0)', icon: 'fa-person-running' },
            { title: 'Best Bowler', value: 'Manikandan (GVR Champs)', icon: 'fa-baseball' },
            { title:'Man of the Series', value:'Manikandan (GVR Champs)', icon:'fa-crown'},
            { title:'Most Catches', value:'Nagaraj (Kavin Engineering)', icon:'fa-hand-back-fist'},
            { title:'Fastest 50', value:'Raghu (Rockers Calpion)', icon:'fa-bolt'},
            { title:'Fastest 100', value:'Vetri (Shrewd Warriors)', icon:'fa-explosion'},
            { title:'Hundreds of the Tournament', value:'Raghu (Envision) & Pradeep Pandian (Pricol)', icon:'fa-hand-back-fist'},
            { title:'Electric Striker', value:'Santhosh (GVR Champs)', icon:'fa-bolt'},
            { title:'Most Sixes', value:'Vetri (Shrewd Warriors)', icon:'fa-six'},
            { title:'Most Fours', value:'Pradeep Pandian (Pricol)', icon:'fa-four'},
            { title:'Best Bowling Figure', value:'Arun (Accenture)', icon:'fa-chart-line'},
            { title:'Best Economy Bowler', value:'Raman Magar (Exterro)', icon:'fa-futbol'}
        ],
        2024: [
          { title: 'Winners', value: 'Bosch Igniters', icon: 'fa-trophy' },
          { title: 'Runners', value: 'Verticul', icon: 'fa-medal' },
          { title: '3rd Place', value: 'Gilbarco', icon: 'fa-award' },
          { title: '4th Place', value: 'Capegemini', icon: 'fa-flag-checkered' },
          { title: 'Fair Play Award', value: 'Iamneo', icon: 'fa-handshake' },
          { title: 'Best Spirited Team', value: 'HCL', icon: 'fa-star' },
          { title: 'Best Batsman', value: 'Dhanajay (Capegemini)', icon: 'fa-person-running' },
          { title: 'Best Bowler', value: 'Sasi (CTS Spartans)', icon: 'fa-baseball' },
          { title: 'Man of the Series', value: 'Kishore (Verticurl)', icon: 'fa-crown' },
          { title: 'Most Catches', value: 'Ananth Krishnan (Verticurl)', icon: 'fa-hand-back-fist' },
          { title: 'Fastest 50', value: 'Anas Mohamad (Wipro)', icon: 'fa-bolt' },
          { title: 'Electric Striker Award', value: 'Natraj (Eleviant)', icon: 'fa-bolt' },
          { title: 'Most 6\'s', value: 'Sathish (Capegemini)', icon: 'fa-six' },
          { title: 'Most 4\'s', value: 'Logesh (Eleviant)', icon: 'fa-four' },
          { title: 'Best Bowling Figure', value: 'Naveen (Iamneo)', icon: 'fa-chart-line' }
      ],
      2023: [
        { title: 'Winners', value: 'Bosch Igniters', icon: 'fa-trophy' },
        { title: 'Runners', value: 'Cummins', icon: 'fa-medal' },
        { title: '3rd Place', value: 'QBSS', icon: 'fa-award' },
        { title: '4th Place', value: 'Objectways', icon: 'fa-flag-checkered' },
        { title: 'Fair Play Award', value: 'Caro Tech', icon: 'fa-handshake' },
        { title: 'Best Batsman', value: 'Manikandan (SKPTS)', icon: 'fa-person-running' },
        { title: 'Best Bowler', value: 'Gopala Krishnan (Bosch Igniters)', icon: 'fa-baseball' },
        { title: 'Man of the Series', value: 'Dinesh (Object Ways)', icon: 'fa-crown' },
        { title: 'Most Catches', value: 'Prasanth (Bosch Igniters)', icon: 'fa-hand-back-fist' },
        { title: 'Fastest 50', value: 'Boobalan (SKPTS)', icon: 'fa-bolt' },
        { title: 'Fastest 100', value: 'Kavin (Object Ways)', icon: 'fa-explosion' },
        { title: 'Most 6\'s', value: 'Manikandan (SKPTS)', icon: 'fa-six' },
        { title: 'Best Strike Rate ', value: 'Saravana Kumar (Pricol)', icon: 'fa-chart-line' },
        { title: 'Best Bowling Figure', value: 'Ganapathy (AHS)', icon: 'fa-chart-line' }
    ],
    2022: [
      { title: 'Winners', value: 'ABT', icon: 'fa-trophy' },
      { title: 'Runners', value: 'SKPTS', icon: 'fa-medal' },
      { title: '3rd Place', value: 'GVR Champs', icon: 'fa-award' },
      { title: '4th Place', value: 'Exterro', icon: 'fa-flag-checkered' },
      { title: 'Fair Play Award', value: 'Exterro', icon: 'fa-handshake' },
      { title: 'Best Batsman', value: 'Vinod (ABT)', icon: 'fa-person-running' },
      { title: 'Best Bowler', value: 'Sethu (SKPTS)', icon: 'fa-baseball' },
      { title: 'Man of the Series', value: 'Natraj (Impiger)', icon: 'fa-crown' }
  ],
  2021: [
    { title: 'Winners', value: 'ABT', icon: 'fa-trophy' },
    { title: 'Runners', value: 'GVR Champs', icon: 'fa-medal' },
    { title: '3rd Place', value: 'SKPTS', icon: 'fa-award' },
    { title: '4th Place', value: 'BH Warriors ', icon: 'fa-flag-checkered' },
    { title: 'Fair Play Award', value: 'BH Warriors ', icon: 'fa-handshake' },
    { title: 'Best Batsman', value: 'Aravind (Kirtilal)', icon: 'fa-person-running' },
    { title: 'Best Bowler', value: 'Murugesh (BH Warriors)', icon: 'fa-baseball' },
    { title: 'Man of the Series', value: 'Syed (ABT)', icon: 'fa-crown' }
],
2019: [
  { title: 'Winners', value: 'HCL', icon: 'fa-trophy' },
  { title: 'Runners', value: 'Impiger', icon: 'fa-medal' },
  { title: '3rd Place', value: 'Bosch Thunderzings', icon: 'fa-award' },
  { title: '4th Place', value: 'Cognizant Slog warriors', icon: 'fa-flag-checkered' },
  { title: 'Fair Play Award', value: 'Information Evolution', icon: 'fa-handshake' },
  { title: 'Best Batsman', value: 'Suresh (Cameron)', icon: 'fa-person-running' },
  { title: 'Best Bowler', value: 'Sugaraj (HCL)', icon: 'fa-baseball' },
  { title: 'Man of the Series', value: 'Bharath (Slog warriors CTS)', icon: 'fa-crown' },
  { title: 'Fastest 50', value: 'Viju (Swigy IT)', icon: 'fa-bolt' },
  { title: 'Most 6\'s', value: 'Natraj (Impiger)', icon: 'fa-six' },
],
2018: [
  { title: 'Winners', value: 'HCL', icon: 'fa-trophy' },
  { title: 'Runners', value: 'Exterro', icon: 'fa-medal' },
  { title: '3rd Place', value: 'Skava', icon: 'fa-award' },
  { title: '4th Place', value: 'Jio', icon: 'fa-flag-checkered' }
],
    };

    const whiteBallSeasons = {
        2025: [
            { title: "Winners", value: "SLB, Cameron", icon: "fa-trophy"},
            { title: "Runners-Up", value: "Bosch Corporate", icon: "fa-medal" },
            { title: "Third Place", value: "Capgemini - CGCC", icon: "fa-award"},
            { title: "Man of the Series", value: "Vinoth (Capgemini - CGCC)", icon: "fa-crown"},
            { title: "Best Batsman", value: "Prem (SLB)", icon: "fa-person-running"},
            { title: "Best Bowler", value: "Lakshmikanth Naidu Challa (Bosch Corporate)", icon: "fa-baseball"}
        ]
    };

    const boxCricketSeasons = {
      2023:[
            { title: 'Winners', value: 'Bosch Corporate', icon: 'fa-trophy' },
            { title: 'Runners-up', value: 'Dreamguys Technology', icon: 'fa-medal' },
            { title: 'Third Place', value: 'Mazenet Solutions', icon: 'fa-award' },
            { title: 'Best Batsman', value: 'Gowtham (Bosch Corporate)' , icon: 'fa-person-running' },
            { title: 'Best Bowler', value: 'Vijay kannan (Dreamguys Technology)', icon: 'fa-baseball-ball' },
            { title: 'Man of the Series', value: 'Ashwin (Mazenet Solutions)', icon: 'fa-crown' }
        ],
      2024:[
            {title:'Winners', value:'SLB Cameron', icon:'fa-trophy'},
            {title:'Runners-up', value:'Scorpions from Sundata Tech' , icon:'fa-medal'},
            {title:'Third Place', value:'Avengers From Cognizant', icon:'fa-award'},
            {title:'Fourth Place', value:'Knoble knights from kovai.co', icon:'fa-flag-checkered'},
            {title:'Best Batsman', value:'Sudarshan (Avengers from Cognizant)', icon:'fa-person-running'},
            {title:'Best Bowler', value:'Rathish (Scorpions from Sundata tech)', icon:'fa-baseball-ball'},
            {title:'Man of the Series', value:'Jeeva (Knoble knights from Kovai.co)', icon:'fa-crown'}
        ],
        2025: [
            { title: 'Winners', value: 'Bosch Corporate', icon: 'fa-trophy' },
            { title: 'Runners-up', value: 'Zoho', icon: 'fa-medal' },
            { title: 'Third Place', value: 'Avengers (Cognizant)', icon: 'fa-award' },
            { title: 'Fourth Place', value: 'Spartans 2.0 ', icon: 'fa-flag-checkered' },
            { title: 'Best Batsman', value: 'Ishwa (Wipro)', icon: 'fa-person-running' },
            { title: 'Best Bowler', value: 'Sanjiviraj R (Zoho)', icon: 'fa-baseball-ball' },
            { title: 'Man of the Series', value: 'Harish (Spartans 2.0)', icon: 'fa-crown' }
        ]
    };

    function initializeSeasonSlider(config) {
        let currentYear = config.initialYear;
        const contentDiv = document.getElementById(config.contentId);
        const yearSpan = document.getElementById(config.yearSpanId);
        const seasonNumberSpan = document.getElementById(config.seasonNumberSpanId);
        const prevButton = document.getElementById(config.prevButtonId);
        const nextButton = document.getElementById(config.nextButtonId);

        function getSeasonNumber(year) {
            if (config.seasonMap) {
                return config.seasonMap[year] || '';
            } else {
                const romanNumerals = { 1: 'I', 2: 'II', 3: 'III', 4: 'IV', 5: 'V', 6: 'VI', 7: 'VII', 8: 'VIII', 9: 'IX', 10: 'X' };
                const sortedYears = Object.keys(config.seasonsData).map(Number).sort((a, b) => a - b);
                const seasonIndex = sortedYears.indexOf(year);
                return romanNumerals[seasonIndex + 1] || '';
            }
        }

        function renderSeason(year, direction = null) {
            const awards = config.seasonsData[year];
            yearSpan.textContent = year;
            seasonNumberSpan.textContent = getSeasonNumber(year);

            const newContent = document.createElement('div');
            newContent.className = 'season-content new';
            
            const html = awards ? `
                <div class="awards-grid">
                    ${awards.map(award => `
                        <div class="award-item">
                            <div class="award-icon">
                                <i class="fas ${award.icon}"></i>
                            </div>
                            <div class="award-info">
                                <h3>${award.title}</h3>
                                <p>${award.value}</p>
                            </div>
                        </div>
                    `).join('')}
                </div>
            ` : `
                <div class="no-data-message">
                    <i class="fas fa-info-circle"></i>
                    <p class="no-data-text">No Data Available for the Year ${year}</p>
                </div>
            `;
            
            newContent.innerHTML = html;

            if (direction === 'next') {
                newContent.style.transform = 'translateX(100%)';
            } else if (direction === 'prev') {
                newContent.style.transform = 'translateX(-100%)';
            }

            contentDiv.appendChild(newContent);

            void newContent.offsetHeight;

            if (contentDiv.children.length > 1) {
                const oldContent = contentDiv.children[0];
                oldContent.style.transform = direction === 'next' ? 'translateX(-100%)' : 'translateX(100%)';
                oldContent.style.opacity = '0';
            }

            requestAnimationFrame(() => {
                newContent.style.transform = 'translateX(0)';
                newContent.style.opacity = '1';
            });

            setTimeout(() => {
                while (contentDiv.children.length > 1) {
                    contentDiv.removeChild(contentDiv.firstElementChild);
                }
            }, 500);
        }

        function updateNavButtons() {
            const availableYears = Object.keys(config.seasonsData).map(Number).sort((a, b) => a - b);
            const currentIndex = availableYears.indexOf(currentYear);
            prevButton.disabled = currentIndex <= 0;
            nextButton.disabled = currentIndex >= availableYears.length - 1;
        }

        function changeSeason(direction) {
            const availableYears = Object.keys(config.seasonsData).map(Number).sort((a, b) => a - b);
            const currentIndex = availableYears.indexOf(currentYear);
            const newIndex = direction === 'next' ? currentIndex + 1 : currentIndex - 1;
            
            if (newIndex >= 0 && newIndex < availableYears.length) {
                currentYear = availableYears[newIndex];
                renderSeason(currentYear, direction);
                updateNavButtons();
            }
        }

        prevButton.addEventListener('click', () => {
            if (!prevButton.disabled) changeSeason('prev');
        });

        nextButton.addEventListener('click', () => {
            if (!nextButton.disabled) changeSeason('next');
        });

        renderSeason(currentYear);
        updateNavButtons();
    }

    initializeSeasonSlider({
        initialYear: 2025,
        contentId: 'seasonContent',
        yearSpanId: 'currentYear',
        seasonNumberSpanId: 'seasonNumber',
        prevButtonId: 'prevSeason',
        nextButtonId: 'nextSeason',
        seasonsData: seasonsData,
        seasonMap: {
            2018: 'I',
            2019: 'II',
            2021: 'III',
            2022: 'IV',
            2023: 'V',
            2024: 'VI',
            2025: 'VII'
        }
    });

    initializeSeasonSlider({
        initialYear: 2025,
        contentId: 'seasonContentWhite',
        yearSpanId: 'currentYearWhite',
        seasonNumberSpanId: 'seasonNumberWhite',
        prevButtonId: 'prevSeasonWhite',
        nextButtonId: 'nextSeasonWhite',
        seasonsData: whiteBallSeasons,
        seasonMap: { 2025: 'I' }
    });

    initializeSeasonSlider({
        initialYear: 2025,
        contentId: 'seasonContentBox',
        yearSpanId: 'currentYearBox',
        seasonNumberSpanId: 'seasonNumberBox',
        prevButtonId: 'prevSeasonBox',
        nextButtonId: 'nextSeasonBox',
        seasonsData: boxCricketSeasons,
        seasonMap: { 2023: 'I',
                     2024: 'II',
                     2025: 'III' 
                   }
    });
});

document.addEventListener("DOMContentLoaded", () => {
    const tabs = document.querySelectorAll(".tab-btn");
    const contents = document.querySelectorAll(".tab-content");

    tabs.forEach(btn => {
        btn.addEventListener("click", () => {
            tabs.forEach(b => b.classList.remove("active"));
            contents.forEach(c => c.classList.remove("active"));
            btn.classList.add("active");
            document.getElementById("tab-" + btn.dataset.tab).classList.add("active");
        });
    });
});
</script>
@endsection