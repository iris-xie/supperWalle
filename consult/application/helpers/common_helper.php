<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function count_cn_word($str) {
    return mb_strlen($str, 'UTF-8');
}

function cut_str($str, $wid=30, $tag = '...') {
    $pos = 0;
    $tok = array();
    if($wid == 0) {
        return '';
    }

    if (strlen($str) > 200 * 3) {
        $str = substr($str, 0, 200 * 3);
    }

    if (mb_strlen($str, 'UTF-8')<$wid) {
        $tag = '';
    }
    if ($wid - mb_strlen($tag, 'UTF-8') > 0) {
        $wid = $wid - mb_strlen($tag, 'UTF-8');
    }

    $flag = false;
    $tok[0] = mb_substr($str, 0, 1, 'UTF-8');
    for ($i = 1 ; $i < $wid ; ++$i) {
        $c = mb_substr($str, $i, 1, 'UTF-8');
        if (!preg_match('/[a-z\'\"]/i',$c)) {
            ++$pos;
            $flag = true;
        } elseif($flag) {
            ++$pos;
            $flag = false;
        }
        $tok[$pos] .= $c;
    }

    $pos = 0;
    $ret = '';
    $l = count($tok);
    for ($i = 0 ; $i < $l ; ++$i) {
        $ret .= $tok[$i];
    }
    return $ret.$tag;
}

function get_log_type($type = '') {
    $log_type = array(
        'login' => lang('login_module'),
        'user' => lang('user_module'),
        'channel' => lang('channel_module'),
        'online' => lang('online_module'),
        'module' => lang('system_module'),
        'acl'    => lang('acl_module'),
    );
    if (empty($type))
        return $log_type;
    return isset($log_type[$type]) ? $log_type[$type] : '';
}

function online_cn_status($status) {
    $cn_status = '';
    if ($status == 0) {
        $cn_status = '<span class="label label-info">待审核</span>';
    } else if ($status == 1) {
        $cn_status = '<span class="label label-primary">待送测</span>';
    } else if ($status == 2) {
        $cn_status = '<span class="label label-primary">已送测</span>';
    } else if ($status == 3) {
        $cn_status = '<span class="label label-primary">待预发布</span>';
    } else if ($status == 4) {
        $cn_status = '<span class="label label-success">已上预发布</span>';
    } else if ($status == 5) {
        $cn_status = '<span class="label label-info">待上线</span>';
    } else if ($status == 6) {
        $cn_status = '<span class="label label-success">已上线</span>';
    } else if ($status == 7) {
        $cn_status = '<span class="label label-warning">已回滚</span>';
    } else if ($status == -1) {
        $cn_status = '<span class="label label-danger">已拒绝</span>';
    } else if ($status == -2) {
        $cn_status = '<span class="label label-danger">撤销送测</span>';
    } else if ($status == -3) {
        $cn_status = '<span class="label label-warning">更新送测</span>';
    }
    return $cn_status;
}