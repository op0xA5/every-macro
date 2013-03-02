<?php
$GLOBALS['mimetypes'] = array(
	'c' => 'text/plain',
	'css' => 'text/css',
	'h' => 'text/plain',
	'htm' => 'text/html',
	'html' => 'text/html',
	'htt' => 'text/webviewhtml',
	'stm' => 'text/html',
	'js' => 'text/javascript',
	'txt' => 'text/plain',
);

function get_mime_type($fileext){
	$result = $GLOBALS['mimetypes'][$fileext];
	if($result) return $result;
	else return 'application/octet-stream';
};
?>