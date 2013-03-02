<?php
/* every-macro 语言包
 *
 */

class Lang{
	
	const CompileFailed = 'Compile Failed: ';
	const AtFile        = 'At: ';
	const Line          = ' Line: ';

	const Stack         = '-------------  Stack  -------------';
	const Defines       = '------------- Defines -------------';
	const Warning       = '------------- Warning -------------';
	const EndLine       = '-----------------------------------';

	const NoFileToInclue      = 'No File To Include';
	const FileNotExists       = 'File Not Exists';
	const CannotReadFile      = 'Cannot Read File';
	const MissingDefineItem   = 'Missing Define Item'; 
	const MissingUndefineItem = 'Missing Undefine Item';
	const FindElseMissingIf   = 'Find else But Missing if';
	const FindElifMissingIf   = 'Find elif But Missing if';
	const FindEndifMissingIf  = 'Find endif But Missing if';
	const NotInRegion         = 'Find endregion But Missing region';

	const UnknownMacroCommand = 'Unknown Command: ';
	const Expected            = 'Expected: ';
	const CannotIncludeFile   = 'Cannot Include File: ';
	const StackImbalance      = 'Stack Imbalance';

	const CannotCreateTempFile = 'Cannot Create Temp File';
	const ExecFailed           = 'Exec Failed: ';
	const FailedToDumpFile     = 'Failed To Dump File: ';
	const UnknownPragmaCommand = 'Unknown Pragma Command: ';

	const UserError  = ' (USER ERROR)';

}

?>
