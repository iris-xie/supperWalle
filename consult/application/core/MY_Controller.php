<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Controller 扩展类
 * 
 */
class MY_Controller extends CI_Controller {

    const SUCCESS_STATUS = 1;
    const DEFAULT_ERROR_STATUS = 0;
    const NO_LOGIN_STATUS = -99;
    const UN_AUTHORIZED = 403;
    const STATIC_VERSION = '2.1';
    const SYS_NAME = '上线系统';
    const PER_PAGE_NUM = 20;
    const PAGE_QUERY_SEGMENT = 'page';
    const CAPTCHA_SESS_KEY = 'captcha_sess_key';
    const MCRYPT_KEY = 'on_-_sys';

    private $url_module;
    private $url_action;

    public function __construct($need_login = TRUE) {
        parent::__construct();
		if (!headers_sent()) {
			header("Cache-Control:no-cache,must-revalidate,no-store");
			header("Pragma:no-cache");
			header("Expires:-1");
		}
        $this->url_module = $this->uri->segment(1) ? $this->uri->segment(1) : 'welcome';
        $this->url_action = $this->uri->segment(2) ? $this->uri->segment(2) : 'index';

        if ($need_login === TRUE) {
            $this->is_logged_in();
            $this->check_privileges();
        }
        $this->init();
	}

    private function init() {

        date_default_timezone_set('Asia/Shanghai');
        $module_config=$this->module_model->get_module_lists();
        $user_info = $this->user_model->info($this->session->userdata('uid'),'uid');

        $this->load->library('Set_Data', array(
            'static_prefix'     => base_url('static/hadmin').'/',
            'static_version'    => self::STATIC_VERSION,
            'sys_name'          => self::SYS_NAME,
            'module_config'    => $module_config,
            'user'             => $user_info,
        ), 'data');
    }

    /**
     * 返回json格式数据
     * @param type $msg
     * @param type $error
     * @param type $data
     */
    public static function json($msg='', $error = self::DEFAULT_ERROR_STATUS, $data = array()) {
        $str = json_encode(
            array(
                'status'        => $error,
                'data'          => $data,
                'msg'           => $msg
            )
        );
        echo @preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
        exit;
    }

    /**
     * 默认值
     * @param type $index
     * @param type $data
     * @param type $default
     * @return type
     */
    public static function default_data($index, $data, $default='') {
        return isset($data[$index]) ? $data[$index] : $default;
    }

    /**
     * 密码加密
     * @param $pwd
     * @param $salt
     * @return string
     */
    protected static function encrypt_pwd($pwd, $salt) {
        return md5('online-'.md5($pwd).'-sys'.$salt);
    }

    /**
     * 用户是否登录
     * @param bool|FALSE $location
     */
    protected function is_logged_in($location = TRUE)
    {
        if (!$this->session->userdata('uid') || !$this->session->userdata('name')) {
            if ($location === FALSE)
                return FALSE;
            else {
                if ($this->input->is_ajax_request()) {
                    self::json(self::NO_LOGIN_STATUS, 'Login Invalid!');
                } else {
                    redirect(site_url('passport/login'));
                }
            }
        }
        return TRUE;
    }

    /**
     * @param $module
     * @param $action
     * @return bool
     */
    protected function has_privileges($module, $action) {
        $module = strtolower($module);
        $action = strtolower($action);
		$user = $this->user_model->info($this->session->userdata('uid'),'uid');
		if($user['module_rights'] === 1 || $module === 'welcome')
			return TRUE;
		$module_rights = explode(',', $user['module_rights']);
		$action_rights = explode(',', $user['action_rights']);
		$module_info = $this->module_model->info(array('module'=>$module, 'parent_id'=>0));

		if (empty($module_info) || !in_array($module_info['id'], $module_rights))
			return FALSE;
		
		$action_info = $this->module_model->info(array('module'=>$module, 'action'=>$action));
		if (empty($action_info) || !in_array($action_info['id'], $action_rights))
			return FALSE;
		return TRUE;
    }

    /**
     * @return bool
     */
    protected function check_privileges() {
        $user = $this->user_model->info($this->session->userdata('uid'),'uid');
        $where = explode(',', $user['module_rights']);
        $module_list = $this->module_model->get_module_map($where);

        $this->url_module = strtolower($this->url_module);
        $this->url_action = strtolower($this->url_action);
        //超管权限
        if( $user['module_rights'] != 1 && $this->url_module != 'welcome' && $this->url_module != 'yzm')
        {
            if (in_array($this->url_module, $module_list))
            {
                $action_list = $this->module_model->get_action_map($this->url_module);

                if (in_array($this->url_action, $action_list)&&!empty($this->url_action))
                {
                    return TRUE;
                }
            }
            if ($this->input->is_ajax_request()) {
                self::json('403 Forbidden', self::UN_AUTHORIZED);
            } else {
                show_error('403 Forbidden!', 403);
            }
        }
        return TRUE;
    }

    /**
     * 设置用户登录
     * @param $user_data
     */
    protected function set_user_login(array $user_data) {
        $sess_data = array(
            'uid' => $user_data['uid'],
			'gid' => $user_data['gid'],
            'name' => $user_data['name'],
            'email' => $user_data['email'],
        );
        return $this->session->set_userdata($sess_data);
    }

    /**
     * @param int $num
     * @return string
     */
    protected function make_salt($num = 4) {
        $this->load->helper('string');
        return random_string('alnum', $num);
    }

    /**
     * @param $total_rows
     * @param $url
     * @return mixed
     */
    protected function pagination($total_rows, $url) {
        $this->load->library('pagination');
        $config['base_url'] = $url;
        $config['total_rows'] = $total_rows;
        $config['per_page'] = self::PER_PAGE_NUM;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = self::PAGE_QUERY_SEGMENT;
        $this->pagination->initialize($config);
        return $this->pagination->create_links();
    }

    /**
     * @return int
     */
    protected function get_cur_page() {
        $page = $this->input->get_post(self::PAGE_QUERY_SEGMENT);
        if (!is_numeric($page) || $page <= 0) {
            $page = 0;
        }
        return $page;
    }

    /**
     * @param $cid
     * @return mixed
     */
    protected function set_channel_svn_info($cid) {
        $channel_info = $this->channel_model->info($cid, 'cid');
        if (empty($channel_info))
            self::json(lang('channel_not_exists'));
        Svn::config($channel_info['svn_account'], self::mcrypt_decode($channel_info['svn_pwd']), $channel_info['svn_root_dir']);
        return $channel_info;
    }

    /**
     * 记录操作日志
     * @param $type
     * @param $title
     * @param string $content
     * @param int $object_id
     * @param int $status
     * @return mixed
     */
    protected function record_log($type, $title, $content = '', $object_id = 0, $status = 1) {
        $status = intval($status);
        $this->load->model('log_model');
        $data = array(
            'module_name' => $this->uri->segment(1),
            'action_name' => $this->uri->segment(2),
            'object_id' => intval($object_id),
            'type' => $type,
            'title' => $title,
            'content' => $content,
            'uid' => $this->session->userdata('uid'),
            'ip' => $this->input->ip_address(),
            'status' => $status && $status != 2 ? 1 : 2,
            'add_time' => date('Y-m-d H:i:s', time()),
        );
        return $this->log_model->add($data);
    }

    /**
     * @param string $uid
     * @param string $field
     * @return string
     */
    public function get_user_info($uid = '', $field = '') {
        static $users_info;
        if (empty($uid))
            $uid = $this->session->userdata('uid');

        if (!isset($users_info[$uid])) {
            $info = $this->user_model->info(array('uid'=>$uid));
            $users_info[$uid] = $info;
        }
        if (empty($field))
            return $users_info[$uid];
        return isset($users_info[$uid][$field]) ? $users_info[$uid][$field] : '';
    }

    /**
     * @param int $img_width
     * @param int $img_height
     * @param string $captcha_sess_key
     * @return mixed
     */
    protected function get_captcha($img_width = 80, $img_height = 28, $captcha_sess_key = self::CAPTCHA_SESS_KEY) {
        $this->load->helper('captcha');
        $vals = array(
            'word' => rand(1000,9999),
            'img_path' => './static/captcha/',
            'img_url' => base_url().'static/captcha/',
            'font_path' => '../system/fonts/texb.ttf',
            'img_width' => $img_width,
            'img_height' => $img_height,
            'expiration' => 7200,
        );
        $this->session->set_userdata(array($captcha_sess_key => $vals['word']));
        $cap = create_captcha($vals);
        return $cap['image'];
    }

    /**
     * @param $path
     * @return array
     */
	protected static function rsync_path($path) {
		$path_arr = explode('/', $path);
		$len = count($path_arr);
		$start_path_arr = array_slice($path_arr, 0, $len -2);
		$start_path = implode('/', $start_path_arr);
		$end_path_arr = array_slice($path_arr, $len - 2);
		$end_path = implode('/', $end_path_arr);
		return array($start_path, $end_path);
	}

    /**
     * 加密
     * @param $data
     * @return mixed
     */
    protected function mcrypt_encode($data) {
        $this->load->library('encrypt');
        return $this->encrypt->encode($data, self::MCRYPT_KEY);
    }

    /**
     * 解密
     * @param $data
     * @return mixed
     */
    protected function mcrypt_decode($data) {
        $this->load->library('encrypt');
        return $this->encrypt->decode($data, self::MCRYPT_KEY);
    }
}	