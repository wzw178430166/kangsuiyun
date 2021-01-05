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
		$id = intval($_POST['id']);
		if($id){
			$this->default_db->load('drug');
			$da = $this->default_db->get_one('`id` = '.$id,'id,trade_name,specification,enterprise,characteristic,sales_status,vip_price,drug_category,origin_category,gmp_validity,each_dose,each_dose_unit,mfrequency,mdays,mcountunit,drug_use');
			exit(json_encode(array('status'=>1,'data'=>$da)));
		}
		exit('{"status":"-1"}');
	}

	public function addDrugPN(){
		$ac = $_POST['ac'];
		$ids = explode(',',trim($_POST['ids'],','));
		$data = explode('|',trim($_POST['data'],'|'));
		$title = $_POST['title'];
        $pnname = $_POST['pnname'];
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
    		//创建正式处方单
    		//$this->default_db->load('prescription');
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

	public function docGetDrug(){
        $user_id = intval($_POST['user_id']);
        $where = '`user_id` = '.$user_id.' AND ';
        if(!$user_id) $where = '';
	    $this->default_db->load('member_prescription');
	    $da = $this->default_db->listinfo($where.'`doctor_user_id` = '.$this->user_id,'create_time DESC',$this->page,20);

	    foreach($da as $v){
            $pid[$v['prescription_no']] = $v['prescription_no'];
            $pnDid[$v['id']] = $v['id'];
            $pnddd[$v['id']] = $v['prescription_no'];
        }
        $this->default_db->load('member_prescription_data');
        $pnDataD = $this->default_db->select(array('prescription_id'=>array('in',$pnDid)));
	    foreach($pnDataD as $v) $pnMsg[$v['prescription_no']][$v['drug_id']] = $v['data_str'];

        $this->default_db->load('order3_prescription');
        $pidArr = $this->default_db->select(array('prescription_no'=>array('in',$pid)));
        foreach($pidArr as $v){
            $oid[$v['order_no']] = $v['order_no'];
            $pnDataDa[$v['order_no']] = $v;
        }
        $this->default_db->load('order3');
        if($_POST['sdate'] && $_POST['edate']) {
            $whereArr = array('order_no' => array('in', $oid), 'create_time' => array('>', strtotime($_POST['sdate'].' 00:00:00')), 'create_time' => array('<', strtotime($_POST['edate'].' 23:59:59')));
            $whereStr = '`order_no` in ('.implode(',',$oid).') AND `create_time` > '.strtotime($_POST['sdate'].' 00:00:00').' AND `create_time` < '.strtotime($_POST['edate'].' 23:59:59');
            if($_GET['status'] >= 1){
                $whereArr['status'] = intval($_GET['status']);
                $whereStr .= ' AND `status` = '.intval($_GET['status']);
            }
            $datas = $this->default_db->select($whereStr,'*','','create_time DESC');
            if($_GET['status'] > 0) $payTxt = '`status` = '.intval($_GET['status']);else $payTxt = '';
            $payMoney = $this->default_db->select($payTxt,'id,status,pay_money');
        }else{
            $whereArr = array('order_no'=>array('in',$oid));
            if($_GET['status'] >= 1) $whereArr['status'] = intval($_GET['status']);
            $datas = $this->default_db->select($whereArr,'*','','create_time DESC');
            if($_GET['status'] > 0) $payTxt = '`status` = '.intval($_GET['status']);else $payTxt = '';
            $payMoney = $this->default_db->select($payTxt,'id,status,pay_money');
        }
        $payMoneyNum = 0;
        foreach($payMoney as $v) $payMoneyNum += $v['pay_money'];
        if ($datas) {
            $status = 1;
            $order_ids = array();
            $statuss = array('已取消','待付款','已付款','已发货','交易成功');
            foreach($datas as $k=>$r){
                $order_ids[] = $r['id'];
                $datas[$k]['order_no_'] = $r['order_no'];
                $datas[$k]['order_no'] = substr_cut($r['order_no'],5,5);
                $datas[$k]['user_name'] = substr_cut($r['user_name'],3,4);
                $datas[$k]['create_time'] = date('Y-m-d H:i:s',$r['create_time']);
                $datas[$k]['statusStr'] = $statuss[$r['status']];
            }
            if ($order_ids) {
                $where2 = array('order_id'=>array('in',$order_ids));
                $this->default_db->load('order3_data');
                $_product = $this->default_db->select($where2);
                foreach($_product as $r){
                    $r['product_img'] = '/drug/images/yaopin.jpg';
                    $product[$r['order_id']][] = $r;
                }
            }
            $merchant = array(1=>array('id'=>1,'name'=>'乐享健官方店'));
        } else {
            $status = 0;
            $datas = $product = $merchant = array();
        }
        if(!$_GET['status'] || $_GET['status'] == 1){
            $where = '';
            $this->default_db->load('member_prescription');
            $newDa = $this->default_db->listinfo($where.'`doctor_user_id` = '.$this->user_id.' AND `process` = 1','create_time DESC',$this->page,20);
            foreach($newDa as $v){
                $pid[$v['prescription_no']] = $v['prescription_no'];
                $pnDid2[$v['id']] = $v['id'];
                $userId[$v['user_id']] =$v['user_id'];
                $pnDid_[$v['id']] = $v['user_id'];
            }
            $this->default_db->load('member_prescription_data');
            $pnDataD = $this->default_db->select(array('prescription_id'=>array('in',$pnDid2)));
            foreach($pnDataD as $v){
                $v['user_id'] = $pnDid_[$v['prescription_id']];
                $pnDataD_[$v['prescription_no']][] = $v;
                $pnMsg[$v['prescription_no']][$v['drug_id']] = $v['data_str'];
                $memberDa2_[$v['prescription_no']] = $v['user_id'];
                $tmpData = explode('_',$v['data_str']);
                $payMoneyNum += $tmpData[6] * $v['drug_price'];
            }
            $this->default_db->load('member');
            $memberDa = $this->default_db->select(array('userid'=>array('in',$userId)),'userid,username,nickname');
            foreach($memberDa as $v){
                $v['username'] = substr_cut($v['username'],3,4);
                $memberDa_[$v['userid']] = $v;
            }
            if($memberDa) $status = 1;
        }
        $jsonData = array(
            'status'=>$status,
            'data'=>array('pnddd'=>$pnddd,'memberDa2_'=>$memberDa2_,'pnDataD'=>$pnDataD_,'memberDa'=>$memberDa_,'datas'=>$datas,'product'=>$product,'merchant'=>$merchant,'pnMsg'=>$pnMsg,'pnDataDa'=>$pnDataDa),
            'payMoneyNum'=>$payMoneyNum
        );
        exit(json_encode($jsonData));
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
		$id = array_filter(explode(',', $_POST['id']));
		if ($id) {
			$where = ['id'=>['in',$id]];
			$this->default_db->load('drug');
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