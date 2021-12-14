@extends('auth.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col"></div>
        <div class="col-3">
            <h3>Реєстрація</h3>
            <form method="POST" action="{{ route('postRegister') }}">
                <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                    <label for="name">ім`я</label>
                    <input class ="form-control " type="text" name="name" value="{{old('name')}}">
                </div>

                <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                    <label for="email">email</label>
                    <input class ="form-control " type="email" name="email" value="{{old('email')}}">
                </div>

                <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                    <label for="password">пароль</label>
                    <input class ="form-control" type="password" name="password" id="password">
                </div>

                <div class="form-group {{ $errors->has('password_confirmation') ? 'has-error' : '' }}">
                    <label for="password_confirmation">ще раз пароль</label>
                    <input class ="form-control" type="password" name="password_confirmation" id="password_confirmation">
                </div>

                <button type="submit" class="btn btn-outline-dark">Реєстрація</button>
                {!! csrf_field() !!}
            </form>
        </div>
        <div class="col"></div>
    </div>
</div>

@endsection

