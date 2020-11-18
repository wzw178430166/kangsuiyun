<?php
defined('IN_drcms') or exit('No permission resources.');
$session_storage = 'session_'.pc_base::load_config('system','session_storage');
// pc_base::load_sys_class($session_storage);  
// pc_base::load_app_class('authority','dataManage',0);
// pc_base::load_app_class('privManage','dataManage',0); 
class index {
	public $default_db,$content_db,$authority;
	public $authData = array();
	public $ajax = 0;//异步访问标识
    public $pageSize = 20;//每页条数
    public $template = 'ksy';
	public function __construct() {
		// $this->authority = new authority();
		// $this->authData = $this->authority->authData;
		// $this->default_db = pc_base::load_model('default_model');
		// $this->content_db = pc_base::load_model('content_model');
		// $this->member_db = pc_base::load_model('member_model');
		// $this->ajax = intval($_GET['ajax']);
        // $this->page = isset($_GET['page'])?intval($_GET['page']):1;
	}

    public function init(){
        $this->guanliindex();
       
    }
    
	public function guanliindex(){
        //系统管理 主界面
        if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {

        }else {
            include template($this->template,'GL_guanli');
        }
	}


    

    public function newlist(){ 
        //数据查看
        if (isset($_POST['dosubmit'])&&$_POST['dosubmit']) {

        }else {
            include template($this->template,'news_list');
        }
    }




   

}



?>