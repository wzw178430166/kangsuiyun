function getRegionNode(regionParent,regionLevel,currentCode){
	var url = '/?m=dataManage&c=datas&a=getAreaNode&ajax=1';
	if (0 < regionParent) url += '&parent='+regionParent;
	$.ajax({
		url: url,
		type: "GET",
		dataType: "JSON",
		success: function(data){
			if (data.status == 1) {
				var html = '<option value="0">请选择</option>';
				var nodes = data.data.nodes;
				for(var i in nodes){
					var info = nodes[i];
					var selected = currentCode&&currentCode==info.code?'selected':'';
					html += '<option value="'+info.code+'" '+selected+'>'+info.name+'</option>';
				}
				$('.region-item').eq(regionLevel).html(html);
				initOperate();
			}
		}
	});
}
function initOperate(){
	$('.region-item').unbind('change');
	var _item = $('.region-item');
	_item.each(function(i) {
        $(_item[i]).on('change',function(){
			var regionParent = $(this).val();
			var regionLevel =  parseInt(i) + 1;
			if (_item.length == regionLevel) return false;
			resetRegionNode(regionLevel);
			getRegionNode(regionParent,regionLevel);
		});
    });
}
function resetRegionNode(regionLevel){
	var _item =$('.region-item');
	for(var i = 0; i< _item.length; i++){
		if (i > regionLevel) $('.region-item').eq(i).html('');
	}
}