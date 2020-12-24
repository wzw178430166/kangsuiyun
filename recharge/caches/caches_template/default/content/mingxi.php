<?php defined('IN_drcms') or exit('No permission resources.'); ?><?php include template('content','header_common'); ?>
<body marginwidth="0" marginheight="0">
<div class="list">
  <div class="list_1">
    <div class="list_1_1"></div>
    <div class="list_1_2" style="padding-top:1px;padding-left:11px">明细数据查询：</div>
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
        <?php if(!$time) { ?>
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
        <?php } ?>
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
              <th width="50">参加时间</th>
              <th width="85" class="sortNumeric clickable">用户ID</th>
              <th width="100" class="sortAlpha clickable">任务名称</th>
              <th width="85" class="sortAlpha clickable">虚拟货币</th>
            </tr>
          </thead>
          <tbody>
          
          <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
          <?php if($n%2!=0) { ?>
          <tr style="background-color: rgb(247, 248, 249); "> <?php } else { ?>
          <tr style="background-color: rgb(230, 245, 255); "> <?php } ?>
          	<td><?php echo date("Y-m-d H:i",$r['addtime']);?></td>
            <td><?php echo $r['username'];?></td>
            <td><?php echo $r['title'];?></td>
            <td><?php echo $r['point'];?>&nbsp;<?php echo $cconfig['unit'];?></td>
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
        <?php if($time) { ?> 
      <p style="text-align:right; cursor:pointer;"><a onClick="history.back();">
      <img src="<?php echo SPATH;?>data/images/back.png" width="15px" height="15px"/>
      返回</a></p>
      <?php } ?>
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