<?php defined('IN_drcms') or exit('No permission resources.'); ?><?php include template('content','header_common'); ?>
<link rel="stylesheet" href="<?php echo SPATH;?>data/css/MenuCss.css" type="text/css"/>
<body marginwidth="0" marginheight="0">
<div class="list">
  <div class="list_1">
    <div class="list_1_1"></div>
    <div class="list_1_2" style="padding-top:1px;padding-left:11px">充值数据：</div>
    <div class="list_1_4" id="SerListDiv_Name"></div>
    <div class="list_1_5" id="SerListDiv_List"><!--<img src="<?php echo SPATH;?>data/images/dote_down.gif" width="13" height="12" align="absmiddle" id="doteImgID" title="点击展开">--></div>
    <div class="list_1_3"></div>
  </div>
  <div class="list_2" style="display:none;" id="SerSelID">
    <div class="list_2_2">
      <div class="right">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <div class="mainHrefDiv"></div>
        <div style="clear:both"></div>
        <div class="mainHrefDiv">
          <div class="hrefDiv" onClick="changeServer('wly-86-020-gz-wanku-1001');"><font color="green">17玩酷-1001区</font></div>
        </div>
      </div>
    </div>
    <div class="list_2_2">
      <div class="operSerListDiv"></div>
    </div>
  </div>
  <div class="list_2">
    <div class="list_2_2">
      <div class="formDiv">
        <form action="" method="post" id="myform">
        <table width="495" border="0" cellpadding="0" cellspacing="0">
        <tbody>
          <tr>
            <td height="30" colspan="4" align="left" class="left_txt2" style="color:red">提示：后台充值报表只作为参考数据，一切以下载明细为准!</td>
          </tr>
          <tr>
            <td valign="middle" background="images/mail_leftbg.gif">&nbsp;</td>
            <td valign="top" bgcolor="#F7F8F9" style="padding-top:5px" align="left">
        <form action="yysListReportByRule.html" method="post" name="payForm" id="payForm" style="margin:0px;padding:0px;">
          <table width="818" border="0" cellpadding="0" cellspacing="0">
            <tbody>
              <tr>
                <td width="80" align="center"><select name="yearNum">
              <option value="2013" selected="selected">2013年</option>
              </select></td>
                <td width="55" align="center" class="left_txt2"><select name="monthNum">
                <option value="" selected="selected">请选择月份</option>
              <option value="01" <?php if($monthnum=='01') { ?>selected="selected"<?php } ?>>1月</option>
              <option value="02" <?php if($monthnum=='02') { ?>selected="selected"<?php } ?>>2月</option>
              <option value="03" <?php if($monthnum=='03') { ?>selected="selected"<?php } ?>>3月</option>
              <option value="04" <?php if($monthnum=='04') { ?>selected="selected"<?php } ?>>4月</option>
              <option value="05" <?php if($monthnum=='05') { ?>selected="selected"<?php } ?>>5月</option>
              <option value="06" <?php if($monthnum=='06') { ?>selected="selected"<?php } ?>>6月</option>
              <option value="07" <?php if($monthnum=='07') { ?>selected="selected"<?php } ?>>7月</option>
              <option value="08" <?php if($monthnum=='08') { ?>selected="selected"<?php } ?>>8月</option>
              <option value="09" <?php if($monthnum=='09') { ?>selected="selected"<?php } ?>>9月</option>
              <option value="10" <?php if($monthnum=='10') { ?>selected="selected"<?php } ?>>10月</option>
              <option value="11" <?php if($monthnum=='11') { ?>selected="selected"<?php } ?>>11月</option>
              <option value="12" <?php if($monthnum=='12') { ?>selected="selected"<?php } ?>>12月</option>
              </select></td>
                <td width="507" height="23" align="left" class="left_txt2" style="padding-left:6px"><span class="linkbtn">
              <a onClick="document.getElementById('myform').submit();">
                <span class="linkbtn-left"><span class="linkbtn-text">查看</span></span>
                </a>
              </span></td>
              </tr>
            </tbody>
          </table>
          <input type="hidden" value="1" name="dosubmit"/>
        </form>
        </td>
        <td background="images/mail_rightbg.gif">&nbsp;
        </td>
        </tr>
        <tr>
          <td valign="middle" background="images/mail_leftbg.gif">&nbsp;</td>
          <td valign="top" bgcolor="#F7F8F9" style="padding-top:10px" align="left"><!--开始--> 
            <!--数据显示区域-->
            
            <table class="tableborder sortTable" border="0" cellspacing="1" cellpadding="0" width="370" align="left" id="tab" style="font-size:12px; margin-left:10px;">
              <thead>
                <tr style="color: rgb(77, 107, 114); background-color: rgb(247, 248, 249); " title="点击排序">
                  <th width="64" height="20" style="padding-top:3px;BACKGROUND-COLOR: #cae8ea;"><div align="center">序号</div></th>
                  <th width="176" style="padding-top:3px;BACKGROUND-COLOR: #cae8ea;"><div align="center">推广ID</div></th>
                  <th width="176" style="padding-top:3px;BACKGROUND-COLOR: #cae8ea;"><div align="center">合作方ID</div></th>
                  <th width="126" style="padding-top:3px;BACKGROUND-COLOR: #cae8ea;cursor:hand;" align="center" class="sortNumeric clickable">人民币(参考值)</th>
                  <th width="176" style="padding-top:3px;BACKGROUND-COLOR: #cae8ea;"><div align="center">结算月份</div></th>
                </tr>
              </thead>
              <tbody>
                <!--加入本月-->
                <?php $total=(float)0;?>
                <?php if(empty($data)) { ?>
                    <tr style="background-color: #F7FEFF; ">
                        <td colspan="5"><div align="center">暂无数据</div></td>
                    </tr>
                <?php } else { ?>
                    <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                    <?php $total += (float)$r['total'];?>
                    <?php if($n%2>0) { ?>
                    <tr style="background-color: rgb(239, 245, 250); ">
                    <?php } else { ?>
                    <tr style="background-color: #F7FEFF; ">
                    <?php } ?>
                      <td height="25"><div align="center"><?php echo $r['id'];?></div></td>
                      <td><div align="center"><?php echo $r['key'];?></div></td>
                      <td><div align="center"><?php echo $r['plat'];?></div></td>
                      <td><div align="center"><?php echo $r['total'];?></div></td>
                      <td><div align="center"><?php echo $r['time'];?></div></td>
                    </tr>
                    <?php $n++;}unset($n); ?>
                <?php } ?>
                <!-- 显示数据完 -->
              </tbody>
              <tfoot>
                <tr style="background-color: rgb(247, 248, 249); ">
                  <td height="25" colspan="5" style="background-color: #f7f8f9; font-size:12px"><div align="center">总人民币：<font color="red"><?php echo $total;?></font></div></td>
                </tr>
              </tfoot>
            </table>
            
            <!--结束--></td>
          <td background="images/mail_rightbg.gif">&nbsp;</td>
        </tr>
        </tbody>
        </table>
        <input  type="hidden" value="<?=$page?>" id="page" name="page"/>
        </form>
      </div>
    </div>
  </div>
  <div class="list_3">
    <div class="list_3_1"></div>
    <div class="list_3_2"></div>
    <div class="list_3_3"></div>
  </div>
</div>
</body>
</html>