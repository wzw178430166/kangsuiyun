<?php
defined('IN_drcms') or exit('No permission resources.');
class monitor {
	public $default_db;
	public $template = 'nutrition';
	public $style = 'nutrition';
	public $ajax = 0;
	public function __construct() {
		$this->default_db = pc_base::load_model('default_model');
		$this->ajax = intval($_GET['ajax']);
    }
     //营养监测
    public function init() {

    }
   
    public function mon_bi_monitor(){
        //生化检查监测图
        if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {

        }else {
            include template($this->template,'mon_bi_monitor');
        }
    }

    public function mon_ad_monitor(){
        //医嘱监测图
        if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {

        }else {
            include template($this->template,'mon_ad_monitor');
        }
    }

}

?>