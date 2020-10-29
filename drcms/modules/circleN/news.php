<?php
defined('IN_drcms') or exit('No permission resources.');
class news{
	public $default_db,$member_db;
	public $ajax = 0;
	public $pageSize = 10;
    public function __construct(){
        //ob_end_clean();
		$this->default_db = pc_base::load_model('default_model');
		$this->member_db = pc_base::load_model('member_model');
		$this->ajax = intval($_GET['ajax']);
		$this->userid = param::get_cookie('_userid');
		$this->page = intval($_GET['page']);
    }
	public function init(){
		
	}
	public function lists(){
		
	}
	public function getNews(){
		if ($this->ajax) {
			$catid = intval($_GET['catid']);
			$where = '`status`=99 AND `catid`='.$catid.' AND `thumb`<>""';
			if (isset($_GET['label'])&&$_GET['label']) {
				$label = $_GET['label'];
				if ('topic'==$label) {
					$where .= ' AND `tag` <> ""';
				} else {
					$where .= '';
				}
			}
			$this->default_db->load('news2');
			$_datas = $this->default_db->listinfo($where,'inputtime desc',$this->page,$this->pageSize);
			if (empty($_datas[0])) $_datas = array();
			$status = 0;
			$datas = $hits = array();
			if ($_datas) {
				$status = 1;
				$ids = $ids2 = array();
				$modelid = 68;
				foreach($_datas as $r){
					$ids[] = $r['id'];
					$ids2[] = 'c-'.$modelid.'-'.$r['id'];
					$r['tag'] = explode(',',trim($r['tag'],','));
					$datas[$r['id']] = $r;
				}
				//var_dump($ids);die;
				if ($ids) {
					$where = array('id'=>array('in',$ids));
					$this->default_db->load('news2_data');
					$model = $this->default_db->select($where,'id,thumbList');
					foreach($model as $r){
						if ($r&&$datas[$r['id']]) {
							$imgs = string2array($r['thumbList']);
							if(99==$datas[$r['id']]['style1']&&1==count($imgs)) $datas[$r['id']]['style1'] = mt_rand(0,2);
							$datas[$r['id']]['imgs'] = $imgs;
						}
					}
					//浏览量
					$where = array('hitsid'=>array('in',$ids2));
					$this->default_db->load('hits');
					$_hits = $this->default_db->select($where,'hitsid,show_views');
					foreach($_hits as $r){
						$hitsid = explode('-',$r['hitsid']);
						$hits[$hitsid[2]] = $r;
					}
				}
				$datas = array_values($datas);
			}
			$jsonData = array(
				'status'=>$status,
				'data'=>array('datas'=>$datas,'hits'=>$hits),
			);
			exit(json_encode($jsonData));
		}
	}
	public function getNewsFy(){
		//$cid = 61;//新型肺炎圈
        //$where = '`status` < 2 AND `cid` = '.$cid;
		$cid = array_filter(explode(',',$_GET['cid']));
		$where = ['status'=>['<',2],'cid'=>['in',$cid]];
		//$where = '`status` < 2';
		//$this->pageSize = 20;
		$this->default_db->load('cn_dynamic');
		$_datas = $this->default_db->listinfo($where,'`add_time` DESC',$this->page,$this->pageSize);
		if ($_datas) {
			$status = 1;
			$user_ids = $cids = array();
			foreach($_datas as $v){
				unset($v['lat'],$v['lng'],$v['address']);
				$v['add_date'] = $this->getLastTime($v['add_time']);
				$v['order'] = json_decode($v['order'],true);
				//读取图片尺寸
				$v['imgs_w'] = $v['imgs_h'] = false;
	//			if($v['img_count']=='1'){
	//				$v['aa'] = getimagesize(siteurl(1).$v['imgs']);
	//				if($v['aa']){ 
	//					$v['imgs_w'] = $v['aa'][0];
	//					$v['imgs_h'] = $v['aa'][1];
	//				}
	//			} 
				//
				if ($v['description']) $v['content'] = $v['description'];
				$datas[] = $v;
				if (!in_array($v['userid'],$user_ids)) $user_ids[] = $v['userid'];
				if (!in_array($v['cid'],$cids)) $cids[] = $v['cid'];
			}

			$where2 = array('id'=>array('in',$cids));
			$this->default_db->load('cn_list');
			$cidDa = $this->default_db->select($where2);

			foreach($cidDa as $v){
				$cidData[$v['id']]['name'] = $v['name'];
				$cidData[$v['id']]['logo'] = $v['logo'];
			}
            //if(2 == param::get_cookie('_userid')) var_dump($cidDa);die;
			if ($user_ids) {
				$where3 = array('userid'=>array('in',$user_ids));
				$this->default_db->load('member_detail');
				$userDa = $this->default_db->select($where3);
				foreach($userDa as $v) $userData[$v['userid']]['portrait'] = $v['portrait'];
				$where4 = array('from_uid'=>$this->userid,'to_uid'=>array('in',$user_ids));
				$this->default_db->load('cn_follow');
				$followDa = $this->default_db->select($where4);
				foreach($followDa as $v) $followData[$v['to_uid']] = 1;
			}
		} else {
			$status = 0;
			$datas = $cidData = $userData = $topicData = $topicDetail = $followData = array();
		}
		$jsonData = array('status'=>$status,'data'=>$datas,'cidData'=>$cidData,'userData'=>$userData,'topicData'=>$topicData,'topicDetail'=>$topicDetail,'followData'=>$followData);
		exit(json_encode($jsonData));
	}
	private function getLastTime($targetTime){
		$todayLast = strtotime(date('Y-m-d 23:59:59'));
		$agoTimeTrue = time() - $targetTime;
		$agoTime = $todayLast - $targetTime;
		$agoDay = floor($agoTime / 86400);
		if ($agoTimeTrue < 60){
			$result = '刚刚';
		}elseif($agoTimeTrue < 3600){
			$result = (ceil($agoTimeTrue / 60)).'分钟前';
		}elseif($agoTimeTrue < 3600 * 12) {
			$result = (ceil($agoTimeTrue / 3600)).'小时前';
		}elseif($agoDay == 0){
			$result = '今天 '.date('H:i',$targetTime);
		}elseif($agoDay == 1){
			$result = '昨天 '.date('H:i',$targetTime);
		}elseif($agoDay == 2){
			$result = '前天 '.date('H:i',$targetTime);
		}elseif($agoDay > 2 && $agoDay < 16){
			$result = $agoDay.'天前 '.date('H:i',$targetTime);
		}else{
			$format = date('Y') != date('Y', $targetTime)?"Y-m-d H:i":"m-d H:i";
			$result = date($format,$targetTime);
		}
		return $result;
	}
}
?>