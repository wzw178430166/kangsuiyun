<?php
/**
 *  index.php drcms 入口
	2010-6-1
 */
 //drcms根目录

define('drcms_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);

include drcms_PATH.'/drcms/base.php';

pc_base::creat_app();

?>