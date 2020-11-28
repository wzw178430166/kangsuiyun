<?php
defined('IN_drcms') or exit('No permission resources.');
class medical {
	public $default_db;
	public $template = 'nutrition';
	public $style = 'nutrition';
	public $ajax = 0;
	public function __construct() {
		$this->default_db = pc_base::load_model('default_model');
		$this->ajax = intval($_GET['ajax']);
	}
    public function init() {

    }
    public function med_patient(){
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
				$member = $this->member_db->get_one($where,'userid');
				if ($member) exit('{"status":0,"erro":"账号已存在"}');
				$info = '`username` = "'.$username.'"';
				$where = '`userid` = '.$userid;
				$member = $this->member_db->get_one($where,'userid,phpssouid');
				$status = $this->member_db->update($info,$where);
				if ($status) {
					//sso
					$this->default_db->load('sso_members');
					$this->default_db->update($info,'`uid`='.$member['phpssouid']);
					//用户信息
					$this->member_db->set_model(10);
					$this->member_db->update($model,$where);
				}
			} else {
				$datas = array('roleid'=>13,'sectorid'=>1,'username'=>$username,'password'=>'000000','nickname'=>$model['realname'],'mobile'=>$username,'state'=>1,'model'=>$model);
				pc_base::load_app_class('user','dataManage',0);
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
					$where = '`userid` = '.$userid;
					$member = $this->member_db->get_one($where);
					$this->member_db->set_model(10);
					$model = $this->member_db->get_one($where,'realname,sex,age,idcard,province,city,area,street,community,address,lng,lat');
					if (is_array($model)) $member = array_merge($model,$member);
				}
				$status = $member?1:0;
				$jsonData  = array(
					'status'=>$status,
					'data'=>array('member'=>$member),
				);
				exit(json_encode($jsonData));
			}
			include template('nutrition','med_patient');
		}
	}

    public function med_anthropometry(){
        //人体测量  
        if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {

        }else {
            include template($this->template,'med_anthropometry');
        }
	}
	
	public function med_screening(){
        //风险筛选  
        if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {

        }else {
            include template($this->template,'med_screening');
        }
	}
	
	public function med_assessment(){
        //营养评估
        if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {

        }else {
            include template($this->template,'med_assessment');
        }
	}
	
	public function med_cure_plan(){
        //营养治疗计划
        if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {

        }else {
            include template($this->template,'med_cure_plan');
        }
	}
	
	public function med_follow_record(){
        //营养治疗计划
        if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {

        }else {
            include template($this->template,'med_follow_record');
        }
	}
	
	
   

}

?>