@extends('front.index')
@section('content')

<div class="container-fluid no-gutters">
  <div class="row no-gutters ">
    <div class="col-12 center">

    <div class="personalCard">
      <div class="closeButton">
          <a href="javascript:history.go(-1)"><i class="material-icons">clear</i></a>
        </div>
      <img src="{{$personal['image']}}">
      <div class="personalName">{{ $personal['name'] }}</div>
      
      <?php 
     
      echo html_entity_decode($personal['description']) 

      ?>

    </div>



    </div>
  </div>
</div>

@endsection