@extends('front.index')
@section('content')

<div class="container-fluid no-gutters">
  <div class="row no-gutters ">
    <div class="col-12 center">

      <div class="showCard">
        <div class="closeButton">
            <a href="javascript:history.go(-1)"><i class="material-icons">clear</i></a>
          </div>
        <img src="{{ $show['image'] }}" alt="{{$show['title']}}">
        <div class="showTitle">"{{ $show['title'] }}"</div>
        Жанр: <?php echo mb_strtolower($show['genre'])?><br/>Тривалість: {{$show['time']}} хв.<br/> Обмеження: {{$show['age']}}<br/><br/>
        <?php 
          echo html_entity_decode( $show['description'] ) 
        ?>
        <div class="staticGallery">
          @foreach($show['images'] as $image)
          <img src="{{$image['org']}}" alt="{{$show['title']}}"/>
          @endforeach
        </div>
      </div>

    </div>
  </div>
</div>

@endsection