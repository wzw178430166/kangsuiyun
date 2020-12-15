<?php
defined('IN_drcms') or exit('No permission resources.');
class medical {
	public $default_db,$content_db,$member_db,$authority;
	public $template = 'nutrition';
	public $style = 'nutrition';
	public $ajax = 0;
	public function __construct() {

		$this->default_db = pc_base::load_model('default_model');

		$this->content_db = pc_base::load_model('content_model');

		$this->member_db = pc_base::load_model('member_model');

		$this->ajax = intval($_GET['ajax']);

		$this->page = isset($_GET['page'])&&intval($_GET['page'])?$_GET['page']:1;

		$this->encrypt = 161301;
		
		if ($this->ajax) {
			pc_base::load_app_class('demo','core',0);
			$demo = new demo();
		}
	}
    public function init() {

	}


    public function med_patient(){
		
		if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {
			$this->default_db->setting('nut');
			$id = intval( $_POST['uid'] );
	  //	  $this->load( 'member' );
			$this->default_db->load_no('nut_member');
			// $wheres = 'id = '.$id.' AND userid = '.$this->userid;
			$wheres = 'userid = '.$this->userid;
		   // $_category = $this->db->get_one( $wheres );
		 

			$info = $_POST['info'];
			$where = '';
			$data['o_diagnosis'] = $info['o_diagnosis'];
			$data['number2'] = $info['number2'];
			$data['number3'] = $info['number3'];
			$json = json_encode( $data );
			if ( !empty( $_category ) ) {
				//编辑
				$json = json_encode( $data );
				$where = '`userid` = ' . $this->userid;
				$result = $this->db->update( array( 'sosnumber' => $json ), $where );
			} else {
				//添加
			  //  $this->db->insert( array( 'sosnumber' => $json, 'userid'=>$this->userid ) );
			}
			$status = 1;
			$erro = '操作成功';
		    exit( '{"status":' . $status . ',"erro":"' . $erro . '"}' );
        }else {
           	if ($this->ajax) {
				$userid = intval($_GET['uid']);
				$member = array();
				$patient= array();
				if (0 < $userid) {
					$this->default_db->setting('nut');
					//$this->default_db->load_no('nut_member');
					$this->default_db->load('member');
					
					$where = '`userid` = '.$userid;
					$member = $this->default_db->get_one($where);
					$model = $this->default_db->get_one($where,'nickname,sex,regdate,userid,lastdate');
					if (is_array($model)) $member = array_merge($model,$member);
                    //$this->default_db->load('patient');
				}
				$status = $member?1:0;
				$jsonData  = array(
					'status'=>$status,
					'data'=>array('member'=>$member,'patient'=>$patient),
				);
				exit(json_encode($jsonData));
			}


			include template('nutrition','med_patient');
            //include template($this->template,'med_anthropometry');
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
	
	public function med_bi_report(){
        //生化报告单
        if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {

        }else {
            include template($this->template,'med_bi_report');
        }
	}

	public function med_consultation(){
        //会诊单
        if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {

        }else {
            include template($this->template,'med_consultation');
        }
	}

	public function med_history(){
        //营养病史
        if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {

        }else {
            include template($this->template,'med_history');
        }
	}
	
   

}

?>