<?php

if(!isset($defines)) die();

//格式:
//
//$defines['NAME'] = 'VALUE';
//等同于 #define NAME VALUE
//
//$defines['FLAG'] = '';
//等同于 #define FLAG

$defines['__FILE_EXT'] = $fileExt;

//通过QueryString传入定义项
foreach($_GET as $key => $value){
	$defines[$key] = $value;
}

?>
