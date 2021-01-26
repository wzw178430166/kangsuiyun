<?php 
pc_base::load_app_class('Quan','member',0);
class da_fun{
	public function __construct(){
		$this->default_db = pc_base::load_model('default_model');
		$this->content_db = pc_base::load_model('content_model');
		$this->userid = param::get_cookie('_userid');
		$this->plat = $_GET['plat']?$_GET['plat']:'';
		$this->page = $_GET['page']?intval($_GET['page']):1;
		$this->pageSize = $_GET['pageSize']?intval($_GET['pageSize']):10;
		$this->Quan = new Quan();
	}
	public function get_hmanage_da(){
		$touid = $_POST['touid']?intval($_POST['touid']):0;
		$type = intval($_POST['type']);
		$year = date('Y');
		$month = intval(date('m'));
		$lmonth = $month - 1;
		$day = date('d');
		$time = time();
		$sts = $time - (7 * 86400);
		$stt = $time - (30 * 86400);
		$this->default_db->load('member');
		$uda = $this->default_db->get_one('`userid` = '.$touid,'userid,nickname');
		$datas['uda']['nickname'] = $uda['nickname'];
		if($touid && $type == 1){
			//查询统计用户血压数据
			$this->default_db->load('medical_mmhg');
			$da1 = $this->default_db->get_one('`year` = '.$year.' AND `month` = '.$month.' AND `userid` = '.$touid);
			$da2 = $this->default_db->get_one('`year` = '.$year.' AND `month` = '.$lmonth.' AND `userid` = '.$touid);
			$dda1 = json_decode($da1['data_json'],true);
			$dda2 = json_decode($da2['data_json'],true);
			$data = array_reverse(array_merge($dda2,$dda1));
			$datas['today']['high'] = $data[0]['high'];
			$datas['today']['low'] = $data[0]['low'];
			$datas['today']['t'] = date('Y-m-d',$data[0]['add_time']);
			$lt = $data[0]['add_date'];
			foreach($data as $v){
				if($v['add_date'] == $lt){
					$datas['on']['tc']++;
					if($v['high'] >= 140 || $v['low'] >= 90){
						$datas['on']['hc']++;
						$datas['on']['ac']++;
					}
					if($v['high'] <= 90 || $v['low'] <= 60){
						$datas['on']['lc']++;
						$datas['on']['ac']++;
					}
				}
				if($v['add_time'] >= $sts && $v['add_time'] <= $time){
					$datas['tw']['tc']++;
					if($v['high'] >= 140 || $v['low'] >= 90){
						$datas['tw']['hc']++;
						$datas['tw']['ac']++;
					}
					if($v['high'] <= 90 || $v['low'] <= 60){
						$datas['tw']['lc']++;
						$datas['tw']['ac']++;
					}
				}
				if($v['add_time'] >= $stt && $v['add_time'] <= $time){
					$datas['th']['tc']++;
					if($v['high'] >= 140 || $v['low'] >= 90){
						$datas['th']['hc']++;
						$datas['th']['ac']++;
					}
					if($v['high'] <= 90 || $v['low'] <= 60){
						$datas['th']['lc']++;
						$datas['th']['ac']++;
					}
				}
			}
		}
		if($touid && $type == 2){
			//查询统计用户血糖数据
			$this->default_db->load('bioland_bg');
			$da1 = $this->default_db->get_one('`year` = '.$year.' AND `month` = '.$month);
			$da2 = $this->default_db->get_one('`year` = '.$year.' AND `month` = '.$lmonth);
			$dda1 = json_decode($da1['data_json'],true);
			$dda2 = json_decode($da2['data_json'],true);
			$data = array_reverse(array_merge($dda2,$dda1));
			$datas['today']['value'] = $data[0]['today_value_t'];
			$datas['today']['t'] = date('Y-m-d',$data[0]['add_time']);
			$lt = $data[0]['add_date'];
			$datas['on']['fc'] = $datas['on']['sc'] = $datas['tw']['fc'] = $datas['tw']['sc'] = $datas['th']['fc'] = $datas['th']['sc'] = 0;
			foreach($data as $v){
				if($v['add_date'] == $lt){
					if($datas['on']['sc'] == 0) $datas['on']['sc'] = $v['today_value_t'];
					$datas['on']['tc']++;
					if($datas['on']['fc'] < $v['today_value_t']) $datas['on']['fc'] = $v['today_value_t'];
					if($datas['on']['sc'] > $v['today_value_t']) $datas['on']['sc'] = $v['today_value_t'];
				}
				if($v['add_time'] >= $sts && $v['add_time'] <= $time){
					if($datas['tw']['sc'] == 0) $datas['tw']['sc'] = $v['today_value_t'];
					$datas['tw']['tc']++;
					if($datas['tw']['fc'] < $v['today_value_t']) $datas['tw']['fc'] = $v['today_value_t'];
					if($datas['tw']['sc'] > $v['today_value_t']) $datas['tw']['sc'] = $v['today_value_t'];
				}
				if($v['add_time'] >= $stt && $v['add_time'] <= $time){
					if($datas['th']['sc'] == 0) $datas['th']['sc'] = $v['today_value_t'];
					$datas['th']['tc']++;
					if($datas['th']['fc'] < $v['today_value_t']) $datas['th']['fc'] = $v['today_value_t'];
					if($datas['th']['sc'] > $v['today_value_t']) $datas['th']['sc'] = $v['today_value_t'];
				}
			}
		}
		echo json_encode($datas);
	}
	public function get_venue_list(){
		//获取医馆列表页
		$this->pageSize = 99;
		$lat = $_POST['lat']?$_POST['lat']:0;
		$lng = $_POST['lng']?$_POST['lng']:0;
		$lat_lng = $this->Convert_GCJ02_To_BD09($lat,$lng);
		$lng = $lat_lng['lng'];
		$lat = $lat_lng['lat'];
		//获取该点周围的4个点
		$distance = 50; //范围（单位千米）
		$EARTH_RADIUS = 6371; //地球半径，平均半径为6371km
		$dlng = rad2deg(2 * asin(sin($distance / (2 * $EARTH_RADIUS)) / cos(deg2rad($lat))));
		$dlat = rad2deg($distance / $EARTH_RADIUS);
		$squares = array('left-top'=>array('lat'=>$lat+$dlat,'lng'=>$lng-$dlng),'right-top'=>array('lat'=>$lat+$dlat,'lng'=>$lng+$dlng), 'left-bottom'=>array('lat'=>$lat-$dlat,'lng'=>$lng-$dlng),'right-bottom'=>array('lat'=>$lat-$dlat,'lng'=>$lng+$dlng));
		$info_sql = "`lat` > '{$squares['right-bottom']['lat']}' and `lat` <'{$squares['left-top']['lat']}' and `lng` >'{$squares['left-top']['lng']}' and `lng` < '{$squares['right-bottom']['lng']}'";
		$this->default_db->load('venue_content');
		$da = $this->default_db->listinfo($info_sql,'',$this->page,$this->pageSize,'','','','','id,title,thumb,address_str,address_top,lng,lat,telephone,show_span,business_str');
		foreach($da as $k=>$v){
			$ids[] = $v['id'];
			$distace = $this->getdistance($v['lng'],$v['lat'],$lng,$lat);//获取距离
			$distace = sprintf('%.2f',$distace / 1000);//单位:米 转换 成 千米/公里 四舍五入 保留两个小数点
			$v['distace'] = $distace;
			$v['show_span_'] = explode(',',$v['show_span']);
			$data[$v['id']] = $v;
		}
		$this->default_db->load('venue_content_data');
		$da_detail = $this->default_db->select('`id` in ('.implode(',',$ids).')','id,content');
		foreach($da_detail as $v) $data[$v['id']]['content'] = $v['content'];
		echo json_encode($this->arraySequence($data,'distace','SORT_ASC'));
	}
	public function get_top_video(){
		//获取中医问道页面顶部视频推荐位数据以及推荐中医
		$this->default_db->load('healthy_video');
		$da = $this->default_db->get_one('`listorder` = 1','id,title,thumb,url,is_zj,zj_name,zj_hospital,zj_ddetail,type,video_txt,view,good,collect,fdate,type,name_text,hthumb');
		$this->default_db->load('doctor_user');
		$dda = $this->default_db->get_one('`id` = 16','id,is_cm,utype,cm_price,comment,department,hospital,position_level,realname,thumb,user_num,userid,bespoke,cm_date');
		$dda['cm_date_'] = json_decode($dda['cm_date'],true);
		$this->default_db->load('doctor_user_data');
		$dda_detail = $this->default_db->get_one('`id` = 16','id,bga,content');
		$ddas = array_merge($dda,$dda_detail);
		echo json_encode(array($da,$ddas));
	}
	public function get_video_lists(){
		//获取健康视频列表
		$this->default_db->load('healthy_video');
		$da = $this->default_db->listinfo('','',$this->page,$this->pageSize,'','','','','id,title,thumb,url,is_zj,zj_name,zj_hospital,zj_ddetail,type,video_txt,view,good,collect,fdate,type,name_text,hthumb,zan,shoucang');
		foreach($da as $k=>$v){
			$v['pay_code'] = $this->Quan->getSubstr($v['url'],'/default/','/main.m3u8');
			$data[$k] = $v;
		}
		echo json_encode($data);
	}
	public function get_video_detail(){
		//获取视频相关列表
		$videoid = $_POST['videoid']?intval($_POST['videoid']):0;
		if($videoid){
			$this->default_db->load('healthy_video');
			$da = $this->default_db->listinfo('`id` <> '.$videoid,'',$this->page,$this->pageSize,'','','','','id,title,thumb,url,is_zj,zj_name,zj_hospital,zj_ddetail,type,video_txt,view,good,collect,fdate,type,name_text,hthumb');
			foreach($da as $k=>$v){
				$v['pay_code'] = $this->Quan->getSubstr($v['url'],'/default/','/main.m3u8');
				$data[$k] = $v;
			}
			$this->default_db->update('`view` = `view` + 1','`id` = '.$videoid);
			echo json_encode($data);
		}
	}
	public function get_cm_fy_list(){
		//获取预约咨询列表数据
		$history = intval($_GET['history']);
		$where = ' AND `end_time` > '.time();
		if($history == 1) $where = ' AND `end_time` < '.time();
		$this->default_db->load('visit_wait_cm_fy');
		$da = $this->default_db->listinfo('`fruid` = '.$this->userid.$where,'end_time DESC',$this->page,$this->pageSize,'','','','','id,fruid,touid,start_time,end_time,fr_unread,last_text,state,stype');
		foreach($da as $v){
			$touid[] = $v['touid'];
			$vid[] = $v['id'];
		}
		$this->default_db->load('doctor_user');
		$duser = $this->default_db->select('`userid` in ('.implode(',',$touid).')','id,userid,thumb,realname');
		foreach($duser as $v) $duser_[$v['userid']] = $v;
		echo json_encode(array($da,$duser_));
	}
	public function get_user_sim(){
		$this->default_db->load('hardware_data');
		$da = $this->default_db->select('`userid` = '.$this->userid,'id,userid,hardwareid,sn');
		echo json_encode($da);
	}
	public function renew_user_sim(){
		$this->default_db->load('sim_renew');
		if($_GET['type'] == 2){
			$this->default_db->update('`sim_text` = "'.htmlspecialchars($_POST['simText']).'"','id = '.intval($_POST['newID']));
		}else{
			$sn = $_POST['sn'];
			$hardwareid = intval($_POST['hardwareid']);
			$da = $this->default_db->insert(array('userid'=>$this->userid,'sn'=>$sn,'hardwareid'=>$hardwareid,'add_time'=>time()),true);
			echo $da;
		}
	}
	public function get_aopeneds(){
		$this->default_db->load('doc_server_set');
		$da = $this->default_db->select('`user_id` = '.$this->userid.' AND `server_time` > '.time(),'id,user_id,doctor_id,server_time');
		foreach($da as $v){
			$dda[$v['doctor_id']] = $v['doctor_id'];
			$data[] = $v;
		}
		$this->default_db->load('doctor_user');
		$dda_list = $this->default_db->select('`userid` in ('.implode(',',$dda).')','userid,realname,price2');
		foreach($dda_list as $v) $dda[$v['userid']] = $v;
		foreach($data as $v){
			$v['price'] = $dda[$v['doctor_id']]['price2'];
			$v['realname'] = $dda[$v['doctor_id']]['realname'];
			$v['server_time'] = date('Y-m-d',$v['server_time']);
			$datas[] = $v;
		}
		$this->default_db->load('service_healthy');
		$hda = $this->default_db->select('`userid` = '.$this->userid.' AND `deadline` > '.time(),'id,userid,doctorid,deadline,type');
		foreach($hda as $k=>$v) $hda[$k]['dead_line_'] = date('Y-m-d',$v['deadline']); 
		echo json_encode(array($datas,$hda));
	}
	public function get_healthy_da(){
		$history = intval($_GET['history']);
		$where = ' AND `deadline` > '.time();
		if($history == 1) $where = ' AND `deadline` < '.time();
		$this->default_db->load('service_healthy');
		$da = $this->default_db->select('`userid` = '.$this->userid.$where,'id,type,userid,doctorid,deadline,uunread');
		foreach($da as $v){
			$did[] = $v['doctorid'];
			$v['deadline_'] = date('Y-m-d',$v['deadline']);
			//$data[$v['doctorid']] = $v;
			$data[$v['doctorid']]['datas'][$v['type']] = $v;
		}
		$this->default_db->load('doc_server_set');
		$vda = $this->default_db->select('`user_id` = '.$this->userid.' AND `doctor_id` in ('.implode(',',$did).')','id,doctor_id,vid');
		$this->default_db->load('doctor_user');
		$dda = $this->default_db->select('`userid` in ('.implode(',',$did).')','id,userid,thumb,realname,is_cm_fy,cm_date,cm_price,hospital,position_level,price2');
		foreach($dda as $v) $ddaid[] = $v['id'];
		$this->default_db->load('doctor_user_data');
		$dda_data = $this->default_db->select('`id` in ('.implode(',',$ddaid).')','id,bga,content');
		foreach($dda_data as $v) $dda_data_[$v['id']] = $v;
		foreach($vda as $v) $data[$v['doctor_id']]['vid'] = $v['vid'];
		foreach($dda as $v){
			$data[$v['userid']]['hospital'] = $v['hospital'];
			$data[$v['userid']]['position_level'] = $v['position_level'];
			$data[$v['userid']]['price2'] = $v['price2'];
			$data[$v['userid']]['thumb'] = $v['thumb'];
			$data[$v['userid']]['realname'] = $v['realname'];
			$data[$v['userid']]['is_cm_fy'] = $v['is_cm_fy'];
			$data[$v['userid']]['cm_date'] = $v['cm_date'];
			$data[$v['userid']]['cm_price'] = $v['cm_price'];
			$data[$v['userid']]['bga'] = $dda_data_[$v['id']]['bga'];
			$data[$v['userid']]['content'] = $dda_data_[$v['id']]['content'];
		}
		$this->default_db->load('healthy_order');
		$da = $this->default_db->select('`userid` = '.$this->userid.' AND `status` = "waitAssign"');
		foreach($da as $v){
			$da2[$v['type']]['name'] = $v['type'] == 'bp'?'血压管理服务':'血糖管理服务';
			$da2[$v['type']]['num'] += $v['deadline'];
		}
		
		echo json_encode(array($data,$da2));
	}
	public function getHealthyStatus(){
		$this->default_db->load('healthy_order');
		$da = $this->default_db->select('`userid` = '.$this->userid.' AND `status` = "waitAssign"');
		foreach($da as $v) $da2[$v['type']]['num'] += $v['deadline'];
		echo json_encode($da2);
	}
	public function get_help_data(){
		//获取帮助中心文章内容 硬件检测|1账号相关|2加盟合作|3购买相关|4平台服务|5服务条款|6其他问题|7
		$where = 1;
		$this->content_db->set_model(66);
		$help = $this->content_db->listinfo($where,'yes desc,no desc',1,5);
		empty($help)&&$help=array();
		$status = 0;
		if ($help) {
			$status = 1;
		}
		$json_data = array(
			'status'=>$status,
			'data'=>array('help'=>$help),
		);
		exit(json_encode($json_data));
	}
	public function get_help_list(){
		$where = 1;
		if (isset($_GET['type'])&&$_GET['type']) {
			$type = intval($_GET['type']);
			$where .= ' AND `type`='.$type;
		}
		$this->content_db->set_model(66);
		$help = $this->content_db->listinfo($where,'inputtime desc',$this->page,20);
		empty($help)&&$help=array();
		$status = 0;
		if ($help) {
			$status = 1;
		}
		$json_data = array(
			'status'=>$status,
			'data'=>array('help'=>$help),
		);
		exit(json_encode($json_data));
	}
	public function get_help_detail(){
		$hid = $_POST['hid']?intval($_POST['hid']):0;
		$this->default_db->load('help_center_data');
		$da = $this->default_db->get_one('`id` = '.$hid,'id,content');
		$this->default_db->load('help_center');
		$total = $this->default_db->count();
		$rand = range(1,$total);
		shuffle($rand);
		for($i=0;$i<5;$i++) $s[] = $rand[$i];
		if($s) $relevant = $this->default_db->select(array('id'=>array('in',$s)),'id,title');
		echo json_encode(array($da,$relevant));
	}
	public function update_isok(){
		$hid = $_POST['hid']?intval($_POST['hid']):0;
		$t = intval($_POST['t']);
		if($t == 1) $update = '`yes` = `yes` + 1';
		if($t == 2) $update = '`no` = `no` + 1';
		$this->default_db->load('help_center');
		$this->default_db->update($update,'`id` = '.$hid);
	}
	
	public function getOrder(){
		$tables = array(1001=>'service_order',1002=>'healthy_order',1003=>'order',1004=>'service_order',1005=>'service_order');
		$gameid = isset($_GET['gameid'])&&$_GET['gameid']?intval($_GET['gameid']):1001;
		$userid = param::get_cookie('_userid');
		$where = '`userid`='.$userid;
		if ($gameid != 1002) $where .= ' AND `gameid`='.$gameid;
		$this->default_db->load($tables[$gameid]);
		$order = $this->default_db->listinfo($where,'addtime desc',$this->page,20);
		//var_dump($tables[$gameid]);die;
		empty($order[0])&&$order=array();
		$doctor = array();
		$status = 0;
		if ($order){
			$status = 1;
			$doctorids = array();
			foreach($order as $k=>$r){
				switch($gameid){
					case 1001:
					case 1004:
					case 1005:
						if (!in_array($r['pro_id'],$doctorids)) $doctorids[] = $r['pro_id'];
						break;
					case 1002:
						if ($r['doctorid']> 0 && !in_array($r['doctorid'],$doctorids)) $doctorids[] = $r['doctorid'];
						break;
				}
				$r['addtime'] = date('Y-m-d H:i:s',$r['addtime']);
				$order[$k] = $r;
			}
			if (!empty($doctorids)) {
				$doctorids = implode(',',$doctorids);
				$where = '`userid` in ('.$doctorids.')';
				$this->content_db->set_model(52);
				$_doctor = $this->content_db->select($where,'userid,realname,thumb');
				if ($_doctor) {
					foreach($_doctor as $r){
						$doctor[$r['userid']] = $r;
					}
				}
			}
		}
		$json_data = array(
			'status'=>$status,
			'data'=>array('order'=>$order,'doctor'=>$doctor),
		);
		exit(json_encode($json_data));
	}
	
	public function getLocation(){
		$ip = $this->getip();
		//$ip = '202.175.50.66';//澳门ip
		//$ip = '59.125.119.188';//台湾ip
		$api = "http://ip.taobao.com/service/getIpInfo.php?ip=$ip";
		$location = @file_get_contents($api);
		$location = json_decode($location,true);
		$status = 0;
		$type = 0;
		if ($location && $location['code'] == 0) {
			$status = 1;
			//$location['data']['country']
			switch($location['data']['country_id']){
				case 'CN'://中国大陆
					$type = 1;
					break;
				default://海外或港澳台地区
					$type = 2;
					break;
			}
		}
		$json_data = array(
			'status'=>$status,
			'data'=>array('location'=>$location,'type'=>$type),
		);
		exit(json_encode($json_data));
	}
	public function getip(){
		if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) {
			$ip = getenv("HTTP_CLIENT_IP");
		} else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) {
			$ip = getenv("HTTP_X_FORWARDED_FOR");
		} else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) {
			$ip = getenv("REMOTE_ADDR");
		} else if (isset ($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) {
			$ip = $_SERVER['REMOTE_ADDR'];
		} else {
			$ip = "unknown";
		}
		return $ip;
	}
	
	public function Convert_GCJ02_To_BD09($lat,$lng){
        $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
        $x = $lng;
        $y = $lat;
        $z =sqrt($x * $x + $y * $y) + 0.00002 * sin($y * $x_pi);
        $theta = atan2($y, $x) + 0.000003 * cos($x * $x_pi);
        $lng = $z * cos($theta) + 0.0065;
        $lat = $z * sin($theta) + 0.006;
        return array('lng'=>$lng,'lat'=>$lat);
	}
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
	public function saveDatum(){
		$this->default_db->load('fb_datum');
		$insert = array(
			'name'=>remove_xss(safe_replace($_GET['name'])),
			'tel'=>remove_xss(safe_replace($_GET['tel'])),
			'email'=>remove_xss(safe_replace($_GET['email'])),
			'company'=>remove_xss(safe_replace($_GET['company'])),
			'content'=>remove_xss(safe_replace($_GET['content'])),
			'f'=>$_GET['f']?intval($_GET['f']):0
		);
		$this->default_db->insert($insert);
		$this->Quan->Customer_service('唐剑科技-康穗云','康穗云官网通知',5);
		try{
			$typeid = intval($_GET['typeid']);
			$introduce = $insert['content']?$insert['content']:$insert['company'];
			$info = array('siteid'=>get_siteid(),'typeid'=>$typeid,'name'=>$insert['name'],'lxqq'=>$insert['tel'],'email'=>$insert['email'],'shouji'=>$insert['tel'],'introduce'=>$introduce,'addtime'=>time());
			$this->default_db->load('guestbook');
			$this->default_db->insert($info);
		} catch( Exception $e){
			
		}
		exit($_GET['jsoncallback']."(".json_encode(array()).")");
	}
	public function exchangeRecharge(){
		if($_POST['code'] == '123456'){
			$this->default_db->load('member');
			$this->default_db->update('`balance` = `balance` + 50','`userid` = '.$this->userid);
			$da = $this->default_db->get_one('`userid` = '.$this->userid,'userid,balance');
			exit('{"status":"1","amount":"'.$da['balance'].'"}');
		}else{
			exit('{"status":"-1"}');
		}
	}
	public function getOrderList(){
		$this->default_db->load('inquiry_order');
		$da = $this->default_db->select('`userid` = '.$this->userid);
		exit(json_encode($da));
	}
	public function update_system_steps(){
		$data = array(
			'userid'=>$_POST['userid'],
			'stepdate'=>$_POST['stepdate'],
			'steps'=>$_POST['steps'],
			'calories'=>$_POST['calories'],
			'distance'=>$_POST['distance']
		);
		$this->default_db->load('system_steps');
		$da = $this->default_db->get_one('`userid` = '.$data['userid'].' AND `stepdate` = '.date('Ymd'));
		if($da){
			$this->default_db->update($data,'`id` = '.$da['id']);
		}else{
			$this->default_db->insert($data);
		}
	}
	public function getDXYdata(){
		$url = 'https://server.toolbon.com/home/tools/getPneumonia';
		$data = file_get_contents($url);
		$data = json_decode($data,true)['data']['listData'];
		$updateTime = $data[0]['createTime'] / 1000;
		$add_time = date('Y-m-d',time());
		$this->default_db->load('ecsn_data');
		$isDa = $this->default_db->get_one('`province` = "'.$data[0]['provinceName'].'" AND `add_time` = "'.$add_time.'"')?1:0;
		foreach($data as $v){
			$toData = array(
				'province'=>$v['provinceName'],
				'tags'=>$v['tags'],
				'city_name'=>$v['cityName'],
				'current_confirmed_count'=>$v['currentConfirmedCount'],
				'confirmed_count'=>$v['confirmedCount'],
				'suspected_count'=>$v['suspectedCount'],
				'cured_count'=>$v['curedCount'],
				'dead_count'=>$v['deadCount'],
				'sort'=>$v['sort'],
				'update_time'=>$updateTime,
				'add_time'=>$add_time,
				'ddate'=>date('Y-m-d',time())
			);
			if(!$isDa){
				$this->default_db->insert($toData);
			}else{
				unset($toData['add_time']);
				unset($toData['province']);
				$this->default_db->update($toData,'`province` = "'.$v['provinceName'].'" AND `add_time` = '.$add_time);
			}
		}
		echo '数据更新完成!';
		//echo '<pre>';var_dump(json_decode($data,true)['data']['listData']);echo '</pre>';
		//echo '<pre>';var_dump(json_decode($data,true)['data']['areaList']);echo '</pre>';
	}
	public function getECSNdata(){
		$add_time = date('Y-m-d',time());
		$this->default_db->load('ecsn_data');
		$da = $this->default_db->select('`ddate` = "'.$add_time.'"','*','','confirmed_count DESC');
		if(!$da) $da = $this->default_db->select('`ddate` = "'.date('Y-m-d',time()-86400).'"','*','','confirmed_count DESC');
		$countDa['current_confirmed_count'] = 0;
		$countDa['confirmed_count'] = 0;
		$countDa['suspected_count'] = 0;
		$countDa['cured_count'] = 0;
		$countDa['dead_count'] = 0;
		foreach($da as $v){
			$countDa['current_confirmed_count'] += $v['current_confirmed_count'];
			$countDa['confirmed_count'] += $v['confirmed_count'];
			$countDa['suspected_count'] += $v['suspected_count'];
			$countDa['cured_count'] += $v['cured_count'];
			$countDa['dead_count'] += $v['dead_count'];
			if(strpos($v['province'],'内蒙古') !== false || strpos($v['province'],'黑龙江') !== false){
				$v['province'] = mb_substr($v['province'],0,3);
			}else{
				$v['province'] = mb_substr($v['province'],0,2);
			}
			$da1[] = $v['province'];
			$da2[] = $v['confirmed_count'];
		}
		echo json_encode(array(array_reverse($da1),array_reverse($da2),$countDa));
		//echo '<pre>';var_dump($da);echo '</pre>';
	}
	public function getDXYdata2(){
		$url = 'https://server.toolbon.com/home/tools/getPneumonia';
		$data = file_get_contents($url);
		$data = json_decode($data,true)['data']['areaList'];
		$add_time = date('Y-m-d',time());
		$this->default_db->load('ecsn_data2');
		$isDa = $this->default_db->get_one('`add_time` = "'.$add_time.'"')?1:0;
		foreach($data as $v){
			$toData = array(
				'update_time'=>time(),
			);
			foreach($v['cities'] as $j){
				$toData['city_name'] = $j['cityName'];
				$toData['current_confirmed_count'] = $j['currentConfirmedCount'];
				$toData['confirmed_count'] = $j['confirmedCount'];
				$toData['suspected_count'] = $j['suspectedCount'];
				$toData['cured_count'] = $j['curedCount'];
				$toData['dead_count'] = $j['deadCount'];
				$this->default_db->update($toData,'`city_name` = "'.$j['cityName'].'"');
			}
		}
		echo '数据更新完成!';
		//echo '<pre>';var_dump(json_decode($data,true)['data']['listData']);echo '</pre>';
		//echo '<pre>';var_dump(json_decode($data,true)['data']['areaList']);echo '</pre>';
	}
	public function getECSNdata2(){
		$this->default_db->load('ecsn_data2');
		$da = $this->default_db->select('','*','','confirmed_count DESC');
		$data = array(
			"unit"=>"人",
			"eleValue"=>"provinceName,cityName,x,y,confirmedCount,suspectedCount,curedCount,deadCount",
			"dateTime"=>date('Ymd',time())
		);
		foreach($da as $k=>$v){
			$data['list'][$k]['provinceName'] = $v['province'];
			$data['list'][$k]['cityName'] = $v['city_name'];
			$data['list'][$k]['x'] = $v['x'];
			$data['list'][$k]['y'] = $v['y'];
			$data['list'][$k]['confirmedCount'] = $v['confirmed_count'];
			$data['list'][$k]['suspectedCount'] = $v['suspected_count'];
			$data['list'][$k]['curedCount'] = $v['cured_count'];
			$data['list'][$k]['deadCount'] = $v['dead_count'];
		}
		echo json_encode($data);
		//echo '<pre>';var_dump($da);echo '</pre>';
	}
	private function getBdAcctoken(){
        $a = getcache('baidu','baidu');
        if($a['updateTime'] > time()){
            return $a;
        }else{
            $url = 'https://aip.baidubce.com/oauth/2.0/token';
            $post_data['grant_type']       = 'client_credentials';
            $post_data['client_id']      = 'iyr6XApB6Sy7amvB3FYszdF0';
            $post_data['client_secret'] = 'qxB4M6Bc3j3vB8ALtGDmBGrtH2e2Guhy';
            $o = "";
            foreach($post_data as $k=>$v) $o .= "$k=".urlencode($v)."&" ;
            $post_data = substr($o,0,-1);
            $res = _curl_post($url,$post_data);
            $data = json_decode($res,true);
            $data['updateTime'] = time() + 86400 * 29;
            setcache('baidu',$data,'baidu');
            return $data;
        }
    }
    public function lookImgCard(){
	    if(!$_GET['nid']){
            $type = $_GET['type']?$_GET['type']:1;
            $token = $this->getBdAcctoken()['access_token'];
            $url = 'https://aip.baidubce.com/rest/2.0/ocr/v1/idcard?access_token='.$token;
            $img = $_POST['imgStr']?$_POST['imgStr']:'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';
            if($type == 2){
                $url = 'https://aip.baidubce.com/rest/2.0/ocr/v1/webimage?access_token='.$token;
                $img = $_POST['imgStr']?$_POST['imgStr']:'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';
            }$bodys = array(
                'id_card_side'=>"front",
                'image'=>$img
            );
            $header = array('Content-Type'=>'application/x-www-form-urlencoded');
            $res = $this->http_post($url,$header,$bodys);
            $data = json_decode($res,true);
            $this->default_db->load('apidata');
            $nid = $this->default_db->insert(array('data'=>$res,'addtime'=>date('Y-m-d H:i:s')),true);
            $data['nid'] = $nid;
            echo json_encode($data);
        }else{
            $this->default_db->load('apidata');
            $data = $this->default_db->get_one(array('id'=>intval($_GET['nid'])));
            echo json_encode($data);
        }
    }
    private function http_post($sUrl, $aHeader, $aData){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $sUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($aData));
        $sResult = curl_exec($ch);
        if($sError=curl_error($ch)) die($sError);
        curl_close($ch);
        return $sResult;
    }
}
?>