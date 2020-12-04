<?php
defined('IN_drcms') or exit('No permission resources.');
class advice {
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
    //医嘱

	public function adv_advice_msg(){
        //营养治疗计划
        if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {

        }else {
            include template($this->template,'adv_advice_msg');
        }
    }
   
    public function adv_intraintestinal(){
        //肠内营养
        if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {

        }else {
            include template($this->template,'adv_intraintestinal');
        }
    }

    public function adv_extraintestinal(){
        //肠外营养
        if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {

        }else {
            include template($this->template,'adv_extraintestinal');
        }
    }

    public function adv_long_term_advice(){
        //肠外营养
        if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {

        }else {
            include template($this->template,'adv_long_term_advice');
        }
    }

    public function adv_supportprogram(){
        //营养支持方案
        if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {

        }else {
            include template($this->template,'adv_supportprogram');
        }
    }

}

?>