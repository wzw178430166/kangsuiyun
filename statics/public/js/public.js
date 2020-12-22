function getQueryString(name){
	var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
	var r = window.location.search.substr(1).match(reg);
	if (r!=null) return unescape(r[2]); return null;
}
function more(that){
	var status = parseInt($(that).attr('data-status'));
	var text = '';
	if (1 == status) {
		status = 0;
		text = '更多查询';
		$('.tb-query-item-more').hide();
	} else {
		status = 1;
		text = '收起';
		$('.tb-query-item-more').show();
	}
	$(that).find('.l-btn-text').text(text);
	$(that).attr('data-status',status);
}
function back(){
	window.history.back();
}
function clearForm(param){
    if(param.ele) $(param.ele).form('clear');
}
/*公共ajax请求入口*/
function request(url,type,param){
	var url = arguments[0]?arguments[0]:'';
	if (!url) {
		alert('请正确输入请求地址');
		return;
	}
	var type = arguments[1]?arguments[1]:'GET';
	var param = arguments[2]?arguments[2]:{};
	var dataType = param.dataType?param.dataType:'JSON';//JSON JSONP
	var data = param.data?param.data:$(param.formEle).serialize();
	//查询条件
	if (param.query) {
		for(var k in param.query){
			var value = param.query[k];
			//if(!value || ) continue;
			if ('GET' == type) {
				url += '&'+k+'='+value;
			} else {
				data[k] = value;
			}
		}
	}
	$.ajax({
		url: url,
		type: type,
		dataType: dataType,
		data: data,
		success: function(res){
			if (param.success) param.success(res);
		},
		/*done: function(res){
			if (param.done) param.done(res);
		},*/
		error: function(xhr, textStatus, errorThrown){
			//console.log(errorThrown);
			if (param.error) param.error(errorThrown);
			//$.messager.alert('错误提示',erro,'error');
		}
	});
}

/*locate*/
function locate(param){
	var param = arguments[0]?arguments[0]:{};
	var src = '/?m=corp&c=doing&a=locate';
	if (param.address) src += '&address='+encodeURI(encodeURI(param.address));
	if (0<param.lng) src += '&lng='+param.lng;
	if (0<param.lat) src += '&lat='+param.lat;
	$('#window_locate').find('iframe').attr('src',src);
	$('#window_locate').window('open');
}
/*area*/
function getAreaConfig(){
	return ['country','province','city','district','street','community'];
}
function getArea(param){
	var param = arguments[0]?arguments[0]:{};
	var config = getAreaConfig();
	var level = param.level?param.level:0;
	//console.log(param);
	//console.log(config[level]);
	if (0<level) {
		var areaKey = config[level];
		//console.log(areaKey);
		var url = '/?m=corp&c=data&a=getAreaChildren&ajax=1';
		if (param.parent_code) url += '&parent_code='+param.parent_code;
		easyuiCombogrid({
		    ele:'#'+areaKey,
		    url: url,
		    panelWidth:150,
		    columns:[
		      {field:'name',title:'名称',width: 120}
		    ],
		    onSelect: function(res){
		    	var level = parseInt(res.level) + 1;
		    	clearArea({level:level});
		    	if (6>level) getArea({level:level,parent_code:res.code});
		    }
		});
	} else {
		$.messager.alert('错误提示','缺少必要参数','error');
	}
}
function clearArea(param){
	var param = arguments[0]?arguments[0]:{};
	if (1<param.level) {
		var level = param.level;
		var config = getAreaConfig();
		for(var k=level;k<6;k++){
			var areaKey = config[k];
			$('#'+areaKey).combogrid('clear');
		}
	}
}
function serviceArea(param){
	var url = '/?m=corp&c=doing&a=serviceArea';
	if (param.value) url += '&value='+param.value;
	if (param.text) url += '&text='+encodeURI(encodeURI(param.text));
	if (param.eleValue) url += '&eleValue='+param.eleValue;
	if (param.eleText) url += '&eleText='+param.eleText;
	layer.open({
	  type: 2,
	  title: '选择区域',
	  shadeClose: true,
	  shade: 0.8,
	  area: ['45%', '55%'],
	  content: url //iframe的url
	}); 
}
/*easyui*/
function easyuiCombogrid(param){
	var param = arguments[0]?arguments[0]:{};
	if (0<Object.keys(param).length) {
		var value = param.value?param.value:[];
		var idField = param.idField?param.idField:'id';
		var textField = param.textField?param.textField:'name';
		var columns = [];
		var fieldWidth = 80;
		if (param.columns) {
			for(var k in param.columns){
				var info = param.columns[k];
				var width = info.width?info.width:fieldWidth;
				columns.push({field:info.field,title:info.title,width:width});
			}
		} else {
			columns = [
			    {field:'id',title:'ID',width:fieldWidth},
			    {field:'name',title:'名称',width:fieldWidth}
			];
		}
		var panelWidth = param.panelWidth?param.panelWidth:450;
		$(param.ele).combogrid({
		    panelWidth:panelWidth,
		    value:value,
		    idField:idField,
		    textField:textField,
		    url:param.url,
		    columns:[columns],
		    onSelect: function(index,row){
		    	if (param.onSelect) param.onSelect(row);
		    }
		});
	} else {
		$.messager.alert('错误提示','缺少必要参数','error');
	}
}
function easyuiTree(param){
	var param = arguments[0]?arguments[0]:{};
	if (0<Object.keys(param).length) {
		$(param.ele).tree({
		    url:param.url,
		    method:'GET',
		    onClick:function(node){
		    	//console.log(node);
		    	if (param.onClick) {
		    		param.onClick(node);
		    	} else {
		    		$('#dg').datagrid('load',{parent_code:node.id});
		    		/*request('/?m=corp&c=data&a=getArea&ajax=1','get',{
		    			query: {parent_code:node.id}
			    	});*/
		    	}
		    },
		    onBeforeExpand:function(node){
		    	//console.log(node);
		    	$(param.ele).tree('options').queryParams.parent_code = node.id;
		    },
		    onExpand:function(node){
		    	//console.log(node);
		    }
		});
	} else {
		$.messager.alert('错误提示','缺少必要参数','error');
	}
}
/*material*/
function material(that){
    var type = $(that).attr('data-type');
	var name = $(that).attr('data-name');
    CKFinder.popup( {
        chooseFiles: true,
        width: 800,
        height: 600,
        onInit: function( finder ) {
            finder.on( 'files:choose', function( evt ) {
            	var num = parseInt($(that).attr('data-num'));
            	if (0<num&&num<$(that).parent().find('.ct-material-list ul li').length) return;//超过上传数量限制
                var file = evt.data.files.first();
                var html = '<li><input type="hidden" name="'+name+'" value="'+file.getUrl()+'"/><img src="'+file.getUrl()+'" onclick="veiwMaterial(this)" /><i class="operate-delete-material" onclick="deleteMaterial(this)"></i></li>';
                if ('mult'==type) {
                    $(that).parent().find('.ct-material-list ul').append(html);
                } else {
					//alert(html);
                    $(that).parent().find('.ct-material-list ul').html(html);
                }
            } );

            finder.on( 'file:choose:resizedImage', function( evt ) {
                console.log(evt.data.resizedUrl);
            } );
        }
    } );
}
function veiwMaterial(that){
	var url = $(that).attr('src');
	if (url) window.open(url);
}
function deleteMaterial(that){
	window.event? window.event.cancelBubble = true : e.stopPropagation();
	$(that).parent().remove();
}
/*ckeditor*/
//新版CKEditor取消了自动同步内容功能，提交表单前手动同步内容
function CKupdate() {
    for (instance in CKEDITOR.instances)
        CKEDITOR.instances[instance].updateElement();
}