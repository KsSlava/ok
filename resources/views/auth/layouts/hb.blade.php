
@extends('auth.admin')

@section('content')

<!-- <h1>Перелік дней народжень співробітників ХОАМДТ ім М. КУЛІША</h1> -->
<div class="content">

 <?php 

	$mns = ['Січень', 'Травень', 'Березень', 'Квітень',	'Травень', 'Червень', 'Липень',	'Серпень', 'Вересень','Жовтень','Листопад', 'Грудень'];

	$i=1;
	foreach ($mns as $m) {


		if($i==$slug){

			$m = '<b>'.$m.'</b>';
		}


		echo '<span class="hb_months"><a href="'.route("hb").'/'.$i.'">'.$m.'</a></span>';


		$i++;





		
	}


?>
	<div class="btn_print"><a href="/{{Request::path()}}/print">Друк</a></div> 
	<br/>	<br/>

	<div class="category">Перелік дней народжень співробітників ХОАМДТ ім М. КУЛІША з {{$b}} по {{$e}}  </div>
@if($prs)
	<table class="adminTable">
	@foreach($prs as $p)
		<tr>
		    <td width="350px">{{$p['fullname']}}</td>
		 	<td width="350px">{{$p['prof']}} {{$p['kkpSubprof'] !=='' ? $p['kkpSubprof'] : '' }}</td>
		    <td width="100px">{{$p['birthdate']}}</td>
		    <td width="150px"></td>
		    <td></td>
		</tr>

	@endforeach

@endif
</div>


@endsection