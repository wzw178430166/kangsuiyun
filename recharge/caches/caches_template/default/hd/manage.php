<?php defined('IN_drcms') or exit('No permission resources.'); ?><html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="<?php echo SPATH;?>data/css/main.css" type="text/css">
<style type="text/css">
#yddContainer { display:block; font-family:Microsoft YaHei; position:relative; width:100%; height:100%; top:-4px; left:-4px; font-size:12px; border:1px solid }
#yddTop { display:block; height:22px }
#yddTopBorderlr { display:block; position:static; height:17px; padding:2px 28px; line-height:17px; font-size:12px; color:#5079bb; font-weight:bold; border-style:none solid; border-width:1px }
#yddTopBorderlr .ydd-sp { position:absolute; top:2px; height:0; overflow:hidden }
.ydd-icon { left:5px; width:17px; padding:0px 0px 0px 0px; padding-top:17px; background-position:-16px -44px }
.ydd-close { right:5px; width:16px; padding-top:16px; background-position:left -44px }
#yddKeyTitle { float:left; text-decoration:none }
#yddMiddle { display:block; margin-bottom:10px }
.ydd-tabs { display:block; margin:5px 0; padding:0 5px; height:18px; border-bottom:1px solid }
.ydd-tab { display:block; float:left; height:18px; margin:0 5px -1px 0; padding:0 4px; line-height:18px; border:1px solid; border-bottom:none }
.ydd-trans-container { display:block; line-height:160% }
.ydd-trans-container a { text-decoration:none; }
#yddBottom { position:absolute; bottom:0; left:0; width:100%; height:22px; line-height:22px; overflow:hidden; background-position:left -22px }
.ydd-padding010 { padding:0 10px }
#yddWrapper { color:#252525; z-index:10001; background:url(chrome-extension://eopjamdnofihpioajgfdikhhbobonhbb/ab20.png); }
#yddContainer { background:#fff; border-color:#4b7598 }
#yddTopBorderlr { border-color:#f0f8fc }
#yddWrapper .ydd-sp { background-image:url(chrome-extension://eopjamdnofihpioajgfdikhhbobonhbb/ydd-sprite.png) }
#yddWrapper a, #yddWrapper a:hover, #yddWrapper a:visited { color:#50799b }
#yddWrapper .ydd-tabs { color:#959595 }
.ydd-tabs, .ydd-tab { background:#fff; border-color:#d5e7f3 }
#yddBottom { color:#363636 }
#yddWrapper { min-width:250px; max-width:400px; }
</style>
</head>
<body>
<div class="list">
  <div class="list_1">
    <div class="list_1_1"></div>
    <div class="list_1_2 sonSys">活动列表</div>
    <div class="list_1_3"></div>
  </div>
  <div class="list_2">
    <div class="list_2_2">
      <div class="right">
        <div class="mainHrefDiv"></div>
        <div style="clear:both"></div>
        <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
        <div class="mainHrefDiv">
          <div class="hrefDiv"><a href="index.php?m=data&c=hd&a=index&hdid=<?php echo $r['hdid'];?>" target="main"><?php echo $r['hd_name'];?></a></div>
        </div>
        <?php $n++;}unset($n); ?>
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