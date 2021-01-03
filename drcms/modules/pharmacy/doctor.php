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
		$this->default_db->load('drug_pn');
		if($ac == 'edit'){
			$eids = explode(',',trim($_POST['eids'],','));
			$da = $this->default_db->delete('`id` in ('.trim($_POST['eids'],',').')');
		}
		$pnid = date('YmdHis');
        $tmpDa2 = explode(',',$_POST['dname']);
		foreach($ids as $k=>$v){
			if($v){
				$tmpDa = explode(',',$data[$k]);
                //echo '<pre>';var_dump($tmpDa);echo '</pre>';
				$this->default_db->insert(array('userid'=>$this->userid,'data_str'=>$data[$k],'pnname'=>$pnname,'title'=>$title,'dname'=>$tmpDa2[$k],'data1'=>$tmpDa[1],'data2'=>$tmpDa[2],'data3'=>$tmpDa[3],'data4'=>$tmpDa[4],'data5'=>$tmpDa[5],'data6'=>$tmpDa[6],'add_time'=>time(),'pnid'=>$pnid,'rid'=>$v));
			}
		}
        exit('{"status":"1","pnid":"'.$pnid.'","title":"'.$title.'"}');
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