<?php
/* every-macro 杂项函数集
 * by:zhujinliang
 */

//将状态压入栈
function pushStatus($actInfo){
	global $statusStack, $status;
	$status['info'] = $actInfo;
	array_push($statusStack, $status);
	$status = array(
		'output'    => $status['output'],
		'outputdir' => $status['outputdir'],
		'filename'  => $status['filename'],
		'line'      => $status['line'],
	);
}
//从栈恢复状态
function popStatus(){
	global $statusStack, $status;
	if(count($statusStack)>0) $status = array_pop($statusStack);
	else {
		$status = array();
		errEnd('Stack Cannot Pop');
	}
}
//使用define定义项替换处理字串
function replaceDefines($instr, $phpmode = false){
	global $defines;
	$chunks = preg_split('/\b/', $instr);
	if($phpmode){
		foreach($chunks as $i => $chunk){
			if(array_key_exists($chunk, $defines))	$chunks[$i] = "'".$defines[$chunk]."'";
		}
	}else{
		foreach($chunks as $i => $chunk){
			if(array_key_exists($chunk, $defines))	$chunks[$i] = $defines[$chunk];
		}
	}
	return implode($chunks);
}
//if语句检测
function ifTest($exp){
	global $defines;
	$exp = replaceDefines($exp, true);
	//define指令判断，此处取了个巧
	//在replaceDefines中，如果已被定义，则替换为文本方式
	//此处判断括号内是否含有''符合作为是否定义标准
	$exp = preg_replace("/define\s*\(\s*\'.*\'\s*\)/", '1', $exp);
	$exp = preg_replace("/define\s*\(.*\)/", '0', $exp);

	$func = false;
	@$func = create_function('', 'return '.$exp.';');
	if($func){
		return $func();
	}else{
		errEnd('Cannot Parse Expression');
		return false;
	}
}
//在includePaths中查找文件
function findFile($name){
	global $includePaths, $status;
	foreach($includePaths as $path){
		$filename = $path.'/'.$name;
		if($path[0] != '/' && $path[1] != ':') $filename = getCurrentPath().$filename;
		if(file_exists($filename)) return $filename;
	}
	errEnd(Lang::CannotIncludeFile . $name);
	return false;
}
//使用proc_open执行命令，成功返回0
function proc_run($cmd, $stdin, &$stdout, &$err){
	$descriptorspec = array(
		0 => array("pipe", "r"),
		1 => array("pipe", "w"),
		2 => array("pipe", "w")
	);
	$process = proc_open($cmd, $descriptorspec, $pipes, getCurrentPath(), NULL);
	if(is_resource($process)){
		fwrite($pipes[0], $stdin);
		fclose($pipes[0]);		
		$stdout = stream_get_contents($pipes[1]);
		$err = stream_get_contents($pipes[2]);
		return proc_close($process);
	}
	return -1;
}
//格式化输出警告信息
function outputWranings(){
	global $outputs;	
	$i = 1;
	foreach($outputs['warnings'] as $value){
		echo $i.'. '.$value;
	}
}
//检查是否为期望值
function checkExpected($code, $info){
	global $status;
	if($status['expected'] == $code) return true;
	if($status['expected']){
		errEnd(Lang::Expected . $status['expected']);
	}else{
		errEnd($info);
	}
	return false;
}
//获取当前目录
function getCurrentPath(){
	global $status;
	return dirname($status['filename']).'/';
}

?>
