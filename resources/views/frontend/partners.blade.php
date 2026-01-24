@extends('layouts.app')
@section('content')
<style>
  .our-sponsers {
    display: none;
  }
  .coming-soon-wrapper {
    min-height: calc(100vh - 149px);
    display: flex;
    justify-content: center;
    background-image: url(https://pitchburners.com/uploads/images/homepage.svg);
    background-position: bottom center;
    background-repeat: no-repeat;
    background-size: contain;
position:relative;
  }
  .coming-soon-message {
    font-size: 40px;
    color: #614092;
    text-align: center;
    padding: 20px 20px;
    border-radius: 10px;
    margin-top: 15px;
    position: absolute;
    inset: 0;
    margin: auto;
    top: 20%;
    font-family: 'Saira';
    font-weight: 500;
}
  .partners-info {
    min-height: calc(100vh - 149px);
  }
  .partners-div{
    display:grid;
    grid-template-columns:auto auto auto auto;
    align-items:center;
    max-width:100%;
  }
  @media (max-width: 767.98px) {
  .partners-div {
    grid-template-columns:auto auto;
  }
}
    @media (max-width: 425.98px) {
  .partners-div {
    grid-template-columns:auto;
  }
      .partners-item-wrap{
        margin-bottom:20px;
      }
}
.partners-info .partners-item-wrap a img {
   max-width:80%;
  }
 
  
</style>
<section class="partners-info">
 
    @if($partners->isEmpty())
    <div class="coming-soon-wrapper">
      <div class="coming-soon-message">
        "Coming soon - stay with us!"
      </div>
    </div>
    @else
   <div class="container">
    <div class="partners-wrap">
      <div class="row">
        <div class="col-12">
          <div class="partners-title "> OUR PARTNERS</div>
           <div class="partners-div">
           @foreach($partners as $partner)
          <div class="partners-item-wrap">
            
             <a href="{{ $partner->link ? $partner->link : '#' }}" target="{{ $partner->link ? '_blank' : '_self' }}"><img src="{{ config('constants.upload_url') . '/partners/' . $partner->image }}" /></a>
            
          </div>
              @endforeach
          </div>
        </div>
      </div>
    </div>
      </div>
    @endif
 
</section>
@endsection