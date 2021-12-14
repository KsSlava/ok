@extends('front.index')
@section('content')
<div class="container-fluid no-gutters">
  <div class="row no-gutters ">
    <div class="col-12 center">
      <div class="massMediaList">
      <div class="massMediaTitle">ЗМІ про нас</div>
        @foreach($massMedia as $item)
          <div class="massMediaItem"><? echo html_entity_decode($item['link']) ?></div>
        @endforeach 
      </div>    
    </div>
  </div>
</div>
@endsection