@extends('front.index')
@section('content')




<div class="container-fluid no-gutters">
  <div class="row no-gutters ">
    <div class="col-12 center">
      <div class="showList">
      <? $oldCatTitle = ''; ?>
      @foreach($shows as $show)
      @if( $show['scId'] <= 5)
        @if( $show['scTitle'] !== $oldCatTitle )
          <div class="showCat" >{{ $show['scTitle'] }}</div>
        @endif
               
          <div class="showLink">
          <a href="{{ route('frontShow', ['slug'=> $show['slug'] ]) }}" title="{{$show['title']}}">{{$show['title']}}</a></div>
   
        <? $oldCatTitle = $show['scTitle']; ?>
      @endif
      @endforeach

     <?php $i=0;?>
      @foreach($shows as $show)

        @if( $show['scId']>5)
          @if($i==0)
          <div class="showCat" id="openair">Театр просто неба</div>
          <div class="showListText">
          Ні для кого не секрет, що наш Херсонський обласний академічний музично-драматичний театр ім. Миколи Куліша – один з небагатьох державних театрів України, який вже 20-й рік поспіль організовує міжнародний фестиваль «Мельпомена Таврії». Це і обумовило створення нових сцен – майданчиків просто неба, адже з кожним роком фестиваль збирає все більше гостей і нам хочеться ділитись з вами їх творчістю. Так, з’явились театральні сцени біля театру, у Потьомкінському сквері, на площі Героїв, у лісі, на схилах Дніпра тощо. Для нашого театру дуже важливо бути ближчим до вас, любі глядачі, тому всі заходи, які відбуваються на грін-зоні завжди безкоштовні.<br/> <br/> 
Понад двадцять років ми практикуємо «театр просто неба», який і далі розростається різними локаціями. Наразі, у репертуарі театру, що відбувається просто неба вистави: «Кицька на спогад про темінь», «Лицар храму», «Лісова пісня», «Монолог актриси», «Get-Happy» режисера Сергія Павлюка. Окрім цього, ми маємо багато музичних програм заслуженого діяча мистецтв, режисера Ірини Корольової спільно з головним диригентом театру Артемом Філенко, які влітку театр показує і на грін-зоні.<br/> <br/> 
Тому, ми залюбки запрошуємо вас відвідувати наш «театр просто неба» і переконані, що ви не пожалкуєте!
</div>
          <?php $i++; ?>
          @endif
               
          <div class="showLink">
          <a href="{{ route('frontShow', ['slug'=> $show['slug'] ]) }}" title="{{$show['title']}}">{{$show['title']}},</a> {{$show['scTitle']}}

          </div>
       @endif
        
      @endforeach
     
     </div>
    </div>
  </div>
</div>

@endsection