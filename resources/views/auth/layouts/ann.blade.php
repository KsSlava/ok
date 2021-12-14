
@extends('auth.admin')

@section('content')


<div class="content">


	<div class="btn_print"><a href="/{{Request::path()}}/print">Друк</a></div> 
	<br/>	

	<div class="category">Перелік  співробітників - ювілярів ХОАМДТ ім М. КУЛІША у <?php echo date('Y'); ?>р. </div>
@if($prs)
	<table class="adminTable">
	@foreach($prs as $p)
		<tr>
		    <td width="350px">{{$p['fullname']}}</td>
		 	<td width="350px">{{$p['prof']}} {{$p['kkpSubprof'] !=='' ? $p['kkpSubprof'] : '' }}</td>
		    <td width="100px">{{$p['birthdate']}}</td>
		    <td width="150px"><?php echo (int) date('Y') - (int) date('Y', strtotime($p['birthdate'])); ?> р.</td>
		    <td></td>
		</tr>

	@endforeach

@endif
</div>


@endsection