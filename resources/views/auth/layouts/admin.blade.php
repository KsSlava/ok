
@extends('auth.admin')

@section('content')

<div class="content">
<div class="btn_print"><a href="/{{Request::path()}}/print">Друк</a>&nbsp;&nbsp;<a href="/{{Request::path()}}/print_az">Друк (А-Я)</a></div> 
<br/>

@if($prs)

	@foreach($prs as $cat => $person)


	<div class="category">{{$cat}}</div>
	<table class="adminTable">

		@foreach($person as $p)

		<tr>
		    <td width="350px"><a href="{{route('edit')}}/{{ $p['tid'] }}">{{$p['fullname']}}</a></td>
		 	<td width="350px">{{$p['prof']}} {{$p['kkpSubprof'] !=='' ? $p['kkpSubprof'] : '' }}</td>
		    <td width="100px">{{$p['birthdate']}}</td>
		    <td width="150px">{{$p['mob']}}</td>
		    <td></td>
		</tr>

		@endforeach

</table>


	@endforeach

@endif



@if($noprs)

	<div class="category" style="background-color: #e28686;">Заповніть анкети:</div>
	<table class="adminTable">

	@foreach($noprs as $person)

		<tr>
		    <td width="350px"><a href="{{route('edit')}}/{{ $person['tid'] }}">{{$person['fullname']}}</a></td>
		</tr>

	@endforeach

</table>

@endif

{{ csrf_field() }}

</div>



@endsection