@extends('auth.layouts.master')
@section('menu')

   <nav>
      <div> 

      <a href="{{route('admin')}}">КАДРОВИЙ ОБЛІК ХОАМДТ ім М.Куліша - <?php echo date('Y')?></a>


      @if(route::currentRouteName() == 'admin')
      <a class="menu" href="{{route('new')}}">Створити</a>
      <a class="menu" href="{{route('archive')}}">Архів</a>
      <a class="menu" href="{{route('hb')}}/<?php echo date('m'); ?>">Дні народження</a>
      <a class="menu" href="{{route('ann')}}">Ювілеї</a>
      @endif


      @if(route::currentRouteName() == 'new' or route::currentRouteName() == 'edit')
      <a class="menu" href="#"  onclick="jqSubmit()">Зберегти</a>
      <a class="menu" href="{{route('admin')}}">Закрити</a>
      @endif



      <span>
      {{ Auth::user()->name }}
      <a href="{{route('getLogout')}}">вийти</a>
      </span>

      </div>

    </nav>


{!! csrf_field() !!}
@endsection

