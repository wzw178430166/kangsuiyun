<?php
defined( 'IN_drcms' )or exit( 'No permission resources.' );


class news {
	public function __construct() {
		$this->default_db = pc_base::load_model('default_model');
		$this->page = isset($_GET['page'])&&$_GET['page']?intval($_GET['page']):1;
		$this->pageSize = isset($_GET['pagesize'])&&$_GET['pagesize']?intval($_GET['pagesize']):15;
		$this->ajax = intval($_REQUEST['ajax']);
		
		$this->userid = param::get_cookie('_userid');

	}
	
	function getDatas($is_return = 0){ 
		$this->default_db->load('news2');
		$userid = param::get_cookie('_userid');
		$this->pageSize = 50; 
		$where = '`status` = 99 AND thumb<>""'; 
		if(isset($_REQUEST['catid'])){
			$where .= ' AND catid='.intval($_REQUEST['catid']); 
		}
		if(!empty($_REQUEST['search']))$where .= ' AND `title` like "%'.$_REQUEST['search'].'%"';
		if($_REQUEST['topic'] == 1) $where .= ' AND `tag` <> ""';
		
		$datas_ = $this->default_db->listinfo($where,'inputtime DESC',$this->page,$this->pageSize,'',10,'',array(),'id,catid,title,thumb,style,style1,url,author,islink,tag,count_r,inputtime');  
		$lng = $_POST['lng'];
		$lat = $_POST['lat'];
 		
		$modelid = 68;
		if(!empty($datas_)){
			foreach($datas_ as $k=>$v){
				$tagArr = explode(',',trim($v['tag'],','));
				if($tagArr) foreach($tagArr as $j) $v['topic'] .= '<span>#'.$j.'#</span>';
				$inid[] = $v['id'];  
				$datas[$v['id']] = $v;
				$hitsid[]='c-'.$modelid.'-'.$v['id'];
			}
			//副表
			$this->default_db->load('news2_data');
			$datas2 = $this->default_db->select(array('id'=>array('in',$inid)),'id,thumbList'); 

			foreach($datas2 as $k=>$v){
				$thumbList = $v['thumbList']? string2array($v['thumbList']) : array();
				if(!$is_return){
					$v['thumbList'] = json_encode($thumbList ); 
				}
				$datas[$v['id']]['thumbList'] = $thumbList;   
			}
			//阅读数
			$this->default_db->load('hits');
			$views_ = $this->default_db->select(array('hitsid'=>array('in',$hitsid))); 
			foreach($views_ as $v){
				$id = explode('-',$v['hitsid']);
				$views[$id[2]] = $v;
			}
			//整合
			$datas_ = $datas; 
			$datas = array();
			foreach( $datas_ as $v){
				if(empty($views[$v['id']]['show_views'])){
					$views[$v['id']] = $this->create_views('c-'.$modelid.'-'.$v['id']); 
				}
				$v['views'] = $views[$v['id']]['show_views']; 
				$datas['data'][] = $v;   
			}
			$datas['status'] = 1;

			if($is_return){
				return $datas;
			}else{
				echo json_encode($datas);
			}
        }else{
            echo json_encode(array('status'=>-1));
        }
	}
	function getNewsData(){
        $this->default_db->load('news2');
        $data = $this->default_db->listinfo('`catid` = 96 AND `status` = 99','inputtime DESC',$this->page,$this->pageSize);
        exit(json_encode(array('data'=>$data)));
	}
	function create_views($hitsid){
		$this->default_db->load('hits');
		$r = array(
			'hitsid'=>$hitsid,
			'catid'=>$_GET['catid'],
			'views'=>0,  
			'yesterdayviews'=>0,
			'dayviews'=>0,
			'weekviews'=>0,
			'monthviews'=>0,
			'updatetime'=>SYS_TIME,
			'show_views'=>random(mt_rand(3, 5)), 
		);
		$this->default_db->insert($r);   
		return $r;
	}
	
	function getshow($is_return = 0){ 
		$this->default_db->load('news2');
		$userid = param::get_cookie('_userid'); 
		$id = intval($_REQUEST['id']);
		$where = 'id='.$id;  //`status` = 99 AND 
		
		$datas = $this->default_db->get_one($where); 
		$this->default_db->load('news2_data');
		$datas2 = $this->default_db->get_one(array('id'=>$id)); 
		 
		$datas['oth'] = $datas2; 
		$datas['oth']['count_z'] = $this->getZan();
					  					 
		if($is_return){
			return $datas; 
		}else{
			echo json_encode($datas);
		}
	}
	//读取赞数 
	function getZan(){
		$this->default_db->load('zan_list');
		$zan = $this->default_db->count(array('type'=>'news2','userid'=>$this->userid));
		
		return $zan;
	}
	
	function showTuijian(){
		$this->default_db->load('news2');
		$userid = param::get_cookie('_userid');
		$this->pageSize = 3;  
		$where = '`status` = 99 AND thumb<>""'; 
		if(isset($_REQUEST['catid'])){
			//$where .= ' AND catid='.intval($_REQUEST['catid']); 
		}
	
		$datas_ = $this->default_db->listinfo($where,'inputtime DESC',$this->page,$this->pageSize,'',10,'',array(),'id,title,thumb,style,url,author,islink,tag,count_r,inputtime');   
		$lng = $_POST['lng'];
		$lat = $_POST['lat'];

		if(!empty($datas_)){ 
			$datas = array();
			$datas['data'] = $datas_; 
			$datas['status'] = 1;

			if($is_return){
				return $datas;
			}else{
				echo json_encode($datas);
			}
        }else{
            echo json_encode(array('status'=>-1));
        }
	}
}