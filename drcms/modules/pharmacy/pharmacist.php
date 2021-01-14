<?php
defined('IN_drcms') or exit('No permission resources.');
pc_base::load_app_class('authority','corp',0);
class pharmacist extends authority{
	public $store_id = 0;
	public function __construct() {
		parent::__construct();
		$this->store_id = intval(param::get_cookie('store_id'));
	}
	public function init(){
		$this->index();
	}
	public function lists(){
		if ($this->ajax) {
			$where = 1;//['store_id'=>$this->store_id];
			// if ($_GET['k']) $where['prescription_no'] = $_GET['k'];
			// if ($_GET['status']) $where['status'] = $_GET['status'];
			// if ($_GET['st']&&$_GET['et']) {
			// 	$res = dateToTimeStamp(['st'=>$_GET['st'],'et'=>$_GET['et']]);
	 	    // 	$where .= ' AND `create_time`>='.$res['st'].' AND `create_time`<='.$res['et'];
			// }
			$this->default_db->load('pharmacist');
			$rows = $this->default_db->listinfo($where,'create_time desc',$this->page,$this->pageSize);
			if ($rows) {
				$total = $this->default_db->number;
				$res = getDictionary(['category_id'=>[14],'is_value'=>1,'value_category'=>[14]]);
				// $sex = $res['data'][10]?:[];
				$statuss = $res['data'][14]?:[];
				//var_dump($statuss);die;
				foreach ($rows as $k => $v) {
					//$rows[$k]['sex'] = $sex[$v['sex']]['name']?:'';
					$rows[$k]['status_str'] = $statuss[$v['status']]['name']?:'';
					$rows[$k]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
				}
			} else {
				$total = 0;
				$rows = [];
			}
			//$jsonData = ['status'=>1,'rows'=>$rows,'total'=>$total];
			//exit(json_encode($jsonData));
			output(1,'',['type'=>'json','data'=>['rows'=>$rows,'total'=>$total]]);
		} else {
			include template($this->template,'pharmacist',$this->style);
		}
	}

	public function detail(){
		$prescription_no = $_GET['prescription_no'];
		$where = ['prescription_no'=>$prescription_no];
		$this->default_db->load('prescription');
		$row = $this->default_db->get_one($where);
		$drug = [];
		if ($row) {
			$res = getDictionary(['category_id'=>[10,14,15],'is_value'=>1,'value_category'=>[14,15]]);
			$sex = $res['data'][10]?:[];
			$statuss = $res['data'][14]?:[];
			$payStatuss = $res['data'][15]?:[];
			$row['sex'] = $sex[$row['sex']]['name']?:'';
			$row['status_str'] = $statuss[$row['status']]['name']?:'';
			$row['pay_status_str'] = $payStatuss[$row['pay_status']]['name']?:'';
			$row['create_time'] = date('Y-m-d H:i:s',$row['create_time']);
			$this->default_db->load('prescription_drug');
			$drug = $this->default_db->select($where);
		} else {
			$row = [];
		}
		output(1,'',['format'=>2,'data'=>['row'=>$row,'drug'=>$drug]]);
	}
	public function doPharmacist(){
	   //  var_dump($_FILES);
		if ($_POST['dosubmit']) {
			$id = intval($_POST['id']);
			$where = ['id'=>$id];
			$this->default_db->load('pharmacist');
			$row = $this->default_db->get_one($where,'id');
			$info = [
				'name'=>$_POST['name'],
				//'img'=>$img,
				'is_default'=>$_POST['is_default'],
				'update_time'=>SYS_TIME,
			];
			if ($_POST['base64']) {
				//下载图片
				$res = imgDown(['type'=>'base64','img'=>$_POST['base64'],'user_id'=>$_POST['user_id']]);
				$img = 'SUCCESS' == $res['status']?$res['data']['src']:'';
				$info['img'] = $img;
			}
			if ($row) {//编辑
				$res = $this->default_db->update($info,$where);
			} else {//新增
				$info['create_time'] = $info['update_time'];
				$res = $this->default_db->insert($info,true);
			}
			if ($res) {
				$status = 1;
				$erro = '保存成功';
			} else {
				$status = 1;
				$erro = '保存失败';
			}
			output($status,$erro);
		} else {
			$id = intval($_GET['id']);
			$where = ['id'=>$id];
			$this->default_db->load('pharmacist');
			$row = $this->default_db->get_one($where);
			if (!$row) $row = [];
			output(1,'',['format'=>2,'data'=>$row]);
		}
	}
	public function aditePharmacist(){   // 修改个人信息  上传图片
		$id = intval($_POST['useid']);
		$where = ['useid'=>$id];
		$array=$_FILES["myFile"]["type"][0];
		$array=explode("/",$array);
		$newfilename="upload_images".date('YmdHis');
		$_FILES["myFile"]["name"][0]=$newfilename.".".$array[1];
		$filepath = drcms_PATH.'upload_tmp';
		if (!is_dir($filepath))//当路径不穿在
		{
			mkdir($filepath);//创建路径
		}
		$url=$filepath."/";//记录路径
		if (file_exists($url.$_FILES["myFile"]["name"][0]))//当文件存在
		{
			  echo $_FILES["myFile"]["name"][0] . " already exists. ";
		} else {
			$url=$url.$_FILES["myFile"]["name"][0];
			if (move_uploaded_file($_FILES["myFile"]["tmp_name"][0],$url)) {
				$this->default_db->load('pharmacist');
				$res = $this->default_db->update($info,$where);  
			} 
			if ($res) {
				$status = 1;
				$erro = '保存成功';
			} else {
				$status = 1;
				$erro = '保存失败';
			}
			output($status,$erro);
		}
	 }
	public function doDefault(){
		if ($this->ajax) {
			$id = intval($_POST['id']);
			$where = ['id'=>$id];
			$this->default_db->load('pharmacist');
			$row = $this->default_db->get_one($where,'id');
			$info = [
				'is_default'=>$_POST['is_default'],
			];
			if ($row) {//编辑
				$res = $this->default_db->update($info,$where);
			}
			if ($res) {
				$status = 1;
				$erro = '保存成功';
			} else {
				$status = 1;
				$erro = '保存失败';
			}
			output($status,$erro);
		}
	}
	public function delete(){
		$id = array_filter(explode(',', $_POST['id']));
		if ($id) {
			$where = ['id'=>['in',$id]];
			$this->default_db->load('pharmacist');
			$res = $this->default_db->delete($where);
			if ($res) {
				$status = 1;
				$erro = '删除成功';
			} else {
				$status = 0;
				$erro = '删除失败';
			}
		} else {
			$status = 0;
			$erro = '缺少必要参数';
		}
		output($status,$erro);
	}


	public function doVerify(){
		if ($_POST['dosubmit']) {
			$id = intval($_POST['id']);
			$where = ['id'=>$id];
			$this->default_db->load('pharmacist');
			$row = $this->default_db->get_one($where,'id');
			$info = [
				'name'=>$_POST['name'],
				//'img'=>$img,
				'is_default'=>$_POST['is_default'],
				'update_time'=>SYS_TIME,
			];
			if ($_POST['base64']) {
				//下载图片
				$res = imgDown(['type'=>'base64','img'=>$_POST['base64'],'user_id'=>$_POST['user_id']]);
				$img = 'SUCCESS' == $res['status']?$res['data']['src']:'';
				$info['img'] = $img;
			}
			if ($row) {//编辑
				$res = $this->default_db->update($info,$where);
			} else {//新增
				$info['create_time'] = $info['update_time'];
				$res = $this->default_db->insert($info,true);
			}
			if ($res) {
				$status = 1;
				$erro = '保存成功';
			} else {
				$status = 1;
				$erro = '保存失败';
			}
			output($status,$erro);
		} else {
			$id = intval($_GET['id']);
			$where = ['id'=>$id];
			$this->default_db->load('pharmacist');
			$row = $this->default_db->get_one($where);
			if (!$row) $row = [];
			output(1,'',['format'=>2,'data'=>$row]);
		}
	}

  




}
?>