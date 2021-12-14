@extends('auth.layouts.master')

@section('content')


<div class="log">
<div class="login">
        

        <form method="POST" action="{{ route('postLogin') }}">

            <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                <div>email</div>
                <div><input type="email" name="email" value=""></div>
            </div>

            <br/>
            <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                <div>password</div>
                <div><input type="password" name="password" id="password"></div>
            </div>
            <br/>
            <button type="submit">Увійти</button>
            {!! csrf_field() !!}
        </form>
</div>
</div>




@endsection