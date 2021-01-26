<?php

defined('IN_drcms') or exit('No permission resources.');
pc_base::load_app_class('authority', 'corp', 0);
class authed extends authority {
    public function __construct() {
        parent::__construct();
        //var_dump($this->template.'------'.$this->style);die;
    }
    public function login() {
        if ($_POST['dosubmit']) {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);
            $data = [];
            if (!$username || !$password) output(0,'账号密码不能为空');

            $is_code = intval($_POST['is_code']);
            if (1==$is_code) {
                $code = trim($_POST['code']);
                if (!$code) output(0,'验证码不能为空');
                if ($_SESSION['code'] != strtolower($code)) output(0,'验证码不正确');
            }
            
            //查询帐号
            //department_id 部门暂未启用
            $where = ['user_name'=>$username];
            $this->default_db->load('administrator');
            $account = $this->default_db->get_one($where, 'user_id,user_name,full_name,password,encrypt,role_id,store_id,status');
            if (!$account) output(0,'账号不存在');
            if (0==$account['status']) output(0,'账号已停用');
            $role_id = intval($account['role_id']);
            $this->default_db->load('role');
            $role = $this->default_db->get_one(['id'=>$role_id], 'id,system');
            //验证权限
            if (1>3) output(0,'非常抱歉，您无权限访问');
            $user_id = intval($account['user_id']);
            $password = password($password, $account['encrypt']);
            if ($account['password'] !== $password) output(0,'用户名或密码不正确');
            //更新登录ip 时间
            $info = ['last_login_ip'=>ip(),'last_login_time'=>time()];
            $this->default_db->load('administrator');
            $this->default_db->update($info, $where);
            //$cookietime = time() + 30*86400;
            $cookietime = 0;//登录信息过期时间
            $http_user_agent = $_SERVER['HTTP_USER_AGENT'];
            $drcms_auth_key = md5(pc_base::load_config('system', 'auth_key') . $http_user_agent);
            $drcms_auth = sys_auth($user_id . "\t" . $password, 'ENCODE', $drcms_auth_key);
            param::set_cookie('auth', $drcms_auth, $cookietime);
            param::set_cookie('admin_user_id', $user_id, $cookietime);
            param::set_cookie('admin_user_name', $username, $cookietime);
            param::set_cookie('admin_full_name', $account['full_name'], $cookietime);
            param::set_cookie('role_id', $role_id, $cookietime);
            $_SESSION['role_id'] = $role_id;

            //子系统设置
            //门店信息设置
            $store_id = intval($account['store_id']);
            if (0<$store_id) {
                $this->default_db->load('store');
                $store = $this->default_db->get_one(['id'=>$store_id]);
                param::set_cookie('store_id', $store_id, $cookietime);
                param::set_cookie('store_name', $store['name'], $cookietime,1);
            }


            //兼容原系统
            /*param::set_cookie('authUserid', $user_id, $cookietime);
            param::set_cookie('authUsername', $username, $cookietime);
            param::set_cookie('authNickname', $user['full_name'], $cookietime);
            param::set_cookie('roleid', $role_id, $cookietime);*/
            //var_dump($role_id);die;
            $_SESSION['role_id'] = $role_id;
            $data = ['role_id' => $role_id];
            output(1,'登录成功',['format'=>2,'data'=>$data]);
        } else {
            //pc_base::load_sys_class('form', '', 0);
            //var_dump(template($this->template,'login',$this->style));die;
            include template($this->template,'login',$this->style);
        }
    }

    public function doPassword() {
        if ($_POST['dosubmit']) {
            $old_password = $_POST['old_password'];
            $password = $_POST['password'];
            $param = array('user_id'=>param::get_cookie('user_id'),'old_password'=>$old_password,'password'=>$password);
            //var_dump($datas);die;
            pc_base::load_app_class('account', 'corp', 0);
            $account = new account();
            $result = $account->changePassword($param);
            if ('SUCCESS'==$result['status']) {
                $status = 1;
            } else {
                $status = 0;
            }
            exit('{"status":'.$status.',"erro":"'.$result['erro'].'"}');
        } else {
            include $this->admin_tpl('doPassword');
        }
    }

    public function logout() {
        $time = time() - 3600;
        $s = $_SERVER['SERVER_PORT'] == '443' ? 1 : 0;
        $sessionName = session_name();
        if (isset($_COOKIE[$sessionName])) {
            setcookie($sessionName, '', $time, pc_base::load_config('system', 'cookie_path'), pc_base::load_config('system', 'cookie_domain'), $s);
        }
        session_destroy();
        foreach ($_COOKIE as $key => $value) {
            //var_dump($key);die;
            setcookie($key, '', $time, pc_base::load_config('system', 'cookie_path'), pc_base::load_config('system', 'cookie_domain'), $s);
        }
        $system = $_GET['system'];
        switch ($system) {
            case 'pharmacy':
                $url = '/pharmacy/login.html';
                break;
            default:
                $url = '/?m=corp&c=authed&a=login';
                break;
        }
        $forward = $_GET['forward']?urlencode($forward):'';
        if ($forward) $url .= '?&forward='.$forward;
        //var_dump($url);die;
        if ($this->ajax) {
            output(0,'已退出',['format'=>2,'data'=>['forward'=>$forward]]);
        } else {
            header('location:'.$url);
        }
    }
}
