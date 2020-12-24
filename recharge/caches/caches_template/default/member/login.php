<?php defined('IN_drcms') or exit('No permission resources.'); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>e8游戏数据后台管理系统</title>
<link rel="stylesheet" href="<?php echo SPATH;?>data/css/main.css" type="text/css">
</head>
<body style="background-color: #1D3647;">
<div style="width:100%; height:166px;">
  <div class="login_one">
    <div class="languages">
      <!--<div class="languageDiv"><a href="http://wlymanager.uqee.com/index_ko.html">韩语</a></div>-->
    </div>
  </div>
  <div class="login_two">
    <div class="login_two_one">
      <div class="login_two_one_1">
        <p><img src="<?php echo SPATH;?>data/images/logo.png" width="279" height="68"> <span class="login_leftTxt">e8游戏后台管理系统V2</span></p>
      </div>
    </div>
    <div class="login_two_two">
      <div class="height" style="height:394px;">
        <div class="login_two_two_1">
        <form action="" method="post" id="myform">
          <table class="login_txt" border="0" cellspacing="0" cellpadding="0" width="434" height="138/">
            <tbody>
              <tr>
                <td height="38" width="112" align="right">用户名：</td>
                <td>&nbsp;</td>
                <td colspan="2"><input id="username" name="username" maxlength="20" type="text" style="width:160px;"></td>
              </tr>
              <tr>
                <td height="38" align="right">密码： </td>
                <td>&nbsp;</td>
                <td colspan="2"><input id="password" name="password" maxlength="20" type="password" style="width:160px">
                  &nbsp;<img src="<?php echo SPATH;?>data/images/luck.gif" width="19" height="18"></td>
              </tr>
              <!--<tr>
                <td height="38" align="right">验证码：</td>
                <td>&nbsp;</td>
                <td colspan="2"><input id="codes" name="codes" maxlength="10" type="text" style="width:100px">
                  &nbsp;<img src="<?php echo SPATH;?>data/images/ValidateCodeImage" class="CodeImgCssClass" name="randImage" id="randImage" align="absMiddle" width="55" height="20" onclick="javascript:loadimage();" alt="点击更换验证码">
                 
                  </td>
              </tr>-->
              <tr>
                <td height="38" align="right">记住密码：</td>
                <td>&nbsp;</td>
                <td colspan="2"><select name="rememberPswd" id="rememberPswd" style="width:108px">
                    <option value="0">不保存</option>
                    <option value="1">保存1小时</option>
                    <option value="6">保存6小时</option>
                    <option value="12">保存12小时</option>
                    <option value="24">保存一天</option>
                    <option value="168">保存一周</option>
                  </select></td>
              </tr>
              <tr>
                <td height="40">&nbsp;</td>
                <td width="12"></td>
                <td width="76"><span class="linkbtn"><a onclick="login();"><span class="linkbtn-left"><span class="linkbtn-text">登陆</span></span></a></span></td>
                <td width="234"><span class="linkbtn"><a onclick="resetLogin();"><span class="linkbtn-left"><span class="linkbtn-text">取消</span></span></a></span></td>
              </tr>
            </tbody>
          </table>
          <input type="hidden" value="1" name="dosubmit" />
          </form>
        </div>
      </div>
      <div class="login_two_three"><img src="<?php echo SPATH;?>data/images/login-wel.png" width="242" height="138"></div>
    </div>
  </div>
  <div class="login_three"><span class="login-buttom-txt">广州联竣公司版权所有&nbsp;&nbsp;Copyright © 2010-2012 </span></div>
</div>
<script language="javascript" src="<?php echo JS_PATH;?>jquery.min.js"></script>
<script language="javascript">
	document.onkeypress=function(e) {
	    var code;  
	    if(!e){var e=window.event;}  
	    if(e.keyCode){code=e.keyCode;}else if(e.which){code=e.which;}
	    if(code==13){
	    	login();
	    }
	}
	function login(){
		if ($('#username').val().length == 0) {
			alert('请输入用户名!');
		} else if ($('#password').val().length == 0) {
			alert('请输入密码!');
		}/* else if ($('#codes').val().length == 0) {
			alert('请输入验证码!');
		}*/else {
			document.getElementById('myform').submit();
			/*
			$.post("http://wlymanager.uqee.com:80/loginAction.html", {"username" : $('#username').val(),"password" : $('#password').val(),"codes" :$('#codes').val(),"rememberPswd":$('#rememberPswd').val()}, function(data) {
				if(data.indexOf('.html')==-1){
					alert(data);
				}else{
					window.location='http://wlymanager.uqee.com:80/'+data;
				}
			});
		*/}
	}
</script>
</body>
</html>