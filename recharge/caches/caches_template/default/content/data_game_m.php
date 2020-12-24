<?php defined('IN_drcms') or exit('No permission resources.'); ?><?php include template('content','header_common'); ?>
<body marginwidth="0" marginheight="0">
<div class="list">
  <div class="list_1">
    <div class="list_1_1"></div>
    <div class="list_1_2" style="padding-top:1px;padding-left:11px">充值数据(具体游戏/月)：</div>
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
        
      </div>
    </div>
  </div>
  <div class="list_2">
    <div class="list_2_2">
      <div class="tablePadding"> 
      	<form action="" method="post" id="myform">
          <table width="495" border="0" cellpadding="0" cellspacing="0">
            <tbody>
              <tr>
                <td class="formLable">时间范围：</td>
                <td class="formInput" colspan="2" width="240">
					<?php echo form::date('start_time',$start_time)?>-
                    <?php echo form::date('end_time',$end_time)?>
                    <span style="color:rgb(153, 152, 152);">*每次查询只能显示最后12个月的数据</span>
                </td> 
              </tr>
              <tr>
                <td class="formLable"></td>
                <td class="formInput">
                    <span class="linkbtn">
                      <a onClick="page_(1)">
                        <span class="linkbtn-left"><span class="linkbtn-text" id="nextBut">查看</span></span>
                      </a>
                    </span>
                    &nbsp;&nbsp;
                    <span class="linkbtn" id="showDown" style="">
                      <a onClick="downRoleTop();">
                        <span class="linkbtn-left"><span class="linkbtn-text" id="nextBut">导出</span></span>
                      </a>
                    </span>
              	</td>
              </tr>
            </tbody>
          </table>
          <input  type="hidden" value="<?=$page?>" id="page" name="page"/>
        </form>
        <br>
        <br>

        <table width="100%" class="sortTable tableList" id="allTopTab" style="">
          <thead>
            <tr style="background-color: rgb(239, 245, 250); ">
              <th>游戏  \  月份</th>
              <?php $time_arr = current($data); $time = array_keys($time_arr);?>
              <?php $n=1;if(is_array($time)) foreach($time AS $k) { ?>
              <th><?php echo $k;?></th>
              <?php $n++;}unset($n); ?>
            </tr>
          </thead>
          <?php if(empty($data)) { ?>
          <tbody id="content">
              <tr> 
                <td>暂无数据</td>
              </tr>
          </tbody>
          <?php } else { ?>
          <tbody id="content">
              <?php $n=1; if(is_array($data)) foreach($data AS $game => $r) { ?>
              <tr> 
                <td><?php echo $game;?></td>
                <?php $n=1; if(is_array($r)) foreach($r AS $z => $money) { ?>
                <td><?php if($money == NULL) { ?>-<?php } else { ?><?php echo $money;?><?php } ?></td>
                <?php $n++;}unset($n); ?>
              </tr>
              <?php $n++;}unset($n); ?>
          </tbody>
          <?php } ?>
          
          
          <tfoot class="tableList_oneRadiusTD">
            <tr style="background-color: rgb(239, 245, 250); ">
              <td colspan="13"><div class="manu" id="showNumsDiv">
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
</body>
</html>

<script type="text/javascript"> 
	$(function(){  
	 	$('#content tr:even').css('background','rgb(247, 248, 249)'); 
	 	$('#content tr:odd').css('background','rgb(230, 245, 255)'); 
	}); 
</script> 