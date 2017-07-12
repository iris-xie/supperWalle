<?php
/**
 * Created by PhpStorm.
 * User: gideon
 * Date: 2016/11/29
 * Time: 11:47
 */
class Acl {
    private $url_module;
    private $url_action;
    private $CI;
    function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->library('session');
        $this->CI->load->model('user_model');
        $this->CI->load->model('module_model');

        $this->url_module = $this->CI->uri->segment(1);
        $this->url_action = $this->CI->uri->segment(2);
    }
    function auth()
    {

        if($this->url_module != 'passport' && $this->url_module != 'login' )
        {
            $uid = $this->CI->session->userdata('uid');

            if(empty($uid))
            {
                show_error('您尚未登录，请点击登录<a href="'. site_url('passport/login') .'">登录</a>');
            }else
            {
                //$this->CI->load->config('acl');
                $user = $this->CI->user_model->get_one_user($uid,'uid');
                //$AUTH = $this->CI->config->item('AUTH');
                $where = explode(',',$user['module_rights']);
                $module_list = $this->CI->module_model->get_module_map($where);

            }
            //超管权限
            if( $user['module_rights'] != 1 )
            {
                if (in_array($this->url_module, $module_list))
                {
                    $action_list = $this->CI->module_model->get_action_map($this->url_module);

                    if (!in_array($this->url_action, $action_list)&&!empty($this->url_action))
                    {
                        show_error('您无权访问该方法<a href="' . $_SERVER['HTTP_REFERER'] . '">返回</a>');
                    }
                } else
                {
                    show_error('您无权访问该模块<a href="' . $_SERVER['HTTP_REFERER'] . '">返回</a>');
                }
            }
        }
    }
}