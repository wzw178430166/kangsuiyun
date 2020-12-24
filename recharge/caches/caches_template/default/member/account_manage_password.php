<?php defined('IN_drcms') or exit('No permission resources.'); ?><?php include template('content','header_common'); ?>
<body marginwidth="0" marginheight="0">
<div class="list">
  <div class="list_1">
    <div class="list_1_1"></div>
    <div class="list_1_2" style="padding-top:1px;padding-left:11px">就改密码：</div>
    <div class="list_1_4" id="SerListDiv_Name"></div>
    <div class="list_1_5" id="SerListDiv_List"><!--<img src="<?php echo SPATH;?>data/images/dote_down.gif" width="13" height="12" align="absmiddle" id="doteImgID" title="点击展开">--></div>
    <div class="list_1_3"></div>
  </div>
  <div class="list_2">
    <div class="list_2_2">
      <div class="formDiv">
        <form action="" method="post" id="myform">
          <table width="495" border="0" cellpadding="0" cellspacing="0">
            <tbody>
              <tr>
              <td class="formLable">邮箱：</td>
              <td class="formInput"><input name="info[email]" id="pageNo" type="text" style="width: 136px;" value="<?php echo $this->memberinfo['email']?>">
              </td>
            </tr>
            <tr>
             <td class="formLable">原密码：</td>
              <td class="formInput"><input name="info[password]" id="password" type="text" style="width: 136px;">
            </tr>
              <tr>
            <td class="formLable">新密码：</td>
              <td class="formInput"><input name="info[newpassword]" id="newpassword" type="text" style="width: 136px;">
               </td>
             
                
            </tr>
            <tr>
            <td class="formLable">重复新密码：</td>
              <td class="formInput"><input name="info[renewpassword]" id="renewpassword" type="text" style="width: 136px;">
               </td>
             
                
            </tr>
            <tr>
              <td class="formLable"></td>
              <td class="formInput"><span class="linkbtn">
              <a onClick="document.getElementById('myform').submit();">
              <span class="linkbtn-left"><span class="linkbtn-text" id="nextBut">修改</span></span>
              </a>
              </span></td>
            </tr>
            </tbody>
          </table>
          <input  type="hidden" value="alert('1')" name="forward"/>
          <input  type="hidden" value="1" name="dosubmit"/>
        </form>
      </div>
    </div>
  </div>
  <div class="list_2">
    
  <div class="list_3">
    <div class="list_3_1"></div>
    <div class="list_3_2"></div>
    <div class="list_3_3"></div>
  </div>
</div>
<script>

</script>
</body>
</html>