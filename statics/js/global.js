function zhuangb(){console.log("%c医家讯   %c- ⓒ广州唐剑科技有限公司 版权所有 ","font-size:40px; color:#2CACFC; font-weight: bold;font-family:Microsoft YaHei; text-shadow: 2px 2px 1px #9ED9FD;"," color:#969696");console.log("%c智能医体健康平台 ","font-size:35px; color:#2CACFC; font-weight: 600;font-family:Microsoft YaHei;text-shadow: 2px 2px 1px #9ED9FD; ");console.log("\n==========================================================\n");console.log("\n如果你看到了这个，又想加入我们，请用以下联系方式：");console.log("\n电子邮箱：%c 790059922@qq.com","color: #005991;font-weight:800");console.log("\n电话：%c 020-81533173","color: #005991;font-weight:800");console.log("\n\n")};
setCookie('webSocketIp','124.172.184.210');
//====================================
jQuery.getScript("/statics/js/public_yjx.js", function(data, status, jqxhr){
	console.log('加载完成~public_yjx');
});
/*jQuery.getScript("/statics/eeedit/js/etGlobal.js", function(data, status, jqxhr){
	console.log('加载完成~weChatShare');
	var ua = window.navigator.userAgent.toLowerCase();
	if(ua.match(/MicroMessenger/i) == 'micromessenger'){
		if(getQueryString('WeChat_SpApi') == 1 && getQueryString('backNone') == 1){
			$(function(){
				hideTBar();
			})
		}else{
			try{
				wx.miniProgram.getEnv(function(res) {
					if (res.miniprogram) {
						$('.index_foot').hide();
					}
					if (res.miniprogram && getQueryString('backNone') == 1) hideTBar();
				})
			}catch(e){ }
		}
	}
});*/

var Tapp_ = getTapp();
if (Tapp_) {  
	jQuery.getScript("/index/js/dapp.js", function(data, status, jqxhr){
		console.log('加载完成~dapp');
		try{
			dappConfig(); 
		}catch(e){}
	});
}



var startTime = Date.parse(new Date()) / 1000;
function hideTBar(){
	$('.header_boxl img').hide();
	$('.header_box img').hide();
	$('.index_foot').hide();
	$('.header_box .return-btn').hide();
	$('.m-header-return').hide();
	$('.header_box .left_b').hide();
}
/**/
var this_site = '//www.519ksy.com';
var this_cookie_pre = 'GkvUq_';
var cookie_pre = 'GkvUq_';
var iosP = false;
setTimeout(function(){ 
	$('#_embed_v3_dc').remove();
},1500)
$(function(){
	if(isiOS){
		//$('.ios_class_none').hide();
		console.log($('.ios_class_none'));
	}
	var get_src = encodeURIComponent(location.href);
	setTimeout(function(){
		$.ajax({
			type: 'get',
			url:'/?m=member&c=data&a=set_visit_data&get_src='+get_src,
			dataType: 'jsonp',
			jsonp: "jsoncallback",
			success:function(data){}
		})
	},9);
	if(!isInclude('im_client.js') && !isInclude('myim_service.js') && !isInclude('myim_sr.js')){
		jQuery.getScript("/statics/js/ex/im_online.js", function(data, status, jqxhr){
			console.log('加载完成~im_online');
		});
	}
})
if(!document.getElementsByName("description")[0]){
	$("title").before('<meta name="keywords" content="医家讯,医家讯智能医疗设备,智能健康电子档案,个人健康、家庭健康,健康检测,心率监测,血糖监测,血压监测,睡眠监测,健康饮食,运动健美,亲友互动,运动圈子,医体健康,食动平衡,人工智能,健康管理,物联网,智能大数据">');
	$("title").before('<meta name="description" content="医家讯智能医体健康平台通过智能紧急提醒、生命健康监测、智能穿戴设备及专业级智能医疗设备，结合物联网、云计算、健康大数据分析与呼叫中心，构建智能医体健康平台，让家人享受更健康、更安全、更舒心、更智慧化的生活，也让子女随时随地的了解父母的健康情况，及时表达爱心和孝心。">');
}

/**/
function setios(){
	iosP = true;
	setCookie('ios',1, 31536000);
	 
}
function isios(){ 
	return iosP;
	if(getCookie('ios')){
		alert('ok');	
	}
}
/**/
try{
	jstojava.close_toobar(); 
}catch(e){}

function sync_member(){
	if(getUrlParam('mobile')=='ios'){  
		console.log("setlogin for ios"); 
		goo('dapp://setlogin=>{"auth":"'+getCookie(cookie_pre+'auth')+'","status":"1","forward":"","username":"'+getCookie(cookie_pre+'__username')+'","userid":"'+getCookie(cookie_pre+'_userid')+'","avatar":"","avatar180":"","nickname":"1373","point":"29103","groupid":"14","regdate":"","regdate_":"","portrait":"'+getCookie('GkvUq__portrait')+'"}');
	}else{ 
		try{
			jstojava.sync_member(getCookie(cookie_pre+'_userid'),getCookie(cookie_pre+'__username'),getCookie(cookie_pre+'__nickame'),getCookie(cookie_pre+'auth'),getCookie(cookie_pre+'__avatar'));    
		}catch(e){ }
	}
}
//全自动调整图片
function AutoImage(id, maxWidth, maxHeight, classname) {
	var c = $('#' + id + ' img');
	for (var i = 0; c[i]; i++) {
		AutoResizeImage_(maxWidth, maxHeight, c[i]);
		AutoRecenterImage_(c[i],classname);
	}
}
//自动限制图片居中
function AutoRecenterImage(id, classname) {
	var c = $('#' + id + ' img');
	for (var i = 0; c[i]; i++) {
		AutoRecenterImage_(c[i], classname);
	}
}
function AutoRecenterImage_(objImg, classname) {
	if (classname && classname!="") {
		$(objImg).wrap("<div class='"+classname+"'></div>");
	}else{
		$(objImg).wrap("<div style='text-align:center;text-indent:0;margin:10px 0'></div>");
	}
}

//自动限制图片大小
function AutoResizeImage(maxWidth, maxHeight, id) {
	var c = $('#'+id+' img');//alert(c[1]);
	for (var i = 0; c[i]; i++) {
		
		AutoResizeImage_(maxWidth, maxHeight, c[i]);
	}
}
function AutoResizeImage_(maxWidth, maxHeight, objImg) {
	var img = new Image();
	img.src = objImg.src;      
	img.onload=function(){
		var hRatio, wRatio, Ratio = 1;
		var w = img.width; 
		var h = img.height;
		wRatio = maxWidth / w;
		hRatio = maxHeight / h;
		if (maxWidth == 0 && maxHeight == 0) {
			Ratio = 1;
		} else if (maxWidth == 0) {
			if (hRatio < 1) Ratio = hRatio;
		} else if (maxHeight == 0) {
			if (wRatio < 1) Ratio = wRatio;
		} else if (wRatio < 1 || hRatio < 1) {
			Ratio = (wRatio <= hRatio ? wRatio: hRatio);
		}
		if (Ratio < 1) {
			w = w * Ratio;
			h = h * Ratio;
		}
		objImg.style.height = h + 'px';
		objImg.style.width = w + 'px';
	}	

}

//JS操作cookies方法! 
/**
*
* 写cookies 
* 这是有设定过期时间的使用示例：
* s20是代表20秒
* h是指小时，如12小时则是：h12
* d是天数，30天则：d30
*
**/
function setCookie(name, value, time, domain, path) {
	var exp = new Date(),
	timestr = "";
	if (time) { 
		var strsec = getsec(time);
		exp.setTime(exp.getTime() + strsec * 1);
		timestr = ";expires=" + exp.toGMTString();
	}
	domain = domain ? ";domain=" + domain: '';
	//alert(name + "="+ escape (value) + ";expires=" + exp.toGMTString()+ ";path=/;domain="+domain);
	document.cookie = name + "=" + escape(value) + timestr + ";path=/" + domain;

}

//读取cookies 
function getCookie(name) {
	var arr, reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");
	if (arr = document.cookie.match(reg)){
		//console.log(arr[2]);
		if(arr[2].indexOf('%')!=-1){
			arr[2] =  decodeURIComponent(arr[2]); 
			return unescape(arr[2]); 
		}else{
			return arr[2];
		}
	}
	else return null;
}

//删除cookies 
function delCookie(name) {
	var exp = new Date();
	exp.setTime(exp.getTime() - 1);
	var cval = getCookie(name);
	if (cval != null) document.cookie = name + "=" + cval + ";expires=" + exp.toGMTString();
}

function getsec(str) {
	var str1 = str.substring(1, str.length) * 1;
	var str2 = str.substring(0, 1);
	if (str2 == "s") {
		return str1 * 1000;
	} else if (str2 == "h") {
		return str1 * 60 * 60 * 1000;
	} else if (str2 == "d") {
		return str1 * 24 * 60 * 60 * 1000;
	}
}
//$_GET
function getUrlParam(name,url) {
	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
	var reg2 = new RegExp("(^|/?)" + name + "=([^&]*)(&|$)");

	if(!url){ 
		var r = window.location.search.substr(1).match(reg);
		if (r != null) return unescape(r[2]); return null;	
	}else{
		var r = url.match(reg);
		var r2 = url.match(reg2);
		if (r != null){
			return unescape(r[2]);
		}else if(r2!= null){ 
			return unescape(r2[2]);
		}
		return null;
	}
}
function addUrlParam(name,value,url){
	url = url? url : window.location.href;
	if(url.indexOf('?')!=-1){
		url += '&'+name+'='+value;
	}else{
		url += '?'+name+'='+value;
	}
	return url;
}
//加入收藏夹
function Addme() {
    url = document.URL;  //你自己的主页地址
    title = "****";  //你自己的主页名称
    window.external.AddFavorite(url, title);
}
//设为首页
function SetHome(obj,vrl){
        try{
                obj.style.behavior='url(#default#homepage)';obj.setHomePage(vrl);
        }
        catch(e){
                if(window.netscape) {
                        try {
                                netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
                        }
                        catch (e) {
                                alert("此操作被浏览器拒绝！\n请在浏览器地址栏输入“about:config”并回车\n然后将 [signed.applets.codebase_principal_support]的值设置为'true',双击即可。");
                        }
                        var prefs = Components.classes['@mozilla.org/preferences-service;1'].getService(Components.interfaces.nsIPrefBranch);
                        prefs.setCharPref('browser.startup.homepage',vrl);
                 }
        }
}
//复制
function copyToClipBoard(){
var clipBoardContent=''
clipBoardContent=document.getElementByIdx_x("Top1_dizhi1").value
window.clipboardData.setData("Text",clipBoardContent)
alert('地址已复制到剪切板，赶快推荐您的好友吧！')
}

function goo_t(url,full){
  	var ua_ = window.navigator.userAgent.toLowerCase();
  	// && ua_.match(/MicroMessenger/i) == 'micromessenger'
	if(url != '#'){
		try{ 
			jstojava.isok(); 
			//url += "&target=_blank"
		}catch(e){}
		//alert(url);
		if (full) {
			if(url.indexOf("http://") <= 0 && url.indexOf(this_site) <= 0){
			//if(url.substr(0, 1)=='/'){ url = url.substr(1, url.length); }
			url = this_site + url;
		  } 
		}
		if(getUrlParam('target',url) == 'top'){
			//url = addUrlParam('ta','self',url); 
			window.parent.location.href=url; 
			return;  
		} 
		if(getUrlParam('mobile')){
			if(url.indexOf('dapp://')==-1 && url.indexOf('ios://')==-1){ 
				url = addUrlParam('mobile',getUrlParam('mobile'),url); 
				url = addUrlParam('version',getUrlParam('version'),url);  
			}
			
		}
		location.href = url;
	}
}



function goo(url,full){
  	var ua_ = window.navigator.userAgent.toLowerCase();
  	// && ua_.match(/MicroMessenger/i) == 'micromessenger'
	// if(!userid_){
	// 	if(url.indexOf('lgqb') == -1 && url.indexOf('yfzn') == -1 && url.indexOf('xxfeiyan/bobao/') == -1 && url.indexOf('xxfeiyan/chunyu') == -1 && url.indexOf('shop.yjxun.cn') == -1){
	// 		location.href = '/member/login.html?forward='+encodeURIComponent(location.href);
	// 		return;
	// 	}
	// }
	if(url != '#'){
		try{ 
			jstojava.isok(); 
			//url += "&target=_blank"
		}catch(e){}
		//alert(url);
		if (full) {
			if(url.indexOf("http://") <= 0 && url.indexOf(this_site) <= 0){
			//if(url.substr(0, 1)=='/'){ url = url.substr(1, url.length); }
			url = this_site + url;
		  } 
		}
		if(getUrlParam('target',url) == 'top'){
			//url = addUrlParam('ta','self',url); 
			window.parent.location.href=url; 
			return;  
		} 
		if(getUrlParam('mobile')){
			if(url.indexOf('dapp://')==-1 && url.indexOf('ios://')==-1){ 
				url = addUrlParam('mobile',getUrlParam('mobile'),url); 
				url = addUrlParam('version',getUrlParam('version'),url);  
			}
			
		}
		location.href = url;
	}
}
function goo2(url){
	try{ 
		jstojava.isok();
		goo(url);
	}catch(e){
       if(getUrlParam('mobile') == 'ios' && Number(getUrlParam('version'))>=2019081502){
       		url += url.indexOf('?') == -1 ? "?go_top=1" : "&go_top=1";
			url += '&ta=self&random='+(new Date().getTime());
			$('body').attr('scrolltop',$(document).scrollTop());
			$('#index_edit').attr('src',url);
			$('.application-mange').css({"animation":"mangeleft 0.4s 1","-webkit-animation":"mangeleft 0.4s 1","animation-fill-mode":"forwards","-webkit-animation-fill-mode":"forwards"});
            return;
       }
		if(url != '#' && getUrlParam('mobile') != 'ios'){
			url += url.indexOf('?') == -1 ? "?go_top=1" : "&go_top=1";
			url += '&random='+(new Date().getTime());
			$('body').attr('scrolltop',$(document).scrollTop());
			$('#index_edit').attr('src',url);
			$('.application-mange').css({"animation":"mangeleft 0.4s 1","-webkit-animation":"mangeleft 0.4s 1","animation-fill-mode":"forwards","-webkit-animation-fill-mode":"forwards"});
		}else{
            //url += '&target=_self';
			goo(url);
		}
	}
}
function randomtrade(len,is_date) {
	var timeStr = '';
	var is_date = arguments[1] ? arguments[1] : 0;
	if (is_date>0) {
		var date = new Date();
		var month = (date.getMonth()+1);
		month = month<10?'0'+month:month;
		var day = date.getDate();
		day = day<10?'0'+day:day;
		var hours = date.getHours();
		hours = hours<10?'0'+hours:hours;
		var minu = date.getMinutes();
		minu = minu<10?'0'+minu:minu;
		var second = date.getSeconds();
		second = second<10?'0'+second:second;
		
		var timeStr = String(date.getFullYear()) + String(month)  + String(day) + String(hours) + String(minu) + String(second);
	}
	
　　len = len || 32;
　　var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';    /****默认去掉了容易混淆的字符oOLl,9gq,Vv,Uu,I1****/
　　var maxPos = $chars.length;
　　var str = '';
　　for (i = 0; i < len; i++) {
　　　　str += $chars.charAt(Math.floor(Math.random() * maxPos));
　　}
　　return timeStr+str;
}
//手机端返回键
function goback(){
	try{
		$('html,body',parent.document).removeClass('ovfHiden');
	}catch(e){}
	//var reg=new RegExp("http\:\/\/www\.lyj008\.com/\?mobile\=android\&version=\d*");
	//var reg=new RegExp("www\.yjxun\.cn/\?");
	//self != top && userid_ == 4 && 
	if(getUrlParam('go_top') == 1){
		update_top_window();
		return;
	}
	if(getUrlParam('mobile')=='ios'){ 
		if(Number(getUrlParam('version'))>=2019081502){
			goo('dapp://goback');
			return;
		} 
	}
	try{
		jstojava.isok();
		var endTime = (Date.parse(new Date()) / 1000) - startTime;
		$.ajax({
			url:'//ksy.yjxun.cn/?m=myim&c=temp&a=addBugLog&onpagehide=1&duration='+endTime+'&title='+$('title').text()+'&toUrl='+encodeURIComponent(window.location.href),
			async:false
		});
		goo('dapp://goback');
	}catch(e){ }
	try{
		jstojava.goback();
	}catch(e){
		//alert(document.referrer.match(reg) != null);
		window.history.back();
	}
}
function update_top_window(){
	//运行在iframe框架中
	//$('#index_edit',parent.document).attr('src','');
	var num = $('html',parent.document).find('.tixing_user').text();
	//$('html',parent.document).find('.tixing_user').text(Number(num) + 1);
	$('body',parent.document).scrollTop($('body',parent.document).attr('scrolltop'));
	$('.application-mange',parent.document).css({"animation":"mangeright 0.4s 1","-webkit-animation":"mangeright 0.4s 1","animation-fill-mode":"forwards","-webkit-animation-fill-mode":"forwards"});
}
function goback_2(){
	window.history.go(-2);
}
function goback_iframe(eid,state){
	//eid class or id
	if($(eid,parent.document).length > 0){
		if(state == 1){
			$(eid,parent.document).fadeToggle(456);
		}else{
			goback();
		}
	}
}
var u = navigator.userAgent;
var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
function set_qq_map_js(){
	/*var oHead = document.getElementsByTagName('head').item(0);
	var oScript= document.createElement("script");
	oScript.type = "text/javascript";
	oScript.src="//apis.map.qq.com/tools/geolocation/min?key=SABBZ-3CO6O-ZMUWI-SEINE-ZVEG6-UGBUU&referer=Quan";
	oHead.appendChild(oScript);*/
	
	if(!isiOS){
		jQuery.getScript("//apis.map.qq.com/tools/geolocation/min?key=SABBZ-3CO6O-ZMUWI-SEINE-ZVEG6-UGBUU&referer=Quan", function(data, status, jqxhr){
			console.log('加载完成~地图定位');
			get_latlng();
		});
	}else{
		jQuery.getScript("//apis.map.qq.com/tools/geolocation/min?key=SABBZ-3CO6O-ZMUWI-SEINE-ZVEG6-UGBUU&referer=Quan", function(data, status, jqxhr){
			console.log('加载完成~地图定位');
			get_latlng();
		});
	}
}
function Base64() {
    // private property 
    _keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
    // public method for encoding 
    this.encode = function(input) {
        var output = "";
        var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
        var i = 0;
        input = _utf8_encode(input);
        while (i < input.length) {
            chr1 = input.charCodeAt(i++);
            chr2 = input.charCodeAt(i++);
            chr3 = input.charCodeAt(i++);
            enc1 = chr1 >> 2;
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
            enc4 = chr3 & 63;
            if (isNaN(chr2)) {
                enc3 = enc4 = 64;
            } else if (isNaN(chr3)) {
                enc4 = 64;
            }
            output = output + _keyStr.charAt(enc1) + _keyStr.charAt(enc2) + _keyStr.charAt(enc3) + _keyStr.charAt(enc4);
        }
        return output;
    }
    // public method for decoding 
    this.decode = function(input) {
        var output = "";
        var chr1, chr2, chr3;
        var enc1, enc2, enc3, enc4;
        var i = 0;
        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
        while (i < input.length) {
            enc1 = _keyStr.indexOf(input.charAt(i++));
            enc2 = _keyStr.indexOf(input.charAt(i++));
            enc3 = _keyStr.indexOf(input.charAt(i++));
            enc4 = _keyStr.indexOf(input.charAt(i++));
            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;
            output = output + String.fromCharCode(chr1);
            if (enc3 != 64) {
                output = output + String.fromCharCode(chr2);
            }
            if (enc4 != 64) {
                output = output + String.fromCharCode(chr3);
            }
        }
        output = _utf8_decode(output);
        return output;
    }
    // private method for UTF-8 encoding 
    _utf8_encode = function(string) {
        string = string.replace(/\r\n/g, "\n");
        var utftext = "";
        for (var n = 0; n < string.length; n++) {
            var c = string.charCodeAt(n);
            if (c < 128) {
                utftext += String.fromCharCode(c);
            } else if ((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            } else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }
        }
        return utftext;
    }
    // private method for UTF-8 decoding 
    _utf8_decode = function(utftext) {
        var string = "";
        var i = 0;
        var c = c1 = c2 = 0;
        while (i < utftext.length) {
            c = utftext.charCodeAt(i);
            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            } else if ((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i + 1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            } else {
                c2 = utftext.charCodeAt(i + 1);
                c3 = utftext.charCodeAt(i + 2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }
        }
        return string;
    }
}
var geolocation;
function get_latlng(){
	geolocation = new qq.maps.Geolocation();
	getCurLocation();
}
var options = {timeout: 9000};
var positionNum = 0;
var userid_ = getCookie('GkvUq___userid');
function getCurLocation(){
	try{
  geolocation.getLocation(showPosition, showErr, options);
  }catch(e){}
}
try{
	var a__= jstojava.getCurrent();
	var r = eval('(' + a__ + ')');
	lng__ = r.lng;
	lat__ = r.lat;
	//alert(a__);
}catch(e){
	getCurLocation();
}
var position;
function showPosition(position){
	positionNum ++;
	lat = position.lat;
	lng = position.lng;
	if(lng__ > 0){lng = lng__;}
	if(lat__ > 0){lat = lat__;}
	
	var province = position.province;//省份
	var city = position.city;//城市
	var district = position.district;//区域
	var addr = position.addr;//详细地址
	set_position(lat,lng,province,city,district,addr);
};
function set_position(lat,lng,province,city,district,addr){
	$.ajax({
		url:'/index.php?m=member&c=data&a=get_datas',
		type:'POST',
		data:'judge=9&lat='+lat+'&lng='+lng+'&province='+province+'&city='+city+'&district='+district+'&addr='+addr,
		dataType:'json',
		success:function(data){
			console.log(data+'---'+lat+'---'+lng);
		}
	})
}
function showErr(){
	positionNum ++;
	//alert('定位失败');
	
	console.log('定位失败');
	if(userid_ == 4){
		//alert('err:'+lat+'---'+lng);
	}
	if(lng > 0 && lat >0){
		var province = position.province;//省份
		var city = position.city;//城市
		var district = position.district;//区域
		var addr = position.addr;//详细地址
		$.ajax({
			url:'/index.php?m=member&c=data&a=get_datas',
			type:'POST',
			data:'judge=9&lat='+lat+'&lng='+lng+'&province='+province+'&city='+city+'&district='+district+'&addr='+addr,
			dataType:'json',
			success:function(data){
				console.log(data+'---'+lat+'---'+lng);
			}
		})
	}
	
	
	return '定位失败';
};

function home(){
    do{
      try{
      	jstojava.home(); return;
      }catch(e){}
      try{
          var a = getTapp();
          if(a){
              //if(a[1]==2 && Number(a[2])<2019081502){
                  //break;
              //}
              goo('dapp://home');
          }
      }catch(e){}
    }while(1>5);
    
	url = '../';
	if(getUrlParam('mobile')){ url = addUrlParam('mobile',getUrlParam('mobile'),url); }
	if(getUrlParam('version')){ url = addUrlParam('version',getUrlParam('version'),url); }
		
	window.location.href = url;
}
function measur(){
	try{
		jstojava.measur();
	}catch(e){
		window.location.href = '/body_check/index.html';
	}
}
function yingjian(){
	try{
		jstojava.yingjian();
	}catch(e){
		goo('../equipment/');
	}
}
function changeUrlArg(url, arg, arg_val) {
    var pattern = arg + '=([^&]*)';
    var replaceText = arg + '=' + arg_val;
    if (url.match(pattern)) {
        var tmp = '/(' + arg + '=)([^&]*)/gi';
        tmp = url.replace(eval(tmp), replaceText);
        return tmp;
    } else {
        if (url.match('[\?]')) {
            return url + '&' + replaceText;
        } else {
            return url + '?' + replaceText;
        }
    }
    return url + '\n' + arg + '\n' + arg_val;
}
function getQueryString(name) { 
	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i"); 
	var r = window.location.search.substr(1).match(reg); 
	if (r != null) return unescape(r[2]); return null; 
}
/*var geolocation = new qq.maps.Geolocation();
function get_latlng(){
	getCurLocation();
}
var options = {timeout: 9000};
var positionNum = 0;
function getCurLocation(){
	geolocation.getLocation(showPosition, showErr, options);
}
function showPosition(position){
	positionNum ++;
	var lat = position.lat;
	var lng = position.lng;
	alert(lat+'---'+lng);
	console.log(lat+'---'+lng);
};
function showErr(){ 
	positionNum ++;
	return '定位失败';
};*/
//点击分享
function goshare(url,title,content,image,auth) {
	var str = share_(url,title,content,image,auth);
	console.log(str);
	try{
		//JSON.stringify(str) 
		jstojava.share( str);
	}catch(e){
		
	} 
	if(isiOS){
    str = share_2(url,title,content,image,auth);
		window.location.href = "dapp://goshare=>"+ str;
	} 
}
function share_(url,title,content,image,auth){
	var str = {}; 
	//str = JSON.parse(str);
	url = url?url:location.href; 
	title = title?title:'分享标题';
	content = content?content:'分享内容';
	image = image?image:'/statics/images/no_img.jpg';
	auth = auth?auth:''; 
    str = '{"url":"'+(url)+'","title":"'+(title)+'","content":"'+(content)+'","image":"'+image+'","auth":"'+(auth)+'"}';
	return str;
}
function share_2(url,title,content,image,auth){
  var str = share_(url,encodeURI(title),encodeURI(content),image,auth)
	return str;
}
function json_decode(str_json) {
    var json = this.window.JSON;
    if (typeof json === 'object' && typeof json.parse === 'function') {
        return json.parse(str_json);
    }
    var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g;
    var j;
    var text = str_json;
    cx.lastIndex = 0;
    if (cx.test(text)) {
        text = text.replace(cx, function(a) {
            return '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
        });
    }
    if (/^[\],:{}\s]*$/.test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, '@').replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {
        j = eval('(' + text + ')');
        return j;
    }
    // If the text is not JSON parseable, then a SyntaxError is thrown. 
    throw new SyntaxError('json_decode');
}

function json_encode(mixed_val) {
    var json = this.window.JSON;
    if (typeof json === 'object' && typeof json.stringify === 'function') {
        return json.stringify(mixed_val);
    }
    var value = mixed_val;
    var quote = function(string) {
        var escapable = /[\\\"\u0000-\u001f\u007f-\u009f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g;
        var meta = { // table of character substitutions 
            '\b': '\\b',
            '\t': '\\t',
            '\n': '\\n',
            '\f': '\\f',
            '\r': '\\r',
            '"': '\\"',
            '\\': '\\\\'
        };
        escapable.lastIndex = 0;
        return escapable.test(string) ? '"' + string.replace(escapable, function(a) {
            var c = meta[a];
            return typeof c === 'string' ? c : '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
        }) + '"' : '"' + string + '"';
    };
    var str = function(key, holder) {
        var gap = '';
        var indent = ' ';
        var i = 0; // The loop counter. 
        var k = ''; // The member key. 
        var v = ''; // The member value. 
        var length = 0;
        var mind = gap;
        var partial = [];
        var value = holder[key];
        // If the value has a toJSON method, call it to obtain a replacement value. 
        if (value && typeof value === 'object' && typeof value.toJSON === 'function') {
            value = value.toJSON(key);
        }
        // What happens next depends on the value's type. 
        switch (typeof value) {
            case 'string':
                return quote(value);
            case 'number':
                return isFinite(value) ? String(value) : 'null';
            case 'boolean':
            case 'null':
                return String(value);
            case 'object':
                if (!value) {
                    return 'null';
                }
                // Make an array to hold the partial results of stringifying this object value. 
                gap += indent;
                partial = [];
                // Is the value an array? 
                if (Object.prototype.toString.apply(value) === '[object Array]') {
                    length = value.length;
                    for (i = 0; i < length; i += 1) {
                        partial[i] = str(i, value) || 'null';
                    }
                    v = partial.length === 0 ? '[]' : gap ? '[\n' + gap + partial.join(',\n' + gap) + '\n' + mind + ']' : '[' + partial.join(',') + ']';
                    gap = mind;
                    return v;
                }
                // Iterate through all of the keys in the object. 
                for (k in value) {
                    if (Object.hasOwnProperty.call(value, k)) {
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k) + (gap ? ': ' : ':') + v);
                        }
                    }
                }
                // Join all of the member texts together, separated with commas, 
                // and wrap them in braces. 
                v = partial.length === 0 ? '{}' : gap ? '{\n' + gap + partial.join(',\n' + gap) + '\n' + mind + '}' : '{' + partial.join(',') + '}';
                gap = mind;
                return v;
        }
    };
    return str('', {
        '': value
    });
}
function isWeiXin() {
	var ua = window.navigator.userAgent.toLowerCase();
	if(ua.match(/MicroMessenger/i) == 'micromessenger'){
		return true;
	}else{
		return false;
	}
}
var addEvent = (function(){
    if(document.addEventListener){
        return function(el,type,fn){
            if(el.length){
                for(var i=0;i<el.length;i++){
                    addEvent(el[i],type,fn);
                }
            }else{
                el.addEventListener(type,fn,false);
            }
        };
    }else{
        return function(el,type,fn){
            if(el.length){
                for(var i=0;i<el.length;i++){
                    addEvent(el[i],type,fn);
                }
            }else{
                el.attachEvent('on'+type,
                function(){
                    return fn.call(el,window.event);
                });
            }
        };
    }
})();
function isInclude(name){
	/*判断js或css是否加载*/
    var js= /js$/i.test(name);
    var es=document.getElementsByTagName(js?'script':'link');
    for(var i=0;i<es.length;i++) 
    if(es[i][js?'src':'href'].indexOf(name)!=-1)return true;
    return false;
}
var domain = window.location.href.split(/http:\/\/([^\.]*)/);
console.log(domain[1]);
function logout(forward){
	$.get('/index.php?m=member&a=logout&app=1&forward=');
	setTimeout(function(){
		try{
			jstojava.logout();
		}catch(d){
			var domain = window.location.href.split(/http:\/\/([^\.]*)/);
			if(domain[1]=="dr"){
				goo('/member/login_dr.html?forward='+forward);
				return;
			} 
			goo('/member/login.html?forward='+forward);
		}
	},300);
}
window.onload = function(e){
	$.ajax({
		url:'//ksy.yjxun.cn/?m=myim&c=temp&a=addBugLog&onload=1'+'&title='+$('title').text()+'&toUrl='+encodeURIComponent(window.location.href),
		async:false
	});
	/*var blob = new Blob([`onload=12345`], {type : 'application/x-www-form-urlencoded'});
	navigator.sendBeacon("//www.yjxun.cn/?m=myim&c=temp&a=addBugLog", blob);*/
}
window.onpagehide = function(e){
	//安卓原生返回键能监听 右上角返回键无效 监听goback
	var endTime = (Date.parse(new Date()) / 1000) - startTime;
	$.ajax({
		url:'//ksy.yjxun.cn/?m=myim&c=temp&a=addBugLog&onpagehide=1&duration='+endTime+'&title='+$('title').text()+'&toUrl='+encodeURIComponent(window.location.href),
		async:false
	});
	/*var blob = new Blob([`onpagehide=123`], {type : 'application/x-www-form-urlencoded'});
	navigator.sendBeacon("//www.yjxun.cn/?m=myim&c=temp&a=addBugLog", blob);*/
}

function delayimg(obj,nosize){
	var url = $(obj).attr('data-src');
	var img = new Image();
	img.src = url;
	img.onload=function(){
		obj.src = img.src;
		$(obj).addClass('delayimg');  
		obj.onload = null; //控制不要一直跳动
	}
}
function getTapp() {
	var s = navigator.userAgent; 
	var arr, reg = new RegExp("Tapp=([^/*]).*/.*Tapp_version=([^/]*)"); 

	if (arr = s.match(reg)){
		return arr;
	}else{
		return false;
	} 
}


function goo_d(){
	layer.msg('申请会员查看更多详情')
}
