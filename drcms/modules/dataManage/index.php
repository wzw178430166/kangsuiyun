<?php
defined('IN_drcms') or exit('No permission resources.');
pc_base::load_sys_class('form', '', 0);
pc_base::load_app_class('authority','dataManage',0);
pc_base::load_app_class('privManage','dataManage',0);
require drcms_PATH.'api/GatewayClient-master/Gateway.php';
use GatewayClient\Gateway;
class index {
	public $default_db,$content_db,$member_db,$authority;
	public $authData = array();
	public $ajax = 0;//异步访问标识
	function __construct() {
		$this->authority = new authority();
		$this->authData = $this->authority->authData;
		$this->default_db = pc_base::load_model('default_model');
		$this->content_db = pc_base::load_model('content_model');
		$this->member_db = pc_base::load_model('member_model');
		$this->ajax = intval($_GET['ajax']);
		if ($this->ajax) {
			pc_base::load_app_class('demo','core',0);
			$demo = new demo();
		}
		if(isset($_GET['system_style'])){
			$system_style = $_GET['system_style'];
			param::set_cookie('system_style',$system_style);
		}
	}
	public function init() {
		/*if (!$_GET['ssaw']) {
			$this->statistics();
			exit();
		}*/
		if ($this->ajax) {
			$datas = array('toDoDay3'=>0,'toDoDay7'=>0,'toDoWarning'=>0,'toDoCs'=>0,'toDoGuest'=>0,'todayTotal'=>0,'todayOnline'=>0,'todayRegister'=>0,'todayActive'=>0,'yesterdayRegister'=>0,'yesterdayActive'=>0,'todayEquipment'=>0,'todayWarning'=>0,'yesterdayEquipment'=>0,'yesterdayWarning'=>0,'todayPayMoney'=>0,'yesterdayPayMoney'=>0,'todayUv'=>0,'yesterdayUv'=>0,'todayPayMember'=>0,'yesterdayPayMember'=>0,'todayPv'=>0,'yesterdayPv'=>0,'todayPayNumber'=>0,'yesterdayPayNumber'=>0,'trend'=>array(),'trend2'=>array(),'trend3'=>array(),'trend4'=>array(),'pie'=>array(),'columnar'=>array());
			
			//待处理
			$currentTime = time();
			$day3 = 3*24*60*60;
			$day7 = 7*24*60*60;
			$where = '`last_time`<='.($currentTime - $day3);
			$this->default_db->load('online_users');
			$offline = $this->default_db->select($where,'userid,last_time');
			foreach($offline as $r){
				if ($day7<=($currentTime - $r['last_time'])) {
					$datas['toDoDay7']++;
				} else if ($day3<=($currentTime - $r['last_time'])) {
					$datas['toDoDay3']++;
				}
			}
			$where = '`status`=0';
			$this->default_db->load('warning');
			$datas['toDoWarning'] = $this->default_db->count($where);
			$where2 = '`aunread`>0';
			$this->default_db->load('csc_visitwait');
			$datas['toDoCs'] = $this->default_db->count($where2);
			$where3 = '`replytime`>0';
			$this->default_db->load('guestbook');
			$datas['toDoGuest'] = $this->default_db->count($where3);
			
			//用户数据
			$this->default_db->load('member');
			$datas['todayTotal'] = $this->default_db->count(1);
			$where = '`last_time`>'.($currentTime-300);
			$this->default_db->load('online_users');
			$datas['todayOnline'] = $this->default_db->count($where);
			
			$et = strtotime(date('Y-m-d 23:59:59'));
			$_et = date('Y-m-d 23:59:59');
			$st = strtotime(date('Y-m-d 00:00:00',strtotime("$_et -1 day")));
			
			$where = '`regdate`>='.$st.' AND `regdate`<='.$et;
			$this->default_db->load('member');
			$member = $this->default_db->select($where,'userid,regdate');
			if ($member) {
				foreach($member as $r){
					$regDate = date('Y-m-d',$r['regdate']);
					if ($regDate == date('Y-m-d')) {
						$datas['todayRegister']++;
					} else {
						$datas['yesterdayRegister']++;
					}
				}
			}
			
			$where2 = '`add_time`>='.$st.' AND `add_time`<='.$et;
			$this->default_db->load('online_detail');
			$active = $this->default_db->select($where2,'userid,add_time');
			if ($active) {
				$dateMember = array();
				foreach($active as $r){
					$createDate = date('Y-m-d',$r['add_time']);
					if ($createDate == date('Y-m-d')) {
						if (!in_array($r['userid'],$dateMember['today'])) $dateMember['today'][] = $r['userid'];
					} else {
						if (!in_array($r['userid'],$dateMember['yesterday'])) $dateMember['yesterday'][] = $r['userid'];
					}
				}
				$datas['todayActive'] = count($dateMember['today']);
				$datas['yesterdayActive'] = count($dateMember['yesterday']);
			}
			
			//设备数据
			$where = '`create_time`>='.$st.' AND `create_time`<='.$et;
			$where1 = $where . ' AND `source`=1 ';
			$this->default_db->load('health_data');
			$healthData = $this->default_db->select($where1,'user_id,create_time');
			if ($healthData) {
				$filterUser = array();
				foreach($healthData as $r){
					$day = date('Y-m-d',$r['create_time']);
					if (in_array($r['user_id'],$filterUser[$day])) continue;
					$filterUser[$day][] = $r['user_id'];
					if ($day == date('Y-m-d')) {
						$datas['todayEquipment']++;
					} else {
						$datas['yesterdayEquipment']++;
					}
				}
			}
			$where2 = $where.' AND `type`=1';
			$this->default_db->load('warning');
			$warning = $this->default_db->select($where2,'user_id,create_time');
			if ($warning) {
				$filterUser = array();
				foreach($warning as $r){
					$day = date('Y-m-d',$r['create_time']);
					if (in_array($r['user_id'],$filterUser[$day])) continue;
					$filterUser[$day][] = $r['user_id'];
					if ($day == date('Y-m-d')) {
						$datas['todayWarning']++;
					} else {
						$datas['yesterdayWarning']++;
					}
				}
			}
			
			//商城数据
			pc_base::load_app_class('itfApi','interface',0);
			$itfApi = new itfApi();
			$param = $itfApi->filterParam(array());
			$token = $itfApi->getToken();
			$param['appid'] = $itfApi->appid;
			$param['token'] = $token['token'];
			$param['timeStamp'] = time();
			$param['nonceStr'] = $itfApi->getNonceStr();
			$sign = $itfApi->sign($param);
			$param['sign'] = $sign;
			$url = 'https://shop.yjxun.cn/?m=interface&c=datas&a=getProductStatistics';
			$result = $itfApi->postCurl($param,$url);
			//var_dump($result);die;
			$result = json_decode($result,true);
			if ($result['data']['datas']) {
				foreach($result['data']['datas'] as $k=>$r){
				    //临时数据
				    if ('trend' == $k) {
				        foreach($r as $k2=>$v2){
				            $r[$k2]['today'] = mt_rand(0,20);
				            $r[$k2]['yesterday'] = mt_rand(0,20);
				        }
				    } else {
				        $r = mt_rand(0,30);
				    }
					$datas[$k] = $r;
				}
			}
			
			//网页数据
			for($i=6;$i>=0;$i--){
				$currentDate = date('Y-m-d');
				$date = strtotime("$currentDate -".$i." day");
				$date = date('m-d',$date);
				$webTrend[$date] = array(
					'pv'=>array('date'=>$date,'type'=>'pv','value'=>0),
					'uv'=>array('date'=>$date,'type'=>'uv','value'=>0)
				);
			}
			//var_dump($webTrend);die;
			$et = strtotime(date('Y-m-d 23:59:59'));
			$_et = date('Y-m-d 23:59:59');
			$st = strtotime(date('Y-m-d 00:00:00',strtotime("$_et -6 day")));
			$where = '`add_time`>='.$st.' AND `add_time`<='.$et;
			$this->default_db->load('online_detail');
			$web = $this->default_db->select($where,'id,userid,add_time','','add_time asc');
			if ($web) {
				$filter = array();
				foreach($web as $r){
					$webDate = date('m-d',$r['add_time']);
					$webTrend[$webDate]['pv']['value']++;
					if (0<$r['userid']) {
						if (!in_array($r['userid'],$filter[$webDate])) {
							//var_dump($r['userid']);
							$filter[$webDate][] = $r['userid'];
							$webTrend[$webDate]['uv']['value']++;
						}
					}
				}
			}
			foreach($webTrend as $r){
				foreach($r as $r2){
					$datas['trend2'][] = $r2;
				}
			}
			
			//设备型号分布
			$where = 1;
			$this->default_db->load('equipment');
			$equipment = $this->default_db->select($where,'id,model_id');
			if ($equipment) {
				$modelPie = array();
				$model_ids = array();
				foreach($equipment as $r){
					$modelPie[$r['model_id']]++;
					if (!in_array($r['model_id'],$model_ids)) $model_ids[] = $r['model_id'];
				}
				if ($model_ids) {
					$where2 = array('id'=>array('in',$model_ids));
					$this->default_db->load('equipment_model');
					$equipmentModel = $this->default_db->select($where2,'id,name');
					foreach($equipmentModel as $r){
						$datas['pie'][] = array('type'=>$r['name'],'value'=>$modelPie[$r['id']]);
					}
				}
			}
			
			//问诊数据
			for($i=6;$i>=0;$i--){
				$currentDate = date('Y-m-d');
				$date = strtotime("$currentDate -".$i." day");
				$date = date('m-d',$date);
				$inquiryTrend[$date] = $inquiryTrend2[$date] = array(
					'imageText'=>array('date'=>$date,'type'=>'imageText','value'=>0),
					'fast'=>array('date'=>$date,'type'=>'fast','value'=>0),
					'expert'=>array('date'=>$date,'type'=>'expert','value'=>0),
					'telephone'=>array('date'=>$date,'type'=>'telephone','value'=>0)
				);
			}
			$inquiryColumnar = array('imageText'=>0,'fast'=>0,'expert'=>0,'telephone'=>0);
			/*$et = strtotime(date('Y-m-d 23:59:59'));
			$_et = date('Y-m-d 23:59:59');
			$st = strtotime(date('Y-m-d 00:00:00',strtotime("$_et -6 day")));
			$where = '`status`="success" AND `addtime`>='.$st.' AND `addtime`<='.$et;*/
			$where = '`status`="success"';
			$this->default_db->load('inquiry_order');
			$inquiry = $this->default_db->select($where,'type,money,addtime','','addtime desc');
			if ($inquiry) {
				$filter = array();
				foreach($inquiry as $r){
					$inquiryDate = date('m-d',$r['addtime']);
					if ($inquiryTrend[$inquiryDate]) {
						$inquiryTrend[$inquiryDate][$r['type']]['value'] = bcadd($inquiryTrend[$inquiryDate][$r['type']]['value'],$r['money'],2);
					}
					if (!in_array($r['userid'],$filter[$inquiryDate][$r['type']])) {
						$filter[$inquiryDate][$r['type']][] = $r['userid'];
						$inquiryTrend2[$inquiryDate][$r['type']]['value']++;
					}
					$inquiryColumnar[$r['type']] = bcadd($inquiryColumnar[$r['type']],$r['money'],2);
				}
			}
			foreach($inquiryTrend as $r){
				foreach($r as $r2){
					$r2['value'] = floatval($r2['value']);
					$datas['trend3'][] = $r2;
				}
			}
			foreach($inquiryTrend2 as $r){
				foreach($r as $r2){
					$datas['trend4'][] = $r2;
				}
			}
			$inquiryType = array('imageText'=>'图文问诊','fast'=>'图文急诊','expert'=>'找医生(含专家)','telephone'=>'快捷电话');
			foreach($inquiryColumnar as $k=>$r){
				$datas['columnar'][] = array('type'=>$inquiryType[$k],'value'=>floatval($r));
			}
			
			$status = 1;
			$jsonData = array('status'=>$status,'data'=>array('datas'=>$datas));
			exit(json_encode($jsonData));
		}
		$today = date('Y-m-d');
		$yesterday = date('Y-m-d',strtotime("$today -1 day"));
		include template('dataManage','index');
		//$this->statistics();
	}
	public function statistics(){
		if ($this->ajax) {
			$datas = array();
			$type = $_POST['type'];
			$date = $_POST['date'];
			$date2 = explode(' - ',$date);
			$st = strtotime($date2[0]);
			$et = strtotime($date2[1]);
			//var_dump($date2);die;
			if ('all' == $type || 'user' == $type){
				$this->default_db->load('member');
				$userTotal = $this->default_db->count(1);
				$where = '`regdate`>'.$st.' AND `regdate`<'.$et;
				$userReg = $this->default_db->count($where);
				$where2 = '`add_time`>='.$st.' AND `add_time`<='.$et;
				$sql = 'SELECT DISTINCT userid from drcms_online_detail where '.$where2;
				$userLogin = $this->default_db->query($sql);
				$userLogin = $this->default_db->fetch_array();
				$userLogin = count($userLogin);
				/*$where2 = '`last_time`>'.$st.' AND `last_time`<'.$et;
				$this->default_db->load('online_users');
				$userLogin = $this->default_db->count($where2);*/
				
				//在线用户
				$currentTime = time();
				$userOnline = $userOffline3 = $userOffline7 = 0;
				$st2 = strtotime(date('Y-m-d 00:00:00'));
				$et2 = strtotime(date('Y-m-d 23:59:59'));
				//$where3 = '`last_time`>'.$st2.' AND `last_time`<'.$et2;
				$where3 = '`last_time`>'.($currentTime-300);
				$this->default_db->load('online_users');
				$online = $this->default_db->select($where3,'userid,last_time');
				//var_dump($online);die;
				foreach($online as $r){
					//if (300 > (time() - $r['last_time'])) $userOnline++;
					$userOnline++;
				}
				$day3 = 3*24*60*60;
				$day7 = 7*24*60*60;
				$where4 = '`last_time`<='.($currentTime - $day3);
				$userOffline = $this->default_db->select($where4,'userid,last_time');
				//var_dump($userOffline);die;
				foreach($userOffline as $r){
					if ($day7<=($currentTime - $r['last_time'])) {
						$userOffline7++;
					} else if ($day3<=($currentTime - $r['last_time'])) {
						$userOffline3++;
					}
				}
				
				//var_dump();die;
				$datas['user'] = array(
					'userTotal'=>intval($userTotal),
					'userOnline'=>intval($userOnline),
					'userOffline3'=>intval($userOffline3),
					'userOffline7'=>intval($userOffline7),
					'userReg'=>intval($userReg),
					'userLogin'=>intval($userLogin),
				);
			}
			
			if ('all' == $type || 'device' == $type){
				$where = '`create_time`>'.$st.' AND `create_time`<'.$et;
				$this->default_db->load('measure_data');
				$measureData = $this->default_db->select($where);
				$categorys = array(2=>array(),3=>array(),5=>array(),6=>array(),8=>array());
				foreach($measureData as $r){
					if (0<$r['hardwareid']&&!in_array($r['userid'],$categorys[$r['hardwareid']])) $categorys[$r['hardwareid']][] = $r['userid'];
				}
				$sql = 'SELECT DISTINCT userid from drcms_measure_data where '.$where;
				$deviceUse = $this->default_db->query($sql);
				$deviceUse = $deviceUse->num_rows;
				
				$where2 = '`createtime`>'.$st.' AND `createtime`<'.$et;
				$sql = 'SELECT DISTINCT userid from drcms_alarm where '.$where2;
				$deviceAlarm = $this->default_db->query($sql);
				$deviceAlarm = $deviceAlarm->num_rows;
				$datas['device'] = array(
					'deviceUse'=>intval($deviceUse),
					'deviceAlarm'=>intval($deviceAlarm),
					'deviceCategory'=>$categorys,
				);
			}
			
			if ('all' == $type || 'device2' == $type){
			
			}
			
			if ('all' == $type || 'inquiry' == $type){
				$where = '`addtime`>'.$st.' AND `addtime`<'.$et.' AND `transaction_status` = "success"';
				$this->default_db->load('inquiry_order');
				$order = $this->default_db->select($where,'id,out_trade_no,userid,amount,money,status,transaction_status,transaction_fee');
				$inquiryNumber = $inquiryAmount = $inquiryMoney = $inquiryRefundNumber = $inquiryRefundMoney = 0;
				foreach($order as $r){
					$inquiryNumber++;
					$inquiryAmount += $r['money'];
					$inquiryMoney += $r['transaction_fee'];
					if (in_array($r['status'],array('waitRefund','refundSuccess'))) {
						$inquiryRefundNumber++;
						$inquiryRefundMoney += $r['transaction_fee'];
					}
				}
				$datas['inquiry'] = array(
					'inquiryNumber'=>intval($inquiryNumber),
					'inquiryAmount'=>number_format($inquiryAmount,2),
					'inquiryMoney'=>number_format($inquiryMoney,2),
					'inquiryRefundNumber'=>intval($inquiryRefundNumber),
					'inquiryRefundMoney'=>number_format($inquiryRefundMoney,2),
				);
			}
			
			if ('all' == $type || 'healthy' == $type){
				$where = '`addtime`>'.$st.' AND `addtime`<'.$et.' AND `pay_status` = "success"';
				$this->default_db->load('healthy_order');
				$order = $this->default_db->select($where,'id,orderid,type,money,status,pay_money,pay_status');
				$healthyNumber = $healthyAmount = $healthyMoney = 0;
				$healthyBs = $healthyBp = array();
				foreach($order as $r){
					$healthyNumber++;
					$healthyAmount += $r['money'];
					if ('success' == $r['pay_status']) $healthyMoney += $r['pay_money'];
					switch($r['type']){
						case 'bp':
							if (!in_array($r['userid'],$healthyBp)) $healthyBp[] = $r['userid'];
							break;
						case 'bs':
							if (!in_array($r['userid'],$healthyBs)) $healthyBs[] = $r['userid'];
							break;
					}
				}
				$datas['healthy'] = array(
					'healthyNumber'=>intval($healthyNumber),
					'healthyAmount'=>number_format($healthyAmount,2),
					'healthyMoney'=>number_format($healthyMoney,2),
					'healthyBs'=>$healthyBs,
					'healthyBp'=>$healthyBp,
				);
			}
			
			if ('all' == $type || 'shop' == $type){
				pc_base::load_app_class('itfApi','interface',0);
				$itfApi = new itfApi();
				//var_dump(json_encode($scopeProduct,JSON_FORCE_OBJECT));die;
				$param = $itfApi->filterParam(array('st'=>$st,'et'=>$et));
				$token = $itfApi->getToken();
				$param['appid'] = $itfApi->appid;
				$param['token'] = $token['token'];
				$param['timeStamp'] = time();
				$param['nonceStr'] = $itfApi->getNonceStr();
				$sign = $itfApi->sign($param);
				$param['sign'] = $sign;
				$url = 'https://shop.yjxun.cn/?m=apiCenter&c=datas&a=getStatistics2';
				$result = $itfApi->postCurl($param,$url);
				//var_dump($result);die;
				$result = json_decode($result,true);
				//var_dump($result);die;
				$datas['shop'] = $result['data']['datas'];
			}
			
			if ('all' == $type || 'cs' == $type){
				$csUser = $csNumber = $csReply = $csWaitReply = 0;
				$where = '`addtime`>'.$st.' AND `addtime`<'.$et;
				$this->default_db->load('csc_chatdata');
				$chat = $this->default_db->select($where,'id,cid,adminid,userid');
				$cids = array();
				foreach($chat as $r){
					if (!in_array($r['cid'],$cids)) $cids[] = $r['cid'];
				}
				$where4 = array('id'=>array('in',$cids));
				$this->default_db->load('csc_visitwait');
				$conversate = $this->default_db->select($where4,'id,userid,aunread');
				$csMember = array();
				foreach($conversate as $r){
					$csNumber++;
					if (!in_array($r['userid'],$csMember)) $csMember[] = $r['userid'];
					if (0<$r['aunread']) {
						$csWaitReply++;
					} else {
						$csReply++;
					}
				}
				if ($csMember) $csUser = count($csMember);
				//$where2 = '`addtime`>'.$st.' AND `addtime`<'.$et;
				$this->default_db->load('guestbook');
				$guestbook = $this->default_db->select($where,'guestid,name');
				//var_dump($guestbook);die;
				$csGuestUser = $csGuestNumber = $csGuestDeal = $csGuestWaitDeal = 0;
				$guestMember = $dealMember = $waitDealMember = array();
				foreach($guestbook as $r){
					$csGuestNumber++;
					if ($r['name']&&!in_array($r['name'],$guestMember)) $guestMember[] = $r['name'];
					if ($r['reply']) {
						$csGuestDeal++;
					} else {
						$csGuestWaitDeal++;
					}
					/*if ($r['reply']) {
						if ($r['name']&&!in_array($r['name'],$dealMember)) $dealMember[] = $r['name'];
					} else {
						if ($r['name']&&!in_array($r['name'],$waitDealMember)) $waitDealMember[] = $r['name'];
					}*/
				}
				if ($guestMember) $csGuestUser = count($guestMember);
				
				//$where3 = '`addtime`>'.$st.' AND `addtime`<'.$et;
				$this->default_db->load('auth_return_visit');
				$visit = $this->default_db->select($where,'id,result');
				//var_dump($visit);die;
				foreach($visit as $r){
					$csVisitNumber++;
					if ('success' == $r['result']) {
						$csVisitSucc++;
					} else {//暂定除成功回访，其他均为失败
						$csVisitFail++;
					}
				}
				$datas['cs'] = array(
					'csUser'=>$csUser,
					'csNumber'=>intval($csNumber),
					'csReply'=>intval($csReply),
					'csWaitReply'=>intval($csWaitReply),
					'csGuestUser'=>intval($csGuestUser),
					'csGuestNumber'=>intval($csGuestNumber),
					'csGuestDeal'=>intval($csGuestDeal),
					'csGuestWaitDeal'=>intval($csGuestWaitDeal),
					'csVisitNumber'=>intval($csVisitNumber),
					'csVisitSucc'=>intval($csVisitSucc),
					'csVisitFail'=>intval($csVisitFail),
				);
			}
			$status = 1;
			$jsonData = array(
				'status'=>$status,
				'data'=>array('datas'=>$datas)
			);
			exit(json_encode($jsonData));
		}
		include template('dataManage','statistics');
	}
	public function memberStatistics(){
		if ($this->ajax) {
			$datas = array('number'=>0,'regNumber'=>0,'activeNumber'=>0,'onlineNumber'=>0,'regNumberYesterday'=>0,'activeNumberYesterday'=>0);
			$this->default_db->load('member');
			$datas['number'] = $this->default_db->count(1);
			$currentDate = date('Y-m-d');
			
			$et = strtotime(date('Y-m-d 23:59:59'));
			$_et = date('Y-m-d 23:59:59');
			$st = strtotime(date('Y-m-d 00:00:00',strtotime("$_et -1 day")));
			
			$where = '`regdate`>='.$st.' AND `regdate`<='.$et;
			$regMember = $this->default_db->select($where,'userid,regdate');
			if ($regMember) {
				foreach($regMember as $r){
					$regDate = date('Y-m-d',$r['regdate']);
					if ($regDate == date('Y-m-d')) {
						$datas['regNumber']++;
					} else {
						$datas['regNumberYesterday']++;
					}
				}
			}
			
			$where2 = '`add_time`>='.$st.' AND `add_time`<='.$et;
			$this->default_db->load('online_detail');
			$activeMember = $this->default_db->select($where2,'userid,add_time');
			if ($activeMember) {
				$dateMember = array();
				foreach($activeMember as $r){
					$createDate = date('Y-m-d',$r['add_time']);
					if ($createDate == date('Y-m-d')) {
						if (!in_array($r['userid'],$dateMember['today'])) $dateMember['today'][] = $r['userid'];
					} else {
						if (!in_array($r['userid'],$dateMember['yesterday'])) $dateMember['yesterday'][] = $r['userid'];
					}
				}
				$datas['activeNumber'] = count($dateMember['today']);
				$datas['activeNumberYesterday'] = count($dateMember['yesterday']);
			}
			
			//在线用户
			$currentTime = time();
			$where3 = '`last_time`>'.($currentTime-300);
			$this->default_db->load('online_users');
			$datas['onlineNumber'] = $this->default_db->count($where3);
			
			$sex = array(37=>array('item'=>'男','count'=>0,'percent'=>0),38=>array('item'=>'女','count'=>0,'percent'=>0),39=>array('item'=>'保密','count'=>0,'percent'=>0));
			$age = array(array('item'=>'18岁以下','count'=>0,'percent'=>0),array('item'=>'18岁到25岁','count'=>0,'percent'=>0),array('item'=>'26岁到35岁','count'=>0,'percent'=>0),array('item'=>'36岁到45岁','count'=>0,'percent'=>0),array('item'=>'46岁到60岁','count'=>0,'percent'=>0),array('item'=>'60岁以上','count'=>0,'percent'=>0));
			$terminal = array(array('item'=>'未知','count'=>0,'percent'=>0),array('item'=>'Android','count'=>0,'percent'=>0),array('item'=>'iPhone','count'=>0,'percent'=>0));
			$this->default_db->load('member_information');
			$information = $this->default_db->select(1);
			if ($information) {
				foreach($information as $r){
					$sex[$r['sex']]['count']++;
					if (60<$r['age']) {
						$index = 5;
					} else if (45<$r['age']) {
						$index = 4;
					} else if (35<$r['age']) {
						$index = 3;
					} else if (25<$r['age']) {
						$index = 2;
					} else if (17<$r['age']) {
						$index = 1;
					} else {
						$index = 0;
					}
					$age[$index]['count']++;
					$terminal[$r['terminal']]['count']++;
				}
				$effectiveSex = 0;
				foreach($sex as $k=>$r){
					$sex[$k]['percent'] = round(bcdiv($r['count'],$datas['number'],4),4);
					$effectiveSex += $r['count'];
				}
				$unknownSex = $datas['number'] - $effectiveSex;
				$sex[0] = array('item'=>'未知','count'=>$unknownSex,'percent'=>round(bcdiv($unknownSex,$datas['number'],4),4));
				
				$effectiveAge = 0;
				foreach($age as $k=>$r){
					$age[$k]['percent'] = round(bcdiv($r['count'],$datas['number'],4),4);
					$effectiveAge += $r['count'];
				}
				$unknownAge = $datas['number'] - $effectiveAge;
				$age[0]['count'] += $unknownAge;
				$age[0]['percent'] = round(bcdiv($age[0]['count'],$datas['number'],4),4);
				
				$effectiveTerminal = 0;
				foreach($terminal as $k=>$r){
					$terminal[$k]['percent'] = round(bcdiv($r['count'],$datas['number'],4),4);
					$effectiveTerminal += $r['count'];
				}
				$unknownTerminal = $datas['number'] - $effectiveTerminal;
				$terminal[0] = array('item'=>'未知','count'=>$unknownTerminal,'percent'=>round(bcdiv($unknownTerminal,$datas['number'],4),4));
			}
			$sex = array_values($sex);
			$terminal = array_values($terminal);
			$status = 1;
			$jsonData = array(
				'status'=>$status,
				'data'=>array('datas'=>$datas,'sex'=>$sex,'age'=>$age,'terminal'=>$terminal)
			);
			exit(json_encode($jsonData));
		}
		include template('dataManage','memberStatistics');
	}
	public function equipmentStatistics(){
		if ($this->ajax) {
			$datas = array('todyNumber'=>0,'todayWarning'=>0,'yesterdayNumber'=>0,'yesterdayWarning'=>0);
			$et = strtotime(date('Y-m-d 23:59:59'));
			$_et = date('Y-m-d 23:59:59');
			$st = strtotime(date('Y-m-d 00:00:00',strtotime("$_et -1 day")));
			$where = '`create_time`>='.$st.' AND `create_time`<='.$et;
			$where1 = $where . ' AND `source`=1 ';
			$this->default_db->load('health_data');
			$_datas = $this->default_db->select($where1,'user_id,create_time');
			if ($_datas) {
				$filterUser = array();
				foreach($_datas as $r){
					$day = date('Y-m-d',$r['create_time']);
					if (in_array($r['user_id'],$filterUser[$day])) continue;
					$filterUser[$day][] = $r['user_id'];
					if ($day == date('Y-m-d')) {
						$datas['todayNumber']++;
					} else {
						$datas['yesterdayNumber']++;
					}
				}
			}
			$where2 .= $where.' AND `type`=1';
			$this->default_db->load('warning');
			$_datas = $this->default_db->select($where2,'user_id,create_time');
			if ($_datas) {
				$filterUser = array();
				foreach($_datas as $r){
					$day = date('Y-m-d',$r['create_time']);
					if (in_array($r['user_id'],$filterUser[$day])) continue;
					$filterUser[$day][] = $r['user_id'];
					if ($day == date('Y-m-d')) {
						$datas['todayWarning']++;
					} else {
						$datas['yesterdayWarning']++;
					}
				}
			}
			$where2 = '`parent`>0';
			$this->default_db->load('equipment_category');
			$_category = $this->default_db->select($where2,'id,name');
			if ($_category) {
				foreach($_category as $r){
					$category[$r['id']] = array('item'=>$r['name'],'count'=>0,'count2'=>0,'user'=>array(),'percent'=>0);
				}
			}
			$where3 = 1;
			$this->default_db->load('equipment_model');
			$_model = $this->default_db->select($where3,'id,name');
			if ($_model) {
				foreach($_model as $r){
					$model[$r['id']] = array('item'=>$r['name'],'count'=>0,'count2'=>0,'user'=>array(),'percent'=>0);
				}
			}
			$where4 = 1;
			$this->default_db->load('equipment');
			$equipment = $this->default_db->select($where4,'id,category_id,model_id,user_id');
			$equipmentNumber = 0;
			foreach($equipment as $r){
				$equipmentNumber++;
				if (in_array($r[''],$effectiveMember)) {
					$effectiveMember[] = $r['user_id'];
		
				}
				if ($category[$r['category_id']]) $category[$r['category_id']]['count']++;
				if (!in_array($r['user_id'],$category[$r['category_id']]['user'])) $category[$r['category_id']]['user'][] = $r['user_id'];
				if ($model[$r['model_id']]) $model[$r['model_id']]['count']++;
				if (!in_array($r['user_id'],$model[$r['model_id']]['user'])) $model[$r['model_id']]['user'][] = $r['user_id'];
			}
			if (0<$equipmentNumber) {
				foreach($category as $k=>$r){
					$category[$k]['count2'] = count($r['user']);
					unset($category[$k]['user']);
					$category[$k]['percent'] = round(bcdiv($r['count'],$equipmentNumber,4),4);
				}
				foreach($model as $k=>$r){
					$model[$k]['count2'] = count($r['user']);
					unset($model[$k]['user']);
					$model[$k]['percent'] = round(bcdiv($r['count'],$equipmentNumber,4),4);
				}
				$category = array_values($category);
				$model = array_values($model);
			}
			$status = 1;
			$jsonData = array(
				'status'=>$status,
				'data'=>array('datas'=>$datas,'category'=>$category,'model'=>$model)
			);
			exit(json_encode($jsonData));
		}
		include template('dataManage','equipmentStatistics');
	}
	public function webStatistics(){
		if ($this->ajax) {
			$datas = array('todyPV'=>0,'todayUV'=>0,'yesterdayPV'=>0,'yesterdayUV'=>0);
			$et = strtotime(date('Y-m-d 23:59:59'));
			$_et = date('Y-m-d 23:59:59');
			$st = strtotime(date('Y-m-d 00:00:00',strtotime("$_et -1 day")));
			$where = '`add_time`>='.$st.' AND `add_time`<='.$et;
			$this->default_db->load('online_detail');
			$_datas = $this->default_db->select($where,'id,userid,add_time','','add_time asc');
			if ($_datas) {
				$filterUser = array();
				foreach($_datas as $r){
					$day = date('Y-m-d',$r['add_time']);
					if ($day == date('Y-m-d')) {
						$datas['todayPV']++;
						$key = 'todayUV';
					} else {
						$datas['yesterdayPV']++;
						$key = 'yesterdayUV';
					}
					if (in_array($r['userid'],$filterUser[$day])) continue;
					$filterUser[$day][] = $r['userid'];
					$datas[$key]++;
				}
			}
			$where = 1;
			$this->default_db->load('online_detail');
			$_datas = $this->default_db->select($where,'userid,title,duration,source,add_time','','add_time desc');
			if ($_datas) {
				$webNumber = 0;
				$filterUser = array();
				$terminals = array('unknown','Wechat','Android','iPhone');
				foreach($_datas as $r){
					$webNumber++;
					/*if ($web[$r['title']]) {
						$web[$r['title']]['count']++;
						if (!in_array($r['userid'],$web[$r['title']]['user'])) $web[$r['title']]['user'][] = $r['userid'];
					} else {
						$web[$r['title']] = array('item'=>$r['title'],'count'=>1,'user'=>array($r['userid']),'percent'=>0);
					}*/
					if ($r['title']) {
						$day = date('Y-m-d',$r['add_time']);
						if ($_web2[$day][$r['title']]) {
							$_web2[$day][$r['title']]['pv']++;
							if (!in_array($r['userid'],$_web2[$day][$r['title']]['user'])) $_web2[$day][$r['title']]['user'][] = $r['userid'];
						} else {
							$_web2[$day][$r['title']] = array('date'=>$day,'name'=>$r['title'],'pv'=>1,'uv'=>0,'user'=>array($r['userid']));
						}
					}
					
					if ($terminal[$r['source']]) {
						$terminal[$r['source']]['count']++;
						if (!in_array($r['userid'],$terminal[$r['source']]['user'])) $terminal[$r['source']]['user'][] = $r['userid'];
					} else {
						$terminal[$r['source']] = array('item'=>$terminals[$r['source']],'count'=>1,'user'=>array($r['userid']),'percent'=>0);
					}
				}
				//var_dump($_web2);die;
				foreach($_web2 as $r){
					foreach($r as $r2){
						$r2['uv'] = count($r2['user']);
						unset($r2['user']);
						$web2[] = $r2;
					}
				}
				//$web = array_values($web);
				//$web2 = array_values($web2);
				$terminal = array_values($terminal);
				/*foreach($web as $k=>$r){
					$web[$k]['count2'] = count($r['user']);
					unset($web[$k]['user']);
					$web[$k]['percent'] = round(bcdiv($r['count'],$webNumber,4),4);
				}*/
				foreach($terminal as $k=>$r){
					$terminal[$k]['count2'] = count($r['user']);
					unset($terminal[$k]['user']);
					$terminal[$k]['percent'] = round(bcdiv($r['count'],$webNumber,4),4);
				}
			}
			$status = 1;
			$jsonData = array(
				'status'=>$status,
				'data'=>array('datas'=>$datas,'web'=>$web,'web2'=>$web2,'terminal'=>$terminal)
			);
			exit(json_encode($jsonData));
		}
		include template('dataManage','webStatistics');
	}
	public function productStatistics(){
		if ($this->ajax) {
			pc_base::load_app_class('itfApi','interface',0);
			$itfApi = new itfApi();
			$param = $itfApi->filterParam(array());
			$token = $itfApi->getToken();
			$param['appid'] = $itfApi->appid;
			$param['token'] = $token['token'];
			$param['timeStamp'] = time();
			$param['nonceStr'] = $itfApi->getNonceStr();
			$sign = $itfApi->sign($param);
			$param['sign'] = $sign;
			$url = 'https://shop.yjxun.cn/?m=interface&c=datas&a=getProductStatistics';
			$result = $itfApi->postCurl($param,$url);
			//var_dump($result);die;
			$result = json_decode($result,true);
			$datas = $result['data']['datas'];
			$status = 1;
			$jsonData = array(
				'status'=>$status,
				'data'=>array('datas'=>$datas)
			);
			exit(json_encode($jsonData));
		}
		include template('dataManage','productStatistics');
	}
	public function areaStatistics(){
		if ($this->ajax) {
			$where = 1;
			$areaLevel = 0;
			if (isset($_POST['province'])&&$_POST['province']) {
				$province = $_POST['province'];
				$where .= ' AND `province`='.$province;
				$areaLevel = 1;
			}
			if (isset($_POST['city'])&&$_POST['city']) {
				$city = $_POST['city'];
				$where .= ' AND `city`='.$city;
				$areaLevel = 2;
			}
			if (isset($_POST['district'])&&$_POST['district']) {
				$district = $_POST['district'];
				$where .= ' AND `district`='.$district;
				$areaLevel = 3;
			}
			if (isset($_POST['street'])&&$_POST['street']) {
				$street = $_POST['street'];
				$where .= ' AND `street`='.$street;
				$areaLevel = 4;
			}
			if (isset($_POST['community'])&&$_POST['community']) {
				$community = $_POST['community'];
				$where .= ' AND `community`='.$community;
				$areaLevel = 4;
			}
			$this->default_db->load('member_information');
			$_datas = $this->default_db->select($where,'user_id,area_type,province,city,district,street,community');
			
			//总人数
			if (0==$areaLevel) $this->default_db->load('member');
			$count = $this->default_db->count($where);
			
			if ($_datas) {
				$status = 1;
				$total = 0;
				$area_codes = array();
				$keys = array('province','city','district','street','community');
				$areaLevelKey = $keys[$areaLevel]?:'province';
				foreach($_datas as $r){
					$key = 2==$r['area_type']?7:$r[$areaLevelKey];
					if (0<$key&&!in_array($key,$area_codes)) $area_codes[] = $key;
					if (!$datas[$key]) {
						$datas[$key]['area_code'] = $key;
					}
					$datas[$key]['number']++;
				}
				//var_dump($area_codes);die;
				$where2 = array('code'=>array('in',$area_codes));
				$this->default_db->load('area');
				$_area = $this->default_db->select($where2,'code,name');
				if ($_area) {
					foreach($_area as $r){
						$area[$r['code']] = $r;	
					}
				}
				foreach($datas as $k=>$r){
					$rate = floatval(bcdiv(strval($r['number']),strval($count),4));//bcdiv(strval($r['number']),strval($count),4)
					//var_dump($rate);
					$datas[$k]['percent'] = round(($rate * 100),2);
					$areaName = $area[$k]['name']?:(7==$k?'港澳地区':'未知');
					$datas[$k]['name'] = $areaName;
					$datas2[] = array('item'=>$areaName,'count'=>$r['number'],'percent'=>$rate);
				}
			} else {
				$status = 0;
				$datas = array();
				$datas2 = array('item'=>'暂无数据','count'=>1,'percent'=>1);
			}
			$jsonData = array(
				'status'=>$status,
				'data'=>array('datas'=>$datas,'datas2'=>$datas2,'count'=>$count),
			);
			exit(json_encode($jsonData));
		}
		include template('dataManage','areaStatistics');
	}
	public function areaDistStatistics(){
		if ($this->ajax) {
			$type = intval($_POST['type']);
			$where = '`city`>0';
			switch($type){
				case 1:
					break;
				case 2:
					$where .= ' AND `is_warning`=1';
					break;
				case 3:
					$where .= ' AND `chronic` <> ""';
					break;
			}
			$this->default_db->load('member_information');
			$_datas = $this->default_db->select($where,'user_id,province,city,district');
			if ($_datas) {
				$status = 1;
				$codes = array();
				foreach($_datas as $r){
					if (0==$r['city']) continue;
					$code = $r['city'];
					if (!in_array($r[''],$codes)) $codes[] = $code;
					$datas[$code]['value']++;
				}
				if ($codes) {
					$where2 = array('code'=>array('in',$codes));
					$this->default_db->load('area');
					$_area = $this->default_db->select($where2,'code,name,lng,lat');
					foreach($_area as $r){
						$area[$r['code']] = $r;
						$location[$r['name']] = array(floatval($r['lng']),floatval($r['lat']));
					}
				}
				foreach($datas as $k=>$r){
					$datas[$k]['name'] = $area[$k]['name'];
				}
				$datas = array_values($datas);
				//var_dump($datas);die;
			} else {
				$status = 0;
				$datas = $location = array();
			}
			$jsonData = array(
				'status'=>$status,
				'data'=>array('datas'=>$datas,'location'=>$location),
			);
			exit(json_encode($jsonData));
		}
		include template('dataManage','areaDistStatistics');
	}
	public function areaClassifyStatistics(){
		if ($this->ajax) {
			$jsonData = array(
				'status'=>$status,
				'data'=>array('datas'=>$datas),
			);
			exit(json_encode($jsonData));
		}
		include template('dataManage','areaClassifyStatistics');
	}
	public function getMenu2(){
		$where = '`state`=1 AND `level` < 3 AND `display`=1';
		$authData = $this->authData;
		$isPriv = $authData['roleid'] > 1?1:0;//是否开启权限过滤
		$menuids = array();

		$privManage = new privManage();
		$authRole = $privManage->getRole(array($authData['roleid']));
		$authRole = $authRole[$authData['roleid']];
		if ($authRole['system']) {
			
		} else {
			$where2 = '`roleid` = '.$authData['roleid'];
			$this->default_db->load('auth_role_priv');
			$rolePriv = $this->default_db->select($where2,'menuid');
			if (empty($rolePriv)) exit('{"status":0}');
			foreach($rolePriv as $r){
				$menuids[] = $r['menuid'];
			}
			
			if ($menuids) {
				$menuids = implode(',',$menuids);
				$where .= ' AND `id` in ('.$menuids.')';
			} else {
				exit('{"status":0}');
			}
		}
		
		$this->default_db->load('auth_menu');
		$_menu = $this->default_db->select($where,'*','','priority asc');
		$datas = $menu = array();
		if ($_menu) {
			$status = 1;
			foreach($_menu as $r){
				if (0 == $r['parent']) {
					if ($menu[$r['id']]) {
						$menu[$r['id']] = array_merge($menu[$r['id']],$r);
					} else {
						$menu[$r['id']] = $r;
					}
					if ($datas[$r['priority']]) {
						$datas[] = $r['id'];
					} else {
						$datas[$r['priority']] = $r['id'];
					}
					
				} else {
					$priority = intval($r['priority']);
					if ($menu[$r['parent']]['items'][$priority]) {
						$menu[$r['parent']]['items'][] = $r;
					} else {
						$menu[$r['parent']]['items'][$priority] = $r;
					}
				}
			}
		}

		$jsonData  = array(
			'status'=>$status,
			'data'=>array('datas'=>$datas,'menu'=>$menu,'icons'=>$icons),
		);
		exit(json_encode($jsonData));

	}
	public function cnStatistics(){
        include template('dataManage','cnStatistics');
    }
}



?>