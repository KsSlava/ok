@extends('front.index')
@section('content')
<div class="container-fluid no-gutters">
  <div class="row no-gutters">
    <div class="col-12 center newsBox">
    <div class="newsBoxCat">Усі новини</div>
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
    </div>
</div>
@endsection