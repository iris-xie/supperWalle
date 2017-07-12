<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $sys_name; ?></title>
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="cache-control" content="no-cache">
	<meta http-equiv="expires" content="0">
	<link href="<?php echo $static_prefix; ?>css/bootstrap.min.css?v=<?php echo $static_version; ?>" rel="stylesheet">
    <link href="<?php echo $static_prefix; ?>css/font-awesome.min.css?v=<?php echo $static_version; ?>" rel="stylesheet">
    <link href="<?php echo $static_prefix; ?>css/plugins/dataTables/dataTables.bootstrap.css?v=<?php echo $static_version; ?>" rel="stylesheet">
    <link href="<?php echo $static_prefix; ?>css/animate.min.css?v=<?php echo $static_version; ?>" rel="stylesheet">
    <link href="<?php echo $static_prefix; ?>css/style.min.css?v=<?php echo $static_version; ?>" rel="stylesheet">
</head>
<body class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>后台账号列表</h5>
                    </div>
                    <div class="ibox-content">
                        <form action="<?php echo site_url('user/index'); ?>" method="get" class="online-form">
                            <div class="row">
                                <div class="col-sm-5">
                                    <div class="input-group">
                                        <input type="text" name="name" placeholder="请输入账号" class="input-sm form-control" value="<?php  echo $name; ?>">
                                        <span class="input-group-btn">
                                            <button type="submit" class="btn btn-sm btn-primary"> 搜索</button>
                                            <?php if ($add_pri): ?>
                                                <button type="button" class="btn btn-sm btn-white add-account" style="margin-left: 10px;">添加账号</button>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <table class="table table-striped table-bordered table-hover " id="editable">
                            <thead>
                                <tr>
                                    <th>UID</th>
									<th>用户组</th>
                                    <th>账号</th>
                                    <th>邮箱</th>
									<th>SVN账号</th>
                                    <th>登录次数</th>
                                    <th>最后登录IP</th>
                                    <th>最后登录时间</th>
                                    <th>创建时间</th>
									<th>状态</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($list as $v): ?>
                                <tr class="gradeX">
                                    <td><?php echo $v['uid']; ?></td>
									<td><?php echo $group_list[$v['gid']]['name']; ?></td>
                                    <td><?php echo $v['name']; ?></td>
                                    <td><?php echo $v['email']; ?></td>
									<td><?php echo $v['svn_account']; ?></td>
                                    <td><?php echo $v['login_num']; ?></td>
                                    <td><?php echo $v['last_login_ip']; ?></td>
                                    <td><?php echo $v['last_login_time']; ?></td>
                                    <td><?php echo $v['add_time']; ?></td>
									<td>
                                        <?php if ($v['status'] == 1): ?>
                                            <span class="label label-primary">正常</span>
                                        <?php else: ?>
                                            <span class="label label-danger">禁用</span>
                                        <?php endif;?>
                                    </td>
                                    <td align="center">
										<input type="hidden" name="hidden_json" value='<?php echo json_encode($v);?>' />
                                        <?php if ($edit_pri): ?>
										    <button class="btn btn-outline btn-default edit-btn">账号编辑</button>
                                        <?php endif; ?>
                                        <?php if ($acl_ctl_pri): ?>
										    <a class="btn btn-outline btn-info" href="<?php echo site_url('user/acl_ctl')?>?uid=<?php echo $v['uid']; ?>">权限编辑</a>
                                        <?php endif; ?>
                                        <?php if ($set_status_pri): ?>
                                            <?php  if ($v['status'] == 1): ?>
                                                <button type="button" class="btn btn-outline btn-danger set-status" status="2" uid="<?php echo $v['uid']; ?>">禁用</button>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-outline btn-primary set-status" status="1" uid="<?php echo $v['uid']; ?>">启用</button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
						<div class="row">
							<div class="col-sm-6">
								<div class="dataTables_info">共 <?php echo $count; ?> 项</div>
							</div>
							<div class="col-sm-6">
								<div class="dataTables_paginate paging_simple_numbers">
									<ul class="pagination">
                                        <?php echo $page; ?>
									</ul>
								</div>
							</div>
						</div>
                    </div>
                </div>
            </div>
        </div>


        <div id="modal-form" class="modal fade modal-form" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <h3 class="m-t-none m-b">添加账号</h3>
                        <form class="form-horizontal addUserForm">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">账号：</label>
                                <div class="col-sm-8">
                                    <input type="input" placeholder="账号" class="form-control" name="name"><span class="help-block m-b-none"></span>
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-sm-3 control-label">账号组：</label>
                                <div class="col-sm-8">
									<select name="gid" class="form-control">
										<?php foreach ($group_list as $g): ?>
											<option value="<?php echo $g['gid']; ?>"><?php echo $g['name']; ?></option>
										<?php endforeach; ?>
									</select>
                                    <span class="help-block m-b-none"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">密码：</label>
                                <div class="col-sm-8">
                                    <input type="password" placeholder="密码" class="form-control" name="pwd">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">邮箱：</label>
                                <div class="col-sm-8">
                                    <input type="input" placeholder="邮箱" class="form-control" name="email">
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-sm-3 control-label">SVN账号：</label>
                                <div class="col-sm-8">
                                    <input type="input" placeholder="SVN账号" class="form-control" name="svn_account">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-8">
									<input type="hidden" name="submit_url" />
                                    <input type="hidden" name="uid" />
                                    <button class="btn btn-sm btn-primary" type="submit"><i class="fa fa-check"></i>保 存</button>
                                    <button class="btn btn-sm btn-white" type="reset" data-dismiss="modal">取 消</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
	
    <script src="<?php echo $static_prefix; ?>js/jquery.min.js?v=<?php echo $static_version; ?>"></script>
    <script src="<?php echo $static_prefix; ?>js/bootstrap.min.js?v=<?php echo $static_version; ?>"></script>
    <script src="<?php echo $static_prefix; ?>js/plugins/jeditable/jquery.jeditable.js?v=<?php echo $static_version; ?>"></script>
    <script src="<?php echo $static_prefix; ?>js/plugins/dataTables/jquery.dataTables.js?v=<?php echo $static_version; ?>"></script>
    <script src="<?php echo $static_prefix; ?>js/plugins/dataTables/dataTables.bootstrap.js?v=<?php echo $static_version; ?>"></script>
    <script src="<?php echo $static_prefix; ?>js/content.min.js?v=<?php echo $static_version; ?>"></script>
    <script src="<?php echo $static_prefix; ?>js/plugins/layer/layer.min.js?v=<?php echo $static_version; ?>"></script>
    <script>
        $(function () {
            var modalForm = $(".modal-form"),
                $addUserForm = $(".addUserForm"),
				$modalTitle = modalForm.find(".m-b"),
                $modalForm = $("#modal-form");

            $addUserForm.submit(function (e) {
                if (e) {
                    e.preventDefault();
                }
                var account = $addUserForm.find("[name='name']"),
                    password = $addUserForm.find("[name='pwd']"),
                    email = $addUserForm.find("[name='email']"),
                    submit_url = $addUserForm.find("[name='submit_url']").val();
				
                if (account.length == 1 && account.val() == "") {
					account.focus();
                    parent.layer.msg('请填写账号！',{icon: 2});
                    return false;
                }
                if (password.val() == "") {
					password.focus();
                    parent.layer.msg('请填写密码！',{icon: 2});
                    return false;
                }
                if (email.val() == "") {
					email.focus();
                    parent.layer.msg('请填写邮箱！',{icon: 2});
                    return false;
                }
                $.post(submit_url, $addUserForm.serialize(), function (res) {
					$modalForm.modal("hide");
                    if (res.status == 1) {
                        parent.layer.msg(res.msg,{icon: 1}, function(){
                            window.location.reload();
                        });
                    } else {
                        parent.layer.msg(res.msg,{icon: 2});
                    }
                }, "json");
            });

            $(".set-status").on("click", function() {
                var status = $(this).attr("status"),
                    uid = $(this).attr("uid");
                if (status == "" || uid == "") {
                    parent.layer.msg('参数错误~~~',{icon: 2});
                    return false;
                }
                var confirm_msg = "禁用";
                if (status == 1) {
                    var confirm_msg = "启用";
                }
                parent.layer.confirm("你确认要执行 <b>"+confirm_msg+"</b> 操作吗？？？", {
                    icon: 3,
					btn: ['确定','取消']
                }, function(){
                    $.post("<?php echo site_url('user/set_status');?>", {status:status,uid:uid}, function (res) {
						parent.layer.close();
                        if (res.status == 1) {
                            parent.layer.msg(res.msg,{icon: 1}, function(){
                                window.location.reload();
                            });
                        } else {
                            parent.layer.msg(res.msg,{icon: 2});
                        }
                    }, "json");
                }, function(){
                    parent.layer.close();
                });
            });

            $(".add-account").on("click", function() {
				$addUserForm[0].reset();
				$addUserForm.find("input[name='name']").prop("disabled", false);
                $modalTitle.html("添加账号");
				$addUserForm.find("input[name='submit_url']").val("<?php echo site_url('user/add'); ?>");
                $modalForm.modal();
            });

			$(".edit-btn").on("click", function() {
                var json_str = $(this).parent().find(":hidden").val(),
                    json_obj = jQuery.parseJSON(json_str);
                for (var k in json_obj) {
                    if ($addUserForm.find("input[name='"+k+"']").length > 0) {
                        $addUserForm.find("input[name='"+k+"']").val(json_obj[k]);
                    }
                }
				$addUserForm.find("option[value='"+json_obj.gid+"']").prop("selected", true);
				$addUserForm.find("input[name='submit_url']").val("<?php echo site_url('user/edit'); ?>");
				$addUserForm.find("input[name='name']").prop("disabled", true);
                $modalTitle.html("编辑账号");
                $modalForm.modal();
            });
        });
    </script>
	</body>
</html>