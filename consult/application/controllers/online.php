<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Online
 */
class Online extends MY_Controller {

    const CODE_AGAIN_SEND_TEST = -3; // 更新送测
    const CODE_CANCEL_SEND_TEST = -2; // 撤销送测
    const CODE_NO_PASS = -1; //申请失败
    const CODE_APPLY = 0; // 申请中
    const CODE_APPLY_SUCCESS = 1; // 待送测
    const CODE_SEND_TEST = 2; // 已送测
    const CODE_SUBMIT_PREPARE = 3; // 提交预发布环境
    const CODE_PREPARE = 4; // 已上预发布环境
    const CODE_SUBMIT_ONLINE = 5; // 提交上线
    const CODE_ONLINE = 6; // 已上线
    const CODE_ROLLBACK = 7; // 已回滚
    private $_project_rights = NULL;

    public function __construct() {
        parent::__construct();
		set_time_limit(0);
        $this->load->library('svn');
        $this->load->model('online_model');
        $this->load->language('online');
        $this->_project_rights = explode(',', $this->get_user_info(NULL, 'project_rights'));
    }

    public function project_list() {
        $channel_list = $this->channel_model->lists(array('status'=>1), NULL, NULL,'cid');
        foreach ($channel_list as $key=>$val) {
            if (!in_array($val['cid'], $this->_project_rights))
                unset($channel_list[$key]);
        }
        $this->data['channel_list'] = $channel_list;
        $this->load->view('online/project_list', $this->data->all());
    }

    public function create() {
        $type = $this->input->post('type', TRUE);
        $cid = $this->input->get_post('cid', TRUE);
        $new_revision = $this->input->get_post('new_revision', TRUE);
        if (empty($cid)) {
            if ($this->input->is_ajax_request())
                self::json(lang('miss_parameter'));
            else
                redirect(site_url('online/project_list'));
        }

        $channel_info = $this->set_channel_svn_info($cid);
        $svn_info = Svn::info();
        $this->data['svn_account'] = $this->get_user_info(NULL, 'svn_account');
        $this->data['data'] = $svn_info;
        if ($this->input->is_ajax_request() && $type == 'diff') {
            $old_revision = intval($svn_info['revision']);
            $diff_data = Svn::status($new_revision, $old_revision);
            foreach ($diff_data as &$di) {
                $di['name'] = str_replace('\\', '/', $di['name']);
                $di['old_revision'] = $di['revision'];
            }
            self::json(lang('operation_success'), self::SUCCESS_STATUS, $diff_data);
        } else if ($this->input->is_ajax_request() && $type == 'create') {
            $title = $this->input->get_post('title', TRUE);
            $old_revision = $this->input->get_post('old_revision', TRUE);
            $new_revision = $this->input->get_post('new_revision', TRUE);
            $demand = $this->input->get_post('demand', TRUE);
            $test_note = $this->input->get_post('test_note', TRUE);

            $item = $this->input->get_post('item', TRUE);
            $kind = $this->input->get_post('kind', TRUE);
            $name = $this->input->get_post('name', TRUE);
            $revision = $this->input->get_post('revision', TRUE);
            $check = $this->input->get_post('check', TRUE);

            if (empty($title) || empty($old_revision) || empty($new_revision) || empty($demand))
                self::json(lang('miss_parameter'));

            if (!is_array($check) || count($check) == 0 || count($item) == 0 || count($item) != count($kind) || count($item) != count($name) || count($item) != count($revision))
                self::json(lang('no_selected_online_file'));

            $file_arr = array();
            $wc_revision_arr = array();
            $name = array_unique($name);
            $real_filename_arr = array();
            foreach ($name as $key=>$val) {
                if (in_array($val, $check)) {
                    $arr = array(
                        'item' => $item[$key],
                        'kind' => $kind[$key],
                        'name' => $val,
                        'revision' => $revision[$key],
                    );
                    $wc_file_info = Svn::wc_file_info($val);
                    if ($wc_file_info != FALSE && !empty($wc_file_info['r'])) {
                        $wc_revision = intval($wc_file_info['r']);
                        $wc_revision_arr[] = $wc_revision;
                        $arr['revision'] = $wc_revision;
                        $wc_diff_data = Svn::diff($wc_revision.':'.$new_revision, $val);
                        if ($wc_diff_data != FALSE) {
                            $arr['item'] = $wc_diff_data[0]['item'];
                            $arr['kind'] = $wc_diff_data[0]['kind'];
                        }
                    }
                    $file_arr[] = $arr;
                    $real_filename_arr[] = $val;
                }
            }

            if (count($file_arr) == 0)
                self::json(lang('no_selected_online_file'));
            //krsort($file_arr);

            $exists_files_arr = array();
            $online_lists = $this->online_model->lists('status in(0, 1, 2)', NULL, NULL);
            foreach ($online_lists as $ol) {
                $files = json_decode($ol['files'], TRUE);
                foreach ($files as $f) {
                    if (in_array($f['name'], $real_filename_arr)) {
                        $exists_files_arr[] = $f['name'];
                    }
                }
            }
            if (!empty($exists_files_arr))
                self::json(sprintf(lang('file_already_submit'), implode(PHP_EOL, $exists_files_arr).PHP_EOL));

            $data = array(
                'title' => $title,
                'demand' => $demand,
                'test_note' => $test_note,
                'cid' => $cid,
                'start' => $old_revision,
                'end' => $new_revision,
                'files' => json_encode($file_arr),
                'apply_uid' => $this->session->userdata('uid'),
                'apply_time' => date('Y-m-d H:i:s', time()),
                'status' => self::CODE_APPLY_SUCCESS,
            );
            $online_id = $this->online_model->add($data);
            self::record_log('online', lang('log_add_online_title'), sprintf(lang('log_add_online_content'), $title, $new_revision, $online_id), $online_id, $online_id);
            if ($online_id)
                self::json(lang('operation_success'), self::SUCCESS_STATUS);
            else
                self::json(lang('operation_fail'));
        }

        $this->data['cid'] = $cid;
        $channel_list = $this->channel_model->lists(NULL, NULL, NULL,'cid');
        $this->data['channel_list'] = $channel_list;
        $this->data['url'] = site_url('online/create');
        $this->data['channel_info'] = $channel_info;
        $this->load->view('online/create', $this->data->all());
    }

    public function apply_list() {
        $cid = intval($this->input->get_post('cid', TRUE));
        $where = array();
        if ($cid) {
            $where['cid'] = $cid;
        }
        $cur_page = $this->get_cur_page();
        $result = $this->online_model->page_lists($where, 'id DESC', self::PER_PAGE_NUM, $cur_page);
        foreach ($result['list'] as &$val) {
            $val['uname'] = $this->get_user_info($val['apply_uid'], 'name');
        }
        $this->data['page'] = $this->pagination($result['count'], site_url('online/send_online'));
        $this->data->set($result);
        $this->data['cid'] = $cid;
        $channel_list = $this->channel_model->lists(NULL, NULL, NULL,'cid');
        $this->data['channel_list'] = $channel_list;
        $this->data['url'] = site_url('online/apply_list');
		$this->data['create_pri'] = $this->has_privileges('online', 'create');
		$this->data['edit_pri'] = $this->has_privileges('online', 'edit');
		$this->data['cancel_pri'] = $this->has_privileges('online', 'cancel_send_test');
        $this->data['check_pri'] = $this->has_privileges('online', 'check');
        $this->load->view('online/apply_list', $this->data->all());
    }

    public function edit() {
        $online_id = intval($this->input->get_post('online_id', TRUE));
        $online_info = $this->online_model->info($online_id, 'id');
        if (empty($online_info)) {
            if ($this->input->is_ajax_request())
                self::json(lang('online_no_exists'));
            else
                redirect(site_url('online/apply_list'));
        }
        $cid = $online_info['cid'];
        if ($this->input->is_ajax_request()) {
            $title = $this->input->post('title', TRUE);
            $new_revision = $this->input->post('new_revision', TRUE);
            $demand = $this->input->get_post('demand', TRUE);
			$test_note = $this->input->get_post('test_note', TRUE);

            $item = $this->input->post('item', TRUE);
            $kind = $this->input->post('kind', TRUE);
            $name = $this->input->post('name', TRUE);
            $revision = $this->input->post('revision', TRUE);
            $check = $this->input->post('check', TRUE);

            if (empty($title) || empty($new_revision) || empty($demand))
                self::json(lang('miss_parameter'));

            if (!is_array($check) || count($check) == 0 || count($item) == 0 || count($item) != count($kind) || count($item) != count($name) || count($item) != count($revision))
                self::json(lang('no_selected_online_file'));

            $file_arr = array();
			$wc_revision_arr = array();
			$name = array_unique($name);
			$real_filename_arr = array();
            foreach ($name as $key=>$val) {
                if (in_array($val, $check)) {
                    $arr = array(
                        'item' => $item[$key],
                        'kind' => $kind[$key],
                        'name' => $val,
                        'revision' => $revision[$key],
                    );
					$wc_file_info = Svn::wc_file_info($val);
					if ($wc_file_info != FALSE && !empty($wc_file_info['r'])) {
						$wc_revision = $wc_file_info['r'];
						$wc_revision_arr[] = $wc_revision;
						$arr['revision'] = $wc_revision;
						$wc_diff_data = Svn::diff($wc_revision.':'.$new_revision, $val);
						if ($wc_diff_data != FALSE) {
							$arr['item'] = $wc_diff_data[0]['item'];
							$arr['kind'] = $wc_diff_data[0]['kind'];
						}
					}
					$file_arr[] = $arr;
					$real_filename_arr[] = $val;
                }
            }
            if (count($file_arr) == 0)
                self::json(lang('no_selected_online_file'));
            //krsort($file_arr);

			$exists_files_arr = array();
			$online_lists = $this->online_model->lists('status in(0, 1, 2) and id<>'.$online_id, NULL, NULL);
			foreach ($online_lists as $ol) {
				$files = json_decode($ol['files'], TRUE);
				foreach ($files as $f) {
					if (in_array($f['name'], $real_filename_arr)) {
						$exists_files_arr[] = $f['name'];
					}
				}
			}

            if (!empty($exists_files_arr))
                self::json(sprintf(lang('file_already_submit'), implode(PHP_EOL, $exists_files_arr).PHP_EOL));

            $old_files_arr = json_decode($online_info['files'], TRUE);
            foreach ($old_files_arr as $oval) {
                if (!in_array($oval['name'], $real_filename_arr)) {
                    Svn::update($oval['revision'], $oval['name']);
                }
            }

            $update_data = array(
                'title' => $title,
                'demand' => $demand,
				'test_note' => $test_note,
                'end' => $new_revision,
                'files' => json_encode($file_arr),
            );
            if ($online_info['status'] == self::CODE_SEND_TEST) {
                $update_data['status'] = self::CODE_AGAIN_SEND_TEST;
            }

            $res = $this->online_model->update(array('id'=>$online_id), $update_data);
            self::record_log('online', lang('log_edit_online_title'), sprintf(lang('log_edit_online_content'), $online_id), $online_id, $res);
            if ($res)
                self::json(lang('operation_success'), self::SUCCESS_STATUS);
            else
                self::json(lang('operation_fail'));
        }
        $this->set_channel_svn_info($cid);
        $svn_info = Svn::info();
        $this->data['data'] = $svn_info;
        $this->data['cid'] = $cid;
        $this->data['info'] = $online_info;
        $this->data['files'] = json_decode($online_info['files'], TRUE);
        $this->data['svn_account'] = $this->get_user_info(NULL, 'svn_account');
        $channel_list = $this->channel_model->lists(NULL, NULL, NULL,'cid');
        $this->data['channel_list'] = $channel_list;
        $this->data['url'] = site_url('online/edit');
        $this->load->view('online/edit', $this->data->all());
    }

    public function check() {
        if ($this->input->is_ajax_request()) {
            $online_id = intval($this->input->post('online_id', TRUE));
            $status = intval($this->input->post('status', TRUE));
            if (empty($online_id) || !in_array($status, array(1, -1)))
                self::json(lang('miss_parameter'));

            $update_data = array(
                'check_uid' => $this->session->userdata('uid'),
                'check_time' => date('Y-m-d H:i:s', time()),
                'status' => $status,
            );
            $res = $this->online_model->update(array('id'=>$online_id), $update_data);
            self::record_log('online', lang('log_check_online_title'), sprintf(($status == 1 ? lang('log_online_set_pass') : lang('log_online_set_refuse')), $online_id), $online_id, $res);
            if ($res)
                self::json(lang('operation_success'), self::SUCCESS_STATUS);
            else
                self::json(lang('operation_fail'));
        }
    }

    public function send_test() {
        $cid = intval($this->input->get_post('cid', TRUE));
        $where = "status in(1, 2, -3, -2) ";
        if ($cid) {
            $where .= 'AND cid='.$cid;
        }
        $cur_page = $this->get_cur_page();
        $result = $this->online_model->page_lists($where, 'id DESC', self::PER_PAGE_NUM, $cur_page);
        foreach ($result['list'] as &$val) {
            $val['uname'] = $this->get_user_info($val['apply_uid'], 'name');
        }
        $this->data['page'] = $this->pagination($result['count'], site_url('online/send_test'));
        $this->data->set($result);
        $this->data['cid'] = $cid;
        $channel_list = $this->channel_model->lists(NULL, NULL, NULL,'cid');
        $this->data['channel_list'] = $channel_list;
        $this->data['url'] = site_url('online/send_test');
		$this->data['send_test_pri'] = $this->has_privileges('online', 'submit_send_test');
		$this->data['edit_pri'] = $this->has_privileges('online', 'edit');
		$this->data['cancel_pri'] = $this->has_privileges('online', 'cancel_send_test');
        $this->data['submit_wait_prepare_pri'] = $this->has_privileges('online', 'submit_wait_prepare');
        $this->data['update_send_test_pri'] = $this->has_privileges('online', 'submit_update_send_test');
        $this->load->view('online/send_test', $this->data->all());
    }

    public function submit_send_test() {
        if ($this->input->is_ajax_request()) {
            $online_id = intval($this->input->get_post('online_id', TRUE));
            $online_info = $this->online_model->info($online_id, 'id');
            if (empty($online_info))
                self::json(lang('online_no_exists'));

            if ($online_info['status'] != self::CODE_APPLY_SUCCESS)
                self::json(lang('online_status_exception'));

            $this->set_channel_svn_info($online_info['cid']);
            $update_files_arr = json_decode($online_info['files'], TRUE);
            if (empty($update_files_arr))
                self::json(lang('no_update_file'));

            $update_files = '';
            foreach ($update_files_arr as $val) {
                $filename = $val['name'];
                $update_files = $update_files.' '.$filename;
            }
            $new_revision = $online_info['end'];
            $res = Svn::update($new_revision, $update_files);
            if ($res === false) {
                self::record_log('online', lang('log_test_title'), sprintf(lang('log_test_content', $online_id, Svn::last_cmd(), var_export($res, TRUE))), $online_id, 2);
                self::json(lang('log_test_fail'));
            }

            $update_data = array(
                'update_cmd' => Svn::last_cmd(),
                'send_test_uid' => $this->session->userdata('uid'),
                'send_test_time' => date('Y-m-d H:i:s', time()),
                'status' => self::CODE_SEND_TEST,
            );
            $res = $this->online_model->update(array('id'=>$online_id), $update_data);
            self::record_log('online', lang('log_test_title'), sprintf(lang('log_sql_test_content'), $online_id, Svn::last_cmd(), var_export($res, TRUE)), $online_id, $res);
            if ($res)
                self::json(lang('operation_success'), self::SUCCESS_STATUS);
            else
                self::json(lang('operation_fail'));
        }
    }

    public function cancel_send_test() {
        if ($this->input->is_ajax_request()) {
            $online_id = intval($this->input->get_post('online_id', TRUE));
            $online_info = $this->online_model->info($online_id, 'id');
            if (empty($online_info))
                self::json(lang('online_no_exists'));

            if ($online_info['status'] != self::CODE_SEND_TEST)
                self::json(lang('online_status_exception'));

            $cid = $online_info['cid'];
            $this->set_channel_svn_info($cid);
            $update_files_arr = json_decode($online_info['files'], TRUE);
            if (empty($update_files_arr))
                self::json(lang('no_update_file'));

            $tips = '';
            $flag = TRUE;
            foreach ($update_files_arr as $val) {
                $res = Svn::update($val['revision'], $val['name']);
                if (!$res) {
                    $flag = FALSE;
                }
                $tips .= implode(PHP_EOL, $res);
            }
            if ($flag === FALSE) {
                self::record_log('online', lang('log_cancel_test_title'), sprintf(lang('log_cancel_test_content'), $online_id, $tips), $online_id, 2);
                self::json(lang('cancel_test_fail'));
            }
            $update_data = array(
                'status' => self::CODE_CANCEL_SEND_TEST,
            );
            $res = $this->online_model->update(array('id'=>$online_id), $update_data);
            self::record_log('online', lang('log_cancel_test_title'), sprintf(lang('log_sql_cancel_test_content'), $online_id), $online_id, $res);
            if ($res)
                self::json(lang('operation_success'), self::SUCCESS_STATUS);
            else
                self::json(lang('operation_fail'));
        }
    }

    public function submit_update_send_test() {
        if ($this->input->is_ajax_request()) {
            $online_id = intval($this->input->get_post('online_id', TRUE));
            $online_info = $this->online_model->info($online_id, 'id');
            if (empty($online_info))
                self::json(lang('online_no_exists'));

            if ($online_info['status'] != self::CODE_AGAIN_SEND_TEST)
                self::json(lang('online_status_exception'));

            $this->set_channel_svn_info($online_info['cid']);
            $update_files_arr = json_decode($online_info['files'], TRUE);
            if (empty($update_files_arr))
                self::json(lang('no_update_file'));

            $update_files = '';
            foreach ($update_files_arr as $val) {
                $filename = $val['name'];
                $update_files = $update_files.' '.$filename;
            }
            $new_revision = $online_info['end'];
            $res = Svn::update($new_revision, $update_files);
            if ($res === false) {
                self::record_log('online', lang('log_update_test_title'), sprintf(lang('log_update_test_content', $online_id, Svn::last_cmd(), var_export($res, TRUE))), $online_id, 2);
                self::json(lang('log_update_test_fail'));
            }

            $update_data = array(
                'update_cmd' => Svn::last_cmd(),
                'send_test_uid' => $this->session->userdata('uid'),
                'send_test_time' => date('Y-m-d H:i:s', time()),
                'status' => self::CODE_SEND_TEST,
            );
            $res = $this->online_model->update(array('id'=>$online_id), $update_data);
            self::record_log('online', lang('log_update_test_title'), sprintf(lang('log_sql_update_test_content'), $online_id), $online_id, $res);
            if ($res)
                self::json(lang('operation_success'), self::SUCCESS_STATUS);
            else
                self::json(lang('operation_fail'));
        }
    }

    public function submit_wait_prepare() {
        if ($this->input->is_ajax_request()) {
            $online_id = intval($this->input->get_post('online_id', TRUE));
            $online_info = $this->online_model->info($online_id, 'id');
            if (empty($online_info))
                self::json(lang('online_no_exists'));

            if ($online_info['status'] != self::CODE_SEND_TEST)
                self::json(lang('online_status_exception'));

            $update_data = array(
                'status' => self::CODE_SUBMIT_PREPARE,
            );
            $res = $this->online_model->update(array('id'=>$online_id), $update_data);
            self::record_log('online', lang('log_submit_wait_prepare_title'), sprintf(lang('log_submit_wait_prepare_content'), $online_id, $online_info['title']), $online_id, $res);
            if ($res)
                self::json(lang('operation_success'), self::SUCCESS_STATUS);
            else
                self::json(lang('operation_fail'));
        }
    }

    public function prepare_online() {
        $cid = intval($this->input->get_post('cid', TRUE));
        $where = "status in(3, 4) ";
        if ($cid) {
            $where .= 'AND cid='.$cid;
        }
        $cur_page = $this->get_cur_page();
        $result = $this->online_model->page_lists($where, 'id DESC', self::PER_PAGE_NUM, $cur_page);
        foreach ($result['list'] as &$val) {
            $val['uname'] = $this->get_user_info($val['apply_uid'], 'name');
        }
        $this->data['page'] = $this->pagination($result['count'], site_url('online/prepare_online'));
        $this->data->set($result);
        $this->data['cid'] = $cid;
        $channel_list = $this->channel_model->lists(NULL, NULL, NULL,'cid');
        $this->data['channel_list'] = $channel_list;
        $this->data['prepare_online_pri'] = $this->has_privileges('online', 'submit_prepare_online');
        $this->data['submit_prepare_rollback_pri'] = $this->has_privileges('online', 'submit_prepare_rollback');
        $this->data['submit_wait_online_pri'] = $this->has_privileges('online', 'submit_wait_online');
        $this->load->view('online/prepare_online', $this->data->all());
    }

    public function submit_prepare_online() {
        if ($this->input->is_ajax_request()) {
            $online_id = intval($this->input->get_post('online_id', TRUE));
            $online_info = $this->online_model->info(array('id'=>$online_id));
            if (empty($online_info))
                self::json(lang('online_no_exists'));

            $channel_info = $this->channel_model->info(array('cid'=>$online_info['cid']));
            if (empty($channel_info))
                self::json(lang('channel_not_exists'));

            $log_dir = $channel_info['log_dir'];
            $file_name = $online_id.'.log';
            if (!file_exists($log_dir))
                self::json(lang('log_dir_no_exists'));

            if (!is_writable($log_dir))
                self::json(lang('log_dir_no_write'));

            $files_arr = json_decode($online_info['files'], TRUE);
            if (empty($files_arr))
                self::json(lang('no_update_file'));

            $files_data = array();
            foreach ($files_arr as $f) {
                list($start_path, $end_path) = self::rsync_path($f['name']);
                $files_data[] = $f['name'].' '.$f['item'].' '.$f['kind'].' '.$start_path.' '.$end_path;
            }
            $full_log_dir = $log_dir.'/'.$file_name;

            $prepare_cmd = $channel_info['prepare_shell'].' '.$full_log_dir;
            $exec_res = Svn::exec_cmd($prepare_cmd);
            if (Svn::check_exec_status($exec_res) == FALSE) {
                self::record_log('online', lang('log_prepare_title'), sprintf(lang('log_prepare_content'), $online_id, $prepare_cmd, var_export($exec_res, TRUE)), $online_id, 2);
                self::json(lang('prepare_fail'));
            }

            $update_data = array(
                'log_name' => $file_name,
                'log_dir' => $log_dir,
                'prepare_cmd' => $prepare_cmd,
                'prepare_online_uid' => $this->session->userdata('uid'),
                'prepare_online_time' => date('Y-m-d H:i:s', time()),
                'status' => self::CODE_PREPARE,
            );
            $res = $this->online_model->update(array('id'=>$online_id), $update_data);
            self::record_log('online', lang('log_prepare_title'), sprintf(lang('log_sql_prepare_content'), $online_id, $prepare_cmd, var_export($exec_res, TRUE)), $online_id, $res);
            if ($res)
                self::json(lang('operation_success'), self::SUCCESS_STATUS);
            else
                self::json(lang('operation_fail'));
        }
    }

    public function submit_prepare_rollback() {
        if ($this->input->is_ajax_request()) {
            $online_id = intval($this->input->get_post('online_id', TRUE));
            $online_info = $this->online_model->info(array('id'=>$online_id));
            if (empty($online_info))
                self::json(lang('online_no_exists'));

            $channel_info = $this->channel_model->info(array('cid'=>$online_info['cid']));
            if (empty($channel_info))
                self::json(lang('channel_not_exists'));

            $full_log_dir = $online_info['log_dir'].'/'.$online_info['log_name'];
            $rollback_cmd = $channel_info['prepare_rollback_shell'].' '.$full_log_dir;
            $exec_res = Svn::exec_cmd($rollback_cmd);
            if (Svn::check_exec_status($exec_res) == FALSE) {
                self::record_log('online', lang('log_prepare_rollback_title'), sprintf(lang('log_prepare_rollback_content'), $online_id, $rollback_cmd, var_export($exec_res, TRUE)), $online_id, 2);
                self::json(lang('prepare_rollback_fail'));
            }

            $update_data = array(
                'status' => self::CODE_SEND_TEST,
            );
            $res = $this->online_model->update(array('id'=>$online_id), $update_data);
            self::record_log('online', lang('log_prepare_rollback_title'), sprintf(lang('log_sql_prepare_rollback_content'), $online_id, $rollback_cmd, var_export($exec_res, TRUE)), $online_id, $res);
            if ($res)
                self::json(lang('operation_success'), self::SUCCESS_STATUS);
            else
                self::json(lang('operation_fail'));
        }
    }

    public function submit_wait_online() {
        if ($this->input->is_ajax_request()) {
            $online_id = intval($this->input->get_post('online_id', TRUE));
            $online_info = $this->online_model->info($online_id, 'id');
            if (empty($online_info))
                self::json(lang('online_no_exists'));

            if ($online_info['status'] != self::CODE_PREPARE)
                self::json(lang('online_status_exception'));

            $update_data = array(
                'status' => self::CODE_SUBMIT_ONLINE,
            );
            $res = $this->online_model->update(array('id'=>$online_id), $update_data);
            self::record_log('online', lang('log_submit_wait_online_title'), sprintf(lang('log_submit_wait_online_content'), $online_id, $online_info['title']), $online_id, $res);
            if ($res)
                self::json(lang('operation_success'), self::SUCCESS_STATUS);
            else
                self::json(lang('operation_fail'));
        }
    }

    public function send_online() {
        $cid = intval($this->input->get_post('cid', TRUE));
        $where = "status in(5, 6, 7)";
        if ($cid) {
            $where .= 'AND cid='.$cid;
        }
        $cur_page = $this->get_cur_page();
        $result = $this->online_model->page_lists($where, 'id DESC', self::PER_PAGE_NUM, $cur_page);
        foreach ($result['list'] as &$val) {
            $val['uname'] = $this->get_user_info($val['apply_uid'], 'name');
        }
        $this->data['page'] = $this->pagination($result['count'], site_url('online/send_online').'?cid='.$cid);
        $this->data->set($result);
        $this->data['cid'] = $cid;
        $last_online = $this->online_model->lists(array('status'=>6), 'id DESC', 1);
        $this->data['last_online_id'] = !empty($last_online[0]['id']) ? $last_online[0]['id'] : 0;
        $channel_list = $this->channel_model->lists(NULL, NULL, NULL,'cid');
        $this->data['channel_list'] = $channel_list;
        $this->data['submit_online_pri'] = $this->has_privileges('online', 'submit_online');
		$this->data['rollback_pri'] = $this->has_privileges('online', 'submit_online_rollback');
        $this->data['diff_pri'] = $this->has_privileges('online', 'diff');
        $this->load->view('online/send_online', $this->data->all());
    }

    public function submit_online() {
        if ($this->input->is_ajax_request()) {
            $online_id = intval($this->input->get_post('online_id', TRUE));
            $online_info = $this->online_model->info(array('id'=>$online_id));
            if (empty($online_info))
                self::json(lang('online_no_exists'));

            $channel_info = $this->channel_model->info(array('cid'=>$online_info['cid']));
            if (empty($channel_info))
                self::json(lang('channel_not_exists'));

            $full_log_dir = $online_info['log_dir'].'/'.$online_info['log_name'];
            $online_cmd = $channel_info['online_shell'].' '.$full_log_dir;
            $exec_res = Svn::exec_cmd($online_cmd);
            if (Svn::check_exec_status($exec_res) == FALSE) {
                self::record_log('online', lang('log_online_title'), sprintf(lang('log_online_content'), $online_id, $online_cmd, var_export($exec_res, TRUE)), $online_id, 2);
                self::json(lang('online_fail'));
            }

            $update_data = array(
                'online_cmd' => $online_cmd,
                'status' => self::CODE_ONLINE,
                'online_uid' => $this->session->userdata('uid'),
                'online_time' => date('Y-m-d H:i:s', time()),
            );
            $res = $this->online_model->update(array('id'=>$online_id), $update_data);
            self::record_log('online', lang('log_online_title'), sprintf(lang('log_sql_online_content'), $online_id, $online_cmd, var_export($exec_res, TRUE)), $online_id, $res);
            if ($res)
                self::json(lang('operation_success'), self::SUCCESS_STATUS);
            else
                self::json(lang('operation_fail'));
        }
    }

    public function submit_online_rollback() {
        if ($this->input->is_ajax_request()) {
            $online_id = intval($this->input->get_post('online_id', TRUE));
            $online_info = $this->online_model->info(array('id'=>$online_id));
            if (empty($online_info))
                self::json(lang('online_no_exists'));

            $channel_info = $this->channel_model->info(array('cid'=>$online_info['cid']));
            if (empty($channel_info))
                self::json(lang('channel_not_exists'));

            $full_log_dir = $online_info['log_dir'].'/'.$online_info['log_name'];
            $rollback_cmd = $channel_info['online_rollback_shell'].' '.$full_log_dir;
            $exec_res = Svn::exec_cmd($rollback_cmd);
            if (Svn::check_exec_status($exec_res) == FALSE) {
                self::record_log('online', lang('log_online_rollback_title'), sprintf(lang('log_online_rollback_content'), $online_id, $rollback_cmd, var_export($exec_res, TRUE)), $online_id, 2);
                self::json(lang('online_rollback_fail'));
            }

            $update_data = array(
                'rollback_cmd' => $rollback_cmd,
                'status' => self::CODE_ROLLBACK,
                'rollback_uid' => $this->session->userdata('uid'),
                'rollback_time' => date('Y-m-d H:i:s', time()),
            );
            $res = $this->online_model->update(array('id'=>$online_id), $update_data);
            self::record_log('online', lang('log_online_rollback_title'), sprintf(lang('log_sql_online_rollback_content'), $online_id, $rollback_cmd, var_export($exec_res, TRUE)), $online_id, $res);
            if ($res)
                self::json(lang('operation_success'), self::SUCCESS_STATUS);
            else
                self::json(lang('operation_fail'));
        }
    }

    public function diff() {
        $cid = $this->input->get_post('cid', TRUE);
        $revision = $this->input->get_post('revision', TRUE);
        $dir = $this->input->get_post('dir', TRUE);
        if (empty($cid) || empty($revision))
            self::json(lang('miss_parameter'));

        $this->set_channel_svn_info($cid);
        $summarize = Svn::diff($revision, $dir, false);
        $summarize_str = '';
        foreach ($summarize as $s) {
            $class = '';
            $s = trim($s);
            if (substr($s, 0, 1) == '+') {
                $class = 'alert-success';
            } else if (substr($s, 0, 1) == '-') {
                $class = 'alert-danger';
            } else if (substr($s,0,6) == 'Index:') {
                $class = 'label label-xlg label-primary arrowed arrowed-right';
            } else if(substr($s, 0, 1) == '\\') {
                continue;
            }
            $summarize_str .= '<p class="'.$class.'">'.htmlentities(mb_convert_encoding($s,'GBK','UTF-8')).'</p>';
        }
        if (!$summarize_str)
            $summarize_str = '<p style="text-align: center;">没有任何变动信息...</p>';
        $data = array(
            'summarize' => $summarize_str,
        );
        self::json(lang('operation_success'), self::SUCCESS_STATUS, $data);
    }
}