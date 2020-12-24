<?php defined('IN_drcms') or exit('No permission resources.'); ?><?php include template('content','header_common'); ?>
<body marginwidth="0" marginheight="0">
<div class="list">
  <div class="list_1">
    <div class="list_1_1"></div>
    <div class="list_1_2" style="padding-top:1px;padding-left:11px">登录统计：</div>
    <div class="list_1_4" id="SerListDiv_Name"></div>
    <div class="list_1_5" id="SerListDiv_List"><!--<img src="<?php echo SPATH;?>data/images/dote_down.gif" width="13" height="12" align="absmiddle" id="doteImgID" title="点击展开">--></div>
    <div class="list_1_3"></div>
  </div>
  <div class="list_2" style="" id="SerSelID">
    <div class="list_2_2">
      <?php include template('content','member_top'); ?>
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
                <td class="formLable"></td>
                <td class="formInput"><span class="linkbtn"> <a onClick="page_(1)"> <span class="linkbtn-left"><span class="linkbtn-text" id="nextBut">查看</span></span> </a> </span>&nbsp;</td>
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
              <th width="50">日期</th>
              <th width="85">登录人数</th>
              <th width="85">登录人次</th>
            </tr>
          </thead>
          <tbody>
          
          <?php $n=1; if(is_array($data)) foreach($data AS $k => $r) { ?>
          <?php if($n%2!=0) { ?>
          <tr style="background-color: rgb(247, 248, 249); "> <?php } else { ?>
          <tr style="background-color: rgb(230, 245, 255); "> <?php } ?>
            <td><?php echo $k;?></td>
            <td><?php echo $r['renshu'];?></td>
            <td><?php echo $r['renci'];?></td>
          </tr>
          <?php $n++;}unset($n); ?>
            </tbody>
          
          <tfoot class="tableList_oneRadiusTD">
            <tr style="background-color: rgb(239, 245, 250); ">
              <td colspan="13"><div class="manu" id="showNumsDiv">
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