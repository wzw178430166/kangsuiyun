<?php

defined('IN_drcms') or exit('No permission resources.');

pc_base::load_app_class('authority','dataManage',0);

pc_base::load_app_class('upload_public','sport',0);

class hospital {

	public $default_db,$content_db,$member_db,$authority;
	public $authData = array();
	public $ajax = 0;//异步访问标识
	public $page = 1;//每页条数
	public $pageSize = 20;//每页条数
	public $template = 'dataManage';//每页条数

	function __construct() {
		$this->authority = new authority();
		$this->authData = $this->authority->authData;
		$this->default_db = pc_base::load_model('default_model');
		$this->content_db = pc_base::load_model('content_model');
		$this->member_db = pc_base::load_model('member_model');
		$this->ajax = intval($_GET['ajax']);
		$this->page = isset($_GET['page'])?intval($_GET['page']):1;
		if ($this->ajax) {
			pc_base::load_app_class('demo','core',0);
			$demo = new demo();
		}
	}
	public function init() {
		$this->lists();
	}
    public function lists(){
		if ($this->ajax) {
			$where = 1;
			if (isset($_GET['k'])&&$_GET['k']) {
				$keyword = $_GET['k'];
				$where .= ' AND `name` like "%'.$keyword.'%"';
			}
			if (isset($_GET['level'])&&$_GET['level']) {
				$level = $_GET['level'];
				$where .= ' AND `level` = '.$level;
			}
			//var_dump($where);die;
			$this->pageSize = 10;
			$this->default_db->load('hospitals');
			$datas = $this->default_db->listinfo($where,'create_time desc',$this->page,$this->pageSize);
			empty($datas[0])&&$datas=array();
			$status = $pageCount = 0;
			$region = array();
			if ($datas) {
				$status = 1;
				$pageCount = $this->default_db->number;//数据总条数
				$codes = array();
				foreach($datas as $k=>$r){
					if (!in_array($r['province'],$codes)) $codes[] = $r['province'];
					if (!in_array($r['city'],$codes)) $codes[] = $r['city'];
					if (!in_array($r['district'],$codes)) $codes[] = $r['district'];
					$datas[$k]['create_time'] = date('Y-m-d H:i:s',$r['create_time']);
				}
				if ($codes) {
					$codes = implode(',',$codes);
					$where = '`code` in ('.$codes.')';
					$this->default_db->load('area');
					$_region = $this->default_db->select($where,'code,name');
					//var_dump($where);die;
					if ($_region) {
						foreach($_region as $r){
							$region[$r['code']] = $r;
						}
					}
				}
			}
			$jsonData = array(
				'status'=>$status,
				'data'=>array('datas'=>$datas,'region'=>$region,'pageCount'=>$pageCount)
			);
			exit(json_encode($jsonData));
		}
		include template($this->template,'hospitalList');
	}
	public function doHospital(){
		if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {
			$id = intval($_POST['id']);
			$info = $_POST['info'];
			$images = array();
			if ($_POST['img']) {
				$img = $_POST['img'];
				$alt = $_POST['alt'];
				foreach($img as $k=>$r){
					$images[] = array('url'=>$r,'alt'=>urlencode($alt[$k]));
				}
				$images = urldecode(json_encode($images));
				$info['images'] = $images;
			}
			$this->default_db->load('hospitals');
			if (0 < $id) {
				$where = '`id` = '.$id;
				$result = $this->default_db->update($info,$where);
			} else {
				$info['create_time'] = time();
				$result = $this->default_db->insert($info,true);
			}
			if ($result) {
				$status = 1;
				$erro = '保存成功';
			} else {
				$status = 0;
				$erro = '保存失败';
			}
			$jsonData = array(
				'status'=>$status,
				'erro'=>$erro,
			);
			exit(json_encode($jsonData));
		} else {
			if ($this->ajax) {
				$id = intval($_GET['id']);
				if (0<$id) {
					$where = '`id`='.$id;
					$this->default_db->load('hospitals');
					$datas = $this->default_db->get_one($where);
					if ($datas['images']) $datas['images'] = json_decode($datas['images'],true);
				}
				$status = $datas?1:0;
				$jsonData = array(
					'status'=>$status,
					'data'=>array('datas'=>$datas)
				);
				exit(json_encode($jsonData));
			}
			include template($this->template,'doHospital');
		}
	}
	public function detail(){
		if ($this->ajax) {
			$id = intval($_GET['id']);
			if (0<$id) {
				$this->default_db->load('hospitals');
				$datas = $this->default_db->get_one($where);
				if ($datas['images']) $hospital['images'] = json_decode($datas['images'],true);
			}
			$status = $hospital?1:0;
			$jsonData = array(
				'status'=>$status,
				'data'=>array('datas'=>$datas)
			);
			exit(json_encode($jsonData));
		}
		include template($this->template,'hospitalDetail');
	}
	public function delete(){
		if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {
			$ids = explode(',',$_POST['ids']);
			$ids = array_filter($ids);
			//var_dump($ids);die;
			if (!empty($ids)) {
				$this->default_db->load('hospitals');
				foreach($ids as $r){
					$where = '`id` = '.$r;
					$status = $this->default_db->delete($where);
				}	
			}
			exit('{"status":'.($status?1:0).',"erro":"'.($status?'删除成功':'删除失败').'"}');
		}
	}
	/*public function hospitalList(){
		if ($this->ajax) {
			$where = 1;
			if (isset($_GET['k'])&&$_GET['k']) {
				$keyword = $_GET['k'];
				$where .= ' AND `name` like "%'.$keyword.'%"';
			}
			if (isset($_GET['level'])&&$_GET['level']) {
				$level = $_GET['level'];
				$where .= ' AND `level` = '.$level;
			}
			//var_dump($where);die;
			$this->pageSize = 10;
			$this->default_db->load('hospitals');
			$datas = $this->default_db->listinfo($where,'id desc',$this->page,$this->pageSize);
			empty($datas[0])&&$datas=array();
			$status = $pageCount = 0;
			$region = array();
			if ($datas) {
				$status = 1;
				$pageCount = $this->default_db->number;//数据总条数
				$codes = array();
				foreach($datas as $k=>$r){
					if (!in_array($r['province'],$codes)) $codes[] = $r['province'];
					if (!in_array($r['city'],$codes)) $codes[] = $r['city'];
					if (!in_array($r['area'],$codes)) $codes[] = $r['area'];
					$datas[$k]['addtime'] = date('Y-m-d H:i:s',$r['addtime']);
				}
				if ($codes) {
					$codes = implode(',',$codes);
					$where = '`code` in ('.$codes.')';
					$this->default_db->load('area');
					$_region = $this->default_db->select($where,'code,name');
					//var_dump($where);die;
					if ($_region) {
						foreach($_region as $r){
							$region[$r['code']] = $r;
						}
					}
				}
			}
			$jsonData = array(
				'status'=>$status,
				'data'=>array('datas'=>$datas,'region'=>$region,'pageCount'=>$pageCount)
			);
			exit(json_encode($jsonData));
		}
		include template($this->template,'hospitalList');
	}
	public function doHospital(){
		if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {
			$hospitalid = intval($_POST['id']);
			$name = $_POST['name'];
			$level = $_POST['level'];
			$telephone = $_POST['telephone'];
			$region = $_POST['region'];
			$address = $_POST['address'];
			$content = $_POST['content'];
			$note = $_POST['note'];
			$info = array(
				'name'=>$name,
				'level'=>$level,
				'telephone'=>$telephone,
				'province'=>$region['province'],
				'city'=>$region['city'],
				'area'=>$region['area'],
				'address'=>$address,
				'content'=>$content,
				'note'=>$note,
			);
			$this->default_db->load('hospitals');
			if (0 < $hospitalid) {
				$where = '`id` = '.$hospitalid;
				$status = $this->default_db->update($info,$where);
			} else {
				$info['addtime'] = time();
				$status = $this->default_db->insert($info,true);
			}
			exit('{"status":'.($status?1:0).',"erro":"'.($status?'保存成功':'保存失败').'"}');
		} else {
			if ($this->ajax) {
				$hospitalid = intval($_GET['id']);
				if (0<$hospitalid) {
					$this->default_db->load('hospitals');
					$hospital = $this->default_db->get_one($where);
				}
				$status = $hospital?1:0;
				$jsonData = array(
					'status'=>$status,
					'data'=>array('hospital'=>$hospital)
				);
				exit(json_encode($jsonData));
			}
			include template($this->template,'doHospital');
		}
	}
	public function hospitalDetail(){
		if ($this->ajax) {
			$hospitalid = intval($_GET['id']);
			if (0<$hospitalid) {
				$this->default_db->load('hospitals');
				$hospital = $this->default_db->get_one($where);
			}
			$status = $hospital?1:0;
			$jsonData = array(
				'status'=>$status,
				'data'=>array('hospital'=>$hospital)
			);
			exit(json_encode($jsonData));
		}
		include template($this->template,'hospitalDetail');
	}
	public function delHospital(){
		if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {
			$ids = explode(',',$_POST['ids']);
			$ids = array_filter($ids);
			//var_dump($ids);die;
			if (!empty($ids)) {
				$this->default_db->load('hospitals');
				foreach($ids as $r){
					$where = '`id` = '.$r;
					$status = $this->default_db->delete($where);
				}	
			}
			exit('{"status":'.($status?1:0).',"erro":"'.($status?'删除成功':'删除失败').'"}');
		}
	}*/
	/*public function departments(){
		if ($this->ajax) {
			$where = 1;
			if (isset($_GET['k'])&&$_GET['k']) {
				$keyword = $_GET['k'];
				$where .= ' AND `name` like "%'.$keyword.'%"';
			}
			if (isset($_GET['parent'])&&$_GET['parent']) {
				$parent = $_GET['parent'];
				$where .= ' AND `node` like "%,'.$parent.',%"';
			}
			//var_dump($where);die;
			$this->pageSize = 10;
			$this->default_db->load('departments');
			$datas = $this->default_db->listinfo($where,'id desc',$this->page,$this->pageSize);
			empty($datas[0])&&$datas=array();
			$status = $pageCount = 0;
			$hospital = $parent = array();
			if ($datas) {
				$status = 1;
				$pageCount = $this->default_db->number;//数据总条数
				$hospitalids = $parentids = array();
				foreach($datas as $k=>$r){
					if (!in_array($r['parent'],$parentids)) $parentids[] = $r['parent'];
					if (!in_array($r['hospitalid'],$hospitalids)) $hospitalids[] = $r['hospitalid'];
				}
				if ($parentids) {
					$parentids = implode(',',$parentids);
					$where = '`id` in ('.$parentids.')';
					$_parent = $this->default_db->select($where,'id,name');
					//var_dump($where);die;
					if ($_parent) {
						foreach($_parent as $r){
							$parent[$r['id']] = $r;
						}
					}
				}
				if ($hospitalids) {
					$hospitalids = implode(',',$hospitalids);
					$where = '`id` in ('.$hospitalids.')';
					$this->default_db->load('hospitals');
					$_hospital = $this->default_db->select($where,'id,name');
					//var_dump($where);die;
					if ($_hospital) {
						foreach($_hospital as $r){
							$hospital[$r['id']] = $r;
						}
					}
				}
			}
			$jsonData = array(
				'status'=>$status,
				'data'=>array('datas'=>$datas,'parent'=>$parent,'hospital'=>$hospital,'pageCount'=>$pageCount)
			);
			exit(json_encode($jsonData));
		}
		include template($this->template,'departments');
	}
	public function doDepartments(){
		if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {
			$departmentid = intval($_POST['id']);
			$parent = intval($_POST['parent']);
			$hospitalid = intval($_POST['hospitalid']);
			$name = $_POST['name'];
			$note = $_POST['note'];
			$datas = array(
				'parent'=>$parent,
				'hospitalid'=>$hospitalid,
				'name'=>$name,
				'note'=>$note,
			);
			//var_dump($datas);die;
			$param = array('table'=>'departments','currentid'=>$departmentid,'parent'=>$parent);
			pc_base::load_app_class('category','dataManage',0);
			$category = new category();
			$status = $category->doCategory($datas,$param);
			exit('{"status":'.($status?1:0).',"erro":"'.($status?'保存成功':'保存失败').'"}');
		} else {
			if ($this->ajax) {
				$this->default_db->load('hospitals');
				$hospital = $this->default_db->select(1);
				
				$this->default_db->load('departments');
				$parent = $this->default_db->select(1);
				
				$department = array();
				$departmentid = intval($_GET['id']);
				if (0<$departmentid) {
					$where = '`id` = '.$departmentid;
					$department = $this->default_db->get_one($where);
				}
				$status = $department?1:0;
				$jsonData = array(
					'status'=>$status,
					'data'=>array('department'=>$department,'parent'=>$parent,'hospital'=>$hospital)
				);
				exit(json_encode($jsonData));
			}
			include template($this->template,'doDepartments');
		}
	}
	public function delDepartment(){
		if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {
			$ids = explode(',',$_POST['ids']);
			$ids = array_filter($ids);
			//var_dump($ids);die;
			if (!empty($ids)) {
				$this->default_db->load('departments');
				foreach($ids as $r){
					$where = '`id` = '.$r;
					$status = $this->default_db->delete($where);
				}	
			}
			exit('{"status":'.($status?1:0).',"erro":"'.($status?'删除成功':'删除失败').'"}');
		}
	}*/
	public function department(){
		if ($this->ajax) {
			$where = 1;
			if (isset($_GET['k'])&&$_GET['k']) {
				$keyword = $_GET['k'];
				$where .= ' AND `name` like "%'.$keyword.'%"';
			}
			if (isset($_GET['parent'])&&$_GET['parent']) {
				$parent = $_GET['parent'];
				$where .= ' AND `node` like "%,'.$parent.',%"';
			}
			//var_dump($where);die;
			$this->pageSize = 10;
			$this->default_db->load('hospital_department');
			$datas = $this->default_db->listinfo($where,'create_time desc',$this->page,$this->pageSize);
			empty($datas[0])&&$datas=array();
			$status = $pageCount = 0;
			$hospital = $parent = array();
			if ($datas) {
				$status = 1;
				$pageCount = $this->default_db->number;//数据总条数
				$hospital_ids = $parent_ids = array();
				foreach($datas as $k=>$r){
					if (!in_array($r['parent'],$parent_ids)) $parent_ids[] = $r['parent'];
					if (!in_array($r['hospital_id'],$hospital_ids)) $hospital_ids[] = $r['hospital_id'];
				}
				if ($parent_ids) {
					$parent_ids = implode(',',$parent_ids);
					$where = '`id` in ('.$parent_ids.')';
					$_parent = $this->default_db->select($where,'id,name');
					//var_dump($where);die;
					if ($_parent) {
						foreach($_parent as $r){
							$parent[$r['id']] = $r;
						}
					}
				}
				if ($hospital_ids) {
					$hospital_ids = implode(',',$hospital_ids);
					$where = '`id` in ('.$hospital_ids.')';
					$this->default_db->load('hospitals');
					$_hospital = $this->default_db->select($where,'id,name');
					//var_dump($where);die;
					if ($_hospital) {
						foreach($_hospital as $r){
							$hospital[$r['id']] = $r;
						}
					}
				}
			}
			$jsonData = array(
				'status'=>$status,
				'data'=>array('datas'=>$datas,'parent'=>$parent,'hospital'=>$hospital,'pageCount'=>$pageCount)
			);
			exit(json_encode($jsonData));
		}
		include template($this->template,'hospitalDepartment');
	}
	public function doDepartment(){
		if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {
			$id = intval($_POST['id']);
			$info = $_POST['info'];
			if (0==$id) $info['create_time'] = time();
			$param = array('table'=>'hospital_department','currentid'=>$id,'parent'=>$info['parent']);
			pc_base::load_app_class('category','dataManage',0);
			$category = new category();
			$status = $category->doCategory($info,$param);
			exit('{"status":'.($status?1:0).',"erro":"'.($status?'保存成功':'保存失败').'"}');
		} else {
			if ($this->ajax) {
				$this->default_db->load('hospitals');
				$hospital = $this->default_db->select(1);
				
				$this->default_db->load('hospital_department');
				$parent = $this->default_db->select(1);
				
				$department = array();
				$id = intval($_GET['id']);
				if (0<$id) {
					$where = '`id` = '.$id;
					$datas = $this->default_db->get_one($where);
				}
				$status = $datas?1:0;
				$jsonData = array(
					'status'=>$status,
					'data'=>array('datas'=>$datas,'parent'=>$parent,'hospital'=>$hospital)
				);
				exit(json_encode($jsonData));
			}
			include template($this->template,'doHospitalDepartment');
		}
	}
	public function delDepartment(){
		if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {
			$ids = explode(',',$_POST['ids']);
			$ids = array_filter($ids);
			//var_dump($ids);die;
			if (!empty($ids)) {
				$this->default_db->load('hospital_department');
				foreach($ids as $r){
					$where = '`id` = '.$r;
					$status = $this->default_db->delete($where);
				}	
			}
			exit('{"status":'.($status?1:0).',"erro":"'.($status?'删除成功':'删除失败').'"}');
		}
	}
	public function doctorList(){
		if ($this->ajax) {
			$where = 1;
			if (isset($_GET['k'])&&$_GET['k']) {
				$keyword = $_GET['k'];
				$where .= ' AND `realname` like "%'.$keyword.'%"';
			}
			if (isset($_GET['k2'])&&$_GET['k2']) {
				$keyword2 = $_GET['k2'];
				$where .= ' AND `telephone` = "'.$keyword2.'"';
			}
			//var_dump($where);die;
			$this->pageSize = 10;
			$this->default_db->load('doctor_user');
			$datas = $this->default_db->listinfo($where,'id desc',$this->page,$this->pageSize);
			empty($datas[0])&&$datas=array();
			$status = $pageCount = 0;
			$hospital = $department = array();
			if ($datas) {
				$status = 1;
				$pageCount = $this->default_db->number;//数据总条数
				$hospitalids = $departmentids = $titlesids = array();
				foreach($datas as $k=>$r){
					if (!in_array($r['departmentid'],$departmentids)) $departmentids[] = $r['departmentid'];
					if (!in_array($r['hospitalid'],$hospitalids)) $hospitalids[] = $r['hospitalid'];
					if (!in_array($r['titlesid'],$titlesids)) $titlesids[] = $r['titlesid'];
					$datas[$k]['inputtime'] = date('Y-m-d H:i:s',$r['inputtime']);
				}
				if ($departmentids) {
					$departmentids = implode(',',$departmentids);
					$where = '`id` in ('.$departmentids.')';
					$this->default_db->load('departments');
					$_department = $this->default_db->select($where,'id,name');
					//var_dump($where);die;
					if ($_department) {
						foreach($_department as $r){
							$department[$r['id']] = $r;
						}
					}
				}
				if ($hospitalids) {
					$hospitalids = implode(',',$hospitalids);
					$where = '`id` in ('.$hospitalids.')';
					$this->default_db->load('hospitals');
					$_hospital = $this->default_db->select($where,'id,name');
					//var_dump($where);die;
					if ($_hospital) {
						foreach($_hospital as $r){
							$hospital[$r['id']] = $r;
						}
					}
				}
				if ($titlesids) {
					$titlesids = implode(',',$titlesids);
					$where = '`id` in ('.$titlesids.')';
					$this->default_db->load('titles');
					$_titles = $this->default_db->select($where,'id,name');
					//var_dump($where);die;
					if ($_titles) {
						foreach($_titles as $r){
							$titles[$r['id']] = $r;
						}
					}
				}
			}
			$jsonData = array(
				'status'=>$status,
				'data'=>array('datas'=>$datas,'department'=>$department,'hospital'=>$hospital,'titles'=>$titles,'pageCount'=>$pageCount)
			);
			exit(json_encode($jsonData));
		}
		include template($this->template,'doctorList');
	}
	public function doDoctor(){
		if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {
			$userid = intval($_POST['userid']);
			$username = $_POST['username'];
			$info = $_POST['info'];			
			$info['catid'] = 80;
			$info['status'] = 99;
			$info['state'] = 3;
			$info['updatetime'] = time();
			$model = $_POST['model'];
			
			$this->default_db->load('doctor_user');
			if (0<$userid) {//编辑
				$where = '`userid` = '.$userid;
				$doctor = $this->default_db->get_one($where,'id');
				$status = $this->default_db->update($info,$where);
				
				$where = '`id` = '.$doctor['id'];
				$this->default_db->load('doctor_user_data');
				$this->default_db->update($model,$where2);
				
				//修改账号
				$where = '`username` = "'.$username.'"';
				$r = $this->member_db->get_one($where,'userid');
				if ($r) exit('{"status":0,"erro":"账号已存在"}');
				
				$where = '`userid` = '.$userid;
				$memberData = $this->member_db->get_one($where,'userid,phpssouid');
				$info2 = '`username` = "'.$username.'"';
				$this->member_db->update($info2,$where);
				$where = '`uid` = '.$memberData['phpssouid'];
				$this->default_db->load('sso_members');
				$this->default_db->update($info2,$where);
			} else {
				$password = '000000';
				$datas = array('username'=>$username,'password'=>$password,'nickname'=>$info['realname'],'roleid'=>12,'state'=>1);
				pc_base::load_app_class('user','dataManage',0);
				$user = new user();
				$result = $user->createAccount($datas);
				if (0 >= $result['status']) exit('{"status":0,"erro":"'.$result['erro'].'"}');
				$info['userid'] = $result['data']['userid'];
				$info['inputtime'] = $info['updatetime'];
				//var_dump($info);die;
				$status = $this->default_db->insert($info,true);
				if (0<$status) {
					$model['id'] = $status;
					$this->default_db->load('doctor_user_data');
					$this->default_db->insert($model);
				}
			}
			exit('{"status":'.($status?1:0).',"erro":"'.($status?'保存成功':'保存失败').'"}');
		} else {
			if ($this->ajax) {
				$this->default_db->load('hospitals');
				$hospital = $this->default_db->select(1);
				
				/*$this->default_db->load('departments');
				$department = $this->default_db->select(1);*/
				$department = array();
				
				//
				$this->default_db->load('titles');
				$titles = $this->default_db->select(1);
				
				$doctor = array();
				$userid = intval($_GET['userid']);
				if (0<$userid) {
					$where = '`userid` = '.$userid;
					$this->default_db->load('doctor_user');
					$doctor = $this->default_db->get_one($where);
					$where = '`id` = '.$doctor['id'];
					$this->default_db->load('doctor_user_data');
					$model = $this->default_db->get_one($where);
					if (is_array($model)&&$model) $doctor = array_merge($doctor,$model);
					
					$where = '`userid` = '.$userid;
					$member = $this->member_db->get_one($where,'username');
				}
				$status = $doctor?1:0;
				$jsonData = array(
					'status'=>$status,
					'data'=>array('doctor'=>$doctor,'member'=>$member,'department'=>$department,'hospital'=>$hospital,'titles'=>$titles)
				);
				exit(json_encode($jsonData));
			}
			include template($this->template,'doDoctor');
		}
	}
	public function delDoctor(){
		if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {
			$ids = explode(',',$_POST['ids']);
			$ids = array_filter($ids);
			//var_dump($ids);die;
			if (!empty($ids)) {
				$this->default_db->load('doctor_user');
				foreach($ids as $r){
					$where = '`userid` = '.$r;
					$status = $this->default_db->delete($where);
					if ($status) {//删除医生用户账号
						
					}
				}	
			}
			exit('{"status":'.($status?1:0).',"erro":"'.($status?'删除成功':'删除失败').'"}');
		}
	}
	/*public function titlesList(){
		if ($this->ajax) {
			$where = 1;
			if (isset($_GET['k'])&&$_GET['k']) {
				$keyword = $_GET['k'];
				$where .= ' AND `name` like "%'.$keyword.'%"';
			}
			//var_dump($where);die;
			$this->pageSize = 10;
			$this->default_db->load('titles');
			$datas = $this->default_db->listinfo($where,'id desc',$this->page,$this->pageSize);
			empty($datas[0])&&$datas=array();
			$status = $pageCount = 0;
			if ($datas) {
				$status = 1;
				$pageCount = $this->default_db->number;//数据总条数
			}
			$jsonData = array(
				'status'=>$status,
				'data'=>array('datas'=>$datas,'pageCount'=>$pageCount)
			);
			exit(json_encode($jsonData));
		}
		include template($this->template,'titlesList');
	}
	public function doTitles(){
		if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {
			$titlesid = intval($_POST['id']);
			$name = $_POST['name'];
			$note = $_POST['note'];
			$state = intval($_POST['state']);
			$datas = array(
				'name'=>$name,
				'note'=>$note,
				'state'=>$state
			);
			$this->default_db->load('titles');
			if (0<$titlesid) {
				$where = '`id` = '.$titlesid;
				$status = $this->default_db->update($datas,$where);
			} else {
				$status = $this->default_db->insert($datas,true);
			}
			exit('{"status":'.($status?1:0).',"erro":"'.($status?'保存成功':'保存失败').'"}');
		} else {
			if ($this->ajax) {
				$titles = array();
				$titlesid = intval($_GET['id']);
				if (0<$titlesid) {
					$where = '`id` = '.$titlesid;
					$this->default_db->load('titles');
					$titles = $this->default_db->get_one($where);
				}
				$status = $titles?1:0;
				$jsonData = array(
					'status'=>$status,
					'data'=>array('titles'=>$titles)
				);
				exit(json_encode($jsonData));
			}
			include template($this->template,'doTitles');
		}
	}
	public function delTitles(){
		if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {
			$ids = explode(',',$_POST['ids']);
			$ids = array_filter($ids);
			//var_dump($ids);die;
			if (!empty($ids)) {
				$this->default_db->load('titles');
				foreach($ids as $r){
					$where = '`userid` = '.$r;
					$status = $this->default_db->delete($where);
				}	
			}
			exit('{"status":'.($status?1:0).',"erro":"'.($status?'删除成功':'删除失败').'"}');
		}
	}*/
	public function doctorTitles(){
		if ($this->ajax) {
			$where = 1;
			if (isset($_GET['k'])&&$_GET['k']) {
				$keyword = $_GET['k'];
				$where .= ' AND `name` like "%'.$keyword.'%"';
			}
			//var_dump($where);die;
			$this->pageSize = 10;
			$this->default_db->load('doctor_titles');
			$datas = $this->default_db->listinfo($where,'create_time desc',$this->page,$this->pageSize);
			empty($datas[0])&&$datas=array();
			$status = $pageCount = 0;
			if ($datas) {
				$status = 1;
				$pageCount = $this->default_db->number;//数据总条数
			}
			$jsonData = array(
				'status'=>$status,
				'data'=>array('datas'=>$datas,'pageCount'=>$pageCount)
			);
			exit(json_encode($jsonData));
		}
		include template($this->template,'doctorTitles');
	}
	public function doTitles(){
		if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {
			$id = intval($_POST['id']);
			$info = $_POST['info'];
			$this->default_db->load('doctor_titles');
			if (0<$id) {
				$where = '`id` = '.$id;
				$result = $this->default_db->update($info,$where);
			} else {
				$info['create_time'] = time();
				$result = $this->default_db->insert($info,true);
			}
			if ($result) {
				$status = 1;
				$erro = '保存成功';
			} else {
				$status = 0;
				$erro = '保存失败';
			}
			$jsonData = array(
				'status'=>$status,
				'erro'=>$erro,
			);
			exit(json_encode($jsonData));
		} else {
			if ($this->ajax) {
				$id = intval($_GET['id']);
				if (0<$id) {
					$where = '`id` = '.$id;
					$this->default_db->load('doctor_titles');
					$datas = $this->default_db->get_one($where);
				}
				$status = $datas?1:0;
				$jsonData = array(
					'status'=>$status,
					'data'=>array('datas'=>$datas)
				);
				exit(json_encode($jsonData));
			}
			include template($this->template,'doDoctorTitles');
		}
	}
	public function delTitles(){
		if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {
			$ids = explode(',',$_POST['ids']);
			$ids = array_filter($ids);
			//var_dump($ids);die;
			if (!empty($ids)) {
				$where = array('id'=>array('in',$ids));
				$this->default_db->load('doctor_titles');
				$result = $this->default_db->delete($where);
			}
			if ($result) {
				$status = 1;
				$erro = '删除成功';
			} else {
				$status = 0;
				$erro = '删除失败';
			}
			$jsonData = array(
				'status'=>$status,
				'erro'=>$erro,
			);
			exit(json_encode($jsonData));
		}
	}
}
?>