<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Channel
 */
class Channel extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->language('channel');
    }

    public function index() {
        $cur_page = $this->get_cur_page();
        $where = array();
        $result = $this->channel_model->page_lists($where, 'cid DESC', self::PER_PAGE_NUM, $cur_page);
        $this->data['page'] = $this->pagination($result['count'], site_url('channel/index'));
        foreach ($result['list'] as &$val) {
            $val['svn_pwd'] = $this->mcrypt_decode($val['svn_pwd']);
        }
		$this->data->set($result);
		$this->data['add_pri'] = $this->has_privileges('channel', 'add');
        $this->data['edit_pri'] = $this->has_privileges('channel', 'edit');
		$this->data['set_status_pri'] = $this->has_privileges('channel', 'set_status');
        $this->load->view('channel/index', $this->data->all());
    }

    public function add() {
        if (!$this->input->is_ajax_request())
            self::json(lang('illegal_request'));

        $name = $this->input->post('name', TRUE);
        $svn_account = $this->input->post('svn_account', TRUE);
        $svn_pwd = $this->input->post('svn_pwd', TRUE);
        $svn_root_dir = $this->input->post('svn_root_dir', TRUE);
        $log_dir = $this->input->post('log_dir', TRUE);
        $prepare_shell = $this->input->post('prepare_shell', TRUE);
        $online_shell = $this->input->post('online_shell', TRUE);
        $prepare_rollback_shell = $this->input->post('prepare_rollback_shell', TRUE);
        $online_rollback_shell = $this->input->post('online_rollback_shell', TRUE);

        if (empty($name) || empty($svn_account) || empty($svn_pwd) || empty($svn_root_dir) || empty($prepare_shell) || empty($online_shell) || empty($prepare_rollback_shell) || empty($online_rollback_shell))
            self::json(lang('miss_parameter'));

        $data = array(
            'name'              => $name,
            'svn_account'       => $svn_account,
            'svn_pwd'           => $this->mcrypt_encode($svn_pwd),
            'svn_root_dir'      => str_replace('\\', '/', $svn_root_dir),
            'log_dir'           => str_replace('\\', '/', $log_dir),
            'prepare_shell'      => str_replace('\\', '/', $prepare_shell),
            'online_shell'      => str_replace('\\', '/', $online_shell),
            'prepare_rollback_shell'    => str_replace('\\', '/', $prepare_rollback_shell),
            'online_rollback_shell'    => str_replace('\\', '/', $online_rollback_shell),
            'add_uid' => $this->session->userdata('uid'),
            'add_time' => date('Y-m-d H:i:s', time()),
        );
        $res = $this->channel_model->add($data);
        self::record_log('channel', lang('log_add_channel_title'), sprintf(lang('log_add_channel_content'), $name), $res, $res);
        if ($res)
            self::json(lang('operation_success'), self::SUCCESS_STATUS);
        else
            self::json(lang('operation_fail'));
    }

    public function edit() {
        if (!$this->input->is_ajax_request())
            self::json(lang('illegal_request'));

        $cid = intval($this->input->post('cid', TRUE));
        $name = $this->input->post('name', TRUE);
        $svn_account = $this->input->post('svn_account', TRUE);
        $svn_pwd = $this->input->post('svn_pwd', TRUE);
        $svn_root_dir = $this->input->post('svn_root_dir', TRUE);
        $log_dir = $this->input->post('log_dir', TRUE);
        $prepare_shell = $this->input->post('prepare_shell', TRUE);
        $online_shell = $this->input->post('online_shell', TRUE);
        $prepare_rollback_shell = $this->input->post('prepare_rollback_shell', TRUE);
        $online_rollback_shell = $this->input->post('online_rollback_shell', TRUE);

        if (empty($cid) || empty($name) || empty($svn_account) || empty($svn_pwd) || empty($svn_root_dir) || empty($prepare_shell) || empty($online_shell) || empty($prepare_rollback_shell) || empty($online_rollback_shell))
            self::json(lang('miss_parameter'));

        $channel_info = $this->channel_model->info($cid, 'cid');
        if (empty($channel_info))
            self::json(lang('channel_not_exists'));

        if ($channel_info['name'] != $name) {
            $channel_info = $this->channel_model->info($name, 'name');
            if (!empty($channel_info))
                self::json(sprintf(lang('channel_name_exists'), $name));
        }

        $data = array(
            'name'              => $name,
            'svn_account'       => $svn_account,
            'svn_pwd'           => $this->mcrypt_encode($svn_pwd),
            'svn_root_dir'      => str_replace('\\', '/', $svn_root_dir),
            'log_dir'           => str_replace('\\', '/', $log_dir),
            'prepare_shell'      => str_replace('\\', '/', $prepare_shell),
            'online_shell'      => str_replace('\\', '/', $online_shell),
            'prepare_rollback_shell'    => str_replace('\\', '/', $prepare_rollback_shell),
            'online_rollback_shell'    => str_replace('\\', '/', $online_rollback_shell),
            'update_uid' => $this->session->userdata('uid'),
            'update_time' => date('Y-m-d H:i:s', time()),
        );

        $res = $this->channel_model->update(array('cid'=>$cid), $data);
        self::record_log('channel', lang('log_edit_channel_title'), sprintf(lang('log_edit_channel_content'), $name), $cid, $res);
        if ($res)
            self::json(lang('operation_success'), self::SUCCESS_STATUS);
        else
            self::json(lang('operation_fail'));
    }

    public function set_status() {
        if (!$this->input->is_ajax_request())
            self::json(lang('illegal_request'));

        $status = $this->input->post('status', TRUE);
        $cid = $this->input->post('cid', TRUE);
        if (!in_array($status, array(1, 2)))
            self::json(lang('error_parameter'));

        $channel_info = $this->channel_model->info($cid, 'cid');
        if (empty($channel_info))
            self::json(lang('channel_not_exists'));

        $where = array('cid'=>$cid);
        $res = $this->channel_model->update($where, array(
            'update_uid' => $this->session->userdata('uid'),
            'update_time' => date('Y-m-d H:i:s', time()),
            'status' => $status,
        ));
        self::record_log('channel', lang('log_edit_channel_title'), $status == 1 ? sprintf(lang('log_channel_set_open'), $channel_info['name']) : sprintf(lang('log_channel_set_close'), $channel_info['name']), $cid, $res);
        if ($res)
            self::json(lang('operation_success'), self::SUCCESS_STATUS);
        else
            self::json(lang('operation_fail'));
    }
}