<?php defined('IN_drcms') or exit('No permission resources.'); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="<?php echo SPATH;?>data/css/main.css" type="text/css"/>
<style>
.redbor{ border:1px #CC0000 solid;}
</style>
<script language="javascript" src="<?php echo JS_PATH;?>jquery.min.js"></script>
<script>
function jssubmit(){
	document.body.style.cursor = 'wait';	
	$('a').css('cursor','wait');
	window.onclick=function(){return false;}
	return 'return false';	
}
function page_(page)
{
	document.getElementById('page').value=page;
	jssubmit();
	document.getElementById('myform').submit();
}
</script>
</head>