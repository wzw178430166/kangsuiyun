<?php

require_once('../drcms/base.php'); 

$ac = $_GET['ac'];

switch($ac){
	case 's1':
		if (isset($_GET['channelname'])) {
			header('Location:/member/index.html?target=_self&tatget=_self');
		} else {
			require_once('style1/index.html'); 
		}
		break;
	case 's2':
		header('Location:/member/index.html?target=_self&tatget=_self');  
		//require_once('style2/index.html'); 
		break;
	case 's3':
		if (isset($_GET['channelname'])) {
			header('Location:/member/index.html?target=_self&tatget=_self');
		} else {
			require_once('style3/index.html'); 
		}
			break;
		case 's4':
				if (isset($_GET['channelname'])) {
					header('Location:/member/index.html?target=_self&tatget=_self');
				} else {
					require_once('style4/index.html'); 
				}
					break;
	default : mgo_index();
}


function mgo_index(){
	if($_GET['mobile']=='ios' && isset($_GET['version'])){
		if(floatval($_GET['version'])==2019090202){
//			include('index_ios.html');  
//			exit();    
		} 
	}
	
	
	require_once('index.html'); 
}
?>