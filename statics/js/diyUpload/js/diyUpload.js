/* 
 *	5.5  2020.10.8
 */
//var successCallBack,errorCallBack;
(function($) {
	var errorCallBack_;
	$.fn.extend({
		/*
		 *	上传方法 opt为参数配置;
		 *	serverCallBack回调函数 每个文件上传至服务端后,服务端返回参数,无论成功失败都会调用 参数为服务器返回信息;
		 */
		diyUpload: function(opt, serverCallBack) {
			if (typeof opt != "object") {
				alert('参数错误!');
				return;
			}
			var $fileInput = $(this);
			var $fileInputId = $fileInput.attr('id');
			if (opt.isbutton) {
				var isbutton = opt.isbutton;
			}
			//组装参数;
			var uploadac = opt.uploadac ? opt.uploadac : '';
			opt.server = opt.url ? opt.url : '/index.php?m=attachment&c=attachments&a=html5_upload';
			opt.server += opt.uploadac ? '&uploadac=' + uploadac : '';
			opt.server += opt.filename ? '&file_name=' + opt.filename : '';
			delete opt.url;

			if (opt.error) {
				var errorCallBack = opt.error;
				delete opt.error;
			}
			//迭代出默认配置
			$.each(getOption('#' + $fileInputId), function(key, value) {
				opt[key] = opt[key] || value;
			});
			if (opt.buttonText) {
				opt['pick']['label'] = opt.buttonText;
				//delete opt.buttonText;	
			}
			//微信判定
			var ua = navigator.userAgent.toLowerCase();
			if (1 > 3 && ua.match(/MicroMessenger/i) == "micromessenger") {
				createBox3($fileInput, uploadac, opt);
				return;
			} //android 判定
			else {
				if (getUrlParam('test')) {
					createBox2($fileInput, uploadac, opt);
					return;
				} else {
					try {
						console.log('isok', jstojava.isok());
						if (jstojava.isok() != "") {
							opt.anversion = 2;
						}
						opt.jstojava = jstojava;
						$('.gophoto').css('opacity', '1 !important');
						createBox2($fileInput, uploadac, opt);
						return;
					} catch (e) {
						var webUploader = getUploader(opt);
						if (!WebUploader.Uploader.support()) {
							alert(' 上传组件不支持您的浏览器！');
							return false;
						}
					}
				}
			}
			//绑定文件加入队列事件;
			webUploader.on('fileQueued', function(file) {
				createBox($fileInput, file, webUploader, opt);
			});
			//进度条事件
			webUploader.on('uploadProgress', function(file, percentage) {
				var $fileBox = $('#fileBox_' + file.id);
				var $diyBar = $fileBox.find('.diyBar');
				$diyBar.show();
				percentage = percentage * 100;
				showDiyProgress(percentage.toFixed(2), $diyBar);
				try {
					opt.goingCallBack(percentage);
				} catch (e) {
					console.log(e.message);
				}
			});
			//全部上传结束后触发;
			webUploader.on('uploadFinished', function() {
				$fileInput.next('.parentFileBox').children('.diyButton').remove();
			});
			//绑定发送至服务端返回后触发事件;
			webUploader.on('uploadAccept', function(object, data) {
				if (data.error) {
					var $fileBox = $('#fileBox_' + data.id);
					var $diyBar = $fileBox.find('.diyBar');
					showDiyProgress(0, $diyBar, '上传失败!');
					if (errorCallBack) {
						data.error.code = errorshow(data.error.code);
						errorCallBack(data);
					}
				} else {
					if (serverCallBack) serverCallBack(data);
				}
			});
			//上传成功后触发事件;
			webUploader.on('uploadSuccess', function(file, response) {
				var $fileBox = $('#fileBox_' + file.id);
				var $diyBar = $fileBox.find('.diyBar');
				$fileBox.removeClass('diyUploadHover');
				$diyBar.fadeOut(1000, function() {
					try {
						layer.open({
							content: '上传成功',
							skin: 'msg',
							time: 2 //2秒后自动关闭
						});
					} catch (e) {}
					//layer.msg('上传成功!');
					//$fileBox.children('.diySuccess').show();
				});
				opt.success(response);
			});
			//上传失败后触发事件;
			webUploader.on('uploadError', function(file, reason) {
				var $fileBox = $('#fileBox_' + file.id);
				var $diyBar = $fileBox.find('.diyBar');
				showDiyProgress(0, $diyBar, '上传失败!');
				var err = '上传失败! 文件:' + file.name + ' 错误码:' + reason;
				if (errorCallBack) {
					errorCallBack(err);
				}
			});
			//选择文件错误触发事件;
			webUploader.on('error', function(code) {
				//alert(code);return;
				var text = '';
				text = errorshow(code);
				//alert( text );
				showTips(text, 600, 3);
			});
			var errorshow = function(code) {
				switch (code) {
					case 'F_DUPLICATE':
						text = '该文件已经被选择了!';
						break;
					case 'Q_EXCEED_NUM_LIMIT':
						text = '上传文件数量超过限制!';
						break;
					case 'F_EXCEED_SIZE':
						text = '文件大小超过限制!';
						break;
					case 'Q_EXCEED_SIZE_LIMIT':
						text = '所有文件总大小超过限制!';
						break;
					case 'Q_TYPE_DENIED':
						text = '文件类型不正确或者是空文件!';
						break;
					default:
						text = code;
						break;
				}
				return text;
			}
			opt.errorshow = errorshow;
		}
	});
	//Web Uploader默认配置;
	function getOption(objId) {
		var da = {};
		/*
		 *	配置文件同webUploader一致,这里只给出默认配置.
		 *	具体参照:http://fex.baidu.com/webuploader/doc/index.html
		 */
		return {
			//按钮容器;
			pick: {
				id: objId,
				label: "点击选择图片"
			},
			//类型限制;
			accept: {
				title: "Images",
				extensions: "gif,jpg,jpeg,bmp,png",
				mimeTypes: "image/*"
			},
			//配置生成缩略图的选项
			thumb: {
				width: 170,
				height: 150,
				// 图片质量，只有type为`image/jpeg`的时候才有效。
				quality: 70,
				// 是否允许放大，如果想要生成小图的时候不失真，此选项应该设置为false.
				allowMagnify: false,
				// 是否允许裁剪。
				crop: true,
				// 为空的话则保留原有图片格式。
				// 否则强制转换成指定的类型。
				type: "image/jpeg"
			},
			//文件上传方式
			method: "POST",
			//服务器地址;
			server: "",
			//是否已二进制的流的方式发送文件，这样整个上传内容php://input都为文件内容
			sendAsBinary: false,
			// 开起分片上传。 thinkphp的上传类测试分片无效,图片丢失;
			chunked: true,
			// 分片大小
			chunkSize: 800 * 1024,
			//最大上传的文件数量, 总文件大小,单个文件大小(单位字节);
			fileNumLimit: 50,
			fileSizeLimit: 5000 * 1024,
			fileSingleSizeLimit: 500 * 1024,
			//
			beforeupload: function(da){},
			goingCallBack: function(da){},
			success: function(da){},
		};
	}
	//实例化Web Uploader
	function getUploader(opt) {
		return new WebUploader.Uploader(opt);;
	}
	//操作进度条;
	function showDiyProgress(progress, $diyBar, text) {
		if (progress >= 100) {
			progress = progress + '%';
			//text = text || '上传完成';
			text = text || '正在上传';
		} else {
			progress = progress + '%';
			text = text || progress;
		}
		var $diyProgress = $diyBar.find('.diyProgress');
		var $diyProgressText = $diyBar.find('.diyProgressText');
		$diyProgress.width(progress);
		$diyProgressText.text(text);
	}
	//取消事件;	
	function removeLi($li, file_id, webUploader, fid) {
		if (typeof(layer.confirm) == 'undefined') {
			layer.open({
				content: '确定删除该图片？',
				btn: ['删除', '取消'],
				yes: function(index) {
					var isrc = $('#diyFileUrl_' + file_id).val();
					var url = '/index.php?m=attachment&c=attachments&a=deleteImg&ajax=1&isrc=' + isrc;
					$.get(url, function(data) {
						var msg = data.status == 1 ? '删除成功!' : '删除失败!';
						if (data.status == 1) {
							var $fileInput = $('#' + fid);
							$fileInput.removeClass('none');
							if ($li.siblings('li').length <= 0) {
								$li.parents('.parentFileBox').remove();
							} else {
								$li.remove();
							}
						}
						layer.open({
							content: msg,
							skin: 'msg',
							time: 2 //2秒后自动关闭
						});
					}, 'json');
				}
			});
			return;
		}
		layer.confirm('确定删除该图片', {
			btn: ['删除', '取消']
		}, function() {
			try {
				webUploader.removeFile(file_id);
			} catch (e) {}
			var $fileInput = $('#' + fid);
			$fileInput.removeClass('none');
			if ($li.siblings('li').length <= 0) {
				$li.parents('.parentFileBox').remove();
			} else {
				$li.remove();
			}
			layer.closeAll();
		});
	}
	//获取文件类型;
	function getFileTypeClassName(type) {
		var fileType = {};
		var suffix = '_diy_bg';
		fileType['pdf'] = 'pdf';
		fileType['zip'] = 'zip';
		fileType['rar'] = 'rar';
		fileType['csv'] = 'csv';
		fileType['doc'] = 'doc';
		fileType['xls'] = 'xls';
		fileType['xlsx'] = 'xls';
		fileType['txt'] = 'txt';
		fileType = fileType[type] || 'txt';
		return fileType + suffix;
	}
	//创建文件操作div;	
	function createBox($fileInput, file, webUploader, opt) {
		var file_id = file.id;
		var fid = $fileInput.attr('id');
		var $fileInput_x = $fileInput;
		var $fileInput = $('#' + fid + '__');
		var $parentFileBox = $fileInput.next('.parentFileBox');
		
		var fileNum = $parentFileBox.find('.fileBoxUl').children('li').length;
		console.log(fileNum,opt.fileNumLimit);
		if(fileNum>=opt.fileNumLimit){
			var text = opt.errorshow('F_EXCEED_SIZE');
			showTips(text, 600, 3);
			return;
		}
		
		//添加父系容器;
		if ($parentFileBox.length <= 0) {
			var div = '<div class="parentFileBox" id="parentFileBox_' + fid +
				'">\
					<ul class="fileBoxUl"></ul>\
				</div>';
			$fileInput.after(div);
			//$fileInput_x.hide();
			$fileInput_x.addClass('none');
			$fileInput_x.next().removeClass('lin_s');
			$fileInput_x.next().next().removeClass('lin_s');
			$parentFileBox = $fileInput.next('.parentFileBox');
		}
		
		
		//开始上传,暂停上传,重新上传事件;
		var uploadStart = function() {
			opt.beforeupload();
			//隐藏图片删除按钮(xin) 
			$fileInput.parent().find('.ml-5').css('display', 'none');
			//开始上传
			webUploader.upload();
			try {
				$startButton.text('暂停上传').one('click', function() {
					webUploader.stop();
					$(this).text('继续上传').one('click', function() {
						uploadStart();
					});
				});
			} catch (e) {}
		}
		if (!opt.autoup) {
			//创建按钮
			if ($parentFileBox.find('.diyButton').length <= 0) {
				var div =
					'<div class="diyButton">\
						<a class="diyStart" href="javascript:void(0)">开始上传</a>\
						<a class="diyCancelAll" href="javascript:void(0)">全部取消</a>\
					</div>';
				$parentFileBox.append(div);
				var $startButton = $parentFileBox.find('.diyStart');
				var $cancelButton = $parentFileBox.find('.diyCancelAll');
				//绑定开始上传按钮;
				$startButton.one('click', uploadStart);
				//绑定取消全部按钮;
				$cancelButton.bind('click', {
					fid_: fid
				}, function() {
					var fileArr = webUploader.getFiles('queued');
					$.each(fileArr, function(i, v) {
						removeLi($('#fileBox_' + v.id), v.id, webUploader, fid_);
					});
				});
			}
		}
		//添加子容器;
		var li = '<li id="fileBox_' + file_id + '" class="diyUploadHover" data-index="' + file_id +
			'">\
				<div class="viewThumb"></div>\
				<div class="diyCancel"></div>\
				<div class="diySuccess"></div>\
				<div class="diyFileName">' +
			file.name + '</div>\
				<input type="hidden" name="' + fid + '[]" value="" id="diyFileUrl_' + file_id +
			'"/>\
				<div class="diyBar">\
						<div class="diyProgress"></div>\
						<div class="diyProgressText">0%</div>\
				</div>\
			</li>';
			
		$parentFileBox.children('.fileBoxUl').append(li);
		//父容器宽度;
		var $width = $('.fileBoxUl>li').length * 180;
		var $maxWidth = $fileInput.parent().width();
		$width = $maxWidth > $width ? $width : $maxWidth;
		$parentFileBox.width($width);
		var $fileBox = $parentFileBox.find('#fileBox_' + file_id);
		//绑定取消事件;
		var $diyCancel = $fileBox.children('.diyCancel').bind('click', function() {
			//恢复图片删除按钮(xin)
			$fileInput.parent().find('.ml-5').css('display', '');
			removeLi($(this).parent('li'), file_id, webUploader, fid);
			//delImg($('#diyFileUrl_'+file_id).val());
			//return false;	
		});
		//绑定点击事件;
		/*var $diyCancel = $fileBox.bind('click',function(){
			creatPreview($(this).attr('data-index'));
		});*/
		if (file.type.split("/")[0] != 'image') {
			var liClassName = getFileTypeClassName(file.name.split(".").pop());
			$fileBox.addClass(liClassName);
			return;
		}
		//生成预览缩略图;
		webUploader.makeThumb(file, function(error, dataSrc) {
			if (!error) {
				$fileBox.find('.viewThumb').append('<img src="' + dataSrc + '" >');
			}
		});
		if (opt.duo_img_style) {
			duo_img_style();
		}
		if (opt.autoup) {
			uploadStart();
		}
	}

	function create_parentFileBox(fid) {
		var $fileInput = $('#' + fid + '__');
		var $parentFileBox = $fileInput.next('.parentFileBox');
		var file_id = generateMixed(4);;
		//添加父系容器;
		if ($parentFileBox.length <= 0) {
			var div = '<div class="parentFileBox" id="parentFileBox_' + fid +
				'">\
					<ul class="fileBoxUl"></ul>\
				</div>';
			$fileInput.after(div);
			$parentFileBox = $fileInput.next('.parentFileBox');
		}
		//添加子容器 ;
		/*var li = '<li id="fileBox_'+file_id+'" class="diyUploadHover" style="display:none;"> \
					 <div class="diyCancel" onclick="removeLi2( $(\'#fileBox_'+file_id+'\') ,\''+file_id+'\',\''+fid+'\')"></div>\
					<div class="viewThumb"></div> \
					<input type="hidden" name="'+fid+'[]" value="" id="diyFileUrl_'+file_id+'" />\
					\
				</li>';*/
		var li = document.createElement("li");
		li.id = 'fileBox_' + file_id;
		li.className = 'diyUploadHover';
		li.style.display = 'none';
		var diyCancel = document.createElement("div");
		diyCancel.className = 'diyCancel';
		diyCancel.setAttribute('data-id','diyCancel_' + file_id);
		$(diyCancel).on('click', function() {
			//removeLi2($('#fileBox_'+file_id),file_id,fid);
			$('#fileBox_' + $(this).arrt('data-id')).remove();

		});
		var orther = '\
			<div class="viewThumb"></div> \
			<input type="hidden" name="' + fid +
			'[]" value="" id="diyFileUrl_' + file_id + '" />';
		$(diyCancel).appendTo(li);
		$(orther).appendTo(li);
		//$(li) = //+orther;
		$parentFileBox.children('.fileBoxUl').append(li);
		
		return file_id;
	}
	//创建文件操作div;	
	function createBox2($fileInput, uploadac, opt) {
		try {
			var fid = $fileInput.attr('id');
			var $fileInput__ = $('#' + fid + '__');
			var file_id;
			console.log(file_id);
			if (opt.buttonText) {
				img = opt.buttonText;
			} else {
				img = '<img src="statics/yz/app/images/icon33.jpg" id="' + fid + '2"/>';
			}
			var a = document.createElement("div");
			a.className = "webuploader-pick shz_" + fid + " abutton";
			$(a).html(img);
			$(a).on('click', function() {
				//android_upload(fid,uploadac,opt);
				//return;
				try {
					file_id = create_parentFileBox(fid);  
					errorCallBack_ = opt.error;
					console.log("userid:" + userid_ + ';');
					console.log("file_id:" + fid + '-' + uploadac + '-' + file_id + ';');

					jstojava.upload(fid, uploadac, file_id);
				} catch (e) {
					console.log('-2|' + e);
				}
			});
			a.setAttribute("data_fid", fid);
			a.setAttribute("data_uploadac", uploadac);
			a.setAttribute("data_file_id", file_id);
			$fileInput.prepend(a);
		} catch (e) {
			alert('-1|' + e);
		}
	}

	function android_upload(id, uploadac, opt) {
		try {
			file_id = generateMixed(4);
			create_parentFileBox(id, file_id);
			errorCallBack_ = opt.error;
			//alert(file_id); 
			console.log("file_id:" + fid + '-' + uploadac + '-' + file_id + ';');
			jstojava.upload(id, uploadac, file_id);
		} catch (e) {
			alert('-2|' + e);
		}
	}

	function removeLi2($li, file_id, fid) {
		var $fileInput = $('#' + fid);
		$fileInput.removeClass('none');
		if ($li.siblings('li').length <= 0) {
			$li.parents('.parentFileBox').remove();
		} else {
			$li.remove();
		}
	}
	/*
	 * 微信操作
	 */
	//创建文件操作div;	
	function createBox3($fileInput, uploadac, opt) {
		try {
			var fid = $fileInput.attr('id');
			var $fileInput__ = $('#' + fid + '__');
			var html = '<a href="javascript:upload(\'' + fid + '\',\'' + uploadac + '\')" class="webuploader-pick shz_' + fid +
				'">';
			if (opt.buttonText) {
				img = opt.buttonText;
			} else {
				img = '<img src="statics/yz/app/images/icon33.jpg" id="' + fid + '2"/>';
			}
			var a = document.createElement("a");
			a.className = "webuploader-pick shz_" + fid;
			$(a).html(img);
			$(a).on('click', function() {
				chooseImage_(function(da, da2) {
					$(this).html('<img src="' + da + '" />');
					try {
						$('.shz_' + fid).html('<img src="' + da + '" />');
					} catch (e) {}
					try {
						$('#' + fid + '_input').val(da2);
					} catch (e) {}
				});
			});
			$fileInput.html(a);
		} catch (e) {
			alert('-1|' + e);
		}
	}

	function generateMixed(n) {
		var chars = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K',
			'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
		];
		var res = "";
		for (var i = 0; i < n; i++) {
			var id = Math.ceil(Math.random() * 35);
			res += chars[id];
		}
		return res;
	}
})(jQuery);

function onImgUploadResultBack(data) {
	alert(data);
}

function upload_back(i, file_id, id) {
	try {
		//$('#'+id).hide();
		//i = 'upload_cache/' +i; //图片url地址 
		//var img = '<img src="'+i+'" width="20%" />';
		try {
			$('#parentFileBox_' + id + ' #fileBox_' + file_id + ' .viewThumb').append('<img src="' + i + '" />');
			$('#parentFileBox_' + id + ' #fileBox_' + file_id + ' input').val(i);
			$('#parentFileBox_' + id + ' #fileBox_' + file_id).show();
		} catch (e) {}
		//alert($('#parentFileBox_'+id+' #fileBox_'+file_id+' input').val());
		//layer.msg("上传成功");
		var json = {
			'id': id,
			result: {
				url: i
			}
		}
		//alert(id+'_uploadback("'+ i +'","'+ id +'");');
		eval(id + '_uploadback("' + i + '","' + id + '");');
		//successCallBack_(json);
	} catch (e) {
		alert('-3.|' + e);
	}
}

function showTips(content, height, time) {
	var windowWidth = $(window).width();
	var tipsDiv = '<div class="tipsClass">' + content + '</div>';
	$('body').append(tipsDiv);
	$('div.tipsClass').css({
		'top': height + 'px',
		'left': (windowWidth / 2) - 200 / 2 + 'px',
		'position': 'absolute',
		'padding': '3px 5px',
		'background': '#2CACFC',
		'font-size': 14 + 'px',
		'margin': '0 auto',
		'text-align': 'center',
		'width': '200px',
		'height': 'auto',
		'color': '#fff',
		'opacity': '.96'
	}).show();
	setTimeout(function() {
		$('div.tipsClass').fadeOut();
	}, (time * 1000));
}
