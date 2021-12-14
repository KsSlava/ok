@extends('front.index')
@section('content')




<div class="container-fluid no-gutters">
  <div class="row no-gutters ">
    <div class="col-12 center">

      <? $oldCatTitle = ''; ?>
      @foreach($personal as $person)

        @if( $person['title'] !== $oldCatTitle )
          <div class="personCat" >{{ $person['title'] }}</div>
        @endif

        <div class="person">
        	<span class="personImage">
          <a href="{{ route('frontPersonal', ['slug'=> $person['slug'] ]) }}">
              <img width="100%" src="{{ $person['image'] }}" alt="{{ $person['name']}}" data-slug="{{ $person['slug']}}" >
            </a>
          </span>
        	<span class="personTitle">{{ $person['name'] }}</span>
        </div>

        <? $oldCatTitle = $person['title']; ?>

      @endforeach

    </div>
  </div>
</div>

@endsection