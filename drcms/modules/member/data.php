<?php
defined('IN_drcms') or exit('No permission resources.');
pc_base::load_app_class('upload_public','sport',0);
pc_base::load_sys_class('form','',0);
pc_base::load_app_func('global','weixin',0);
pc_base::load_app_class('foreground', 'member',0);
pc_base::load_sys_class('upload_file','libs',0);
pc_base::load_app_class('Quan','member',0);
pc_base::load_app_class('common','myim',0);
pc_base::load_app_class('encipher','data',0);
use GatewayClient\Gateway;
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
class data{
    public function __construct(){
		define('AJAX', $_REQUEST['ajax']); 
		$this->default_db = pc_base::load_model('default_model');
		$this->member_db = pc_base::load_model('member_model');
		$this->weixin = pc_base::load_app_class('wxctrl', 'weixin');
		$this->weixin_send = pc_base::load_app_class('weixin_send', 'weixin');
		$this->page = isset($_GET['page'])&&$_GET['page']?intval($_GET['page']):1;
		$this->pageSize = $_GET['pagesize']?(int)$_GET['pagesize']:6;
		$this->ajax = intval($_REQUEST['ajax']);
		$this->fun = array(
			1=>'get_user_data',//获取用户详细信息
			2=>'get_favorite',//获取我的收藏数据
			3=>'set_sign',//用户签到操作
			4=>'set_feedback',//意见反馈
			5=>'get_activities',//获取周边活动数据
			6=>'activities_up',//报名周边活动
			7=>'get_point_data',//查询积分交易明细
			8=>'get_amount_data',//查询金币交易明细
			9=>'real_time_addr',//每个页面获取用户实时位置 写入数据 用于附近的人
			10=>'get_nearby_user',//获取附近的人列表
			11=>'get_integration',//获取积分任务列表
			12=>'get_msg_load',//我的消息 基本数据 查询所有消息数等等
			13=>'get_msg_detail',//获取某个消息类型的所有数据 列表
			14=>'get_medical_data',//获取健康档案数据
			15=>'add_medical_bpm',//新增 心率数据的方法
			16=>'get_medical_bpm',//查询 心率数据的方法 某日期X 换使用*******history查询
			17=>'add_medical_spot',//新增 血氧数据的方法
			18=>'get_medical_spot',//查询 血氧数据的方法 某日期X 换使用*******history查询
			19=>'add_medical_mmhg',//新增 血压数据的方法
			20=>'get_medical_mmhg',//查询 血压数据的方法 某日期X 换使用*******history查询
			21=>'get_coupons_list',//获取卡券列表
			22=>'get_medical_sport',//查询 运动数据的方法
			23=>'get_firends',//获取互相关注的 作为好友列表使用
			24=>'get_user_uid_data',//获取指定用户资料 用于直播间主播基本信息使用
			25=>'get_medical_bpm_history',//查询心率历史数据/指定日期/指定时间段/指定设备
			26=>'get_medical_spot_history',//查询血氧历史数据/指定日期/指定时间段/指定设备
			27=>'get_medical_mmhg_history',//查询血压历史数据/指定日期/指定时间段/指定设备
			28=>'get_medical_weight_history',//查询体重历史数据
			29=>'get_set_user_data',//获取用户设置修改个人资料的指定数据
			30=>'target_steps_get',//每日目标步数 读写方法
			31=>'get_soprt_recommend',//获取运动推荐的数据
			32=>'search_all',//搜索内容!
			33=>'add_kak',//添加亲友~申请通过~删除亲友等
			34=>'get_kak',//获取亲友列表
			35=>'get_kak_user',//查询亲友信息
			36=>'get_dingwei',//亲友位置数据
			37=>'add_medical_sleep',//新增 睡眠数据的方法 待定方法
			38=>'get_medical_sleep',//查询 睡眠数据的方法 待定方法
			39=>'update_sport_data',//更新运动排行的数据
			40=>'get_user_detail_one',//查询指定用户 小数据 头像 昵称 性别等
			41=>'get_medical_bpm_today',//查询心率数据
			42=>'get_medical_spot_today',//查询血氧数据
			43=>'get_medical_mmhg_today',//查询血压数据
			44=>'get_medical_sport_today',//查询运动数据
			45=>'get_medical_sleep_today',//查询睡眠数据
			46=>'get_recharge_record',
			47=>'get_user_today_data',//查询用户最新检测数据
			48=>'update_today_data_v3',//v3手环更新今日数据
			49=>'get_medical_sleep_today_tmp',//查询睡眠方法v2
			50=>'get_coupon_detail_data',//获取详细优惠券
			51=>'add_num_medical',//手动输入数值
			52=>'get_medical_temperature',//查询体温数据
			53=>'get_medical_bpm_today_tmp',//查询心率数据方法v2
			54=>'get_medical_mmhg_today_tmp',//查询血压数据方法v3
			55=>'get_medical_mmhg_today_tmp_v2',//查询血压数据方法IV
		);
		$this->info = pc_base::load_config('info');
		$this->upload_file = new FileUpload();
		$this->Quan = new Quan();
		$this->userid = isset($_POST['userid']) && $_POST['userid'] > 0? intval($_POST['userid']) : param::get_cookie('_userid');
		$this->nickname = param::get_cookie('_nickname');
		$this->username = param::get_cookie('_username');
		$a = $this->member_db->get_one(array('userid'=>$this->userid));
		$this->nickname = $a['nickname'];
		$this->username = $a['username'];
		$this->wx_openid = $a['wx_openid'];
		$this->regdate = $a['regdate'];
		$this->member_db->set_model(10);
		$this->member_data = $this->member_db->get_one(array('userid'=>$this->userid));
		$this->portrait = $this->member_data['portrait'];
		$this->device_text = $this->member_data['device_text'];
		param::set_cookie('_portrait',$this->portrait,0,1);
		param::set_cookie('_age',$this->member_data['age'],0,1);
		$this->encipher = new encipher();
    }
	public function init(){
		include template('member','index');
	}
	public function get_datas($isreturn = 0,$judge=''){
		$judge = $judge? $judge : intval($_REQUEST['judge']);
		$fun_name = $this->fun[$judge];
		if( !$judge ){  exit('error:-1, judge is notfound');} 
		if(!$fun_name){  exit('error:-2, fun is notfound');	}
		$datas = $this->{$fun_name}(1);
		if($isreturn){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	private function get_user_data($is_return = 0){
		$this->default_db->load('member');
		$user_data = $this->default_db->get_one(array('userid'=>$this->userid),'userid,mobile,point,amount,balance,experience,groupid');
		$sex = $this->member_data['sex'];
		if($sex == '男')$sex = 1;
		if($sex == '女')$sex = 2;
		$sex_str = $sex==1?'/statics/member/images/male.png':'/statics/member/images/female.png';
		$firend_num = count(array_intersect(explode(',',trim($this->member_data['f_s'],',')),explode(',',trim($this->member_data['my_fs'],','))));
		foreach($this->info['experience'] as $k=>$v){
			if($user_data['experience'] >= $v[0]){
				$exp_grade = $k;
				$v[1] = $v[1]!=0?$v[1]:$v[0];
				$exp_grade_diff = $v[1] - $user_data['experience'];
			}
		}
		$this->default_db->load('member_group');
		$group_name = $this->default_db->get_one(array('groupid'=>$user_data['groupid']));
		$exp_user = array('group_name'=>$group_name['name'],'point'=>$user_data['point'],'experience'=>$user_data['experience']);
		if($user_data['experience'] < 100){
			$exp_user['diff_exp'] = 100 - $user_data['experience'];
			$exp_user['diff_up'] = ceil($user_data['experience'] / 100 * 100);
		}elseif($user_data['experience'] < 1000){
			$exp_user['diff_exp'] = 1000 - $user_data['experience'];
			$exp_user['diff_up'] = ceil($user_data['experience'] / 1000 * 100);
		}elseif($user_data['experience'] < 3000){
			$exp_user['diff_exp'] = 3000 - $user_data['experience'];
			$exp_user['diff_up'] = ceil($user_data['experience'] / 3000 * 100);
		}elseif($user_data['experience'] < 4500){
			$exp_user['diff_exp'] = 4500 - $user_data['experience'];
			$exp_user['diff_up'] = ceil($user_data['experience'] / 4500 * 100);
		}elseif($user_data['experience'] < 6000){
			$exp_user['diff_exp'] = 6000 - $user_data['experience'];
			$exp_user['diff_up'] = ceil($user_data['experience'] / 6000 * 100);
		}elseif($user_data['experience'] < 7500){
			$exp_user['diff_exp'] = 7500 - $user_data['experience'];
			$exp_user['diff_up'] = ceil($user_data['experience'] / 7500 * 100);
		}elseif($user_data['experience'] < 9000){
			$exp_user['diff_exp'] = 9000 - $user_data['experience'];
			$exp_user['diff_up'] = ceil($user_data['experience'] / 9000 * 100);
		}
		/*统计收藏*/
		/*$this->default_db->setting("shop");
		$this->default_db->load_no("ku_favorite");
		//echo json_encode(array('msg'=>123));exit;
		$_favorite = $this->default_db->select(array('userid'=>$this->userid),'userid');
		$this->default_db->setting("default");*/
		$this->default_db->load('favorite');
		$shop = $this->default_db->select(array('userid'=>$this->userid));
		$this->default_db->load('favorite_video');
		$fav_video = $this->default_db->select(array('userid'=>$this->userid));
		$favorite = count($_favorite) + count($shop) + count($fav_video);
		/****************/
		/*count message*/
		$this->default_db->load('message');
		$message = $this->default_db->count('`to_userid` = '.$this->userid.' AND `is_look` = 0 AND `mss_type` <> 2 AND `mss_type` <> 3 AND `mss_type` <> 6 AND `mss_type` <> 7');
		/**************/
		$status = $this->userid?1:0;
		$this->default_db->load('cn_list');
		$cnDa = $this->default_db->get_one('`userid` = '.$this->userid.' AND `status` < 2');
		$datas = array('status'=>$status,'portrait'=>$this->portrait,'nickname'=>$this->nickname,'bind_tel'=>substr_cut($this->username,3,4),'bind_tel2'=>$user_data['mobile']?substr_cut($user_data['mobile'],3,4):'','sex_str'=>$sex_str,'userid'=>$this->userid,'point'=>$user_data['point'],'amount'=>$user_data['amount'],'balance'=>$user_data['balance'],'experience'=>$user_data['experience'],'firend_num'=>$firend_num,'exp_grade'=>$exp_grade,'exp_grade_diff'=>$exp_grade_diff,'favorite'=>$favorite,'exp_user'=>$exp_user,'message'=>$message,'cid'=>$cnDa['id']);
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	private function get_favorite($is_return = 0){
		/*$this->default_db->setting("shop");
		$this->default_db->load_no("ku_favorite");
		$_favorite = $this->default_db->select(array('userid'=>$this->userid),'userid,title,url,pcatid,pid');
		foreach($_favorite as $v){
			$pid[] = $v['pid'];
			$favorite[$v['pid']] = $v;
		}
		$this->default_db->load_no("ku_wb_shop");
		$favorite_ = $this->default_db->select(array('id'=>array('in',$pid)),'id,thumb');
		foreach($favorite_ as $k=>$v){
			if(is_array($v)&&!empty($v)) $favorite[$v['id']] = array_merge($favorite[$v['id']],$v); 
		}*/
		/*---------------------------------------*/
		$this->default_db->setting("default");
		$this->default_db->load('member');
		$user_data = $this->default_db->get_one(array('userid'=>$this->userid),'userid,phpssouid');
		$time = time();
		$auth = array(
			'time'=>$time,
			'plat'=>1,
			'sign'=>md5('1yjxun'.$time),
		);
		$url = 'http://shop.yjxun.cn/index.php?m=wpm&c=datas&a=getFavorite&uid='.$user_data['phpssouid'];
		$data = _curl_post($url,$auth);
		$data_favorite = json_decode($data,true);
		$this->default_db->load('favorite');
		$shop = $this->default_db->select(array('userid'=>$this->userid));
		$this->default_db->load('favorite_video');
		$fav_video = $this->default_db->select(array('userid'=>$this->userid));
		$datas = array('favorite'=>$shop,'fav_video'=>$fav_video,'shop'=>$data_favorite['data']['datas'],'data_favorite'=>$data_favorite['data']['datas']);
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	private function set_sign($is_return = 0){
		$date_time = time();
		$data_time_0 = strtotime(date("Y-m-d 00:00:00"));
		$this->default_db->load('sign');
		$data = $this->default_db->get_one(array('userid'=>$this->userid));
		if($data){
			if($data['last_time'] < $data_time_0){
				$total_sign = $date_time - $data['last_time'] >= 86400?1:$data['total_sign']+1;
				$this->default_db->update(array('last_time'=>$date_time,'total_sign'=>$total_sign),array('userid'=>$this->userid));
				$this->default_db->load('member');
				$this->default_db->update(array('point'=>'+=50'),array('userid'=>$this->userid));
				/*$this->default_db->load('point_detail');
				$this->default_db->insert(array('userid'=>$this->userid,'addtime'=>time(),'title'=>'签到奖励','title_'=>'每日签到奖励','num_detail'=>'50'));*/
				$this->Quan->set_task(3,20,$this->userid);//签到任务 以及 增加积分
				$datas = array('msg'=>'签到成功~','state'=>1);
			}else{
				$datas = array('msg'=>'今天已经签到过啦~','state'=>2,'sign_data'=>$data);
			}
		}else{
			$this->default_db->insert(array('userid'=>$this->userid,'first_time'=>$date_time,'last_time'=>$date_time,'total_sign'=>'1'));
			$this->default_db->load('member');
			$this->default_db->update(array('point'=>'+=50'),array('userid'=>$this->userid));
			/*$this->default_db->load('point_detail');
			$this->default_db->insert(array('userid'=>$this->userid,'addtime'=>time(),'title'=>'签到奖励','title_'=>'每日签到奖励','num_detail'=>'50'));*/
			$this->Quan->set_task(3,20,$this->userid);//签到任务 以及 增加积分
			$datas = array('msg'=>'签到成功~','state'=>1);
		}
		if($_POST['get_sign'] == 1 && $data['last_time'] && $data['last_time'] < $data_time_0){
			$datas = array('msg'=>'今天已经签到过啦~','state'=>2,'sign_data'=>$data);
		}
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	private function set_feedback($is_return = 0){
		$text = $_POST['text'];
		$lxqq = $_POST['lxqq']?(int)$_POST['lxqq']:'';
		$typeid = intval($_POST['typeid']);
		$datas = array('msg'=>'未登录~','state'=>2);
		if($this->userid){
			$this->default_db->load('guestbook');
			$siteid = get_siteid();
			$this->default_db->insert(array('siteid'=>$siteid,'introduce'=>$text,'addtime'=>time(),'typeid'=>$typeid,'name'=>$this->nickname,'shouji'=>$this->username,'lxqq'=>$lxqq));
			$datas = array('msg'=>'提交成功~','state'=>1);
		}
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	public function get_feedback(){
		if($_GET['tt'] === 1) $select = '';else $select = array('name'=>$this->username);
		$this->default_db->load('guestbook');
		$data = $this->default_db->listinfo($select,'addtime DESC',1,6);
		foreach($data as $k=>$v){
			if($_GET['tt'] == 1){
				$v['date1'] = date('Y-m-d',$v['addtime']);
				$v['date2'] = date('H:i:s',$v['addtime']);
			}
			$data_[$k] = $v;
			$data_[$k]['addtime_str'] = date('Y-m-d H:i:s',$v['addtime']);
		}
		$datas = array('list'=>$data_);
		echo json_encode($datas);
	}
	private function get_activities($is_return = 0){
		$ulat = $_POST['u_lat']>0?$_POST['u_lat']:39.89477;
		$ulng = $_POST['u_lng']>0?$_POST['u_lng']:116.35432;
		$this->default_db->load('activities');
		$id = intval($_POST['id']);
		if($id){
			$activities = $this->default_db->get_one(array('id'=>$id));
			$distace = $this->getdistance($ulng,$ulat,$activities['lng'],$activities['lat']);//获取距离
			$distace_ = sprintf('%.2f',$distace / 1000);//单位:米 转换 成 千米/公里 四舍五入 保留两个小数点
			$activities['distace'] = $distace_;
			$activities['distace_'] = $distace;
			$this->default_db->load('activities_data');
			$activities_ = $this->default_db->get_one(array('id'=>$id));
			$activities['content'] = $activities_['content'];
			$activities['content_'] = $activities_['content_'];
			$datas = array('activities'=>$activities,'enroll_data'=>$enroll_data);
		}else{
			$activities_inputtime = $this->default_db->listinfo('status=99','listorder desc,inputtime DESC',$this->page,$this->pageSize,'','','','','id,inputtime,title,start_time,lng,lat,address_str,thumb');
			foreach($activities_inputtime as $k=>$v){
				$distace = $this->getdistance($ulng,$ulat,$v['lng'],$v['lat']);//获取距离
				$distace_ = sprintf('%.2f',$distace / 1000);//单位:米 转换 成 千米/公里 四舍五入 保留两个小数点
				$activities_inputtime[$k]['distace'] = $distace_;
				$activities_inputtime[$k]['distace_'] = $distace;
			}
			$activities_start = $this->arraySequence($activities_inputtime,'start_time','SORT_ASC');
			$activities_distace = $this->arraySequence($activities_inputtime,'distace','SORT_ASC');
			$datas = array('inputtime'=>$activities_inputtime,'starttime'=>$activities_start,'distace'=>$activities_distace);
		}
		if($_GET['index3_'] == 1) exit($_GET['jsoncallback_____']."(".json_encode($datas).")");
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	function activities_up($is_return = 0){
		$this->default_db->load('activities_data');
		$id = intval($_POST['id']);
		$data = $this->default_db->get_one(array('id'=>$id));
		$data_ = strpos($data['enroll'],','.$this->userid);
		if($data_ === false){
			$this->default_db->update(array('enroll'=>','.$this->userid),array('id'=>$id));
			$datas = array('state'=>1,'msg'=>'报名成功~');
			$this->default_db->load('message');
			$this->default_db->insert(array('content'=>'【<span style="color: #f73116">活动提醒</span>】您于 '.date('Y-m-d H:i:s').' 报名了一个活动，点击前往查看活动详情。','subject'=>'活动提醒','message_time'=>time(),'status'=>1,'folder'=>'inbox','send_to_id'=>$this->username,'send_from_id'=>'SYSTEM','replyid'=>0,'del_type'=>0,'mss_type'=>5,'to_userid'=>$this->userid,'to_nickname'=>$this->nickname,'go_url'=>'/index.php?m=member&c=data&a=activity&id='.$id,'update_time'=>time()));
		}else{
			$datas = array('state'=>2,'msg'=>'请勿重复报名~');
		}
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	private function get_point_data($is_return = 0){
		$this->pageSize = 10;
		$this->default_db->load('point_detail');
		$data = $this->default_db->listinfo(array('userid'=>$this->userid),'addtime DESC',$this->page,$this->pageSize);
		if($data){
			foreach($data as $k=>$v){
				if($v['num_detail'] > 0){
					$num_detail = '+'.$v['num_detail'];
					$this_css = 'style="color:green"';
				}else{
					$num_detail = $v['num_detail'];
					$this_css = 'style="color:red"';
				}
				$year = date('Y',$v['addtime']);
				$month = date('m',$v['addtime']);
				$data_[$year.'年'.$month.'月'][$k] = $v;
				$data_[$year.'年'.$month.'月'][$k]['addtime_str'] = date('Y-m-d H:i:s',$v['addtime']);
				$data_[$year.'年'.$month.'月']['html_str'] .= '<div class="account_line"><p class="line"><span class="line_l fl">'.$v['title'].'</span><span class="line_r fr" '.$this_css.'>'.$num_detail.'</span></p><p>'.$v['title_'].'</p><p>'.date('Y-m-d H:i:s',$v['addtime']).'</p></div>';
			}
			$html_str = '';
			foreach($data_ as $k=>$v){
				$html_str .= '<div class="account_box"><div class="account"><p class="title">'.$k.'</p></div><div class="account_box_">'.$v['html_str'].'</div></div>';
				continue;
			}
		}
		$datas = array('point_data'=>$data_,'html_str'=>$html_str);
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	private function get_amount_data($is_return = 0){
		$this->pageSize = 10;
		$this->default_db->load('amount_detail');
		$data = $this->default_db->listinfo(array('userid'=>$this->userid),'addtime DESC',$this->page,$this->pageSize);
		if($data){
			foreach($data as $k=>$v){
				if($v['num_detail'] > 0){
					$num_detail = '+'.$v['num_detail'];
					$this_css = 'style="color:green"';
				}else{
					$num_detail = $v['num_detail'];
					$this_css = 'style="color:red"';
				}
				$year = date('Y',$v['addtime']);
				$month = date('m',$v['addtime']);
				$data_[$year.'年'.$month.'月'][$k] = $v;
				$data_[$year.'年'.$month.'月'][$k]['addtime_str'] = date('Y-m-d H:i:s',$v['addtime']);
				$data_[$year.'年'.$month.'月']['html_str'] .= '<div class="account_line"><p class="line"><span class="line_l fl">'.$v['title'].'</span><span class="line_r fr" '.$this_css.'>'.$num_detail.'</span></p><p>'.$v['title_'].'</p><p>'.date('Y-m-d H:i:s',$v['addtime']).'</p></div>';
			}
			$html_str = '';
			foreach($data_ as $k=>$v){
				$html_str .= '<div class="account_box"><div class="account"><p class="title">'.$k.'</p></div><div class="account_box_">'.$v['html_str'].'</div></div>';
				continue;
			}
		}
		$datas = array('amount_data'=>$data_,'html_str'=>$html_str);
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	private function get_integration($is_return = 0){
		$this->pageSize = 10;
		$this->default_db->load('task_finish');
		$task_data = $this->default_db->get_one(array('userid'=>$this->userid));
		$this->default_db->load('integration');
		$data = $this->default_db->listinfo('');
		foreach($data as $k=>$v){
			$data_[$k] = $v;
			if(strpos($task_data['task_str'],','.$v['id']) !== false){
				$data_[$k]['is_wan'] = '<span class="fr is_wan is_wan_">完成</span>';
				$data_[$k]['onclick_'] = 'onClick="goo(\''."#".'\')"';
			}else{
				$data_[$k]['is_wan'] = '<span class="fr is_wan is_wan" onClick="if(is_login_()){goo(\''.$v['go_url'].'\');}">未完成</span>';
				$data_[$k]['onclick_'] = 'onClick="goo(\''.$v['go_url'].'\')"';
			}
		}
		$this->default_db->load('member');
		$member_data = $this->default_db->get_one(array('userid'=>$this->userid),'userid,point');
		$datas = array('integration'=>$data_,'member_data'=>$member_data);
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	private function real_time_addr($is_return = 0){
		$lat = $_POST['lat'];
		$lng = $_POST['lng'];
		$lat_lng = $this->Convert_GCJ02_To_BD09($lat,$lng);
		$lng_ = $lat_lng['lng'];
		$lat_ = $lat_lng['lat'];
		$province = $_POST['province'];//省份
		$city = $_POST['city'];//城市
		$district = $_POST['district'];//区域
		$addr = $_POST['addr'];//详细地址
		if($this->userid && $lat && $lng){
			$this->default_db->load('member_rtp');
			$data = $this->default_db->get_one(array('userid'=>$this->userid));
			if($data){
				$this->default_db->update(array('lat'=>$lat,'lng'=>$lng,'province'=>$province,'city'=>$city,'district'=>$district,'addr'=>$addr,'lat_'=>$lat_,'lng_'=>$lng_,'addtime'=>time(),'nickname'=>$this->nickname,'portrait'=>$this->portrait,'sex'=>$this->member_data['sex']),array('userid'=>$this->userid));
			}else{
				$this->default_db->insert(array('userid'=>$this->userid,'lat'=>$lat,'lng'=>$lng,'province'=>$province,'city'=>$city,'district'=>$district,'addr'=>$addr,'lat_'=>$lat_,'lng_'=>$lng_,'addtime'=>time(),'nickname'=>$this->nickname,'portrait'=>$this->portrait,'sex'=>$this->member_data['sex']));
			}
		}else{
			$datas = array('state'=>2,'msg'=>'未登录!');
		}
		//更新用户信息表
		try {
			pc_base::load_app_class('tool','member',0);
			$tool = new tool();
			$tool->doInformation(array('user_id'=>$this->userid,'type'=>'location','province'=>$province,'city'=>$city,'district'=>$district,'address'=>$addr,'lng'=>$lng,'lat'=>$lat));
		} catch (Exception $e) {
			//echo $e->getMessage();
		}
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	private function get_nearby_user($is_return = 0){
		/*附近的人 START**********************************/
		$lat = $_POST['lat']>0?$_POST['lat']:39.89477;
		$lng = $_POST['lng']>0?$_POST['lng']:116.35432;
		$lat_lng = $this->Convert_GCJ02_To_BD09($lat,$lng);
		$lng = $lat_lng['lng'];
		$lat = $lat_lng['lat'];
		//获取该点周围的4个点
		$distance = 50; //范围（单位千米）
		$EARTH_RADIUS = 6371; //地球半径，平均半径为6371km
		$dlng = 2 * asin(sin($distance / (2 * $EARTH_RADIUS)) / cos(deg2rad($lat)));
		$dlng = rad2deg($dlng);
		$dlat = $distance / $EARTH_RADIUS;
		$dlat = rad2deg($dlat);
		$squares = array('left-top' => array('lat' => $lat + $dlat, 'lng' => $lng - $dlng), 'right-top' => array('lat' => $lat + $dlat, 'lng' => $lng + $dlng), 'left-bottom' => array('lat' => $lat - $dlat, 'lng' => $lng - $dlng), 'right-bottom' => array('lat' => $lat - $dlat, 'lng' => $lng + $dlng));
		$info_sql = "lat<>0 and lat>{$squares['right-bottom']['lat']} and lat<{$squares['left-top']['lat']} and lng>{$squares['left-top']['lng']} and lng<{$squares['right-bottom']['lng']} and userid != 0";
		/*附近的人 END***********************************/
		$this->pageSize = 50;
		$this->default_db->load('member_rtp');
		$data = $this->default_db->listinfo($info_sql,'addtime DESC',$this->page,$this->pageSize);
		$new_time = time();
		$data_uid = '';
		foreach($data as $k=>$v){
			$data_uid .= $v['userid'].',';
			$data_[$v['userid']] = $v;
			$distace = $this->getdistance($v['lng'],$v['lat'],$lng,$lat);//获取距离
			$distace = sprintf('%.2f',$distace / 1000);//单位:米 转换 成 千米/公里 四舍五入 保留两个小数点
			$data_[$v['userid']]['distace'] = $distace;
			$diff_time = $this->timediff($v['addtime'],$new_time);
			$diff_time_str = '';
			if($diff_time['day'] > 0) $diff_time_str = $diff_time['day'].'天 前';
			elseif($diff_time['hour'] > 0) $diff_time_str = $diff_time['hour'].'小时 前';
			elseif($diff_time['min'] > 0) $diff_time_str = $diff_time['min'].'分钟 前';
			elseif($diff_time['sec'] > 0) $diff_time_str = $diff_time['sec'].'秒 前';
			$data_[$v['userid']]['diff_time'] = $diff_time_str;
		}
		if(!$data_uid) return;
		$this->default_db->load('member_detail');
		$user_data = $this->default_db->select('userid in ('.rtrim($data_uid,',').')','userid,f_s,portrait,sex');
		$this->default_db->load('member');
		$user_data_ = $this->default_db->select('userid in ('.rtrim($data_uid,',').')','userid,nickname,username');
		$this->default_db->load('online_users');
		$online_da = $this->default_db->select('userid in ('.rtrim($data_uid,',').')');
		foreach($online_da as $v){
			if(!empty($v['last_socket_id']) && $v['last_socket_id'] > 0){
				if(Gateway::isOnline($v['last_socket_id'])){
					$adc = array('weixin_no','android_no','apple_no');
					if($v['wx_socket_id'] > 0 && Gateway::isOnline($v['wx_socket_id'])) $adc[0] = 'weixin_ok';
					if($v['ad_socket_id'] > 0 && Gateway::isOnline($v['ad_socket_id'])) $adc[1] = 'android_ok';
					if($v['ios_socket_id'] > 0 && Gateway::isOnline($v['ios_socket_id'])) $adc[2] = 'apple_ok';
					$data_[$v['userid']]['online_status'] = '<img src="/statics/images/'.$adc[0].'.png"/><img src="/statics/images/'.$adc[1].'.png"/><img src="/statics/images/'.$adc[2].'.png"/>';
				}else{
					$adc = array('weixin_no','android_no','apple_no');
					$data_[$v['userid']]['online_status'] = '<img src="/statics/images/'.$adc[0].'.png"/><img src="/statics/images/'.$adc[1].'.png"/><img src="/statics/images/'.$adc[2].'.png"/>';
				}
			}
		}
		foreach($user_data_ as $v){
			$data_[$v['userid']]['nickname'] = $v['nickname'];
			$data_[$v['userid']]['username'] = substr_cut($v['username'],3,4);
		}
		foreach($user_data as $v){
			$f_s_arr = explode(',',trim($v['f_s'],','));
			if($data_[$v['userid']]['userid'] != $this->userid && strrpos($this->member_data['f_s'],','.$v['userid'].',') !== false){
				$data_[$v['userid']]['follow'] = '<div class="fans_line_r fr" style="border:1px solid #2bacfd; color:#2bacfd;" onClick="clear_user('.$v['userid'].',this)">取消关注</div>';
				//$data_[$v['userid']]['follow'] = '<div class="fans_line_r fr" style="border:1px solid #2bacfd; color:#2bacfd;" onClick="setFollow(2,'.$v['userid'].',this)"><span>取消关注</span></div>';
			}else if($data_[$v['userid']]['userid'] != $this->userid && strrpos($this->member_data['f_s'],','.$v['userid'].',') === false){
				$data_[$v['userid']]['follow'] = '<div class="fans_line_r fr" style="border:1px solid #2bacfd; color:#2bacfd;" onClick="follow_user('.$v['userid'].',this)">立即关注</div>';
				//$data_[$v['userid']]['follow'] = '<div class="fans_line_r fr" style="border:1px solid #2bacfd; color:#2bacfd;" onClick="setFollow(1,'.$v['userid'].',this)"><span>立即关注</span></div>';
			}else{
				$data_[$v['userid']]['follow'] = '';//用户自己
			}
			if($v['userid'] == $data_[$v['userid']]['userid']) $data_[$v['userid']]['portrait'] = $v['portrait'];
			if($v['sex'] == 1 || $v['sex'] == '男') $sex = 1;
			if($v['sex'] == 2 || $v['sex'] == '女') $sex = 2;
			$data_[$v['userid']]['sex_str'] = $sex == 1?'/statics/member/images/male.png':'/statics/member/images/female.png';
		}
		foreach($data_ as $v){
			if(!$v['username']) unset($data_[$v['userid']]);
			if(!$data_[$v['userid']]['follow']) unset($data_[$v['userid']]);
			if(strpos($v['portrait'],'nophoto.gif') !== false) unset($data_[$v['userid']]);
			if(empty($v['online_status'])) $data_[$v['userid']]['online_status'] = '离线';
		}
		unset($data_[$this->userid]);
		$data_desc = $this->arraySequence($data_,'addtime');
		$datas = array('rtp_data'=>$data_desc,'squares'=>$squares,'user_data'=>$user_data);
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	private function get_msg_load($is_return = 0){
		$datas = $this->get_msg_count_fun();
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	private function get_msg_detail($is_return = 0){
		$this->pageSize = 10;
		/**/
		$this->default_db->load('kith_and_kin');
		$one_data = $this->default_db->select(array('kakuid'=>$this->userid));
		$two_data = $this->default_db->select(array('fuid'=>$this->userid));
		$uid_str = ',';
		foreach($one_data as $v){
			$uid_str .= $v['fuid'].',';
			$uid_arr[] = $v['fuid'];
		}
		foreach($two_data as $v){
			$uid_str .= $v['kakuid'].',';
			$uid_arr[] = $v['kakuid'];
		}
		$uid_str = trim($uid_str,',');
		/**/
		$this->default_db->load('message');
		$mss_type = (int)$_POST['mss_type'];
		$mss_type_ = (int)$_POST['mss_type_'];
		$msg_id = (int)$_POST['msgid'];
		if($mss_type_ == 1){
			$this->get_msg_detail_($mss_type,$msg_id);
			exit();
		}
		if($mss_type != 2){
			$data = $this->default_db->listinfo('`mss_type` = '.$mss_type.' AND (`to_userid` = 0 OR `to_userid` = '.$this->userid.') AND `message_time` > '.$this->regdate,'message_time DESC',$this->page,$this->pageSize);
		}else{
			if($uid_str){
				$data = $this->default_db->listinfo('`mss_type` = '.$mss_type.' AND (`to_userid` = 0 OR `to_userid` = "'.$this->userid.','.$uid_str.'")','message_time DESC',$this->page,$this->pageSize);
			}else{
				$data = array();
			}
		}
		foreach($data as $k=>$v){
			$data_[$k] = $v;
			$data_[$k]['add_time'] = date('Y-m-d H:i:s',$v['message_time']);
			if($data_[$k]['is_look'] == 0 && $v['to_userid'] != 0){
				$data_[$k]['is_look_str'] = '<font style="color:red">未阅读</font>';
				$data_[$k]['mss_type_2_class'] = 'remind_box_1';
			}else if($data_[$k]['is_look'] == 1 && $v['to_userid'] != 0){
				$data_[$k]['is_look_str'] = '已阅读';
				$data_[$k]['mss_type_2_class'] = 'remind_box_2';
			}else{
				$data_[$k]['is_look_str'] = '';
				$data_[$k]['mss_type_2_class'] = 'remind_box_2';
			}
		}
		$datas = array('mss_data'=>$data_,'uid_str'=>$uid_str);
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	public function get_fir_data(){
		if($_GET['userid_']) $this->userid = intval(sys_auth($_GET['userid_'], 'DECODE'));
		$this->default_db->load('kith_and_kin');
		$one_data = $this->default_db->select(array('kakuid'=>$this->userid));
		$two_data = $this->default_db->select(array('fuid'=>$this->userid));
		$uid_str = ',';
		foreach($one_data as $v){
			$uid_str .= $v['fuid'].',';
			$uid_arr[] = $v['fuid'];
		}
		foreach($two_data as $v){
			$uid_str .= $v['kakuid'].',';
			$uid_arr[] = $v['kakuid'];
		}
		$uid_str = trim($uid_str,',');
		$this->default_db->load('message');
		$data = $this->default_db->select('`mss_type` = 2 AND `is_look` = 0 AND `to_userid` in ('.$uid_str.')');
		if($data){
			echo $_GET['jsoncallback__']."(".json_encode(array('num'=>1)).")";
		}else{
			echo $_GET['jsoncallback__']."(".json_encode(array('num'=>2)).")";
		}
	}
	public function if_portrait_fun(){
		if($_GET['userid_'] != 'null'){$this->userid = intval(sys_auth($_GET['userid_'], 'DECODE'));}
		$this->default_db->load('member');
		$data_ = $this->default_db->get_one(array('userid'=>$this->userid));
		if(!$data_){echo $_GET['jsoncallback___']."(".json_encode(array('num'=>3)).")";exit();}
		$this->default_db->load('member_detail');
		$data = $this->default_db->get_one(array('userid'=>$this->userid));
		if($data['portrait'] == '/statics/images/member/nophoto.gif' || $data_['nickname'] == ''){
			//未上传头像 || 设置昵称
			echo $_GET['jsoncallback___']."(".json_encode(array('num'=>2,'member'=>$data)).")";
		}else 
			if($data['portrait'] != '/statics/images/member/nophoto.gif' && $data_['nickname'] != '')
		{
			//已上传头像 || 设置昵称
			if(!is_file('./'.$_SERVER['CONTEXT_DOCUMENT_ROOT'].$data['portrait']) || strpos($data['portrait'],'upload_cache') !== false){
				echo $_GET['jsoncallback___']."(".json_encode(array('num'=>2)).")";exit();
			}
			echo $_GET['jsoncallback___']."(".json_encode(array('num'=>1)).")";
		}//end else if
	}
	private function get_msg_detail_($mss_type,$msg_id = ''){
		$this->default_db->load('kith_and_kin');
		$one_data = $this->default_db->select(array('kakuid'=>$this->userid));
		$two_data = $this->default_db->select(array('fuid'=>$this->userid));
		$uid_str = ',';
		foreach($one_data as $v){
			$uid_str .= $v['fuid'].',';
			$uid_arr[] = $v['fuid'];
		}
		foreach($two_data as $v){
			$uid_str .= $v['kakuid'].',';
			$uid_arr[] = $v['kakuid'];
		}
		$uid_str = trim($uid_str,',');
		/**/
		$this->default_db->load('message');
		$mss_type = (int)$_POST['mss_type'];
		$this->default_db->update(array('is_look'=>1),'`mss_type` = '.$mss_type.' AND `is_look` = 0 AND `to_userid` = '.$this->userid);
		if($mss_type == 1 && $msg_id && $msg_id > 0){
			$this->default_db->load('message_see_record');
			$daa = $this->default_db->get_one(array('messageid'=>$msg_id,'userid'=>$this->userid));
			if(empty($daa)){
				$this->default_db->insert(array('messageid'=>$msg_id,'userid'=>$this->userid,'addtime'=>time()));
			}
		}
	}
	private function get_medical_data($is_return = 0){ }
	function add_medical_bpm($post = 0){
		/*新增心率数据 方法*/
		$new_year = date('Y');
		$new_month = date('n');
		$add_date = $_POST['add_date']?$_POST['add_date']:time();//检测数据 是否有指定的时间戳
		$add_time = $_POST['add_time']?$_POST['add_time']:time();//检测数据 是否有指定的时间戳
		$num_ = $post['health_num'];
		if($num_ > 0){
			$num = $num_;//心率数值
		}else{
			$num = $_POST['num']?$_POST['num']:0;//心率数值
		}
		$state = 1;//默认为1 正常
		if($num > 130){
			$state = 2;//偏高
			$state_str = '偏高';
		}else if($num < 60){
			$state = 3;//偏低
			$state_str = '偏低';
		}
		$this->default_db->load('medical_bpm');
		$bpm_data = $this->default_db->get_one(array('userid'=>$this->userid,'year'=>$new_year,'month'=>$new_month));
		$data_arr = json_decode($bpm_data['data_json'],true);
		///*
		$cache_arr_start = getcache('bpm_cache_u'.$this->userid);//第零位为start时 测量开始
		$cache_arr_end = array_reverse(getcache('bpm_cache_u'.$this->userid));//数组反向排序 第零位为end时 测量结束
		if($cache_arr_end[0]['status'] == 'end'){
			$nums = 0;//测量数据
			$nums_ = 0;//数据条数
			foreach($cache_arr_start as $v){
				if($v['status'] != 'start' && $v['status'] != 'end' && $v['add_time'] <= $cache_arr_end[1]['add_time'] + 120){
					if($v['num']) $nums = $v['num'];
					$nums_ = 1;
					//$nums_++;
				}
			}
			delcache('bpm_cache_u'.$this->userid);
			$nums = $nums / $nums_;//平均数据
			//执行写入数据库
			$state = 1;//默认为1 正常
			if($nums > 100){
				$state = 2;//偏高
				$state_str = '偏高';
			}else if($nums < 60){
				$state = 3;//偏低
				$state_str = '偏低';
			}
			$data_arr[] = array(
				'add_date'=>date('Ymd',$add_date),
				'add_time'=>$add_time,
				'hrDate'=>$post['hrDate'],
				'num'=>$nums,
				'state'=>$state,
				'device_name'=>$_POST['device_name'],
				'device_number'=>$_POST['device_number']
			);
			$data_json = json_encode($data_arr);
			if($nums > 0){
				if(!empty($bpm_data) && $bpm_data){
					$this->default_db->update(array('data_json'=>$data_json,'update_time'=>time(),'today_num'=>$nums,'device_name'=>$_POST['device_name'],'device_number'=>$_POST['device_number']),array('userid'=>$this->userid,'year'=>$new_year,'month'=>$new_month));
				}else{
					$this->default_db->insert(array('userid'=>$this->userid,'username'=>$this->username,'year'=>$new_year,'month'=>$new_month,'data_json'=>$data_json,'update_time'=>time(),'today_num'=>$nums,'device_name'=>$_POST['device_name'],'device_number'=>$_POST['device_number']));
				}
				if($state == 2) $this->Quan->health_status(6,1,$this->userid);
				if($state == 3) $this->Quan->health_status(5,1,$this->userid);
				if($state == 1) $this->Quan->health_status(0,0,$this->userid,array(5,6));//将健康标签去除 恢复正常状态
			}
		}else{
			$nums = 0;//测量数据
			$nums_ = 0;//数据条数
			foreach($cache_arr_start as $v){
				if($v['status'] != 'start' && $v['status'] != 'end' && $v['add_time'] <= $cache_arr_end[1]['add_time'] + 120){
					if($v['num']) $nums = $v['num'];
					$nums_ = 1;
					//$nums_++;
				}
			}
			//var_dump($cache_arr_start);exit;
			//delcache('bpm_cache_u'.$this->userid);
			$nums = $nums / $nums_;//平均数据
			//执行写入数据库
			$state = 1;//默认为1 正常
			if($nums > 100){
				$state = 2;//偏高
				$state_str = '偏高';
			}else if($nums < 60){
				$state = 3;//偏低
				$state_str = '偏低';
			}
			//$data_arr = array_reverse($data_arr);
			$data_arr_count = count($data_arr);
			$data_arr_count = $data_arr_count>0?$data_arr_count-1:0;
			//var_dump($add_time - $data_arr[$data_arr_count]['add_time']);
			if($add_time - $data_arr[$data_arr_count]['add_time'] >= 30){
				//$data_arr = array_reverse($data_arr);
				$data_arr[] = array(
					'add_date'=>date('Ymd',$add_date),
					'add_time'=>$add_time,
					'hrDate'=>$post['hrDate'],
					'num'=>$num,
					'state'=>$state,
					'device_name'=>$post['device_name'],
					'device_number'=>$post['device_number']
				);
			}else{
				$data_arr[$data_arr_count]['add_date'] = date('Ymd',$add_date);
				$data_arr[$data_arr_count]['add_time'] = $add_time;
				$data_arr[$data_arr_count]['hrDate'] = $post['hrDate'];
				$data_arr[$data_arr_count]['num'] = $num;
				$data_arr[$data_arr_count]['state'] = $state;
				$data_arr[$data_arr_count]['device_name'] = $post['device_name'];
				$data_arr[$data_arr_count]['device_number'] = $post['device_number'];
				//$data_arr = array_reverse($data_arr);
			}
			$data_json = json_encode($data_arr);
			if($num > 0){
				if(!empty($bpm_data) && $bpm_data){
					$this->default_db->update(array('data_json'=>$data_json,'update_time'=>time(),'today_num'=>$num,'device_name'=>$_POST['device_name'],'device_number'=>$_POST['device_number']),array('userid'=>$this->userid,'year'=>$new_year,'month'=>$new_month));
				}else{
					$this->default_db->insert(array('userid'=>$this->userid,'username'=>$this->username,'year'=>$new_year,'month'=>$new_month,'data_json'=>$data_json,'update_time'=>time(),'today_num'=>$num,'device_name'=>$_POST['device_name'],'device_number'=>$_POST['device_number']));
				}
				if($state == 2) $this->Quan->health_status(6,1,$this->userid);
				if($state == 3) $this->Quan->health_status(5,1,$this->userid);
				if($state == 1) $this->Quan->health_status(0,0,$this->userid,array(5,6));//将健康标签去除 恢复正常状态
				if(($state == 2 || $state == 3) && $bpm_data['today_num'] != $num){
					$ttt['userid'] = $this->userid;
					//写入数据异常表
					$this->Quan->msg_bpm_unusual($ttt,$num,$state,1);
				}
			}
			//检查数据是否异常1.0 即将废弃
			/*try{
				pc_base::load_app_class('abnormal','hardware',0);
				$hardware = array('x9pro'=>array('id'=>6),'v3'=>array('id'=>5),'tjd'=>array('id'=>8));
				$hardwareid = $hardware[$post['device_name']]?$hardware[$post['device_name']]['id']:0;
				$data = array('sn'=>$_POST['device_mac']?:'','imsi'=>'','value'=>$post['health_num']);
				$datas = array('data'=>$data,'param'=>'hr','hardwareid'=>$hardwareid,'userid'=>$this->userid);
				$abnormal = new abnormal();
				$abnormal->checkAbnormal($datas);
			}catch (Exception $e) {
				//echo 'Caught exception: ',  $e->getMessage(), "\n";
			}*/
			
			//设备数据上传2.0
			//$this->default_db->load('logtxt');
			//$this->default_db->insert(array('log'=>'param：'.array2string($post)));
			try{
				pc_base::load_app_class('tools','equipment',0);
				//dictionary_id:1 心率 category_id:4 手环 model_id:
				$category_id = $model_id = 0;
				switch($post['device_name']){
					case 'x9pro':
						$category_id = 4;
						$model_id = 3;
						break;
					case 'v3':
						$category_id = 4;
						$model_id = 4;
						if (!$_POST['device_mac']) $_POST['device_mac'] = 'tmp_'.date('YmdHi');
						break;
					case 'W3':
					case 'tjd':
						$category_id = 4;
						$model_id = 5;
						break;
				}
				$param = array('user_id'=>$this->userid,'dictionary_id'=>1,'category_id'=>$category_id,'model_id'=>$model_id,'sn'=>$_POST['device_mac'],'value'=>intval($post['health_num']));
				$tools = new tools();
				$tools->create($param);
			}catch (Exception $e) {
				//echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
		}
	}
	public function add_num_medical(){
		$year = date('Y');
		$month = date('m');
		$add_time = time();
		if($_GET['type'] == 'mmhg'){
			$state = 1;//默认为1 正常
			$low = $_POST['health_low'];
			$high = $_POST['health_high'];
			if($high >= 140 || $low >= 90){
				$state = 2;//偏高
				$state_str = '偏高';
			}else if($high <= 90 || $low <= 60){
				$state = 3;//偏低
				$state_str = '偏低';
			}
			$this->default_db->load('medical_mmhg');
			$da1 = $this->default_db->get_one(array('year'=>$year,'month'=>$month,'userid'=>$this->userid));
			$data_arr = json_decode($da1['data_json'],true);
			$data_arr[] = array(
				'add_date'=>date('Ymd',time()),
				'add_time'=>$add_time,
				'bpm'=>0,
				'low'=>$low,
				'high'=>$high,
				'state'=>$state,
				'bpDate'=>date('Y-m-d H:i:s',time()),
				'device_name'=>$_POST['device_name'],
				'device_number'=>$_POST['device_number']
			);
			$data_json = json_encode($data_arr);
			if(!empty($da1) && $da1){
				$this->default_db->update(array('data_json'=>$data_json,'update_time'=>time(),'today_high'=>$high,'today_low'=>$low,'device_name'=>$_POST['device_name'],'device_number'=>$_POST['device_number']),array('userid'=>$this->userid,'year'=>$year,'month'=>$month));
			}else{
				$this->default_db->insert(array('userid'=>$this->userid,'username'=>$this->username,'year'=>$year,'month'=>$month,'data_json'=>$data_json,'update_time'=>time(),'today_high'=>$high,'today_low'=>$low,'device_name'=>$_POST['device_name'],'device_number'=>$_POST['device_number']));
			}
			$this->default_db->load('member_detail');
			$this->default_db->update(array('healthy_mmhg'=>$high.','.$low),array('userid'=>$this->userid));
		}else if($_GET['type'] == 'bpm'){
			$nums = $_POST['health_num'];
			$state = 1;//默认为1 正常
			if($nums > 100){
				$state = 2;//偏高
			}else if($nums < 60){
				$state = 3;//偏低
			}
			$this->default_db->load('medical_bpm');
			$da2 = $this->default_db->get_one(array('year'=>$year,'month'=>$month,'userid'=>$this->userid));
			$data_arr = json_decode($da2['data_json'],true);
			$data_arr[] = array(
				'add_date'=>date('Ymd',time()),
				'add_time'=>$add_time,
				'hrDate'=>date('Y-m-d'),
				'num'=>$nums,
				'state'=>$state,
				'device_name'=>$_POST['device_name'],
				'device_number'=>$_POST['device_number']
			);
			if($nums > 0){
				if(!empty($da2) && $da2){
					$this->default_db->update(array('data_json'=>json_encode($data_arr),'update_time'=>time(),'today_num'=>$nums,'device_name'=>$_POST['device_name'],'device_number'=>$_POST['device_number']),array('userid'=>$this->userid,'year'=>$year,'month'=>$month));
				}else{
					$this->default_db->insert(array('userid'=>$this->userid,'username'=>$this->username,'year'=>$year,'month'=>$month,'data_json'=>json_encode($data_arr),'update_time'=>time(),'today_num'=>$nums,'device_name'=>$_POST['device_name'],'device_number'=>$_POST['device_number']));
				}
				$this->default_db->load('member_detail');
				$this->default_db->update(array('healthy_bpm'=>$nums),array('userid'=>$this->userid));
				if($state == 2) $this->Quan->health_status(6,1,$this->userid);
				if($state == 3) $this->Quan->health_status(5,1,$this->userid);
				if($state == 1) $this->Quan->health_status(0,0,$this->userid,array(5,6));//将健康标签去除 恢复正常状态
			}
		}else if($_GET['type'] == 1){
			//手动记录血压计血压
			//echo json_encode($_POST);exit();
			$state = 1;//默认为1 正常
			$bpm = $_POST['hrRate'];
			$low = $_POST['health_low'];
			$high = $_POST['health_high'];
			if($high >= 140 || $low >= 90){
				$state = 2;//偏高
				$state_str = '偏高';
			}else if($high <= 90 || $low <= 60){
				$state = 3;//偏低
				$state_str = '偏低';
			}
			$this->default_db->load('bioland_bp_a223');
			$da1 = $this->default_db->get_one(array('year'=>$year,'month'=>$month,'userid'=>$this->userid));
			$data_arr = json_decode($da1['data_json'],true);
			$data_arr[] = array(
				'add_date'=>date('Ymd',time()),
				'add_time'=>time(),
				'bp_rate'=>$_POST['hrRate'],
				'low'=>$low,
				'high'=>$high,
				'state'=>$state,
				'bpDate'=>date('Y-m-d',time()),
				'device_name'=>$_POST['device_name'],
				'device_number'=>$_POST['device_number'],
				'equipment'=>$_POST['equipment'],
				'ext'=>$_POST['ext']
			);
			$data_json = json_encode($data_arr);
			if(!empty($da1) && $da1){
				$this->default_db->update(array('data_json'=>$data_json,'update_time'=>time(),'today_high'=>$high,'today_low'=>$low,'bp_rate'=>$_POST['hrRate'],'device_name'=>$_POST['device_name'],'device_number'=>$_POST['device_number'],'equipment'=>$_POST['equipment'],'ext'=>$_POST['ext']),array('userid'=>$this->userid,'year'=>$year,'month'=>$month));
			}else{
				$this->default_db->insert(array('userid'=>$this->userid,'username'=>$this->username,'year'=>$year,'month'=>$month,'data_json'=>$data_json,'update_time'=>time(),'today_high'=>$high,'today_low'=>$low,'bp_rate'=>$_POST['hrRate'],'device_name'=>$_POST['device_name'],'device_number'=>$_POST['device_number'],'equipment'=>$_POST['equipment'],'ext'=>$_POST['ext']));
			}
			$this->default_db->load('member_detail');
			$this->default_db->update(array('healthy_mmhg'=>$high.'/'.$low),array('userid'=>$this->userid));
			$this->default_db->load('medical_mmhg');
			$da1 = $this->default_db->get_one(array('year'=>$year,'month'=>$month,'userid'=>$this->userid));
			$data_arr = json_decode($da1['data_json'],true);
			$data_arr_count = count($data_arr);
			$data_arr_count = $data_arr_count>0?$data_arr_count:0;
			if($data_arr){
				$data_arr[$data_arr_count]['add_date'] = date('Ymd',time());
				$data_arr[$data_arr_count]['add_time'] = $add_time;
				$data_arr[$data_arr_count]['bpm'] = $bpm;
				$data_arr[$data_arr_count]['low'] = $low;
				$data_arr[$data_arr_count]['high'] = $high;
				$data_arr[$data_arr_count]['state'] = $state;
				$data_arr[$data_arr_count]['bpDate'] = date('Y-m-d H:i:s',time());
				$data_arr[$data_arr_count]['device_name'] = $_POST['device_name'];
				$data_arr[$data_arr_count]['device_number'] = $_POST['device_number'];
			}else{
				$data_arr[] = array(
					'add_date'=>date('Ymd H:i:s',time()),
					'add_time'=>$add_time,
					'bpm'=>$bpm,
					'low'=>$low,
					'high'=>$high,
					'state'=>$state,
					'bpDate'=>date('Y-m-d H:i:s',time()),
					'device_name'=>$_POST['device_name'],
					'device_number'=>$_POST['device_number']
				);
			}
			$data_json = json_encode($data_arr);
			if(!empty($da1) && $da1){
				$this->default_db->update(array('data_json'=>$data_json,'update_time'=>time(),'today_high'=>$high,'today_low'=>$low,'device_name'=>$_POST['device_name'],'device_number'=>$_POST['device_number']),array('userid'=>$this->userid,'year'=>$year,'month'=>$month));
			}else{
				$this->default_db->insert(array('userid'=>$this->userid,'username'=>$this->username,'year'=>$year,'month'=>$month,'data_json'=>$data_json,'update_time'=>time(),'today_high'=>$high,'today_low'=>$low,'device_name'=>$_POST['device_name'],'device_number'=>$_POST['device_number']));
			}
			$ttt['userid'] = $this->userid;
			if($state == 2) $this->Quan->health_status(1,1,$this->userid);
			if($state == 3) $this->Quan->health_status(2,1,$this->userid);
			if($state == 1) $this->Quan->health_status(0,0,$this->userid,array(1,2));
			if($bpm > 100) $this->Quan->health_status(6,1,$this->userid);
			if($bpm < 60) $this->Quan->health_status(5,1,$this->userid);
			if($bpm >= 60 && $bpm <= 100) $this->Quan->health_status(0,0,$this->userid,array(5,6));
		}else if($_GET['type'] == 2){
			//手动记录血糖
			$state = 1;//默认为1 正常
			$low = $_POST['tnum'];
			$this->default_db->load('bioland_tp');
			$da1 = $this->default_db->get_one(array('year'=>$year,'month'=>$month,'userid'=>$this->userid));
			$data_arr = json_decode($da1['data_json'],true);
			$data_arr[] = array(
				'add_date'=>date('Ymd',time()),
				'add_time'=>time(),
				'tnum'=>$_POST['tnum'],
				'state'=>$state,
				'bpDate'=>date('Y-m-d',time()),
				'device_name'=>$_POST['device_name'],
				'device_number'=>$_POST['device_number'],
				'equipment'=>$_POST['equipment'],
				'ext'=>$_POST['ext']
			);
			$data_json = json_encode($data_arr);
			if(!empty($da1) && $da1){
				$this->default_db->update(array('data_json'=>$data_json,'update_time'=>time(),'tnum'=>$_POST['tnum'],'device_name'=>$_POST['device_name'],'device_number'=>$_POST['device_number'],'equipment'=>$_POST['equipment'],'ext'=>$_POST['ext']),array('userid'=>$this->userid,'year'=>$year,'month'=>$month));
			}else{
				$this->default_db->insert(array('userid'=>$this->userid,'username'=>$this->username,'year'=>$year,'month'=>$month,'data_json'=>$data_json,'update_time'=>time(),'tnum'=>$_POST['tnum'],'device_name'=>$_POST['device_name'],'device_number'=>$_POST['device_number'],'equipment'=>$_POST['equipment'],'ext'=>$_POST['ext']));
			}
		}
	}
	private function get_medical_bpm($is_return = 0,$userid){
		$userid = $this->check_userid($userid);
		/*查询心率数据 方法*/
		$this->default_db->load('medical_bpm');
		$new_year = $_POST['year']?intval($_POST['year']):date('Y');
		$new_month = $_POST['month']?intval($_POST['month']):date('m');
		$new_month = $new_month<10?'0'.$new_month:$new_month;
		$new_day = $_POST['day']?intval($_POST['day']):date('d');
		$ymd = $new_year.$new_month.$new_day;
		$bpm_data = $this->default_db->get_one(array('userid'=>$userid,'year'=>$new_year,'month'=>$new_month));
		$data = array();
		if(!empty($bpm_data) && $bpm_data){
			$bpm_arr = json_decode($bpm_data['data_json'],true);
			foreach(array_reverse($bpm_arr) as $k=>$v){
				if($v['add_date'] == $ymd){
					$data[$k] = $v;
					$data[$k]['time_1'] = date('m月d日',$v['add_time']);
					$data[$k]['time_2'] = date('H:i:s',$v['add_time']);
				}
			}
		}
		$datas = array('bpm'=>$data);
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	private function add_medical_spot($post = array()){
		/*新增血氧数据 方法*/
		$this->default_db->load('medical_spot');
		$new_year = date('Y');
		$new_month = date('n');
		$add_date = $_POST['add_date']?$_POST['add_date']:time();//检测数据 是否有指定的时间戳
		$add_time = $_POST['add_time']?$_POST['add_time']:time();//检测数据 是否有指定的时间戳
		$num = $post['health_num'];
		if($num == 0) $num = $_POST['num']?$_POST['num']:0;//心率数值
		$state = 1;//默认为1 正常
		if($num > 98){
			$state = 2;//偏高
			$state_str = '偏高';
		}else if($num < 90){
			$state = 3;//偏低
			$state_str = '偏低';
		}
		$bpm_data = $this->default_db->get_one(array('userid'=>$this->userid,'year'=>$new_year,'month'=>$new_month));
		$data_arr = json_decode($bpm_data['data_json'],true);
		/**/
		$cache_arr_start = getcache('spot_cache_u'.$this->userid);//第零位为start时 测量开始
		$cache_arr_end = array_reverse(getcache('spot_cache_u'.$this->userid));//数组反向排序 第零位为end时 测量结束
		if($cache_arr_end[0]['status'] == 'end'){
			$nums = 0;//测量数据
			$nums_ = 0;//数据条数
			foreach($cache_arr_start as $v){
				if($v['status'] != 'start' && $v['status'] != 'end' && $v['add_time'] <= $cache_arr_end[1]['add_time'] + 120){
					if($v['num']) $nums = $v['num'];
					$nums_ = 1;
				}
			}
			delcache('spot_cache_u'.$this->userid);
			$nums = $nums / $nums_;//平均数据
			//执行写入数据库
			$state = 1;//默认为1 正常
			if($nums > 98){
				$state = 2;//偏高
				$state_str = '偏高';
			}else if($nums < 90){
				$state = 3;//偏低
				$state_str = '偏低';
			}
			$data_arr[] = array(
				'add_date'=>date('Ymd',$add_date),
				'add_time'=>$add_time,
				'boDate'=>$post['boDate'],
				'num'=>$nums,
				'state'=>$state,
				'device_name'=>$_POST['device_name'],
				'device_number'=>$_POST['device_number']
			);
			$data_json = json_encode($data_arr);
			if($nums > 0){
				if(!empty($bpm_data) && $bpm_data){
					$this->default_db->update(array('data_json'=>$data_json,'update_time'=>time(),'today_num'=>$nums,'device_name'=>$_POST['device_name'],'device_number'=>$_POST['device_number']),array('userid'=>$this->userid,'year'=>$new_year,'month'=>$new_month));
				}else{
					$this->default_db->insert(array('userid'=>$this->userid,'username'=>$this->username,'year'=>$new_year,'month'=>$new_month,'data_json'=>$data_json,'update_time'=>time(),'today_num'=>$nums,'device_name'=>$_POST['device_name'],'device_number'=>$_POST['device_number']));
				}
			}
		}else{
			$nums = 0;//测量数据
			$nums_ = 0;//数据条数
			foreach($cache_arr_start as $v){
				if($v['status'] != 'start' && $v['status'] != 'end' && $v['add_time'] <= $cache_arr_end[1]['add_time'] + 120){
					if($v['num']) $nums = $v['num'];
					$nums_ = 1;
					//$nums_++;
				}
			}
			//delcache('bpm_cache_u'.$this->userid);
			$nums = $nums / $nums_;//平均数据
			//执行写入数据库
			$state = 1;//默认为1 正常
			if($nums > 98){
				$state = 2;//偏高
				$state_str = '偏高';
			}else if($nums < 90){
				$state = 3;//偏低
				$state_str = '偏低';
			}
			$data_arr_count = count($data_arr);
			$data_arr_count = $data_arr_count>0?$data_arr_count-1:0;
			if($add_time - $data_arr[$data_arr_count]['add_time'] >= 10){
				//$data_arr = array_reverse($data_arr);
				$data_arr[] = array(
					'add_date'=>date('Ymd',$add_date),
					'add_time'=>$add_time,
					'boDate'=>$post['boDate'],
					'num'=>$num,
					'state'=>$state,
					'device_name'=>$_POST['device_name'],
					'device_number'=>$_POST['device_number']
				);
			}else{
				$data_arr[$data_arr_count]['add_date'] = date('Ymd',$add_date);
				$data_arr[$data_arr_count]['add_time'] = $add_time;
				$data_arr[$data_arr_count]['boDate'] = $post['boDate'];
				$data_arr[$data_arr_count]['num'] = $num;
				$data_arr[$data_arr_count]['state'] = $state;
				$data_arr[$data_arr_count]['device_name'] = $post['device_name'];
				$data_arr[$data_arr_count]['device_number'] = $post['device_number'];
				//$data_arr = array_reverse($data_arr);
			}
			$data_json = json_encode($data_arr);
			if($num > 0){
				if(!empty($bpm_data) && $bpm_data){
					$this->default_db->update(array('data_json'=>$data_json,'update_time'=>time(),'today_num'=>$num,'device_name'=>$_POST['device_name'],'device_number'=>$_POST['device_number']),array('userid'=>$this->userid,'year'=>$new_year,'month'=>$new_month));
				}else{
					$this->default_db->insert(array('userid'=>$this->userid,'username'=>$this->username,'year'=>$new_year,'month'=>$new_month,'data_json'=>$data_json,'update_time'=>time(),'today_num'=>$num,'device_name'=>$_POST['device_name'],'device_number'=>$_POST['device_number']));
				}
			}
		}
		//$this->Quan->send_msg($this->userid,$this->username,$this->nickname,'健康提醒','您于'.date('Y年m月d日 H:i').'分<span>血氧检测</span>数据<span>'.$state_str.'</span>。',2,'','/medical/index.html');
	}
	private function get_medical_spot($is_return = 0,$userid){
		$userid = $this->check_userid($userid);
		/*查询血氧数据 方法*/
		$this->default_db->load('medical_spot');
		$new_year = $_POST['year']?intval($_POST['year']):date('Y');
		$new_month = $_POST['month']?intval($_POST['month']):date('m');
		$new_month = $new_month<10?'0'.$new_month:$new_month;
		$new_day = $_POST['day']?intval($_POST['day']):date('d');
		$ymd = $new_year.$new_month.$new_day;
		$spot_data = $this->default_db->get_one(array('userid'=>$this->userid,'year'=>$new_year,'month'=>$new_month));
		$data = array();
		if(!empty($spot_data) && $spot_data){
			$spot_arr = json_decode($spot_data['data_json'],true);
			foreach($spot_arr as $k=>$v){
				if($v['add_date'] == $ymd){
					$data[$k] = $v;
					$data[$k]['time_1'] = date('m月d日',$v['add_time']);
					$data[$k]['time_2'] = date('H:i:s',$v['add_time']);
				}
			}
		}
		$datas = array('spot'=>$data);
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	function add_medical_mmhg($low_ = 0,$high_ = 0,$post = ''){
		/*新增血压数据 方法*/
		$this->default_db->load('medical_mmhg');
		$new_year = date('Y');
		$new_month = date('n');
		$add_date = $_POST['add_date']?$_POST['add_date']:time();// 检测数据 是否有指定的时间戳
		$add_time = $_POST['add_time']?$_POST['add_time']:time();// 检测数据 是否有指定的时间戳
		$bpm = $post['bpm']?$post['bpm']:0;//心率数值
		if($low_ > 0 && $high_ > 0){
			$low = $low_;
			$high = $high_;
		}else{
			$low = $post['low']?$post['low']:0;//低压数值
			$high = $post['high']?$post['high']:0;//高压数值
		}
		$state = 1;//默认为1 正常
		if($high >= 140 || $low >= 90){
			$state = 2;//偏高
			$state_str = '偏高';
		}else if($high <= 90 || $low <= 60){
			$state = 3;//偏低
			$state_str = '偏低';
		}
		if($bpm < 90) $state = 3;//偏低
		$bpm_data = $this->default_db->get_one(array('userid'=>$this->userid,'year'=>$new_year,'month'=>$new_month));
		$data_arr = json_decode($bpm_data['data_json'],true);
		/**/
		$cache_arr_start = getcache('mmhg_cache_u'.$this->userid);//第零位为start时 测量开始
		$cache_arr_end = array_reverse(getcache('mmhg_cache_u'.$this->userid));//数组反向排序 第零位为end时 测量结束
		if($cache_arr_end[0]['status'] == 'end'){
			$nums = 0;//测量数据
			$nums_ = 0;//数据条数
			foreach($cache_arr_start as $v){
				if($v['status'] != 'start' && $v['status'] != 'end' && $v['add_time'] <= $cache_arr_end[1]['add_time'] + 120){
					if($v['health_low']) $low = $v['health_low'];//高压数值
					if($v['health_high']) $high = $v['health_high'];//低压数值
				}
			}
			delcache('mmhg_cache_u'.$this->userid);
			//执行写入数据库
			$state = 1;//默认为1 正常
			if($high >= 140 || $low >= 90){
				$state = 2;//偏高
				$state_str = '偏高';
			}else if($high <= 90 || $low <= 60){
				$state = 3;//偏低
				$state_str = '偏低';
			}
			$data_arr[] = array(
				'add_date'=>date('Ymd',$add_date),
				'add_time'=>$add_time,
				'bpm'=>$bpm,
				'low'=>$low,
				'high'=>$high,
				'state'=>$state,
				'bpDate'=>$post['bpDate'],
				'device_name'=>$_POST['device_name'],
				'device_number'=>$_POST['device_number'],
				'data_type'=>$post['data_type']
			);
			$data_json = json_encode($data_arr);
			if($low > 0 && $high > 0){
				if(!empty($bpm_data) && $bpm_data){
					$this->default_db->update(array('data_json'=>$data_json,'update_time'=>time(),'today_high'=>$high,'today_low'=>$low,'device_name'=>$_POST['device_name'],'device_number'=>$_POST['device_number']),array('userid'=>$this->userid,'year'=>$new_year,'month'=>$new_month));
				}else{
					$this->default_db->insert(array('userid'=>$this->userid,'username'=>$this->username,'year'=>$new_year,'month'=>$new_month,'data_json'=>$data_json,'update_time'=>time(),'today_high'=>$high,'today_low'=>$low,'device_name'=>$_POST['device_name'],'device_number'=>$_POST['device_number']));
				}
			}
		}else{
			//执行写入数据库
			$state = 1;//默认为1 正常
			if($high >= 140 || $low >= 90){
				$state = 2;//偏高
				$state_str = '偏高';
			}else if($high <= 90 || $low <= 60){
				$state = 3;//偏低
				$state_str = '偏低';
			}
			$data_arr_count = count($data_arr);
			$data_arr_count = $data_arr_count>0?$data_arr_count-1:0;
			if($add_time - $data_arr[$data_arr_count]['add_time'] >= 30){
				$data_arr[] = array(
					'add_date'=>date('Ymd',$add_date),
					'add_time'=>$add_time,
					'bpm'=>$bpm,
					'low'=>$low,
					'high'=>$high,
					'state'=>$state,
					'bpDate'=>$post['bpDate'],
					'device_name'=>$_POST['device_name'],
					'device_number'=>$_POST['device_number'],
					'data_type'=>$post['data_type']
				);
			}else{
				$data_arr[$data_arr_count]['add_date'] = date('Ymd',$add_date);
				$data_arr[$data_arr_count]['add_time'] = $add_time;
				$data_arr[$data_arr_count]['bpm'] = $bpm;
				$data_arr[$data_arr_count]['low'] = $low;
				$data_arr[$data_arr_count]['high'] = $high;
				$data_arr[$data_arr_count]['state'] = $state;
				$data_arr[$data_arr_count]['device_name'] = $_POST['device_name'];
				$data_arr[$data_arr_count]['device_number'] = $_POST['device_number'];
				$data_arr[$data_arr_count]['data_type'] = $post['data_type'];
			}
			$data_json = json_encode($data_arr);
			if($low > 0 && $high > 0){
				if(!empty($bpm_data) && $bpm_data){
					$this->default_db->load('medical_mmhg');
					$this->default_db->update(array('data_json'=>$data_json,'update_time'=>time(),'today_high'=>$high,'today_low'=>$low,'device_name'=>$_POST['device_name'],'device_number'=>$_POST['device_number']),array('userid'=>$this->userid,'year'=>$new_year,'month'=>$new_month));
				}else{
					$this->default_db->insert(array('userid'=>$this->userid,'username'=>$this->username,'year'=>$new_year,'month'=>$new_month,'data_json'=>$data_json,'update_time'=>time(),'today_high'=>$high,'today_low'=>$low,'device_name'=>$_POST['device_name'],'device_number'=>$_POST['device_number']));
				}
			}
			if(($state == 2 || $state == 3) &&  $bpm_data['low'] != $low &&  $bpm_data['high'] != $high){
				$update_time_s = strtotime(date('Y-m-d H:i:00',time()));
				$update_time_e = strtotime(date('Y-m-d H:i:59',time()));
				$temp_status = 0;
				if($state == 2) $temp_status = 1;//偏高
				if($state == 3) $temp_status = 2;//偏低
				if($temp_status > 0 && $this->userid > 0 && !empty($bpm_data) && $bpm_data && $low > 0 && $high > 0){
					$ttt['userid'] = $this->userid;
				}
			}
		}
		if($low > 0 && $high > 0) $this->add_medical_mmhg2($low,$high,$state,$post);
		/*//检查数据是否异常1.0 即将废弃
		try{
			pc_base::load_app_class('abnormal','hardware',0);
			$hardware = array('x9pro'=>array('id'=>6),'v3'=>array('id'=>5),'tjd'=>array('id'=>8));
			$hardwareid = $hardware[$post['device_name']]?$hardware[$post['device_name']]['id']:0;
			$data = array('sn'=>$post['device_mac']?:'','imsi'=>'','value'=>$high,'value2'=>$low,'value3'=>$bpm);
			$datas = array('data'=>$data,'param'=>'bp','hardwareid'=>$hardwareid,'userid'=>$this->userid);
			$abnormal = new abnormal();
			$abnormal->checkAbnormal($datas);
		}catch (Exception $e) {
			//echo 'Caught exception: ',  $e->getMessage(), "\n";
		}*/
		//设备数据上传2.0
		try{
			pc_base::load_app_class('tools','equipment',0);
			//dictionary_id:1 心率 category_id:4 手环 model_id:
			$category_id = $model_id = 0;
			switch($post['device_name']){
				case 'x9pro':
					$category_id = 4;
					$model_id = 3;
					break;
				case 'v3':
					$category_id = 4;
					$model_id = 4;
					if (!$post['device_mac']) $post['device_mac'] = 'tmp_'.date('YmdHi');
					break;
				case 'W3':
				case 'tjd':
					$category_id = 4;
					$model_id = 5;
					break;
			}
			$param = array('user_id'=>$this->userid,'dictionary_id'=>2,'category_id'=>$category_id,'model_id'=>$model_id,'sn'=>$post['device_mac'],'value'=>intval($high),'value2'=>intval($low),'value3'=>$bpm);
			$tools = new tools();
			$tools->create($param);
			//var_dump($tool->create($param));
		}catch (Exception $e) {
			//echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
	}
	private function add_medical_mmhg2($low_,$high_,$state,$arr = array()){
		$low_ = strlen($low_)<3?'0'.$low_:$low_.$high_;
		$high_ = strlen($high_)<3?'0'.$high_:$high_.$low_;
		$low_ = $this->encipher->j16_encode($low_);
		$high_ = $this->encipher->j16_encode($high_);
		$this->default_db->load('medical_mmhg_new');
		$da = $this->default_db->get_one('`userid` = "'.$this->encipher->fixed_encode($this->userid).'"','*','`id` DESC');
		$newDa = array(
			'userid'=>$this->encipher->fixed_encode($this->userid),
			'username'=>$this->encipher->encode($this->username),
			'add_date'=>date('Y-m-d H:i:s',time()),
			'low'=>$low_,
			'high'=>$high_,
			'device_name'=>$arr['device_name'],
			'device_number'=>$arr['device_number'],
			'ext'=>0,
			'state'=>$state,
			'device_date'=>date('Y-m-d H:i:s',time())
		);
		if(time() - strtotime($da['add_date']) > 40){
			$this->default_db->insert($newDa);
		}else{
			$this->default_db->update('`low` = "'.$low_.'",`high` = "'.$high_.'"','id = '.$da['id']);
		}
	}
	private function get_medical_mmhg($is_return = 0,$userid){
		$userid = $this->check_userid($userid);
		/*查询血压数据 方法*/
		$this->default_db->load('medical_mmhg');
		$new_year = $_POST['year']?intval($_POST['year']):date('Y');
		$new_month = $_POST['month']?intval($_POST['month']):date('m');
		$new_month = $new_month<10?'0'.$new_month:$new_month;
		$new_day = $_POST['day']?intval($_POST['day']):date('d');
		$ymd = $new_year.$new_month.$new_day;
		$mmhg_data = $this->default_db->get_one(array('userid'=>$this->userid,'year'=>$new_year,'month'=>$new_month));
		$data = array();
		if(!empty($mmhg_data) && $mmhg_data){
			$mmhg_arr = json_decode($mmhg_data['data_json'],true);
			foreach($mmhg_arr as $k=>$v){
				if($v['add_date'] == $ymd){
					$data[$k] = $v;
					$data[$k]['time_1'] = date('m月d日',$v['add_time']);
					$data[$k]['time_2'] = date('H:i:s',$v['add_time']);
				}
			}
		}
		$datas = array('mmhg'=>$data);
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	function add_medical_sleep(){
		$this->default_db->load('logtxt');
		$this->default_db->insert(array('log'=>'睡眠数据：'.array2string($_POST)));
		/*新增睡眠数据 方法*/
		//echo json_encode($_POST);
		$this->default_db->load('medical_sleep');
		$new_year = date('Y');
		$new_month = date('n');
		$add_date = $_POST['add_date']?$_POST['add_date']:time(); //检测数据 是否有指定的时间戳
		$add_time = $_POST['add_time']?$_POST['add_time']:time(); //检测数据 是否有指定的时间戳
		$state = 1;//默认为1 正常
		$device_name = $_POST['device_name'];
		$device_number = $_POST['device_number'];
		$end_time = $_POST['end_time'];
		$sleep_awake = $_POST['sleep_awake'];
		$sleep_day = $_POST['sleep_day'];
		$sleep_deep = $_POST['sleep_deep'];
		$sleep_length = $_POST['sleep_length'];
		$sleep_light = $_POST['sleep_light'];
		$sleep_num = $_POST['sleep_num'];
		$sleep_type = $_POST['sleep_type'];
		$start_time = $_POST['start_time'];
		if(!$sleep_num){
			$sleep_num = $sleep_deep + $sleep_light;
		}
		$bpm_data = $this->default_db->get_one(array('userid'=>$this->userid,'year'=>$new_year,'month'=>$new_month));
		$data_arr = json_decode($bpm_data['data_json'],true);
		$cache_arr_start = getcache('sleep_cache_u'.$this->userid);//第零位为start时 测量开始
		$cache_arr_end = array_reverse(getcache('sleep_cache_u'.$this->userid));//数组反向排序 第零位为end时 测量结束
		if($cache_arr_end['status'] == 'end'){
			delcache('sleep_cache_u'.$this->userid);
			$data_arr[] = array(
				'add_date'=>date('Ymd',$add_date),
				'add_time'=>$add_time,
				'device_name'=>$device_name,
				'device_number'=>$device_number,
				'end_time'=>$cache_arr_end['end_time'],
				'sleep_awake'=>$sleep_awake,
				'sleep_day'=>$sleep_day,
				'sleep_deep'=>$cache_arr_end['sleep_deep'],
				'sleep_length'=>$sleep_length,
				'sleep_light'=>$cache_arr_end['sleep_light'],
				'sleep_num'=>$cache_arr_end['sleep_num'],
				'sleep_type'=>$cache_arr_end['sleep_type'],
				'start_time'=>$cache_arr_end['start_time']
			);
			$data_json = json_encode($data_arr);
			if(!empty($bpm_data) && $bpm_data){
				$this->default_db->update(array('data_json'=>$data_json,'update_time'=>time(),'device_name'=>$_POST['device_name'],'device_number'=>$_POST['device_number'],'end_time'=>$end_time,'sleep_awake'=>$sleep_awake,'sleep_day'=>$sleep_day,'sleep_deep'=>$sleep_deep,'sleep_length'=>$sleep_length,'sleep_light'=>$sleep_light,'sleep_num'=>$sleep_num,'sleep_type'=>$sleep_type,'start_time'=>$start_time),array('userid'=>$this->userid,'year'=>$new_year,'month'=>$new_month));
			}else{
				$this->default_db->insert(array('data_json'=>$data_json,'update_time'=>time(),'userid'=>$this->userid,'year'=>$new_year,'month'=>$new_month,'username'=>$this->username,'device_name'=>$_POST['device_name'],'device_number'=>$_POST['device_number'],'end_time'=>$end_time,'sleep_awake'=>$sleep_awake,'sleep_day'=>$sleep_day,'sleep_deep'=>$sleep_deep,'sleep_length'=>$sleep_length,'sleep_light'=>$sleep_light,'sleep_num'=>$sleep_num,'sleep_type'=>$sleep_type,'start_time'=>$start_time));
			}
			$this->user_healthy('healthy_sleep',$cache_arr_end['sleep_deep'] + $cache_arr_end['sleep_light']);//睡眠
		}
	}
	public function get_medical_sleep($is_return = 0){
		/*查询睡眠~*/
		$this->default_db->load('medical_sleep');
		$this->userid = $_POST['to_uid']>0?$_POST['to_uid']:$this->userid;
		$new_year = $_POST['year']?intval($_POST['year']):date('Y');
		$new_month = $_POST['month']?intval($_POST['month']):date('m');
		//$new_month = $new_month<10?'0'.$new_month:$new_month;
		$new_day = $_POST['day']?intval($_POST['day']):date('d');
		$days_ = $_POST['date_slot']?$_POST['date_slot']:1;//查询的时间段
		$day_time = time()-$days_*24*3600;
		$ymd = $new_year.$new_month.$new_day;
		$device_name = $_POST['device_name']?$_POST['device_name']:'';//x9pro
		if($_POST['d_time'] != '')$ymd = str_replace('-','',$_POST['d_time']);
		if($days_ != 1){
			$ymd_['start'] = date('Ymd',strtotime($new_year.'-'.$new_month.'-'.$new_day));
			$ymd_['end'] = date('Ymd',$day_time);
		}
		$sleep_data = $this->default_db->get_one(array('userid'=>$this->userid,'year'=>$new_year,'month'=>$new_month));
		$data = array();
		if(!empty($sleep_data) && $sleep_data){
			$bpm_arr = json_decode($sleep_data['data_json'],true);
			foreach($bpm_arr as $k=>$v){
				if($v['add_date'] == $ymd && $days_ == 1 && ($device_name == '' || $device_name == $v['device_name'])){
					$data['m'.$k] = $v;
					$data['m'.$k]['time_1'] = date('m月d日',$v['add_time']);
					$data['m'.$k]['time_2'] = date('H:i:s',$v['add_time']);
				}else if($v['add_date'] <= $ymd_['start'] && $v['add_date'] >= $ymd_['end'] && ($device_name == '' || $device_name == $v['device_name'])){
					$data['m'.$k] = $v;
					$data['m'.$k]['time_1'] = date('m月d日',$v['add_time']);
					$data['m'.$k]['time_2'] = date('H:i:s',$v['add_time']);
				}
			}
		}
		$datas = array('sleep'=>$data,'today_num'=>$sleep_data['today_num']);
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	private function get_coupons_list($is_return = 0){
		$this->default_db->load('member');
		$user_data = $this->default_db->get_one(array('userid'=>$this->userid),'userid,phpssouid');
		$type = $_POST['type'];
		$time = time();
		$auth = array(
			'time'=>$time,
			'plat'=>1,
			'sign'=>md5('1yjxun'.$time),
		);
		$url = 'http://shop.yjxun.cn/?m=wpm&c=datas&a=getMemberCoupon&uid='.$user_data['phpssouid'].'&type='.$type;
		$data = _curl_post($url,$auth);
		//var_dump($data);die;
		echo $data;exit;
		$this->default_db->setting("shop");
		if($_POST['type'] == 'get'){
			//未使用
			$this->default_db->load_no("ku_member_detail");
			$coupons = $this->default_db->get_one(array('userid'=>$this->userid));
			$coupons_arr = string2array($coupons['coupon']);
			$this->default_db->load_no("ku_coupon");
			if($coupons_arr) $coupons_data = $this->default_db->listinfo(array('id'=>array('in'=>$coupons_arr)),'',$this->page,$this->pageSize);
		}else if($_POST['type'] == 'use' || $_POST['type'] == 'using'){
			//已使用
			$this->default_db->load_no("ku_coupon_record");
			$coupons = $this->default_db->listinfo(array('userid'=>$this->userid,'type'=>$_POST['type']),'userid,type,cid,addtime',$this->page,$this->pageSize);
			if($coupons){
				foreach($coupons as $v){
					$cid[] = $v['cid'];
				}
			}
			if($cid){
				$this->default_db->load_no("ku_coupon");
				$coupons_data = $this->default_db->listinfo(array('id'=>array('in'=>$cid)),'',$this->page,$this->pageSize);
			}
		}else{
			//已失效
			$this->default_db->load_no("ku_coupon_record");
			$coupons = $this->default_db->listinfo(array('userid'=>$this->userid,'type'=>$_POST['type']),'userid,type,cid,addtime',$this->page,$this->pageSize);
			if($coupons){
				foreach($coupons as $v){
					$coupons_[] = $v;
					$cid[$v['id']] = $v['cid'];
				}
			}
			if($cid){
				$this->default_db->load_no("ku_coupon");
				$coupons_data_ = $this->default_db->listinfo(array('id'=>array('in'=>$cid)),'',$this->page,$this->pageSize);
			}
			foreach($coupons_data_ as $k=>$v){
				if($v['addtime'] < time()){
					$coupons_data[$k] = $v;
				}
			}
			/*失效时间*/
		}
		foreach($coupons_data as $k=>$v){
			$coupons_data_[$k] = $v;
			$coupons_data_[$k]['end_time_str'] = date('Y-m-d H:i:s',$v['end_time']);
			if($v['condition'] == 1){
				$coupons_data_[$k]['condition_str'] = '商城'.$v['money'].'元优惠券，购买满'.$v['c_money'].'元即可使用';
			}else{
				$coupons_data_[$k]['condition_str'] = '无条件使用';
			}
		}
		$this->default_db->setting("default");
		$this->default_db->load('member');
		$datas = array('coupons'=>$coupons_data_);
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	private function get_medical_sport($is_return = 0){
		$month = date('m');
		$today = strtotime(date('d'));
		$this->default_db->load('sport_walk');
		$is_data = $this->default_db->get_one(array('userid'=>$this->userid,'add_month'=>$month));
		$datas = array('sport_data'=>$is_data);
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	private function get_firends($is_return = 0){
		/*获取圈子我的关注和关注我的人 查询互相关注 用于好友列表*/
		$this->default_db->load('member_detail');
		$data = $this->default_db->get_one(array('userid'=>$this->userid));
		$follow_arr = explode(',',trim($data['f_s'],','));
		$fans_arr = explode(',',trim($data['my_fs'],','));
		$firends_uid = array_intersect($follow_arr,$fans_arr);
		$where = '`userid` in ('.trim(implode(',',$firends_uid),',').')';
		$data_o = $this->default_db->listinfo($where,'',$this->page,$this->pageSize,'','','','','userid,portrait,sex');
		foreach($data_o as $k=>$v){
			$data_o_[$v['userid']] = $v;
			$v['sex_'] = $v['sex']=='男'||$v['sex']==1?1:2;
			if($v['sex_'] == 1){
				$data_o_[$v['userid']]['sex_str'] = 'male.png';
			}else{
				$data_o_[$v['userid']]['sex_str'] = 'female.png';
			}
		}
		$this->default_db->load('member');
		$data_t = $this->default_db->listinfo($where,'',$this->page,$this->pageSize,'','','','','userid,username,nickname');
		foreach($data_t as $k=>$v){
			$firends[$v['userid']]['portrait'] = $data_o_[$v['userid']]['portrait'];
			$firends[$v['userid']]['username'] = $v['username'];
			$firends[$v['userid']]['nickname'] = $v['nickname'];
			$firends[$v['userid']]['sex_str'] = $data_o_[$v['userid']]['sex_str'];
			$firends[$v['userid']]['userid'] = $v['userid'];
		}
		$datas = array('firends'=>$firends);
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	private function get_user_uid_data($is_return = 0){
		$userid = (int)$_POST['roomid'];
		$lid = (int)$_POST['lid'];
		$this->default_db->load('member');
		$user_data = $this->default_db->get_one(array('userid'=>$userid),'userid,nickname,username');
		$this->default_db->load('member_detail');
		$member_data = $this->default_db->get_one(array('userid'=>$userid),'userid,sex,portrait,coach_img,m_mood,dynamic,f_s,my_fs,age');
		$sex = $member_data['sex'];
		if($sex == '男')$sex = 1;
		if($sex == '女')$sex = 2;
		$sex_str = $sex==1?'/statics/member/images/male.png':'/statics/member/images/female.png';
		$member_data['sex_str'] = $sex_str;
		$this->default_db->load('live_manage');
		$list_index = $_POST['list_index']?1:2;
		if($_POST['list_index'] != 1){
			$live_data = $this->default_db->get_one(array('id'=>$lid));
		}else{
			$live_data = $this->default_db->listinfo(array('userid'=>$userid));
		}
		if(strpos($member_data['my_fs'],','.$this->userid.',') !== false){
			$member_data['follow'] = '<li class="fr" onClick="clear_user('.$userid.',this)">取消关注</li>';
		}else{
			$member_data['follow'] = '<li class="fr" onClick="follow_user('.$userid.',this)">+&nbsp;关注</li>';
		}
		$member_data['f_s_str'] = count(explode(',',trim($member_data['f_s'],',')));
		$member_data['my_fs_str'] = count(explode(',',trim($member_data['my_fs'],',')));
		$user_detail = array_merge($user_data,$member_data);
		$datas = array('user_detail'=>$user_detail,'live_data'=>$live_data);
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	function get_medical_bpm_history($is_return = 0){
		$this->default_db->load('medical_bpm');
		$this->userid = (int)$_POST['to_uid']>0?$_POST['to_uid']:$this->userid;
		$new_year = $_POST['year']?intval($_POST['year']):date('Y');
		$new_month = $_POST['month']?intval($_POST['month']):date('m');
		//$new_month = $new_month<10?'0'.$new_month:$new_month;
		$new_day = $_POST['day']?intval($_POST['day']):date('d');
		$days_ = $_POST['date_slot']?$_POST['date_slot']:1;//查询的时间段
		$day_time = time()-$days_*24*3600;
		if($days_ == 1) $day_time = strtotime(date('Y-m-d 23:59:59',time()));
		$ymd = $new_year.$new_month.$new_day;
		$device_name = $_POST['device_name']?$_POST['device_name']:'';//x9pro
		if($_POST['d_time'] != '' && $_POST['d_time'] != 'undefined')$ymd = str_replace('-','',$_POST['d_time']);
		if($days_ != 1){
			$ymd_['start'] = date('Ymd',strtotime($new_year.'-'.$new_month.'-'.$new_day));
			$ymd_['end'] = date('Ymd',$day_time);
		}
		$bpm_data = $this->default_db->get_one(array('userid'=>$this->userid,'year'=>$new_year,'month'=>$new_month));
		if($device_name == 'v3'){
			$datas = array('hrRate'=>$bpm_data['today_num']);
			echo json_encode($datas);exit();
		}
		$data = array();
		if(!empty($bpm_data) && $bpm_data){
			$bpm_arr = json_decode($bpm_data['data_json'],true);
			$bpm_arr_ = array_reverse($bpm_arr);
			$i = 0;
			$limit = (int)$_GET['limit']?(int)$_GET['limit']:10;
			foreach($bpm_arr_ as $k=>$v){
				if($v['add_date'] == $ymd && $days_ == 1 && ($device_name == '' || $device_name == $v['device_name'])){
					$data['m'.$k] = $v;
					$data['m'.$k]['time_1'] = date('m月d日',$v['add_time']);
					$data['m'.$k]['time_2'] = date('H:i:s',$v['add_time']);
					$state = '正常';//默认为1 正常
					if($v['num'] > 100) $state = '偏高';//2 偏高
					if($v['num'] < 60) $state = '偏低';//3 偏低
					$data['m'.$k]['state'] = $state;
					if($limit && $limit <= $i){break;}
					$i++;
				}else if($v['add_date'] <= $ymd_['start'] && $v['add_date'] >= $ymd_['end'] && ($device_name == '' || $device_name == $v['device_name'])){
					$data['m'.$k] = $v;
					$data['m'.$k]['time_1'] = date('m月d日',$v['add_time']);
					$data['m'.$k]['time_2'] = date('H:i:s',$v['add_time']);
					$state = '正常';//默认为1 正常
					if($v['num'] > 100) $state = '<font style="color:red">偏高</font>';//2 偏高
					if($v['num'] < 60) $state = '<font style="color:red">偏低</font>';//3 偏低
					$data['m'.$k]['state'] = $state;
					if($limit && $limit <= $i){break;}
					$i++;
				}
			}
		}
		$datas = array('bpm'=>$data,'today_num'=>$bpm_data['today_num']);
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	function get_medical_spot_history($is_return = 0){
		$this->default_db->load('medical_spot');
		$this->userid = $_POST['to_uid']>0?$_POST['to_uid']:$this->userid;
		$new_year = $_POST['year']?intval($_POST['year']):date('Y');
		$new_month = $_POST['month']?intval($_POST['month']):date('m');
		//$new_month = $new_month<10?'0'.$new_month:$new_month;
		$new_day = $_POST['day']?intval($_POST['day']):date('d');
		$days_ = $_POST['date_slot']?$_POST['date_slot']:1;//查询的时间段
		$day_time = time()-$days_*24*3600;
		$ymd = $new_year.$new_month.$new_day;
		$device_name = $_POST['device_name']?$_POST['device_name']:'';//x9pro
		if($_POST['d_time'] != '')$ymd = str_replace('-','',$_POST['d_time']);
		if($days_ != 1){
			$ymd_['start'] = date('Ymd',strtotime($new_year.'-'.$new_month.'-'.$new_day));
			$ymd_['end'] = date('Ymd',$day_time);
		}
		$spot_data = $this->default_db->get_one(array('userid'=>$this->userid,'year'=>$new_year,'month'=>$new_month));
		$data = array();
		if(!empty($spot_data) && $spot_data){
			$spot_arr = json_decode($spot_data['data_json'],true);
			$spot_arr_ = array_reverse($spot_arr);
			$i=0;
			$limit = (int)$_GET['limit']?(int)$_GET['limit']:10;
			foreach($spot_arr_ as $k=>$v){
				if($v['add_date'] == $ymd && $days_ == 1 && ($device_name == '' || $device_name == $v['device_name'])){
					$data['m'.$k] = $v;
					$data['m'.$k]['time_1'] = date('m月d日',$v['add_time']);
					$data['m'.$k]['time_2'] = date('H:i:s',$v['add_time']);
					$state = '正常';//默认为1 正常
					if($v['num'] > 100) $state = '<font style="color:red">偏高</font>';//偏高
					if($v['num'] < 90) $state = '<font style="color:red">偏低</font>';//偏低
					$data['m'.$k]['state'] = $state;
					if($limit && $limit <= $i){break;}
					$i++;
				}else if($v['add_date'] <= $ymd_['start'] && $v['add_date'] >= $ymd_['end'] && ($device_name == '' || $device_name == $v['device_name'])){
					$data['m'.$k] = $v;
					$data['m'.$k]['time_1'] = date('m月d日',$v['add_time']);
					$data['m'.$k]['time_2'] = date('H:i:s',$v['add_time']);
					$state = '正常';//默认为1 正常
					if($v['num'] > 100) $state = '<font style="color:red">偏高</font>';//偏高
					if($v['num'] < 90) $state = '<font style="color:red">偏低</font>';//偏低
					$data['m'.$k]['state'] = $state;
					if($limit && $limit <= $i){break;}
					$i++;
				}
			}
		}
		$datas = array('spot'=>$data,'today_num'=>$spot_data['today_num']);
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	function get_medical_mmhg_history($is_return = 0){
		$this->default_db->load('medical_mmhg');
		$this->userid = $_POST['to_uid']>0?$_POST['to_uid']:$this->userid;
		$new_year = $_POST['year']?intval($_POST['year']):date('Y');
		$new_month = $_POST['month']?intval($_POST['month']):date('m');
		//$new_month = $new_month<10?'0'.$new_month:$new_month;
		$new_day = $_POST['day']?intval($_POST['day']):date('d');
		$ymd = $new_year.$new_month.$new_day;
		$days_ = $_POST['date_slot']?(int)$_POST['date_slot']:1;//查询的时间段
		$day_time = time()-$days_*24*3600;
		if($_POST['d_time'] != '' && $_POST['d_time'] != 'undefined')$ymd = str_replace('-','',$_POST['d_time']);
		if($days_ != 1){
			$ymd_['start'] = date('Ymd',strtotime($new_year.'-'.$new_month.'-'.$new_day));
			$ymd_['end'] = date('Ymd',$day_time);
		}
		$device_name = $_POST['device_name']?$_POST['device_name']:'';//x9pro
		if($device_name == 'bp_a223'){
			$this->default_db->load('bioland_bp_a223');
		}
		$mmhg_data = $this->default_db->get_one(array('userid'=>$this->userid,'year'=>$new_year,'month'=>$new_month));
		$data = array();
		//高血压一般高于140/90mmHg，低血压一般指低于90/60mmHg
		if(!empty($mmhg_data) && $mmhg_data){
			$mmhg_arr = json_decode($mmhg_data['data_json'],true);
			$mmhg_arr_ = array_reverse($mmhg_arr);//var_dump($mmhg_arr_);
			$i=0;
			$limit = (int)$_GET['limit']?(int)$_GET['limit']:10;
			foreach($mmhg_arr_ as $k=>$v){
				if($device_name == 'biolandBlue_bp_2006_2b'){
					$ifT = $v['add_date'] == $ymd && $days_ == 1 && $device_name == $v['device_name'];
					$ifT2 = $v['add_date'] <= $ymd_['start'] && $v['add_date'] >= $ymd_['end'] && $device_name == $v['device_name'];
				}else{
					$ifT = $v['add_date'] == $ymd && $days_ == 1 && $v['device_name'] != 'biolandBlue_bp_2006_2b' && ($device_name == '' || $device_name == 'bp_a223');
					$ifT2 = $v['add_date'] <= $ymd_['start'] && $v['add_date'] >= $ymd_['end'] && $v['device_name'] != 'biolandBlue_bp_2006_2b' && ($device_name == '' || $device_name == 'bp_a223');
				}
				if($ifT){
					$data['m'.$k] = $v; 
					$data['m'.$k]['time_1'] = date('m月d日',$v['add_time']);
					$data['m'.$k]['time_2'] = date('H:i:s',$v['add_time']);
					$state = '正常';//默认为1 正常
					if($v['high'] > 140 && $v['low'] > 90) $state = '<font style="color:red">偏高</font>';//2 偏高
					if($v['high'] < 90 && $v['low']< 60) $state = '<font style="color:red">偏低</font>';//3 偏低
					$data['m'.$k]['state'] = $state;
					if($limit && $limit <= $i){break;}
					$i++;
				}else if($ifT2){
					$data['m'.$k] = $v;
					$data['m'.$k]['time_1'] = date('m月d日',$v['add_time']);
					$data['m'.$k]['time_2'] = date('H:i:s',$v['add_time']);
					$state = '正常';//默认为1 正常
					if($v['high'] > 140 && $v['low'] > 90){ $state = '偏高'; }//2 偏高
					if($v['high'] < 90 && $v['low'] < 60){ $state = '偏低' ;}//3 偏低
					$data['m'.$k]['state'] = $state;
					if($limit && $limit <= $i){break;}
					$i++;
				}
			}
		}
		$datas = array('mmhg'=>$data,'low'=>$mmhg_data['today_low'],'high'=>$mmhg_data['today_high'],'days_'=>$ymd,'addtime'=>$mmhg_data['update_time'],'bp_rate'=>$mmhg_data['bp_rate']);
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	private function get_medical_weight_history($is_return = 0){
		$this->default_db->load('medical_weight');
		$this->userid = $_POST['to_uid']>0?$_POST['to_uid']:$this->userid;
		$new_year = $_POST['year']?intval($_POST['year']):date('Y');
		$new_month = $_POST['month']?intval($_POST['month']):date('m');
		//$new_month = $new_month<10?'0'.$new_month:$new_month;
		$new_day = $_POST['day']?intval($_POST['day']):date('d');
		$ymd = $new_year.$new_month.$new_day;
		$days_ = $_POST['date_slot']?(int)$_POST['date_slot']:1;//查询的时间段
		$day_time = time()-$days_*24*3600;
		if($_POST['d_time'] != '' && $_POST['d_time'] != 'undefined')$ymd = str_replace('-','',$_POST['d_time']);
		if($days_ != 1){
			$ymd_['start'] = date('Ymd',strtotime($new_year.'-'.$new_month.'-'.$new_day));
			$ymd_['end'] = date('Ymd',$day_time);
		}
		$weight_data = $this->default_db->get_one(array('userid'=>$this->userid,'year'=>$new_year,'month'=>$new_month));
		$data = array();
		if(!empty($weight_data) && $weight_data){
			$i=0;
			$limit = (int)$_GET['limit']?(int)$_GET['limit']:10;
			$weight_arr = json_decode($weight_data['data_json'],true);
			$weight_arr_ = array_reverse($weight_arr);
			foreach($weight_arr_ as $k=>$v){
				if($v['add_date'] == $ymd && $days_ == 1){
					$data[$k] = $v;
					$data[$k]['time_1'] = date('m月d日',$v['add_time']);
					$data[$k]['time_2'] = date('H:i:s',$v['add_time']);
					$state = '正常';//默认为1 正常
					$data[$k]['state'] = $state;
					if($limit && $limit <= $i){break;}
					$i++;
				}else if($v['add_date'] <= $ymd_['start'] && $v['add_date'] >= $ymd_['end']){
					$data[$k] = $v;
					$data[$k]['time_1'] = date('m月d日',$v['add_time']);
					$data[$k]['time_2'] = date('H:i:s',$v['add_time']);
					$state = '正常';//默认为1 正常
					$data[$k]['state'] = $state;
					if($limit && $limit <= $i){break;}
					$i++;
				}
			}
		}
		$datas = array('weight'=>$data,'fat'=>$weight_data['today_fat'],'water'=>$weight_data['today_water'],'num'=>$weight_data['today_num'],'days_'=>$ymd);
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	function get_soprt_recommend($is_return = 0){
		if($_GET['userid_']) $this->userid = intval(sys_auth($_GET['userid_'], 'DECODE'));
		$this->default_db->load('sport_content');
		if($this->member_data['health_label'] != ''){
			$health_label = explode(',',trim($this->member_data['health_label'],','));
		}else{
			$health_label = '';
		}
		if($health_label != ''){
			$health_str = '';
			$for_length = count($health_label);
			for($i=0;$i<$for_length;$i++){
				$health_str .= '`health_label` like "%,'.$health_label[$i].',%" OR ';
			}
			//$health_str .= '`health_label` like "%,'.$health_label[$i].',%" OR `age_group` like "%,'.$this->member_data['age'].',%"';//用户年龄段
			$health_str = substr($health_str,0,-3);//开启年龄段时 将此处屏蔽
		}else{
			$health_str = '';
		}
		$data = $this->default_db->listinfo($health_str,'inputtime DESC',1,2);
		if(!$data){
			$data = $this->default_db->listinfo('','inputtime DESC',1,2);
		}else if(!$data[1]){
			$data[1] = $this->default_db->get_one('','*','inputtime DESC');
		}
		$datas = array('health'=>$data,'userid'=>$this->userid);
		if($is_return){
			return $datas;
		}else{
			echo $_GET['jsoncallback_']."(".json_encode($datas).")";
		}
	}
	private function search_all($is_return = 0){
		$search = $_POST['search'];
		$this->default_db->load('daily');
		$data_arr = array();
		$daily = $this->default_db->listinfo('`title` like "%'.$search.'%"','',$this->page,$this->pageSize);
		$i = 0;
		foreach($daily as $v){
			$data_arr[$i]['type'] = '食谱';
			$data_arr[$i]['title'] = $v['title'];
			$data_arr[$i]['go_url'] = '/diet/show.html?&catid='.$v['catid'].'&id='.$v['id'];
			$data_arr[$i]['thumb'] = $v['thumb'];
			$i++;
		}
		$this->default_db->load('sport_content');
		$sport = $this->default_db->listinfo('`title` like "%'.$search.'%" AND `status` = 99','',$this->page,$this->pageSize);
		foreach($sport as $v){
			$data_arr[$i]['type'] = '运动健康';
			$data_arr[$i]['title'] = $v['title'];
			$data_arr[$i]['go_url'] = '/sport/course_content.html?id='.$v['id'].'&catid='.$v['catid'];
			$data_arr[$i]['thumb'] = $v['thumb'];
			$i++;
		}
		$datas = array('data_arr'=>$data_arr);
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	private function add_kak($is_return = 0){
		if($_POST['add'] == 1){
			$this->default_db->load('member');
			$data = $this->default_db->get_one(array('username'=>$_POST['tel']),'username,userid');
			if(!$data){
				$datas = array('msg'=>'查无该用户~','state'=>1);
			}else{
				if($data['userid'] == $this->userid){
					$datas = array('msg'=>'不可添加自己~','state'=>2);
				}else{
					$datas = array('msg'=>$data['userid'],'state'=>3);
				}
			}
		}else if($_POST['add'] == 2){
			$kaktel = $_POST['tel'];
			$kakuid = $_POST['uid'];
			$kakuname = $_POST['username'];
			$kakunick = $_POST['nickname'];
			$this->default_db->load('member_detail');
			$temp = $this->default_db->get_one(array('userid'=>$kakuid),'userid,portrait');
			$this->default_db->load('kith_and_kin');
			$data_1 = $this->default_db->get_one(array('kakuid'=>$kakuid,'fuid'=>$this->userid));
			$data_2 = $this->default_db->get_one(array('kakuid'=>$this->userid,'fuid'=>$kakuid));
			if(!$data_1 && !$data_2){
				$this->default_db->insert(array('fuid'=>$this->userid,'funame'=>'','funick'=>$this->nickname,'ftel'=>$this->username,'fhp'=>$this->portrait,'kaktel'=>$kaktel,'kakuid'=>$kakuid,'kakuname'=>$kakuname,'kakunick'=>$kakunick,'kakhp'=>$temp['portrait']));
				$datas = array('msg'=>'添加成功!','state'=>1);
				$this->Quan->send_msg($kakuid,'','','亲友添加','用户<span> '.$this->nickname.' </span>邀请您添加其为亲友，立即查看。',1,'','/group/per_list.html');
				$this->default_db->load('member_detail');
				$this->default_db->update(array('raf_count'=>'+=1'),array('userid'=>$this->userid));
				$this->default_db->update(array('raf_count'=>'+=1'),array('userid'=>$kakuid));
			}else{
				$datas = array('msg'=>'不可重复添加!','state'=>2);
			}
		}else if($_POST['add'] == 3){
			$kakuid = $_POST['uid'];
			$new_username = $_POST['username'];
			$new_nickname = $_POST['nickname'];
			$this->default_db->load('kith_and_kin');
			$data_1 = $this->default_db->get_one(array('kakuid'=>$kakuid,'fuid'=>$this->userid));
			$data_2 = $this->default_db->get_one(array('kakuid'=>$this->userid,'fuid'=>$kakuid));
			if($data_1){
				$this->default_db->update(array('kakuname'=>$new_username,'kakunick'=>$new_nickname),array('kakuid'=>$kakuid,'fuid'=>$this->userid));
			}elseif($data_2){
				$this->default_db->update(array('funame'=>$new_username,'funick'=>$new_nickname),array('kakuid'=>$this->userid,'fuid'=>$kakuid));
			}
			$datas = array('msg'=>'修改完成!','state'=>1);
		}else if($_POST['add'] == 4){
			/*通过申请*/
			$this->default_db->load('kith_and_kin');
			$kakuid = $_POST['fuid'];
			$data_1 = $this->default_db->get_one(array('kakuid'=>$kakuid,'fuid'=>$this->userid));
			$data_2 = $this->default_db->get_one(array('kakuid'=>$this->userid,'fuid'=>$kakuid));
			if($data_1){
				$this->default_db->update(array('state'=>2),array('kakuid'=>$kakuid,'fuid'=>$this->userid));
			}else if($data_2){
				$this->default_db->update(array('state'=>2),array('kakuid'=>$this->userid,'fuid'=>$kakuid));
			}
			$this->default_db->load('member_detail');
			$this->default_db->update(array('raf_count'=>'+=1'),array('userid'=>$this->userid));
			$this->default_db->update(array('raf_count'=>'+=1'),array('userid'=>$kakuid));
		}else if($_POST['add'] == 5){
			/*删除申请*/
			$this->default_db->load('kith_and_kin');
			$kakuid = $_POST['fuid'];
			$data_1 = $this->default_db->get_one(array('kakuid'=>$kakuid,'fuid'=>$this->userid));
			$data_2 = $this->default_db->get_one(array('kakuid'=>$this->userid,'fuid'=>$kakuid));
			if($data_1){
				$this->default_db->delete(array('kakuid'=>$kakuid,'fuid'=>$this->userid));
			}else if($data_2){
				$this->default_db->delete(array('kakuid'=>$this->userid,'fuid'=>$kakuid));
			}
			$this->default_db->load('member_detail');
			$this->default_db->update(array('raf_count'=>'-=1'),array('userid'=>$this->userid));
			$this->default_db->update(array('raf_count'=>'-=1'),array('userid'=>$kakuid));
		}
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);exit();
		}
	}
	private function get_kak($is_return = 0){
		$this->default_db->load('kith_and_kin');
		$data_arr = array();
		$i = 0;
		$data_1 = $this->default_db->listinfo(array('kakuid'=>$this->userid),'',1,13);
		$uid_str = '';
		foreach($data_1 as $v){
			$data_arr[$v['fuid']] = $v;
			$data_arr[$v['fuid']]['new_name'] = $v['funame'];
			$data_arr[$v['fuid']]['new_nick'] = $v['funick'];
			$data_arr[$v['fuid']]['new_tel'] = $v['ftel'];
			$data_arr[$v['fuid']]['new_uid'] = $v['fuid'];
			$data_arr[$v['fuid']]['new_portrait'] = $v['fhp'].'?v='.date('Ymdh',SYS_TIME);;
			if($data_arr[$v['fuid']]['new_name'] == '') $data_arr[$v['fuid']]['new_name'] = $v['ftel'];
			if($v['state'] == 1){
				$data_arr[$v['fuid']]['state_str'] = '<div class="danxuan2 alldanxuan" onClick="danxuan('.$v['fuid'].',this)"/></div>';
				$data_arr[$v['fuid']]['display_1'] = 'block';//添加
				$data_arr[$v['fuid']]['display_2'] = 'none';//申请中
				$data_arr[$v['fuid']]['display_3'] = 'none';//数据
			}else{
				$data_arr[$v['fuid']]['state_str'] = '<div class="danxuan alldanxuan" onClick="danxuan('.$v['fuid'].',this)" style="width:21px;height:21px;"/></div>';
				$data_arr[$v['fuid']]['display_1'] = 'none';
				$data_arr[$v['fuid']]['display_2'] = 'none';
				$data_arr[$v['fuid']]['display_3'] = 'block';
			}
			if($v['fhstate'] == 1){
				$data_arr[$v['fuid']]['healthy_state_'] = 'jinggao';
			}else{
				$data_arr[$v['fuid']]['healthy_state_'] = 'anquan';
			};
			$uid_str .= ','.$v['fuid'];
			$i++;
		}
		$data_2 = $this->default_db->listinfo(array('fuid'=>$this->userid),'',1,13);
		foreach($data_2 as $v){
			$data_arr[$v['kakuid']] = $v;
			$data_arr[$v['kakuid']]['new_name'] = $v['kakuname'];
			$data_arr[$v['kakuid']]['new_nick'] = $v['kakunick'];
			$data_arr[$v['kakuid']]['new_tel'] = $v['kaktel'];
			$data_arr[$v['kakuid']]['new_uid'] = $v['kakuid'];
			$data_arr[$v['kakuid']]['new_portrait'] = $v['kakhp'].'?v='.date('Ymdh',SYS_TIME);
			if($data_arr[$v['kakuid']]['new_name'] == '') $data_arr[$v['kakuid']]['new_name'] = $v['kaktel'];
			if($v['state'] == 1){
				$data_arr[$v['kakuid']]['state_str'] = '<div class="danxuan alldanxuan" onClick="danxuan_('.$v['kakuid'].',this)"/></div>';
				$data_arr[$v['kakuid']]['display_1'] = 'none';//添加
				$data_arr[$v['kakuid']]['display_2'] = 'block';//申请中
				$data_arr[$v['kakuid']]['display_3'] = 'none';//数据
			}else{
				$data_arr[$v['kakuid']]['state_str'] = '<div class="danxuan alldanxuan" onClick="danxuan('.$v['kakuid'].',this)" style="width:21px;height:21px;"/></div>';
				$data_arr[$v['kakuid']]['display_1'] = 'none';
				$data_arr[$v['kakuid']]['display_2'] = 'none';
				$data_arr[$v['kakuid']]['display_3'] = 'block';
			}
			if($v['kakhstate'] == 1){
				$data_arr[$v['kakuid']]['healthy_state_'] = 'jinggao';
			}else{
				$data_arr[$v['kakuid']]['healthy_state_'] = 'anquan';
			}
			$uid_str .= ','.$v['kakuid'];
			$i++;
		}
		$uid_str_ = implode(',',array_unique(explode(',',trim($uid_str,','))));
		$this->default_db->load('member_detail');
		$da = $this->default_db->select('`userid` in ('.$uid_str_.')','userid,portrait');
		$new_user_da = array();
		foreach($da as $v){
			$new_user_da[$v['userid']]['portrait'] = $v['portrait'];
		}
		$data_arr_ = array();
		foreach($data_arr as $k=>$v){
			$v['new_portrait'] = $new_user_da[$v['new_uid']]['portrait'];
			$data_arr_[$k] = $v;
		}
		$datas = array('data_arr'=>$data_arr_);
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);exit();
		}
	}
	private function get_kak_user($is_return = 0){
		$this->default_db->load('kith_and_kin');
		$kakuid = $_POST['uid'];
		$data_1 = $this->default_db->get_one(array('kakuid'=>$kakuid,'fuid'=>$this->userid));
		$data_2 = $this->default_db->get_one(array('kakuid'=>$this->userid,'fuid'=>$kakuid));
		$this->default_db->load('member_detail');
		$one = $this->default_db->get_one(array('userid'=>$data_1['kakuid']),'userid,portrait');
		$two = $this->default_db->get_one(array('userid'=>$data_2['fuid']),'userid,portrait');
		if($data_1){
			$data = array('username'=>$data_1['kakuname'],'nickname'=>$data_1['kakunick'],'portrait'=>$one['portrait'],'tel'=>$data_1['kaktel']);
		}else if($data_2){
			$data = array('username'=>$data_2['funame'],'nickname'=>$data_2['funick'],'portrait'=>$two['portrait'],'tel'=>$data_2['ftel']);
		}
		$datas = array('user_data'=>$data);
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);exit();
		}
	}
	private function get_dingwei($is_return = 0){
		$this->default_db->load('member_rtp');
		$uid = $_POST['uid'];
		$data_ = $this->default_db->get_one(array('userid'=>$uid));
		if($_POST['type'] == 1){
			if(!$data_){
				$data = array('msg'=>'暂无定位数据 , 无法进行定位~','state'=>1);
			}else{
				$data = array('msg'=>'定位数据获取成功~','state'=>2);
			}
		}elseif($_POST['type'] == 2){
			$data_['add_time'] = date('Y-m-d H:i:s',$data_['addtime']);
			$data = $data_;
		}
		$datas = array('rtp'=>$data);
		foreach($datas as $k=>$v){
			$datas[$k] = str_replace('undefined','',$v);
		}
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);exit();
		}
	}
	private function get_user_detail_one($is_return = 0,$a = 0){
		$uid = intval($_POST['to_uid']);
		$this->default_db->load('kith_and_kin');
		$data_1 = $this->default_db->get_one(array('kakuid'=>$uid,'fuid'=>$this->userid,'state'=>2));
		$data_2 = $this->default_db->get_one(array('kakuid'=>$this->userid,'fuid'=>$uid,'state'=>2));
		if($data_1 || $data_2){
			$this->default_db->load('member');
			$da_1 = $this->default_db->get_one(array('userid'=>$uid),'userid,nickname,username');
			$this->default_db->load('member_detail');
			$da_2 = $this->default_db->get_one(array('userid'=>$uid),'userid,age,portrait,target_steps');
			$da = array_merge($da_1,$da_2);
		}
		if($a == 1 && !$da) return false;
		if($da){
			$this->set_healthy_state_(1,$uid);
		}
		$bpm = array('today_num'=>$data_['healthy_bpm'],'update_time_'=>date('Y-m-d',$data_['healthy_time']));
		$bpm['status_srt'] = '心率正常';
		if($bpm['today_num'] <= 50 || $bpm['today_num'] >= 110) $bpm['status_srt'] = '心率异常';
		$mmhg_ = explode('/',$data_['healthy_mmhg']);
		//$mmhg_ = explode(',',$data_['healthy_mmhg']);
		$mmhg = array('today_low'=>$mmhg_[1],'today_high'=>$mmhg_[0],'update_time_'=>date('Y-m-d',$data_['healthy_time']));
		$mmhg['status_str'] = '血压正常';
		if($mmhg['today_high'] >= 140 && $mmhg['today_low'] >= 90) $mmhg['status_str'] = '血压偏高';
		if($mmhg['today_high'] <= 90 && $mmhg['today_low'] <= 60) $mmhg['status_str'] = '血压偏低';
		$spot = array('today_num'=>$data_['healthy_spot'],'update_time_'=>date('Y-m-d',$data_['healthy_time']));
		$spot['status_str'] = '血氧正常';
		if($spot['today_num'] <= 90) $spot['status_str'] = '血氧异常';
		$this->default_db->load('sport_walk');
		$sport = $this->default_db->get_one(array('userid'=>$uid),'userid,last_time,today_walk,calorie,distance,add_year,add_month','last_time DESC');
		$sport['status_str'] = '运动达标';
		if($da['target_steps'] > $sport['today_walk']) $sport['status_str'] = '未达标';
		$sport['last_time_'] = date('Y-m-d H:i:s',$sport['last_time']);
		$datas = array('detail_data'=>$da,'bpm'=>$bpm,'mmhg'=>$mmhg,'spot'=>$spot,'sport'=>$sport,'data_1'=>$data_1['kakuname']);
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);exit();
		}
	}
	public function get_medical_updatetime(){
		$is_user = $this->get_user_detail_one(0,1);
		if($is_user) $userid = $_POST['userid']; else $userid = $this->userid;
		$this->default_db->load('medical_bpm');
		$year = date('Y');
		$month = date('m');
		$bpm = $this->default_db->get_one(array('userid'=>$userid,'year'=>$year,'month'=>$month),'userid,year,month,update_time');
		$this->default_db->load('medical_mmhg');
		$mmhg = $this->default_db->get_one(array('userid'=>$userid,'year'=>$year,'month'=>$month),'userid,year,month,update_time');
		$this->default_db->load('medical_spot');
		$spot = $this->default_db->get_one(array('userid'=>$userid,'year'=>$year,'month'=>$month),'userid,year,month,update_time');
		$this->default_db->load('sport_walk');
		$sport = $this->default_db->get_one(array('userid'=>$userid,'add_year'=>$year,'add_month'=>$month),'userid,add_year,add_month,last_time');
		$this->default_db->load('medical_sleep');
		$sleep = $this->default_db->get_one(array('userid'=>$userid,'year'=>$year,'month'=>$month),'userid,year,month,update_time');
		$this->default_db->load('medical_temperature');
		$temperature = $this->default_db->get_one(array('userid'=>$userid,'year'=>$year,'month'=>$month),'userid,year,month,add_time');
		$this->default_db->load('member_detail');
		$member = $this->default_db->get_one(array('userid'=>$userid),'userid,portrait,healthy_case');
		$this->default_db->load('bioland_bg');
		$bg = $this->default_db->get_one(array('userid'=>$userid,'year'=>$year,'month'=>$month),'userid,year,month,update_time');
		$datas = array('temperature'=>date('m-d H:i',$temperature['add_time']),'bpm'=>date('m-d H:i',$bpm['update_time']),'mmhg'=>date('m-d H:i',$mmhg['update_time']),'spot'=>date('m-d H:i',$spot['update_time']),'sport'=>date('m-d H:i',$sport['last_time']),'sleep'=>date('m-d H:i',$sleep['update_time']),'bg'=>date('m-d H:i',$bg['update_time']),'portrait'=>$member['portrait'],'case_time'=>date('Y-m-d',$member['healthy_case']));
		echo json_encode($datas);exit();
	}
	public function set_healthy_state_($is_return = 0,$uid=''){
		if(empty($uid)){
			$userid = explode(',',$_GET['userid']);
			if($userid[0] && $userid[1]){
				$uid = $userid[0];
				$this->userid = sys_auth($userid[1],'DECODE');
			}else{
				$uid = isset($_GET['userid'])? (int)$_GET['userid']:$this->userid;
			}
		} 
		$this->default_db->load('kith_and_kin');
		$da1 = $this->default_db->get_one(array('kakuid'=>$this->userid,'fuid'=>$uid,'fhstate'=>1));
		$da2 = $this->default_db->get_one(array('kakuid'=>$uid,'fuid'=>$this->userid,'kakhstate'=>1));
		if(!empty($da1)){
			$this->default_db->update(array('fhstate'=>0,'fhtime'=>time()),array('id'=>$da1['id']));
		}
		if(!empty($da2)){
			$this->default_db->update(array('kakhstate'=>0,'kakhtime'=>time()),array('id'=>$da2['id']));
		}
		if($is_return==0){ 
			if($uid && $this->userid)
					exit('{"status":"1","msg":"success"}'); else exit('{"status":"-1","msg":"fail"}');
		}
	}
	function get_medical_bpm_today($is_return = 0){
		/*查询 今天 7天 30天 指定时间的心率数据*/
	}
	function get_medical_bpm_today_tmp($is_return = 0){
		/*查询 今天 7天 30天 指定时间的心率数据*/
		$year = (int)date('Y');
		$month = (int)date('m');
		$day = (int)date('d');
		$date_time = (int)$_POST['date_time'];// 1 = 今天,7 = 7天 ...
		$date_search = (int)$_POST['date_search'];
		$this->default_db->load('medical_bpm');
		$this->userid = $_POST['to_uid']>0?$_POST['to_uid']:$this->userid;
		$data = $this->default_db->get_one(array('year'=>$year,'month'=>$month,'userid'=>$this->userid));
		$data_ = $this->default_db->get_one(array('year'=>$year,'month'=>$month-1,'userid'=>$this->userid));
		if(!$data) $data = $data_;
		$bpm_data = $data['data_json'];
		$bpm_data_ = $data_['data_json'];
		$bpm_arr = json_decode($bpm_data,true);
		$bpm_arr_ = json_decode($bpm_data_,true);
		if($bpm_arr_){
			$bpm_array = array_merge($bpm_arr,$bpm_arr_);
		}else{
			$bpm_array = $bpm_arr;
		}
		/*今日数据 的 初始化变量*/
		$get_date = $_POST['get_date']?$_POST['get_date']:'Y-m-d';
		$lately['update_time'] = date('Y-m-d H:i:s',$data['update_time']);//最近一次检测时间
		$lately['update_data'] = $data['today_num'];//最近一次检测数据
		$seven_start = strtotime(date($get_date.' 00:00:01', strtotime('-7 days')));//22:01 - 05:59 七天前
		$seven_end = time();//strtotime(date($get_date.' 05:59:00',time()));//6:00 - 10:00 七天 
		$thirty_start = strtotime(date($get_date.' 00:00:01', strtotime('-30 days')));//22:01 - 05:59 三十天前
		$thirty_end = time();//strtotime(date($get_date.' 05:59:00',time()));//6:00 - 10:00 三十天
		$date_seven_morning = strtotime(date($get_date.' 00:00:01', strtotime('-7 days')));
		$date_thirty_morning = strtotime(date($get_date.' 00:00:01', strtotime('-30 days')));
		$date_seven_noon = strtotime(date($get_date.' 06:00:01', strtotime('-7 days')));
		$date_thirty_noon = strtotime(date($get_date.' 06:00:01', strtotime('-30 days')));
		$date_seven_afternoon = strtotime(date($get_date.' 12:00:01', strtotime('-7 days')));
		$date_thirty_afternoon = strtotime(date($get_date.' 12:00:01', strtotime('-30 days')));
		$date_seven_night = strtotime(date($get_date.' 18:00:01', strtotime('-7 days')));
		$date_thirty_night = strtotime(date($get_date.' 18:00:01', strtotime('-30 days')));
		$week['morning']['num'] = 0;
		$week['morning']['count'] = 0;
		$week['noon']['num'] = 0;
		$week['noon']['count'] = 0;
		$week['afternoon']['num'] = 0;
		$week['afternoon']['count'] = 0;
		$week['night']['num'] = 0;
		$week['night']['count'] = 0;
		$weekes['morning']['num'] = 0;
		$weekes['morning']['count'] = 0;
		$weekes['noon']['num'] = 0;
		$weekes['noon']['count'] = 0;
		$weekes['afternoon']['num'] = 0;
		$weekes['afternoon']['count'] = 0;
		$weekes['night']['num'] = 0;
		$weekes['night']['count'] = 0;
		/*******************************/
		$week['state_ok']['ok'] = 0;
		$week['state_low']['low'] = 0;
		$week['state_high']['high'] = 0;
		foreach($bpm_array as $k=>$v){
			if($date_time == 1){
				if($v['add_time'] >= strtotime(date($get_date.' 00:00:00')) && $v['add_time'] <= strtotime(date($get_date.' 23:59:59'))){
					$v['hrDate'] = $v['hrDate']?$v['hrDate']:date('Y-m-d H:i:s',$v['add_time']);
					$v['ststus_str'] = '正常';
					if($v['num'] < 60) $v['ststus_str'] = '偏低';
					if($v['num'] > 100) $v['ststus_str'] = '偏高';
					$v['new_time'] = explode(' ',$v['hrDate'])[1];
					$today_arr['today']['data'][] = $v;
					$today_arr['today']['max'] = $v['num']>$today_arr['today']['max']?$v['num']:$today_arr['today']['max'];
					if(!$today_arr['today']['min']){
						$today_arr['today']['min'] = $v['num'];
					}elseif($today_arr['today']['min'] && $v['num']<$today_arr['today']['min']){
						$today_arr['today']['min'] = $v['num'];
					}
					$today_arr['today']['count_num'] += $v['num'];
					$today_arr['today']['count']++;
				}
				if($v['add_time'] >= $seven_start && $v['add_time'] <= $seven_end){
					/*七天的数据*/
					$week['data'][] = $v;
					$week['count_num'] += $v['num'];
					$week['count']++;
					if($v['state'] == 1){
						$week['state_ok']['ok']++;//正常多少次
						$week['state_ok']['percent'] = '0';//百分比
					}elseif($v['state'] == 2){
						$week['state_low']['low']++;//偏低多少次
						$week['state_low']['percent'] = '0';//百分比
					}elseif($v['state'] == 3){
						$week['state_high']['high']++;//偏高多少次
						$week['state_high']['percent'] = '0';//百分比
					}
					if(!$week['max']){
						$week['max'] = $v['num'];
					}elseif($week['max'] < $v['num']){
						$week['max'] = $v['num'];//七天最高值
					}
					if(!$week['min']){
						$week['min'] = $v['num'];
					}elseif($week['min'] > $v['num']){
						$week['min'] = $v['num'];//七天最低值
					}
				}
				if($v['add_time'] >= $thirty_start && $v['add_time'] <= $thirty_end){
					/*三十天的数据*/
					$weekes['data'][] = $v;
					$weekes['count_num'] += $v['num'];
					$weekes['count']++;
					if($v['state'] == 1){
						$weekes['state_ok']['ok']++;//正常多少次
						$weekes['state_ok']['percent'] = '0';//百分比
					}elseif($v['state'] == 2){
						$weekes['state_low']['low']++;//偏低多少次
						$weekes['state_low']['percent'] = '0';//百分比
					}elseif($v['state'] == 3){
						$weekes['state_high']['high']++;//偏高多少次
						$weekes['state_high']['percent'] = '0';//百分比
					}
					if(!$weekes['max']){
						$weekes['max'] = $v['num'];
					}elseif($weekes['max'] < $v['num']){
						$weekes['max'] = $v['num'];//七天最高值
					}
					if(!$weekes['min']){
						$weekes['min'] = $v['num'];
					}elseif($weekes['min'] > $v['num']){
						$weekes['min'] = $v['num'];//七天最低值
					}
				}
			}
		}
		$today_arr['today']['data'] = array_reverse($today_arr['today']['data']);
		$week['state_ok']['percent'] = round($week['state_ok']['ok'] / $week['count'] * 100);//四舍五入百分比
		$week['state_low']['percent'] = round($week['state_low']['low'] / $week['count'] * 100);//四舍五入百分比
		$week['state_high']['percent'] = round($week['state_high']['high'] / $week['count'] * 100);//四舍五入百分比
		$weekes['state_ok']['percent'] = round($weekes['state_ok']['ok'] / $weekes['count'] * 100);//四舍五入百分比
		$weekes['state_low']['percent'] = round($weekes['state_low']['low'] / $weekes['count'] * 100);//四舍五入百分比
		$weekes['state_high']['percent'] = round($weekes['state_high']['high'] / $weekes['count'] * 100);//四舍五入百分比
		$datas = array('today_arr'=>$today_arr,'lately'=>$lately,'week_data'=>$week,'weekes'=>$weekes);
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	private function get_medical_spot_today($is_return = 0){
		/*查询 今天 7天 30天 指定时间的心率数据*/
		$year = (int)date('Y');
		$month = (int)date('m');
		$day = (int)date('d');
		$date_time = (int)$_POST['date_time'];// 1 = 今天,7 = 7天 ...
		$date_search = (int)$_POST['date_search'];
		$this->default_db->load('medical_spot');
		$this->userid = $_POST['to_uid']>0?$_POST['to_uid']:$this->userid;
		$data = $this->default_db->get_one(array('year'=>$year,'month'=>$month,'userid'=>$this->userid));
		$data_ = $this->default_db->get_one(array('year'=>$year,'month'=>$month-1,'userid'=>$this->userid));
		if(!$data) $data = $data_;
		$spot_data = $data['data_json'];
		$spot_data_ = $data_['data_json'];
		$spot_arr = json_decode($spot_data,true);
		$spot_arr_ = json_decode($spot_data_,true);
		if($spot_arr_){
			$spot_array = array_merge($spot_arr,$spot_arr_);
		}else{
			$spot_array = $spot_arr;
		}
		/*今日数据 的 初始化变量*/
		$get_date = $_POST['get_date']?$_POST['get_date']:'Y-m-d';
		$morning_start = strtotime(date($get_date.' 00:00:01',time()));//6:00 - 10:00
		$morning_end = $morning_start + 21600;//14400;//6:00 - 10:00
		$noon_start = strtotime(date($get_date.' 06:00:01',time()));//10:01 - 16:00
		$noon_end = $noon_start + 21600;//21540;//10:01 - 16:00
		$afternoon_start = strtotime(date($get_date.' 12:00:01',time()));//16:01 - 22:00
		$afternoon_end = $afternoon_start + 21600;//21540;//16:01 - 22:00
		$night_start = strtotime(date($get_date.' 18:00:01',time()));//22:01 - 05:59
		$night_end = $night_start + 21600;//25140;//22:01 - 05:59
		$lately['update_time'] = date('Y-m-d H:i:s',$data['update_time']);//最近一次检测时间
		$lately['update_data'] = $data['today_num'];//最近一次检测数据
		$seven_start = strtotime(date($get_date.' 00:00:01', strtotime('-7 days')));//22:01 - 05:59 七天前
		$seven_end = time();//strtotime(date($get_date.' 05:59:00',time()));//6:00 - 10:00 七天 
		$thirty_start = strtotime(date($get_date.' 00:00:01', strtotime('-30 days')));//22:01 - 05:59 三十天前
		$thirty_end = time();//strtotime(date($get_date.' 05:59:00',time()));//6:00 - 10:00 三十天
		$date_seven_morning = strtotime(date($get_date.' 06:00:00', strtotime('-7 days')));
		$date_thirty_morning = strtotime(date($get_date.' 06:00:00', strtotime('-30 days')));
		$date_seven_noon = strtotime(date($get_date.' 10:01:00', strtotime('-7 days')));
		$date_thirty_noon = strtotime(date($get_date.' 10:01:00', strtotime('-30 days')));
		$date_seven_afternoon = strtotime(date($get_date.' 16:01:00', strtotime('-7 days')));
		$date_thirty_afternoon = strtotime(date($get_date.' 16:01:00', strtotime('-30 days')));
		$date_seven_night = strtotime(date($get_date.' 22:01:00', strtotime('-7 days')));
		$date_thirty_night = strtotime(date($get_date.' 22:01:00', strtotime('-30 days')));
		$week['morning']['num'] = 0;
		$week['morning']['count'] = 0;
		$week['noon']['num'] = 0;
		$week['noon']['count'] = 0;
		$week['afternoon']['num'] = 0;
		$week['afternoon']['count'] = 0;
		$week['night']['num'] = 0;
		$week['night']['count'] = 0;
		$weekes['morning']['num'] = 0;
		$weekes['morning']['count'] = 0;
		$weekes['noon']['num'] = 0;
		$weekes['noon']['count'] = 0;
		$weekes['afternoon']['num'] = 0;
		$weekes['afternoon']['count'] = 0;
		$weekes['night']['num'] = 0;
		$weekes['night']['count'] = 0;
		/*******************************/
		foreach($spot_arr as $k=>$v){
			if($date_time == 1){
				if($v['add_time'] >= $morning_start && $v['add_time'] <= $morning_end){
					$today_arr['morning']['data'][] = $v;
					$today_arr['morning']['max'] = $v['num']>$today_arr['morning']['max']?$v['num']:$today_arr['morning']['max'];
					if(!$today_arr['morning']['min']){
						$today_arr['morning']['min'] = $v['num'];
					}elseif($today_arr['morning']['min'] && $v['num']<$today_arr['morning']['min']){
						$today_arr['morning']['min'] = $v['num'];
					}
					$today_arr['morning']['count_num'] += $v['num'];
					$today_arr['morning']['count']++;
				}elseif($v['add_time'] >= $noon_start && $v['add_time'] <= $noon_end){
					$today_arr['noon']['data'][] = $v;
					$today_arr['noon']['max'] = $v['num']>$today_arr['noon']['max']?$v['num']:$today_arr['noon']['max'];
					if(!$today_arr['noon']['min']){
						$today_arr['noon']['min'] = $v['num'];
					}elseif($today_arr['noon']['min'] && $v['num']<$today_arr['noon']['min']){
						$today_arr['noon']['min'] = $v['num'];
					}
					$today_arr['noon']['count_num'] += $v['num'];
					$today_arr['noon']['count']++;
				}elseif($v['add_time'] >= $afternoon_start && $v['add_time'] <= $afternoon_end){
					$today_arr['afternoon']['data'][] = $v;
					$today_arr['afternoon']['max'] = $v['num']>$today_arr['afternoon']['max']?$v['num']:$today_arr['afternoon']['max'];
					if(!$today_arr['afternoon']['min']){
						$today_arr['afternoon']['min'] = $v['num'];
					}elseif($today_arr['afternoon']['min'] && $v['num']<$today_arr['afternoon']['min']){
						$today_arr['afternoon']['min'] = $v['num'];
					}
					$today_arr['afternoon']['count_num'] += $v['num'];
					$today_arr['afternoon']['count']++;
				}elseif($v['add_time'] >= $night_start && $v['add_time'] <= $night_end){
					$today_arr['night']['data'][] = $v;
					$today_arr['night']['max'] = $v['num']>$today_arr['night']['max']?$v['num']:$today_arr['night']['max'];
					if(!$today_arr['night']['min']){
						$today_arr['night']['min'] = $v['num'];
					}elseif($today_arr['night']['min'] && $v['num']<$today_arr['night']['min']){
						$today_arr['night']['min'] = $v['num'];
					}
					$today_arr['night']['count_num'] += $v['num'];
					$today_arr['night']['count']++;
				}
				if($v['add_time'] >= $seven_start && $v['add_time'] <= $seven_end){
					/*七天的数据*/
					$week['data'][] = $v;
					$week['count_num'] += $v['num'];
					$week['count']++;
					if($v['state'] == 1){
						$week['state_ok']['ok']++;//正常多少次
						$week['state_ok']['percent'] = '0';//百分比
					}elseif($v['state'] == 2){
						$week['state_low']['low']++;//偏低多少次
						$week['state_low']['percent'] = '0';//百分比
					}elseif($v['state'] == 3){
						$week['state_high']['high']++;//偏高多少次
						$week['state_high']['percent'] = '0';//百分比
					}
					if(!$week['max']){
						$week['max'] = $v['num'];
					}elseif($week['max'] < $v['num']){
						$week['max'] = $v['num'];//七天最高值
					}
					if(!$week['min']){
						$week['min'] = $v['num'];
					}elseif($week['min'] > $v['num']){
						$week['min'] = $v['num'];//七天最低值
					}
					for($i=7;$i>=0;$i--){
						if($v['add_time'] >= strtotime(date($get_date.' 06:00:00', strtotime('-'.$i.' days'))) && $v['add_time'] <= strtotime(date($get_date.' 06:00:00', strtotime('-'.$i.' days'))) + 14400){
							$week['morning']['num'] += $v['num'];
							$week['morning']['count'] ++;
						}
						if($v['add_time'] >= strtotime(date($get_date.' 10:01:00', strtotime('-'.$i.' days'))) && $v['add_time'] <= strtotime(date($get_date.' 10:01:00', strtotime('-'.$i.' days'))) + 21540){
							$week['noon']['num'] += $v['num'];
							$week['noon']['count'] ++;
						}
						if($v['add_time'] >= strtotime(date($get_date.' 16:01:00', strtotime('-'.$i.' days'))) && $v['add_time'] <= strtotime(date($get_date.' 16:01:00', strtotime('-'.$i.' days'))) + 21540){
							$week['afternoon']['num'] += $v['num'];
							$week['afternoon']['count'] ++;
						}
						if($v['add_time'] >= strtotime(date($get_date.' 22:01:00', strtotime('-'.$i.' days'))) && $v['add_time'] <= strtotime(date($get_date.' 22:01:00', strtotime('-'.$i.' days'))) + 21540){
							$week['night']['num'] += $v['num'];
							$week['night']['count'] ++;
						}
					}
				}
				if($v['add_time'] >= $thirty_start && $v['add_time'] <= $thirty_end){
					/*三十天的数据*/
					$weekes['data'][] = $v;
					$weekes['count_num'] += $v['num'];
					$weekes['count']++;
					if($v['state'] == 1){
						$weekes['state_ok']['ok']++;//正常多少次
						$weekes['state_ok']['percent'] = '0';//百分比
					}elseif($v['state'] == 2){
						$weekes['state_low']['low']++;//偏低多少次
						$weekes['state_low']['percent'] = '0';//百分比
					}elseif($v['state'] == 3){
						$weekes['state_high']['high']++;//偏高多少次
						$weekes['state_high']['percent'] = '0';//百分比
					}
					if(!$weekes['max']){
						$weekes['max'] = $v['num'];
					}elseif($weekes['max'] < $v['num']){
						$weekes['max'] = $v['num'];//七天最高值
					}
					if(!$weekes['min']){
						$weekes['min'] = $v['num'];
					}elseif($weekes['min'] > $v['num']){
						$weekes['min'] = $v['num'];//七天最低值
					}
					for($i=30;$i>=0;$i--){
						if($v['add_time'] >= strtotime(date($get_date.' 06:00:00', strtotime('-'.$i.' days'))) && $v['add_time'] <= strtotime(date($get_date.' 06:00:00', strtotime('-'.$i.' days'))) + 14400){
							$weekes['morning']['num'] += $v['num'];
							$weekes['morning']['count'] ++;
						}
						if($v['add_time'] >= strtotime(date($get_date.' 10:01:00', strtotime('-'.$i.' days'))) && $v['add_time'] <= strtotime(date($get_date.' 10:01:00', strtotime('-'.$i.' days'))) + 21540){
							$weekes['noon']['num'] += $v['num'];
							$weekes['noon']['count'] ++;
						}
						if($v['add_time'] >= strtotime(date($get_date.' 16:01:00', strtotime('-'.$i.' days'))) && $v['add_time'] <= strtotime(date($get_date.' 16:01:00', strtotime('-'.$i.' days'))) + 21540){
							$weekes['afternoon']['num'] += $v['num'];
							$weekes['afternoon']['count'] ++;
						}
						if($v['add_time'] >= strtotime(date($get_date.' 22:01:00', strtotime('-'.$i.' days'))) && $v['add_time'] <= strtotime(date($get_date.' 22:01:00', strtotime('-'.$i.' days'))) + 21540){
							$weekes['night']['num'] += $v['num'];
							$weekes['night']['count'] ++;
						}
					}
				}
			}
		}
		$week['state_ok']['percent'] = round($week['state_ok']['ok'] / $week['count'] * 100);//四舍五入百分比
		$week['state_low']['percent'] = round($week['state_low']['low'] / $week['count'] * 100);//四舍五入百分比
		$week['state_high']['percent'] = round($week['state_high']['high'] / $week['count'] * 100);//四舍五入百分比
		$weekes['state_ok']['percent'] = round($week['state_ok']['ok'] / $week['count'] * 100);//四舍五入百分比
		$weekes['state_low']['percent'] = round($week['state_low']['low'] / $week['count'] * 100);//四舍五入百分比
		$weekes['state_high']['percent'] = round($week['state_high']['high'] / $week['count'] * 100);//四舍五入百分比
		$datas = array('today_arr'=>$today_arr,'lately'=>$lately,'week_data'=>$week,'weekes'=>$weekes);
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	function get_medical_mmhg_today($is_return = 0){
		/*查询 今天 7天 30天 指定时间的血压数据*/
	}
	function get_medical_mmhg_today_tmp($is_return = 0){
		/*查询 今天 7天 30天 指定时间的血压数据*/
	}
	function get_medical_mmhg_today_tmp_v2($is_return = 0){
		/*查询 今天 7天 30天 指定时间的血压数据*/
		$year = (int)date('Y');
		$month = (int)date('m');
		$day = (int)date('d');
		$date_time = (int)$_POST['date_time'];// 1 = 今天,7 = 7天 ...
		$date_search = (int)$_POST['date_search'];
		$this->default_db->load('medical_mmhg');
		$this->userid = $_POST['to_uid']>0?(int)$_POST['to_uid']:$this->userid;
		$data = $this->default_db->get_one(array('year'=>$year,'month'=>$month,'userid'=>$this->userid));
		$data_ = $this->default_db->get_one(array('year'=>$year,'month'=>$month-1,'userid'=>$this->userid));
		if(!$data) $data = $data_;
		$mmhg_data = $data['data_json'];
		$mmhg_data_ = $data_['data_json'];
		$mmhg_arr = json_decode($mmhg_data,true);
		$mmhg_arr_ = json_decode($mmhg_data_,true);
		if($mmhg_arr_){
			$mmhg_array = array_merge($mmhg_arr,$mmhg_arr_);
		}else{
			$mmhg_array = $mmhg_arr;
		}
		/*今日数据 的 初始化变量*/
		$get_date = $_POST['get_date']?$_POST['get_date']:'Y-m-d';
		$lately['update_time'] = date('Y-m-d H:i:s',$data['update_time']);//最近一次检测时间
		$lately['update_data'] = $data['today_high'].'/'.$data['today_low'];//最近一次检测数据
		$seven_start = strtotime(date($get_date.' 00:00:01', strtotime('-7 days')));//22:01 - 05:59 七天前
		$seven_end = time();//strtotime(date($get_date.' 05:59:00',time()));//6:00 - 10:00 七天 
		$thirty_start = strtotime(date($get_date.' 00:00:01', strtotime('-30 days')));//22:01 - 05:59 三十天前
		$thirty_end = time();//strtotime(date($get_date.' 05:59:00',time()));//6:00 - 10:00 三十天
		$date_seven_morning = strtotime(date($get_date.' 06:00:00', strtotime('-7 days')));
		$date_thirty_morning = strtotime(date($get_date.' 06:00:00', strtotime('-30 days')));
		$date_seven_noon = strtotime(date($get_date.' 10:01:00', strtotime('-7 days')));
		$date_thirty_noon = strtotime(date($get_date.' 10:01:00', strtotime('-30 days')));
		$date_seven_afternoon = strtotime(date($get_date.' 16:01:00', strtotime('-7 days')));
		$date_thirty_afternoon = strtotime(date($get_date.' 16:01:00', strtotime('-30 days')));
		$date_seven_night = strtotime(date($get_date.' 22:01:00', strtotime('-7 days')));
		$date_thirty_night = strtotime(date($get_date.' 22:01:00', strtotime('-30 days')));
		$week['morning']['num'] = 0;
		$week['morning']['count'] = 0;
		$week['noon']['num'] = 0;
		$week['noon']['count'] = 0;
		$week['afternoon']['num'] = 0;
		$week['afternoon']['count'] = 0;
		$week['night']['num'] = 0;
		$week['night']['count'] = 0;
		$weekes['morning']['num'] = 0;
		$weekes['morning']['count'] = 0;
		$weekes['noon']['num'] = 0;
		$weekes['noon']['count'] = 0;
		$weekes['afternoon']['num'] = 0;
		$weekes['afternoon']['count'] = 0;
		$weekes['night']['num'] = 0;
		$weekes['night']['count'] = 0;
		/*******************************/
		foreach($mmhg_array as $k=>$v){
			if($date_time == 1){
				if($v['add_time'] >= strtotime(date($get_date.' 00:00:00')) && $v['add_time'] <= strtotime(date($get_date.' 23:59:59'))){
					$v['bpDate'] = $v['bpDate']?$v['bpDate']:date('Y-m-d H:i:s',$v['add_time']);
					$v['new_time'] = explode(' ',$v['bpDate'])[1];
					$v['status_str'] = '血压正常';
					if($v['high'] >= 140 || $v['low'] >= 90) $v['status_str'] = '血压偏高';
					if($v['high'] <= 90 || $v['low'] <= 60) $v['status_str'] = '血压偏低';
					$today_arr['data'][] = $v;
					$today_arr['high_max'] = $v['high']>$today_arr['high_max']?$v['high']:$today_arr['high_max'];
					if(!$today_arr['high_min']){
						$today_arr['high_min'] = $v['high'];
					}elseif($today_arr['today']['high_min'] && $v['high']<$today_arr['high_min']){
						$today_arr['high_min'] = $v['high'];
					}
					$today_arr['low_max'] = $v['low']>$today_arr['low_max']?$v['low']:$today_arr['low_max'];
					if(!$today_arr['low_min']){
						$today_arr['low_min'] = $v['low'];
					}elseif($today_arr['low_min'] && $v['low']<$today_arr['low_min']){
						$today_arr['low_min'] = $v['low'];
					}
					$today_arr['high_count_num'] += $v['high'];
					$today_arr['high_count']++;
					$today_arr['low_count_num'] += $v['low'];
					$today_arr['low_count']++;
				}
				if($v['add_time'] >= $seven_start && $v['add_time'] <= $seven_end){
					/*七天的数据*/
					$week['data'][] = $v;
					$week['count_num'] += $v['num'];
					$week['count']++;
					if($v['state'] == 1){
						$week['state_ok']['ok']++;//正常多少次
						$week['state_ok']['percent'] = '0';//百分比
					}elseif($v['state'] == 2){
						$week['state_low']['low']++;//偏低多少次
						$week['state_low']['percent'] = '0';//百分比
					}elseif($v['state'] == 3){
						$week['state_high']['high']++;//偏高多少次
						$week['state_high']['percent'] = '0';//百分比
					}
					if(!$week['max_high']) $week['max_high'] = $v['high'];
					if($week['max_high'] < $v['high']) $week['max_high'] = $v['high'];//七天最高值
					if(!$week['min_high']) $week['min_high'] = $v['high'];
					if($week['min_high'] > $v['high']) $week['min_high'] = $v['high'];//七天最低值
					if(!$week['max_low']) $week['max_low'] = $v['low'];
					if($week['max_low'] < $v['low']) $week['max_low'] = $v['low'];//七天最高值
					if(!$week['min_low']) $week['min_low'] = $v['low'];
					if($week['min_low'] > $v['low']) $week['min_low'] = $v['low'];//七天最低值
					for($i=7;$i>=0;$i--){
						if($v['add_time'] >= strtotime(date($get_date.' 00:00:00', strtotime('-'.$i.' days'))) && $v['add_time'] <= strtotime(date($get_date.' 00:00:00', strtotime('-'.$i.' days'))) + 86400){
							$week['echar'][date('m-d',strtotime(date($get_date, strtotime('-'.$i.' days'))))]['high'] += $v['high'];
							$week['echar'][date('m-d',strtotime(date($get_date, strtotime('-'.$i.' days'))))]['low'] += $v['low'];
							$week['echar'][date('m-d',strtotime(date($get_date, strtotime('-'.$i.' days'))))]['high_count'] ++;
							$week['echar'][date('m-d',strtotime(date($get_date, strtotime('-'.$i.' days'))))]['low_count'] ++;
						}
					}
				}
				if($v['add_time'] >= $thirty_start && $v['add_time'] <= $thirty_end){
					/*三十天的数据*/
					$weekes['data'][] = $v;
					$weekes['count_num'] += $v['num'];
					$weekes['count']++;
					if($v['state'] == 1){
						$weekes['state_ok']['ok']++;//正常多少次
						$weekes['state_ok']['percent'] = '0';//百分比
					}elseif($v['state'] == 2){
						$weekes['state_low']['low']++;//偏低多少次
						$weekes['state_low']['percent'] = '0';//百分比
					}elseif($v['state'] == 3){
						$weekes['state_high']['high']++;//偏高多少次
						$weekes['state_high']['percent'] = '0';//百分比
					}
					if(!$weekes['max_high']){
						$weekes['max_high'] = $v['high'];
					}elseif($weekes['max_high'] < $v['high']){
						$weekes['max_high'] = $v['high'];//七天最高值
					}
					if(!$weekes['min_high']){
						$weekes['min_high'] = $v['high'];
					}elseif($weekes['min_high'] > $v['high']){
						$weekes['min_high'] = $v['high'];//七天最低值
					}
					if(!$weekes['max_low']){
						$weekes['max_low'] = $v['low'];
					}elseif($weekes['max_low'] < $v['low']){
						$weekes['max_low'] = $v['low'];//七天最高值
					}
					if(!$weekes['min_low']){
						$weekes['min_low'] = $v['low'];
					}elseif($weekes['min_low'] > $v['low']){
						$weekes['min_low'] = $v['low'];//七天最低值
					}
					for($i=30;$i>=0;$i--){
						if($v['add_time'] >= strtotime(date($get_date.' 00:00:00', strtotime('-'.$i.' days'))) && $v['add_time'] <= strtotime(date($get_date.' 00:00:00', strtotime('-'.$i.' days'))) + 86400){
							$weekes['num'] += $v['num'];
							$weekes['count'] ++;
							if(!$weekes['max_low']){
								$weekes['max_low'] = $v['low'];
								$weekes['max_low_num'] += $v['low'];
								$weekes['max_low_count']++;
							}elseif($weekes['max_low'] < $v['low']){
								$weekes['max_low'] = $v['low'];//七天最高值
								$weekes['max_low_num'] += $v['low'];
								$weekes['max_low_count']++;
							}
							if(!$weekes['min_low']){
								$weekes['min_low'] = $v['low'];
								$weekes['min_low_num'] += $v['low'];
								$weekes['min_low_count']++;
							}elseif($weekes['min_low'] > $v['low']){
								$weekes['min_low'] = $v['low'];//七天最低值
								$weekes['min_low_num'] += $v['low'];
								$weekes['min_low_count']++;
							}
							if(!$weekes['max_high']){
								$weekes['max_high'] = $v['high'];
								$weekes['max_high_num'] += $v['high'];
								$weekes['max_high_count']++;
							}elseif($weekes['max_high'] < $v['high']){
								$weekes['max_high'] = $v['high'];//七天最高值
								$weekes['max_high_num'] += $v['high'];
								$weekes['max_high_count']++;
							}
							if(!$weekes['min_high']){
								$weekes['min_high'] = $v['high'];
								$weekes['min_high_num'] += $v['high'];
								$weekes['min_high_count']++;
							}elseif($weekes['min_high'] > $v['high']){
								$weekes['min_high'] = $v['high'];//七天最低值
								$weekes['min_high_num'] += $v['high'];
								$weekes['min_high_count']++;
							}
							$weekes['echar'][date('m-d',strtotime(date($get_date, strtotime('-'.$i.' days'))))]['high'] += $v['high'];
							$weekes['echar'][date('m-d',strtotime(date($get_date, strtotime('-'.$i.' days'))))]['low'] += $v['low'];
							$weekes['echar'][date('m-d',strtotime(date($get_date, strtotime('-'.$i.' days'))))]['high_count'] ++;
							$weekes['echar'][date('m-d',strtotime(date($get_date, strtotime('-'.$i.' days'))))]['low_count'] ++;
						}
					}
				}
			}
		}
		foreach($week['echar'] as $k=>$v){
			$week['echar'][$k]['high_'] = ceil($v['high'] / $v['high_count']);
			$week['echar'][$k]['low_'] = ceil($v['low'] / $v['low_count']);
			$week['total_high'] += $week['echar'][$k]['high_'];
			$week['total_low'] += $week['echar'][$k]['low_'];
			$week['total_high_count'] ++;
			$week['total_low_count'] ++;
		}
		foreach($weekes['echar'] as $k=>$v){
			$weekes['echar'][$k]['high_'] = ceil($v['high'] / $v['high_count']);
			$weekes['echar'][$k]['low_'] = ceil($v['low'] / $v['low_count']);
			$weekes['total_high'] += $weekes['echar'][$k]['high_'];
			$weekes['total_low'] += $weekes['echar'][$k]['low_'];
			$weekes['total_high_count'] ++;
			$weekes['total_low_count'] ++;
		}
		$week['state_ok']['percent'] = round($week['state_ok']['ok'] / $week['count'] * 100);//四舍五入百分比
		$week['state_low']['percent'] = round($week['state_low']['low'] / $week['count'] * 100);//四舍五入百分比
		$week['state_high']['percent'] = round($week['state_high']['high'] / $week['count'] * 100);//四舍五入百分比
		$weekes['state_ok']['percent'] = round($weekes['state_ok']['ok'] / $weekes['count'] * 100);//四舍五入百分比
		$weekes['state_low']['percent'] = round($weekes['state_low']['low'] / $weekes['count'] * 100);//四舍五入百分比
		$weekes['state_high']['percent'] = round($weekes['state_high']['high'] / $weekes['count'] * 100);//四舍五入百分比
		$today_arr['data'] = array_reverse($today_arr['data']);
		$datas = array('today_arr'=>$today_arr,'lately'=>$lately,'week_data'=>$week,'weekes'=>$weekes);
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	function get_medical_temperature($is_return = 0){
		//查询体温计数据
		$year = (int)date('Y');
		$month = (int)date('m');
		$day = (int)date('d');
		$date_time = (int)$_POST['date_time'];
		$this->userid = $_POST['to_uid']>0?(int)$_POST['to_uid']:$this->userid;
		$this->default_db->load('medical_temperature');
		$data = $this->default_db->get_one(array('year'=>$year,'month'=>$month,'userid'=>$this->userid));
		if($month == 1){
			$data_ = $this->default_db->get_one(array('year'=>$year-1,'month'=>12,'userid'=>$this->userid));
		}else{
			$data_ = $this->default_db->get_one(array('year'=>$year,'month'=>$month-1,'userid'=>$this->userid));
		}
		if(!$data && $data_) $data = $data_;
		$temperature_arr = json_decode($data['data_json'],true);
		$temperature_arr_ = json_decode($data_['data_json'],true);
		if($temperature_arr_){
			$temperature_array = array_merge($temperature_arr,$temperature_arr_);
		}else{
			$temperature_array = $temperature_arr;
		}
		$data_arr['total_num'] = 0;
		$data_arr['total_num_7'] = 0;
		$data_arr['total_num_30'] = 0;
		$data_arr['total_tnum'] = 0;
		$data_arr['total_tnum_7'] = 0;
		$data_arr['total_tnum_30'] = 0;
		$data_arr['tnum_l'] = 0;
		$data_arr['tnum_h'] = 0;
		$data_arr['tnum_l_7'] = 0;
		$data_arr['tnum_h_7'] = 0;
		$data_arr['tnum_l_30'] = 0;
		$data_arr['tnum_h_30'] = 0;
		foreach($temperature_array as $v){
			if($v['add_time'] >= strtotime(date('Ymd 00:00:00')) && $v['add_time'] <= strtotime(date('Ymd 23:59:59'))){
				$data_arr['total_num'] ++;
				$data_arr['total_tnum'] += (int)$v['tnum'];
				if($data_arr['tnum_l'] == 0 && (int)$v['tnum'] > 0) $data_arr['tnum_l'] = (int)$v['tnum'];
				if($data_arr['tnum_h'] == 0 && (int)$v['tnum'] > 0) $data_arr['tnum_h'] = (int)$v['tnum'];
				if((int)$v['tnum'] < $data_arr['tnum_l']) $data_arr['tnum_l'] = (int)$v['tnum'];
				if((int)$v['tnum'] > $data_arr['tnum_h']) $data_arr['tnum_h'] = (int)$v['tnum'];
				$ttt['a'][] = $v;
			}
			if($v['add_time'] >= strtotime(date('Ymd 00:00:00')) - 6 * 86400 && $v['add_time'] <= strtotime(date('Ymd 23:59:59'))){
				$data_arr['total_num_7'] ++;
				$data_arr['total_tnum_7'] += (int)$v['tnum'];
				if($data_arr['tnum_l_7'] == 0 && (int)$v['tnum'] > 0) $data_arr['tnum_l_7'] = (int)$v['tnum'];
				if($data_arr['tnum_h_7'] == 0 && (int)$v['tnum'] > 0) $data_arr['tnum_h_7'] = (int)$v['tnum'];
				if((int)$v['tnum'] < $data_arr['tnum_l_7']) $data_arr['tnum_l_7'] = (int)$v['tnum'];
				if((int)$v['tnum'] > $data_arr['tnum_h_7']) $data_arr['tnum_h_7'] = (int)$v['tnum'];
				$ttt['b'][] = $v;
			}
			if($v['add_time'] >= strtotime(date('Ymd 00:00:00')) - 28 * 86400 && $v['add_time'] <= strtotime(date('Ymd 23:59:59'))){
				$data_arr['total_num_30'] ++;
				$data_arr['total_tnum_30'] += (int)$v['tnum'];
				if($data_arr['tnum_l_30'] == 0 && (int)$v['tnum'] > 0) $data_arr['tnum_l_30'] = (int)$v['tnum'];
				if($data_arr['tnum_h_30'] == 0 && (int)$v['tnum'] > 0) $data_arr['tnum_h_30'] = (int)$v['tnum'];
				if((int)$v['tnum'] < $data_arr['tnum_l_30']) $data_arr['tnum_l_30'] = (int)$v['tnum'];
				if((int)$v['tnum'] > $data_arr['tnum_h_30']) $data_arr['tnum_h_30'] = (int)$v['tnum'];
				$ttt['c'][] = $v;
			}
			$da_ave['s'] = round($data_arr['total_tnum'] / $data_arr['total_num'],1);
			$da_ave['s_7'] = round($data_arr['total_tnum_7'] / $data_arr['total_num_7'],1);
			$da_ave['s_30'] = round($data_arr['total_tnum_30'] / $data_arr['total_num_30'],1);
		}
		$datas = array('one_da'=>$data_arr,'update_date'=>$data['add_date'],'tnum'=>$data['tnum'],'da_ave'=>$da_ave,'t_array'=>json_encode($temperature_array),'ccc'=>$ttt);
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	function get_medical_sleep_today($is_return = 0){
		/*查询7天 30天 睡眠数据*/
	}
	function get_medical_sleep_today_tmp($is_return = 0){
		/*查询7天 30天 睡眠数据*/
		$year = (int)date('Y');
		$month = (int)date('m');
		$day = (int)date('d');
		$date_time = (int)$_POST['date_time'];// 1 = 今天,7 = 7天 ...
		$date_search = (int)$_POST['date_search'];
		$this->default_db->load('medical_sleep');
		$this->userid = $_POST['to_uid']>0?$_POST['to_uid']:$this->userid;
		$data = $this->default_db->get_one(array('year'=>$year,'month'=>$month,'userid'=>$this->userid));
		$data_ = $this->default_db->get_one(array('year'=>$year,'month'=>$month-1,'userid'=>$this->userid));
		if($month == 1) $data_ = $this->default_db->get_one(array('year'=>$year-1,'month'=>12,'userid'=>$this->userid));
		if(!$data) $data = $data_;
		$sleep_data = $data['data_json'];
		$sleep_data_ = $data_['data_json'];
		$sleep_arr = json_decode($sleep_data,true);
		$sleep_arr_ = json_decode($sleep_data_,true);
		/*获取睡眠最近一次 当天的数据 包含 深睡时长 浅睡时长 入睡时间 起床时间*/
		if($sleep_data_){
			$sleep_array = array_merge($sleep_arr,$sleep_arr_);
		}else{
			$sleep_array = $sleep_arr;
		}
		$sleep_num_min_ = $sleep_array['sleep_deep'] + $sleep_array['sleep_light'];
		$sleep_num_min = $sleep_num_min_ % 60;//睡眠总时长 分钟
		$sleep_num_hour = floor($sleep_num_min_/60);//睡眠总时长 小时
		//当前日期
		$sdefaultDate = date("Y-m-d");
		$first = 0;
		$w = date('w',strtotime($sdefaultDate));
		$week_start = date('Ymd',strtotime("$sdefaultDate -".($w ? $w - $first : 6).' days'));
		$week_end = date('Ymd',strtotime("$week_start +6 days"));
		/*********************************************************/
		//var_dump($week_start."--->".$week_end);exit;
		$week_sleep_o = 0;//睡眠总时间 本周
		$week_sleep_t = 0;//睡眠总时间 上周
		$week_arr['week_sleep_l_last'] = 0;//初始化 上周 浅睡眠
		$week_arr['week_sleep_d_last'] = 0;//初始化 上周 深睡眠
		$week_arr['week_sleep_l_first'] = 0;//初始化 本周 浅睡眠
		$week_arr['week_sleep_d_first'] = 0;//初始化 本周 深睡眠
		foreach($sleep_array as $v){
			$week_arr[$v['add_date']]['week_sleep_d'] = 0;//深睡眠
			$week_arr[$v['add_date']]['week_sleep_l'] = 0;//浅睡眠
			if(($v['add_date'] == date('Ymd',strtotime("$week_start +0 days"))) || ($v['add_date'] == date('Ymd',strtotime("$week_start +1 days"))) || ($v['add_date'] == date('Ymd',strtotime("$week_start +2 days"))) || ($v['add_date'] == date('Ymd',strtotime("$week_start +3 days"))) || ($v['add_date'] == date('Ymd',strtotime("$week_start +4 days"))) || ($v['add_date'] == date('Ymd',strtotime("$week_start +5 days"))) || ($v['add_date'] == date('Ymd',strtotime("$week_start +6 days")))){
				/*本周 数据*/
				$week_sleep_o = $week_sleep_o + $v['sleep_deep'] + $v['sleep_light'];
				$week_arr[$v['add_date']]['week_sleep_l'] += $v['sleep_light'];
				$week_arr[$v['add_date']]['week_sleep_d'] += $v['sleep_deep'];
				$week_arr['week_sleep_l_first'] += $v['sleep_light'];
				$week_arr['week_sleep_d_first'] += $v['sleep_deep'];
			}
			if(($v['add_date'] == date('Ymd',strtotime("$week_start -7 days"))) || ($v['add_date'] == date('Ymd',strtotime("$week_start -6 days"))) || ($v['add_date'] == date('Ymd',strtotime("$week_start -5 days"))) || ($v['add_date'] == date('Ymd',strtotime("$week_start -4 days"))) || ($v['add_date'] == date('Ymd',strtotime("$week_start -3 days"))) || ($v['add_date'] == date('Ymd',strtotime("$week_start -2 days"))) || ($v['add_date'] == date('Ymd',strtotime("$week_start -1 days")))){
				/*上周 数据*/
				$week_sleep_t = $week_sleep_t + $v['sleep_deep'] + $v['sleep_light'];
				$week_arr['week_sleep_l_last'] += $v['sleep_light'];
				$week_arr['week_sleep_d_last'] += $v['sleep_deep'];
			}
		}
		//exit(json_encode($week_sleep_t));
		if($week_sleep_o > $week_sleep_t){
			//本周睡眠总时长 比 上周长
			$week_arr['last_week_min'] = ($week_sleep_o - $week_sleep_t) % 60;
			$week_arr['last_week_hour'] = floor(($week_sleep_o - $week_sleep_t)/60);
			$week_arr['a_images'] = 'images/sleep_up.png';
		}else{
			$week_arr['last_week_min'] = ($week_sleep_t - $week_sleep_o) % 60;
			$week_arr['last_week_hour'] = floor(($week_sleep_t - $week_sleep_o)/60);
			$week_arr['a_images'] = 'images/sleep_down.png';
		}
		$week_arr['first_week_min'] = $week_sleep_o % 60;
		$week_arr['first_week_hour'] = floor($week_sleep_o/60);
		$temp_data = $week_arr['week_sleep_d_first'];
		$week_arr['week_sleep_d_first_min'] = $temp_data % 60;//分钟
		$week_arr['week_sleep_d_first_hour'] = floor($temp_data/60);//小时
		$temp_data = $week_arr['week_sleep_l_first'];
		$week_arr['week_sleep_l_first_min'] = $temp_data % 60;//分钟
		$week_arr['week_sleep_l_first_hour'] = floor($temp_data/60);//小时
		$week_arr['week_sleep_d_first_m'] = 0;
		$week_arr['week_sleep_d_first_h'] = 0;
		$week_arr['week_sleep_l_first_m'] = 0;
		$week_arr['week_sleep_l_first_h'] = 0;
		$week_arr['week_sleep_d_last_m'] = 0;
		$week_arr['week_sleep_d_last_h'] = 0;
		$week_arr['week_sleep_l_last_m'] = 0;
		$week_arr['week_sleep_l_last_h'] = 0;
		if($week_arr['week_sleep_d_first'] > $week_arr['week_sleep_d_last']){
			//本周比上周深睡眠时间长
			$temp_data = $week_arr['week_sleep_d_first'] - $week_arr['week_sleep_d_last'];
			$week_arr['week_sleep_d_last_m'] = $temp_data % 60;
			$week_arr['week_sleep_d_last_h'] = floor($temp_data/60);
			$week_arr['b_images'] = 'images/sleep_up.png';
		}
		if($week_arr['week_sleep_l_first'] > $week_arr['week_sleep_l_last']){
			//本周比上周浅睡眠时间长
			$temp_data = $week_arr['week_sleep_l_first'] - $week_arr['week_sleep_l_last'];
			$week_arr['week_sleep_l_last_m'] = $temp_data % 60;
			$week_arr['week_sleep_l_last_h'] = floor($temp_data/60);
			$week_arr['c_images'] = 'images/sleep_up.png';
		}
		if($week_arr['week_sleep_d_first'] < $week_arr['week_sleep_d_last']){
			$temp_data = $week_arr['week_sleep_d_last'] - $week_arr['week_sleep_d_first'];
			$week_arr['week_sleep_d_last_m'] = $temp_data % 60;
			$week_arr['week_sleep_d_last_h'] = floor($temp_data/60);
			$week_arr['b_images'] = 'images/sleep_down.png';
		}
		if($week_arr['week_sleep_l_first'] < $week_arr['week_sleep_l_last']){
			$temp_data = $week_arr['week_sleep_l_last'] - $week_arr['week_sleep_l_first'];
			$week_arr['week_sleep_l_last_m'] = $temp_data % 60;
			$week_arr['week_sleep_l_last_h'] = floor($temp_data/60);
			$week_arr['c_images'] = 'images/sleep_down.png';
		}
		/*获取本月当前的天数 start*/
		$j = date('j'); //获取当前月份天数
		$start_time = strtotime(date('Y-m-01'));//获取本月第一天时间戳
		$array = array();
		for($i=0;$i<$j;$i++){
			 $array[] = date('Y-m-d',$start_time + $i * 86400);//每隔一天赋值给数组
		}
		/*获取本月当前的天数 如今天8号 则总共取8天 END*/
		//$sleep_arr //本月的json数据
		//$sleep_arr_ //上月的json数据
		$month_['sleep_time_o'] = 0;//本月睡眠的数据 总分钟 初始化
		$month_['sleep_time_o_d'] = 0;//本月深睡眠的数据 总分钟 初始化
		$month_['sleep_time_o_l'] = 0;//本月浅睡眠的数据 总分钟 初始化
		$month_['sleep_time_o_hour'] = 0;//本月睡眠的数据 转小时 初始化
		$month_['sleep_time_o_min'] = 0;//本月睡眠的数据 转分钟 初始化
		$month_['sleep_time_t'] = 0;//上月睡眠的数据 总分钟 初始化
		$month_['sleep_time_t_d'] = 0;//上月深睡眠的数据 总分钟 初始化
		$month_['sleep_time_t_l'] = 0;//上月浅睡眠的数据 总分钟 初始化
		$month_['sleep_time_t_hour'] = 0;//上月睡眠的数据 转小时 初始化
		$month_['sleep_time_t_min'] = 0;//上月睡眠的数据 转分钟 初始化
		foreach($sleep_arr as $k=>$v){
			$month_['sleep_time_o'] = $month_['sleep_time_o'] + $v['sleep_num'];//本月睡眠总时长
			$month_['sleep_time_o_d'] = $month_['sleep_time_o_d'] + $v['sleep_deep'];//本月深睡眠总时长 分钟
			$month_['sleep_time_o_l'] = $month_['sleep_time_o_l'] + $v['sleep_light'];//本月浅睡眠总时长 分钟
		}
		foreach($sleep_arr_ as $k=>$v){
			$month_['sleep_time_t'] = $month_['sleep_time_t'] + $v['sleep_num'];//上月睡眠总时长
			$month_['sleep_time_t_d'] = $month_['sleep_time_t_d'] + $v['sleep_deep'];//上月深睡眠总时长 分钟
			$month_['sleep_time_t_l'] = $month_['sleep_time_t_l'] + $v['sleep_light'];//上月浅睡眠总时长 分钟
		}
		$month_['sleep_time_o'] = $month_['sleep_time_o_d']+$month_['sleep_time_o_l'];
		$month_['sleep_time_t'] = $month_['sleep_time_t_d']+$month_['sleep_time_t_l'];
		$month_data['sleep_time_o_hour'] = floor(($month_['sleep_time_o_d']+$month_['sleep_time_o_l'])/60);//小时 本月睡眠总时长
		$month_data['sleep_time_o_min'] = ($month_['sleep_time_o_d']+$month_['sleep_time_o_l']) % 60;//分钟 本月睡眠总时长
		$month_data['sleep_time_o_d_hour'] = floor($month_['sleep_time_o_d']/60);//小时 本月深睡眠总时长
		$month_data['sleep_time_o_d_min'] = $month_['sleep_time_o_d'] % 60;//分钟 本月深睡眠总时长
		$month_data['sleep_time_o_l_hour'] = floor($month_['sleep_time_o_l']/60);//小时 本月浅睡眠总时长
		$month_data['sleep_time_o_l_min'] = $month_['sleep_time_o_l'] % 60;//分钟 本月浅睡眠总时长
		$month_data['sleep_time_t_hour'] = floor(($month_['sleep_time_t_d']+$month_['sleep_time_t_l'])/60);//小时 上月睡眠总时长
		$month_data['sleep_time_t_min'] = ($month_['sleep_time_t_d']+$month_['sleep_time_t_l']) % 60;//分钟 上月睡眠总时长
		$month_data['sleep_time_t_d_hour'] = floor($month_['sleep_time_t_d']/60);//小时 上月深睡眠总时长
		$month_data['sleep_time_t_d_min'] = $month_['sleep_time_t_d'] % 60;//分钟 上月深睡眠总时长
		$month_data['sleep_time_t_l_hour'] = floor($month_['sleep_time_t_l']/60);//小时 上月浅睡眠总时长
		$month_data['sleep_time_t_l_min'] = $month_['sleep_time_t_l'] % 60;//分钟 上月浅睡眠总时长
		if($month_['sleep_time_o'] > $month_['sleep_time_t']){
			$month_data['sleep_time_t_hour'] = floor(($month_['sleep_time_o'] - $month_['sleep_time_t'])/60);
			$month_data['sleep_time_t_min'] = ($month_['sleep_time_o'] - $month_['sleep_time_t']) % 60;
			$month_data['zsrc'] = 'images/sleep_up.png';
		}else{
			$month_data['sleep_time_t_hour'] = floor(($month_['sleep_time_t'] - $month_['sleep_time_o'])/60);
			$month_data['sleep_time_t_min'] = ($month_['sleep_time_t'] - $month_['sleep_time_o']) % 60;
			$month_data['zsrc'] = 'images/sleep_down.png';
		}
		if($month_['sleep_time_o_d'] > $month_['sleep_time_t_d']){
			$month_data['sleep_time_t_d_hour'] = floor(($month_['sleep_time_o_d'] - $month_['sleep_time_t_d'])/60);
			$month_data['sleep_time_t_d_min'] = ($month_['sleep_time_o_d'] - $month_['sleep_time_t_d']) % 60;
			$month_data['ssrc'] = 'images/sleep_up.png';
		}else{
			$month_data['sleep_time_t_d_hour'] = floor(($month_['sleep_time_t_d'] - $month_['sleep_time_o_d'])/60);
			$month_data['sleep_time_t_d_min'] = ($month_['sleep_time_t_d'] - $month_['sleep_time_o_d']) % 60;
			$month_data['ssrc'] = 'images/sleep_down.png';
		}
		if($month_['sleep_time_o_d'] > $month_['sleep_time_t_d']){
			$month_data['sleep_time_t_l_hour'] = floor(($month_['sleep_time_o_d'] - $month_['sleep_time_t_d'])/60);
			$month_data['sleep_time_t_l_min'] = ($month_['sleep_time_o_d'] - $month_['sleep_time_t_d']) % 60;
			$month_data['qsrc'] = 'images/sleep_up.png';
		}else{
			$month_data['sleep_time_t_l_hour'] = floor(($month_['sleep_time_t_d'] - $month_['sleep_time_o_d'])/60);
			$month_data['sleep_time_t_l_min'] = ($month_['sleep_time_t_d'] - $month_['sleep_time_o_d']) % 60;
			$month_data['qsrc'] = 'images/sleep_down.png';
		}
		/*今日数据 的 初始化变量*/
		$get_date = $_POST['get_date']?$_POST['get_date']:'Y-m-d';
		//var_dump($sleep_array);
		$data['start_time_'] = explode(" ",$data['start_time']);
		$data['end_sleep_'] = explode(" ",$data['end_time']);
		$sleep_num_min_ = $data['sleep_deep'] + $data['sleep_light'];
		$sleep_num_min = $sleep_num_min_ % 60;//睡眠总时长 分钟
		//$sleep_num_min = $sleep_num_min < 10?"0".$sleep_num_min:$sleep_num_min;
		$sleep_num_hour = floor($sleep_num_min_/60);//睡眠总时长 小时
		//$sleep_num_hour = $sleep_num_hour < 10?"0".$sleep_num_hour:$sleep_num_hour;
		$datas = array('update_time'=>date('Ymd',$data['update_time']),'sleep'=>$sleep_array,'sleep_num_min'=>$sleep_num_min,'sleep_num_hour'=>$sleep_num_hour,'start_sleep'=>substr($data['start_time_'][1], 0, 5),'end_sleep'=>substr($data['end_sleep_'][1], 0, 5),'sleep_deep'=>$data['sleep_deep'],'sleep_light'=>$data['sleep_light'],'week_arr'=>$week_arr,'month_data'=>$month_data,'month_'=>$month_,'sleep_arr'=>$sleep_arr);
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	public function check_today_data_w_h(){
		$arr_data['bmi'] = round($_POST['u_weight'] / pow(($_POST['u_height'] / 100),2),2);
		$arr_data['u_height'] = $_POST['u_height'];
		$arr_data['u_weight'] = $_POST['u_weight'];
		$this->default_db->load('member_detail');
		$this->default_db->update($arr_data,array('userid'=>$this->userid));	
		if($arr_data['bmi'] >= 40){
			$data['bmi_str'] = '超重 极重度肥胖';
		}else if($arr_data['bmi'] >= 35 && $arr_data['bmi'] <= 39.9){
			$data['bmi_str'] = '超重 重度肥胖';
		}else if($arr_data['bmi'] >= 30 && $arr_data['bmi'] <= 34.9){
			$data['bmi_str'] = '超重 肥胖';
		}else if($arr_data['bmi'] >= 25 && $arr_data['bmi'] <= 29.9){
			$data['bmi_str'] = '超重 偏胖';
		}else if($arr_data['bmi'] >= 25){
			$data['bmi_str'] = '超重';
		}else if($arr_data['bmi'] >= 18.5 && $arr_data['bmi'] <= 24.9){
			$data['bmi_str'] = '正常';
		}else if($arr_data['bmi'] < 18.5){
			$data['bmi_str'] = '偏瘦';
		}
		echo json_encode(array('bmi'=>$arr_data['bmi'],'bmi_str'=>$data['bmi_str']));
	}
	public function get_set_user_data($is_return = 0){
		if($_POST['edit'] == 1){
			//修改
			$safe_param = array('nickname','portrait','age','sex','u_weight','u_height','birthday');
			foreach($_POST as $k=>$v){
				if(!in_array($k,$safe_param)){
					unset($_POST[$k]);
				}
			}
			$_POST['bmi'] = round($_POST['u_weight'] / pow(($_POST['u_height'] / 100),2),2);
			//var_dump($_POST);exit;
			$this->default_db->load('member');
			$data = $this->default_db->update(array('nickname'=>$_POST['nickname']),array('userid'=>$this->userid));
			if($data){
				param::set_cookie('_nickname',$_POST['nickname']);
				unset($_POST['nickname']);
				//头像 =========================
				$_POST['portrait'] = urldecode($_POST['portrait']);
				$portrait = $_POST['portrait'];
				if(strpos($portrait,'/upload/member/avatar_')===false && $_GET['editNew'] != 1){
					$portrait = drcms_PATH.'/'.str_replace(array('http://',siteurl(1)),'',$portrait);
					$lastname = substr($portrait, strrpos($portrait, '.') + 1);
					$part_name = drcms_PATH.'/upload/member/avatar_'.$this->userid;
					$file_name = '/upload/member/avatar_'.$this->userid.'.'.$lastname;
					@unlink($part_name.'.jpg');
					@unlink($part_name.'.jpeg');
					@unlink($part_name.'.JPG');
					@unlink($part_name.'.JPEG');
					@unlink($part_name.'.png');
					@unlink($part_name.'.PNG');
					@unlink($part_name.'.gif');
					@unlink($part_name.'.GIF');
					$part_name.= '.'.$lastname; 
					$copy = copy($portrait, $part_name);
					$_POST['portrait'] = $file_name;
				}else{
					//unset($_POST['portrait']);		
				}
				//=============================
				$this->default_db->load('member_detail');
				$this->default_db->update($_POST,array('userid'=>$this->userid));
				$this->default_db->load('kith_and_kin');
				$this->default_db->update(array('kakhp'=>$_POST['portrait']),array('kakuid'=>$this->userid));
				$this->default_db->update(array('fhp'=>$_POST['portrait']),array('fuid'=>$this->userid));
				if($_POST['portrait'] != 'http://www.yjxun.cn//statics/images/member/nophoto.gif') $this->Quan->set_task(2,50,$this->userid);
				if($_POST['u_weight'] > 0 && $_POST['u_height'] > 0) $this->Quan->set_task(1,50,$this->userid);
				$this->Quan->set_images_status($file_name);
				
				//更新用户信息表
				try {
					pc_base::load_app_class('tool','member',0);
					$tool = new tool();
					$tool->doInformation(array('user_id'=>$this->userid,'type'=>'base','sex'=>$_POST['sex'],'age'=>$_POST['age'],'birth_date'=>$_POST['birthday'],'head'=>$_POST['portrait']));
				} catch (Exception $e) {
					//echo $e->getMessage();
				}
				
				if($this->ajax){
					exit( json_encode( array('file_name'=>$file_name) ));
				}
			}
			//header('location:/member/setting/diet_userdata.html');
		}else{
			//读取
			if($this->userid){
				$this->default_db->load('member_detail');
				$data = $this->default_db->get_one(array('userid'=>$this->userid),'userid,portrait,age,u_weight,u_height,sex,birthday');
			}
			if($data['sex'] == 1 || $data['sex'] == '男') $data['sex'] = '男';
			if($data['sex'] == 2 || $data['sex'] == '女') $data['sex'] = '女';
			$data['nickname'] = $this->nickname;
			$datas = array('user_data'=>$data);
		}
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	public function target_steps_get($is_return = 0){
		$this->default_db->load('member_detail');
		if($_POST['edit_data'] == 1){
			$_POST['target_steps_if'] = $_POST['target_steps_if'] == 'on'?1:2;
			$data_arr = array(
				'target_steps_if'=>$_POST['target_steps_if'],
				'target_steps'=>$_POST['target_steps']
			);
			$this->default_db->update($data_arr,array('userid'=>$this->userid));
			//header('location:/member/setting/target_steps.html');
		}else{
			$data = $this->default_db->get_one(array('userid'=>$this->userid),'userid,target_steps,target_steps_if');
		}
		$datas = array('target_steps'=>$data);
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	public function perfect_data(){
		$for_system = $this->for_system();
		$this->default_db->load('member');
		$nickname = $_POST['nickname'];
		$this->default_db->update(array('nickname'=>$nickname),array('userid'=>$this->userid));
		$_POST['birthday'] = $_POST['birthday']?$_POST['birthday']:date('Y-m-d');
		$birthday = explode('-',$_POST['birthday']);
		$age = date('Y') - $birthday[0];
		$birthday = $_POST['birthday'];
		$u_weight = $_POST['weight'];
		$u_height = $_POST['height'];
		$bmi = $_POST['bmi'];
		$sex = $_POST['sex'];
		$portrait = $_POST['portrait'];
		if(strpos($portrait,'/upload/member/avatar_')===false){
			$portrait = drcms_PATH.'/'.str_replace(array('http://',siteurl(1)),'',$portrait);
			$lastname = substr($portrait, strrpos($portrait, '.') + 1);
			$part_name = drcms_PATH.'/upload/member/avatar_'.$this->userid;
			$file_name = '/upload/member/avatar_'.$this->userid.'.'.$lastname;
			@unlink($part_name.'.jpg');
			@unlink($part_name.'.jpeg');
			@unlink($part_name.'.JPG');
			@unlink($part_name.'.JPEG');
			@unlink($part_name.'.png');
			@unlink($part_name.'.PNG');
			@unlink($part_name.'.gif');
			@unlink($part_name.'.GIF');
			$part_name.= '.'.$lastname; 
			$copy = copy($portrait, $part_name);
			$portrait = $_POST['portrait'] = $file_name;
			//同步商城用户数据
			try {
				pc_base::load_app_class('userInterface','member',0);
				$userInterface = new userInterface();
				$userinfo = $this->default_db->get_one(array('userid'=>$this->userid),'phpssouid');
				$head = '//www.yjxun.cn'.$file_name;
				$userInterface->synMemberData(array('ssouid'=>$userinfo['phpssouid'],'head'=>$head));
			} catch (Exception $e) {
			}
		}
		$data_arr = array(
			'age'=>$age,
			'birthday'=>$birthday,
			'u_weight'=>$u_weight,
			'u_height'=>$u_height,
			'bmi'=>$bmi,
			'sex'=>$sex,
			'portrait'=>$portrait
		);
		$where = '`userid`='.$this->userid;
		$this->default_db->load('member_detail');
		$is_exist = $this->default_db->get_one($where,'userid');
		//var_dump($is_exist);die;
		if ($is_exist) {
			$this->default_db->update($data_arr,$where);
		} else {
			$data_arr['userid'] = $this->userid;
			$this->default_db->insert($data_arr);
		}
		//$this->default_db->update($data_arr,array('userid'=>$this->userid));
		$this->default_db->load("total_num");
		$temp = $this->default_db->get_one(array('addtime'=>strtotime(date('Ymd 00:00:00'))));
		$update_arr = array('total_num'=>'+=1','region'=>'2227','cityid'=>'326','provinceid'=>'21');
		$insert_arr = array('total_num'=>'+=1','region'=>'2227','cityid'=>'326','provinceid'=>'21','addtime'=>strtotime(date('Ymd 00:00:00')));
		if($for_system == 1 && $sex == 1){
			$update_arr['android_male'] = '+=1';
			$insert_arr['android_male'] = '+=1';
		}else if($for_system == 1 && $sex == 2){
			$update_arr['android_female'] = '+=1';
			$insert_arr['android_female'] = '+=1';
		}else if($for_system == 2 && $sex == 1){
			$update_arr['ios_male'] = '+=1';
			$insert_arr['ios_male'] = '+=1';
		}else if($for_system == 2 && $sex == 2){
			$update_arr['ios_female'] = '+=1';
			$insert_arr['ios_female'] = '+=1';
		}
		if($temp){
			$this->default_db->update($update_arr,array('addtime'=>strtotime(date('Ymd 00:00:00'))));
		}else{
			$this->default_db->insert($insert_arr);
		}
		if($_POST['portrait'] != 'http://www.yjxun.cn//statics/images/member/nophoto.gif' && $_POST['portrait'] != '') $this->Quan->set_task(2,50,$this->userid);
		param::set_cookie('_nickname',$nickname);
		param::set_cookie('__nickname',$nickname,0,1);
		//更新用户信息表
		try {
			pc_base::load_app_class('tool','member',0);
			$tool = new tool();
			$tool->doInformation(array('user_id'=>$this->userid,'type'=>'base','sex'=>$sex,'age'=>$age,'birth_date'=>$birthday,'head'=>$portrait));
		} catch (Exception $e) {
			//echo $e->getMessage();
		}
	}
	function for_system(){
		if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')||strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')){
			return 2;
		}else if(strpos($_SERVER['HTTP_USER_AGENT'], 'Android')){
			return 1;
		}else{
			return 3;
		}
	}
	public function get_portrait(){
		$portrait = $this->portrait;
		$datas = array('portrait'=>$portrait);
		echo json_encode($datas);
	}
	public function get_news_data_today(){
		$this->default_db->load('daily');
		$tmp_1 = $this->default_db->select(array('catid'=>74),'*',2,'listorder desc,inputtime desc');
		//$tmp_2 = $this->default_db->get_one(array('catid'=>74,'paixu'=>2));
		$data = array('a'=>array($tmp_1[0]),'b'=>array($tmp_1[1]));
		if($_GET['types'] == 1){
			$data = $this->default_db->select(array('catid'=>81),'*',3,'listorder desc,inputtime desc');
		}
		echo $_GET['jsoncallback_______']."(".json_encode($data).")";exit();
	}
    public function getNewsData(){
        $this->default_db->load('daily');
        $data = $this->default_db->listinfo(array('catid'=>74),'listorder desc,inputtime desc',$this->page,$this->pageSize);
        exit(json_encode(array('data'=>$data)));
    }
	public function activity(){
		$ulat = $_POST['u_lat'];
		$ulng = $_POST['u_lng'];
		$this->default_db->load('activities');
		$id = intval($_GET['id']);
		if($id){
			$activities = $this->default_db->get_one(array('id'=>$id)); 
			if($activities['islink']==1){
				header('location:'.$activities['url']);
				exit();
			}
			$distace = $this->getdistance($ulng,$ulat,$activities['lng'],$activities['lat']);//获取距离
			$distace = sprintf('%.2f',$distace / 1000);//单位:米 转换 成 千米/公里 四舍五入 保留两个小数点
			$activities['distace'] = $distace;
			$this->default_db->load('activities_data');
			$activities_ = $this->default_db->get_one(array('id'=>$id));
			$activities['content'] = $activities_['content'];
			$activities['content_'] = $activities_['content_'];
			$datas = array('activities'=>$activities,'enroll_data'=>$enroll_data);
		}
		include template('member','activity');
	}
	private function get_user_today_data($is_return = 0){
		$to_uid = $_POST['to_uid']?(int)$_POST['to_uid']:$this->userid;
		$year = date('Y');
		$month = date('m');
		$this->default_db->load('member');
		$data_o = $this->default_db->get_one(array('userid'=>$to_uid),'nickname,username,userid');
		$this->default_db->load('member_detail');
		$data = $this->default_db->get_one(array('userid'=>$to_uid),'u_height,u_weight,userid,portrait');
		$data = array_merge($data,$data_o);
		$data['bmi'] = round($data['u_weight'] / (pow($data['u_height']/100,2)),2);
		if($data['bmi'] >= 40){
			$data['bmi_str'] = '超重 极重度肥胖';
		}else if($data['bmi'] >= 35 && $data['bmi'] <= 39.9){
			$data['bmi_str'] = '超重 重度肥胖';
		}else if($data['bmi'] >= 30 && $data['bmi'] <= 34.9){
			$data['bmi_str'] = '超重 肥胖';
		}else if($data['bmi'] >= 25 && $data['bmi'] <= 29.9){
			$data['bmi_str'] = '超重 偏胖';
		}else if($data['bmi'] >= 25){
			$data['bmi_str'] = '超重';
		}else if($data['bmi'] >= 18.5 && $data['bmi'] <= 24.9){
			$data['bmi_str'] = '正常';
		}else if($data['bmi'] < 18.5){
			$data['bmi_str'] = '偏瘦';
		}
		$this->default_db->load('sport_walk');
		$data_sport = $this->default_db->get_one(array('userid'=>$to_uid,'add_year'=>$year,'add_month'=>$month),'add_year,add_month,last_time,today_walk,calorie,distance');
		if(!$data_sport){
			$data_sport['last_time_'] = '暂无数据';
			$data_sport['today_walk'] = '--';
			$data_sport['calorie'] = '--';
			$data_sport['distance_'] = '--';
		}else{
			$data_sport['last_time_'] = date('Y-m-d H:i',$data_sport['last_time']);
			$data_sport['distance_'] = round($data_sport['distance'] / 1000,2);
			$data_sport['calorie_'] = round($data_sport['calorie'] / 1000,2);
		}
		$this->default_db->load('medical_sleep');
		$sleep_data = $this->default_db->get_one(array('year'=>$year,'month'=>$month,'userid'=>$to_uid),'year,month,userid,sleep_light,sleep_deep,update_time');
		if(!$sleep_data){
			$sleep_data['update_time_'] = '暂无数据';
			$sleep_data['sleep_str'] = '--';
		}else{
			$sleep_data['update_time_'] = date('Y-m-d H:i',$sleep_data['update_time']);
			$sleep_data['sleep_str'] = round(($sleep_data['sleep_light'] + $sleep_data['sleep_deep']) / 60,2);
		}
		$this->default_db->load('medical_bpm');
		$bpm_data = $this->default_db->get_one(array('userid'=>$to_uid),'year,month,userid,today_num,update_time','update_time DESC');
		if(!$bpm_data){
			$bpm_data['update_time_'] = '暂无数据';
			$bpm_data['today_num_'] = '--';
		}else{
			$bpm_data['update_time_'] = date('Y-m-d H:i',$bpm_data['update_time']);
			$bpm_data['today_num_'] = $bpm_data['today_num'];
		}
		$this->default_db->load('medical_mmhg');
		$mmhg_data = $this->default_db->get_one(array('userid'=>$to_uid),'today_high,today_low,month,year,update_time','update_time DESC');
		if(!$mmhg_data){
			$mmhg_data['update_time_'] = '暂无数据';
			$mmhg_data['today_low_'] = '--';
			$mmhg_data['today_high_'] = '--';
		}else{
			$mmhg_data['update_time_'] = date('Y-m-d H:i',$mmhg_data['update_time']);
			$mmhg_data['today_low_'] = $mmhg_data['today_low'] > 0?$mmhg_data['today_low']:0;
			$mmhg_data['today_high_'] = $mmhg_data['today_high'] > 0?$mmhg_data['today_high']:0;
		}
		$this->default_db->load('medical_spot');
		$spot_data = $this->default_db->get_one(array('year'=>$year,'month'=>$month,'userid'=>$to_uid),'year,month,userid,today_num,update_time');
		if(!$spot_data){
			$spot_data['update_time_'] = '暂无数据';
			$spot_data['today_num_'] = '--';
		}else{
			$spot_data['update_time_'] = date('Y-m-d H:i',$spot_data['update_time']);
			$spot_data['today_num_'] = $spot_data['today_num'];
		}
		$this->default_db->load('medical_temperature');
		$temperature = $this->default_db->get_one(array('year'=>$year,'month'=>$month,'userid'=>$to_uid),'year,month,userid,add_time,tnum');
		if(!$temperature){
			$temperature['add_time_'] = '暂无数据';
		}else{
			$temperature['add_time_'] = date('Y-m-d H:i',$temperature['add_time']);
		}
		$temperature['add_time_'] = date('Y-m-d H:i',$temperature['add_time']);
		$this->default_db->load('bioland_bp_a223');
		$bioland = $this->default_db->get_one(array('userid'=>$to_uid,'year'=>$year,'month'=>$month),'year,month,userid,update_time,today_low,today_high','','','','','','','update_time DESC');
		if(!$bioland){
			$bioland['add_time_'] = '暂无数据';
		}else{
			$bioland['add_time_'] = date('Y-m-d H:i',$bioland['update_time']);
		}
		$datas = array('user_data'=>$data,'data_sport'=>$data_sport,'sleep_data'=>$sleep_data,'bpm_data'=>$bpm_data,'mmhg_data'=>$mmhg_data,'spot_data'=>$spot_data,'temperature'=>$temperature,'bioland'=>$bioland);
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	function timediff($begin_time, $end_time) {
		if ($begin_time < $end_time) {
			$starttime = $begin_time;
			$endtime = $end_time;
		} else {
			$starttime = $end_time;
			$endtime = $begin_time;
		}
		$timediff = $endtime - $starttime;
		$days = intval($timediff / 86400);
		$remain = $timediff % 86400;
		$hours = intval($remain / 3600);
		$remain = $remain % 3600;
		$mins = intval($remain / 60);
		$secs = $remain % 60;
		$res = array("day" => $days, "hour" => $hours, "min" => $mins, "sec" => $secs);
		return $res;
	}
	//求两个已知经纬度之间的距离,单位为米 @param lng1 $ ,lng2 经度 @param lat1 $ ,lat2 纬度 @return float 距离，单位米
	private function getdistance($lng1,$lat1,$lng2,$lat2) {
		// 将角度转为狐度
		$radLat1 = deg2rad($lat1); //deg2rad()函数将角度转换为弧度
		$radLat2 = deg2rad($lat2);
		$radLng1 = deg2rad($lng1);
		$radLng2 = deg2rad($lng2);
		$a = $radLat1 - $radLat2;
		$b = $radLng1 - $radLng2;
		$s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000;
		return $s;
	}
	//二维数组根据指定字段排序
	private function arraySequence($array, $field, $sort = 'SORT_DESC'){
		$arrSort = array();
		foreach ($array as $uniqid => $row) {
			foreach ($row as $key => $value) {
				$arrSort[$key][$uniqid] = $value;
			}
		}
		array_multisort($arrSort[$field], constant($sort), $array);
		return $array;
	}
	//百度地图BD09坐标---->中国正常GCJ02坐标 腾讯地图用的也是GCJ02坐标 @param double $lat 纬度 @param double $lng 经度 @return array();
	function Convert_BD09_To_GCJ02($lat,$lng){
		$x_pi = 3.14159265358979324 * 3000.0 / 180.0;
		$x = $lng - 0.0065;
		$y = $lat - 0.006;
		$z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
		$theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
		$lng = $z * cos($theta);
		$lat = $z * sin($theta);
		return array('lng'=>$lng,'lat'=>$lat);
	}
	//中国正常GCJ02坐标---->百度地图BD09坐标 腾讯地图用的也是GCJ02坐标 @param double $lat 纬度 @param double $lng 经度
	function Convert_GCJ02_To_BD09($lat,$lng){
        $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
        $x = $lng;
        $y = $lat;
        $z =sqrt($x * $x + $y * $y) + 0.00002 * sin($y * $x_pi);
        $theta = atan2($y, $x) + 0.000003 * cos($x * $x_pi);
        $lng = $z * cos($theta) + 0.0065;
        $lat = $z * sin($theta) + 0.006;
        return array('lng'=>$lng,'lat'=>$lat);
	}
	/*增加积分方法 START*/
	function diet_show(){
		$del = (int)$_POST['del']?1:0;
		$this->Quan->set_task(4,20,$this->userid,2,1,$del);//看一篇健康资讯
	}
	function sport_show(){
		$del = (int)$_POST['del']?1:0;
		$this->Quan->set_task(5,30,$this->userid,5,1,$del);//做一次运动
	}
	function all_zan(){
		$del = (int)$_POST['del']?1:0;
		$this->Quan->set_task(6,10,$this->userid,1,1,$del);//为一篇文章点赞
	}
	function all_flower(){
		$del = (int)$_POST['del']?1:0;
		$this->Quan->set_task(8,10,$this->userid,10,1,$del);//送花
	}
	/*增加积分方法 END*/
	function set_region_water(){
		//数据异常区域流水表
		$this->default_db->load('region_water');
	}
	/*app 测量一次心率 方法*/
	function measure_bpm(){
		if(strpos($this->device_text,$_POST['device_name'].":".$_POST['device_mac']) === false){
			$this->default_db->load('member_detail');
			$this->default_db->update(array('device_text'=>$this->device_text.','.$_POST['device_name'].":".$_POST['device_mac']),array('userid'=>$this->userid));
			$this->default_db->load('mac_detail');
			$d_data = $this->default_db->get_one(array('userid'=>$this->userid,'mac_txt'=>$this->device_text));
			if(!$d_data){
				$this->default_db->insert(array('userid'=>$this->userid,'mac_txt'=>$this->device_text,'mac_name'=>$_POST['device_name']));
			}
		}
		$this->add_medical_bpm($_POST);
		$del = (int)$_POST['del']?1:0;
		$data = $this->Quan->set_task(9,10,$this->userid,5,1,$del);
		$bpm = $_POST['health_num'];
		$this->user_healthy('healthy_bpm',$bpm,$state);
		/*更新用户附表 每日最新数据*/
		$state = 0;
		if($bpm < 60 || $bpm > 110){$state = 1;}
		echo json_encode(array('bpm'=>'update_bpm_ok'));
	}
	/*app 测量一次血压 方法*/
	function measure_mmhg(){
		if(strpos($this->device_text,$_POST['device_name'].":".$_POST['device_mac']) === false){
			$this->default_db->load('member_detail');
			$this->default_db->update(array('device_text'=>$this->device_text.','.$_POST['device_name'].":".$_POST['device_mac']),array('userid'=>$this->userid));
			$this->default_db->load('mac_detail');
			$d_data = $this->default_db->get_one(array('userid'=>$this->userid,'mac_txt'=>$this->device_text));
			if(!$d_data){
				$this->default_db->insert(array('userid'=>$this->userid,'mac_txt'=>$this->device_text,'mac_name'=>$_POST['device_name']));
			}
		}
		$del = (int)$_POST['del']?1:0;
		$data = $this->Quan->set_task(10,10,$this->userid,5,1,$del);
		//$mmhg_arr = json_decode($_POST['data']);
		$this->add_medical_mmhg($_POST['health_low'],$_POST['health_high'],$_POST);
		$state = 0;
		if($_POST['health_high'] >= 140 && $_POST['health_low'] >= 90){
			$state = 1;//偏高
		}else if($_POST['health_high'] <= 90 && $_POST['health_low'] <= 60){
			$state = 1;//偏低
		}
		$this->user_healthy('healthy_mmhg',$_POST['health_high'].'/'.$_POST['health_low'],$state);
		echo json_encode($data);
	}
	function measure_spot(){
		if(strpos($this->device_text,$_POST['device_name'].":".$_POST['device_mac']) === false){
			$this->default_db->load('member_detail');
			$this->default_db->update(array('device_text'=>$this->device_text.','.$_POST['device_name'].":".$_POST['device_mac']),array('userid'=>$this->userid));
			$this->default_db->load('mac_detail');
			$d_data = $this->default_db->get_one(array('userid'=>$this->userid,'mac_txt'=>$this->device_text));
			if(!$d_data){
				$this->default_db->insert(array('userid'=>$this->userid,'mac_txt'=>$this->device_text,'mac_name'=>$_POST['device_name']));
			}
		}
		$del = (int)$_POST['del']?1:0;
		$data = $this->Quan->set_task(9,10,$this->userid,5,1,$del);
		$bpm = $_POST['health_num'];
		$this->add_medical_spot($_POST);
		/*更新用户附表 每日最新数据*/
		$state = 0;
		if($bpm < 93 || $bpm > 98){
			$state = 1;
		}
		$this->user_healthy('healthy_spot',$bpm,$state);
		echo json_encode($data);
	}
	/*app 数据更新 方法*/
	function add_data_count(){
		//$info = json_decode(str_replace('\\','',$_POST['info']),true);
		//$this->encryption($info);
		$data_arr = json_decode($_POST['data']);
		if($data_arr['sleep']) $this->user_healthy('healthy_sleep',$data_arr['sleep']);//每日睡眠
		if($data_arr['steps']) $this->user_healthy('healthy_steps',$data_arr['steps']);//每日步数
		if($data_arr['distance']) $this->user_healthy('healthy_distance',$data_arr['distance']);//每日距离
		if($data_arr['remind']) $this->user_healthy('healthy_remind',$data_arr['remind']);//每日提醒(久坐)
		if($data_arr['bpm']) $this->user_healthy('healthy_bpm',$data_arr['bpm']);//每日最新心率
		if($data_arr['spot']) $this->user_healthy('healthy_spot',$data_arr['spot']);//每日最新血氧
		if($data_arr['mmhg']) $this->user_healthy('healthy_mmhg',$data_arr['mmhg']);//每日最新血压
		echo json_encode(array('state'=>1,'msg'=>'完成'));
	}
	/*app 用户测量后写入状态进用户附表 方法 用于精准推荐*/
	function health_status($num,$state,$arr = ''){
		if($num == 11 || $num == 12) return;
		$this->default_db->load('member_detail');
		$member_data = $this->default_db->get_one(array('userid'=>$this->userid));
		$health_label = $member_data['health_label'];
		$member_data['health_label'] = $member_data['health_label']?rtrim($member_data['health_label'],','):'';
		/*以下 附表的状态判断与写入!*/
		if(strpos($health_label,','.$num.',') === false && $state == 1 && $num > 0){
			$a_data = implode(',',array_unique(explode(',',trim($member_data['health_label'].','.$num,','))));
			$this->default_db->update(array('health_label'=>$a_data,'abnormal_time'=>time()),array('userid'=>$this->userid));
			$member_data['health_label'] = $a_data;
		}else if(strpos($health_label,','.$num.',') !== false && $state == 2 && $num > 0){
			$health_label = explode(',',trim($member_data['health_label'],','));
			$health_label_new = $this->delByValue($health_label,$num)?','.implode(',',array_unique($health_label_new)).',':'';
			$this->default_db->update(array('health_label'=>$health_label_new,'abnormal_time'=>time()),array('userid'=>$this->userid));
			$member_data['health_label'] = $health_label_new;
		}
		if($arr){
			$health_label = explode(',',trim($member_data['health_label'],','));
			foreach($arr as $v){
				$health_label = $this->delByValue($health_label,$v);
			}
			$this->default_db->update(array('health_label'=>implode(',',array_unique($health_label)),'abnormal_time'=>time()),array('userid'=>$this->userid));
		}
		$this->default_db->load('kith_and_kin');
		if($state == 1){
			$this->default_db->update(array('fhstate'=>1),array('fuid'=>$this->userid));
			$this->default_db->update(array('kakhstate'=>1),array('kakuid'=>$this->userid));
		}
		echo json_encode($data);
	}
	function delByValue($arr, $value) {
		if (!is_array($arr)) return $arr;
		foreach($arr as $k=>$v){
			if ($v == $value) unset($arr[$k]);
		}
		return $arr;
	}
	function update_sport_data($steps = 0,$cal = 0,$distan = 0,$sleep_ = 0){
		$this->default_db->load('logtxt');
		$this->default_db->insert(array('log'=>'运动数据：'.array2string($_POST)));
		//设备数据上传2.0
		try{
			pc_base::load_app_class('tools','equipment',0);
			//dictionary_id:1 心率 category_id:4 手环 model_id:
			$category_id = $model_id = 0;
			switch($post['device_name']){
				case 'x9pro':
					$category_id = 4;
					$model_id = 3;
					break;
				case 'v3':
					$category_id = 4;
					$model_id = 4;
					if (!$_POST['device_mac']) $_POST['device_mac'] = 'tmp_'.date('YmdHi');
					break;
				case 'W3':
				case 'tjd':
					$category_id = 4;
					$model_id = 5;
					break;
			}
			$param = array('user_id'=>$this->userid,'dictionary_id'=>9,'category_id'=>$category_id,'model_id'=>$model_id,'sn'=>$_POST['device_mac'],'value'=>intval($_POST['steps']),'value2'=>floatval($_POST['distance']),'value3'=>floatval($_POST['calories']));
			$tools = new tools();
			$tools->create($param);
		}catch (Exception $e) {
			//echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
		/*健康排行榜 数据接口方法 运动档案 通用方法*/
		$this->default_db->load('sport_walk');
		$data_arr = json_decode($_POST['data']);
		$today_walk = $_POST['walk']?$_POST['walk']:0;//步数
		if($steps > 0) $today_walk = $steps;
		$calorie = $_POST['calorie']?$_POST['calorie']:0;//卡路里
		if($cal > 0) $calorie = $cal;
		$distance = $_POST['distance']?$_POST['distance']:0;//距离 km
		if($distan > 0) $distance = $distan;
		$sleep = $_POST['sleep']?$_POST['sleep']:0;//睡眠 sleep 浅睡眠/深睡眠
		if($sleep_ > 0) $sleep = $sleep_;
		$hisNum = $_POST['hisNum']?$_POST['hisNum']:0;
		$month = $_POST['month']?$_POST['month']:date('m');
		$year = $_POST['year']?$_POST['year']:date('Y');
		$last_time = $_POST['last_time']?$_POST['last_time']:time();
		$data_ = $this->default_db->get_one(array('userid'=>$this->userid,'add_month'=>$month,'add_year'=>$year));
		$data_old = json_decode($data_['walk_json'],true);
		$data_old_ = array_reverse($data_old);
		$to_data_0 = strtotime(date("Y-m-d"),time());
		$to_data_06 = strtotime(date("Y-m-d 06:00:00"),time());
		$to_data_12 = strtotime(date("Y-m-d 12:00:00"),time());
		$to_data_18 = strtotime(date("Y-m-d 18:00:00"),time());
		$to_data_24 = strtotime(date("Y-m-d 23:59:59"),time());
		if($data_){
			/*更新*/
			if($data_['last_time'] < strtotime(date("Y-m-d"),time())) $this->default_db->update(array('total_zan'=>0),array('userid'=>$this->userid,'add_month'=>$month));//如果 数据库的最后更新时间小于今日的零点 则将赞统计归零
			if($data_old_[0]['add_time'] < $to_data_0){
				$data_old[] = array(
					'add_date'=>date('Ymd',$last_time),
					'add_time'=>$last_time,
					'walk'=>$today_walk,
					'calorie'=>$calorie,
					'distance'=>$distance,
					'sleep'=>$sleep,
					'hisNum'=>$hisNum,
					'to_data_time'=>$to_data_06
				);
			}elseif($data_old_[0]['add_time'] > $to_data_0 && $data_old_[0]['add_time'] <= $to_data_06){
				if($data_old_[0]['to_data_time'] != $to_data_06){
					$data_old[] = array(
						'add_date'=>date('Ymd',$last_time),
						'add_time'=>$last_time,
						'walk'=>$today_walk,
						'calorie'=>$calorie,
						'distance'=>$distance,
						'sleep'=>$sleep,
						'hisNum'=>$hisNum,
						'to_data_time'=>$to_data_06
					);
				}else{
					$data_old_[0]['add_date'] = date('Ymd',$last_time);
					$data_old_[0]['add_time'] = $last_time;
					$data_old_[0]['walk'] = $today_walk;
					$data_old_[0]['calorie'] = $calorie;
					$data_old_[0]['distance'] = $distance;
					$data_old_[0]['sleep'] = $sleep;
					$data_old_[0]['hisNum'] = $hisNum;
					$data_old_[0]['to_data_time'] = $to_data_06;
					$data_old = array_reverse($data_old_);
				}
			}elseif($data_old_[0]['add_time'] > $to_data_06 && $data_old_[0]['add_time'] <= $to_data_12){
				if($data_old_[0]['to_data_time'] != $to_data_12){
					$data_old[] = array(
						'add_date'=>date('Ymd',$last_time),
						'add_time'=>$last_time,
						'walk'=>$today_walk,
						'calorie'=>$calorie,
						'distance'=>$distance,
						'sleep'=>$sleep,
						'hisNum'=>$hisNum,
						'to_data_time'=>$to_data_12
					);
				}else{
					$data_old_[0]['add_date'] = date('Ymd',$last_time);
					$data_old_[0]['add_time'] = $last_time;
					$data_old_[0]['walk'] = $today_walk;
					$data_old_[0]['calorie'] = $calorie;
					$data_old_[0]['distance'] = $distance;
					$data_old_[0]['sleep'] = $sleep;
					$data_old_[0]['hisNum'] = $hisNum;
					$data_old_[0]['to_data_time'] = $to_data_12;
					$data_old = array_reverse($data_old_);
				}
			}elseif($data_old_[0]['add_time'] > $to_data_12 && $data_old_[0]['add_time'] <= $to_data_18){
				if($data_old_[0]['to_data_time'] != $to_data_18){
					$data_old[] = array(
						'add_date'=>date('Ymd',$last_time),
						'add_time'=>$last_time,
						'walk'=>$today_walk,
						'calorie'=>$calorie,
						'distance'=>$distance,
						'sleep'=>$sleep,
						'hisNum'=>$hisNum,
						'to_data_time'=>$to_data_18
					);
				}else{
					$data_old_[0]['add_date'] = date('Ymd',$last_time);
					$data_old_[0]['add_time'] = $last_time;
					$data_old_[0]['walk'] = $today_walk;
					$data_old_[0]['calorie'] = $calorie;
					$data_old_[0]['distance'] = $distance;
					$data_old_[0]['sleep'] = $sleep;
					$data_old_[0]['hisNum'] = $hisNum;
					$data_old_[0]['to_data_time'] = $to_data_18;
					$data_old = array_reverse($data_old_);
				}
			}elseif($data_old_[0]['add_time'] > $to_data_18 && $data_old_[0]['add_time'] <= $to_data_24){
				if($data_old_[0]['to_data_time'] != $to_data_24){
					$data_old[] = array(
						'add_date'=>date('Ymd',$last_time),
						'add_time'=>$last_time,
						'walk'=>$today_walk,
						'calorie'=>$calorie,
						'distance'=>$distance,
						'sleep'=>$sleep,
						'hisNum'=>$hisNum,
						'to_data_time'=>$to_data_24
					);
				}else{
					$data_old_[0]['add_date'] = date('Ymd',$last_time);
					$data_old_[0]['add_time'] = $last_time;
					$data_old_[0]['walk'] = $today_walk;
					$data_old_[0]['calorie'] = $calorie;
					$data_old_[0]['distance'] = $distance;
					$data_old_[0]['sleep'] = $sleep;
					$data_old_[0]['hisNum'] = $hisNum;
					$data_old_[0]['to_data_time'] = $to_data_24;
					$data_old = array_reverse($data_old_);
				}
			}
			$data_old = json_encode($data_old);
			$this->default_db->update(array('today_walk'=>$today_walk,'calorie'=>$calorie,'distance'=>$distance,'sleep'=>$sleep,'last_time'=>$last_time,'walk_json'=>$data_old,'username'=>$this->username,'nickname'=>$this->nickname),array('userid'=>$this->userid,'add_month'=>$month,'add_year'=>$year));
		}else{
			/*新增*/
			$this->default_db->insert(array('today_walk'=>$today_walk,'calorie'=>$calorie,'distance'=>$distance,'sleep'=>$sleep,'last_time'=>$last_time,'walk_json'=>$data_old,'userid'=>$this->userid,'add_month'=>$month,'add_year'=>$year,'userid'=>$this->userid,'username'=>$this->username,'nickname'=>$this->nickname,'portrait'=>$this->portrait));
		}
		$this->update_walk_ss($today_walk);
		$this->user_healthy('healthy_steps',$today_walk);//每日步数
		$this->user_healthy('health_distance',$distance);//运动距离
		$this->user_healthy('health_remind',$hisNum);//久坐提醒
		$this->user_healthy('healthy_calorie',$calorie);//卡路里
	}
	public function update_walk_ss($today_walk = 0){
		//$today_walk = 20000;
		$this->default_db->load('sport_ranking');//运动排行榜
		$da = $this->default_db->get_one('`add_time` >= '.strtotime(date('Ymd 00:00:00',time())).' AND `add_time` <= '.strtotime(date('Ymd 23:59:59')));
		$daa = json_decode($da['data_json'],true);
		$first_walk = $da['first_walk'];$first_user = $da['first_user'];$first_por = $da['first_por'];$first_nick = $da['first_nick'];
		if(!empty($daa)){
			$h_walk = $da['first_walk']?$da['first_walk']:0;
			//$this->default_db->delete(array('add_time'=>strtotime(date('Ymd 23:59:59',time()))));
			array_multisort(array_column($daa,'walk'),SORT_DESC,$daa);
			$aa = $daa;
			$daa = array();
			foreach($aa as $c){
				$daa[$c['userid']] = $c;
			}
			foreach($daa as $k=>$v){
				if($v['walk'] > $h_walk){
					$h_walk = $v['walk'];
					$first_walk = $v['walk'];
					$first_user = $v['username'];
					$first_por = $v['portrait'];
					$first_nick = urlencode($v['nickname']);
				}
				if(!empty($daa[$this->userid]) && ($daa[$this->userid]['walk'] < $today_walk || $daa[$this->userid]['walk'] > $today_walk)){
					$daa[$this->userid]['walk'] = $today_walk;
					break;
				}else if(empty($daa[$this->userid]) && $today_walk > $v['walk']){
					array_pop($daa);
					$daa[$this->userid]['userid'] = $this->userid;
					$daa[$this->userid]['walk'] = $today_walk;
					$daa[$this->userid]['nickname'] = urlencode($this->nickname);
					$daa[$this->userid]['portrait'] = $this->portrait;
					break;
				}
			}
			if($today_walk > $h_walk){
				$first_walk = $today_walk;
				$first_user = $this->username;
				$first_por = $this->portrait;
				$first_nick = urlencode($this->nickname);
			}
			//array_multisort(array_column($daa,'walk'),SORT_DESC,$daa);
			$this->default_db->load('sport_ranking');
			$this->default_db->update(array('first_walk'=>$first_walk,'first_user'=>$first_user,'first_por'=>$first_por,'first_nick'=>$first_nick,'data_json'=>json_encode($daa)),array('id'=>$da['id']));
		}else{
			$this->default_db->load('member_detail');
			$f_da = $this->default_db->listinfo(array('healthy_steps'=>0),'userid ASC',1,10,'','','','','userid,healthy_steps,portrait');
			foreach($f_da as $v){
				$m_da[$v['userid']]['userid'] = $v['userid'];
				$m_da[$v['userid']]['portrait'] = $v['portrait'];
				$ids[] = $v['userid'];
			}
			$this->default_db->load('member');
			$tmp_da = $this->default_db->select(array('userid'=>array('in',$ids)),'userid,nickname');
			$daa_[$this->userid]['userid'] = $this->userid;
			$daa_[$this->userid]['nickname'] = urlencode($this->nickname);
			$daa_[$this->userid]['walk'] = $today_walk;
			$daa_[$this->userid]['portrait'] = $this->portrait;
			foreach($tmp_da as $v){
				if($v['userid'] != $this->userid){
					$f_da[$v['userid']]['nickname'] = $v['nickname'];
					$daa_[$v['userid']]['userid'] = $v['userid'];
					$daa_[$v['userid']]['nickname'] = urlencode($v['nickname']);
					$daa_[$v['userid']]['walk'] = 0;
					$daa_[$v['userid']]['portrait'] = $m_da[$v['userid']]['portrait'];
				}
			}
			if(count($daa_) == 11) array_pop($daa_);
			$this->default_db->load('sport_ranking');
			//$this->default_db->delete(array('add_time'=>strtotime(date('Ymd 23:59:59',time()))));
			$this->default_db->insert(array('add_time'=>strtotime(date('Ymd 23:59:59',time())),'data_json'=>json_encode($daa_),'first_walk'=>$today_walk,'first_user'=>$this->username,'first_por'=>$this->portrait,'first_nick'=>urlencode($this->nickname)));
		}
	}
	function get_medical_sport_today($is_return = 0){
		$year = (int)date('Y');
		$month = (int)date('m');
		$day = (int)date('d');
		$date_time = (int)$_POST['date_time'];// 1 = 今天,7 = 7天 ...
		$date_search = (int)$_POST['date_search'];
		$this->default_db->load('sport_walk');
		$this->userid = $_POST['to_uid']>0?$_POST['to_uid']:$this->userid;
		$data = $this->default_db->get_one(array('add_year'=>$year,'add_month'=>$month,'userid'=>$this->userid));
		$data_ = $this->default_db->get_one(array('add_year'=>$year,'add_month'=>$month-1,'userid'=>$this->userid));
		if(!$data) $data = $data_;
		$sport_data = $data['walk_json'];
		$sport_data_ = $data_['walk_json'];
		$sport_data = json_decode($sport_data,true);
		$sport_data_ = json_decode($sport_data_,true);
		if($sport_data_){
			$sport_array = array_merge($sport_data,$sport_data_);
		}else{
			$sport_array = $sport_data;
		}
		$sport['day_data']['walk'] = $data['today_walk'];
		$sport['day_data']['distance'] = $data['distance'];
		$sport['day_data']['calorie'] = $data['calorie'];
		$sport['day_data']['update_time'] = date('Y-m-d',$data['last_time']);//最近一次详细时间
		$sport['day_data']['is_ok'] = ceil($data['today_walk'] / 15000 * 100);//完成度
		$get_date = $_POST['get_date']?$_POST['get_date']:'Y-m-d';
		$morning_start = strtotime(date($get_date.' 06:00:00',time()));
		$noon_start = strtotime(date($get_date.' 12:00:00',time()));
		$afternoon_start = strtotime(date($get_date.' 18:00:00',time()));
		$night_start = strtotime(date($get_date.' 23:59:59',time()));
		$seven_start = strtotime(date($get_date.' 23:59:59', strtotime('-7 days')));//七天前
		$thirty_start = strtotime(date($get_date.' 23:59:59', strtotime('-30 days')));//三十天前
		for($i=6;$i>=0;$i--){
			$seven_day_[$i]['a'] = strtotime(date($get_date.' 00:00:01', strtotime('-'.$i.' days')));//七天前
			$seven_day_[$i]['b'] = strtotime(date($get_date.' 23:59:59', strtotime('-'.$i.' days')));//七天前
			$data_arr['seven_data'][$i] = 0;
		}
		for($i=29;$i>=0;$i--){
			$thirty_day_[$i]['a'] = strtotime(date($get_date.' 00:00:01', strtotime('-'.$i.' days')));//三十天前
			$thirty_day_[$i]['b'] = strtotime(date($get_date.' 23:59:59', strtotime('-'.$i.' days')));//三十天前
			$data_arr['thirty_data'][$i] = 0;
		}
		$data_arr['today_data']['a'] = 0;
		$data_arr['today_data']['b'] = 0;
		$data_arr['today_data']['c'] = 0;
		$data_arr['today_data']['d'] = 0;
		foreach($sport_array as $k=>$v){
			if($v['to_data_time'] == $morning_start){
				$data_arr['today_data']['a'] = $v['walk'];
			}elseif($v['to_data_time'] == $noon_start){
				$data_arr['today_data']['b'] = $v['walk'];
			}elseif($v['to_data_time'] == $afternoon_start){
				$data_arr['today_data']['c'] = $v['walk'];
			}elseif($v['to_data_time'] == $night_start){
				$data_arr['today_data']['d'] = $v['walk'];
			}
			for($i=6;$i>=0;$i--){
				if($v['add_time'] >= $seven_day_[$i]['a'] && $v['add_time'] <= $seven_day_[$i]['b']){
					$data_arr['seven_data'][$i] = $v['walk'];
					$data_arr['seven_data_'][$i]['calorie'] = $v['calorie'];
					$data_arr['seven_data_'][$i]['distance'] = $v['distance'];
				}
			}
			for($i=29;$i>=0;$i--){
				if($v['add_time'] >= $thirty_day_[$i]['a'] && $v['add_time'] <= $thirty_day_[$i]['b']){
					$data_arr['thirty_data'][$i] = $v['walk'];
					$data_arr['thirty_data_'][$i]['calorie'] = $v['calorie'];
					$data_arr['thirty_data_'][$i]['distance'] = $v['distance'];
				}
			}
		}
		foreach($data_arr['seven_data_'] as $v){
			$data_arr['seven_data_calorie'] += $v['calorie'];
			$data_arr['seven_data_distance'] += (int)$v['distance'];
		}
		foreach($data_arr['thirty_data_'] as $v){
			$data_arr['thirty_data_calorie'] += $v['calorie'];
			$data_arr['thirty_data_distance'] += (int)$v['distance'];
		}
		$datas = array('sport'=>$data_arr,'sport_'=>$sport);
		if($is_return){
			return $datas;
		}else{
			echo json_encode($datas);
		}
	}
	private function encryption($info){
		/*加密 方法*/
		if(time() - $info['time_str'] > 30) exit(json_encode(array('state'=>'error','msg'=>'overtime')));
		$code = 'Yjx_QsCFTYJMKo_2017';//两站指定加密code
		$sign = strtoupper(md5(md5(md5($code.$info['time_str']))));
		if($sign != strtoupper($info['sign'])) exit(json_encode(array('state'=>'error','msg'=>'signature error')));
		echo json_encode(array('state'=>'success','msg'=>'verify success'));
	}
	public function update_today_data_v3(){
		$this->default_db->load('logtxt');
		$this->default_db->insert(array('log'=>'运动数据2：'.array2string($_POST)));
		//仅更新今日数据 未更新档案
		$new_year = date('Y');
		$new_month = date('m');
		$this->default_db->load('member_detail');
		$data = $this->default_db->get_one(array('userid'=>$this->userid));
		$healthy_bpm = $_POST['heartRate'];
		$healthy_sleep = $_POST['deepSleep'] + $_POST['shallowSleep'];
		$healthy_steps = $_POST['steps'];
		$health_distance = $_POST['distance'];
		$healthy_calorie = $_POST['calories'];
		$device_name = isset($_POST['device_name'])? $_POST['device_name'] : 'v3'; 
		$this->update_sport_data($healthy_steps,$healthy_calorie,$health_distance);//写入运动档案
		//更新用户附表 今日数据
		$this->user_healthy('healthy_steps',$healthy_steps);//每日步数
		$this->user_healthy('health_distance',$health_distance);//运动距离
		$this->user_healthy('healthy_calorie',$healthy_calorie);//卡路里
		$this->user_healthy('healthy_bpm',$healthy_bpm);//心率
		$this->default_db->load('medical_sleep');
		$da = $this->default_db->get_one(array('userid'=>$this->userid,'year'=>$new_year,'month'=>$new_month));
		$data_arr = array('add_date'=>date('Ymd'),'add_time'=>$_POST['time'],'device_name'=>$device_name,'device_number'=>'0','end_time'=>'0','sleep_awake'=>'0','sleep_day'=>date('Y-m-d'),'sleep_length'=>$healthy_sleep,'sleep_deep'=>$_POST['deepSleep'],'sleep_light'=>$_POST['shallowSleep']);
		if(empty($da)){
			$this->default_db->insert(array('userid'=>$this->userid,'year'=>$new_year,'month'=>$new_month,'update_time'=>$_POST['time'],'device_name'=>$device_name,'data_json'=>json_encode($data_arr)));
		}else{
			if($da['update_time'] == $_POST['time']) return;
			$data_arr_ = json_decode($da['data_json'],true);
			$data_arr_[] = $data_arr;
			$this->default_db->update(array('data_json'=>json_encode($data_arr_)),array('id'=>$da['id']));
		}
	}
	public function set_data_v3_tmp(){
		//仅更新今日数据 档案当天记录 未更新档案每天json数据
		$new_year = date('Y');
		$new_month = date('m');
		$this->default_db->load('member_detail');
		$data = $this->default_db->get_one(array('userid'=>$this->userid));
		$healthy_bpm = $_POST['health_num'];
		$health_high = $_POST['health_high'];
		$health_low = $_POST['health_low'];
		if($data){
			$da = $this->default_db->update(array('healthy_bpm'=>$healthy_bpm,'healthy_mmhg'=>$health_high.'/'.$health_low,'healthy_time'=>time()),array('userid'=>$this->userid));
		}
		$this->default_db->load('medical_bpm');
		$bpm_data = $this->default_db->get_one(array('userid'=>$this->userid,'year'=>$new_year,'month'=>$new_month));
		if($bpm_data){
			$this->default_db->update(array('today_num'=>$healthy_bpm,'update_time'=>time()),array('id'=>$bpm_data['id']));
		}else{
			//$this->default_db->insert(array('today_num'=>$healthy_bpm,'update_time'=>time()));
		}
		if($health_high > 0 || $health_low > 0){
			$this->default_db->load('medical_mmhg');
			$mmhg_data = $this->default_db->get_one(array('userid'=>$this->userid,'year'=>$new_year,'month'=>$new_month));
			if($mmhg_data){
				$this->default_db->update(array('today_high'=>$health_high,'today_low'=>$health_low,'update_time'=>time()),array('id'=>$mmhg_data['id']));
			}else{
				//$this->default_db->insert(array('today_num'=>$healthy_bpm,'update_time'=>time()));
			}
		}
	}
	//=========================
	private function check_userid($userid){
		$userid = $userid? $userid : $this->userid;
		return($userid);
	}
	public function get_index_data(){
		if($_GET['userid_']) $this->userid = intval(sys_auth($_GET['userid_'], 'DECODE'));
		$this->default_db->load('member_detail');
		$return = $this->default_db->get_one(array('userid'=>$this->userid),'userid,healthy_bpm,healthy_spot,healthy_mmhg,healthy_sleep,healthy_steps,health_distance,health_remind,healthy_time,target_steps,healthy_calorie');
		if($return['target_steps'] > 0 && $return['target_steps'] < $return['healthy_steps']){
			$return['steps_arc'] = 1000;//100%
		}elseif($return['target_steps'] > $return['healthy_steps']){
			$return['steps_arc'] = $return['healthy_steps'] / $return['target_steps'] * 100 * 10;//100% *10为前端需要的转换
		}
		if($return['target_steps'] <= 0 && $return['healthy_steps'] > 0){
			$return['steps_arc'] = 1000;//100%
		}else if($return['target_steps'] < 0 && $return['healthy_steps'] <= 0){
			$return['steps_arc'] = 0;//0%
		}
		if($return['health_distance'] > 0){
			$return['distance_arc'] = $return['health_distance'] / 10 * 100 * 10;//100% * 10
			$return['distance_arc'] = $return['distance_arc']>1000?1000:$return['distance_arc'];
		}else{
			$return['distance_arc'] = 0;//0%
		}
		if($return['health_remind'] > 0){
			$return['remind_arc'] = $return['health_remind'] / 100 * 100 * 10;//100% * 10
			$return['remind_arc'] = $return['remind_arc']>1000?1000:$return['remind_arc'];
		}else{
			$return['remind_arc'] = 0;//0%
		}
		if($return['healthy_calorie'] > 0){
			$return['calorie_arc'] = $return['healthy_calorie'] / 1000 * 100 * 10;//100% * 10
			$return['calorie_arc'] = $return['calorie_arc']>10000?10000:$return['calorie_arc'];
		}else{
			$return['calorie_arc'] = 0;//0%
		}
		$return['healthy_sleep_o'] = $return['healthy_sleep'] % 60;//睡眠总时长 分钟
		$return['healthy_sleep_t'] = floor($return['healthy_sleep'] / 60);//睡眠总时长 小时
		$return['healthy_sleep'] = $return['healthy_sleep_t'].".".$return['healthy_sleep_o'];
		echo $_GET['jsoncallback']."(".json_encode($return).")";
	}
	public function get_user_detail(){
		/**/
		$this->default_db->load('member_detail');
		$data = $this->default_db->get_one(array('userid'=>$this->userid));
		echo json_encode(array('u_weight'=>$data['u_weight']));
	}
	private function user_healthy($one,$two,$state=0){
		/*更新用户附表 每日最新数据*/
		//$one = health_num 需要更新的字段名
		//$two = health_num 需要更新的字段值
		$this->default_db->load('member_detail');
		$user_data = $this->default_db->get_one(array('userid'=>$this->userid));
		if($one == 'healthy_bpm'){
			if($two < 60 || $two > 100){
				if(strpos($user_data['healthy_state'],'1') === false){
					$state = $user_data['healthy_state'].'1';
				}else{
					$state = $user_data['healthy_state'];
				}
			}else if($two >= 60 || $two <= 100){
				if(strpos($user_data['healthy_state'],'1') !== false){
					$state = str_replace('1','',$user_data['healthy_state']);
				}else{
					$state = $user_data['healthy_state'];
				}
			}
		}else if($one == 'healthy_spot'){
			if($two < 90 || $two > 98){
				if(strpos($user_data['healthy_state'],'2') === false){
					$state = $user_data['healthy_state'].'2';
				}else{
					$state = $user_data['healthy_state'];
				}
			}else if($two >= 90 || $two < 99){
				if(strpos($user_data['healthy_state'],'2') !== false){
					$state = str_replace('2','',$user_data['healthy_state']);
				}else{
					$state = $user_data['healthy_state'];
				}
			}
		}else if($one == 'healthy_mmhg'){
			$data_ = explode('/',$two);
			if(($data_[0] >= 140 && $data_[1] >= 90) || ($data_[0] <= 90 && $data_[1] <= 60)){
				if(strpos($user_data['healthy_state'],'3') === false){
					$state = $user_data['healthy_state'].'3';
				}else{
					$state = $user_data['healthy_state'];
				}
			}else{
				if(strpos($user_data['healthy_state'],'3') !== false){
					$state = str_replace('3','',$user_data['healthy_state']);
				}else{
					$state = $user_data['healthy_state'];
				}
			}
		}
		if($two){
			$this->default_db->update(array($one=>$two,'healthy_time'=>time(),'healthy_state'=>$state,'abnormal_time'=>time()),array('userid'=>$this->userid));
		}else{
			$this->default_db->update(array('log'=>'err','abnormal_time'=>time()),array('userid'=>$this->userid));
		}
	}
	public function clear_data_table(){
		/*将每日步数清零*/
		$year = date('Y');
		$month = date('m');
		$day = date('d');
		$this->default_db->load('sport_walk');
		$this->default_db->update(array('today_walk'=>0),array('add_year'=>$year,'add_month'=>$month));
		$this->default_db->load('sport_walk_zan');
		$this->default_db->delete();
	}
	public function set_total_num(){
		//总统计表记录
		$num = $_GET['num'];//心率数据
		$this->default_db->load("total_num");
		$temp = $this->default_db->get_one(array('addtime'=>strtotime(date('Ymd 00:00:00'))));
		if($_GET['type'] == 'bpm'){
			if($temp){
				$a = $this->default_db->update(array('bpm_count'=>'+=1','bpm_num_count'=>'+='.$num),array('addtime'=>strtotime(date('Ymd 00:00:00'))));
			}else{
				$a = $this->default_db->insert(array('bpm_count'=>'+=1','bpm_num_count'=>'+='.$num,'addtime'=>strtotime(date('Ymd 00:00:00'))));
			}
		}else if($_GET['type'] == 'mmhg'){
			if($temp){
				$a = $this->default_db->update(array('mmhg_count'=>'+=1','mmhg_num_count'=>'+='.$num),array('addtime'=>strtotime(date('Ymd 00:00:00'))));
			}else{
				$a = $this->default_db->insert(array('mmhg_count'=>'+=1','mmhg_num_count'=>'+='.$num,'addtime'=>strtotime(date('Ymd 00:00:00'))));
			}
		}
		echo $_GET['jsoncallback']."(".json_encode($a).")";
	}
	public function get_recharge_record(){
		$userid = param::get_cookie('_userid');
		if (!$userid) return array();
		$page = isset($_GET['page'])&&intval($_GET['page'])?intval($_GET['page']):1;
		$pagesize = 20;
		$where = '`userid`='.$userid;
		$this->default_db->load('order');
		$datas = $this->default_db->listinfo($where,'addtime DESC',$page,$pagesize);
		empty($datas[0])&&$datas=array();
		$status = $datas?1:0;
		return array('data'=>array('record'=>$datas),'status'=>$status);
	}
	public function set_visit_data(){
		$visit_src = $_SERVER['HTTP_REFERER'];
		$visit_src = urldecode($_GET['get_src']);
		$visit_ip = $_SERVER['REMOTE_ADDR'];
		$visit_time = time();
		$visit_time_m = strtotime(date('Y-m-01 00:00:00',time()));
		$this->default_db->load('visit_detail');
		$data = $this->default_db->get_one(array('userid'=>$this->userid,'visit_time_m'=>$visit_time_m));
		if($visit_src != ''){
			if($data){
				$data_arr = json_decode($data['data_json'],true);
				if($data_arr && count($data_arr) < 500){
					$data_arr[] = array('visit_src'=>$visit_src,'visit_ip'=>$visit_ip,'visit_time'=>$visit_time);
					$data_json = json_encode($data_arr);
					$this->default_db->update(array('visit_src'=>$visit_src,'visit_ip'=>$visit_ip,'visit_time'=>$visit_time,'count'=>'+=1','data_json'=>$data_json),array('userid'=>$this->userid,'visit_time_m'=>$visit_time_m));
				}else if($data_arr && count($data_arr) > 500){
					unset($data_arr[0]);
					$data_arr[] = array('visit_src'=>$visit_src,'visit_ip'=>$visit_ip,'visit_time'=>$visit_time);
					$data_json = json_encode($data_arr);
					$this->default_db->update(array('visit_src'=>$visit_src,'visit_ip'=>$visit_ip,'visit_time'=>$visit_time,'count'=>'+=1','data_json'=>$data_json),array('userid'=>$this->userid,'visit_time_m'=>$visit_time_m));
				}
			}else{
				$data_arr = array('visit_src'=>$visit_src,'visit_ip'=>$visit_ip,'visit_time'=>$visit_time);
				$data_json = json_encode(array($data_arr));
				$this->default_db->insert(array('userid'=>$this->userid,'visit_src'=>$visit_src,'visit_ip'=>$visit_ip,'count'=>'+=1','data_json'=>$data_json,'visit_time'=>$visit_time,'visit_time_m'=>$visit_time_m));
			}
		}
	}
	public function get_activitie(){
		$this->default_db->load('activities');
		$data = $this->default_db->listinfo('','start_time DESC',1,6);
		echo $_GET['jsoncallback_a']."(".json_encode($data).")";
	}
	public function get_coupon_detail_data(){
		$time = time();
		$auth = array(
			'time'=>$time,
			'plat'=>1,
			'sign'=>md5('1yjxun'.$time),
		);
		$url = 'http://shop.yjxun.cn/index.php?m=wpm&c=datas&a=getCouponDetail&couponid='.$_POST['cid'];
		$data = _curl_post($url,$auth);
		return json_decode($data);
	}
	public function add_temperature(){
		$this->default_db->load('medical_temperature');
		$year = date('Y');
		$month = date('m');
		$device_name = $_POST['device_name'];
		$device_number = $_POST['device_number'];
		$add_date = date('Y-m-d H:i:s');
		$add_time = time();
		if($_GET['read'] == 1){
			$da = $this->default_db->get_one(array('year'=>$year,'month'=>$month,'userid'=>$this->userid));
			echo json_encode($da);exit();
		}
		if($_GET['read'] == 2){
			$da = $this->default_db->get_one(array('year'=>$year,'month'=>$month,'userid'=>$this->userid));
			$da_arr = json_decode($da['data_json'],true);
			foreach($da_arr as $k=>$v){
				if($k < 10) $data[] = $v;
			}
			echo json_encode($data);exit();
		}
		$tnum = (float)$_POST['tnum'];
		$data_json = array('add_date'=>$add_date,'add_time'=>$add_time,'device_name'=>$device_name,'device_number'=>$device_number,'tnum'=>$tnum);
		$data = $this->default_db->get_one(array('year'=>$year,'month'=>$month,'userid'=>$this->userid));
		if($data){
			$data_json_tmp = json_decode($data['data_json'],true);
			$data_json_tmp[] = $data_json;
			$this->default_db->update(array('device_name'=>$device_name,'device_number'=>$device_number,'add_date'=>$add_date,'add_time'=>$add_time,'tnum'=>$tnum,'data_json'=>json_encode($data_json_tmp)),array('year'=>$year,'month'=>$month,'userid'=>$this->userid));
		}else{
			$this->default_db->insert(array('year'=>$year,'month'=>$month,'userid'=>$this->userid,'device_name'=>$device_name,'device_number'=>$device_number,'add_date'=>$add_date,'add_time'=>$add_time,'tnum'=>$tnum,'data_json'=>json_encode(array($data_json))));
		}
		if($tnum > 37 || $tnum < 36){
			$this->Quan->set_tp_msg(array('userid'=>$this->userid));
			$this->default_db->load('kith_and_kin');
			$this->default_db->update(array('fhstate'=>1),array('fuid'=>$this->userid));
			$this->default_db->update(array('kakhstate'=>1),array('kakuid'=>$this->userid));
			$this->default_db->load('abnormal_data');
			$this->default_db->insert(array('uid'=>$this->userid,'type'=>4,'da1'=>$tnum,'province'=>$_POST['province'],'city'=>$_POST['city'],'district'=>$_POST['district'],'add_time'=>time()));
		}
		//设备数据上传2.0
		try{
			pc_base::load_app_class('tools','equipment',0);
			$category_id = $model_id = 0;
			$param = array('user_id'=>$this->userid,'dictionary_id'=>4,'category_id'=>$category_id,'model_id'=>$model_id,'sn'=>'','value'=>$tnum);
			$tools = new tools();
			$tools->create($param);
		}catch (Exception $e) {
			//echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
	}
	public function update_file_temp(){
		$save_path = 'caches/caches_/user_excel.xls';
		if($_POST['upload_s']){
			if($_FILES['error'] == 0){
				$ccc = date('YmdHis');
				if (!file_exists("caches/caches_".$_FILES["file_"]["tmp_name"])){
					$a = move_uploaded_file($_FILES["file_"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/uploadfile/member/'.$ccc.'.xls');
					$save_path = '/uploadfile/member/'.$ccc.'.xls';
				}
			}else{
				exit('上传失败 , 未知错误.');
			}
		}
		pc_base::load_sys_class('reader','libs'.DIRECTORY_SEPARATOR.'ExcelReader',0);
		$xls = new Spreadsheet_Excel_Reader();
		$xls->setOutputEncoding('utf-8');  //设置编码 
		$xls->read($save_path);  //解析文件 
		$sheets = 0;
		$numRows = $xls->sheets[$sheets]['numRows'];
		$start_cells = isset($_GET['excel_top'])? (int)$_GET['excel_top'] : 2;
		//$len = $start_cells + 10;
		$addtime = SYS_TIME;
		for($i=$start_cells;$i<=$numRows;$i++) { 
			$data_values[$i]['a'] = trim($xls->sheets[$sheets]['cells'][$i][1]);
			$data_values[$i]['b'] = trim($xls->sheets[$sheets]['cells'][$i][2]);
			$data_values[$i]['c'] = trim($xls->sheets[$sheets]['cells'][$i][3]);
			$data_values[$i]['d'] = trim($xls->sheets[$sheets]['cells'][$i][4]);
			$data_values[$i]['e'] = trim($xls->sheets[$sheets]['cells'][$i][5]);
			$data_values[$i]['f'] = trim($xls->sheets[$sheets]['cells'][$i][6]);
			$data_values[$i]['g'] = trim($xls->sheets[$sheets]['cells'][$i][7]);
			$data_values[$i]['h'] = trim($xls->sheets[$sheets]['cells'][$i][8]);
			$data_values[$i]['i'] = trim($xls->sheets[$sheets]['cells'][$i][9]);
		}
		if($this->userid == 4 || $this->userid == 115  || $this->userid == 1){
			include template('member','update_file_temp');
		}
	}
	public function send_mmhg($high = 120,$low = 110,$bpm = 66,$openid = ''){
		//$openid = 'ogHyPv_HLl5elJeUuSdMKLZbEPBk';
		$state_str = '正常';
		if($high >= 140 || $low >= 90){
			$state_str = '偏高';
		}else if($high <= 90 || $low <= 60){
			$state_str = '偏低';
		}
		$data = array(
			'first'=>array('value'=>urlencode(date('Y-m-d H:i:s',time())),'color'=>"#743A3A"),
			'keyword1'=>array('value'=>urlencode($high." mmhg"),'color'=>'#F89406'),
			'keyword2'=>array('value'=>urlencode($low.' mmhg'),'color'=>'#F89406'),
			'keyword3'=>array('value'=>urlencode($bpm.' bpm'),'color'=>'#0088CC'),
			'keyword4'=>array('value'=>urlencode($state_str),'color'=>'#62C462'),
			'remark'=>array('value'=>urlencode('想了解更多数据，请点击进入医家讯进行查阅!'),'color'=>'#CCCCCC'),
		);
		if($state_str != '正常'){
			$this->weixin_send->doSend($openid,'DgmVbv9eKlJWKlYZqfvM-oLEYj-PMF0kwUgRSBqhezE','http://www.yjxun.cn',$data);
		}
	}
	public function delivery_task(){
	    $this->Quan->delivery_task();
    }
	public function get_ssssss(){
		$appid = 'wxe11d0c58a357011c';
		$appSecret = 'b73a78944d0cdab287b50c7ed1da1b06';
		$access_token_arr = wx_get_token($appid,$appSecret);//echo time()-$access_token_arr['time'];
		//pc_base::load_app_func('global','weixin','Quan');
		$timestamp = time().'';
		$wxnonceStr = 'Quan';
		$jsapi_ticket = wx_get_jsapi_ticket($appid,$appSecret);
		//$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
		$url = urldecode($_POST['url']);
		$wxOri = sprintf("jsapi_ticket=%s&noncestr=%s&timestamp=%s&url=%s",$jsapi_ticket,$wxnonceStr,$timestamp,$url);
		$wxSha1 = sha1($wxOri);
		echo json_encode(array('wxSha1'=>$wxSha1,'wxnonceStr'=>$wxnonceStr,'timestamp'=>$timestamp,'userid_'=>sys_auth($this->userid,'ENCODE'),'jsapi_ticket'=>$jsapi_ticket));
	}
	public function set_ss_encode(){
		//同步商城用户数据
		try {
			pc_base::load_app_class('userInterface','member',0);
			$userInterface = new userInterface();
			$this->default_db->load('member');
			$userinfo = $this->default_db->get_one(array('userid'=>$this->userid),'phpssouid,username,mobile');
			$mobile = !$userinfo['mobile']&&preg_match("/^1[34578]{1}\d{9}$/",$userinfo['username'])?$userinfo['username']:$userinfo['mobile'];
			$this->default_db->load('member_detail');
			$userModel = $this->default_db->get_one(array('userid'=>$this->userid),'portrait');
			$head = '//www.yjxun.cn'.$userModel['portrait'];
			$result = $userInterface->synMemberData(array('ssouid'=>$userinfo['phpssouid'],'mobile'=>$mobile,'head'=>$head));
			//var_dump($result);
		} catch (Exception $e) {
		}
		$userid_ = $_GET['userid'];
		if(!$userid_ && $_GET['userid_'] != 'null'){$userid_ = intval(sys_auth($_GET['userid_'], 'DECODE'));}
		if(intval(sys_auth($_GET['userid'], 'DECODE'))) $userid_ = intval(sys_auth($_GET['userid'], 'DECODE'));
		$datas = array('top_img'=>array( 
			1=>'https://www.yjxun.cn/index6/images/banner_pic1__.jpg',
			2=>'https://www.yjxun.cn/index6/images/banner_pic1_.jpg', 
		));
//		$datas['l1_'] = 'http://www.yjxun.cn/statics/member/images/9.png';
//		$datas['l2_'] = 'http://www.yjxun.cn/statics/member/images/9.png';
//		$datas['l3_'] = 'http://www.yjxun.cn/statics/member/images/9.png';
//		$datas['l4_'] = 'http://www.yjxun.cn/statics/member/images/9.png';
		$datas['advertisement'] = 'https://www.yjxun.cn/index/statics/images/700205.jpg';
		$datas['data'] = $userid_;
		$datas['msg_count'] = 0;
		$datas['msg_id'] = '';
		if($userid_){
			$this->default_db->load('message');
			$count_ = $this->default_db->count(array('to_userid'=>$userid_,'mss_type'=>1,'is_look'=>0));
			$all_arr = $this->default_db->select(array('to_userid'=>0),'to_userid,messageid');
			foreach($all_arr as $v){
				$m_id .= ','.$v['messageid'];
			}
			$m_id = trim($m_id,',');
			$this->default_db->load('message_see_record');
			$where = '`userid`='.$userid_.' AND `messageid` in ('.$m_id.')';
			$user_arr = $this->default_db->select($where);
			foreach($user_arr as $v){
				$msg_id_ .= ','.$v['messageid'];
			}
			$msg_id_ = trim($msg_id_,',');
			$this->default_db->load('message');
			if($msg_id_){
				$where = '`to_userid` = '.$userid_.' AND `is_look` = 0 AND `messageid` not in ('.$msg_id_.')';
				$datas['msg_count'] = $count_ + $this->default_db->count($where);
			}else{
				$datas['msg_count'] = $count_;
			}
			$where2 = '`to_userid` = '.$userid_.' AND `is_look` = 0 AND `mss_type` = 2 AND `del_userid` not like ("%,'.$userid_.',%")';
			$datas['hea_count'] = $this->default_db->count($where2);
			$this->default_db->load('kith_and_kin');
			$kda_1 = $this->default_db->select(array('kakuid'=>$userid_),'kakuid,fuid');
			$kda_2 = $this->default_db->select(array('fuid'=>$userid_),'kakuid,fuid');
			$kuids = array();
			foreach($kda_1 as $v){
				$kuids[] = $v['fuid'];
			}
			foreach($kda_2 as $v){
				$kuids[] = $v['kakuid'];
			}
			$this->default_db->load('kith_and_kin');
			$kda_1 = $this->default_db->select(array('kakuid'=>$userid_,'fhstate'=>1),'kakuid,fuid,fhstate,funick');
			$kda_2 = $this->default_db->select(array('fuid'=>$userid_,'kakhstate'=>1),'kakuid,fuid,kakhstate,kakunick');
			$datas['kcount'] = count($kda_1) + count($kda_2);
			foreach($kda_1 as $v) $datas['kcountD'][$v['fuid']] = $v['funick'];
			foreach($kda_2 as $v) $datas['kcountD'][$v['kakuid']] = $v['kakunick'];
			$temp_da = $this->get_msg_count_fun($userid_);
			$datas['msg_count_temp'] = $temp_da['count'];
		}
		/*$datas['activity'][] = array(
			'status'=>1,
			'title'=>'母亲节特惠测试活动，即将开始~',
			'url'=>'https://shop.yjxun.cn',
			'iconimg'=>'https://www.yjxun.cn/app/pc_down/images/qrcode_280.png',
		);*/
		if($_GET['index_top'] == 1){
			$this->default_db->load('medical_mmhg');
			$da = $this->default_db->get_one('`userid` = '.$userid_.' AND `update_time` > '.strtotime(date('Y-m-d 00:00:00')));
			$healda = array_reverse(json_decode($da['data_json'],true))[0];
			$datas['healda']['high'] = $healda['high']?$healda['high']:0;
			$datas['healda']['low'] = $healda['low']?$healda['low']:0;
			$datas['healda']['bpm'] = $healda['bpm']?$healda['bpm']:0;
			$this->default_db->load('sport_walk');
			$walkDa = $this->default_db->get_one('`userid` = '.$userid_.' AND `last_time` > '.strtotime(date('Y-m-d 00:00:00')));
			$datas['walkData'] = $walkDa['today_walk']?$walkDa['today_walk']:0;
			echo $_GET['jsoncallback']."(".json_encode($datas).")";
			exit();
		}
		if($this->userid) echo sys_auth($this->userid,'ENCODE');
	}
	function del_messages(){
		$msgid = $_POST['ids']?trim($_POST['ids'],','):'';
		if(!empty($msgid)){
			$msgid_arr = explode(',',$msgid);
			$this->default_db->load('message');
			$this->default_db->delete(array('messageid'=>array('in',$msgid_arr)));
		}
	}
	function get_msg_count_fun($uid = 0){
		if(!$uid || $uid == 0) $uid = $this->userid;
		$this->default_db->load('message');
		for($i=1;$i<8;$i++){
			if($i == 2 || $i == 3 || $i == 6 || $i == 7) continue;
			$type[$i] = $this->default_db->count(array('to_userid'=>$uid,'mss_type'=>$i,'is_look'=>0));
			$type_arr[$i] = $type[$i]?$type[$i]:0;
		}
		$all_arr = $this->default_db->select(array('to_userid'=>0),'to_userid,messageid');
		foreach($all_arr as $v){
			$m_id .= ','.$v['messageid'];
		}
		$m_id = trim($m_id,',');
		$this->default_db->load('message_see_record');
		$where = '`userid`='.$uid.' AND `messageid` in ('.$m_id.')';
		$user_arr = $this->default_db->select($where);
		foreach($user_arr as $v){
			$msg_id_ .= ','.$v['messageid'];
		}
		$msg_id_ = trim($msg_id_,',');
		if($msg_id_){
			$this->default_db->load('message');
			$where = '`mss_type` = 1 AND `to_userid` = '.$uid.' AND `is_look` = 0 AND `messageid` not in ('.$msg_id_.')';
			$user_count = $this->default_db->select($where);
			$type[1] = count($user_count);
		}
		for($i=1;$i<=7;$i++){
			$count += (int)$type[$i];
		}
		return $datas = array('type_data'=>$type,'temp_da'=>$user_count,'count'=>$count);
	}
	public function fun_mprice(){
		$this->default_db->load('doctor_user');
		if($_POST['type'] == 1){
			$da = $this->default_db->get_one('`userid` = '.$this->userid,'id,userid,price2');
			echo json_encode($da);
		}else{
			$new_price = intval($_POST['data']);
			$this->default_db->update('`price2` = '.$new_price,'`userid` = '.$this->userid);
		}
	}
    public function fun_mprice_one(){
		$user_id = $_POST['uid']?intval($_POST['uid']):0;
		$type = $_POST['type']?intval($_POST['type']):$_POST['type'];
		$price = $_POST['da']?intval($_POST['da']):0;
		if(!$user_id) return;
		$this->default_db->load('doc_server_set');
		if($type == 1){
			$this->default_db->load('doctor_user');
			$da_ = $this->default_db->get_one('`userid` = '.$this->userid,'id,userid,price2');
			$to_price = $da_['price2'];
			echo json_encode(array('da'=>intval($to_price)));
		}else if($type == 2){
			$this->default_db->update('`new_money` = '.$price,'`doctor_id` = '.$this->userid.' AND `user_id` = '.$user_id);
		}
	}
	public function resetImg(){
		if (isset($_POST['dosubmit']) && $_POST['dosubmit']) {
			$img = $_POST['img'];
			if (!$img) exit('{"status":0,"erro":"图片不存在"}');
			$status = 0;
			if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $img, $result)) {
				$type = $result[2];
				//var_dump($result);die;
				$documentRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
				$filePath = "/upload_cache/member/avatar_".$this->userid.".{$type}";
				$newFile = $documentRoot.DIRECTORY_SEPARATOR.$filePath;
				$img = base64_decode(str_replace($result[1], '', $img));
				if (file_put_contents($newFile, $img)){
					$status = 1;
					//业务逻辑 v9自带头像上传业务兼容
					$filePath = urlencode($filePath);
				}
			}
			$jsonData = array('status'=>$status,"erro"=>$status?'上传成功':'上传失败','data'=>array('headimgurl'=>$filePath));
			exit(json_encode($jsonData));
		}
	}
	public function temp_fun(){
		//var_dump(sys_auth('3435','ENCODE'));
	}
	public function checkNickname(){ 
		if($_GET['useridAuth'] && intval(sys_auth($_GET['useridAuth'],'DECODE'))) $this->userid = intval(sys_auth($_GET['useridAuth'],'DECODE'));
		$this->default_db->load('member');
		$da = $this->default_db->get_one('`userid` = '.$this->userid);
		if ($da) {
			if($da['nickname'] == '' || $da['nickname'] == ' ') exit('{"status":"-1"}');
		} else {//用户删除
			$sessionName = session_name();
			if (isset($_COOKIE[$sessionName])) {
				//var_dump($sessionName);die;
				setcookie($sessionName,'',time() - 3600,'/');
			}
			session_destroy();
			param::set_cookie('auth','');
			param::set_cookie('__userid','');
		}
		exit('{"status":"1"}');
	}
}
?>