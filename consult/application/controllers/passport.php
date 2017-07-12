<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Passport
 */
class Passport extends MY_Controller {
    const MAX_ERR_LOGIN_NUM = 5;
    const WAIT_UNLOCK_LOGIN_TIME = 1800;

    public function __construct() {
        parent::__construct(FALSE);
        $this->load->language('user');
    }

    public function index() {
        $this->login();
    }

    public function login() {
        if ($this->input->is_ajax_request()) {
            $account = $this->input->post('account', TRUE);
            $password = $this->input->post('password', TRUE);
            $captcha = $this->input->post('captcha', TRUE);

            if (empty($account) || empty($password) || empty($captcha))
                self::json(lang('miss_parameter'));

            $this->load->library('Securimage');
            $img = new Securimage();
            if (!$valid = $img->check($captcha))
                self::json(lang('valid_img_error'));
			
            $user_data = $this->user_model->info(array(
                'name' => $account
            ));
            if (empty($user_data))
                self::json(lang('account_not_exists'));

            if ($user_data['err_login_num'] >= self::MAX_ERR_LOGIN_NUM) {
                if (time() - strtotime($user_data['err_login_time']) <= self::WAIT_UNLOCK_LOGIN_TIME) {
                    self::json(sprintf(lang('login_more_max_num'), self::MAX_ERR_LOGIN_NUM));
                } else {
                    $this->user_model->update(array('uid'=>$user_data['uid']), array(
                        'err_login_num' => 0,
                        'err_login_time' => '0000-00-00 00:00:00',
                    ));
                }
            }

            if (self::encrypt_pwd($password, $user_data['salt']) != $user_data['pwd']) {
                $this->user_model->update(array('uid'=>$user_data['uid']), array(
                    'err_login_num' => 'err_login_num+1',
                    'err_login_time' => date('Y-m-d H:i:s', time()),
                ), array('err_login_num'));
                self::json(lang('login_pwd_error'));
            }

            if ($user_data['status'] != 1)
                self::json(lang('login_user_forbid'));

            $update_user_info = array(
                'err_login_num' => 0,
                'err_login_time' => '0000-00-00 00:00:00',
                'login_num' => 'login_num + 1',
                'last_login_ip' => $this->input->ip_address(),
                'last_login_time' => date('Y-m-d H:i:s', time()),
            );
            $res = $this->user_model->update(array('uid'=>$user_data['uid']), $update_user_info, array('login_num'));
            $this->set_user_login($user_data);
            self::record_log('login', lang('log_user_login_title'),  sprintf(lang('log_user_login_content'), $account), $user_data['uid']);
            self::json(lang('operation_success'), self::SUCCESS_STATUS);
        } else {
            $this->data['captcha_image'] = $this->get_captcha();
            $this->load->view('passport/login', $this->data->all());
        }
    }

    public function captcha() {
        self::json(lang('operation_success'), array('url'=>$this->get_captcha()));
    }

    public function upgrade() {
        $this->load->view('passport/upgrade', $this->data->all());
    }

    public function logout() {
        if ($this->is_logged_in(FALSE)) {
            $this->session->unset_userdata();
            $this->session->sess_destroy();
        }
        redirect(site_url('passport/login'));
    }
}