<?php defined('IN_drcms') or exit('No permission resources.'); ?><html>
<head>
<meta http-equiv="content-type" content="text/html;charset=utf8">
<meta id="viewport" name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1; user-scalable=no;">
<title>微信支付</title>

<style type="text/css">
/* 重置 [[*/
body,p,ul,li,h1,h2,form,input{margin:0;padding:0;}
h1,h2{font-size:100%;}
ul{list-style:none;}
body{-webkit-user-select:none;-webkit-text-size-adjust:none;font-family:Helvetica;background:#ECECEC;}
html,body{height:100%;}
a,button,input,img{-webkit-touch-callout:none;outline:none;}
a{text-decoration:none;}
/* 重置 ]]*/
/* 功能 [[*/
.hide{display:none!important;}
.cf:after{content:".";display:block;height:0;clear:both;visibility:hidden;}
/* 功能 ]]*/
/* 按钮 [[*/
a[class*="btn"]{display:block;height:42px;line-height:42px;color:#FFFFFF;text-align:center;border-radius:5px;}
.btn-blue{background:#3D87C3;border:1px solid #1C5E93;}
.btn-green{background-image:-webkit-gradient(linear, left top, left bottom, color-stop(0, #43C750), color-stop(1, #31AB40));border:1px solid #2E993C;box-shadow:0 1px 0 0 #69D273 inset;}
/* 按钮 [[*/
/* 充值页 [[*/
.charge{font-family:Helvetica;padding-bottom:10px;-webkit-user-select:none;}
.charge h1{height:44px;line-height:44px;color:#FFFFFF;background:#3D87C3;text-align:center;font-size:20px;-webkit-box-sizing:border-box;box-sizing:border-box;}
.charge h2{font-size:14px;color:#777777;margin:5px 0;text-align:center;}
.charge .content{padding:10px 12px;}
.charge .select li{position:relative;display:block;float:left;width:100%;margin-right:2%;height:150px;line-height:150px;text-align:center;border:1px solid #BBBBBB;color:#666666;font-size:16px;margin-bottom:5px;border-radius:3px;background-color:#FFFFFF;-webkit-box-sizing:border-box;box-sizing:border-box;overflow:hidden;}
.charge .price{border-bottom:1px dashed #C9C9C9;padding:10px 10px 15px;margin-bottom:20px;color:#666666;font-size:12px;}
.charge .price strong{font-weight:normal;color:#EE6209;font-size:26px;font-family:Helvetica;}
.charge .showaddr{border:1px dashed #C9C9C9;padding:10px 10px 15px;margin-bottom:20px;color:#666666;font-size:12px;text-align:center;}
.charge .showaddr strong{font-weight:normal;color:#9900FF;font-size:26px;font-family:Helvetica;}
.charge .copy-right{margin:5px 0; font-size:12px;color:#848484;text-align:center;}
/* 充值页 ]]*/
</style>
</head>
<body>
	<article class="charge">
		<h1>微信支付</h1>
		<section class="content">
				<h2>商品：<?php echo $info['title'];?></h2>		
		  <!--<ul class="select cf">
					<li><img src="weixin.jpg" style="width:150px;height:150px"></li>
				</ul>-->
				<p class="copy-right">亲，此商品不提供退款和发货服务哦</p>
				<div class="price">微信价：<strong>￥<?php echo $money;?>元</strong></div>
				<div class="operation"><a class="btn-green" id="getBrandWCPayRequest" href="<?php echo $sendurl;?>">立即购买</a></div> 
				<p class="copy-right">广州唐剑科技有限公司</p>
		</section>
	</article>



</body>


<style type="text/css">#yddContainer{display:block;font-family:Microsoft YaHei;position:relative;width:100%;height:100%;top:-4px;left:-4px;font-size:12px;border:1px solid}#yddTop{display:block;height:22px}#yddTopBorderlr{display:block;position:static;height:17px;padding:2px 28px;line-height:17px;font-size:12px;color:#5079bb;font-weight:bold;border-style:none solid;border-width:1px}#yddTopBorderlr .ydd-sp{position:absolute;top:2px;height:0;overflow:hidden}.ydd-icon{left:5px;width:17px;padding:0px 0px 0px 0px;padding-top:17px;background-position:-16px -44px}.ydd-close{right:5px;width:16px;padding-top:16px;background-position:left -44px}#yddKeyTitle{float:left;text-decoration:none}#yddMiddle{display:block;margin-bottom:10px}.ydd-tabs{display:block;margin:5px 0;padding:0 5px;height:18px;border-bottom:1px solid}.ydd-tab{display:block;float:left;height:18px;margin:0 5px -1px 0;padding:0 4px;line-height:18px;border:1px solid;border-bottom:none}.ydd-trans-container{display:block;line-height:160%}.ydd-trans-container a{text-decoration:none;}#yddBottom{position:absolute;bottom:0;left:0;width:100%;height:22px;line-height:22px;overflow:hidden;background-position:left -22px}.ydd-padding010{padding:0 10px}#yddWrapper{color:#252525;z-index:10001;background:url(chrome-extension://eopjamdnofihpioajgfdikhhbobonhbb/ab20.png);}#yddContainer{background:#fff;border-color:#4b7598}#yddTopBorderlr{border-color:#f0f8fc}#yddWrapper .ydd-sp{background-image:url(chrome-extension://eopjamdnofihpioajgfdikhhbobonhbb/ydd-sprite.png)}#yddWrapper a,#yddWrapper a:hover,#yddWrapper a:visited{color:#50799b}#yddWrapper .ydd-tabs{color:#959595}.ydd-tabs,.ydd-tab{background:#fff;border-color:#d5e7f3}#yddBottom{color:#363636}#yddWrapper{min-width:250px;max-width:400px;}</style>
<script type="text/javascript" src="/statics/js/jquery-1.8.3.min.js"></script>
  <script>
  check_order();
  function send(){
	check_order();
   	//window.setTimeout(function (){check_order();},3000);
  }
  function check_order(){
	   var trade_sn = "<?php echo $trade_sn;?>";
	   var url = 'http://www.yjxun.cn/recharge/index.php?m=pay&a=wechat_check_order&trade_sn='+trade_sn;
	   $.get(url,function(data){
		   if(data.status=='succ'){
			    var gameId = "<?php echo $info['gameId'];?>";
				var redirectUrl = '';
			   	switch(parseInt(gameId)){
					case 234:
						redirectUrl = 'http://www.yjxun.cn/member/rechargeRecord.html?&target=_self';
						break;
					default:
						redirectUrl = 'http://www.yjxun.cn/member/index.php?&target=_self';
						break;
				}
				window.location.href = redirectUrl;
			}
	   },'json');
	   window.setTimeout(function (){check_order();},3000);
  }
  </script>
</html>