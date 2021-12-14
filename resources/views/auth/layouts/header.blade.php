<html>
<title>@yield('title')</title>
<head>



	<script src="{{ url('public/src/js/jq.js') }}"></script>
	
	<link rel="stylesheet" href="{{ url('public/src/css/jquery.datetimepicker.css') }}">
    <script src="{{ url('public/src/js/jquery.datetimepicker.js') }}"></script>

	<script src="{{ url('public/src/js/helper.js') }}"></script>

	<link rel="stylesheet" href="{{ url('public/src/css/admin.css') }}">
    <script src="{{ url('public/src/js/admin.js') }}"></script>

	<link rel="stylesheet" href="{{ url('public/src/css/dropzone.css') }}">
	<script src="{{ url('public/src/js/dropzone.js') }}"></script>

	<script src="{{ url('public/src/js/inputmask.js') }}"></script>


	<script type="text/javascript"> var token = '{{ Session::token() }}';</script>

<script type="text/javascript">

function jqSubmit(){

    console.log('2')

    $("form").submit();

}

$(document).ready(function(){
    
$("form").submit(function() {

        console.log('1')
        $(this).find(":input").filter(function(){ return !this.value; }).attr("disabled", "disabled");
        return true; // ensure form still submits
    });





  })
</script>


</script>
</head>

<body>

