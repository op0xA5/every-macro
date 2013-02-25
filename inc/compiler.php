<?php

/* every-macro 解释器
 * 提供指令解析，输出控制，及错误输出功能
 * by:zhujinliang
 */

function compile($file){
	global $status, $defines, $outputs;
	
	if(!file_exists($file)) return errEnd(Lang::FileNotExists);
	$content = file($file);
	if($content === false) return errEnd(Lang::CannotReadFile);

	pushStatus('enter file');
	$status['filename'] = $file;
	$status['line'] = $lineNo = 0;
	$status['__FILE_CONTEXT'] = true;
	//此状态用以确认文件结束，检查退出文件时是否存在未配对指令

	//去除UTF-8 BOM
	$line = $content[0];
	if(ord($line[0]) == 239 && ord($line[1]) == 187 && ord($line[2]) == 191){
		$content[0] = substr($line, 3);
	}
		
	foreach($content as $line){
		$status['line'] = ++$lineNo;

		$output = true;
		
		$text = ltrim($line);
		if($text && $text[0] == '#'){
			$text = substr($text, 1);
			$argv = preg_split('/\s+/', $text, 2);
			$cmd = array_shift($argv);
			if($cmd && !commands($cmd, rtrim($argv[0]))){
				$output = false;
			}
		}else if(substr($text, 0, 3) == '//#'){
			$output = false;
		}

		if($output && $status['output']){
			$outputdir = $status['outputdir'];
			switch($outputdir){			
			case 'content':
			case 'protected':
				$line = replaceDefines($line);
				array_push($outputs[$outputdir], $line);
				break;
			case 'header':
				array_push($outputs['protected'], $line);
				break;
			}
		}
	}
	$status['line'] = $lineNo;
	//检测未封闭指令
	if(!$status['__FILE_CONTEXT']){
		if($status['expected']){
			errEnd(Lang::Expected . $status['expected']);
		}else{
			errEnd(lang::StackImbalance);
		}
	}
	popStatus();
}

function errEnd($info){
	global $statusStack, $status, $defines, $pgmFlags;

	echo "/*\r\n";
	echo Lang::CompileFailed . $info."\r\n";
	echo Lang::AtFile . realpath($status['filename']) . Lang::Line . $status['line']."\r\n";
	echo Lang::Stack . "\r\n";
	if($statusStack){
		for($i = count($statusStack)-1; $i>=0; $i--){
			$dumpStatus = $statusStack[$i];
			echo realpath($dumpStatus['filename']) . Lang::Line . $dumpStatus['line'].' ('.$dumpStatus['info'].')'."\r\n";
		}
	}
	echo Lang::Defines . "\r\n";
	if($defines) foreach($defines as $key => $value){
		echo $key;
		if($value) echo ' => '.$value;
		echo "\r\n";
	}
	echo Lang::Warning . "\r\n";
	outputWranings();
	echo Lang::EndLine . "\r\n";
	echo '*/';
	
	if($pgmFlags['keep_processed']) output();

	die();
}

function output(){
	global $outputs, $pgmFlags;
	if(count($outputs['protected'])) echo implode($outputs['protected'])."\r\n";

	if(!$pgmFlags['ignore_warning'] && count($outputs['warnings']) > 0){
		echo '/* ' . Lang::Warning . "\r\n";
		outputWranings();
		echo Lang::EndLine . " */\r\n";
	}

	echo implode($outputs['content']);
}

?>
