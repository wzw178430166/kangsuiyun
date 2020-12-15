<?php
defined('IN_drcms') or exit('No permission resources.');
class index {
	public $default_db;
	public $template = 'nutrition';
	public $style = 'nutrition';
	public $page = 1;
	public $pageSize = 20;
	public $ajax = 0;
	public function __construct() {
		$this->default_db = pc_base::load_model('default_model');
		$this->ajax = intval($_GET['ajax']);
		$this->page = isset($_GET['page'])&&intval($_GET['page'])?$_GET['page']:1;
	}

	public function init(){
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
						case 'offline3':
						case 'healthWarning':
							$queryWhere = '`create_time`>'.$st2.' AND `create_time`<'.$et2;
							$sql = 'select DISTINCT user_id from drcms_warning where '.$queryWhere;
							$this->default_db->query($sql);
							$rq = $this->default_db->fetch_array();
							foreach($rq as $r){
								$queryUserids[] = $r['user_id'];
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
			$this->default_db->setting('nut');
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
					//$r['userid'] = '666';
					$datas[$r['userid']] = $r;
				}
				//var_dump($datas);die;
				if ($userids) {
					$this->default_db->setting('nut');
					$where = array('userid'=>array('in',$userids));
					$this->default_db->load('patient');
					$model = $this->default_db->select($where,'userid,sex,age,province,city,area,street,community,address,lng,lat,device_text');
					//var_dump($model);
					$sexs = array('保密','男','女');
					foreach($model as $r){
						if (is_array($r)&&$datas[$r['userid']]) {
							$r['sexStr'] = $sexs[$r['sex']]?:'保密';
						//	$datas[$r['sexStr']]=$r['sexStr'];
							$datas[$r['userid']] = array_merge($datas[$r['userid']],$r);
						}
					
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
		include template($this->template,'index',$this->style);
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
				$member = $this->default_db->get_one($where,'userid');
				if ($member) exit('{"status":0,"erro":"账号已存在"}');
				$info = '`username` = "'.$username.'"';
				$where = '`userid` = '.$userid;
				$member = $this->default_db->get_one($where,'userid,phpssouid');
				$status = $this->default_db->update($info,$where);  
				if ($status) { 
					//sso
					$this->default_db->setting('nut');
					$this->default_db->load('sso_members');
					$this->default_db->update($info,'`uid`='.$member['phpssouid']);
					//用户信息
					$this->member_db->set_model(10);
					$this->member_db->update($model,$where);
				}
			} else {
				$datas = array('roleid'=>13,'sectorid'=>1,'username'=>$username,'password'=>'000000','nickname'=>$model['realname'],'mobile'=>$username,'state'=>1,'model'=>$model);
				pc_base::load_app_class('user','nutrition',0);
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
					$this->default_db->setting('nut');
					$this->default_db->load('member');
					$where = '`userid` = '.$userid;
					$member = $this->default_db->get_one($where);
					//$this->default_db->set_model(10);
					$model = $this->default_db->get_one($where,'realname,sex,age,idcard,province,city,area,street,community,address,lng,lat');
					if (is_array($model)) $member = array_merge($model,$member);
				}
				$status = $member?1:0;
				$jsonData  = array(
					'status'=>$status,
					'data'=>array('member'=>$member),
				);
				exit(json_encode($jsonData));
			}
			include template('nutrition','doMember');
		}
	}


}

?>