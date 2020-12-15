var zan_opt = {};
function zan(opt){
	if ( typeof opt != "object" ) {
		alert('参数错误!');
		return;	
	}
	zan_opt = opt;
	var url = '/index.php?m=circle&c=index&a=creatZan&ajax=1&did='+zan_opt.did+'&type='+zan_opt.type;
	//console.log(typeof(zan_opt.dcatid) != 'undefined');return;
	if (typeof(zan_opt.dcatid) != 'undefined') {url += '&dcatid='+zan_opt.dcatid;}
	//var index = layer.open({type: 2,time:5});
	$.get(url,function(data){
		//layer.close(index);
		if (data.status == '-1') {
			layer.open({
				content:'你还没登录sssss,前往登录?',
				btn:["登录",'关闭'],
				yes:function(index){
					window.location.href = '/member/login.html?&forward='+escape(window.location.href);
				}
			});
			return;
		} else if (data.status == 1) {
			$.ajax({
				url:'/index.php?m=member&c=data&a=all_zan',
				type:'POST',
				data:'',
				success:function(data){}
			})
			/*layer.open({
				content: '已点赞!'
				,skin: 'msg'
				,time: 2 //2秒后自动关闭
			});	*/		
		} else if(data.status == 2) {
			$.ajax({
				url:'/index.php?m=member&c=data&a=all_zan',
				type:'POST',
				data:'del=1',
				success:function(data){}
			})
			/*layer.open({
				content: '已取消!'
				,skin: 'msg'
				,time: 2 //2秒后自动关闭
			});*/
		} else {
			/*layer.open({
				content: '点赞失败!'
				,skin: 'msg'
				,time: 2 //2秒后自动关闭
			});*/
			return;
		}
		zan_opt.success(data.status);
	},'json');
}
function is_login(){
	if(!getcookie('auth')){
		layer.open({
			content:'你还没登录,前往登录?',
			btn:["登录",'关闭'],
			yes:function(index){
				window.location.href = '/member/login.html?forward='+escape(window.location.href);
			}
		});
		return false;
	}
	return true;
}
function initImgs(selector,width,height){
	//alert(123123);return;
	
	var selector = arguments[0] ? arguments[0] : '';
	var i_height,i_width,offset;
	var imgs = selector? $('#'+selector+' img') : $('img._reset');
	//console.log(imgs);
	imgs.each(function(i) {
		var margin = 0;
        var src = $(imgs[i]).attr('src');
		var d_width = width?width:parseInt($(this).parent().width());
		var d_height = height?height:($(imgs[i]).attr('data-height')!=undefined?$(imgs[i]).attr('data-height'):d_width);
		d_height = parseInt(d_height);
		$(imgs[i]).parent().css({"height":d_height});
		//alert(d_width+'--'+d_height);
		var img = new Image();
		img.src = src;
		if (parseInt(img.width)<parseInt(img.height)) {
			//竖图
			i_height = (parseInt(d_width)*parseInt(img.height))/parseInt(img.width);
			offset = Math.abs(i_height-d_width);
			$(imgs[i]).css({"height":"auto","marginTop":"-"+(offset/2)+"px"});
		} else if (parseInt(img.width)>parseInt(img.height)) {
			//横图
			i_width = (parseInt(d_height)*parseInt(img.width))/parseInt(img.height);
			if (i_width<d_width) {i_width = d_width;d_height=(parseInt(d_width)*parseInt(img.height))/parseInt(img.width);}
			offset = Math.abs(i_width-d_width);
			//margin = Number(offset)/2;
			$(imgs[i]).css({"width":"auto","height":d_height,"marginLeft":"-"+margin+"px","":"red"});
			//var strtr = strtr+'***'+$(imgs[i]).parent().height();
		}
		/*img.onload=function(){
			//load finsh
			if (img.width<img.height) {
				//竖图
				i_height = (Number(d_width)*img.height)/img.width;
				offset = Math.abs(i_height-d_width);
				$(imgs[i]).css({"height":"auto","marginTop":"-"+(offset/2)+"px"});
				
			} else if (img.width>img.height) {
				//横图
				i_width = (Number(d_height)*img.width)/img.height;
				if (i_width<d_width) {i_width = d_width;d_height=(Number(d_width)*img.height)/img.width;}
				offset = Math.abs(i_width-d_width);
				//alert(d_height+'---'+(offset/2));
				$(imgs[i]).css({"width":"auto","height":d_height,"marginLeft":"-"+(offset/2)+"px","":"red"});
			}
		};*/
    });
}
function delayimg(obj,callback_){
	initImgs();
	var callback = function(data){};
	if(callback_){ callback = callback_;}
	var url = $(obj).attr('data-src');
	var img = new Image();

	img.onload=function(){		
		img.onload = null;
		callback(img);   
	}	
	obj.onload = null;
	obj.src = url;
	$(obj).addClass('delayimg');
}
//手机端返回键
/*function goback(){
	//var reg=new RegExp("http\:\/\/www\.lyj008\.com/\?mobile\=android\&version=\d*");
	var reg=new RegExp("www\.yjxun\.cn/\?");
	try{
		//console.log(document.referrer);
		if(document.referrer=='' && !getUrlParam('m')){
			jstojava.exit();
			jstojava.mycleargoback();  
		}else{
			jstojava.mysetgoback('window.history.back();'); 
			window.history.back();	
		}
	}catch(e){
		var reg=new RegExp("star\.lyj008\.com/\?");
		//alert(document.referrer.match(reg) != null);
		if (getUrlParam('m') == 'star' && document.referrer.match(reg) != null) {
			window.location.href = 'http://star.lyj008.com/';
		} else {
			window.history.back();
		}
	}
}*/