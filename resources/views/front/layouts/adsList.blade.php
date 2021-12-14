@extends('front.index')
@section('content')
<div class="container-fluid no-gutters">
  <div class="row no-gutters">
    <div class="col-12 center">
      <div class="adsList">
              <div class="closeButton">
          <a href="{{ route('getIndex') }}"><i class="material-icons">clear</i></a>
        </div>
        <div class="adsCat">{{$adsListCat->title}}</div>

        @foreach($adsList as $ads)
        <a rel="nofollow" href="{{route('ads', ['slug'=>$ads->slug])}}">{{$ads->title}}</a><br/>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endsection