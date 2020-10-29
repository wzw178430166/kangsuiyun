<?php
defined('IN_drcms') or exit('No permission resources.');
pc_base::load_app_class('authority','dataManage',0);
class advert {
	public $default_db;
	public $ajax = 0;//异步访问标识
	public $pageSize = 15;//每页条数
	public $template = 'dataManage';//模板
	public $user_id = 0;
	function __construct() {
		$this->authority = new authority();
		$this->authData = $this->authority->authData;
		$this->default_db = pc_base::load_model('default_model');
		$this->content_db = pc_base::load_model('content_model');
		$this->member_db = pc_base::load_model('member_model');
		$this->page = isset($_GET['page'])&&$_GET['page']?intval($_GET['page']):1;
		$this->ajax = intval($_GET['ajax']);
		$this->user_id = param::get_cookie('authUserid');
	}
	public function init() {
		
	}
	public function category() {
		if ($this->ajax) {
			$parent = intval($_GET['parent']);
			//$where = '`parent`='.$parent;
			$where = 0==$parent?1:'`node` like "%,'.$parent.',%"';
			if (isset($_GET['k'])&&$_GET['k']) {
				$keyword = $_GET['k'];
				$where .= ' AND `name` like "%'.$keyword.'%"';
			}
			$this->default_db->load('article_category');
			$datas = $this->default_db->listinfo($where,'id desc',$this->page,$this->pageSize);
			$parent = array();
			if ($datas) {
				$status = 1;
				$pageCount = $this->default_db->number;
				$parentids = array();
				foreach($datas as $r){
					//$r['create_time'] = date('Y-m-d H:i:s',$r['create_time']);
					if (!in_array($r['parent'],$parentids)) $parentids[] = $r['parent'];
					//$datas[$k] = $r;
				}
				if ($parentids) {
					//$parentids = implode(',',$parentids);
					//$where = '`id` in ('.$parentids.')';
					$where = array('id'=>array('in',$parentids));
					$_parent = $this->default_db->select($where,'id,name');
					//var_dump($where);die;
					if ($_parent) {
						foreach($_parent as $r){
							$parent[$r['id']] = $r;
						}
					}
				}
			} else {
				$datas = array();
				$status = 0;
				$pageCount = 0;
			}
			$jsonData = array(
				'status'=>$status,
				'data'=>array('datas'=>$datas,'parent'=>$parent,'pageCount'=>$pageCount)
			);
			exit(json_encode($jsonData));
		}	
		include template($this->template,'articleCategory');
	}
	public function doCategory(){
		if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {
			$category_id = intval($_POST['id']);
			$parent = intval($_POST['parent']);
			$name = $_POST['name'];
			$content = $_POST['content'];
			$datas = array(
				'parent'=>$parent,
				'name'=>$name,
				'content'=>$content,
			);
			//var_dump($datas);die;
			$param = array('table'=>'article_category','currentid'=>$category_id,'parent'=>$parent);
			pc_base::load_app_class('category','dataManage',0);
			$category = new category();
			$status = $category->doCategory($datas,$param);
			exit('{"status":'.($status?1:0).',"erro":"'.($status?'保存成功':'保存失败').'"}');
		} else {
			if ($this->ajax) {
				$id = intval($_GET['id']);
				$datas = array();
				$this->default_db->load('article_category');
				if (0 < $id) {
					$where = '`id`='.$id;
					$datas = $this->default_db->get_one($where);
				}
				$parent = $this->default_db->select(1);
				$status = $datas?1:0;
				$jsonData = array(
					'status'=>$status,
					'data'=>array('datas'=>$datas,'parent'=>$parent)
				);
				exit(json_encode($jsonData));
			}
			include template($this->template,'doArticleCategory');
		}
		
	}
	public function delCategory(){
		if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {
			$ids = explode(',',$_POST['ids']);
			$ids = array_filter($ids);
			
			if (!empty($ids)) {
				$is_success = 1;
				foreach($ids as $r){
					$where = '`id` = '.$r;
					$this->default_db->load('article_category');
					$current = $this->default_db->get_one($where,'id');
					if (empty($current)) {
						continue;//过滤不存在记录
					} else {
						$where2 = '`parent`='.$r;
						$node = $this->default_db->get_one($where2,'id');
						if ($node) {
							$is_success = 0;
							$erro = '分类存在下级无法删除';
							break;
						}
						$this->default_db->delete($where);
					}
				}
				if (0 == $is_success) {
					$status = 0;
				} else {
					$status = 1;
					$erro = '删除成功';
				}
			} else {
				$status = 0;
				$erro = '请选择';
			}
			exit('{"status":'.$status.',"erro":"'.$erro.'"}');
		}
	}
	public function lists() {
		if ($this->ajax) {
			$where = 1;
			$this->default_db->load('advert');
			$datas = $this->default_db->listinfo($where,'create_time desc',$this->page,$this->pageSize);
			
			if ($datas) {
				$status = 1;
				$pageCount = $this->default_db->number;
				$category_ids = $userids = array();
				foreach($datas as $k=>$r){
					if (!in_array($r['category_id'],$category_ids)) $category_ids[] = $r['category_id'];
					//if (!in_array($r['userid'],$userids)) $userids[] = $r['userid'];
					$datas[$k]['update_time'] = date('Y-m-d H:i:s',$r['update_time']);
					$datas[$k]['create_time'] = date('Y-m-d H:i:s',$r['create_time']);
				}
				//$category = array(1=>array('id'=>1,'name'=>'矩形横幅'));
				if ($category_ids) {
					$where = array('id'=>array('in',$category_ids));
					$this->default_db->load('advert_category');
					$_category = $this->default_db->select($where,'id,name');
					foreach($_category as $r){
						$category[$r['id']] = $r;
					}
				}
				/*if ($userids) {
					$where = array('userid'=>array('in',$userids));
					$this->default_db->load('member');
					$_auth = $this->default_db->select($where,'userid,username,nickname,roleid,state');
					$roleids = array();
					foreach($_auth as $r){
						if (!in_array($r['roleid'],$roleids)) $roleids[] = $r['roleid'];
						$auth[$r['userid']] = $r;
					}
					$where = array('roleid'=>array('in',$roleids));
					$this->default_db->load('auth_role');
					$_role = $this->default_db->select($where,'roleid,name');
					foreach($_role as $r){
						$role[$r['roleid']] = $r;
					}
				}*/
			} else {
				$status = 0;
				$datas = $category = array();
			}
			$jsonData = array(
				'status'=>$status,
				'data'=>array('datas'=>$datas,'category'=>$category,'pageCount'=>$pageCount)
			);
			exit(json_encode($jsonData));
		}
		include template($this->template,'advertList');
	}
	public function doAdvert(){
		if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {
			$id = intval($_POST['id']);
			$info = $_POST['info'];
			$this->default_db->load('advert');
			if (0<$id) {
				$where = '`id`='.$id;
				$info['update_time'] = time();
				$result = $this->default_db->update($info,$where);
			} else {
				$info['create_time'] = $info['update_time'] = time();
				$info['user_id'] = $this->user_id;
				$result = $this->default_db->insert($info,true);
			}
			if ($result) {
				$status = 1;
				$erro = '保存成功';
			} else {
				$status = 0;
				$erro = '保存失败';
			}
			exit('{"status":'.$status.',"erro":"'.$erro.'"}');
		} else {
			if ($this->ajax) {
				$id = intval($_GET['id']);
				if (0<$id) {
					$where = '`id` = '.$id;
					$this->default_db->load('advert');
					$datas = $this->default_db->get_one($where);
				} else {
					$datas = array();
				}
				$this->default_db->load('advert_category');
				$category = $this->default_db->select(1);
				//$category = array(array('id'=>1,'name'=>'矩形横幅'));
				$status = $datas?1:0;
				$jsonData = array(
					'status'=>$status,
					'data'=>array('datas'=>$datas,'category'=>$category)
				);
				exit(json_encode($jsonData));
			}
			include template($this->template,'doAdvert');
		}
	}
	public function delAdvert(){
		if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {
			$ids = explode(',',$_POST['ids']);
			$ids = array_filter($ids);
			//var_dump($ids);die;
			if (!empty($ids)) {
				foreach($ids as $r){
					$where = '`id` = '.$r;
					$this->default_db->load('advert');
					$current = $this->default_db->get_one($where,'id');
					if (empty($current)) continue;//过滤不存在记录
					$status = $this->default_db->delete($where);
					if ($status) {//清除记录
						$where2 = '`advert_id`='.$r;
						$this->default_db->load('advert_data');
						$this->default_db->delete($where2);
					}
				}
				$status = 1;
				$erro = '删除成功';		
			} else {
				$status = 0;
				$erro = '参数错误';
			}
			exit('{"status":'.$status.',"erro":"'.$erro.'"}');
		}
	}
	public function advertData(){
		if ($this->ajax) {
			$advert_id = intval($_GET['advert_id']);
			$where = '`advert_id`='.$advert_id;
			$this->default_db->load('advert_data');
			$datas = $this->default_db->listinfo($where,'create_time desc',$this->page,$this->pageSize);
			if ($datas) {
				$status = 1;
				$pageCount = $this->default_db->number;
				$types = array('','图片');
				$statuss = array('库存','正常');
				foreach($datas as $k=>$r){
					$datas[$k]['typeStr'] = $types[$r['type']];
					$datas[$k]['statusStr'] = $statuss[$r['status']];
					$datas[$k]['create_time'] = date('Y-m-d H:i:s',$r['create_time']);
				}
			} else {
				$status = $pageCount = 0;
				$datas = array();
			}
			$jsonData = array(
				'status'=>$status,
				'data'=>array('datas'=>$datas,'pageCount'=>$pageCount),
			);
			exit(json_encode($jsonData));
		}
		include template($this->template,'advertData');
	}
	public function doAdvertData(){
		if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {
			$id = intval($_POST['id']);
			$advert_id = intval($_POST['advert_id']);
			$info = $_POST['info'];
			$this->default_db->load('advert_data');
			if (0<$id) {
				$where = '`id`='.$id;
				$result = $this->default_db->update($info,$where);
			} else {
				$info['create_time'] = time();
				$info['advert_id'] = $advert_id;
				$result = $this->default_db->insert($info,true);
			}
			if ($result) {
				$status = 1;
				$erro = '保存成功';
			} else {
				$status = 0;
				$erro = '保存失败';
			}
			exit('{"status":'.$status.',"erro":"'.$erro.'"}');
		} else {
			if ($this->ajax) {
				$id = intval($_GET['id']);
				if (0<$id) {
					$where = '`id` = '.$id;
					$this->default_db->load('advert_data');
					$datas = $this->default_db->get_one($where);
				} else {
					$datas = array();
				}
				$status = $datas?1:0;
				$jsonData = array(
					'status'=>$status,
					'data'=>array('datas'=>$datas)
				);
				exit(json_encode($jsonData));
			}
			include template($this->template,'doAdvertData');
		}
	}
	public function delAdvertData(){
		if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {
			$ids = explode(',',$_POST['ids']);
			$ids = array_filter($ids);
			//var_dump($ids);die;
			if (!empty($ids)) {
				$where = array('id'=>array('in',$ids));
				$this->default_db->load('advert_data');
				$this->default_db->delete($where);
				$status = 1;
				$erro = '删除成功';	
			} else {
				$status = 0;
				$erro = '参数错误';
			}
			exit('{"status":'.$status.',"erro":"'.$erro.'"}');
		}
	}
	public function doAdvertDataStatus(){
		if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {
			$ids = explode(',',$_POST['ids']);
			$ids = array_filter($ids);
			$operateStatus = intval($_POST['status']);
			$where = array('id'=>array('in',$ids));
			$info = '`status` = '.$operateStatus;
			$this->default_db->load('advert_data');
			$status = $this->default_db->update($info,$where);
			exit('{"status":'.($status?1:0).',"erro":"'.($status?'保存成功':'保存失败').'"}');
		}
	}
	public function doAdvertDataPriority(){
		if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {
			$priority = $_POST['priority'];
			if ($priority) {
				$this->default_db->load('advert_data');
				foreach($priority as $k=>$r){
					$where = '`id`='.$k;
					$info = '`priority`='.$r;
					$this->default_db->update($info,$where);
				}
				$status = 1;
				$erro = '保存成功';
			} else {
				$status = 0;
				$erro = '暂无可排序';
			}
			$jsonData = array(
				'status'=>$status,
				'erro'=>$erro
			);
			exit(json_encode($jsonData));
		}
	}
}

?>