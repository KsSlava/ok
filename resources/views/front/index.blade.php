@extends('front.layouts.master')
@section('menu')
<nav class="navbar navbar-expand-xl navbar-dark bg-dark fixed-top">
  <a class="navbar-brand" href="/" title="Театр Кулиша билеты Херсон, офіційний сайт">
    <img src="/public/src/images/template/brand_logo.jpg"  class="brandImg" alt="Театр ім. Миколи Куліша Херсон">
  </a>
  <button class="navbar-toggler" style="margin-right:4px;" type="button" data-toggle="collapse" data-target="#navResp" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navResp">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
        <a class="nav-link" href="/">ГОЛОВНА</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{route('frontPersonalList')}}">ТВОРЧИЙ СКЛАД</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{route('frontShowList')}}">РЕПЕРТУАР</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{route('frontShowList')}}#openair">ТЕАТР ПРОСТО НЕБА</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{route('frontNewsList')}}">НОВИНИ</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">УЧАСТЬ У ФЕСТИВАЛЯХ</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{route('media')}}">ЗМІ про НАС</a>
      </li>
    </ul> 
  </div>
</nav>
@endsection