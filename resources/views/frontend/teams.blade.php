@extends('layouts.app')
@section('content')
<style>
    .our-sponsers {
        display: none;
    }
    .teams-wrap .teams-item figcaption{
        font-size:22px;
    }
    @media (max-width: 767.98px) {
        .add-teamname-wrap {
            flex-direction: row;
        }
    }
    .has-filter-dropdown .dropdown .btn {
        border-radius:5px;
    }
    .teams-wrap .teams-item figure{
        overflow:hidden;
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
    @media (max-width: 767.98px) {
        .select-category {
            width: auto;
        }
        .fixed-second-header.teams-second-header {
            height: 75px;
        }
    }
    .teams-wrap .teams-item{
        border-radius: 20px;
    }
    .teams-wrap .teams-item figure{
        border-radius: 10px;
        border-top-left-radius: 30px;
        border-bottom-right-radius: 30px;
    }
    .footer-mobile-app{
        position:relative;
    }
    /* Dropdown specific styles */
    .seasondropdown {
        position: relative;
    }
    .seasondropdown .btn {
        background: linear-gradient(135deg, #FF9F00, #FFB84D);
        border: none;
        color: #000;
        font-weight: bold;
        text-align: left;
        padding: 10px 20px;
        border-radius: 8px;
        min-width: 200px;
        position: relative;
    }
    .seasondropdown .btn:hover,
    .seasondropdown .btn:focus,
    .seasondropdown .btn:active,
    .seasondropdown .btn.show {
        background: linear-gradient(135deg, #E68A00, #FF9F00) !important;
        border: none !important;
        box-shadow: none !important;
        color: #000 !important;
    }
    .seasondropdown .btn label {
        font-size: 12px;
        font-weight: bold;
        margin: 0;
        display: block;
        text-transform: uppercase;
    }
    .seasondropdown .btn p {
        font-size: 16px;
        margin: 0;
        font-weight: normal;
    }
    .seasondropdown .btn::after {
        content: "▼";
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        font-size: 12px;
    }
    .seasondropdown .dropdown-menu {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-top: 5px;
        min-width: 200px;
        z-index: 1050;
    }
    .seasondropdown .dropdown-item {
        padding: 10px 20px;
        color: #333;
        font-weight: 500;
    }
    .seasondropdown .dropdown-item:hover,
    .seasondropdown .dropdown-item:focus {
        background-color: #F8F9FA;
        color: #FF9F00;
    }
    /* Remove Bootstrap's default dropdown arrow */
    .seasondropdown .btn.dropdown-toggle::after {
        display: none;
    }
    /* Ensure dropdown is visible when open */
    .seasondropdown .dropdown-menu.show {
        display: block;
    }
</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<section class="addnewplayer-title-wrap fixed-second-header teams-second-header">
    <div class="container h-100">
        <div class="row h-100 d-flex align-items-center">
            <div class="col-12">
                <div class="add-teamname-wrap">
                    <div class="addteam-logo d-flex align-items-center">
                        <figcaption>
                            <h5>Teams</h5>
                        </figcaption>
                    </div>
                    <div class="select-category d-flex align-items-center has-filter-dropdown">
                        <div class="dropdown seasondropdown">
                            <button class="btn btn-secondary dropdown-toggle"
                                    type="button"
                                    id="tournamentDropdown"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false"
                                    data-bs-auto-close="true">
                                <label>SELECT <span>TOURNAMENT</span></label>
                                <p>{{ $selectedTournamentId && $tournaments->firstWhere('id', $selectedTournamentId) ? $tournaments->firstWhere('id', $selectedTournamentId)->name : 'All Tournaments' }}</p>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="tournamentDropdown">
                                <li><a class="dropdown-item" href="{{ route('teams.view') }}">All Tournaments</a></li>
                                @foreach($tournaments as $tournament)
                                    <li>
                                        <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['tournament_id' => $tournament->id]) }}">
                                            {{ $tournament->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<main class="main-wrapper-start teams-main">
    <div class="container">
        <div class="row">
           <div class="col-12">
                <div class="teams-wrap" style="justify-content: center;">
                    @if($teams->isEmpty())
                        <p class="text-center">No teams available</p>
                    @else
                        @foreach($teams->sortBy('name') as $team)
                            <div class="teams-item">
                                <figure>
                                    <img width="200px" src="{{ config('constants.upload_url') . '/team_logos/' . $team->logo }}" alt="{{ $team->name }}" />
                                </figure>
                                 <figcaption style="text-align: center;">{{ $team->name }}</figcaption>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
    <button class="scroll-to-top" id="scrollToTop" aria-label="Scroll to top">
        <i class="fa-solid fa-chevron-up"></i>
    </button>
</main>
<!-- Bootstrap JS - Make sure it's loaded -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    // Initialize dropdowns manually
    var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl);
    });
    // Debug: Check if Bootstrap is loaded
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap JavaScript is not loaded properly!');
    } else {
        console.log('Bootstrap loaded successfully');
    }
    // Fallback dropdown functionality
    const tournamentDropdown = document.getElementById('tournamentDropdown');
    if (tournamentDropdown) {
        // Add click event listener as fallback
        tournamentDropdown.addEventListener('click', function(e) {
            e.preventDefault();
            // Try Bootstrap method first
            try {
                const bsDropdown = bootstrap.Dropdown.getOrCreateInstance(this);
                bsDropdown.toggle();
            } catch (error) {
                console.log('Bootstrap method failed, using fallback');
                // Fallback method
                const dropdownMenu = this.nextElementSibling;
                const isOpen = dropdownMenu.classList.contains('show');
                // Close all other dropdowns
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.remove('show');
                });
                document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
                    toggle.classList.remove('show');
                    toggle.setAttribute('aria-expanded', 'false');
                });
                if (!isOpen) {
                    dropdownMenu.classList.add('show');
                    this.classList.add('show');
                    this.setAttribute('aria-expanded', 'true');
                }
            }
        });
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.remove('show');
                });
                document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
                    toggle.classList.remove('show');
                    toggle.setAttribute('aria-expanded', 'false');
                });
            }
        });
    }
    // Scroll to top functionality
    const scrollToTopButton = document.getElementById('scrollToTop');
    if (scrollToTopButton) {
        // Show/hide button based on scroll position
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                scrollToTopButton.classList.add('visible');
            } else {
                scrollToTopButton.classList.remove('visible');
            }
        });
        // Smooth scroll to top when clicked
        scrollToTopButton.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
});
</script>
@endsection