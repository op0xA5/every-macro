<?php
/* every-macro 宏指令命令集
 * by:zhujinliang
 */

function commands($cmd, $argv){
	global $status, $defines, $outputs, $pragmas;
	switch($cmd){
		case 'include':
			if(!$status['output']) return;
			$argv = str_replace('"', '', $argv);
			$argv = str_replace("'", '', $argv);
			$argv = str_replace('<', '', $argv);
			$argv = str_replace('>', '', $argv);
			if(!$argv) return errEnd(Lang::NoFileToInclue);
			$file = findFile($argv);
			if($file) compile($file);
			break;
		case 'define':
			if(!$status['output']) return;
			if($argv){
				$argv = preg_split('/\s+/', $argv, 2);
				$key = $argv[0]; $value = $argv[1];
				$defines[$key] = replaceDefines($value);
			}else{
				errEnd(Lang::MissingDefineItem);
			}
			break;
		case 'undef':
			if(!$status['output']) return;
			if($argv){
				if(array_key_exists($argv, $defines)) unset($defines[$argv]);
			}else{
				errEnd(Lang::MissingUndefineItem);
			}
			break;
		case 'ifdef':
			$op = array_key_exists($argv, $defines);
			pushStatus('ifdef check: '.$key.' => '.($op ? 'true' : 'false'));
			$status['expected'] = 'endif';
			$status['output'] = $status['output'] && $op;
			//如果当前状态为禁止输出，则必须继承该状态，保持禁止输出状态
			$status['ifop'] = $op;
			break;
		case 'ifndef':
			$op = !array_key_exists($argv, $defines);
			pushStatus('ifndef check: '.$argv.' => '.($op ? 'true' : 'false'));	
			$status['expected'] = 'endif';
			$status['output'] = $status['output'] && $op;
			$status['ifop'] = $op;
			break;		
		case 'if':
			$op = ifTest($argv);
			pushStatus('if check: "'.$argv.'" => '.($op ? 'true' : 'false'));	
			$status['expected'] = 'endif';
			$status['output'] = $status['output'] && $op;
			$status['ifop'] = $op;
			break;
		case 'else':
			if(checkExpected('endif', Lang::FindElseMissingIf)){
				$op = !$status['ifop'];
				popStatus();
				pushStatus('if-else => '.($op ? 'true' : 'false'));
				$status['expected'] = 'endif';
				$status['output'] = $status['output'] && $op;
				$status['ifop'] = $op;
			}
			break;
		case 'elif':
			if(checkExpected('endif', Lang::FindElifMissingIf)){				
				$op = !$status['ifop'];
				popStatus();
				pushStatus('elif => '.($op ? 'enter' : 'pass'));
				$status['expected'] = 'endif';
				$status['output'] = $status['output'] && $op;
				$status['ifop'] = $op;
				$status['implicitEndif'] = true;  //添加一个隐式的endif
				
				$op = $op && ifTest($argv);
				pushStatus('if check: "'.$argv.'" => '.($op ? 'true' : 'false'));
				$status['expected'] = 'endif';
				$status['output'] = $status['output'] && $op;
				$status['ifop'] = $op;
			}
			break;
		case 'endif':
			if(checkExpected('endif', Lang::FindEndifMissingIf)){
				do{
					popStatus();
				} while($status['implicitEndif']);  //处理隐式endif
			}
			break;
		case 'warning':
			if(!$status['output']) return;
			array_push($outputs['warnings'],
				$argv.'  @'.realpath($status['filename']).':'.$status['line']."\r\n");
			break;
		case 'error':
			if(!$status['output']) return;
			errEnd($argv.Lang::UserError);
			break;
		case 'pragma':
			if(!$status['output']) return;
			$argv = preg_split('/\s+/', $argv, 2);
			$cmd = strtolower($argv[0]); $argv = replaceDefines($argv[1]);
			pragmaCmds($cmd, $argv);
			break;
		case 'region':
			pushStatus('enter region: '.$argv);
			$status['expected'] = 'endregion';
			switch($argv){
				case '_REM_':
					$status['output'] = false;
					break;
				case '_PROTECTED_':
					$status['outputdir'] = 'protected';
					break;
				case '_HEADER_':
					$status['outputdir'] = 'header';
					break;
				case '_MACRO_':
					$status['outputdir'] = 'macro';
					break;
			}		
			break;
		case 'endregion':
			if(checkExpected('endregion', Lang::NotInRegion)){
				popStatus();
			}
			break;
		default:
			return -1;
	}
}


?>
