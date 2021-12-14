@extends('auth.admin')

@section('content')

<div class="container">
	<div class="row">
		<div class="col-12">        
			<h1 class="display-4">Співробітники</h1>
		</div>  
	</div>

	<div class="row">
		<div class="col-12">
			<table class="table table-hover">
				<thead>
					<tr>
						<th scope="col">П.І.Б.</th>
						<th scope="col">Категорія</th>
						<th scope="col"></th>
					</tr>
				</thead>
				<tbody id="sortable">
				@foreach($personalList as $personal)

				<tr id="{{ $personal->id }}">
					<td class="align-middle"><a href="{{ route('getPersonalEdit', ['slug' => $personal->slug] ) }}">{{ $personal->name }}</a></td>
					<td class="align-middle">{{ $personal->title }}</td>
					<td class="align-middle">
					<button data-target="#delModal" data-slug="{{$personal->slug}}" data-name="{{ $personal->name }}" data-toggle="modal" type="button" class="btn btn-outline-danger delPersonalModal">X</button></td>
				</tr>


          
				@endforeach

				</tbody>
			</table>
		</div>
	</div>

</div>



					<!-- Modal -->
					<div class="modal fade" id="delModal" tabindex="-1" role="dialog"  aria-hidden="true">
					  <div class="modal-dialog" role="document">
					    <div class="modal-content">
					      <div class="modal-header">
					        <h5 class="modal-title" id="exampleModalLabel">Увага!</h5>
					        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					          <span aria-hidden="true">&times;</span>
					        </button>
					      </div>
					      <div class="modal-body">
					       
					      </div>
					      <div class="modal-footer">
					        <button type="button" class="btn btn-secondary" data-dismiss="modal">Ні</button>
					        <button type="button" class="btn btn-danger delPersonal" data-dismiss="modal">Так</button>
					      </div>
					    </div>
					  </div>
					</div>
@endsection
