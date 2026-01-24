<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pitch Burners</title>
    <link rel="icon" href="{{ asset('uploads/images/logo.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- <link rel="stylesheet" href="https://mdbgo.io/marta-szymanska/mdb5-demo-pro-design-blocks/css/mdb.min.css" />
    <link rel="stylesheet" href="https://mdbgo.io/marta-szymanska/mdb5-demo-pro-design-blocks/dev/css/new-prism.css" />--}}
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"  crossorigin="anonymous">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  </head>
  <body class="addnewplayer-body">
    {{-- @if(request()->routeIs('coming-soon')) --}}
    <img class="bg-sticky left-position" src="{{ asset('uploads/images/batsman2.png') }}"/>
    <section class="small-header-top">
      <div class="container h-100">
        <div class="row h-100">
          <div class="col-12 h-100 d-flex align-items-center justify-content-center justify-content-md-end">
            <p>CIN:U85410TZ2023NPL029681</p>
            <ul>
              <li class="mail"><i><img src="{{ asset('uploads/images/email.svg')}}" /></i><a href="mailto:cricket@pitchburners.com">cricket@pitchburners.com</a></li>
              <li class="phone"><i><img src="{{ asset('uploads/images/telephone.svg')}}" /></i><a href="tel:+91 99628 51516">+91 99628 51516</a></li>
              <li class="insta"><i><img src="{{ asset('uploads/images/video.svg')}}" /></i><a href="https://www.instagram.com/pitchburners_sports_foundation?igsh=bHhsdmtvbnJobjht" target="_blank">Pitchburners</a></li>
            </ul>
          </div>
        </div>
      </div>
    </section>
 {{--   @endif --}}
    <header class="fixed-header" >
      <nav class="navbar navbar-expand-xl h-100">
          <div class="container">

             <a class="navbar-brand" href="{{route('index')}}" >
              <img width="250" src="{{ asset('uploads/images/pitchburners-new-logo1.png') }}" />
            </a>

            </div>
          </div>
        </header>
        @yield('content')
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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"  crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"  crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
     <script>
      $(document).ready(function(){
        $( '#multiple-select-cityfield').select2( {
          width: '100%',
        } );
        $( '#multiple-select-groundfield' ).select2( {
          width: '100%',
        } );
      });
      </script>

  <script>$(document).ready(function(){
    $('.marquee-slider').slick({
      slidesToShow: 3,        // Number of items visible at a time
      slidesToScroll: 1,       // Number of items to scroll at a time
      autoplay: true,          // Enable auto-play
      autoplaySpeed: 0,        // Set speed for continuous scrolling
      speed: 5000,             // Speed of the scrolling (adjust as needed)
      cssEase: 'linear',       // Makes the scroll linear like a marquee
      infinite: true,          // Infinite loop
      arrows: false,           // Hide arrows if not needed
      variableWidth: true      // Adjust item width dynamically
    });
  });

</script>
    <script src="{{ asset('js/custom.js') }}" type="text/javascript"></script>
  </body>
</html>
