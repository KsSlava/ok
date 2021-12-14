@extends('front.index')
@section('content')

<div class="container-fluid no-gutters">
  <div class="row no-gutters ">
    <div class="col-12 center">

      <div class="newsCard">
        <div class="closeButton">
          <a href="javascript:history.go(-1)"><i class="material-icons">clear</i></a>
        </div>
        <div class="nwTitle">"{{ $news->title }}"</div>
        <div class="nwPublicDate">{{ $news->publicDate }}</div>
        <img src="{{ $news->image }}" alt="{{ $news->title }}">
        <? echo html_entity_decode($news->description); ?>

        <div class="staticGallery">

          @foreach($news->images as $image)
          <img src="{{$image['org']}}" alt="{{$news['title']}}"/>
          @endforeach
        </div>
      </div>



    </div>
  </div>
</div>

@endsection