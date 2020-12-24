<?php defined('IN_drcms') or exit('No permission resources.'); ?><?php include template('content','header_common'); ?>
<body marginwidth="0" marginheight="0" class="left_body">
<div class="left_Menu"> 
  <!-- 加载左部 -->
  <div> 
    <!-- 公告管理 -->
    <h1 onClick="display(5)" title="点击折叠"><a href="javascript:void(0)">任务数据查询</a></h1>
    <div id="menuDiv5">
      <ul class="MM">
        <li><a  href="javascript:void(0)" class="f_gray" target="main">明细数据查询</a></li>
        <li><a  href="javascript:void(0)" class="f_gray" target="main">访问统计</a></li>
      </ul>
    </div>
    
     <h1 onClick="display(6)" title="点击折叠"><a href="javascript:void(0)">任务收入查询</a></h1>
         <div id="menuDiv6">
      <ul class="MM">
        <li><a href="javascript:void(0)" class="f_gray" target="main">汇总数据</a></li>
        <li><a href="javascript:void(0)" class="f_gray" target="main">每日数据汇总</a></li>
        <li><a href="javascript:void(0)" class="f_gray" target="main">每月数据汇总</a></li>
      </ul>
    </div>
    
    <h1 onClick="display(1)" title="点击折叠"><a href="javascript:void(0)">运营数据查询</a></h1>
    <div id="menuDiv1">
      <ul class="MM">
        <li><a href="index.php?m=data&a=d5" target="main">查看注册数据</a></li>
        <li><a href="index.php?m=data&a=r2" target="main">查看充值数据</a></li>
      <li><a href="index.php?m=data&a=r2_m" target="main">按月查看充值数据<span style="color:#C00"></span></a></li>  
        <?php if($this->memberinfo['from']==1) { ?>
        <li><a href="index.php?m=data&a=d6" target="main">弹窗数据查看<span style="color:#C00"></span></a></li>
        <?php } ?>
      </ul>
    </div>
    <?php if($this->memberinfo['groupid']==1) { ?>
    <h1 onClick="display(5)" title="点击折叠"><a href="javascript:void(0)">管理员功能</a></h1>
    <div id="menuDiv5">
      <ul class="MM">
        <li><a href="index.php?m=data&a=re" target="main">查看总充值数据</a></li>       
        <li><a href="index.php?m=data&a=data_game_d" target="main">充值数据（具体游戏/日）</a></li>
        <li><a href="index.php?m=data&a=data_game_m" target="main">充值数据（具体游戏/月）</a></li>
        <li><a href="index.php?m=data&c=member" target="main">用户统计</a></li>
        
        
        <li><a href="index.php?m=data&a=duigame" target="main">订单号查找游戏名称</a></li>
        
         <li><a href="index.php?m=data&c=admin_member_mamage" target="main">管理推广员</a></li>
      </ul>
    </div>
    <?php } ?>
    <h1 onClick="display(2)" title="点击折叠"><a href="javascript:void(0)">推广代码</a></h1>
    <div id="menuDiv2">
      <ul class="MM">
       <?php if($this->memberinfo['groupid']!=3) { ?>
      <li><a href="index.php?m=data&a=get_link"  target="main">获取推广链接</a></li>
      <?php } ?>
       <li><a href="index.php?m=data&c=hd" target="main">活动专区</a></li>
      <!-- <li><a href="javascript:vold(0)" onClick="alert('暂未开通')" target="main"><span style="color:#999">获取广告</span></a></li>-->
       <li><a href="index.php?m=data&a=get_link&ac=hd" target="main">活动推广专题页</a></li>
      </ul>
    </div>
    
    <h1 onClick="display(3)" title="点击折叠"><a href="javascript:void(0)">账号管理</a></h1>
    <div id="menuDiv3">
      <ul class="MM">
     	 <li><a href="index.php?m=member&c=index&a=account_manage_password&t=2" target="main">修改邮箱/密码</a></li>
       <!-- <li><a href="javascript:vold(0)" onClick="alert('暂未开通')" target="main"><span style="color:#999">用户资料</span></a></li>
       -->
        <?php if(($this->memberinfo['username']=='bb4' || $this->memberinfo['from']==1) && $this->memberinfo['groupid']<=2) { ?>
        <li><a href="index.php?m=data&a=d6_2" target="main"><span>二级数据查询</span></a></li>
        <?php } ?>
      </ul>
    </div>
    
    <h1 onClick="display(4)" title="点击折叠"><a href="javascript:void(0)">帮助</a></h1>
    <div id="menuDiv4">
      <ul class="MM">
        <li><a href="index.php?m=data&a=kefu" target="main">联系客服</a></li>  
        <li><a href="index.php?m=data&a=fankui" target="main">意见反馈</a></li> 
        <li><a href="index.php?m=data&a=aboutus" target="main">关于我们</a></li>       
      </ul>
    </div>
    <div class="center_end"></div>
  </div>
</div>
<!-- 安全退出 --> 
<script language="javascript" src="<?php echo JS_PATH;?>jquery.min.js"></script>
</body>
</html>