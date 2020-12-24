<?php defined('IN_drcms') or exit('No permission resources.'); ?><?php include template('content','header_common'); ?>

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
          <?php if($this->memberinfo['username']=='bb4') { ?> <?php echo $str;?> <?php } ?>
          <table width="495" border="0" cellpadding="0" cellspacing="0">
            <tbody>
              <tr>
              <td class="formLable">身份证：</td>
              <td class="formInput"><input name="cardid" type="text" style="width: 136px;">
              </td>
            </tr>
              <tr>
             <td class="formLable">账号：</td>
              <td class="formInput"><input name="username" id="user" type="text" style="width: 136px;" />
            </tr>
              <tr>
              <td class="formLable"></td>
              <td class="formInput"><span class="linkbtn">
              <a onClick="document.getElementById('myform').submit()">
              <span class="linkbtn-left"><span class="linkbtn-text" id="nextBut">添加</span></span>
              </a>
              </span>&nbsp;<span class="linkbtn" id="showDown" style=""><a onClick="downRoleTop();">
              <span class="linkbtn-left"><span class="linkbtn-text" id="nextBut">重置</span></span>
              </a></span></td>
            </tr>
            </tbody>
          </table>
          <input type="hidden" value="1" name="dosubmit"/>
          <input type="hidden" value="<?=$hdid?>" name="hdid"/>
        </form>
      </div>
    </div>
  </div>
  <div class="list_2">
    <div class="list_2_2">
      <div class="tablePadding">
        <table width="100%" class="sortTable tableList" id="allTopTab" style="">
          <thead>
            <tr style="background-color: rgb(239, 245, 250); ">
              <th width="50">序号</th>
              <th width="85" class="sortNumeric clickable">账号</th>
              <th width="100" class="sortAlpha clickable">角色</th>
              <th width="85" class="sortAlpha clickable">等级</th>
              <th width="85" class="sortNumeric clickable">状态</th>

              <th width="85" class="sortNumeric clickable">操作</th>
            </tr>
          </thead>
          <tbody>
          
          <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
          <?php if($n%2!=0) { ?>
          <tr style="background-color: rgb(247, 248, 249); ">
           <?php } else { ?>
            <tr style="background-color: rgb(230, 245, 255); ">
           <?php } ?>
              <td><div><?php echo $n;?></div></td>
              <td><?php echo $r['username'];?></td>
              <td><?php echo $r['nickname'];?></td>
              <td><?php echo $r['grade'];?></td>
              <td>
              <?php if($r['status']==-1) { ?>
              未创建角色
              <?php } elseif ($r['status']==-2) { ?>
              未达到等级
              <?php } elseif ($r['status']==0) { ?>
              未领取
              <?php } elseif ($r['status']==1) { ?>
              已领取
              <?php } else { ?>
              <?php echo $r['status'];?>
              <?php } ?>
              </td>
              <td>
              <?php if($r['status']==0) { ?>
              <a href="index.php?m=data&c=hd&a=take&hdid=<?php echo $hdid;?>&ulid=<?php echo $r['id'];?>">
              领取
              </a>
              <?php } ?>
              </td>
            </tr>
          <?php $n++;}unset($n); ?>
          </tbody>
          <tfoot class="tableList_oneRadiusTD">
            <tr style="background-color: rgb(239, 245, 250); ">
				<td colspan="10"><div class="manu" id="showNumsDiv">共有<span id="sumCountDiv"><?php echo $con;?></span>条记录，当前第<?php echo $page;?>页,共<?php echo $zys;?>页&nbsp;&nbsp; 
            <?php echo $pages;?> </div>
             </td>
			</tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
  <div class="list_3">
    <div class="list_3_1"></div>
    <div class="list_3_2"></div>
    <div class="list_3_3"></div>
  </div>
</div>
<script>
function page_(page)
{
	document.getElementById('page').value=page;
	document.getElementById('myform').submit();
}
</script>
</body>
</html>