<?php
//http://www.e8wan.com/s.php?ac=yhqh
error_reporting( E_ALL ^ E_NOTICE ^ E_WARNING );
if ( $_REQUEST[ 'plat' ] ) {
  $plat = '&plat=' . $_REQUEST[ 'plat' ];
  if ( !$_REQUEST[ 'k' ] ) {
    $_REQUEST[ 'k' ] = $_REQUEST[ 'key' ] ? $_REQUEST[ 'key' ] : $_REQUEST[ 'plat' ];
  }
}
if ( $_REQUEST[ 'se' ] ) {
  $plat .= '&se=' . $_REQUEST[ 'se' ];
}
switch ( $_REQUEST[ 'ac' ] ) {
  //爱心卡
  case 'ax':
    /*header('Location:http://www.yjxun.cn/?f=ax');*/ echo '医生卡二维码链接地址';
    die();
    break;

  case 'gotest':
    header( 'Location:http://www.yjxun.cn/equipment/equipment_list.html?target=_self' );
    break;
    //game
  case 'yad':
    header( 'Location:http://www.yjxun.cn/index.php?m=jiugongge_&pid=1&hid=5&hdd=1&vxid=10' );
    break;
  case 'jcs':
    header( 'Location:http://www.yjxun.cn/fo/jcs' );
    break; //web-mobile/ 

  case 'yhw':
    header( 'Location:http://shop.yjxun.cn/?m=coupon&c=index2&a=dealVoucher&code=0b9b81f4dde04175127127437d5a3773' );
    break;
  case 'yhs':
    header( 'Location:http://shop.yjxun.cn/?m=coupon&a=dealCoupon&code=86f031aee8d0e2c756dcdd346e7a2d4f' );
    break;


    //语音技巧中心   
  case 'yyjq':
    header( "Location:https://www.yjxun.cn/plugs/skill/show.html?target=_self" );
    break;
    //直播跳转地址
  case 'zb':
    header( "Location:https://www.yjxun.cn/html/liveFrame.html?target=_self&y=2" );
    break;

//3月广告图链接
  case 'mms_cn': //移动医疗中文=》链接到中文模板
    header( "Location:https://www.yjxun.cn/index_yy/yjx_home.html?target=_self" );
    break;
  case 'mms_en': //移动医疗英文=》链接到英文模板
    header( "Location:https://s2.yjxun.cn/?target=_self" );
    break;
  case 'miz_cn': //医疗信息化中文=》链接到官网对应栏目
    header( "Location:https://b.yjxun.cn/?target=_self" );
    break;
  case 'miz_en':  //医疗信息化英文=》链接到英文关于我们
    header( "Location:https://b.yjxun.cn/?m=core&c=data&a=setlen&target=_self" );
    break;
  case 'watch':
    $device = getDevice();
    $goUrl = '';
    if ('ios' == $device) {
      $goUrl = 'https://apps.apple.com/cn/app/%E5%AE%89%E5%85%A8%E5%AE%88%E6%8A%A42/id1026478681';
    } else {
      $goUrl = 'https://android.myapp.com/myapp/detail.htm?apkName=com.tgelec.aqsh&ADTAG=mobile';
    }
    header( "Location:".$goUrl );
    break;
//
//康穗云微信
//
  case 'k_jianjie': 
	header( "Location:https://www.519ksy.com/" );
  break;
  //行业新闻
  case 'k_hangyenews': 
    header( "Location:https://www.519ksy.com/index.php?m=content&c=index&a=lists&catid=101" );
    break;
    //移动医疗
    case 'move': 
      header( "Location:https://www.519ksy.com/m2/fc1.html" );
      break;
      //互联网医院
      case 'internet': 
        header( "Location:https://www.519ksy.com/m2/fc2.html" );
        break;
        //慢病管理
        case 'chronic': 
          header( "Location:https://www.519ksy.com/m2/fc4.html" );
          break;
          //居家养老
          case 'pension': 
            header( "Location:https://www.519ksy.com/m2/fc3.html" );
            break;
            //更多产品
            case 'more': 
              header( "Location:https://www.519ksy.com/m2/fc1.html" );
              break;
              //快速搭建
              case 'build': 
                header( "Location:https://www.519ksy.com/m2/hezuo.html" );
                break;
                   //定制服务
              case 'customized': 
                header( "Location:https://www.519ksy.com/m2/service2.html" );
                break;
                   //申请体验
              case 'experience': 
                header( "Location:https://www.519ksy.com/m2/tiyans.html" );
                break;
                   //代理加盟
              case 'join': 
                header( "Location:https://www.519ksy.com/m2/joinus.html" );
                break;
                   //客服中心
              case 'customerss': 
                header( "Location:https://www.519ksy.com/?m=myim&c=customer_service" );
                break;
  default:
    header( "Location:https://www.yjxun.cn" );
    break;
}
//判断设备
function getDevice(){
  $result = '';
  if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')||strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')){
     $result = 'ios';
  }else if(strpos($_SERVER['HTTP_USER_AGENT'], 'Android')){
     $result = 'android';
  }else{
    $result = 'other';
  }
  return $result;
}
//PHP COOKIE设置函数立即生效。
function mysetcookie( $var, $value = '', $time = 0, $path = '', $domain = '' ) {
  $_COOKIE[ $var ] = $value;
  if ( is_array( $value ) ) {
    foreach ( $value as $k => $v ) {
      setcookie( $var . '[' . $k . ']', $v, $time, $path, $domain, $s );
    }
  } else {
    setcookie( $var, $value, $time, $path, $domain, $s );
  }
}
?>
