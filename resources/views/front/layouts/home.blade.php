@extends('front.index')
@section('content')
<div class="container-fluid no-gutters">
  <div class="row no-gutters ">
    <div class="col-12 center">
      @foreach($events as $event)
      <div class="event">
        <span class="eventImage">
        @if($event['description']==1)
        <a rel="nofollow" href="{{ route('frontShow', ['slug'=> $event['slug'] ]) }}" title="{{ $event['title'] }}">
        <img width="100%" src="{{ $event['image'] }}" alt="{{ $event['title'] }}" >
        </a>
        @else
        <img width="100%" src="{{ $event['image'] }}" alt="{{ $event['title'] }}" >
        @endif
        </span>

        <span class="eventTitle"><h5>{{ $event['title'] }}</h5></span>
        <span class="eventDate">
          <span class="eventDay">{{ $event['day'] }}</span> {{ $event['month'] }} {{ $event['time'] }}
          <br/>{{ $event['price'] }}
        </span>
        <span class="eventPaylink">
          <a href="{{$event['paylink']}}" class="btn btn-danger">Купити квиток</a>
        </span>
      </div>
      @endforeach
    </div>
  </div>
  <div class="row no-gutters">
    <div class="col-12 center newsBox">
        <div class="newsBoxCat">Останні події</div>
        @foreach($news as $item)
        <div class="news">
          <span class="newsImage">
            <img src="{{$item['image']}}" alt="{{$item['title']}}" />
          </span>
          <div class="newsPublicDate">{{$item['publicDate']}}</div>
          <div class="newsTitle"><a href="{{route('frontNews', ['slug'=> $item['slug'] ]) }}" title="{{$item['title']}}"> {{$item['title']}}</a></div>
          <div class="newsIntro">{{$item['intro']}}</div>
        </div>
        @endforeach
        <div style="width: 100%; margin-top: 20px;">
        <a href="{{route('frontNewsList')}}"><button type="button" class="btn btn-outline-light">Усі новини</button></a>
        </div>
    </div>
  </div>
    <div class="row no-gutters">
    <div class="col-12 center">
    <div class="footer">
        <div class="f1"> 
        <address>
          <strong>ХОАМДТ ім. М. Куліша</strong><br>
          73000, м. Херсон, вул. Театральна, 7<br>
          Час прийому громадян з особистих питань: четвер з 14:00 до 16:00<br>
          <abbr title="Phone">Приймальня:</abbr> (0552) 22-50-93, факс: (0552) 49-22-30<br>
          <abbr title="Phone">Каса:</abbr> (0552) 22-55-20, (095)275-55-20<br>
          <abbr title="Phone">Адміністрація:</abbr> (0552) 22-55-20, (095)275-55-20<br>
          <abbr title="email">Email:</abbr> <a href="mailto:#">teatr-kulisha@ukr.net</a>
        </address>
        </div>
        <div class="f2">@facebook<br/>@instagram</div>
        <div class="f3">
          @foreach($adsCat as $a)
            <a href="{{route('adsList', ['id'=>$a->id])}}" rel="nofollow">{{$a->title}}</a><br/>
          @endforeach
        </div>
      </div>
 
    </div>
  </div>
</div>
@endsection