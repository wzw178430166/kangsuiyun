<?php
defined('IN_drcms') or exit('No permission resources.');
//pc_base::load_sys_class('form', '', 0);
//pc_base::load_app_class('authority','dataManage',0);
pc_base::load_app_class('currency','authed',0);
class data {
	public $default_db;
	public $roleid = 0;
	public $ajax = 0;//异步访问标识
	public $pageSize = 10;//每页条数
	public function __construct() {
		$this->default_db = pc_base::load_model('default_model');
		$this->page = isset($_GET['page'])&&$_GET['page']?intval($_GET['page']):1;
		$this->ajax = intval($_GET['ajax']);
		$this->roleid = intval(param::get_cookie('roleid'));
		
		$this->menu_tb = 'auth_menu_all';
		$system_style = '';
		if(isset($_GET['system_style'])){
			$system_style = $_GET['system_style'];
		}else{
			$system_style = param::get_cookie('system_style');
		}
		$this->menu_flag = $system_style; 
		$this->system_style = $system_style;
		if (!empty($system_style) && 'default' != $system_style) {
		    //$this->menu_tb .= '_'.$system_style;
		}
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