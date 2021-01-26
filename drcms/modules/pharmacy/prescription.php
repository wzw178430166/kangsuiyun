<?php
defined('IN_drcms') or exit('No permission resources.');
pc_base::load_app_class('authority','corp',0);
class prescription extends authority{
	public $store_id = 1;
	public function __construct() {
		parent::__construct();
		$this->store_id = 1||intval(param::get_cookie('store_id'));
	}
	public function init(){
		$this->index();
	}
	public function lists(){
		if ($this->ajax) {
			$where = ['store_id'=>$this->store_id];
			if ($_GET['nid']) $where['user_id'] = $_GET['nid'];
			if ($_GET['k']) $where['prescription_no'] = $_GET['k'];
			if ($_GET['status']) $where['status'] = $_GET['status'];
			if ($_GET['st']&&$_GET['et']) {
				$res = dateToTimeStamp(['st'=>$_GET['st'],'et'=>$_GET['et']]);
	 	    	$where .= ' AND `create_time`>='.$res['st'].' AND `create_time`<='.$res['et'];
			}
			$this->default_db->load('prescription');
			$this->pageSize=12;
			$rows = $this->default_db->listinfo($where,'create_time desc',$this->page,$this->pageSize);
			if ($rows) {
				$total = $this->default_db->number;
				$res = getDictionary(['category_id'=>[10,14],'is_value'=>1,'value_category'=>[14]]);
				$sex = $res['data'][10]?:[];
				$statuss = $res['data'][14]?:[];
				//var_dump($statuss);die;
				foreach ($rows as $k => $v) {
					$rows[$k]['sex'] = $sex[$v['sex']]['name']?:'';
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
			include template($this->template,'prescription',$this->style);
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
	public function test(){
	    $this->default_db->load('prescription_drug');
	    $data = $this->default_db->get_one(1);
	    var_dump($data);die;
		$res = $this->default_db->update('second_password="271025c44c25018a849c5c4270de7ef8"',1);
	}
	public function verify(){
		//var_dump($_POST);die;
		if ($_POST['dosubmit']) {
			//暂未验证审核人员本人操作
			$verify_user_id = intval($_POST['verify_user_id']);
			$prescription_no = $_POST['prescription_no'];
			if (!$verify_user_id || !$prescription_no) output('FAIL','非法操作');

			//验证密码
			$password = $_POST['password'];
			pc_base::load_app_class('account', 'corp', 0);
            $account = new account();
            $res = $account->checkPassword(['account_type'=>2,'type'=>2,'user_id'=>$verify_user_id,'password'=>$password]);
            //var_dump($res);die;
            if ('FAIL' == $res['status']) output(-1,$res['erro']);
			
			$this->default_db->load('administrator');
			$admin = $this->default_db->get_one(['user_id'=>$verify_user_id],'user_id,full_name,role_id');
			//需验证审核人员角色
			if (!$admin) output('FAIL','非法审核人员');
			
			//电子签名
			$signature_id = intval($_POST['signature_id']);
			if ($signature_id) {
				$this->default_db->load('signature');
				$signature = $this->default_db->get_one(['id'=>$signature_id],'id,img');
				$verify_sign = $signature['img'];
			} else {
				$signature_value = $_POST['signature_value'];
				//电子签名数据转为本地图片
				$res = imgDown(['type'=>'base64','img'=>$signature_value,'user_id'=>$verify_user_id]);
				$verify_sign = 'SUCCESS' == $res['status']?$res['data']['src']:'';
				//var_dump($res);die;
			}

			//是否验证处方属于该门店FAIL
			$where = ['prescription_no'=>$prescription_no];
			$info = ['status'=>$_POST['status'],'verify_status'=>$_POST['status'],'verify_user_id'=>$admin['user_id'],'verify_name'=>$admin['full_name'],'verify_time'=>SYS_TIME,'verify_reason'=>$_POST['opinion'],'verify_sign'=>$verify_sign];
			$this->default_db->load('prescription');
			$res = $this->default_db->update($info,$where);
			if ($res) {
				$status = 1;
				$erro = '处方已审核';
			} else {
				$status = 0;
				$erro = '审核失败';
			}
			output($status,$erro);
		}
	}
	public function delete(){
		$id = array_filter(explode(',', $_GET['id']));
		if ($id) {
			$where = ['id'=>['in',$id]];
			$this->default_db->load('prescription');
			$res = $this->default_db->delete($where);
			if ($res) {
				$status = 1;
				$erro = '删除成功';
				$this->default_db->load('prescription_drug');
				$this->default_db->delete(['prescription_id'=>['in',$id]]);
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
}
?>