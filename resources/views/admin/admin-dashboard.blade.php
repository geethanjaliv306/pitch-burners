@extends('layouts.terms')
@section('content')
 <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f8f8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            text-align: center;
            padding: 2rem;
            max-width: 600px;
            width: 90%;
        }

        .icon-container {
            margin-bottom: 2rem;
        }

        .icon {
            font-size: 8rem;
            color: #008F7A;
            animation: bounce 2s infinite;
        }

        .title {
            font-size: 2.5rem;
            color: #23004b;
            margin-bottom: 1rem;
        }

        .message {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .home-button {
            display: inline-block;
            padding: 1rem 2.5rem;
            background-color: #008F7A;
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .home-button:hover {
            background-color: #23004b !important;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 1.5rem;
            }

            .title {
                font-size: 2rem;
            }

            .message {
                font-size: 1rem;
            }

            .icon {
                font-size: 6rem;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 1rem;
            }

            .title {
                font-size: 1.75rem;
            }

            .icon {
                font-size: 5rem;
            }

            .home-button {
                padding: 0.8rem 2rem;
                font-size: 1rem;
            }
          
        }
   footer,.small-header-top{
            display:none;
          }
   .small-header-top ~ .fixed-header {
     top:0px;
   }
    </style>
  <div class="container">
        <div class="icon-container">
            <i class="fas fa-lock icon"></i>
        </div>
        <h1 class="title">Restricted Access</h1>
        <p class="message">Sorry, You're not authorized to access this page. Please contact your administrator for assistance.</p>
        <a href="{{route('index')}}" class="home-button">
            Return to Website
        </a>
    </div>

@endsection