<?php
/* every-macro 语言包
 *
 */

class Lang{
	
	const CompileFailed = '编译错误: ';
	const AtFile        = '在: ';
	const Line          = ' 行号: ';

	const Stack         = '------------- 调用堆栈 -------------';
	const Defines       = '------------- 定义项目 -------------';
	const Warning       = '------------- 警告信息 -------------';
	const EndLine       = '------------------------------------';

	const NoFileToInclue      = '未定义包含的文件';
	const FileNotExists       = '文件不存在';
	const CannotReadFile      = '无法读取文件';
	const MissingDefineItem   = '未定义项目'; 
	const MissingUndefineItem = '未定义项目';
	const FindElseMissingIf   = '检测到else但未找到对应的if指令';
	const FindElifMissingIf   = '检测到elif但未找到对应的if指令'
	const FindEndifMissingIf  = '检测到endif但未找到对应的if指令';
	const NotInRegion         = '检测到endregion但未找到对应的region指令';

	const UnknownMacroCommand = '未知指令: ';
	const Expected            = '未发现: ';
	const CannotIncludeFile   = '无法包含文件: ';
	const StackImbalance      = '未知错误导致栈不平衡';

	const CannotCreateTempFile = '无法创建临时文件';
	const ExecFailed           = '执行命令失败: ';
	const FailedToDumpFile     = '导出文件失败: ';
	const UnknownPragmaCommand = '未知Pragma指令: ';

	const UserError  = ' (用户错误)';

}

?>
