<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Log
 */
class Log extends MY_Controller{

    public function __construct() {
        parent::__construct();
        $this->load->model('log_model');
    }

    public function index() {
        $cur_page = $this->get_cur_page();
        $where = '1=1';
        $keyword = $this->input->get_post('keyword', TRUE);
        if ($keyword) {
            $where .= " AND title like '%{$keyword}%'";
        }
        $type = $this->input->get_post('type', TRUE);
        if ($type) {
            $where .= " AND type='{$type}'";
        }

        $result = $this->log_model->page_lists($where, 'id DESC', self::PER_PAGE_NUM, $cur_page);
        foreach ($result['list'] as &$val) {
            $val['uname'] = $this->get_user_info($val['uid'], 'name');
        }
        $this->data->set(array(
            'keyword' => $keyword,
            'type' => $type,
        ));
        $this->data->set($result);
        $this->data['page'] = $this->pagination($result['count'], site_url('log/index'));
        $channel_list = $this->channel_model->lists(array(), 'cid ASC', 0, 'cid');
        $this->data['channel_list'] = $channel_list;
        $this->data['log_type'] = get_log_type();
        $this->load->view('log/index', $this->data->all());
    }
}