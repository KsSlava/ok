<?php 
error_reporting(~0);
ini_set('display_errors', 1);
$a = file_get_contents('https://widget.kontramarka.ua/uk/widget211site11172/widget/index'); 

print_r($a);
?>