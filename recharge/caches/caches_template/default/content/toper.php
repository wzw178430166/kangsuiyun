<?php defined('IN_drcms') or exit('No permission resources.'); ?><?php include template('content','header_common'); ?>
<body marginwidth="0" marginheight="0">
<div class="top">
  <div class="top_1"><a href="SerMain.html" class="top_1_map" target="_top"></a></div>
  <div class="top_2">
    <div class="top_2_3"><a href="javascript:void(0)" target="_self" onclick="logout();"><img src="<?php echo SPATH;?>data/images/out.gif" alt="安全退出" width="46" height="20" border="0"></a></div>
    <div class="top_2_2"><a href="SerMain.html" target="_top"><img src="<?php echo SPATH;?>data/images/toSonMain.gif" alt="返回主界面" border="0"></a></div>
    <div class="top_2_1">当前管理员:
    <?php if($this->memberinfo['nickname']) { ?>
    <?php echo $this->memberinfo['nickname']?>
    <?php } else { ?>
    <?php echo $this->memberinfo['username']?>
    <?php } ?>
    </div>
  </div>
</div>
<script language="JavaScript">
	function logout(){
		if (confirm('您确认要退出后台吗？'))
		top.location = "index.php?m=member&a=logout";
		return false;
	}
</script> 
<script language="javascript" src="<?php echo JS_PATH;?>jquery.min.js"></script>
<div id="topNotic" style="display:none;font-size: 12px;" align="center" class="topNoticA"></div>
</body>
</html>