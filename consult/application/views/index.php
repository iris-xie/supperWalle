<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit">
    <title><?php echo $sys_name; ?></title>
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="cache-control" content="no-cache">
	<meta http-equiv="expires" content="0">
    <!--[if lt IE 9]>
    <meta http-equiv="refresh" content="0;<?php echo site_url('passport/upgrade');?>" />
    <![endif]-->
    <link rel="shortcut icon" href="favicon.ico"> <link href="<?php echo $static_prefix; ?>css/bootstrap.min.css?v=<?php echo $static_version; ?>" rel="stylesheet">
    <link href="<?php echo $static_prefix; ?>css/font-awesome.min.css?v=<?php echo $static_version; ?>" rel="stylesheet">
    <link href="<?php echo $static_prefix; ?>css/animate.css?v=<?php echo $static_version; ?>" rel="stylesheet">
    <link href="<?php echo $static_prefix; ?>css/style.css?v=<?php echo $static_version; ?>" rel="stylesheet">
</head>
<body class="fixed-sidebar full-height-layout gray-bg" style="overflow:hidden">
    <div id="wrapper">
        <!--左侧导航开始-->
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="nav-close"><i class="fa fa-times-circle"></i>
            </div>
            <div class="sidebar-collapse">
                <ul class="nav" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element">
                            <span><img alt="image" class="img-circle" src="<?php echo $static_prefix; ?>img/logo.jpg?v=<?php echo $static_version; ?>" width="64" height="64"/></span>
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <span class="clear">
                               <span class="block m-t-xs"><strong class="font-bold"><?php echo $this->session->userdata('name'); ?></strong></span>
                                <span class="text-muted text-xs block"><?php echo isset($group_info['name']) ? $group_info['name'] : '';?><b class="caret"></b></span>
                                </span>
                            </a>
                            <ul class="dropdown-menu animated fadeInRight m-t-xs">
                                <li><a href="#edit-pwd-modal" data-toggle="modal">修改密码</a>
                                </li>
                                <li class="divider"></li>
                                <li><a href="<?php echo site_url('passport/logout');?>">安全退出</a>
                                </li>
                            </ul>
                        </div>
                        <div class="logo-element">H+
                        </div>
                    </li>
                    <li class="hidden-folded padder m-t m-b-sm text-muted text-xs">
                        <span class="ng-scope"></span>
                    </li>

                    <li>
                        <a href="javascript:void(0);"><i class="fa fa-desktop"></i> <span class="nav-label">栏目</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level collapse in">
                            <?php $action_rights = explode(',',$user['action_rights']);?>
                            <?php foreach ($module_config as $module): ?>
                            <?php if($module['module']=="channel" && $module['action'] == "index" && (in_array($module['id'],$action_rights)||$user['module_rights']==1)):?>
                            <li><a class="J_menuItem" href="<?php echo site_url('channel/index'); ?>">项目管理</a></li>
                                <?php endif;?>
                            <?php endforeach;?>
							<?php foreach ($module_config as $module): ?>
                                <?php if($module['module']=="online" && $module['action'] == "project_list" && (in_array($module['id'],$action_rights)||$user['module_rights']==1)):?>
                                    <li><a class="J_menuItem" href="<?php echo site_url('online/project_list'); ?>">创建申请单</a></li>
                                <?php endif;?>
                            <?php endforeach;?>
                            <?php foreach ($module_config as $module): ?>
                                <?php if($module['module']=="online" && $module['action'] == "apply_list" && (in_array($module['id'],$action_rights)||$user['module_rights']==1)):?>
                                    <li><a class="J_menuItem" href="<?php echo site_url('online/apply_list'); ?>">申请单列表</a></li>
                                <?php endif;?>
                            <?php endforeach;?>
                            <?php foreach ($module_config as $module): ?>
                            <?php if($module['module']=="online" && $module['action'] == "send_test" && (in_array($module['id'],$action_rights)||$user['module_rights']==1)):?>
                            <li><a class="J_menuItem" href="<?php echo site_url('online/send_test'); ?>">送测列表</a></li>
                                <?php endif;?>
                            <?php endforeach;?>
                            <?php foreach ($module_config as $module): ?>
                            <?php if($module['module']=="online" && $module['action'] == "prepare_online" && (in_array($module['id'],$action_rights)||$user['module_rights']==1)):?>
                            <li><a class="J_menuItem" href="<?php echo site_url('online/prepare_online'); ?>">预发布列表</a></li>
                                <?php endif;?>
                            <?php endforeach;?>
                            <?php foreach ($module_config as $module): ?>
                            <?php if($module['module']=="online" && $module['action'] == "send_online" && (in_array($module['id'],$action_rights)||$user['module_rights']==1)):?>
                            <li><a class="J_menuItem" href="<?php echo site_url('online/send_online'); ?>">上线列表</a></li>
                                <?php endif;?>
                            <?php endforeach;?>
                            <?php foreach ($module_config as $module): ?>
                            <?php if($module['module']=="log" && $module['action'] == "index" && (in_array($module['id'],$action_rights)||$user['module_rights']==1)):?>
                            <li><a class="J_menuItem" href="<?php echo site_url('log/index'); ?>">操作日志</a></li>
                                <?php endif;?>
                            <?php endforeach;?>
                            <?php foreach ($module_config as $module): ?>
                            <?php if($module['module']=="user" && $module['action'] == "index" && (in_array($module['id'],$action_rights)||$user['module_rights']==1)):?>
                            <li><a class="J_menuItem" href="<?php echo site_url('user/index'); ?>">后台账号</a></li>
                                <?php endif;?>
                            <?php endforeach;?>
                            <?php foreach ($module_config as $module): ?>
                            <?php if($module['module']=="module" && $module['action'] == "index" && (in_array($module['id'],$action_rights)||$user['module_rights']==1)):?>
                            <li><a class="J_menuItem" href="<?php echo site_url('module/index'); ?>">模块录入</a></li>
                                <?php endif;?>
                            <?php endforeach;?>

                        </ul>
                    </li>
                    <li class="line dk"></li>
                </ul>
            </div>
        </nav>
        <!--左侧导航结束-->
        <!--右侧部分开始-->
        <div id="page-wrapper" class="gray-bg dashbard-1">
            <!--<div class="row border-bottom">
                <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header"><a class="navbar-minimalize minimalize-styl-2 btn btn-info " href="#"><i class="fa fa-bars"></i> </a>
                        
                    </div>
                    <ul class="nav navbar-top-links navbar-right">
                        
                       
                    </ul>
                </nav>
            </div>-->

			<div class="row content-tabs">
                <button class="roll-nav roll-left J_tabLeft"><i class="fa fa-backward"></i>
                </button>
                <nav class="page-tabs J_menuTabs">
                    <div class="page-tabs-content">
                        <a href="javascript:;" class="active J_menuTab" data-id="<?php echo site_url('welcome/home'); ?>">首页</a>
                    </div>
                </nav>
                <button class="roll-nav roll-right J_tabRight"><i class="fa fa-forward"></i>
                </button>
                <div class="btn-group roll-nav roll-right">
                    <button class="dropdown J_tabClose" data-toggle="dropdown">关闭操作<span class="caret"></span>

                    </button>
                    <ul role="menu" class="dropdown-menu dropdown-menu-right">
                        <li class="J_tabShowActive"><a>定位当前选项卡</a>
                        </li>
                        <li class="divider"></li>
                        <li class="J_tabCloseAll"><a>关闭全部选项卡</a>
                        </li>
                        <li class="J_tabCloseOther"><a>关闭其他选项卡</a>
                        </li>
                    </ul>
                </div>
                <a href="<?php echo site_url('passport/logout');?>" class="roll-nav roll-right J_tabExit"><i class="fa fa fa-sign-out"></i> 退出</a>
            </div>
            <div class="row J_mainContent" id="content-main">
                <iframe class="J_iframe" name="iframe0" width="100%" height="100%" src="<?php echo site_url('welcome/home'); ?>" frameborder="0" data-id="<?php echo site_url('welcome/home'); ?>" seamless></iframe>
            </div>
            <div class="footer">
                <div class="pull-right">
                </div>
            </div>
        </div>
        <!--右侧部分结束-->
    </div>

    <!-- 全局js -->
    <script src="<?php echo $static_prefix; ?>js/jquery.min.js?v=<?php echo $static_version; ?>"></script>
    <script src="<?php echo $static_prefix; ?>js/bootstrap.min.js?v=<?php echo $static_version; ?>"></script>
    <script src="<?php echo $static_prefix; ?>js/plugins/metisMenu/jquery.metisMenu.js?v=<?php echo $static_version; ?>"></script>
    <script src="<?php echo $static_prefix; ?>js/plugins/slimscroll/jquery.slimscroll.min.js?v=<?php echo $static_version; ?>"></script>
    <script src="<?php echo $static_prefix; ?>js/plugins/layer/layer.min.js?v=<?php echo $static_version; ?>"></script>
	<script src="<?php echo $static_prefix; ?>js/contabs.min.js?v=<?php echo $static_version; ?>"></script>
    <script src="<?php echo $static_prefix; ?>js/content.js?v=<?php echo $static_version; ?>"></script>
    <!-- 自定义js -->
    <script src="<?php echo $static_prefix; ?>js/hAdmin.js?v=<?php echo $static_version; ?>"></script>
    <div id="edit-pwd-modal" class="modal fade" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <h3 class="m-t-none m-b">修改密码</h3>
                    <form class="form-horizontal editPwdForm">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">原密码：</label>
                            <div class="col-sm-8">
                                <input type="password" placeholder="原密码" class="form-control" name="old_password">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">新密码：</label>
                            <div class="col-sm-8">
                                <input type="password" placeholder="新密码" class="form-control" name="new_password">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">重复密码：</label>
                            <div class="col-sm-8">
                                <input type="password" placeholder="重复密码" class="form-control" name="rnew_password">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-8">
                                <button class="btn btn-sm btn-primary" type="submit">确 定</button>
                                <button class="btn btn-sm btn-white" type="reset" data-dismiss="modal">取 消</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            var $editPwdForm = $(".editPwdForm");
            $editPwdForm.submit(function (e) {
                if (e) {
                    e.preventDefault();
                }
                var old_password = $editPwdForm.find("[name='old_password']").val(),
                    new_password = $editPwdForm.find("[name='new_password']").val(),
                    rnew_password = $editPwdForm.find("[name='rnew_password']").val();

                if (old_password == "") {
                    parent.layer.msg('请填写原密码！', {icon: 2});
                    return false;
                }
                if (new_password == "") {
                    parent.layer.msg('请填写新密码！', {icon: 2});
                    return false;
                }
                if (new_password.length < 6 || new_password.length > 30) {
                    parent.layer.msg('新密码长度必须大于6位！', {icon: 2});
                    return false;
                }
                if (new_password != rnew_password) {
                    parent.layer.msg('两次密码不一致！', {icon: 2});
                    return false;
                }
                $.post("<?php echo site_url('welcome/edit_pwd');?>", $editPwdForm.serialize(), function (res) {
                    if (res.status == 1) {
                        parent.layer.msg(res.msg, {icon: 1}, function () {
                            window.location.href = "<?php echo site_url('passport/logout');?>";
                        });
                    } else {
                        parent.layer.msg(res.msg, {icon: 2});
                    }
                }, "json");
            });
        });
    </script>
</body>
</html>