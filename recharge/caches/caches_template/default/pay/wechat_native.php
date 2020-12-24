<?php defined('IN_drcms') or exit('No permission resources.'); ?><!DOCTYPE html>
<!-- saved from url=(0047)https://cashier.jd.com/payment/getWeixin.action -->
<html class="hb-loaded">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="expires" content="0">
<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
<title>微信支付</title>
<link rel="stylesheet" type="text/css" href="statics/wechat_pay/main.css">
</head>
<style type="text/css">
embed[type*="application/x-shockwave-flash"], embed[src*=".swf"], object[type*="application/x-shockwave-flash"], object[codetype*="application/x-shockwave-flash"], object[src*=".swf"], object[codebase*="swflash.cab"], object[classid*="D27CDB6E-AE6D-11cf-96B8-444553540000"], object[classid*="d27cdb6e-ae6d-11cf-96b8-444553540000"], object[classid*="D27CDB6E-AE6D-11cf-96B8-444553540000"] { display: none !important; }
</style>
<script type="text/javascript" src="http://www.yidespc.com/statics/js/jquery-1.8.3.min.js"></script>
<body huaban_collector_injected="true">
<div class="shortcut">
  <div class="w">
    <!--<ul class="s-right">
      <li id="loginbar" class="s-item fore1">
        <a href="https://home.jd.com/" target="_blank" class="link-user">
        ****
        </a>
        &nbsp;&nbsp;
        <a href="https://passport.jd.com/uc/login?ltype=logout" class="link-logout">
        退出
        </a>
      </li>
      <li class="s-div">|</li>
      <li class="s-item fore2">
        <a class="op-i-ext" >
        我的订单
        </a>
      </li>
      <li class="s-div">|</li>
      <li class="s-item fore3">
        <a class="op-i-ext" target="_blank" >
        支付帮助
        </a>
      </li>
      <li class="s-div">|</li>
      <li class="s-item fore4">
        <a class="op-i-ext" target="_blank" >
        问题反馈
        </a>
      </li>
    </ul>-->
    <span class="clr"></span>
  </div>
</div>
<!-- p-header -->

<div class="p-header">
  <div class="w">
    <div id="logo">
      <img width="170" height="28" src="statics/wechat_pay/logo.png" alt="收银台">
    </div>
  </div>
</div>
<!-- p-header end -->

<div class="main">
  <div class="w">
    <!-- order 订单信息 -->
    <!-- order 订单信息 -->
    <div class="order">
      <div class="o-left">
        <h3 class="o-title"> 请您及时付款，以便订单尽快处理！    		           	订单号：<?php echo $trade_sn;?> </h3>
        <p class="o-tips">
        </p>
      </div>
      <div class="o-right">
        <div class="o-price">
          <em>应付金额</em><strong><?php echo number_format($money,2);?></strong><em>元</em>
        </div>
        <div class="o-detail" id="orderDetail">
          <a>
          订单详情<i></i>
          </a>
        </div>
      </div>
      <div class="clr"></div>
      <div class="o-list j_orderList" id="listPayOrderInfo">
        <!-- 单笔订单 -->
        
        <div class="o-list-info">
          <span class="mr10" id="shdz"></span>
          <span class="mr10" id="shr"></span>
          <span id="mobile"></span>
        </div>
        <div class="o-list-info">
          <span id="spmc"></span>
        </div>
        
        <!-- 单笔订单 end -->
        
      </div>
    </div>
    <!-- order 订单信息 end -->
    <!-- order 订单信息 end -->
    
    <!-- payment 支付方式选择 -->
    <div class="payment">
      <!-- 微信支付 -->
      <div class="pay-weixin">
        <div class="p-w-hd">微信支付</div>
        <div class="p-w-bd" style="position:relative">
         	<div class="j_weixinInfo" style="position:absolute; top: -36px; left: 130px;">距离二维码过期还剩<span class="j_qrCodeCountdown font-bold font-red">45</span>秒，过期后请刷新页面重新获取二维码。</div>
          <div  style="position:absolute; top: -36px; left: 130px; display: none" class="j_weixininfo2">二维码已过期，
            <a href="javascript:getWeixinImage();">
            刷新
            </a>
            页面重新获取二维码。</div>
          <div class="p-w-box">
            <div class="pw-box-hd">
              <img id="weixinImageURL" src="http://paysdk.weixin.qq.com/example/qrcode.php?data=<?php echo urlencode($url2);?>" width="298" height="298">
            </div>
            <div class="pw-retry j_weixiRetry" style="display: none;">
              <a class="ui-button ui-button-gray j_weixiRetryButton" >
              获取失败 点击重新获取二维码
              </a>
            </div>
            <div class="pw-box-ft">
              <p>请使用微信扫一扫</p>
              <p>扫描二维码支付</p>
            </div>
          </div>
          <div class="p-w-sidebar"></div>
        </div>
      </div>
      <!-- 微信支付 end -->
      <!-- payment-change 变更支付方式 -->
      <div class="payment-change">
        <a class="pc-wrap" id="reChooseUrl" href="javascript:window.history.back(-1);">
        <i class="pc-w-arrow-left">&lt;</i>
        <strong>选择其他支付方式</strong>
        </a>
      </div>
      <!-- payment-change 变更支付方式 end -->
    </div>
    <!-- payment 支付方式选择 end -->
  </div>
</div>
<div class="p-footer">
  <div class="pf-wrap w">
    <!--<div class="pf-line">
      <span class="pf-l-copyright">Copyright © 2004-2016  *.com 版权所有</span>
      <img width="185" height="20" src="statics/wechat_pay/footer-auth.png">
    </div>-->
  </div>
</div>
</body>
</html>
<script>
	var autoindex ;
window.setTimeout(function(){ godown();updata(); },1000);
function godown(){
	var s = Number($('.j_qrCodeCountdown').html());
	if(s>0){
		s--;
		$('.j_qrCodeCountdown').html(s);
		window.setTimeout(function(){ godown(); },1000);
	}else{
		$('.j_weixinInfo').hide();
		$('.j_weixininfo2').show();
	}
}
	
function getWeixinImage(){
	window.location.reload(); 
	return;
	$.ajax({
		'url':'./recharge/drcms/modules/pay/api/wechat_v3/native.php',
		'data':'title=<?=$title?>&money=<?=$money?>&trade_sn=<?php echo $trade_sn;?>',
		'type':'post',
		'success':function(data){
			var da =eval('(' + data + ')');
			$('#weixinImageURL').attr('src',da.src);
		},
		'error':function(){
			$('.j_weixiRetry').show();
		}
	});
}
function updata(){
	$.ajax({
		'url':'/recharge/index.php?m=pay&a=wechat_check_order',
		'data':'title=<?=$title?>&trade_sn=<?php echo $trade_sn;?>',
		'type':'post',
		'success':function(data){
			var da =eval('(' + data + ')');
			console.log(da);
	
			if(da.status=='succ'){
				window.location.reload(); 
			}
		},
		'error':function(){
			$('.j_weixiRetry').show();
		}
	});
	
	autoindex = window.setTimeout(function(){ updata(); },4000);
	
}
</script>