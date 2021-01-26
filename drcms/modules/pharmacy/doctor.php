<?php
defined('IN_drcms') or exit('No permission resources.');
pc_base::load_app_class('authority','corp',0);
class doctor extends authority{
	public $store_id = 0;
	public function __construct() {
		parent::__construct();
		$this->store_id = intval(param::get_cookie('store_id'));
	}
	public function init(){
		$this->index();
	}
	public function inquirylists(){  
		if ($this->ajax) {
			$where =  [
				'status'=>1,
			];;
			if($_GET[k]) $where .= ' AND `id`='.$_GET[k];
			$keyword = $_GET['amount'];
			if($_GET[amount]) $where .= ' AND (`gmp_validity` = "'.$keyword.'" OR `gmp_validity` like "%'.$keyword.'%")';
			if ($_GET['st']&&$_GET['et']) {
				$res = dateToTimeStamp(['st'=>$_GET['st'],'et'=>$_GET['et']]);
	 	    	$where .= ' AND `created_time`>='.$res['st'].' AND `created_time`<='.$res['et'];
			}
			$this->default_db->load('con_pn');
			$rows = $this->default_db->listinfo($where,'created_time desc',$this->page,$this->pageSize);
			if ($rows) {
				$total = $this->default_db->number;
				$res = getDictionary(['category_id'=>[10,14],'is_value'=>1,'value_category'=>[14]]);
				$sex = $res['data'][10]?:[];
				$statuss = $res['data'][14]?:[];
				//var_dump($statuss);die;
				$sex_str = '';
				foreach ($rows as $k => $v) {
					if($rows[$k]['sex']  == 1) $sex_str = '男';
					if($rows[$k]['sex']  == 2) $sex_str = '女';
					if($rows[$k]['sex']  == 3) $sex_str = '保密';
					$rows[$k]['sex'] = $sex[$v['sex']]['name']?:$sex_str;
					$rows[$k]['status_str'] = $statuss[$v['status']]['name']?:'';
					$rows[$k]['created_time'] = date('Y-m-d H:i:s',$v['created_time']);
				}
			} else {
				$total = 0;
				$rows = [];
			}
			output(1,'',['type'=>'json','data'=>['rows'=>$rows,'total'=>$total]]);
		} else {
			include template($this->template,'drug',$this->style);
		}
	}

	public function detail(){
		$prescription_no = $_GET['pnid'];
		$where = ['prescription_no'=>$prescription_no];
		$this->default_db->load('drug_pn_user');
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
			$drug = $this->default_db->select($where);
		} else {
			$row = [];
		}
		output(1,'',['format'=>2,'data'=>['row'=>$row,'drug'=>$drug]]);
	}



	public function getUsermsg(){
		$nid = intval($_POST['nid']);
		if($nid){
			$this->default_db->load('con_pn');
			$da = $this->default_db->get_one('`nid` = '.$nid);
			exit(json_encode(array('status'=>1,'data'=>$da)));
		}
	}

	public function druglists(){
		if ($this->ajax) {
			$where = 1;
			if($_GET[k]) $where .= ' AND `id`='.$_GET[k];
			$keyword = $_GET['amount'];
			if($_GET[amount]) $where .= ' AND (`gmp_validity` = "'.$keyword.'" OR `gmp_validity` like "%'.$keyword.'%")';
	
			if ($_GET['st']&&$_GET['et']) {
				$res = dateToTimeStamp(['st'=>$_GET['st'],'et'=>$_GET['et']]);
	 	    	$where .= ' AND `created_time`>='.$res['st'].' AND `created_time`<='.$res['et'];
			}
			$this->default_db->load('drug');
			$rows = $this->default_db->listinfo($where,'created_time desc',$this->page,$this->pageSize);
			if ($rows) {
				$total = $this->default_db->number;
				$res = getDictionary(['category_id'=>[10,14],'is_value'=>1,'value_category'=>[14]]);
				$sex = $res['data'][10]?:[];
				$statuss = $res['data'][14]?:[];
				//var_dump($statuss);die;
				foreach ($rows as $k => $v) {
					$rows[$k]['sex'] = $sex[$v['sex']]['name']?:'';
					$rows[$k]['status_str'] = $statuss[$v['status']]['name']?:'';
					$rows[$k]['created_time'] = date('Y-m-d H:i:s',$v['created_time']);
				}
			} else {
				$total = 0;
				$rows = [];
			}
			//$jsonData = ['status'=>1,'rows'=>$rows,'total'=>$total];
			//exit(json_encode($jsonData));
			output(1,'',['type'=>'json','data'=>['rows'=>$rows,'total'=>$total]]);
		} else {
			include template($this->template,'drug',$this->style);
		}
	}
  
	public function getDrugDetails(){
        $pnid = intval($_POST['pnid']);
		$id = intval($_POST['id']);
        if($pnid){
            $this->default_db->load('drug_pn');
            $da = $this->default_db->get_one('`pnid` = '.$pnid.' AND `rid` = '.$id);
            if($id){
                $this->default_db->load('drug');
                $da2 = $this->default_db->get_one('`id` = '.$id,'id,trade_name,specification,enterprise,characteristic,sales_status,vip_price,drug_category,origin_category,gmp_validity,each_dose,each_dose_unit,mfrequency,mdays,mcountunit,drug_use');
            }
            exit(json_encode(array('status'=>1,'data'=>$da2,'data2'=>$da)));
        }
		if($id){
			$this->default_db->load('drug');
			$da = $this->default_db->get_one('`id` = '.$id,'id,trade_name,specification,enterprise,characteristic,sales_status,vip_price,drug_category,origin_category,gmp_validity,each_dose,each_dose_unit,mfrequency,mdays,mcountunit,drug_use');
			exit(json_encode(array('status'=>1,'data'=>$da)));
		}
		exit('{"status":"-1"}');
	}
	public function editDrugDetails(){
        $this->default_db->load('drug_pn');
        $doid = explode(',',trim($_POST['doid'],','));
        $dname= explode(',',trim($_POST['dname'],','));
        $dodata = explode('|',trim($_POST['dodata'],'|'));
        foreach($doid as $k=>$v){
            $tv = str_replace('info_','',$v);
            $td = $dodata[$k];
            $da = $this->default_db->get_one('`rid` = '.$tv.' AND `pnid` = "'.$_POST['pnid'].'"');
            if($da) $this->default_db->update('`title` = "'.$_POST['title'].'",`pnname` = "'.$_POST['rpText'].'",`dname` = "'.$dname[$k].'",`data_str` = "'.$td.'"','`rid` = '.$tv.' AND `pnid` = "'.$_POST['pnid'].'"');
            else $this->default_db->insert(array('data_str'=>$td,'rid'=>$tv,'pnid'=>$_POST['pnid'],'title'=>$_POST['title'],'pnname'=>$_POST['rpText'],'dname'=>$dname[$k],'add_time'=>time(),'userid'=>$this->userid));
        }
    }
	public function addDrugPN(){
		$ac = $_POST['ac'];
		$nid = $_POST['nid'];
		$ids = explode(',',trim($_POST['ids'],','));
		$data = explode('|',trim($_POST['data'],'|'));
		$title = $_POST['title'];
		$pnname = $_POST['pnname'];
		$full_name = $_POST['full_name'];
		$mobile = $_POST['mobile'];
		$age = $_POST['age'];
		$sex = 1==$_POST['sex']?37:38;
		$address = $_POST['address'];


		$where = ['nid'=>$nid];
		$infos = [
			'status'=>3,
		];
		$this->default_db->load('con_pn');
		$rows = $this->default_db->get_one($where,'nid');
		if ($rows) {
			 $this->default_db->update($infos,$where);
		}
		/*if($ac == 'edit'){
			$eids = explode(',',trim($_POST['eids'],','));
			$da = $this->default_db->delete('`id` in ('.trim($_POST['eids'],',').')');
		}*/
		if ($ids) {
		    $pnid = date('YmdHis');//会出现重复
		    //药品信息
		    $this->default_db->load('drug');
		    $_drug = $this->default_db->select(['id'=>['in'=>$ids]],'id,trade_name,specification,enterprise,vip_price');
		    foreach ($_drug as $v) {
		        $drug[$v['id']] = $v;
		    }
		    //生成处方单
		    $user_id = 0;//提交
		    $time = time();
		    $this->default_db->load('drug_pn');
    		foreach($ids as $k=>$v){
    		    /*var_dump(array('userid'=>$user_id,'data_str'=>$data[$k],'pnname'=>$pnname,'title'=>$title,'dname'=>$drug[$v]['trade_name'],'add_time'=>$time,'pnid'=>$pnid,'rid'=>$v));
    		    die;*/
    			if($v){
    			    //,'data1'=>$tmpDa[1],'data2'=>$tmpDa[2],'data3'=>$tmpDa[3],'data4'=>$tmpDa[4],'data5'=>$tmpDa[5],'data6'=>$tmpDa[6],
    				$this->default_db->insert(array('userid'=>$user_id,'data_str'=>$data[$k],'pnname'=>$pnname,'title'=>$title,'dname'=>$drug[$v]['trade_name'],'add_time'=>$time,'pnid'=>$pnid,'rid'=>$v));
    			}
    		}
    		//var_dump($this->default_db->error());
    		
			$this->default_db->load('drug_pn_user');
			$drug_total_quantity = $drug_total_price = 0;
			foreach($ids as $k=>$v){
    			if($v){
    			    $data_str = explode('_',$data[$k]);
    			    $drug_total_quantity += $data_str[6];
    			    $drug_total_price += $drug[$v]['vip_price'] * $data_str[6];
    				$this->default_db->insert(array('userid'=>$user_id,'data_str'=>$data[$k],'pnname'=>$pnname,'title'=>$title,'dname'=>$drug[$v]['trade_name'],'add_time'=>$time,'pnid'=>$pnid,'rid'=>$v,'uid'=>$nid));
    			}
			}

				//电子签名
				$signature_id = intval($_POST['signature_id']);
				if ($signature_id) {
					$this->default_db->load('signature');
					$signature = $this->default_db->get_one(['id'=>$signature_id],'id,img');
					$verify_sign = $signature['img'];
				} else {
					$signature_value = $_POST['signature_value'];
					//var_dump($signature_value);die;
					//电子签名数据转为本地图片
					$res = imgDown(['type'=>'base64','img'=>$signature_value,'user_id'=>$verify_user_id]);
					$verify_sign = 'SUCCESS' == $res['status']?$res['data']['src']:'';
					//var_dump($res);die;
				}
			
			//创建正式处方单
			$info = [
    	        'prescription_no'=>$pnid,
    	        'store_id'=>1,
    	        'store_name'=>'',
    	        'user_nickname'=>$full_name,
    	        'doctor_user_id'=>$this->admin_user_id,
    	        'doctor_name'=>$this->admin_user_nickname,
    	        'hospital_name'=>'',
				'full_name'=>$full_name,
				'address'=>$address,
				'sex'=>$sex,
				'user_id'=>$nid,
				'age'=>$age,
				'mobile'=>$mobile,
    	        'diagnose'=>$pnname,
    	        'drug_total_quantity'=>$drug_total_quantity,
				'drug_total_price'=>$drug_total_price,
				'doc_sign'=>$verify_sign,
    	        'create_time'=>time(),
    	    ];
    	    $this->default_db->load('prescription');
    	    $prescription_id = $this->default_db->insert($info,true);
    	    if (0<$prescription_id) {
    	        $this->default_db->load('prescription_drug');
    	        foreach($ids as $k=>$v){
    	            $data_str = explode('_',$data[$k]);
    	            $info2 = [
        	            'prescription_id'=>$prescription_id,
        	            'prescription_no'=>$pnid,
        	            'drug_id'=>$v,
        	            'drug_name'=>$drug[$v]['trade_name'],
        	            'drug_sku_properties'=>'规格：'.$drug[$v]['specification'],
        	            'drug_price'=>$drug[$v]['vip_price'],
        	            'drug_quantity'=>$data_str[6],
        	            'drug_sub_price'=>bcmul($data_str[6],$drug[$v]['vip_price'],2),
        	            'drug_unit'=>$data_str[4],
        	            'drug_usage'=>$data[$k],
        	        ];
        	        $this->default_db->insert($info2,true);
    	        }
    	        
    	    }
		}
		
        exit('{"status":"1","pnid":"'.$pnid.'","title":"'.$title.'"}');
	}


	public function getAllPN(){   
		$where = '`display` = 0';
		if($_REQUEST['search'] && $_REQUEST['search'] != 'undefined'){
			$where .= ' AND `dname` like "%'.$_REQUEST['search'].'%"';
		}
		$this->default_db->load('drug_pn');
		$da = $this->default_db->select($where,'*','','add_time DESC');
		
		foreach($da as $v) $data[$v['pnid']][] = $v;
		exit(json_encode($data));
	}

	public function doDefault(){
		if ($this->ajax) {
			$id = intval($_POST['id']);
			$where = ['id'=>$id];
			$this->default_db->load('drug');
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
		$id = intval($_POST['id']);
		$pnid = intval($_POST['pnid']);
		if ($id&&$pnid) {
			$where = array('rid'=>$id,'pnid'=>$pnid);
			$this->default_db->load('drug_pn');
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

	public function doPharmacist(){
		//  var_dump($_FILES);
		 if ($_POST['dosubmit']) {
			 $id = intval($_POST['id']);
			 $where = ['id'=>$id];
			 $this->default_db->load('pharmacy_doc');
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
			 $this->default_db->load('pharmacy_doc');
			 $row = $this->default_db->get_one($where);
			 if (!$row) $row = [];
			 output(1,'',['format'=>2,'data'=>$row]);
		 }
	 }
	
	 public function aditePharmacist(){   // 修改个人信息  上传图片
		if ($this->ajax) {
			$id = intval($_POST['useid']);
			$where = ['useid'=>$id];
			$res = $this->uploadFile(['file'=>$_FILES['myFile']]);
			$this->default_db->load('pharmacy_doc');
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

	public function consultation(){
		if ($_POST['dosubmit']) {
			$id = intval($_POST['id']);
			$where = ['id'=>$id];
			$this->default_db->load('con_pn');
			$row = $this->default_db->get_one($where,'id');
			//var_dump($row);die;
			$info = [
				'name'=>$_POST['name'],
				'nid'=>$_POST['nid'],
				'ages'=>$_POST['ages'],
				'sex'=>$_POST['sex'],
				//'pnid'=>$_POST['pnid'],both
				'both'=>$_POST['both'],
				'idcard'=>$_POST['idcard'],
				'nation'=>$_POST['nation'],
				'address'=>$_POST['address'],
				'mi_card_no'=>$_POST['mi_card_no'],
				'phone'=>$_POST['phone'],
				'update_time'=>date('YmdHis',time()),
			];
			if ($row) {//编辑
				$res = $this->default_db->update($info,$where);
			} else {//新增
				$info['pnid'] = date('YmdHis',time());
				$info['created_time'] = date('YmdHis',time());
				$res = $this->default_db->insert($info,true);
			//	var_dump($this->default_db->error());die;
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
			$id = intval($_GET['nid']);
			$where = ['nid'=>$id];
			$this->default_db->load('con_pn');
			$row = $this->default_db->get_one($where);
			if (!$row) $row = [];
			output(1,'',['status'=>2,'data'=>$row]);
		}
	} 
	
	public function doStatus(){
		if ($this->ajax) {
			$id = intval($_POST['nid']);
			$where = ['nid'=>$id];
			$this->default_db->load('con_pn');
			$row = $this->default_db->get_one($where,'nid');
			$info = [
				'status'=>$_POST['status'],
			];
			if ($row) {//编辑
				$res = $this->default_db->update($info,$where);
			}
			if ($res) {
				$status = 1;
				$erro = '保存成功';
			} else {
				$status = -1;
				$erro = '保存失败';
			}
			output($status,$erro);
		}
	}


	public function doDrug(){
		if ($_POST['dosubmit']) {
			$id = intval($_POST['id']);
			$where = ['id'=>$id];
			$this->default_db->load('drug');
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
			$this->default_db->load('drug');
			$row = $this->default_db->get_one($where);
			if (!$row) $row = [];
			output(1,'',['format'=>2,'data'=>$row]);
		}
	}

  




}
?>