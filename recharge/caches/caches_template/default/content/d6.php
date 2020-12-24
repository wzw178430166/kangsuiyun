<?php defined('IN_drcms') or exit('No permission resources.'); ?><?php include template('content','header_common'); ?>
<body marginwidth="0" marginheight="0">
<div class="list">
  <div class="list_1">
    <div class="list_1_1"></div>
    <div class="list_1_2" style="padding-top:1px;padding-left:11px">注册数据：</div>
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
          <?php if($groupid==1) { ?> <!--<?php echo $where;?>--> <?php } ?>
          <table width="495" border="0" cellpadding="0" cellspacing="0">
            <tbody>
              <tr>
              <td class="formLable">每页显示：</td>
              <td class="formInput"><input name="pageNo" id="pageNo" type="text" style="width: 136px;" value="<?php echo $pageNo;?>">
              </td>
            </tr>
            <tr>
             <td class="formLable">账号：</td>
              <td class="formInput"><input name="user" id="user" type="text" style="width: 136px;" value="<?php echo $user;?>">
            </tr>
              <tr>
               </td>  
              <?php if($groupid==1) { ?>
            <!--<td class="formLable">推广ID：</td>
              <td class="formInput"><input name="key" id="key" type="text" style="width: 136px;" value="<?php echo $key;?>">
               </td>
             <td class="formLable">合作方ID：</td>
              <td class="formInput"><input name="plat" id="plat" type="text" style="width: 136px;" value="<?php echo $plat;?>">
               </td>  -->
              <?php } ?>
              
            </tr>
              <tr>
              <td class="formLable"></td>
              <td class="formInput"><span class="linkbtn">
              <a onClick="page_(1)">
              <span class="linkbtn-left"><span class="linkbtn-text" id="nextBut">查看</span></span>
              </a>
              </span>
                          </td>
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
              <th width="100" class="sortAlpha clickable">电脑编号</th>
              <th width="85" class="sortAlpha clickable">外网IP</th>
              <th width="85" class="sortAlpha clickable">内网IP</th>
              <?php if($groupid!=3) { ?>
              <th width="85" class="sortNumeric clickable">合作方ID</th>
               <?php } ?>
              <th width="85" class="sortNumeric clickable">进入时间</th>
              <th width="85" class="sortNumeric clickable">退出时间</th>
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
              <td><?php echo $r['key'];?></td>
              <td><?php echo $r['wip'];?></td>
              <td><?php echo $r['cip'];?></td>
              <?php if($groupid!=3) { ?>
              <td><?php echo $r['plat'];?></td>
              <?php } ?>
              <td><?php echo date("Y-m-d H:i",$r['intime']);?></td>
              <td><?php if($r['outtime']) { ?><?php echo date("Y-m-d H:i",$r['outtime']);?><?php } else { ?>-<?php } ?></td>
            </tr>
          <?php $n++;}unset($n); ?>
          </tbody>
          <tfoot class="tableList_oneRadiusTD">
            <tr style="background-color: rgb(239, 245, 250); ">
				<td colspan="8"><div class="manu" id="showNumsDiv">共有<span id="sumCountDiv"><?php echo $con;?></span>条记录，当前第<?php echo $page;?>页,共<?php echo $zys;?>页&nbsp;&nbsp; 
                <?php echo $pages;?>
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
</body>
</html>