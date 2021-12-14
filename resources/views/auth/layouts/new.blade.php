
@extends('auth.admin')

@section('content')
<form path="/new" method="post" id="form">
<div class="content">

    <div class="category">Загальна</div>
    <div class="contGray">
        <label>Дата заповнення</label><input  id="createDate" name="createdate" type="text" size="6"  value="<?php echo date('d-m-Y') ?>">
        <label>Табельний номер</label><input id="tid" name="tid" type="text" size="7" maxlength="10" value="{{$tid}}" readonly class="{{ $errors->has('tid') ? 'error':''  }}">
        <label>ІІН</label><input id="pid" class="{{ $errors->has('pid') ? 'error':''  }}" type="text" size="7" maxlength="10" name="pid" value="{{old('pid')}}">
        <label>Стать</label>
        <select id="gen" name="gen">
            <option {{old('gen')=="" ? 'selected':''}}  value=""></option>
            <option {{old('gen')=="0" ? 'selected':''}} value="0">жіноча</option>
            <option {{old('gen')=="1" ? 'selected':''}} value="1">чоловіча</option>
        </select>
        <label>Вид роботи</label>
        <select id="typework" name="typework">
            <option {{old('typework')=="" ? 'selected':''}} value=""></option>
            <option {{old('typework')=="0" ? 'selected':''}} value="0">основна</option>
            <option {{old('typework')=="1" ? 'selected':''}} value="1">за сумнісництвом</option>
            <option {{old('typework')=="2" ? 'selected':''}} value="2">за суміщенням</option>
        </select>
        <br/>
        <br/>
        <label>призвище</label><input  id="name" class="{{ $errors->has('name') ? 'error':''  }}" name="name" type="text" size="20" maxlength="15" value="{{ old('name')}}">
        <label>ім`я</label><input  id="sname"  class="{{ $errors->has('sname') ? 'error':''  }}"  name="sname"    type="text" size="20" maxlength="15" value="{{ old('sname')}}">
        <label>по батькові</label><input  id="mname" class="{{ $errors->has('mname') ? 'error':''  }}" name="mname" type="text" size="20" maxlength="15" value="{{ old('mname')}}">
        <label>дата народження</label><input  id="birthdate" name="birthdate" type="text" size="6">
        <label>громадянство</label><input  id="nat"  name="nat"type="text" size="10">
    </div>


 

</div>

{{ csrf_field() }}
</form>





@endsection