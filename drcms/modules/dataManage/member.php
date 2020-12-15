<?php
defined('IN_drcms') or exit('No permission resources.');
require drcms_PATH.'api/GatewayClient-master/Gateway.php';
pc_base::load_sys_class('form', '', 0);
pc_base::load_app_class('authority','dataManage',0);
pc_base::load_app_class('privManage','dataManage',0);
use GatewayClient\Gateway;
class member {
	public $default_db,$content_db,$member_db,$authority;
	public $authData = array();
	public $ajax = 0;
	public $page = 1;
	public $pageSize = 20;
	public $template = 'dataManage';
	public function __construct() {

		$this->authority = new authority();

		$this->authData = $this->authority->authData;

		$this->default_db = pc_base::load_model('default_model');

		$this->content_db = pc_base::load_model('content_model');

		$this->member_db = pc_base::load_model('member_model');

		$this->ajax = intval($_GET['ajax']);

		$this->page = isset($_GET['page'])&&intval($_GET['page'])?$_GET['page']:1;

		$this->encrypt = 161301;

		$this->operatePassword = password('tjYijiaxun',$this->encrypt);
		Gateway::$registerAddress = pc_base::load_config('system','webSocketIp').':1238';
		
		if ($this->ajax) {
			pc_base::load_app_class('demo','core',0);
			$demo = new demo();
		}
	}

	

	public function init() {

		$this->memberCount();

	}

	

	public function memberCount(){

		if ($this->ajax) {

			$count = array(

				'yest'=>array('memberCount'=>0,'yestNew'=>0),

				'today'=>array('todayNew'=>0,'todayHardware'=>0,'todayAbnormal'=>0),

				'active'=>array('day'=>0,'month'=>0),

			);

			$countData = array(

				'abnormal'=>array(

					'type'=>array('心率','血压','血糖'),

					'data'=>array(0,0,0)

				),

				'area'=>array(

					'count'=>array(),

					'cdoordinate'=>array(),

					'maxCount'=>array(),

				),

				'sex'=>array('male'=>0,'female'=>0,'other'=>0),

				

			);

			$status = 1;

			$where = 1;

			$count['yest']['memberCount'] = $this->member_db->count($where);

		

			$et = date('Y-m-d H:i:s',time());

			$st = date('Y-m-d 00:00:00',strtotime("$et -1 day"));

			$where = '`regdate` >= '.strtotime($st).' AND `regdate`<='.strtotime($et);

			$member = $this->member_db->select($where,'userid,regdate');

			if ($member) {

				foreach($member as $r){

					$day = date('d',$r['regdate']);

					if ($day == date('d')) {

						$count['today']['todayNew']++;

					} else {

						$count['yest']['yestNew']++;

					}

				}

			}

			//活跃统计

			$st = date('Y-m-01 00:00:00',time());

			$et = date('Y-m-d 23:59:59',strtotime('+1 month -1 day',strtotime($st)));

			$where = '`visit_time`>'.strtotime($st).' AND `visit_time`<'.strtotime($et);

			$this->default_db->load('visit_detail');

			$active = $this->default_db->select($where,'userid,visit_time');

			//var_dump($where);die;

			if ($active) {

				foreach($active as $r){

					if (date('d',$r['visit_time']) == date('d')) {

						$count['active']['day']++;

					}

					$count['active']['month']++;

				}	

			}

			$where = 1;

			$this->member_db->set_model(28);

			$location = $this->member_db->select($where);

			if ($location) {

				$areaData = array();

				$coordinateData = array();

				$maxCount = array();

				$maxArea = 0;

				foreach($location as $r){

					if (!in_array($r['city'],$coordinateData[$r['city']])) $coordinateData[$r['city']] = array($r['lng'],$r['lat']);

					if (!in_array($r['city'],$areaData[$r['city']])) {

						$areaData[$r['city']] = array('name'=>$r['city'],'value'=>1);

					} else {

						$areaData[$r['city']]['value'] += 1;

					}

					if ($maxArea < $areaData[$r['city']]['value']) {

						$maxArea = $areaData[$r['city']]['value'];

						$maxCount = array($r['lng'],$r['lat']);

					}

				}

				$areaData = array_values($areaData);

				$countData['area']['count'] = $areaData;

				$countData['area']['cdoordinate'] = $coordinateData;

				$countData['area']['maxCount'] = $maxCount;

			}

			

			//硬件使用统计 异常统计

			//$where = '`update_time`>'.time().' AND `update_time`<'.time();

			$currentSt = strtotime(date('Y-m-d 00:00:00',time()));

			$currentEt = strtotime(date('Y-m-d 23:59:59',time()));

			$dayUseMember = $dayAbnormalMember = $abnormalMember1 = $abnormalMember2 = $$abnormalMember3 = array();

			$where = 1;

			$this->default_db->load('medical_bpm');

			$bpm = $this->default_db->select($where,'id,userid,data_json,update_time');

			if ($bpm) {

				foreach($bpm as $r){

					if (!in_array($r['userid'],$dayUseMember)

					&&$r['update_time']>$currentSt

					&&$r['update_time']<$currentEt) {

						$dayUseMember[] = $r['userid'];

					}

					$jsonData = json_decode($r['data_json'],true);

					foreach($jsonData as $rr){

						$value = $rr['num'];

						if ($value < 60 || $value > 100) {

							if (!in_array($r['userid'],$dayAbnormalMember)

							&&$rr['add_time']>$currentSt

							&&$rr['add_time']<$currentEt) {

								$dayAbnormalMember[] = $r['userid'];

							}

							if (!in_array($r['userid'],$abnormalMember1)) $abnormalMember1[] = $r['userid'];

						}

					}

				}

				$countData['abnormal']['data'][0] = count($abnormalMember1);

			}

			

			$this->default_db->load('medical_mmhg');

			$mmhg = $this->default_db->select($where,'id,userid,data_json,update_time');

			if ($mmhg) {

				foreach($mmhg as $r){

					if (!in_array($r['userid'],$dayUseMember)

					&&$r['update_time']>$currentSt

					&&$r['update_time']<$currentEt) {

						$dayUseMember[] = $r['userid'];

					}

					$jsonData = json_decode($r['data_json'],true);

					foreach($jsonData as $rr){

						if (intval($rr['high']) >= 140 

						|| intval($rr['low']) >= 90 

						|| intval($rr['high']) <= 90 

						|| intval($rr['low']) <= 60) {

							if (!in_array($r['userid'],$dayAbnormalMember)

							&&$rr['add_time']>$currentSt

							&&$rr['add_time']<$currentEt) {

								$dayAbnormalMember[] = $r['userid'];

							}

							if (!in_array($r['userid'],$abnormalMember2)) $abnormalMember2[] = $r['userid'];

						}

					}

				}

				$countData['abnormal']['data'][1] = count($abnormalMember2);

			}

			

			$this->default_db->load('bioland_bg');

			$bg = $this->default_db->select($where,'id,userid,data_json,update_time');

			if ($bg) {

				foreach($bg as $r){

					if (!in_array($r['userid'],$dayUseMember)

					&&$r['update_time']>$currentSt

					&&$r['update_time']<$currentEt) {

						$dayUseMember[] = $r['userid'];

					}

					$jsonData = json_decode($r['data_json'],true);

					foreach($jsonData as $rr){

						$value = floatval($rr['today_value_t']);

						if ($value < 3.9 || $value > 6.1) {

							if (!in_array($r['userid'],$dayAbnormalMember)

							&&$rr['add_time']>$currentSt

							&&$rr['add_time']<$currentEt) {

								$dayAbnormalMember[] = $r['userid'];

							}

							if (!in_array($r['userid'],$abnormalMember3)) $abnormalMember3[] = $r['userid'];

						}

					}

				}

				$countData['abnormal']['data'][2] = count($abnormalMember3);

			}

			//var_dump($dayAbnormalMember);die;

			$count['today']['todayHardware'] = count($dayUseMember);

			$count['today']['todayAbnormal'] = count($dayAbnormalMember);

			

			$where = 1;

			$this->member_db->set_model(10);

			$sex = $this->member_db->select($where,'sex');

			if ($sex) {

				foreach($sex as $r){

					if ($r['sex'] == 1) {

						$countData['sex']['male']++;

					} if ($r['sex'] == 2) {

						$countData['sex']['female']++;

					} else {

						$countData['sex']['other']++;

					}

				}

			}

			$jsonData  = array(

				'status'=>$status,

				'data'=>array('count'=>$count,'countData'=>$countData),

			);

			exit(json_encode($jsonData));

		}

		include template('dataManage','memberCount');

	}

	public function memberList(){
		if ($this->ajax) {
			$where = '`roleid` = 13';//角色编号13 普通用户
			
			
			if (isset($_GET['hl']) || isset($_GET['addr']) || isset($_GET['system']) || isset($_GET['sex']) || isset($_GET['doctor']) || isset($_GET['healthy']) || isset($_GET['hardware'])) {
				$where2 = $where3 = 1;
				$uids = $uids2 = array();
				$health_label = $_GET['hl'];
				$addr = $_GET['addr'];
				$system = intval($_GET['system']);
				$sex = intval($_GET['sex']);
				$doctor = intval($_GET['doctor']);
				$healthy = intval($_GET['healthy']);
				$hardware = intval($_GET['hardware']);
				$isData = 0;
				if ($hardware) {
					//$this->default_db->load('device_bind');
					$this->default_db->load('hardware_data');
					$hardware2 = $this->default_db->select('`userid`>0','userid');
					//var_dump($hardware2);die;
					if ($hardware2) {
						foreach($hardware2 as $r){
							if (!in_array($r['userid'],$uids)) {
								if ($hardware==1) {
									$uids[] = $r['userid'];
								} else {
									$uids2[] = $r['userid'];
								}
							}
						}
						unset($hardware2);
					}
				}

				if ($doctor) {
					$this->default_db->load('doc_server_set');
					$doctor2 = $this->default_db->select(1,'user_id');
					if ($doctor2) {
						foreach($doctor2 as $r){
							if (!in_array($r['user_id'],$uids)) {
								if ($doctor==1) {
									$uids[] = $r['user_id'];
								} else {
									$uids2[] = $r['user_id'];
								}
							}
						}
						unset($doctor2);
					}
				}
				if ($healthy) {
					if (!empty($uids)) {
						$uids = implode(',',$uids);
						$where3 .= ' AND `userid` in ('.$uids.')';
						$uids = array();
					}
					$this->default_db->load('service_healthy');
					$healthy2 = $this->default_db->select($where3,'userid');
					if ($healthy2) {
						foreach($healthy2 as $r){
							if (!in_array($r['userid'],$uids)) {
								if ($healthy==1) {
									$uids[] = $r['userid'];
								} else {
									$uids2[] = $r['userid'];
								}
							}
						}
						unset($healthy2);
					}
				}


				if ($system) {
					$isData = 1;
					$where1 .= ' AND `for_system` = '.$system;
				}
				if ($sex) {
					$isData = 1;
					$where1 .= ' AND `sex` = '.$sex;
				}

				if ($isData == 1) {
					$where1 = $isData.$where1;
					if (!empty($uids)) {
						$uids = implode(',',$uids);
						$where1 .= ' AND `userid` in ('.$uids.')';
						$uids = array();
					}
					$this->member_db->set_model(10);
					$model = $this->member_db->select($where1,'userid');
					if ($model) {
						foreach($model as $r){
							if (!in_array($r['userid'],$uids)) $uids[] = $r['userid'];
						}
					} 
				}
				if ($health_label) {
					$health_label = explode(',',$health_label);
					foreach($health_label as $r){
						switch(intval($r)){
							case 1:
							case 2:
								if ($r==1) {
									$where2 .= ' OR (`type`="bp" AND (`value2`>=140 OR `value3`>=90))';
								} else if ($r==2) {
									$where2 .= ' OR (`type`="bp" AND (`value2`<=90 OR `value3`<=60))';
								}
								break;
							case 3:
							case 4:
								$where2 .= ' OR (`type`="bs" AND `value` '.($r==3?'>':'<').' '.($r==3?109.8:70.2).')';
								break;
							case 5:
							case 6:
								$where2 .= ' OR (`type`="hr" AND `value` '.($r==5?'>':'<').' '.($r==5?100:60).')';
								break;
						}
					}
					$where2 = substr($where2,5);
					if (count($health_label) > 1) {
						$where2 = '('.$where2.')';
					} else {
						//$where2 = '1 AND '.$where2;
					}
					if (!empty($uids)) {
						$uids = implode(',',$uids);
						$where2 .= ' AND `userid` in ('.$uids.')';
						$uids = array();
					}
					$this->default_db->load('users_abnormal_data');
					$abnormal = $this->default_db->select($where2,'userid');

					foreach($abnormal as $r){
						if (!in_array($r['userid'],$uids)) $uids[] = $r['userid'];
					}
				}
				if ($addr) {
					$where2 = '`province` like "%'.$addr.'%" OR `city` like "%'.$addr.'%" OR `district` like "%'.$addr.'%"';
					if (!empty($uids)) {
						$uids = implode(',',$uids);
						$where2 .= ' AND `userid` in ('.$uids.')';
						$uids = array();
					}
					$this->default_db->load('member_rtp');
					$rtp = $this->default_db->select($where2,'userid');
					//var_dump($rtp);die;
					foreach($rtp as $r){
						if (!in_array($r['userid'],$uids)) $uids[] = $r['userid'];
					}
				}
				
				if (!empty($uids)) {
					$uids = implode(',',$uids);
					$where .= ' AND `userid` in ('.$uids.')';
				}

				if (!empty($uids2)) {
					$uids2 = implode(',',$uids2);
					$where .= ' AND `userid` not in ('.$uids2.')';
				}

				if (empty($uids)&&empty($uids2)) {
					exit('{"status":"0"}');
				}
				unset($doctor,$healthy,$rtp,$model,$where1,$where2,$uids,$uids2);
			}

			if (isset($_GET['queryType'])&&$_GET['queryType']) {
				$queryUids = $this->getQueryTypeUids($_GET['queryType']);
				if ($queryUids) {
					$where .= ' AND `userid` in ('.implode(',',$queryUids).')';
				} else {
					exit('{"status":"0"}');
				}
			}
			//var_dump($where);die;
			if (isset($_GET['k'])&&$_GET['k']) {
				$keyword = $_GET['k'];
				$where .= ' AND (`username` = "'.$keyword.'" OR `nickname` like "%'.$keyword.'%")';
			}
			if (isset($_GET['st'])&&isset($_GET['et'])) {
				$st = date('Y-m-d 00:00:00',strtotime($_GET['st']));
				$et = date('Y-m-d 23:59:59',strtotime($_GET['et']));
				$where .= ' AND `regdate` >='.strtotime($st).' AND `regdate`<='.strtotime($et);
			}
			$this->pageSize = 15;
			$this->member_db->set_model();
			$_member = $this->member_db->listinfo($where,'regdate DESC',$this->page,$this->pageSize,'','','','','userid,username,nickname,mobile,regdate,lastdate,user_type,remarks,categoryid');
			empty($_member[0])&&$_member=array();
			$member = $region = $hardware = $service = $category = array();
			$status = 0;
			$pageCount = 0;
			if ($_member) {
				$status = 1;
				$pageCount = $this->member_db->number;
				$uids = $categoryids = array();
				foreach($_member as $k=>$r){
					$uids[] = $r['userid'];
					if ($r['categoryid']&&!in_array($r['categoryid'],$categoryids)) $categoryids[] = $r['categoryid'];
					$r['regdate'] = date('Y-m-d H:i:s',$r['regdate']);
					$r['lastdate'] = date('Y-m-d H:i:s',$r['lastdate']);
					
					$accountType = '';
					switch($r['user_type']){
						case 1:
							break;
						case 2:
							$accountType = '内部账号';
							break;
						case 3:
							$accountType = '其他账号';
							break;
					}
					$r['accountType'] = $accountType;
					$r['onlineState'] = -1;
					$member[$r['userid']] = $r;
				}
				if ($uids) {
					$where = array('userid'=>array('in',$uids));
					$this->member_db->set_model(10);
					$model = $this->member_db->select($where,'userid,realname,sex,age,idcard,province,city,area,street,community,address,lng,lat,device_text,addr');
					if ($model) {
						$codes = array();
						foreach($model as $r){
							if ($r['province']&&!in_array($r['province'],$codes)) $codes[] = $r['province'];
							if ($r['city']&&!in_array($r['city'],$codes)) $codes[] = $r['city'];
							if ($r['area']&&!in_array($r['area'],$codes)) $codes[] = $r['area'];
							if ($r['street']&&!in_array($r['street'],$codes)) $codes[] = $r['street'];
							if ($r['community']&&!in_array($r['community'],$codes)) $codes[] = $r['community'];
							
							if (is_array($r)&&$member[$r['userid']]) {
								$member[$r['userid']] = array_merge($member[$r['userid']],$r);
							}
							//手环绑定情况
							if ($r['device_text']&&(strpos($r['device_text'],'x9pro') !== false) || strpos($r['device_text'],'v3') !== false) {
								if(!in_array($r['userid'],$hardware)) $hardware[] = $r['userid'];
							}
						}
						if ($codes) {
							$codes = implode('","',$codes);
							$where2 = '`code` in ('.$codes.')';
							$this->default_db->load('area');
							$_region = $this->default_db->select($where2,'code,name');
							if ($_region) {
								foreach($_region as $r){
									$region[$r['code']] = $r;
								}
							}
						}
					}
					
					//用户定位信息
					$this->default_db->load('member_rtp');
					$_rtp = $this->default_db->select($where,'userid,province,city,district,street,addr,lng,lat,updatetime');
					if ($_rtp) {
						foreach($_rtp as $r){
							$r['time'] = date('Y-m-d H:i:s',$r['updatetime']);
							$rtp[$r['userid']] = $r;
						}
					}
					
					//设备绑定情况(仅有血压计、血糖仪)
					$this->default_db->load('hardware_data');
					$_hardware = $this->default_db->select($where,'id,userid');
					if ($_hardware) {
						foreach($_hardware as $r){
							if(!in_array($r['userid'],$hardware)) $hardware[] = $r['userid'];
						}
					}
								
					//在线时间
					$onlineSockid = array();
					try {
						//$error = 'Always throw this error';
						//throw new Exception($error);
						$onlineClient = Gateway::getAllClientInfo();
						if ($onlineClient) {
							$onlineSockid = array_keys($onlineClient);
						}
					} catch (Exception $e) {
						//echo 'Caught exception: ',  $e->getMessage(), "\n";
					}
					$this->default_db->load('online_users');
					$online = $this->default_db->select($where,'userid,last_socket_id,last_time');
					if ($online) {
						foreach($online as $r){
							if ($member[$r['userid']]) {
								$state = '';
								$offline = '';
								if (in_array($r['last_socket_id'],$onlineSockid)) {
									$state = 1;
								} else {
									$state = 0;
									$offset = time() - $r['last_time'];
									if ($offset > 365*86400) {
										$remainder = $offset%(365*86400);
										$offLine = floor($offset/(365*86400)) . '年'.(0<$remainder?floor($remainder/(30*86400)).'月':'');
									} elseif ($offset > 30*86400) {
										$offLine = floor($offset/(30*86400)).'月';
									} elseif ($offset > 86400) {
										$offLine = floor($offset/86400).'天';
									} elseif ($offset > 3600) {
										$offLine = floor($offset/3600).'小时';
									} else {
										$offLine = floor($offset/60).'分钟';
									}
									$offLine = '已离线'.$offLine;
								}
								$member[$r['userid']]['onlineState'] = $state;
								$member[$r['userid']]['offline'] = $offLine;
								$member[$r['userid']]['lastonline'] = date('Y-m-d H:i:s',$r['last_time']);
							}
						}
					}
					
					//服务
					$this->default_db->load('service_healthy');
					$_service = $this->default_db->select($where,'id,userid');
					foreach($_service as $r){
						if(!in_array($r['userid'],$service)) $service[] = $r['userid'];
					}
					$this->default_db->load('doc_server_set');
					$_service = $this->default_db->select($where,'id,user_id');
					foreach($_service as $r){
						if(!in_array($r['user_id'],$service)) $service[] = $r['user_id'];
					}
				}
				if ($categoryids) {
					$where = array('id',array('in',$categoryids));
					$this->default_db->load('member_category');
					$_category = $this->default_db->select($where,'id,name');
					foreach($_category as $r){
						$category[$r['id']] = $r;
					}
				}
				
				unset($_member,$_model);
				$member = array_values($member);
			}

			//
			$jsonData  = array(
				'status'=>$status,
				'data'=>array('member'=>$member,'rtp'=>$rtp,'region'=>$region,'hardware'=>$hardware,'service'=>$service,'category'=>$category,'pageCount'=>$pageCount),
				
			);
			exit(json_encode($jsonData));
		}
		include template('dataManage','memberList');
	}

	public function getQueryTypeUids($type){
		$result = array();
		if ($type) {
			$st = date('Y-m-d 00:00:00',time());
			$et = date('Y-m-d H:i:s',time());
			switch($type){
				case 'active':
					$where = '`last_time`>'.strtotime($st).' AND `last_time`<'.strtotime($et);
					//var_dump($where);die;
					$this->default_db->load('online_users');
					$datas = $this->default_db->select($where,'userid');
					//var_dump($datas);die;
					break;
				case 'online':
					$onlineSockid = array();
					try {
						//$error = 'Always throw this error';
						//throw new Exception($error);
						$onlineClient = Gateway::getAllClientInfo();
						//var_dump($onlineClient);die;
						if ($onlineClient) {
							$onlineSockid = array_keys($onlineClient);
						}
					} catch (Exception $e) {
						//echo 'Caught exception: ',  $e->getMessage(), "\n";
					}
					$this->default_db->load('online_users');
					if ($onlineSockid) {
						$where = array('last_socket_id'=>array('in',$onlineSockid));
						$datas = $this->default_db->select($where,'userid');
					}
					break;
				case 'hardware':
					$where = '`updatetime`>'.strtotime($st).' AND `updatetime`<'.strtotime($et);
					$this->default_db->load('users_use_device');
					$datas = $this->default_db->select($where,'userid');
					break;
				case 'abnormal':
					$where = '`createtime`>'.strtotime($st).' AND `createtime`<'.strtotime($et);
					$this->default_db->load('alarm');
					$datas = $this->default_db->select($where,'userid');
					break;
				case 'consult':
					$where = '`addtime`>='.strtotime($st).' AND `addtime`<='.strtotime($et);
					$this->default_db->load('csc_chatdata');
					$datas = $this->default_db->select($where,'userid');
					break;
				case 'guest':
					$where = '`addtime`>='.strtotime($st).' AND `addtime`<='.strtotime($et);
					$this->default_db->load('guestbook');
					$guestbook = $this->default_db->select($where,'name','','addtime desc');
					$usernames = array();
					foreach($guestbook as $r){
						if (!in_array($r['name'],$usernames)) $usernames[] = $r['name'];
					}
					$where = array('roleid'=>13,'username'=>array('in',$usernames));
					$datas = $this->member_db->select($where,'userid');
					break;
			}
			foreach($datas as $r){
				$result[] = $r['userid'];
			}
		}
		return $result;
	}
	

	public function doMember(){
		if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {
			$userid = intval($_POST['userid']);
			$username = $_POST['username'];
			$region = $_POST['region'];
			$model = $_POST['info'];
			$model['province'] = $region['province'];
			$model['city'] = $region['city'];
			$model['area'] = $region['area'];
			$model['street'] = $region['street'];
			$model['community'] = $region['community'];
			if (0 < $userid) {
				$where = '`userid` <> '.$userid.' AND `username`="'.$username.'"';
				$member = $this->member_db->get_one($where,'userid');
				if ($member) exit('{"status":0,"erro":"账号已存在"}');
				$info = '`username` = "'.$username.'"';
				$where = '`userid` = '.$userid;
				$member = $this->member_db->get_one($where,'userid,phpssouid');
				$status = $this->member_db->update($info,$where);
				if ($status) {
					//sso
					$this->default_db->load('sso_members');
					$this->default_db->update($info,'`uid`='.$member['phpssouid']);
					//用户信息
					$this->member_db->set_model(10);
					$this->member_db->update($model,$where);
				}
			} else {
				$datas = array('roleid'=>13,'sectorid'=>1,'username'=>$username,'password'=>'000000','nickname'=>$model['realname'],'mobile'=>$username,'state'=>1,'model'=>$model);
				pc_base::load_app_class('user','dataManage',0);
				$user = new user();
				$result = $user->createAccount($datas);
				$status = $result['status'];
			}
			exit('{"status":'.($status?1:0).',"erro":"'.($status?'保存成功':'保存失败').'"}');
		} else {
			if ($this->ajax) {
				$userid = intval($_GET['uid']);
				$member = array();
				if (0 < $userid) {
					$where = '`userid` = '.$userid;
					$member = $this->member_db->get_one($where);
					$this->member_db->set_model(10);
					$model = $this->member_db->get_one($where,'realname,sex,age,idcard,province,city,area,street,community,address,lng,lat');
					if (is_array($model)) $member = array_merge($model,$member);
				}
				$status = $member?1:0;
				$jsonData  = array(
					'status'=>$status,
					'data'=>array('member'=>$member),
				);
				exit(json_encode($jsonData));
			}
			include template('dataManage','doMember');
		}
	}
	public function doMember2(){
		if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {
			$files = $_FILES['file'];
			//var_dump($_POST);die;
			$imagesExt = array('jpg','png','jpeg','gif','mp4','xls');
			$path = drcms_PATH."upload/excel";
			$status = 0;
			$erro = '';
			if (@$files['error'] == 00) {
				// 判断文件类型
				$ext = strtolower(pathinfo(@$files['name'],PATHINFO_EXTENSION));
				if (!in_array($ext,$imagesExt)){
					$erro = "非法文件类型";
				}
				// 判断是否存在上传到的目录
				if (!is_dir($path)){
					mkdir($path,0777,true);
				}
				// 生成唯一的文件名
				if ($_fileName) {
					$fileName = $_fileName.'.'.$ext;
				} else {
					$fileName = md5(uniqid(microtime(true),true)).'.'.$ext;
				}
				// 将文件名拼接到指定的目录下
				$destName = $path."/".$fileName;
				//var_dump($destName);die;
				// 进行文件移动
				if (move_uploaded_file($files['tmp_name'],$destName)){
					$status = 1;
				} else {
					$erro = "文件上传失败";
				}
			} else {
				// 根据错误号返回提示信息
				switch (@$files['error']) {
					case 1:
						$erro = "上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值";
						break;
					case 2:
						$erro = "上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值";
						break;
					case 3:
						$erro = "文件只有部分被上传";
						break;
					case 4:
						$erro = "没有文件被上传";
						break;
					case 6:
					case 7:
						$erro = "系统错误";
						break;
				}
			}
			if (0==$status) exit('{"status":'.$status.',"erro":"'.$erro.'"}');
			//chmod($file,0777);
			//$file = realpath($file);
			$file = $destName;
			pc_base::load_sys_class('reader','libs'.DIRECTORY_SEPARATOR.'ExcelReader',0);
			$xls = new SpreadsheetExcelReader();//Spreadsheet_Excel_Reader || SpreadsheetExcelReader 
			$xls->setOutputEncoding('utf-8');  //设置编码 
			$xls->read($file);  //解析文件
			//var_dump($xls->sheets[0]['cells'][2]);die;
			$sheets = 0;//默认第一个
			$cellsHead = 1;
			$cellsFirst = 2; //行数默认以1递增
			$rowCount = $xls->sheets[$sheets]['numRows'];
			$thead = $xls->sheets[$sheets]['cells'][1];
			//var_dump($thead);die;
			$values = '';
			//$rowCount = 30000;
			for($i=$cellsFirst;$i<=$rowCount;$i++){
				//var_dump($i);
				foreach($thead as $k=>$r){
					switch($r){
						case '姓名':
						case '客户姓名':
							$nickname = $xls->sheets[$sheets]['cells'][$i][$k];
							break;
						case '城市':
							$addr = '广东省'.$xls->sheets[$sheets]['cells'][$i][$k];
							break;
						case '地址':
						case '装机地址':
							$address = $xls->sheets[$sheets]['cells'][$i][$k];
							break;
						case '电话':
						case '手机号码':
							$username = $xls->sheets[$sheets]['cells'][$i][$k];
							break;
					}
				}
				/*$where = '`username`="'.$username.'"';
				$member = $this->member_db->get_one($where);
				if ($member) continue;
				$groupid2 = '';
				if (isset($_POST['groupid2'])&&$_POST['groupid2']) {
					$groupid2 = ','.intval($_POST['groupid2']).',';
				}
				
				$model = array('addr'=>$addr,'address'=>$address);
				$datas = array('roleid'=>13,'sectorid'=>1,'groupid2'=>$groupid2,'state'=>0,'username'=>$username,'password'=>'000000','nickname'=>$nickname,'mobile'=>$username,'model'=>$model);
				//var_dump($datas);die;
				pc_base::load_app_class('user','dataManage',0);
				$user = new user();
				$result = $user->createAccount($datas);
				//var_dump($result);
				$status = $result['status'];*/
				/*$info = array('username'=>$username,'mobile'=>$username,'nickname'=>$nickname,'addr'=>$addr,'address'=>$address);
				$this->default_db->load('member_test');
				$this->default_db->insert($info);*/
				$values .= '("'.$nickname.'","'.$addr.'","'.$address.'","'.$username.'","'.$username.'",'.$_POST['groupid2'].'),';
				//var_dump($values);die;
			}
			$values = substr($values,0,-1);
			//var_dump($values);die;
			$sql = 'INSERT INTO `drcms_member_test` (`nickname`,`addr`,`address`,`username`,`mobile`,`groupid`) VALUES '.$values;
			$this->default_db->query($sql);
			$file = realpath($file);
			//unlink($file);//删除数据文件
			exit('{"status":'.($status?1:0).',"erro":"'.($status?'保存成功':'保存失败').'"}');
		} else {
			if ($this->ajax) {
				$where = 1;
				$this->default_db->load('auth_member_group');
				$group = $this->default_db->select($where,'id,name','','addtime desc');
				$status = $group?1:0;
				$jsonData = array(
					'status'=>$status,
					'data'=>array('group'=>$group),
				);
				exit(json_encode($jsonData));
			}
			include template('dataManage','doMember2');
		}
	}
	
	public function doMemberPass(){
		if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {
			$uid = intval($_POST['id']);
			$datas = array('uids'=>array($uid),'password'=>'000000');
			pc_base::load_app_class('user','dataManage',0);
			$user = new user();
			$result = $user->resetPassword($datas);
			$status = $result['status'];
			exit('{"status":'.($status?1:0).',"erro":"'.($status?'重置成功':'重置失败').'"}');
		}
	}
	
	public function importMember(){
		if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {
			
		} else {
			include template('dataManage','importMember');
		}
	}
	
	public function examineMember(){
		if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {
			
		} else {
			include template('dataManage','examineMember');
		}
	}	

	public function delMember(){

		$password = trim($_POST['password']);

		if ('' == $password) exit('{"status":-3,"erro":"输入密码"}');

		$password = password($password,$this->encrypt);

		if ($password != $this->operatePassword) {

			exit('{"status":-3,"erro":"操作密码有误"}');

		}

		$uids = $_POST['uid'];

		$uids = trim($uids,',');

		$where = '`userid` in ('.$uids.')';

		$member = $this->member_db->select($where,'userid,phpssouid');

		if (empty($member)) exit('{"status":0,"erro":"用户不存在"}');

		$phpssouids = array();

		foreach($member as $r){

			$phpssouids[] = $r['phpssouid'];

		}

		$status = 0;

		if (!empty($phpssouids)) {

			$this->_init_phpsso();

			$state = $this->client->ps_delete_member($phpssouids, 1);

			if (0 < $state) {

				$status = 1;

			}

		}

		$state = $this->member_db->delete($where);

		if ($state) {

			$status = 1;

			$this->member_db->set_model(10);

			$this->member_db->delete($where);
			
			//用户模型2.0数据
			$where2 = '`user_id` in ('.$uids.')';
			$this->default_db->load('member_information');
			$this->default_db->delete($where2);
			var_dump($this->default_db->error());die;

		} else {

			$status = 0;

		}

		exit('{"status":'.$status.',"erro":"'.($status?'删除成功':'删除失败').'"}');

	}

	

	/**

	 * 初始化phpsso

	 * about phpsso, include client and client configure

	 * @return string phpsso_api_url phpsso地址

	 */

	private function _init_phpsso() {

		pc_base::load_app_class('client', 'member', 0);

		define('APPID', pc_base::load_config('system', 'phpsso_appid'));

		$phpsso_api_url = pc_base::load_config('system', 'phpsso_api_url');

		$phpsso_auth_key = pc_base::load_config('system', 'phpsso_auth_key');

		$this->client = new client($phpsso_api_url, $phpsso_auth_key);

		return $phpsso_api_url;

	}

	

	public function getMemberHardwareHave($uids){

		$data = array();

		if ($uids == '') return $data;

		$where = '`userid` in ('.$uids.')';

		$this->default_db->load('users_use_device');

		$hardware = $this->default_db->select($where);

		if ($hardware) {

			foreach($hardware as $r){

				$r['updatetime'] = date('Y-m-d H:i:s',$r['updatetime']);

				$r['x9'] = $r['x9']?date('Y-m-d H:i:s',$r['x9']):'';

				$r['v3'] = $r['v3']?date('Y-m-d H:i:s',$r['v3']):'';

				$r['a223'] = $r['bp_a223']?date('Y-m-d H:i:s',$r['bp_a223']):'';

				$r['777g'] = $r['g_777g']?date('Y-m-d H:i:s',$r['g_777g']):'';

				$data[$r['userid']] = $r;

			}

		}

		return $data;

	}

	

	public function memberDetail(){

		$userid = intval($_GET['uid']);

		$where = '`userid`='.$userid;

		$member = $this->member_db->get_one($where,'userid,username,nickname,email,regdate,lastdate,mobile,point,amount');

		$member['regdate'] = date('Y-m-d H:i:s', $member['regdate']);

		$member['lastdate'] = date('Y-m-d H:i:s', $member['lastdate']);

		extract($member);

		$this->member_db->set_model(10);

		$model = $this->member_db->get_one($where,'portrait,addr,age,birthday,sex,u_height,u_weight,health_label');

		extract($model);

		//var_dump($health_label);die;

		$type_arr = array(1=>'高血压',2=>'低血压',3=>'高血糖',4=>'低血糖',5=>'心率偏低',6=>'心率偏高',7=>'心率异常',8=>'体型偏胖',9=>'体型偏瘦',10=>'体型超胖',11=>'睡眠不足',12=>'未知');

		include template('dataManage','memberDetail');

	}

	

	public function memberPersonalHardWare(){

		if ($this->ajax) {

			$userid = intval($_GET['uid']);

			if (!$userid) exit('{"status":"0"}');

			$this->default_db->load('device_bind');

			$where = '`userid`='.$userid;

			//$where = 1;

			if (isset($_GET['k'])&&$_GET['k']) {

				$keyword = $_GET['k'];

				$where .= ' AND (`device_mac` = "'.$keyword.'" OR `device_IMSI` = "'.$keyword.'")';

			}

			if (isset($_GET['st'])&&isset($_GET['et'])) {

				$st = date('Y-m-d 00:00:00',strtotime($_GET['st']));

				$et = date('Y-m-d 23:59:59',strtotime($_GET['et']));

				$where .= ' AND `bindtime`>='.strtotime($st).' AND `bindtime`<='.strtotime($et);

			}

			$_bind = $this->default_db->listinfo($where,'bindtime DESC',$this->page, $this->pageSize);

			empty($_bind[0])&&$_bind=array();

			$pages = '';

			$status = 0;

			$bind = array();

			if ($_bind) {

				$status = 1;

				$pages = $this->default_db->pages;

				foreach($_bind as $r){

					$r['bindtime'] = date('Y-m-d H:i:s',$r['bindtime']);

					$bind[] = $r;

				}

				unset($_bind);

			}

			$jsonData = array(

				'data'=>array('bind'=>$bind,'pages'=>$pages),

				'status'=>$status,

			);

			exit(json_encode($jsonData));

		}

		include template('dataManage','memberPersonalHardWare');

	}

	

	public function memberPersonalHealth(){

		if ($this->ajax) {

			$status = 0;

			$uid = intval($_GET['uid']);

			if (0>=$uid) {

				exit('{"status":"0"}');

			}

			//$year = isset($_GET['year'])&&$_GET['year']?intval($_GET['year']):date('Y');

			//$month = isset($_GET['month'])&&$_GET['month']?intval($_GET['month']):date('m');

			$year = date('Y');

			$month = date('m');

			$type = isset($_GET['t'])?intval($_GET['t']):1;

			$feildYear = $type==7?'add_year':'year';

			$feildMonth = $type==7?'add_month':'month';

			$where = '`userid`='.$uid;

			if (isset($_GET['st'])&&isset($_GET['et'])) {

				$styear = date('Y',strtotime($_GET['st']));

				$stmonth = date('n',strtotime($_GET['st']));

				$etyear = date('Y',strtotime($_GET['et']));

				$etmonth = date('n',strtotime($_GET['et']));

				$where .= ' AND ((`'.$feildYear.'` = '.$styear.' AND `'.$feildMonth.'` = '.$stmonth.') OR (`'.$feildYear.'` = '.$etyear.' AND `'.$feildMonth.'` = '.$etmonth.'))';

				//var_dump($where);die;

			} else {

				$where .= ' AND `'.$feildYear.'`='.$year.' AND `'.$feildMonth.'`='.$month;

			}

			//var_dump($where);die;

			$datas = array();

			switch($type){

				case 1:

					$this->default_db->load('medical_bpm');

					$bpm = $this->default_db->select($where,'id,data_json');

					foreach($bpm as $v){

						$_datas = json_decode($v['data_json'],true);

						if ($_datas) {

							$status = 1;

							$kk=0;

							foreach($_datas as $r){

								$datas[$kk]['value'] = '心率 : '.$r['num'];

								$datas[$kk]['add_time_text'] = date('Y-m-d H:i:s',$r['add_time']);

								$datas[$kk]['device_name'] = $r['device_name'];

								$_status = '心率正常';

								if ($r['num'] > 100) {

									$_status = '心率偏高';

								} else if ($r['num'] < 60) {

									$_status = '心率偏低';

								}

								$datas[$kk]['status'] = $_status;

								$kk++;

							}

						}

					}

					

					break;

				case 2:

					$this->default_db->load('medical_mmhg');

					$mmhg = $this->default_db->select($where,'id,data_json');

					

					foreach($mmhg as $v){

						$_datas = json_decode($v['data_json'],true);

						if ($_datas) {

							$status = 1;

							$kk=0;

							foreach($_datas as $r){

								$datas[$kk]['value'] = '血压 : '.$r['high'].'/'.$r['low'].'(心率 : '.$r['bpm'].')';

								$datas[$kk]['add_time_text'] = date('Y-m-d H:i:s',$r['add_time']);

								$datas[$kk]['device_name'] = $r['device_name'];

								$_status = '血压正常';

								if ($r['high'] > 140 || $r['low'] > 90) {

									$_status = '血压偏高';

								} else if($r['high'] < 90 || $r['low'] <60) {

									$_status = '血压偏低';

								}

								$datas[$kk]['status'] = $_status;

								$kk++;

							}

						}

					}

					

					//var_dump($datas);die;

					break;

				case 3:

					$this->default_db->load('bioland_bg');

					$bg = $this->default_db->select($where,'id,data_json');

					foreach($bg as $v){

						$_datas = json_decode($v['data_json'],true);

						//var_dump($datas);die;

						if ($_datas) {

							$status = 1;

							$kk=0;

							foreach($_datas as $r){

								$datas[$kk]['value'] = '血糖 : '.$r['today_value_t'];

								$datas[$kk]['add_time_text'] = date('Y-m-d H:i:s',$r['add_time']);

								$datas[$kk]['device_name'] = $r['device_name'];

								$_status = '血糖正常';

								if ($r['today_value_t'] > 6.1) {

									$_status = '血糖偏高';

								} else if($r['today_value_t'] > 3.9) {

									$_status = '血糖偏低';

								}

								$_status .= '['.$r['info'].']';

								$datas[$kk]['status'] = $_status;

								$kk++;

							}

						}

					}

					

					break;

				case 4:

					$this->default_db->load('medical_spot');

					$spot = $this->default_db->select($where,'id,data_json');

					foreach($spot as $v){

						$_datas = json_decode($v['data_json'],true);

						//var_dump($datas);die;

						if ($_datas) {

							$status = 1;

							$kk=0;

							foreach($_datas as $r){

								$datas[$kk]['value'] = '血氧 : '.$r['num'].'%';

								$datas[$kk]['add_time_text'] = date('Y-m-d H:i:s',$r['add_time']);

								$datas[$kk]['device_name'] = $r['device_name'];

								$_status = '血氧正常';

								if ($r['num'] > 100) {

									$_status = '血氧偏高';

								} else if($r['num'] < 95) {

									$_status = '血氧偏低';

								}

								$datas[$kk]['status'] = $_status;

								$kk++;

							}

						}

					}

					

					break;

				case 5:

					$this->default_db->load('medical_temperature');

					$temperature = $this->default_db->select($where,'id,data_json');

					foreach($temperature as $v){

						$_datas = json_decode($v['data_json'],true);

						//var_dump($datas);die;

						if ($_datas) {

							$status = 1;

							$kk=0;

							foreach($_datas as $r){

								$datas[$kk]['value'] = '体温 : '.$r['tnum'].'℃';

								$datas[$kk]['add_time_text'] = date('Y-m-d H:i:s',$r['add_time']);

								$datas[$kk]['device_name'] = $r['device_name'];

								$_status = '体温正常';

								if ($r['tnum'] > 37.5) {

									$_status = '体温偏高';

								} else if($r['tnum'] > 36) {

									$_status = '体温偏低';

								}

								$datas[$kk]['status'] = $_status;

								$kk++;

							}

						}

					}

					

					break;

				case 6:

					//$where = '`year`=2017 AND `month`=12 AND `userid`=4';

					$this->default_db->load('medical_sleep');

					$sleep = $this->default_db->select($where,'id,data_json');

					foreach($sleep as $v){

						$_datas = json_decode($v['data_json'],true);

						//var_dump($datas);die;

						if ($_datas) {

							$status = 1;

							$kk=0;

							foreach($_datas as $r){

								if (floatval($r['sleep_deep'])<=0&&floatval($r['sleep_light'])<=0) continue;

								$countSleep = floatval($r['sleep_light']) + floatval($r['sleep_deep']);

								$countSleepHour = floor($countSleep/60);

								$countSleepMinutes = $countSleep%60;

								$deepHour = floor(floatval($r['sleep_deep'])/60);

								$deepMinutes = floatval($r['sleep_deep'])%60;

								$lightHour = floor(floatval($r['sleep_light'])/60);

								$lightMinutes = floatval($r['sleep_light'])%60;

								$datas[$kk]['value'] = '总时长 : '.$countSleepHour.'小时'.$countSleepMinutes.'分钟';

								$datas[$kk]['add_time_text'] = date('Y-m-d',$r['add_time']);

								$datas[$kk]['device_name'] = $r['device_name'];

								$_status = '深睡 : '.$deepHour.'小时'.$deepMinutes.'分钟</br>浅睡 : '.$lightHour.'小时'.$lightMinutes.'分钟';

								$datas[$kk]['status'] = $_status;

								$kk++;

							}

						}

					}

					

					break;

				case 7:

					$this->default_db->load('sport_walk');

					$walk = $this->default_db->select($where,'id,walk_json');

					foreach($walk as $v){

						$_datas = json_decode($v['walk_json'],true);

						//var_dump($datas);die;

						if ($_datas) {

							$status = 1;

							$kk=0;

							foreach($_datas as $r){

								$datas[$kk]['value'] = '步数 : '.$r['walk'];

								$datas[$kk]['add_time_text'] = date('Y-m-d H:i:s',$r['add_time']);

								$datas[$kk]['device_name'] = $r['device_name'];

								$distance = $r['distance']/1000 > 0?($r['distance']/1000).'公里':$r['distance'].'米';

								$_status = '消耗卡路里 : '.$r['calorie'].'卡, 步行距离 : '.$distance;

								$datas[$kk]['status'] = $_status;

								$kk++;

							}

						}

					}

					

					break;

			}

			$jsonData = array(

				'status'=>$status,

				'data'=>array('datas'=>$datas)

			);

			exit(json_encode($jsonData));

		}

		include template('dataManage','memberPersonalHealth');

	}

	

	public function memberPersonalMedical(){

		if ($this->ajax) {

			$userid = intval($_GET['uid']);

			if (0 >= $userid) exit('{"status":"0"}');

			$where = '`userid`='.$userid;

			if (isset($_GET['k'])&&$_GET['k']) {

				$keyword = $_GET['k'];

				$where .= ' AND (`name` like "%'.$keyword.'%" OR `hospital` like "%'.$keyword.'%" OR `subject` like "%'.$keyword.'%" OR `result` like "%'.$keyword.'%")';

			}

			if (isset($_GET['st'])&&isset($_GET['et'])) {

				$st = date('Y-m-d 00:00:00',strtotime($_GET['st']));

				$et = date('Y-m-d 23:59:59',strtotime($_GET['et']));

				$where .= ' AND `inputtime`>='.strtotime($st).' AND `inputtime`<='.strtotime($et);

			}

			$modelid = 17;

			$this->content_db->set_model($modelid);

			$_medical = $this->content_db->listinfo($where,'inputtime DESC', $this->page, $this->pageSize);

			empty($_medical[0])&&$_medical=array();

			$pages = '';

			$status = 0;

			$medical = array();

			if ($_medical) {

				$status = 1;

				$pages = $this->content_db->pages;

				foreach($_medical as $r){

					$ids .= $r['id'].',';

					$r['inputtime'] = date('Y-m-d H:i:s',$r['inputtime']);

					$r['seetime'] = date('Y-m-d H:i:s',$r['seetime']);

					$medical[$r['id']] = $r;

				}

				$ids = substr($ids,0,-1);

				$where = '`id` in ('.$ids.')';

				$this->content_db->table_name = $this->content_db->table_name.'_data';

				$model = $this->content_db->select($where,'id,case_record,lab_report,check_report');

				if ($model) {

					foreach($model as $r){

						if ($r&&$medical[$r['id']]) {

							$r['case_record'] = string2array($r['case_record']);

							$r['lab_report'] = string2array($r['lab_report']);

							$r['check_report'] = string2array($r['check_report']);

							$medical[$r['id']] = array_merge($r,$medical[$r['id']]);

						}

					}

				}

				$medical = array_values($medical);

				unset($_medical,$model);

			}

			$jsonData = array(

				'status'=>$status,

				'data'=>array('medical'=>$medical,'pages'=>$pages)

			);

			exit(json_encode($jsonData));

		}

		include template('dataManage','memberPersonalMedical');

	}

	

	public function memberPersonalRfriend(){

		if ($this->ajax) {

			$userid = intval($_GET['uid']);

			if (0 >= $userid) exit('{"status":"0"}');

			$where = '`kakuid`='.$userid.' OR `fuid`='.$userid;

			$this->default_db->load('kith_and_kin');

			$_rfrient = $this->default_db->select($where,'kakuid,kakuname,kakunick,state,fuid,funame,funick');

			$status = 0;

			$rfrient = $member = array();

			if ($_rfrient) {

				$status = 1;

				$uids = array();

				foreach($_rfrient as $r){

					$uids[] = $r['fuid'] == $userid?$r['kakuid']:$r['fuid'];

					$rfrient[] = $r;

				}

				$uids = implode(',',$uids);

				$where = '`userid` in ('.$uids.')';

				$_member = $this->member_db->select($where,'userid,username,nickname');

				if ($_member) {

					foreach($_member as $r){

						$member[$r['userid']] = $r;

					}

					$this->member_db->set_model(10);

					$model = $this->member_db->select($where,'userid,portrait,health_label');

					if ($model) {

						foreach($model as $r){

							if ($r&&$member[$r['userid']]) {

								$r['health_label_str'] = $this->dealHealthLabel($r['health_label']);

								$member[$r['userid']] = array_merge($r,$member[$r['userid']]);

							}

							

						}

					}

				}

				unset($_rfrient,$uids,$_member,$model);

			}			

			$jsonData = array(

				'status'=>$status,

				'data'=>array('rfrient'=>$rfrient,'member'=>$member)

			);

			exit(json_encode($jsonData));

		}

		include template('dataManage','memberPersonalRfriend');

	}

	

	//person pay record

	public function memberPersonalBuy(){

		$uid = intval($_GET['uid']);

		if ($this->ajax) {

			$where = '`userid`='.$uid;

			$type = intval($_GET['type']);

			if ($type==1) {

				$page = isset($_GET['page']) && $_GET['page'] ? intval($_GET['page']) : 1;

        		$pagesize = 20;

				$where .= ' AND `status`=2';

				$this->default_db->load('order');

				$datas = $this->default_db->listinfo($where,'addtime DESC',$page,$pagesize);

				empty($datas[0])&&$datas=array();

				if ($datas) {

					$pages = $this->default_db->pages;

					foreach($datas as $k=>$r){

						$datas[$k]['addtime'] = date('Y-m-d H:i',$r['addtime']);

						$datas[$k]['pmoney'] = number_format($r['pmoney'],2);

						$datas[$k]['pay_money'] = number_format($r['pay_money'],2);

					}

				}

				$status = $datas?1:0;

				exit(json_encode(array('data'=>array('datas'=>$datas,'pages'=>$pages),'status'=>$status)));

			} else {

				$this->default_db->load('member');

				$member = $this->default_db->get_one($where,'userid,phpssouid');

				$time = time();

				$auth = array(

					'time'=>$time,

					'plat'=>1,

					'sign'=>md5('1yjxun'.$time),

				);

				$url = 'http://shop.yjxun.cn/?m=wpm&c=datas&a=getUserOrderRecord&uid='.$member['phpssouid'];

				if (isset($_GET['page'])&&$_GET['page']) {

					$url .= '&page='.intval($_GET['page']);

				}

				if (isset($_GET['k'])&&$_GET['k']) {

					$url .= '&k='.$_GET['k'];

				}

				if (isset($_GET['st'])&&isset($_GET['et'])) {

					$url .= '&st='.$_GET['st'].'&et='.$_GET['et'];

				}

				//var_dump($url);die;

				$data = _curl_post($url,$auth);

				exit($data);

			}

			

		}

		include template('dataManage','memberPersonalBuy');

	}

	

	public function memberPersonalRecharge(){

		$uid = intval($_GET['uid']);

		if ($this->ajax) {

			$where = '`userid`='.$uid.' AND `status`=2';

			if (isset($_GET['st'])&&isset($_GET['et'])) {

				$st = date('Y-m-d 00:00:00',strtotime($_GET['st']));

				$et = date('Y-m-d 23:59:59',strtotime($_GET['et']));

				$where .= ' AND `addtime`>='.strtotime($st).' AND `addtime`<='.strtotime($et);

			}

			$this->default_db->load('order');

			$datas = $this->default_db->listinfo($where,'addtime DESC',$this->page, $this->pageSize);

			empty($datas[0])&&$datas=array();

			$status = 0;

			$pages = '';

			if ($datas) {

				$status = 1;

				$pages = $this->default_db->pages;

				foreach($datas as $k=>$r){

					$datas[$k]['addtime'] = date('Y-m-d H:i',$r['addtime']);

					$datas[$k]['pmoney'] = number_format($r['pmoney'],2);

					$datas[$k]['pay_money'] = number_format($r['pay_money'],2);

				}

			}

			$jsonData = array(

				'status'=>$status,

				'data'=>array('datas'=>$datas,'pages'=>$pages),

			);

			exit(json_encode($jsonData));

		}

		include template('dataManage','memberPersonalRecharge');

	}

	

	public function memberPersonalBrowse($a = ''){

		$t = date("t");//本月天数

		$this->default_db->load('visit_detail');

		if($_REQUEST['start_time']){

			$stime_arr = explode('-',$_REQUEST['start_time']);

			$sy = $stime_arr[0];

			$sm = $stime_arr[1];

			$sd = $stime_arr[2];

		}else{

			$sy = 'Y';

			$sm = 'm';

			$sd = 'd';

		}

		if($_REQUEST['end_time']){

			$etime_arr = explode('-',$_REQUEST['end_time']);

			$ey = $etime_arr[0];

			$em = $etime_arr[1];

			$ed = $etime_arr[2];

		}else{

			$ey = 'Y';

			$em = 'm';

			$ed = 'd';

		}

		if($a == 'day_count_'){

			$data1 = $this->default_db->select('`visit_time` >= '.(strtotime(date($sy.$sm.$sd.' 00:00:00')) - 86400).' AND `visit_time` <= '.(strtotime(date($sy.$sm.$sd.' 23:59:59')) - 86400));

			return count($data1);

		}else if($_GET['day_count'] || $a == 'day_count'){

			$data = $this->default_db->select('`visit_time` >= '.strtotime(date($sy.$sm.$sd.' 00:00:00')).' AND `visit_time` <= '.strtotime(date($sy.$sm.$sd.' 23:59:59')));

			$dat_count = count($data);

			return $dat_count;

		}else if($_GET['month_count'] || $a == 'month_count'){

			/*$sy = 'Y';

			$sm = 'm';*/

			$data = $this->default_db->select('`visit_time` >= '.strtotime(date($sy.$sm.'01 00:00:00')).' AND `visit_time` <= '.strtotime(date('Ym'.$t.' 23:59:59')));

			$month_count = count($data);

			return $month_count;

		}else if($_GET['day_data'] == 1){

			$data = $this->default_db->select('`visit_time` >= '.strtotime(date($sy.$sm.$sd.' 00:00:00')).' AND `visit_time` <= '.strtotime(date($sy.$sm.$sd.' 23:59:59')));

			foreach($data as $v){

				$uids[] = $v['userid'];

				$datas[$v['userid']] = $v;

			}

			if($uids){

				$this->default_db->load('member');

				$data_ = $this->default_db->select(array('userid'=>array('in',$uids)),'userid,username,nickname');

			}

			foreach($data_ as $v){

				$datas[$v['userid']]['username'] = $v['username'];

				$datas[$v['userid']]['nickname'] = $v['nickname'];

			}

			include template('dataManage','memberPersonalBrowse');

		}else if($_GET['day_data'] == 2){

			$data = $this->default_db->select('`visit_time` >= '.strtotime(date($sy.$sm.'1 00:00:00')).' AND `visit_time` <= '.strtotime(date($sy.$sm.$t.' 23:59:59')));

			foreach($data as $v){

				$uids[] = $v['userid'];

				$datas[$v['userid']] = $v;

			}

			if($uids){

				$this->default_db->load('member');

				$data_ = $this->default_db->select(array('userid'=>array('in',$uids)),'userid,username,nickname');

			}

			foreach($data_ as $v){

				$datas[$v['userid']]['username'] = $v['username'];

				$datas[$v['userid']]['nickname'] = $v['nickname'];

			}

			include template('dataManage','memberPersonalBrowse');

		}else if($_GET['get_uid'] > 0){

			$this->default_db->load('visit_detail');

			$data = $this->default_db->get_one(array('userid'=>$_GET['get_uid']));

			$data_arr = array_reverse(json_decode($data['data_json'],true));

			include template('dataManage','memberPersonalBrowse2');

		}

	}

	

	//在线用户
	public function onlineMember(){
		if ($this->ajax) {
			$onlineSockid = array();
			try {
				//$error = 'Always throw this error';
				//throw new Exception($error);
				//Gateway::$registerAddress = '127.0.0.1:1238';
				$onlineClient = Gateway::getAllClientInfo();
				//var_dump($onlineClient);die;
				if ($onlineClient) {
					$onlineSockid = array_keys($onlineClient);
				}
			} catch (Exception $e) {
				//echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
			$where = 1;
			if (isset($_GET['k'])&&$_GET['k']) {
				$keyword = $_GET['k'];
			}
			if ($_GET['st']&&$_GET['et']) {
				$st = date('Y-m-d 00:00:00',strtotime($_GET['st']));
				$et = date('Y-m-d 23:59:59',strtotime($_GET['et']));
				$where .= ' AND `last_time`>='.strtotime($st).' AND `last_time`<='.strtotime($et);
			}
			$queryStatus = intval($_GET['status']);
			if ($queryStatus) {
				if ($onlineSockid) {
					$socketid = $onlineSockid?'"'.implode('","',$onlineSockid).'"':'';
					//var_dump($socketid);die;
					$where .= ' AND `last_socket_id` '.(1==$queryStatus?'in':'not in').' ('.$socketid.')';
				} else {
					if (1==$queryStatus) exit('{"status":0}');
				}
				
			}
			//echo $where;die;
			$this->pageSize = 15;
			$this->default_db->load('online_users');
			$online = $this->default_db->listinfo($where, 'last_time DESC', $this->page, $this->pageSize);
			empty($online[0])&&$online=array();
			$member = $rtp = array();
			$pageCount = 0;
			$status = 0;
			
			//上线/离线统计
			$lineCount['goOnlineCount'] = $this->default_db->count(1);
			
			if($online){
				$status = 1;
				$pageCount = $this->default_db->number;
				$uids = array();
				foreach($online as $k=>$r){
					$uids[] = $r['userid'];
					$offset = time() - $r['last_time'];
					if (in_array($r['last_socket_id'],$onlineSockid)) {
						$state = 1;
						$r['online'] = '在线'.(floor($offset/60)).'分钟';
						$lineCount['onlineCount']++;
					} else {
						$state = 0;
						$offLine = '';
						if ($offset > 365*86400) {
							$remainder = $offset%(365*86400);
							$offLine = floor($offset/(365*86400)) . '年'.(0<$remainder?floor($remainder/(30*86400)).'月':'');
						} elseif ($offset > 30*86400) {
							$offLine = floor($offset/(30*86400)).'月';
						} elseif ($offset > 86400) {
							$offLine = floor($offset/86400).'天';
						} elseif ($offset > 3600) {
							$offLine = floor($offset/3600).'小时';
						} else {
							$offLine = floor($offset/60).'分钟';
						}
						$r['offLine'] = '已离线'.$offLine;
					}
					$device = '';
					switch($r['last_source']){
						case 1:
							$device = '微信';
							break;
						case 2:
							$device = '安卓';
							break;
						case 3:
							$device = '苹果';
							break;
					}
					$r['state'] = $state;
					$r['device'] = $device;
					$r['last_time'] = date('Y-m-d H:i:s',$r['last_time']);
					$online[$k] = $r;
				}
				
				if ($uids) {
					$uids = implode(',',$uids);
					$where = '`userid` in ('.$uids.')';
					$this->default_db->load('member_rtp');
					$_rtp = $this->default_db->select($where,'userid,province,city,district,street,addr,lng,lat,addr,updatetime');
					if ($_rtp) {
						foreach($_rtp as $r){
							$r['time'] = date('Y-m-d H:i:s',$r['updatetime']);
							$rtp[$r['userid']] = $r;
						}
					}
					
					$where .=  ' AND `roleid`=13';
					$this->default_db->load('member');
					$_member = $this->default_db->select($where,'userid,username,nickname');
					if ($_member) {
						foreach($_member as $r){
							$member[$r['userid']] = $r;
						}
					}
				}
				$lineCount['offlineCount'] = $lineCount['goOnlineCount'] - $lineCount['onlineCount'];
			}
			unset($onlineClient);
			$jsonData = array(
				'status'=>$status,
				'data'=>array('online'=>$online,'member'=>$member,'rtp'=>$rtp,'lineCount'=>$lineCount,'pageCount'=>$pageCount)
			);
			exit(json_encode($jsonData));
		}
		include template('dataManage','onlineMember');
	}

	

	public function memberGroup(){
		/*$where = '`id`<20 OR `id`>38';
		$this->default_db->load('auth_member_group');
		$group = $this->default_db->select($where,'id');
		$this->default_db->load('member');
		$result = $tmp = array();
		foreach($group as $r){
			$where2 = '`groupid2` like "%,'.$r['id'].',%"';
			$user = $this->default_db->select($where2,'userid,groupid2');
			foreach($user as $v){
				if (!in_array($v['userid'],$tmp)) {
					$tmp[] = $v['userid'];
				} else {
					$result[$r['id']][] = $v['userid'];
					$where3 = '`userid`='.$v['userid'];
					$groupid = str_replace(','.$r['id'].',',',',$v['groupid2']);
					//var_dump($groupid);die;
					$info = '`groupid2`="'.$groupid.'"';
					$this->default_db->update($info,$where3);
				}
			}
		}
		var_dump($result);die;*/
		if ($this->ajax) {

			$where = 1;
			

			if (isset($_GET['k'])&&$_GET['k']) {

				$keyword = $_GET['k'];

				$where .= ' AND `title` like "%'.$keyword.'%"';

			}

			if (isset($_GET['st'])&&isset($_GET['et'])) {

				$st = date('Y-m-d 00:00:00',strtotime($_GET['st']));

				$et = date('Y-m-d 23:59:59',strtotime($_GET['et']));

				$where .= ' AND `addtime`>='.strtotime($st).' AND `addtime`<='.strtotime($et);

			}
			$this->pageSize = 10;
			$this->default_db->load('auth_member_group');

			$memberGroup = $this->default_db->listinfo($where,'addtime desc',$this->page,$this->pageSize);

			empty($memberGroup[0])&&$memberGroup = array();

			$status = 0;
			$pageCount = 0;

			if ($memberGroup) {

				$status = 1;

				$pageCount = $this->default_db->number;


				foreach($memberGroup as $k=>$r){

					

					$r['addtime'] = date('Y-m-d H:i:s',$r['addtime']);

					$memberGroup[$k] = $r;

				}

			}

			$jsonData = array(

				'status'=>$status,

				'data'=>array('datas'=>$memberGroup,'pageCount'=>$pageCount),

			);

			exit(json_encode($jsonData));

		}

		include template('dataManage','memberGroup');

	}

	

	public function doMemberGroup(){

		if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {

			$groupid = intval($_POST['groupid']);

			$name = $_POST['name'];

			$note = $_POST['note'];

			$this->default_db->load('auth_member_group');

			$info = array('name'=>$name,'note'=>$note);

			if ($groupid > 0) {

				$where = '`id`='.$groupid;

				$status = $this->default_db->update($info,$where);

			} else {

				$info['authid'] = $this->authData['userid'];

				$info['node'] = $this->authData['node'];

				$info['addtime'] = time();

				$status = $this->default_db->insert($info,true);

			}

			exit('{"status":'.($status?1:0).',"erro":"'.($status?'保存成功':'保存失败').'"}');

		} else {

			if ($this->ajax) {

				$groupid = intval($_GET['groupid']);

				$where = '`id` = '.$groupid;

				$this->default_db->load('auth_member_group');

				$memberGroup = $this->default_db->get_one($where);

				$status = $memberGroup?1:0;

				$jsonData = array(

					'status'=>$status,

					'data'=>array('memberGroup'=>$memberGroup),

				);

				exit(json_encode($jsonData));

			}

			include template('dataManage','doMemberGroup');

		}

		

	}

		

	public function delGroup(){
		if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {
			$groupids = trim($_POST['ids'],',');

			if (!$groupids) exit('{"status":0,"erro":"分组不存在"}');
	
			$where = '`id` in ('.$groupids.')';
	
			$this->default_db->load('auth_member_group');
	
			$status = $this->default_db->delete($where);
	
			if ($status) {
	
				$groupids = explode(',',$groupids);
	
				foreach($groupids as $r){
	
					$where = '`groupid2` like "%,'.$r.',%"';
	
					$info = '`groupid2` = replace(`groupid2`,",'.$r.',",",")';
	
					$this->member_db->update($info,$where);
	
				}
	
			}
	
			exit('{"status":'.($status?1:0).',"erro":"'.($status?'删除成功':'删除失败').'"}');
		}
	}

	

	public function memberGroupDetail(){

		if ($this->ajax) {

			$groupid = intval($_GET['groupid']);

			if (0 >= $groupid) exit('{"status":0,"erro":"分组不存在"}');

			$where = '`groupid2` like "%,'.$groupid.',%"';

			if (isset($_GET['k'])&&$_GET['k']) {

				$keyword = $_GET['k'];

				$where .= ' AND (`username` like "%'.$keyword.'%" OR `nickname` like "%'.$keyword.'%")';

			}

			if ($_GET['hardware'] || $_GET['doctor'] || $_GET['healthy'] || $_GET['returnVisit']) {

				$haveuids = $emptyuids = array();

				$hardware = intval($_GET['hardware']);

				$doctor = intval($_GET['doctor']);

				$healthy = intval($_GET['healthy']);

				$returnVisit = intval($_GET['returnVisit']);

				$queryWhere = 1;

				

				if (0 < $hardware) {

					$groupUser = $this->member_db->select($where,'userid');

					if (empty($groupUser)) exit('{"status":0}');

					$groupuids = '';

					foreach($groupUser as $r){

						$groupuids .= $r['userid'].',';

					}

					$groupuids = substr($groupuids,0,-1);

					//var_dump($groupuids);die;

					$queryWhere .= ' AND `active` = 1 AND `userid` in ('.$groupuids.')';

					$this->default_db->load('hardware_data');

					$deviceBind = $this->default_db->select($queryWhere,'userid');

					if ($deviceBind) {

						$_haveuids = $_emptyuids = array();

						foreach($deviceBind as $r){

							if ($hardware==1) {

								$_haveuids[$r['userid']] = $r['userid'];

							} else {

								$_emptyuids[$r['userid']] = $r['userid'];

							}

						}

						if ($_haveuids)  $haveuids = $_haveuids;

						if ($_emptyuids)  $emptyuids = $_emptyuids;

					}

					$cst = isset($_GET['cst'])&&$_GET['cst']?date('Y-m-d 00:00:00',strtotime($_GET['cst'])):'';

					$cet = isset($_GET['cet'])&&$_GET['cet']?date('Y-m-d 23:59:59',strtotime($_GET['cet'])):'';

					if ($haveuids&&$hardware==1&&$cst&&$cet) {

						$_haveuids = array();

						pc_base::load_app_class('encipher','data',0);

						$encipher = new encipher();

						foreach($haveuids as $r){

							$uid = $encipher->fixed_encode($r);

							$queryWhere2 = '`userid` = "'.$uid.'" AND `add_date` BETWEEN "'.$cst.'" AND "'.$cet.'"';

							if (!in_array($r,$_haveuids)) {

								$this->default_db->load('medical_bpm_new');

								$bpm = $this->default_db->get_one($queryWhere2,'userid','','add_date desc');

								//var_dump($queryWhere2);die;

								if ($bpm) {

									$_haveuids[] = $r;

									continue;	

								}

								

								$this->default_db->load('medical_mmhg_new');

								$mmhg = $this->default_db->get_one($queryWhere2,'userid','','add_date desc');

								if ($mmhg) {

									$_haveuids[] = $r;

									continue;	

								}

								

								$this->default_db->load('bioland_bg_new');

								$bg = $this->default_db->get_one($queryWhere2,'userid','','add_date desc');

								if ($bg) {

									$_haveuids[] = $r;

									continue;

								}

							}

						}

						//var_dump($_haveuids);die;

						if ($_haveuids) {

							$haveuids = array_intersect($haveuids,$_haveuids);

						} else {

							exit('{"status":0}');

						}

					}

				}

				//var_dump($haveuids);die;

				

				if (0 < $doctor) {

					$this->default_db->load('doc_server_set');

					$doctorSevice = $this->default_db->select($queryWhere,'user_id');

					if ($doctorSevice) {

						$_haveuids = $_emptyuids = array();

						foreach($doctorSevice as $r){

							if ($doctor==1) {

								$_haveuids[$r['user_id']] = $r['user_id'];

							} else {

								$_emptyuids[$r['user_id']] = $r['user_id'];

							}

						}

						if (empty($haveuids)&&$_haveuids)  $haveuids = $_haveuids;

						if (empty($emptyuids)&&$_emptyuids)  $emptyuids = $_emptyuids;

						if ($_haveuids) $haveuids = array_intersect($haveuids,$_haveuids);

						if ($_emptyuids) $emptyuids = array_intersect($emptyuids,$_emptyuids);

					}

				}

				

				if (0 < $healthy) {

					$this->default_db->load('service_healthy');

					$healthySevice = $this->default_db->select($queryWhere,'userid');

					if ($healthySevice) {

						$_haveuids = $_emptyuids = array();

						foreach($healthySevice as $r){

							if ($healthy==1) {

								$_haveuids[$r['userid']] = $r['userid'];

							} else {

								$_emptyuids[$r['userid']] = $r['userid'];

							}

						}

						if (empty($haveuids)&&$_haveuids)  $haveuids = $_haveuids;

						if (empty($emptyuids)&&$_emptyuids)  $emptyuids = $_emptyuids;

						if ($_haveuids) $haveuids = array_intersect($haveuids,$_haveuids);

						if ($_emptyuids) $emptyuids = array_intersect($emptyuids,$_emptyuids);

					}

				}

				

				if (0 < $returnVisit) {

					if ($returnVisit != 3) $queryWhere = '`type`='.$returnVisit;

					$vst = isset($_GET['vst'])&&$_GET['vst']?date('Y-m-d 00:00:00',strtotime($_GET['vst'])):'';

					$vet = isset($_GET['vet'])&&$_GET['vet']?date('Y-m-d 23:59:59',strtotime($_GET['vet'])):'';

					if ($vst&&$vet) {

						$queryWhere .= ' AND `addtime`>='.strtotime($vst).' AND `addtime`<='.strtotime($vet);

					}

					$this->default_db->load('returnVisit');

					$visitRecord = $this->default_db->select($queryWhere,'userid');

					//var_dump($visitRecord);die;

					if ($visitRecord) {

						$_haveuids = $_emptyuids = array();

						foreach($visitRecord as $r){

							if (in_array($returnVisit,array(1,2))) {

								$_haveuids[$r['userid']] = $r['userid'];

							} else {

								$_emptyuids[$r['userid']] = $r['userid'];

							}

						}

						if (empty($haveuids)&&$_haveuids)  $haveuids = $_haveuids;

						if (empty($emptyuids)&&$_emptyuids)  $emptyuids = $_emptyuids;

						if ($_haveuids) $haveuids = array_intersect($haveuids,$_haveuids);

						if ($_emptyuids) $emptyuids = array_intersect($emptyuids,$_emptyuids);

					} else {

						exit('{"status":0}');

					}

				}

				

				if (empty($haveuids)&&empty($emptyuids)) exit('{"status":0}');

				if (!empty($haveuids)) {

					$haveuids = implode(',',$haveuids);

					$where .= ' AND `userid` in ('.$haveuids.')';

				}

				if (!empty($emptyuids)) {

					$emptyuids = implode(',',$emptyuids);

					$where .= ' AND `userid` not in ('.$emptyuids.')';

				}

				//var_dump($where);die;

				//$uids = array_intersect($haveuids,$emptyuids);

				//$uids = implode(',',$uids);

				//$where .= ' AND `userid` in ('.$uids.')';

			}

			$member = $this->member_db->listinfo($where,'regdate DESC',$this->page,$this->pageSize,'','','','','userid,username,nickname,state');

			empty($member[0])&&$member=array();

			$status = 0;

			$group = $hardware = $dataRecord = $abnormalData = $service = $doctor = $chat = $family = $family2 = $sms = $returnVisit = array();

			$pageCount = '';

			if ($member) {

				$status = 1;

				$pageCount = $this->member_db->number;

				$uids = '';

				foreach($member as $k=>$r){
					$r['stateStr'] = 1==$r['state']?'已激活':'未激活';
					$member[$k] = $r;
					$uids .= $r['userid'].',';

					$where = '`receive_id` = '.$r['userid'];

					$this->default_db->load('smsRecord');

					$_sms = $this->default_db->get_one($where,'receive_id,addtime','addtime desc');

					if ($_sms) {

						$sms[$r['userid']] = array('time'=>date('Y-m-d H:i:s',$_sms['addtime']));

					}

					

					$where = '`userid` = '.$r['userid'].' AND `type`=1';

					$this->default_db->load('returnVisit');

					$_returnVisit = $this->default_db->get_one($where,'userid,addtime','addtime desc');

					if ($_returnVisit) {

						$returnVisit[$r['userid']] = array('time'=>date('Y-m-d H:i:s',$_returnVisit['addtime']));

					}

					

				}

				//var_dump($sms);die;

				$uids = substr($uids,0,-1);

				$where = '`userid` in ('.$uids.')';

				$this->default_db->load('hardware_data');

				$haveHardware = $this->default_db->select($where,'userid,sn,hardwareid,activetime');

				//var_dump($bind);die;

				if ($haveHardware) {

					foreach($haveHardware as $r){

						$_hardware = $this->getHardware($r['hardwareid']);

						$r['hardware'] = $_hardware['name'];

						$r['time'] = date('Y-m-d H:i:s',$r['activetime']);

						$hardware[$r['userid']][] = $r;

					}

				}

				

				$dataRecord = $this->getLastRecord(explode(',',$uids));//获取最新检测数据

				//var_dump($dataRecord);die;

				

				$where = '`user_id` in ('.$uids.')';

				$this->default_db->load('doc_server_set');

				$_service = $this->default_db->select($where,'user_id,doctor_id,server_time,vid');

				if ($_service) {

					$doctorids = array();

					foreach($_service as $r){

						if (!in_array($r['doctor_id'],$doctorids)) $doctorids[] = $r['doctor_id'];

						//获取聊天记录

						$where = '`vid` = '.$r['vid'];

						$this->default_db->load('chat_record_im');

						$_chat = $this->default_db->get_one($where,'last_time','last_time desc');

						if ($_chat){

							$chat[$r['vid']] = array('time'=>date('Y-m-d H:i:s',$_chat['last_time']));

						}

												

						$r['server_time'] = date('Y-m-d H:i:s',$r['server_time']);

						$service[$r['user_id']][] = $r;

					}

					if (!empty($doctorids)) {

						$doctorids = implode(',',$doctorids);

						$where = '`userid` in ('.$doctorids.')';

						$this->content_db->set_model(52);

						$_doctor = $this->content_db->select($where,'userid,realname');

						if ($_doctor) {

							foreach($_doctor as $r){

								$doctor[$r['userid']] = $r;

							}

						}

					}

				}

				

				$where = '`kakuid` in ('.$uids.') OR `fuid` in ('.$uids.')';

				//var_dump($where);die;

				$this->default_db->load('kith_and_kin');

				$_family = $this->default_db->select($where,'fuid,funame,funick,kakuid,kakuname,kakunick,state');

				if ($_family) {

					$uids2 = array();

					foreach($_family as $r){

						if (!in_array($r['fuid'],$uids2)) $uids2[] = $r['fuid'];

						if (!in_array($r['kakuid'],$uids2)) $uids2[] = $r['kakuid'];

						$uids = ','.$uids.',';

						if (false !== strpos($uids,$r['fuid'])) $family[$r['fuid']][] = $r;

						if (false !== strpos($uids,$r['kakuid'])) $family[$r['kakuid']][] = $r;

						

					}

					if (!empty($uids2)) {

						$uids2 = implode(',',$uids2);

						$where = '`userid` in ('.$uids2.')';

						$_member = $this->member_db->select($where,'userid,username,nickname');

						if ($_member) {

							foreach($_member as $r){

								$family2[$r['userid']] = $r;

							}

						}

					}

				}

			}

			$jsonData = array(

				'status'=>$status,

				'data'=>array('member'=>$member,'hardware'=>$hardware,'dataRecord'=>$dataRecord,'service'=>$service,'doctor'=>$doctor,'chat'=>$chat,'family'=>$family,'family2'=>$family2,'sms'=>$sms,'returnVisit'=>$returnVisit,'pageCount'=>$pageCount),

			);

			exit(json_encode($jsonData));

		}

		include template('dataManage','memberGroupDetail');

	}

	

	public function getLastRecord($uids){

		$record = array();

		if (!empty($uids)) {

			pc_base::load_app_class('encipher','data',0);

			$encipher = new encipher();

			foreach($uids as $r){

				$uid = $encipher->fixed_encode($r);

				$where = '`userid` = "'.$uid.'"';

				$this->default_db->load('medical_bpm_new');

				$bpm = $this->default_db->get_one($where.' AND num > 0','userid,num,add_date,device_name','','add_date desc');

				if ($bpm) {

					$hardware = $this->dealHardwareName($bpm['device_name']);

					$value = intval($bpm['num']);

					$state = 1;

					if ($value <= 60 || $value >= 100) {

						$value .= '(心率异常)';

						$state = 2;

					}

					$record[$r][] = array(

						'hardware'=>$hardware,

						'type'=>'心率',

						'value'=>$value,

						'state'=>$state,

						'time'=>$bpm['add_date'],

					);

				}

				$this->default_db->load('medical_mmhg_new');

				$mmhg = $this->default_db->get_one($where,'userid,high,low,add_date,device_name','','add_date desc');

				if ($mmhg) {

					$hardware = $this->dealHardwareName($mmhg['device_name']);

					$high = $encipher->j16_decode($mmhg['high']);

					$low = $encipher->j16_decode($mmhg['low']);

					if (strlen($high) == 6 || strlen($low) == 6) {

						$high = substr($high,0,3);

						$low = substr($low,0,3);

					}

					$high = intval($high);

					$low = intval($low);

					$value = $high.' / '.$low;

					$state = 1;

					if ($high >= 140 || $low >= 90 || $high <= 90 || $low <= 60) {

						$value .= '(血压异常)';

						$state = 2;

					}

					

					$record[$r][] = array(

						'hardware'=>$hardware,

						'type'=>'血压',

						'value'=>$value,

						'state'=>$state,

						'time'=>$mmhg['add_date'],

					);

				}

				$this->default_db->load('bioland_bg_new');

				$bg = $this->default_db->get_one($where,'userid,value,add_date,device_name','','add_date desc');

				if ($bg) {

					$hardware = $this->dealHardwareName($bg['device_name']);

					$value = $encipher->j16_decode($bg['value']);

					$value = round(intval($value)/18,1);

					$state = 1;

					if ($value >= 6.1 || $value <= 3.9) {

						$value .= '(血糖异常)';

						$state = 2;

					}

					$record[$r][] = array(

						'hardware'=>$hardware,

						'type'=>'血糖',

						'value'=>$value,

						'state'=>$state,

						'time'=>$bg['add_date'],

					);

				}

			}

		}

		return $record;

	}

	

	public function groupMemberRemind(){

		if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {

			$uid = trim($_POST['uid']);

			if ($uid == '') exit('{"status":"0"}');

			$type = $_POST['type'];

			$isFamily = intval($_POST['isFamily']);

			$content = $_POST['content'];

			$familyContent = $_POST['familyContent'];

			pc_base::load_app_class('smsManage','dataManage',0);

			$smsManage = new smsManage();

			$datas = array(

				'userid'=>$uid,

				'type'=>$type,

				'sms_type'=>'remind',

				'content'=>$content,

				'familyContent'=>$familyContent,

				'isFamily'=>$isFamily

			);

			$result = $smsManage->sendMessageMult2($datas);

			if (false == $result) {

				$result = array('status'=>0);

			}

			exit('{"status":'.$result['status'].',"erro":"'.($result['status']==1?'发送成功':'发送失败').'"}');

		}

	}

	

	public function dealHardwareName($str = ''){

		$hardware = '';

		if (trim($str) == '') return $hardware;

		switch($str){

			case 'x9pro':

			case 'v3':

				$hardware = $str;

				break;

			case 'bp_a223':

				$hardware = '血压计';

				break;

			case 'g_777g':

				$hardware = '血糖仪';

			default:

				$hardware = '手动录入';

				break;

		}

		return $hardware;

	}

	

	public function getHardware($hardwareid = 0){

		$hardware = array();

		$this->default_db->load('hardware');

		if (0 < $hardwareid) {

			$where = '`id` = '.$hardwareid;

			$hardware = $this->default_db->get_one($where,'id,name');

		} else {

			$_hardware = $this->default_db->select(1,'id,name');

			foreach($_hardware as $r){

				$hardware[$r['id']] = $r;

			}

		}

		return $hardware;

	}


	public function delGroupMember(){
		if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {
			$groupid = intval($_POST['groupid']);
			$ids = $_POST['ids'];
			$where = '`groupid2` like "%,'.$groupid.'%," AND `userid` in ('.$uid.')';
			$member = $this->member_db->select($where,'userid');
			$status = 0;
			if ($member) {
				$info = '`groupid2` = replace(`groupid2`,",'.$groupid.',",",")';
				$status = $this->member_db->update($info,$where);
			}
			exit('{"status":'.($status?1:0).',"erro":"'.($status?'删除成功':'删除失败').'"}');
		}
	}

	public function dealHealthLabel($label){

		$result = '';

		if (!$label) return $result;

		$label = explode(',',$label);

		if (!empty($label)) {

			$health_labels = array(1=>'高血压',2=>'低血压',3=>'高血糖',4=>'低血糖',5=>'心率偏低',6=>'心率偏高',7=>'心率异常',8=>'体型偏胖',9=>'体型偏瘦',10=>'体型超胖',11=>'睡眠不足',12=>'');

			foreach($label as $r){

				if ($r&&$health_labels[$r]) {

					$result .= $health_labels[$r].',';

				}

			}

			$result = substr($result,0,-1);

		}

		return $result;

	}

	

	public function getOnlineCount($where,$onlineClient){

		$count = array('total'=>0,'wecaht'=>0,'android'=>0,'ios'=>0);

		if (empty($onlineClient) || $where == '') return $count;

		$this->default_db->load('online_users');

		$online = $this->default_db->select($where, 'userid,last_socket_id,wx_socket_id,ad_socket_id,ios_socket_id,last_time','','last_time DESC');

		//var_dump($online);die;

		if($online){

			foreach($online as $r){

				if ($r['userid'] == 0) continue;

				$count['total'] += 1;

				//if ($r['wx_socket_id']==$r['last_socket_id']) $count['wechat'] += 1;

				//if ($r['ad_socket_id']==$r['last_socket_id']) $count['android'] += 1;

				//if ($r['ios_socket_id']==$r['last_socket_id']) $count['ios'] += 1;

				if ($r['wx_socket_id']&&in_array($r['wx_socket_id'],$onlineClient)) $count['wechat'] += 1;

				if ($r['ad_socket_id']&&in_array($r['ad_socket_id'],$onlineClient)) $count['android'] += 1;

				if ($r['ios_socket_id']&&in_array($r['ios_socket_id'],$onlineClient)) $count['ios'] += 1;

			}

		}

		return $count;

	}

	

	public function getWeekFirstLast($time = ''){

		//if ($time) echo date('Y-m-d',$time);

		$year = $time?date("Y",$time):date("Y");

		$month = $time?date("m",$time):date("m");

		$day = $time?date('w',$time):date('w');

		$day = $day==0?7:$day;

		$nowMonthDay = $time?date("t",$time):date("t");

		

		$firstday = ($time?date('d',$time):date('d')) - $day + 1;

		//var_dump($firstday);

		if(substr($firstday,0,1) == "-"){

			$firstMonth = $month - 1;

			$lastMonthDay = date("t",strtotime($year.'-'.$firstMonth));

			//var_dump($lastMonthDay);

			$firstday = $lastMonthDay - substr($firstday,1);

			$time_1 = strtotime($year."-".$firstMonth."-".$firstday.' 00:00:00');

		}else{

			$time_1 = strtotime($year."-".$month."-".$firstday.' 00:00:00');

		}

		if ($time) {

			//var_dump($day);

		}

		$lastday = ($time?date('d',$time):date('d')) + (7 - $day);

		if($lastday > $nowMonthDay){

			$lastday = $lastday - $nowMonthDay;

			$lastMonth = $month + 1;

			$time_2 = strtotime($year."-".$lastMonth."-".$lastday.' 23:59:59');

		}else{

			$time_2 = strtotime($year."-".$month."-".$lastday.' 23:59:59');

		}

		return array($time_1,$time_2);

	}
	
	public function memberStatistics(){
		//$allOnlineUsers = intval(Gateway::getAllClientCount()) - 1;//所有在线人数
		$allOnlineUsers = 0;
		$onlineSockid = array();
		try {
			//$error = 'Always throw this error';
			//throw new Exception($error);
			//Gateway::$registerAddress = '127.0.0.1:1238';
			$onlineClient = Gateway::getAllClientInfo();
			//var_dump($onlineClient);die;
			if ($onlineClient) {
				$onlineSockid = array_keys($onlineClient);
			}
			$where = array('last_socket_id'=>array('in',$onlineSockid));
			
			$this->default_db->load('online_users');
			$allOnlineUsers = $this->default_db->count($where);
			//var_dump($allOnlineUsers);die;
		} catch (Exception $e) {
			//echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
		$onlineDuration = 0;//总在线时长
		$regCount = 0;//当天注册量
		$loginCount = array();//当天登录量
		$viewCount = 0;//当天浏览量
		$inquiryCount = 0;//问诊次数
		$hardwareUsage = 0;//硬件使用次数
		$startToday = strtotime(date("Y-m-d"),time());
		$endToday = $startToday+60*60*24;
		//var_dump($startToday.'--'.$endToday);die;
		$this->default_db->load('online_detail');
		$da = $this->default_db->select('`add_time` > '.$startToday.' AND `add_time` < '.$endToday);
		foreach($da as $v){
			//$loginCount[$v['userid']] = 1;
			$onlineDuration += $v['duration'];
			$viewCount++;
		}
		$this->default_db->load('online_users');
		$loginCount = $this->default_db->count('`last_time`>'.$startToday.' AND `last_time`<'.$endToday);
		$d = floor($onlineDuration / (3600*24));
		$h = floor(($onlineDuration % (3600*24)) / 3600);
		$m = floor((($onlineDuration % (3600*24)) % 3600) / 60);
		if($d > '0'){
			$onlineDuration_ = $d.'d'.$h.'h'.$m.'m';
		}else{
			if($h != '0'){
				$onlineDuration_ = $h.'h'.$m.'m';
			}else{
				$onlineDuration_ = $m.'m';
			}
		}
		$this->default_db->load('member');
		$regCount = $this->default_db->count('`roleid` = 13 AND `regdate` > '.$startToday.' AND `regdate` < '.$endToday);
		$this->default_db->load('sr_question');
		$inquiryCount = $this->default_db->count('`add_time` > '.$startToday.' AND `add_time` < '.$endToday);
		include template($this->template,'memberStatistics');
	}
	public function getHistoryOnlineView(){
		$startToday = strtotime(date("Y-m-d"),time());
		$endToday = $startToday-60*60*24*30;
		$this->default_db->load('online_detail');
		$da = $this->default_db->select('`add_time` > '.$endToday.' AND `add_time` < '.$startToday);
		$data = array();
		$count = array();
		foreach($da as $v) $data[date('Y-m-d',$v['add_time'])][$v['userid']]++;
		foreach($data as $k=>$v){
			$count[$k]['online'] = count($v);
			foreach($v as $j) $count[$k]['view'] += $j;
		}
		exit(json_encode($count));
	}
	public function pageBrowse(){
		if ($this->ajax) {
			$this->pageSize = 15;
			$st = date('Y-m-d 00:00:00',isset($_GET['st'])?strtotime($_GET['st']):time());
			$et = date('Y-m-d 23:59:59',isset($_GET['et'])?strtotime($_GET['et']):time());
			$where = '`add_time`>'.strtotime($st).' AND `add_time`<'.strtotime($et);
			if (isset($_GET['k'])&&$_GET['k']) {
				$keyword = $_GET['k'];
				$queryWhere = '`username`="'.$keyword.'" OR `nickname` like "%'.$keyword.'%"';
				$queryMember = $this->member_db->select($where,'userid');
				if ($queryMember) {
					$queryUids = array();
					foreach($queryMember as $r){
						$queryUids[] = $r['userid'];
					}
					$where .= ' AND `userid` in ('.implode(',',$queryUids).')';
				} else {
					exit('{"status":0}');
				}
			}
			$this->default_db->load('online_detail');
			$datas = $this->default_db->listinfo($where,'add_time desc',$this->page,$this->pageSize);
			$status = 0;
			if ($datas) {
				$status = 1;
				$pageCount = $this->default_db->number;
				$uids = array();
				$sources = array('','微信','安卓','苹果');
				foreach($datas as $k=>$r){
					if (!in_array($r['userid'],$uids)) $uids[] = $r['userid'];
					$r['time'] = date('Y-m-d H:i:s',$r['add_time']);
					$r['source'] = $sources[$r['source']];
					$datas[$k] = $r;
				}
				if ($uids) {
					$where = array('userid'=>array('in',$uids));
					$_member = $this->member_db->select($where,'userid,username,nickname');
					foreach($_member as $r){
						$member[$r['userid']] = $r;
					}
				}
			}
			$jsonData = array(
				'status'=>$status,
				'data'=>array('datas'=>$datas,'member'=>$member,'pageCount'=>$pageCount)
			);
			exit(json_encode($jsonData));
		}
		include template($this->template,'pageBrowse');
	}
	
	public function memberList2(){
		if ($this->ajax) {
			$where = '(`roleid` = 13 OR `roleid` = 15)';
			if (isset($_GET['k'])&&$_GET['k']) {
				$keyword = $_GET['k'];
				$where .= ' AND (`username` = "'.$keyword.'" OR `nickname` like "%'.$keyword.'%")';
			}
			if (isset($_GET['userid'])&&$_GET['userid']) {
				$queryUserid = intval($_GET['userid']);
				$where .= ' AND `userid` = '.$queryUserid;
			}
			if (isset($_GET['st'])&&isset($_GET['et'])) {//搜索类型注册
				$st = date('Y-m-d 00:00:00',strtotime($_GET['st']));
				$et = date('Y-m-d 23:59:59',strtotime($_GET['et']));
				$where .= ' AND `regdate` >='.strtotime($st).' AND `regdate`<='.strtotime($et);
				if (isset($_GET['queryType'])&&'a2userReg'==$_GET['queryType']) {
					$where .= ' AND `roleid` = 13 AND `parent` <> 0 ';
				}
			} else {
				$queryType = $_GET['queryType'];
				if ($queryType) {
					$queryUserids = array();
					$st2 = strtotime(date('Y-m-d 00:00:00',strtotime($_GET['st2'])));
					$et2 = strtotime(date('Y-m-d 23:59:59',strtotime($_GET['et2'])));
					switch($queryType){
						case 'online':
							//$queryWhere = '`last_time`>'.strtotime(date('Y-m-d 00:00:00')).' AND `last_time`<'.strtotime(date('Y-m-d 23:59:59'));
							$queryWhere = '`last_time`>'.(time()-300);
							$this->default_db->load('online_users');
							$rq = $this->default_db->select($queryWhere,'userid,last_time');
							//var_dump($rq);die;
							foreach($rq as $r){
								//if (300>(time()-$r['last_time'])) $queryUserids[] = $r['userid'];
								$queryUserids[] = $r['userid'];
							}
							break;
						case 'offline3':
						case 'offline7':
							$currentTime = time();
							$day = intval(substr($queryType,7))*24*60*60;
							if ('offline3'==$queryType) {
								$queryWhere = '`last_time`<='.($currentTime-$day).' AND `last_time`>='.($currentTime-7*24*60*60);
							} else {
								$queryWhere = '`last_time`<='.($currentTime-$day);
							}
							$this->default_db->load('online_users');
							$rq = $this->default_db->select($queryWhere,'userid,last_time');
							foreach($rq as $r){
								//if (300>(time()-$r['last_time'])) $queryUserids[] = $r['userid'];
								$queryUserids[] = $r['userid'];
							}
							break;
						case 'active':
							$queryWhere = '`add_time`>'.$st2.' AND `add_time`<'.$et2;
							$sql = 'select DISTINCT userid from drcms_online_detail where '.$queryWhere;
							$this->default_db->query($sql);
							$rq = $this->default_db->fetch_array();
							//$this->default_db->load('online_detail');
							//$rq = $this->default_db->select($queryWhere,'userid,add_time');
							foreach($rq as $r){
								$queryUserids[] = $r['userid'];
							}
							break;
						case 'device':
						case 'devCate_2':
						case 'devCate_3':
						case 'devCate_5':
						case 'devCate_6':
						case 'devCate_8':
							$queryWhere = '`create_time`>'.$st2.' AND `create_time`<'.$et2;
							if (strpos($queryType,'devCate') !== false) {
								$devCate = explode('_',$queryType);
								$queryWhere .= ' AND `hardwareid`='.$devCate[1];
							}
							$sql = 'select DISTINCT userid from drcms_measure_data where '.$queryWhere;
							$this->default_db->query($sql);
							$rq = $this->default_db->fetch_array();
							foreach($rq as $r){
								$queryUserids[] = $r['userid'];
							}
							break;
						case 'equipment':
							$queryWhere = '`create_time`>'.$st2.' AND `create_time`<'.$et2;
							$sql = 'select DISTINCT user_id from drcms_health_data where '.$queryWhere;
							$this->default_db->query($sql);
							$rq = $this->default_db->fetch_array();
							foreach($rq as $r){
								$queryUserids[] = $r['user_id'];
							}
							break;
						case 'alarm':
							$queryWhere = '`createtime`>'.$st2.' AND `createtime`<'.$et2;
							$sql = 'select DISTINCT userid from drcms_alarm where '.$queryWhere;
							$this->default_db->query($sql);
							$rq = $this->default_db->fetch_array();
							foreach($rq as $r){
								$queryUserids[] = $r['userid'];
							}
							break;
						case 'healthWarning':
							$queryWhere = '`create_time`>'.$st2.' AND `create_time`<'.$et2;
							$sql = 'select DISTINCT user_id from drcms_warning where '.$queryWhere;
							$this->default_db->query($sql);
							$rq = $this->default_db->fetch_array();
							foreach($rq as $r){
								$queryUserids[] = $r['user_id'];
							}
							break;
						case 'shop':
							pc_base::load_app_class('itfApi','dataManage',0);
							$itfApi = new itfApi();
							$param = $itfApi->filterParam(array('st'=>$_GET['st2'],'et'=>$_GET['et2']));
							$token = $itfApi->getToken();
							$param['appid'] = $itfApi->appid;
							$param['token'] = $token['token'];
							$param['timeStamp'] = time();
							$param['nonceStr'] = $itfApi->getNonceStr();
							$sign = $itfApi->sign($param);
							$param['sign'] = $sign;
							$url = 'https://shop.yjxun.cn/?m=apiCenter&c=datas&a=getOrderSuccMember';
							$result = $itfApi->postCurl($param,$url);
							//var_dump($result);die;
							$result = json_decode($result,true);
							if (1==$result['status']) {
								$phpssouid = $result['data']['datas'];
								$queryWhere = array('phpssouid'=>array('in',$phpssouid));
								$this->default_db->load('member');
								$rq = $this->default_db->select($queryWhere,'userid');
								foreach($rq as $r){
									$queryUserids[] = $r['userid'];
								}
							}
							break;
						case 'visit':
							$queryWhere = '`userid`>0 AND `addtime`>'.$st2.' AND `addtime`<'.$et2;
							//$this->default_db->load('auth_return_visit');
							//$visit = $this->default_db->select($where,'userid,mobile');
							$sql = 'select DISTINCT userid from drcms_auth_return_visit where '.$queryWhere;
							$this->default_db->query($sql);
							$rq = $this->default_db->fetch_array();
							foreach($rq as $r){
								$queryUserids[] = $r['userid'];
							}
							break;
						case 'terminal_0':
						case 'terminal_1':
						case 'terminal_2':
						case 'terminal_3':
							$terminal = explode('_',$queryType);
							$queryWhere = '`source`='.$terminal[1];
							$sql = 'select DISTINCT userid from drcms_online_detail where '.$queryWhere;
							$this->default_db->query($sql);
							$rq = $this->default_db->fetch_array();
							foreach($rq as $r){
								$queryUserids[] = $r['userid'];
							}
							break;
						default:
							
					}
					if ($queryUserids) {
						$where .= ' AND `userid` in ('.implode(',',$queryUserids).')';
					} else {
						exit('{"status":0}');
					}
				}
			}
			//var_dump($where);die;
			$this->pageSize = 15;
			$this->default_db->load('member');
			$member = $this->default_db->listinfo($where,'regdate desc',$this->page,$this->pageSize,'','','','','userid,username,nickname,mobile,regdate,lastdate,user_type,remarks');
			$device = $locate = $operateState = array();
			if ($member) {
				$status = 1;
				$pageCount = $this->default_db->number;
				$userids = array();
				$types = array('','正常','内部测试','其他');
				foreach($member as $k=>$r){
					$userids[] = $r['userid'];
					$r['type'] = $types[$r['user_type']]?:'其他';
					$r['regdate'] = date('Y-m-d H:i:s',$r['regdate']);
					$datas[$r['userid']] = $r;
				}
				//var_dump($datas);die;
				if ($userids) {
					$where = array('userid'=>array('in',$userids));
					$this->default_db->load('member_detail');
					$model = $this->default_db->select($where,'userid,sex,age,province,city,area,street,community,address,lng,lat,device_text');
					$sexs = array('保密','男','女');
					foreach($model as $r){
						if (is_array($r)&&$datas[$r['userid']]) {
							$r['sexStr'] = $sexs[$r['sex']]?:'保密';
							$datas[$r['userid']] = array_merge($datas[$r['userid']],$r);
						}
					}
					
					//用户定位信息
					$this->default_db->load('member_rtp');
					$_locate = $this->default_db->select($where,'userid,province,city,district,street,addr,lng,lat,updatetime');
					if ($_locate) {
						foreach($_locate as $r){
							$r['address'] = $r['addr'];
							$r['time'] = date('Y-m-d H:i:s',$r['updatetime']);
							$locate[$r['userid']] = $r;
						}
					}
					
					
					//设备绑定情况
					$this->default_db->load('equipment');
					$equipment = $this->default_db->select(array('user_id'=>array('in',$userids)),'id,user_id');
					if ($equipment) {
						foreach($equipment as $r){
							if(!in_array($r['user_id'],$device)) $device[] = $r['user_id'];
						}
					}
					
					//在线、离线情况
					$this->default_db->load('online_users');
					$online = $this->default_db->select($where,'userid,last_time');
					foreach($online as $r){
						//前台功能限制、默认5分钟没操作为离线
						$offset = time() - $r['last_time'];
						if (300 > $offset) {
							$state = 1;
							$stateStr = '在线';
						} else {
							$state = 0;
							if ($offset > 365*86400) {
								$remainder = $offset%(365*86400);
								$offLine = floor($offset/(365*86400)) . '年'.(0<$remainder?floor($remainder/(30*86400)).'月':'');
							} elseif ($offset > 30*86400) {
								$offLine = floor($offset/(30*86400)).'月';
							} elseif ($offset > 86400) {
								$offLine = floor($offset/86400).'天';
							} elseif ($offset > 3600) {
								$offLine = floor($offset/3600).'小时';
							} else {
								$offLine = floor($offset/60).'分钟';
							}
							$stateStr = '离线，已离线'.$offLine;
						}
						$r['state'] = $state;
						$r['stateStr'] = $stateStr;
						$r['last_time'] = date('Y-m-d H:i:s',$r['last_time']);
						$operateState[$r['userid']] = $r;
					}
				}
				$datas = array_values($datas);
			} else {
				$status = 0;
				$pageCount = 0;
				$datas = array();
			}
			$jsonData = array(
				'status'=>$status,
				'data'=>array('datas'=>$datas,'locate'=>$locate,'device'=>$device,'operateState'=>$operateState,'pageCount'=>$pageCount)
			);
			exit(json_encode($jsonData));
		}
		include template($this->template,'memberList2');
	}
}
?>