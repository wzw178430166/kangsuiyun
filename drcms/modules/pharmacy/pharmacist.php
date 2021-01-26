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
	public function uploadFile($param = []){
	    $data = [];
	    if ($param['file']) {
	        $files = $param['file'];
	        //var_dump($files);die;
	        $filePath = 'upload_tmp';
	        $documentRoot = drcms_PATH.$filePath;
	        if (!is_dir($documentRoot)) mkdir($documentRoot);//创建路径
	        if (is_array($files['tmp_name'])) {
	           foreach($files['tmp_name'] as $k=>$v){
	                $type = $files['type'][$k];
	                $ext = explode("/",$type);
	                //var_dump($ext);die;
	                $ext = $ext[1];
	                $filename = date('YmdHis').'.'.$ext;
	                //var_dump($documentRoot.$filename);die;
                    if (move_uploaded_file($v,$documentRoot.'/'.$filename)) {
                        $data['url'][$k] = '/'.$filePath.'/'.$filename;
                    }
	           }
	        } else {
	            $ext = explode("/",$files['type']);
	            $ext = $ext[1];
	            $filename = date('YmdHis').'.'.$ext;
    			if (move_uploaded_file($files["tmp_name"],$documentRoot.'/'.$filename)) {
    			    $data['url'] = '/'.$filePath.$filename;
    			}
	        }
	        $status = 'SUCCESS';
	        $erro = '';
	    } else {
	        $status = 'FAIL';
	        $erro = '';
	    }
	    return ['status'=>$status,'erro'=>$erro,'data'=>$data];
	}
	public function aditePharmacist(){   // 修改个人信息  上传图片
		if ($this->ajax) {
			$id = intval($_POST['useid']);
			$where = ['useid'=>$id];
			$res = $this->uploadFile(['file'=>$_FILES['myFile']]);
			$this->default_db->load('pharmacist');
			$row = $this->default_db->get_one($where,'useid');
			$info = $_POST['info'];
			$info['update_time'] = SYS_TIME;
			$info['img'] = $res['data']['url'][0];
			//var_dump($where);die;
			if ($row) {//编辑
				$res = $this->default_db->update($info,$where);
			//	var_dump($this->default_db->error());die;
			}else{
				 //添加
				 $info['useid'] = $id;
				 $res = $this->default_db->insert($info,true);
			}
			if ($res) {
				$status = 1;
				$erro = '保存成功';
			} else {
				$status = 0;
				$erro = '保存失败';
			}
			output($status,$erro);
		}
		// $id = intval($_POST['useid']);
		// $where = ['useid'=>$id];
		// $array=$_FILES["myFile"]["type"][0];
		// $array=explode("/",$array);
		// $newfilename="upload_images".date('YmdHis');
		// $_FILES["myFile"]["name"][0]=$newfilename.".".$array[1];
		// $filepath = drcms_PATH.'upload_tmp';
		// if (!is_dir($filepath))//当路径不穿在
		// {
		// 	mkdir($filepath);//创建路径
		// }
		// $url=$filepath."/";//记录路径
		// if (file_exists($url.$_FILES["myFile"]["name"][0]))//当文件存在
		// {
		// 	  echo $_FILES["myFile"]["name"][0] . " already exists. ";
		// } else {
		// 	$url=$url.$_FILES["myFile"]["name"][0];
		// 	if (move_uploaded_file($_FILES["myFile"]["tmp_name"][0],$url)) {
		// 		$this->default_db->load('pharmacist');
		// 		$res = $this->default_db->update($info,$where);  
		// 	} 
		// 	if ($res) {
		// 		$status = 1;
		// 		$erro = '保存成功';
		// 	} else {
		// 		$status = 1;
		// 		$erro = '保存失败';
		// 	}
		// 	output($status,$erro);
		// }
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

	public function doHangup(){   //首页待审核
		// $nid = intval($_POST['nid']);
		// $where = ['nid'=>$nid];
		// $infos = [
		// 	'status'=>3,
		// ];
		// $this->default_db->load('prescription');
		// $rows = $this->default_db->get_one($where,'nid');
	
		// if ($rows) {
		// 	 $res=$this->default_db->update($infos,$where);
		// 	 var_dump($this->default_db->error());die;
		// }
		// if ($res) {
		// 	$status = 1;
		// 	$erro = '保存成功';
		// } else {
		// 	$status = 1;
		// 	$erro = '保存失败';
		// }
		// output($status,$erro);
		$where = [
			'status'=>'WAIT_VERIFY',
		];
		$where2 = [
			'status'=>'PASSED',
		];
		$this->default_db->load('prescription');
		$rows1 = $this->default_db->select($where,'*');
		$num1 = count($rows1);
		$rows2 = $this->default_db->select($where2,'*');
		$num2 = count($rows2);
		$data = ['wait_num'=>$num1,'pass_num'=>$num2];
		exit(json_encode(array('status'=>1,'data'=>$data)));
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

	public function getAllNum(){  
	    $nid = intval($_GET['nid']);
		$where = [
			'status'=>1,
		];
		$this->default_db->load('con_pn');
		$rows = $this->default_db->select($where,'id,nid','','id asc');
		$num = $time = 0;
		$sort = 1;
		if ($rows) {
		    foreach ($rows as $k=>$v) {
		        if ($v['nid'] == $nid) {
		            $sort = ($k + 1);
		            break;
		        }
		    }
		    $num = count($rows);
		    $time = $sort * 60*2;
		}
		$data = ['len'=>$num,'time'=>$time,'sort'=>$sort];
		output(1,'',['data'=>$data]);
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