<?php defined('IN_drcms') or exit('No permission resources.'); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>管理后台</title>
<link rel="stylesheet" href="<?php echo SPATH;?>data/css/main.css" type="text/css"/>
</head>
<!-- 如果登陆过 -->
<frameset rows="75,*" cols="*" frameborder="no" border="0" framespacing="0" scrolling="auto" >
  <frame src="index.php?m=data&a=toper" scrolling="no" noresize="noresize" name="top" marginwidth="0" marginheight="0" target="main"/>
  <frameset rows="*" cols="190,*" frameborder="no" border="0" framespacing="0">
    <frame src="index.php?m=data&a=lefter" name="left" scrolling="auto" noresize="noresize" marginwidth="0" marginheight="0" frameborder="0"/>
    <frame src="index.php?m=data&a=righter" name="main" scrolling="auto" marginwidth="0" marginheight="0" frameborder="0" class="rightFrameRightPad"/>
  </frameset>
</frameset>
<noframes>
<body>
</body>
</noframes>
</html>