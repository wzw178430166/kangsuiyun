<?php defined('IN_drcms') or exit('No permission resources.'); ?><?php include template('content','header_common'); ?>
<body marginwidth="0" marginheight="0">
<div class="list">
  <div class="list_1">
    <div class="list_1_1"></div>
    <div class="list_1_2" style="padding-top:1px;padding-left:11px">每日数据汇总：</div>
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
                <td class="formLable">时间范围：</td>
                <td class="formInput" colspan="2">
					<?php echo form::date('start_time',$start_time)?>-
                    <?php echo form::date('end_time',$end_time)?>
                </td> 
              </tr>
              <tr>
                <td class="formLable"></td>
                <td class="formInput"><span class="linkbtn">
              <a onClick="page_(1)">
                <span class="linkbtn-left"><span class="linkbtn-text" id="nextBut">查看</span></span>
                </a>
              </span>&nbsp;<span class="linkbtn" id="showDown" style=""><a onClick="downRoleTop();">
              <span class="linkbtn-left"><span class="linkbtn-text" id="nextBut">导出</span></span>
              </a></span></td>
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
              <th width="20%"></th>
              <th width="40%" class="sortNumeric clickable">发币数量</th>
              <th width="40%" class="sortNumeric clickable">站长收益</th>
            </tr>
          </thead>
          <tbody>
            <tr style="background-color: rgb(247, 248, 249); ">

            <td>合计收益:</td>
            <td><?php echo $point;?>&nbsp;<?php echo $cconfig['unit'];?></td>
            <td><?php echo $shouyi;?>&nbsp;元</td>
          </tr>
          </tbody>
        </table>
        <br/>
        <table width="100%" class="sortTable tableList" id="allTopTab" style="">
          <thead>
            <tr style="background-color: rgb(239, 245, 250); ">
              <th width="15%">时间</th>
              <th width="20%" class="sortNumeric clickable">完成任务数</th>
              <th width="20%" class="sortAlpha clickable">发放网站币数</th>
              <th width="20%" class="sortAlpha clickable">网站主收益</th>
              <th width="20%" class="sortAlpha clickable">当日详单查询</th>
            </tr>
          </thead>
          <tbody>
          
          <?php $n=1; if(is_array($data)) foreach($data AS $k => $r) { ?>
          <?php if($n%2!=0) { ?>
          <tr style="background-color: rgb(247, 248, 249); "> 
<?php } else { ?>
          <tr style="background-color: rgb(230, 245, 255); "> 
<?php } ?>
          	<td><?php echo $k;?></td>
            <td><?php echo $r['con'];?></td>
            <td><?php echo $r['point'];?>&nbsp;<?php echo $cconfig['unit'];?></td>
             <td><?php echo $r['point']*$cconfig['rmb'];?>&nbsp;元</td>
             <td><a href="index.php?m=data&a=cpsm&time=<?php echo $k;?>">查看详情</a></td>
          </tr>
          <?php $n++;}unset($n); ?>
          </tbody>
          <tfoot class="tableList_oneRadiusTD">
            <tr style="background-color: rgb(239, 245, 250); ">
              <td colspan="8"><div class="manu" id="showNumsDiv">
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