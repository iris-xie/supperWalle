<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class User
 */
class User extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->language('user');
		$this->load->model('group_model');
    }

    public function index() {
        $cur_page = $this->get_cur_page();
        $name = $this->input->get_post('name', TRUE);
        $where = array();
        if ($name)
            $where['name'] = $name;
        $this->data['name'] = $name;
        $result = $this->user_model->page_lists($where, 'uid DESC', self::PER_PAGE_NUM, $cur_page);
        $this->data['page'] = $this->pagination($result['count'], site_url('user/index'));
        $this->data->set($result);
		$this->data['group_list'] = $this->group_model->lists(NULL, NULL, NULL, 'gid');
        $this->data['add_pri'] = $this->has_privileges('user', 'add');
        $this->data['edit_pri'] = $this->has_privileges('user', 'edit');
        $this->data['set_status_pri'] = $this->has_privileges('user', 'set_status');
        $this->data['acl_ctl_pri'] = $this->has_privileges('user', 'acl_ctl');
        $this->load->view('user/index', $this->data->all());
    }

    public function add() {
        $account = $this->input->post('name', TRUE);
		$gid = $this->input->post('gid', TRUE);
        $password = $this->input->post('pwd', TRUE);
        $email = $this->input->post('email', TRUE);
        $svn_account = $this->input->post('svn_account', TRUE);
        if (empty($account) || empty($gid) || empty($password) || empty($email))
            self::json(lang('miss_parameter'));

        $user_info = $this->user_model->info($account, 'name');
        if (!empty($user_info))
            self::json(sprintf(lang('account_exists'), $account));

        $user_info = $this->user_model->info($email, 'email');
        if (!empty($user_info))
            self::json(sprintf(lang('email_exists'), $email));
		
        $salt = self::make_salt();
        $data = array(
            'name' => $account,
			'gid' => $gid,
            'pwd' => $this->encrypt_pwd($password, $salt),
            'salt' => $salt,
            'email' => $email,
            'svn_account' => $svn_account,
            'add_time' => date('Y-m-d H:i:s', time()),
        );
        $uid = $this->user_model->add($data);
        self::record_log('user', lang('log_add_account_title'), sprintf(lang('log_add_account_content'), $account), $uid, $uid);
        if ($uid)
            self::json(lang('operation_success'), self::SUCCESS_STATUS);
        else
            self::json(lang('operation_fail'));

    }

    public function edit() {
        $uid = $this->input->post('uid', TRUE);
		$gid = $this->input->post('gid', TRUE);
        $password = $this->input->post('pwd', TRUE);
        $email = $this->input->post('email', TRUE);
        $svn_account = $this->input->post('svn_account', TRUE);
        if (empty($uid) || empty($gid) || empty($password) || empty($email))
            self::json(lang('miss_parameter'));

        $user_info = $this->user_model->info($uid, 'uid');
        if (empty($user_info))
            self::json(lang('account_not_exists'));

        if ($email != $user_info['email']) {
            $user_info = $this->user_model->info($email, 'email');
            if (!empty($user_info))
                self::json(sprintf(lang('email_exists'), $email));
        }

        $update_data = array(
			'gid' => $gid,
            'email' => $email,
            'svn_account' => $svn_account,
        );
        if ($password != $user_info['pwd']) {
            $salt = self::make_salt();
            $update_data['salt'] = $salt;
            $update_data['pwd'] = $this->encrypt_pwd($password, $salt);
        }
        $this->user_model->update(array('uid'=>$uid), $update_data);
        self::record_log('user', lang('log_edit_account_title'), sprintf(lang('log_edit_account_content'), $user_info['name']), $uid);
        self::json(lang('operation_success'), self::SUCCESS_STATUS);
    }

    public function set_status() {
        $status = $this->input->post('status', TRUE);
        $uid = $this->input->post('uid', TRUE);
        if (!in_array($status, array(1, 2)))
            self::json(lang('miss_parameter'));

        $user_info = $this->user_model->info($uid, 'uid');
        if (empty($user_info))
            self::json(lang('account_not_exists'));

        $res = $this->user_model->update(array('uid'=>$uid), array(
            'status' => $status
        ));
        self::record_log('user', lang('log_edit_account_title'), ($status == 1 ? sprintf(lang('account_set_open'), $user_info['name']) : sprintf(lang('account_set_close'), $user_info['name'])), $uid, $res);
        if ($res)
            self::json(lang('operation_success'), self::SUCCESS_STATUS);
        else
            self::json(lang('operation_fail'));
    }

    public function acl_ctl() {
        $uid = $this->input->get('uid', TRUE);
        $module_config = $this->module_model->get_module_lists();
        $user_info = $this->user_model->info(array('uid'=>$uid),'uid');
        $channel_list = $this->channel_model->lists();
        $this->data->set(array(
            'module_config'    => $module_config,
            'user'             => $user_info,
            'my_module_rights' => explode(',',$user_info['module_rights']),
            'my_action_rights' => explode(',',$user_info['action_rights']),
            'my_project_rights' => explode(',',$user_info['project_rights']),
            'channel_list' => $channel_list,
        ));
        $this->data['set_acl_pri'] = $this->has_privileges('user', 'set_acl');
        $this->load->view('user/acl_ctl', $this->data->all());
    }

    public function set_acl() {
        $uid = $this->input->get('uid', TRUE);
        $module = $this->input->get('module', TRUE);
        $action = $this->input->get('action', TRUE);
        $project = $this->input->get('project', TRUE);

        if (empty($uid) || empty($module) || empty($action))
            self::json(lang('miss_parameter'));

        $user_info = $this->user_model->info($uid, 'uid');
        if (empty($user_info))
            self::json(lang('account_not_exists'));

        $res = $this->user_model->update(array('uid'=>$uid), array(
            'module_rights' => $module,
            'action_rights' => $action,
            'project_rights' => $project,
        ));
        self::record_log('acl', lang('log_permission_title'), sprintf(lang('log_permission_content'), $module, $action, $project), $uid, $res);
        if ($res)
            self::json(lang('operation_success'), self::SUCCESS_STATUS);
        else
            self::json(lang('operation_fail'));
    }
}