<?php
/* 用以对任意文件提供类C预编译指令操作
 * by:zhujinliang 2013.01.22
 * 
 * 需要对服务器做Rewrite，将希望被处理的文件类型转向本文件
 */

$base_path = 'D:\wwwroot';
//网站根目录

$fileName = rawurldecode($_SERVER['REQUEST_URI']);
if($tmplen = strpos($fileName, '?')) $fileName = substr($fileName, 0, $tmplen);
$fileExt = strtolower(array_pop(explode('.', $fileName)));

$currentFile = realpath($base_path.$fileName);
if($currentFile == __FILE__) die('Hello, this is every-macro');

//入口文件完整路径

if(file_exists('include-paths.php')) require('include-paths.php');
else $includePaths = array('.');
//初始化include-path

$defines = array();    //define定义项
$pgmFlags = array();   //程序处理标志
$outputs = array(      //输出内容缓冲
	'content'   => array(),
	'protected' => array(),
	'warnings'  => array(),
);
if(file_exists('sys-defines.php')) require('sys-defines.php');

$statusStack = array();  //状态栈
$status = array(
	'output'    => true,       //输出开关
	'outputdir' => 'content',  //输出目标
	'filename'  => '',         //当前处理文件名
	'line'      => 0,          //当前处理行数
	'info'      => '',         //信息
	'expected'  => ''          //当前环境
);

require('inc/lang.php');
require('inc/compiler.php');
require('inc/commands.php');
require('inc/pragmacmds.php');
require('inc/misc.php');
require('mimetypes.php');

//------- 中文编码补丁 -------

if(!file_exists($currentFile)){
	$currentFile = iconv('UTF-8', 'GB2312', $currentFile);
}

//--------- 编译入口 ---------

compile($currentFile);

//--------- 输出过程 ---------
header('Content-Type: '.get_mime_type($fileExt), true);
if($pgmFlags['ext_header']) foreach($pgmFlags['ext_header'] as $value){
	header($value, true);
}

ob_start();
output();

//--------- 处理dump ---------
if($pgmFlags['dump']){
	$handle = fopen(dirname($currentFile).'/'.$pgmFlags['dump'], 'w');
	if($handle){
		fwrite($handle, ob_get_contents());
		fclose($handle);
	}else{
		errEnd(Lang::FailedToDumpFile . $argv);
	}
}
ob_end_flush();

