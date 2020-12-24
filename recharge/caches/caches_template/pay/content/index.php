<?php defined('IN_drcms') or exit('No permission resources.'); ?><?php include template('content','header',$this->style); ?>

<div id="container">
  <div id="content">
    <div class="conTp"></div>
    <div id="container003" class="con002">
      <div class="oArea">
        <div class="map">当前位置：游戏充值中心</div>
        <div class="main">
          <div class="pay-tip">
            <ul class="pay-tip-list">
              <li class="pay-tip-item pay-tip-item-1"><span class="select">1.选择充值方式</span></li>
              <li class="pay-tip-item pay-tip-item-3"><span>2.填写充值信息</span></li>
              <li class="pay-tip-item pay-tip-item-4"><span>3.充值成功</span></li>
            </ul>
          </div>
          <div class="h20"></div>
          <dl class="pps_pay">
            <dt>请选择要充值的方式</dt>
            <dd class="gameList">
              <h2 class="ui-title fn-clear new-title">快钱</h2>
              <div class="new-service-desc">
                <div class="ncaed003"> <img src="<?php echo SPATH;?>pay/images/kuaiqian.jpg"> </div>
                <div class="new-service-content">
                  <p class="pp003">快钱是国内第一家提供基于EMAIL和手机号码的网上收付费平台。</p>
                  <ul class="ui-list ui-list-square">
                    <li class="ui-list-item">  快钱帐户就像是一个安全便捷的"网上钱包"，只需要免费注册，就可以立即获得一个快钱帐户。</li>
                    <li class="ui-list-item">您可以为这个"钱包"充值，用帐户里的资金消费，而帐户里的资金也可以随时提取出来。</li>
                  </ul>
                </div>
                <div id="submitBtn" class="ui-button ui-button-lorange"><a>
                  <input type="submit" class="ui-button-text" value="选择" onclick="window.location.href='index.php?m=pay&a=c&cf=kuaiqian&bank=&k=<?=strip_tags($_GET['k']);?>'">
                  </a> </div>
              </div>
            </dd>
            
            <dd class="gameList">
              <h2 class="ui-title fn-clear new-title">快钱银行</h2>
              <div class="new-service-desc">
                <div class="ncaed003"> <img src="<?php echo SPATH;?>pay/images/kuaiqianyin.jpg"> </div>
                <div class="new-service-content">
                  <p class="pp003">快钱银行支付是可以直接使用网上银行进行付款，支付无限额。快钱网银支付支持国内几乎所有银行企业网银（覆盖工行、建行、招行、深发展、农行、浦发、民生银行等）。</p>
                </div>
                <div id="submitBtn" class="ui-button ui-button-lorange"><a>
                  <input type="submit" class="ui-button-text" value="选择"  onclick="window.location.href='index.php?m=pay&a=c&cf=kuaiqian&bank=&k=<?=strip_tags($_GET['k']);?>'">
                  </a> </div>
              </div>
            </dd>
            
            <dd class="gameList">
              <h2 class="ui-title fn-clear new-title">支付宝</h2>
              <div class="new-service-desc">
                <div class="ncaed003"> <img src="<?php echo SPATH;?>pay/images/zfb.jpg"> </div>
                <div class="new-service-content">
                  <p class="pp003">您都可以通过支付宝帐号在支付宝平台进行安全、快捷的在线充值服务，即充即玩1分钟完成。 </p>
                  <ul class="ui-list ui-list-square">
                    <li class="ui-list-item">目前已支持20余家银行，覆盖最广泛的企业和个人用户</li>
                    <li class="ui-list-item">网购付款时，您需要跳转银行网银页面，按银行要求的信息进行支付。</li>
                  </ul>
                </div>
                <div id="submitBtn" class="ui-button ui-button-lorange"><a>
                <!--  <input type="submit" class="ui-button-text" value="选择"  onclick="javascript:alert('维护中，暂未开放');return false; 　 　">-->
                  <input type="submit" class="ui-button-text" value="选择"  onclick="window.location.href='index.php?m=pay&a=c&cf=alipay&k=<?=strip_tags($_GET['k']);?>'">
                  </a> </div>
              </div>
            </dd>
            <dd class="gameList">
              <h2 class="ui-title fn-clear new-title">盛付通</h2>
              <div class="new-service-desc">
                <div class="ncaed003"> <img src="<?php echo SPATH;?>pay/images/shengfu.jpg"> </div>
                <div class="new-service-content">
                  <p class="pp003">盛付通首创了用户无需开通网银，只需要用户有一张银行卡、一部手机便可以简单快捷地完成支付。</p>
                  
                </div>
                <div id="submitBtn" class="ui-button ui-button-lorange"><a>
                  <input type="submit" class="ui-button-text" value="选择"  onclick="window.location.href='index.php?m=pay&a=c&cf=shengfutong&k=<?=strip_tags($_GET['k']);?>'">
                  </a> </div>
              </div>
            </dd>
            <dd class="gameList">
              <h2 class="ui-title fn-clear new-title">神州付</h2>
              <div class="new-service-desc">
                <div class="ncaed003"> <img src="<?php echo SPATH;?>pay/images/shenfu.jpg"> </div>
                <div class="new-service-content">
                  <p class="pp003">游戏点卡兑换业务：支持中国移动、中国联通、中国电信发行的手机充值卡兑换，包括全国卡及地方卡。</p>
                  
                </div>
                <div id="submitBtn" class="ui-button ui-button-lorange"><a>
                  <input type="submit" class="ui-button-text" value="选择" onclick="window.location.href='index.php?m=pay&a=c&cf=shenzhoufu&k=<?=strip_tags($_GET['k']);?>'">
                  </a> </div>
              </div>
            </dd>
            
<!--
            <dd class="gameList">
              <h2 class="ui-title fn-clear new-title">财付通</h2>
              <div class="new-service-desc">
                <div class="ncaed003"> <img src="<?php echo SPATH;?>pay/images/tenpay.jpg" > </div>
                <div class="new-service-content">
                  <p class="pp003">财付通是一个专业的在线支付平台，其核心业务是帮助在互联网上进行交易的双方完成支付和收款。是腾讯公司创办的在线支付平台。</p>
                  
                </div>
                <div id="submitBtn" class="ui-button ui-button-lorange"><a>
                  <input type="button" class="ui-button-text" value="选择" onclick="alert('维护中');return false;">
                  </a> </div>
              </div>
            </dd>
  -->
            <dd class="gameList">
              <h2 class="ui-title fn-clear new-title"></h2>
              <div class="new-service-desc">
                <div class="ncaed003"></div>
                  <p class="pp003">更多付款方式即将推出</p>

                
            </dd>
          </dl>
          <!--/pps_pay--> 
        </div>
        <!-- end main--> 
      </div>
      <!--/oArea--> 
    </div>
    <!--/container-->
    <div class="conBot"></div>
    
    <!--footer--> 
  </div>
</div>
<?php include template('content','footer',$this->style); ?>
