<?php

/* every-macro 程序控制(pargma)指令集
 * by:zhujinliang
 */
function pragmaCmds($cmd, $argv){
	global $pgmFlags, $outputs, $includePaths;
	$execAppend = false;

	switch($cmd){
	case 'keep_processed':
	case 'ignore_exec_error':
	case 'ignore_warning':
		$pgmFlags[$cmd] = ($argv=='off') ? false : true;
		break;
	case 'set_time_limit':
		$argv = intval($argv);
		if($argv) set_time_limit($argv);
		break;
	case 'exec':
		$tmpFile = '';
		if(strpos($argv, '%s') !== false){
			$tmpFile = tempnam('every-macro', 'TMP');
			if(!$tmpFile) return errEnd(Lang::CannotCreateTempFile);
			file_put_contents($tmpFile, implode($outputs['content']));
		}
		$argv = str_replace('%s', $tmpFile, $argv);
		$status = proc_run($argv, NULL, $stdout, $err);
		if($status){
			errEnd(Lang::ExecFailed . 'code '.$status.' cmd: '.$argv."\r\n".$err."\r\n");
		}
		break;
	case 'exec_to_output':
		$execAppend = true;
	case 'exec_as_output':
		$tmpInFile = ''; $tmpOutFile = '';
		if(strpos($argv, '%s') !== false){
			$tmpInFile = tempnam('every-macro', 'TMP');
			if(!$tmpInFile) return errEnd(Lang::CannotCreateTempFile);
			file_put_contents($tmpInFile, implode($outputs['content']));
		}
		if(strpos($argv, '%d') !== false){
			$tmpOutFile = tempnam('every-macro', 'TMP');
			if(!$tmpOutFile) return errEnd(Lang::CannotCreateTempFile);
			file_put_contents($tmpInFile, '');
		}
		$argv = str_replace('%s', $tmpInFile, $argv);
		$argv = str_replace('%d', $tmpOutFile, $argv);
		$stdin = $tmpInFile ? NULL :  implode($outputs['content']);
		$status = proc_run($argv, $stdin, $stdout, $err);
		
		if($status){
			errEnd(Lang::ExecFailed . 'code '.$status.' cmd: '.$argv."\r\n".$err."\r\n");
		}else{
			if($tmpOutFile){
				$stdout = file_get_contents($tmpOutFile);
			}
			if($execAppend){
				array_push($outputs['content'], $stdout);
			}else{
				$outputs['content'] = array($stdout);
			}
		}
		break;
	case 'add_include_path':
		array_unshift($includePaths, $argv);
		break;
	case 'dump':
		$pgmFlags['dump'] = $argv;
		break;
	case 'charset':
		$argv = "Content-type: text/plain; charset=".$argv;
	case 'header':
		if(isset($pgmFlags['ext_header'])) array_push($pgmFlags['ext_header'], $argv);
		else $pgmFlags['ext_header'] = array($argv);
		break;
	default:
		errEnd(Lang::UnknownPragmaCommand . $cmd);
		break;
	}
}

?>
