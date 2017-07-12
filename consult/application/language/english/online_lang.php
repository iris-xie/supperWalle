<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$lang['online_no_exists'] = '上线单不存在！';
$lang['online_status_exception'] = '当前上线单状态禁止操作！';
$lang['no_selected_online_file'] = '请选择更新文件！';
$lang['file_already_submit'] = '%s 已经提交过！';
$lang['log_add_online_title'] = '创建申请单';
$lang['log_add_online_content'] = '标题为(%s) 版本(%s) 上线ID为(%s)';

$lang['log_check_online_title'] = '审核上线单';
$lang['log_online_set_pass'] = '上线单ID为(%s)，设置为审核通过';
$lang['log_online_set_refuse'] = '上线单ID为(%s)，设置为拒绝通过';

$lang['log_edit_online_title'] = '编辑上线单';
$lang['log_edit_online_content'] = '上线单ID为(%s)，更新为待送测状态';

$lang['log_test_title'] = '代码送测';
$lang['log_test_content'] = '上线单ID为(%s)， 命令为(%s)，返回的结果为(%s)';
$lang['log_sql_test_content'] = '上线单ID为(%s)，更新为送测状态，执行SVN更新脚本(%s)，返回(%s)';
$lang['log_test_fail'] = '执行更新失败！';

$lang['log_update_test_title'] = '代码更新送测';
$lang['log_update_test_content'] = '上线单ID为(%s)，命令为(%s)，返回的结果为(%s)';
$lang['log_sql_update_test_content'] = '上线单ID为(%s)，更新为送测状态';
$lang['log_update_test_fail'] = '执行代码更新送测失败！';

$lang['log_cancel_test_title'] = '撤销送测';
$lang['log_cancel_test_content'] = '上线单ID为(%s)，返回的结果为(%s)';
$lang['log_sql_cancel_test_content'] = '上线单ID为(%s)，更新为撤销送测状态';
$lang['cancel_test_fail'] = '执行撤销送测失败！';
$lang['empty_update_file'] = '更新文件列表为空！';

$lang['log_dir_no_exists'] = '日志目录不存在！';
$lang['log_dir_no_write'] = '日志目录不可写！';
$lang['log_prepare_title'] = '代码预发布';
$lang['log_prepare_content'] = '上线单ID为(%s)，执行预发布脚本(%s)，返回的结果为(%s)';
$lang['prepare_fail'] = '代码预发布执行失败！';
$lang['log_sql_prepare_content'] = '上线单ID为(%s)，更新为代码预发布状态，执行预发布脚本(%s)，返回(%s)';

$lang['log_submit_wait_prepare_title'] = '提交待预发布';
$lang['log_submit_wait_prepare_content'] = '上线单ID(%s)，更新为待预发布状态';

$lang['log_prepare_rollback_title'] = '撤销预发布';
$lang['log_prepare_rollback_content'] = '上线单ID为(%s)，执行回滚预发布脚本(%s)，返回的结果为(%s)';
$lang['prepare_rollback_fail'] = '执行撤销预发布失败！';
$lang['log_sql_prepare_rollback_content'] = '上线单ID为(%s)，更新为已送测状态，执行撤销预发布脚本(%s)，返回(%s)';

$lang['log_submit_wait_online_title'] = '提交待上线';
$lang['log_submit_wait_online_content'] = '上线单ID(%s)，更新为待预上线状态';

$lang['log_online_title'] = '代码上线';
$lang['log_online_content'] = '上线单ID为(%s)，执行上线脚本(%s)，返回的结果为(%s)';
$lang['log_sql_online_content'] = '上线单ID为(%s)，更新为上线状态，执行上线脚本(%s)，返回(%s)';
$lang['online_fail'] = '执行上线失败！';

$lang['log_online_rollback_title'] = '上线代码回滚';
$lang['log_online_rollback_content'] = '上线单ID为(%s)，执行回滚上线脚本(%s)，返回的结果%s';
$lang['log_sql_online_rollback_content'] = '上线单ID为(%s)，更新为上线回滚状态，执行上线回滚脚本(%s)，返回(%s)';
$lang['online_rollback_fail'] = '执行上线回滚失败！';