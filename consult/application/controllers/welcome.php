<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Welcome
 */
class Welcome extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->language('user');
    }

    public function index() {
		$this->load->model('group_model');
		$group_info = $this->group_model->info($this->session->userdata('gid'), 'gid');
		$this->data['group_info'] = $group_info;
		$this->load->view('index', $this->data->all());
	}

    public function home() {
        $this->load->view('welcome/home', $this->data->all());
    }

    public function edit_pwd() {
        $old_password = $this->input->post('old_password', TRUE);
        $new_password = $this->input->post('new_password', TRUE);
        $rnew_password = $this->input->post('rnew_password', TRUE);
        $uid = $this->input->post('uid', TRUE);
        if (!$old_password || !$new_password || !$rnew_password)
            self::json(lang('miss_parameter'));

        $uid = $this->session->userdata('uid');
        $user_info = $this->user_model->info(array('uid'=>$uid), 'uid');
        if (empty($user_info) || $user_info['status'] != 1) {
            self::json(lang('account_not_exists'));
        }
        if ($user_info['pwd'] != $this->encrypt_pwd($old_password, $user_info['salt']))
            self::json(lang('old_pwd_error'));

        $salt = $this->make_salt();
        $res = $this->user_model->update(array('uid'=>$uid), array(
            'pwd' => $this->encrypt_pwd($new_password, $salt),
            'salt' => $salt
        ));
        self::record_log('user', lang('log_modify_pwd_title'), sprintf(lang('log_modify_pwd_content'), $user_info['name']), $uid, $res);
        if ($res)
            self::json(lang('operation_success'), self::SUCCESS_STATUS);
        else
            self::json(lang('operation_fail'));
    }
}