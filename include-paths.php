<?php

/* 引用路径，当需要引用文件时，将依列表进行查找
 * 绝对路径需以/开头或盘符开头(windows)
 */

$includePaths = array(
	'.',
	'inc',
	'src',
	dirname(__FILE__).'/tools'
);

?>
