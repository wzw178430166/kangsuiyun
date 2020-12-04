<?php
defined('IN_drcms') or exit('No permission resources.');
class index {
	public $default_db;
	public $template = 'nutrition';
	public $style = 'nutrition';
	public $ajax = 0;
	public function __construct() {
		$this->default_db = pc_base::load_model('default_model');
		$this->ajax = intval($_GET['ajax']);
	}
	public function init() {
	
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
			//echo template($this->template,'index',$this->style);die;
		include template($this->template,'index',$this->style);
		//$this->statistics();
    }
    public function getMenu(){
		$where = '`state`=1 AND `level` < 3 AND `level` > 0 AND `display`=1';
		
		$isPriv = $this->roleid > 1?1:0;//是否开启权限过滤
		$menuids = array();

		$currency = new currency();
		$res = $currency->getRole(['roleid'=>$this->roleid]);
		$role = $res['data'][$this->roleid];
		if ($role['system']) {
			
		} else {
			$where2 = '`roleid` = '.$this->roleid;
			$this->default_db->load('auth_menu_nutrition');
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

	    //var_dump($where);
		$this->default_db->load($this->menu_tb);
		$_menu = $this->default_db->select($where,'*','','priority desc');
		//var_dump($this->menu_tb);
		$datas = $menu = array();
		
		if ($_menu) {
			$status = 1;
			$parent_menu_id = $_COOKIE['parent_menu_id']; 
			$parent_menu_id = $this->system_style;
			
			foreach($_menu as $r){
				if($parent_menu_id!=''){
					//var_dump($r);$r['level']=='1'  && 
					if(strpos($r['grade'],','.$parent_menu_id.',') === false){
						continue;
					}
				}
				if ($r['level']==1) {
					if ($menu[$r['id']]) {
						$menu[$r['id']] = array_merge($menu[$r['id']],$r);
					} else {
						$menu[$r['id']] = $r;
					}
					$datas[] = $r['id'];
					/*if ($datas[$r['priority']]) {
						$datas[] = $r['id'];
					} else {
						$datas[$r['priority']] = $r['id'];
					}*/
					
				} else {
					$priority = intval($r['priority']);
					if ($menu[$r['parent']]['items'][$priority]) {
						$menu[$r['parent']]['items'][] = $r;
					} else {
						$menu[$r['parent']]['items'][$priority] = $r;
					}
				}
			}
		} else {
		    $status = 0;
		}

		$jsonData  = array(
			'status'=>$status,
			'data'=>array('datas'=>$datas,'menu'=>$menu),
			'parent_menu_id'=>$parent_menu_id
		);
		exit(json_encode($jsonData));

	}
}

?>