@extends('front.index')
@section('content')
<div class="container-fluid no-gutters">
    <div class="row no-gutters">
        <div class="col-12 center">
        <div class="ads">
      <div class="closeButton">
          <a href="javascript:history.go(-1)"><i class="material-icons">clear</i></a>
        </div>
            <div class="adsTitle">{{$ads->title}}</div>
            <? echo html_entity_decode($ads->description) ?>
        </div>
        </div>
    </div>
</div>
@endsection