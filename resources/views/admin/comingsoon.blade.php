<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>T10 Corporate Cricket Tournament</title>
    <link href="https://fonts.googleapis.com/css2?family=Saira:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"  crossorigin="anonymous">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            font-family: "Saira", Arial, sans-serif;
        }

        .tournament-container {
            min-height: 100vh;
            background-image: url('{{ asset("uploads/images/homepage.svg") }}');
            background-size: contain;
            background-position: bottom;
            background-attachment: fixed;
            padding: 2rem 1rem;
            /* display: flex;a */
            justify-content: center;
            align-items: flex-start;
            background-repeat: no-repeat;
            font-family: "Saira", Arial, Helvetica, sans-serif;
        }

        .premium-card {
            width: 100%;
            max-width: 900px;
            margin: 2rem auto;
            background: rgb(74, 28, 150);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            padding: 2rem;
            position: relative;
        }

        .premium-card:hover {
            background: rgba(58, 21, 120, 1);
        }
        .premium-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 1rem;
            background: linear-gradient(45deg, rgba(255, 183, 3, 0.1), rgba(255, 255, 255, 0.1));
            z-index: -1;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 0rem;
            display: flex;
            justify-content: center;
            align-items: center;

        }

        .logo-container img {
            max-width: 200px;
            height: auto;
            margin-bottom: 0.5rem;
            margin-right: 1.5rem;
        }

        .season-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background-color: #ffb703;
            color: #4a1c96;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .tournament-title {
            color: white;
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            margin: 1.5rem 0;
        }

        .info-container {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            flex-wrap: wrap;
            color: #ffb703;
            margin-bottom: 2rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 0.5rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin: 2rem auto;
            max-width: 800px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            padding: 1.5rem;
            border-radius: 0.5rem;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-title {
            color: #ffb703;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .register-btn {
            display: block;
            width: 100%;
            background-color: #ffb703;
            color: #614092;
            font-weight: 700;
            font-size: 1.25rem;
            padding: 1rem;
            border-radius: 0.5rem;
            text-decoration: none;
            transition: all 0.3s ease;
            text-align: center;
        }

        .register-btn:hover {
            background-color: #614092;
            color: #ffb703;
        }

        .contact-info {
            text-align: center;
            color: rgba(255, 255, 255, 0.8);
            margin-top: 2rem;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 0.5rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .contact-numbers {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-top: 0.5rem;
            flex-wrap: wrap;
        }

        .footer-text {
            text-align: center;
            color: #ffb703;
            font-weight: 700;
            font-style: italic;
            margin-top: 2rem;
            font-size: 1.25rem;
        }

        @media (max-width: 640px) {
            .tournament-container {
                padding: 1rem;
            }

            .premium-card {
                margin: 1rem auto;
                padding: 1rem;
            }

            .tournament-title {
                font-size: 1rem;
            }

            .info-container {
                flex-direction: column;
                align-items: center;
                gap: 0.75rem;
                margin-bottom: 0;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .contact-numbers {
                flex-direction: column;
                gap: 0.5rem;
            }

            .logo-container {
                flex-direction: column;
            }
        }
      footer{
        position: fixed;
         left:0px;
    bottom: 0;
    width: 100%;
    background-color: #ffffff;
      }
    </style>
</head>
<body>
    <div class="tournament-container">
        <div class="premium-card">
            <div class="logo-container">
                <img src="{{ asset('uploads/images/pitchburners-new-logo1.png') }}" alt="Pitch Burners Logo">
                <div class="season-badge">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                    <span>Season 7</span>
                </div>
            </div>

            <h1 class="tournament-title">T10 Corporate Cricket Tournament</h1>

            <div class="info-container">
                <div class="info-item">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Starts February 2025</span>
                </div>
                <div class="info-item">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Red Tennis Ball</span>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-title">Entry Fee</div>
                    <div class="stat-value">₹6,000/-</div>
                </div>
                <div class="stat-card" style="background-color:transparent">
                    @if(Auth::check())
                    <a href="{{ route('login') }}" class="register-btn">
                        Go to Dashboard
                    </a>
                    @else
                    <a href="{{ route('register') }}" class="register-btn">
                        Register Now
                    </a>
                    @endif
                </div>
                <div class="stat-card">
                    <div class="stat-title">Teams</div>
                    <div class="stat-value">Limited Slots</div>
                </div>
            </div>

            <div class="contact-info">
                <p>For more information, contact:</p>
                <div class="contact-numbers">
                    <span>Guru: +91-9962851516</span>
                    <span>Charles: +91-9894568389</span>
                </div>
            </div>

            <div class="footer-text">
                Cricket Fever is Back!!!
            </div>
        </div>
        <footer>
            <div class="container h-100">
                <div class="row h-100">
                    <div class="col-12 h-100 text-center d-flex align-items-center justify-content-center">
                      <p><a target="_blank" href="{{route('terms')}}">Terms and Conditions </a></p>|
                      <p><a target="_blank" href="{{route('privacy')}}">Privacy Policy</a></p> |
                      <p>Copyright © CbePitch Burners Sports Foundation 2024 | Designed by <a target="_blank" href="https://dsignzmedia.com/">Dsignzmedia</a></p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"  crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"  crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
</html>
