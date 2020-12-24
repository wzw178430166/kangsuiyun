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
          <?php if($groupid==1) { ?> <?php echo $str;?> <?php } ?>
          <table width="495" border="0" cellpadding="0" cellspacing="0">
            <tbody>
              <tr>
                <td class="formLable">每页显示：</td>
                <td class="formInput"><input name="pageNo" id="pageNo" type="text" style="width: 136px;" value="<?php echo $pageNo;?>"></td>
              </tr>
              <tr>
                <td class="formLable">订单号：</td>
                <td class="formInput"><input name="trade_sn" id="trade_sn" type="text" style="width: 136px;" value="<?php echo $trade_sn;?>"></td>
                <td class="formLable">充值状态：</td>
                <td class="formInput">
                <select name="status">
			<option value="succ" <? if($status=="succ"){ echo 'selected="selected"';}?>>成功</option>
        	<option value="waitting" <? if($status=="waitting"){echo 'selected="selected"';}?>>等待付款</option>
            <option value="" <? if($status==""){echo 'selected="selected"';}?>>所有订单</option>
        </select>
                </td>
              </tr>
              <tr>
                <td class="formLable">账号：</td>
                <td class="formInput"><input name="user" id="user" type="text" style="width: 136px;" value="<?php echo $user;?>"></td>
              </tr>
              <tr>
                <td class="formLable">游戏：</td>
                <td class="formInput">
                	<select name="game" onChange="checkserver(this.value)">
                    <option value="0">请选择游戏</option>
            		<?php $n=1;if(is_array($gameinfo)) foreach($gameinfo AS $r) { ?>
                    <option value="<?php echo $r['gameId'];?>" <?php if($game==$r['gameId']) { ?>selected="selected"<?php } ?>><?php echo $r['gameName'];?></option>      
            		<?php $n++;}unset($n); ?>
                  </select></td> 
                <td class="formLable">服务器：</td>
                <td class="formInput">
                	<select name="server">
            		<option value="--" <?php if($server=="--") { ?>selected="selected"<?php } ?>>全服</option>
            		<optgroup label="----------" id="setserver">
                    <?php if($game) { ?>
                    <?php $_game_t = $gameinfo[$game]['serve'];?>
                    <?php $n=1; if(is_array($_game_t)) foreach($_game_t AS $k => $r) { ?>
                    <?php if(is_array($r)) { ?>
                    <option value="S<?php echo $r['sid'];?>" <?php if($server==$r['sid']) { ?> selected="selected"<?php } ?>><?php echo $r['name'];?></option>  
                    <?php } else { ?>
                    <option value="s<?php echo $k;?>" <?php if($server=='s'.$k) { ?>selected="selected"<?php } ?>>s<?php echo $k;?><?php echo $r;?></option>      
                    <?php } ?>
            		<?php $n++;}unset($n); ?>
                    <?php } ?>
                    </optgroup>
                  </select></td>
              </tr>
              <tr>
                <td class="formLable"></td>
                <td class="formInput"><span class="linkbtn"> <a onClick="page_(1)"> <span class="linkbtn-left"><span class="linkbtn-text" id="nextBut">查看</span></span> </a> </span>&nbsp;<span class="linkbtn" id="showDown" style=""><a onClick="downRoleTop();"> <span class="linkbtn-left"><span class="linkbtn-text" id="nextBut">导出</span></span> </a></span></td>
              </tr>
            </tbody>
          </table>
          <input  type="hidden" value="<?=$page?>" id="page" name="page"/>
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
              <th width="85" class="sortNumeric clickable">用户ID</th>
              <th width="100" class="sortAlpha clickable">用户名</th>
              <th width="100" class="sortAlpha clickable">邮箱</th>
              <th width="85" class="sortNumeric clickable">角色名称</th>
            </tr>
          </thead>
          <tbody>
          
          <?php $n=1;if(is_array($memberinfo)) foreach($memberinfo AS $r) { ?>
          <?php if($n%2!=0) { ?>
          <tr style="background-color: rgb(247, 248, 249); "> <?php } else { ?>
          <tr style="background-color: rgb(230, 245, 255); "> <?php } ?>
            <td><div><?php echo $n;?></div></td>
            <td><?php echo $r['userid'];?></td>
            <td><?php echo $r['username'];?></td>
            <td><?php echo $r['email'];?></td>
            <td><?php echo $r['ip'];?></td>
          </tr>
          <?php $n++;}unset($n); ?>
            </tbody>
          
          <tfoot class="tableList_oneRadiusTD">
            <tr style="background-color: rgb(239, 245, 250); ">
              <td colspan="13"><div class="manu" id="showNumsDiv">
                共有<span id="sumCountDiv"><?php echo $con;?></span>条记录，当前第<?php echo $page;?>页,共<?php echo $zys;?>页&nbsp;&nbsp; 
                <?php echo $pages;?> </td>
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
function checkserver(gameid){
	$.post('index.php?m=data&a=get_server',{'gameid':gameid,'ajax':'1'},function(data){
		if(data){
			//for(var i=0;)
			$('#setserver').html();
		}	
	});
}
</script>
</body>
</html>