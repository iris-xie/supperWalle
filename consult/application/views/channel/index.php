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
                        <h5>项目列表</h5>
                    </div>
                    <div class="ibox-content">
						<?php if ($add_pri): ?>
							<div>
								<button type="button" class="btn btn-w-m btn-primary add-channel">添加项目</button>
							</div>
						<?php endif; ?>
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>项目ID</th>
                                    <th>项目名</th>
                                    <th>SVN账号</th>
                                    <th>SVN目录</th>
									<th>日志目录</th>
                                    <th>预发布命令</th>
                                    <th>上线命令</th>
                                    <th>预发布回滚命令</th>
                                    <th>上线回滚命令</th>
                                    <th>创建时间</th>
                                    <th>状态</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($list as $v): ?>
                                <tr>
                                    <td><?php echo $v['cid']; ?></td>
                                    <td><?php echo $v['name']; ?></td>
                                    <td><?php echo $v['svn_account']; ?></td>
                                    <td><?php echo $v['svn_root_dir']; ?></td>
									<td><?php echo $v['log_dir']; ?></td>
                                    <td><?php echo $v['prepare_shell']; ?></td>
                                    <td><?php echo $v['online_shell']; ?></td>
                                    <td><?php echo $v['prepare_rollback_shell']; ?></td>
                                    <td><?php echo $v['online_rollback_shell']; ?></td>
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
											<button type="button" class="btn btn-outline btn-info edit-btn">编辑</button>
										<?php endif; ?>
                                        <?php if ($set_status_pri): 
										if ($v['status'] == 1): ?>
                                            <button type="button" class="btn btn-outline btn-danger set-status" status="2" cid="<?php echo $v['cid']; ?>">禁用</button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-outline btn-primary set-status" status="1" cid="<?php echo $v['cid']; ?>">启用</button>
                                        <?php endif; endif; ?>
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

        <div id="modal-form" class="modal fade" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content animated flipInY">
                    <div class="modal-body">
                        <h3 class="m-t-none m-b modal-title">添加项目</h3>
                        <form class="form-horizontal addChannelForm">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">项目名：</label>
                                <div class="col-sm-8">
                                    <input type="input" placeholder="项目名" class="form-control" name="name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">SVN账号：</label>
                                <div class="col-sm-8">
                                    <input type="input" placeholder="SVN账号" class="form-control" name="svn_account">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">SVN密码：</label>
                                <div class="col-sm-8">
                                    <input type="password" placeholder="SVN密码" class="form-control" name="svn_pwd">
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-sm-3 control-label">SVN根目录：</label>
                                <div class="col-sm-8">
                                    <input type="input" placeholder="SVN根目录" class="form-control" name="svn_root_dir">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">日志目录：</label>
                                <div class="col-sm-8">
                                    <input type="input" placeholder="日志目录" class="form-control" name="log_dir">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">预发布命令：</label>
                                <div class="col-sm-8">
                                    <input type="input" placeholder="预发布命令" class="form-control" name="prepare_shell">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">上线命令：</label>
                                <div class="col-sm-8">
                                    <input type="input" placeholder="上线命令" class="form-control" name="online_shell">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">预发布回滚命令：</label>
                                <div class="col-sm-8">
                                    <input type="input" placeholder="预发布回滚命令" class="form-control" name="prepare_rollback_shell">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">上线回滚命令：</label>
                                <div class="col-sm-8">
                                    <input type="input" placeholder="上线回滚命令" class="form-control" name="online_rollback_shell">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-8">
                                    <input type="hidden" name="cid" value="0" />
                                    <input type="hidden" name="url" />
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
            var $addChannelForm = $(".addChannelForm"),
                $modalForm = $("#modal-form"),
                $modalTitle = $(".modal-title");

            $addChannelForm.submit(function (e) {
                if (e) {
                    e.preventDefault();
                }
                var cid = $addChannelForm.find("[name='cid']").val(),
                    name = $addChannelForm.find("[name='name']").val(),
                    svn_account = $addChannelForm.find("[name='svn_account']").val(),
					svn_pwd = $addChannelForm.find("[name='svn_pwd']").val(),
                    svn_root_dir = $addChannelForm.find("[name='svn_root_dir']").val(),
                    log_dir = $addChannelForm.find("[name='log_dir']").val(),
                    prepare_shell = $addChannelForm.find("[name='prepare_shell']").val(),
                    online_shell = $addChannelForm.find("[name='online_shell']").val(),
                    prepare_rollback_shell = $addChannelForm.find("[name='prepare_rollback_shell']").val(),
                    online_rollback_shell = $addChannelForm.find("[name='online_rollback_shell']").val(),
                    url = $addChannelForm.find("input[name='url']").val();

                if (name == "") {
                    parent.layer.msg('请填写项目名！',{icon: 2});
                    return false;
                }
                if (svn_account == "") {
                    parent.layer.msg('请填写SVN账号！',{icon: 2});
                    return false;
                }
                if (svn_pwd == "") {
                    parent.layer.msg('请填写SVN密码！',{icon: 2});
                    return false;
                }
				if (svn_root_dir == "") {
                    parent.layer.msg('请填写SVN根目录！',{icon: 2});
                    return false;
                }
                if (log_dir == "") {
                    parent.layer.msg('请填写日志目录！',{icon: 2});
                    return false;
                }
                if (prepare_shell == "") {
                    parent.layer.msg('请填写预发布脚本！',{icon: 2});
                    return false;
                }
                if (online_shell == "") {
                    parent.layer.msg('请填写上线脚本！',{icon: 2});
                    return false;
                }
                if (prepare_rollback_shell == "") {
                    parent.layer.msg('请填写预发布回滚脚本！',{icon: 2});
                    return false;
                }

                if (online_rollback_shell == "") {
                    parent.layer.msg('请填写上线回滚脚本！',{icon: 2});
                    return false;
                }

                $.post(url, $addChannelForm.serialize(), function (res) {
                    $modalForm.modal('hide');
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
                    cid = $(this).attr("cid");
                if (status == "" || cid == "") {
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
                    $.post("<?php echo site_url('channel/set_status');?>", {status:status,cid:cid}, function (res) {
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

            $(".add-channel").on("click", function () {
                $addChannelForm[0].reset();
                $addChannelForm.find("input[name='cid']").val('0');
                $addChannelForm.find("input[name='url']").val("<?php echo site_url('channel/add'); ?>");
                $modalTitle.html("添加项目");
                $modalForm.modal();
            });

            $(".edit-btn").on("click", function() {
                var json_str = $(this).parent().find(":hidden").val(),
                    json_obj = jQuery.parseJSON(json_str);
                $addChannelForm.find("input[name='url']").val("<?php echo site_url('channel/edit'); ?>");
                for (var k in json_obj) {
                    if ($addChannelForm.find("input[name='"+k+"']").length > 0) {
                        $addChannelForm.find("input[name='"+k+"']").val(json_obj[k]);
                    }
                }
                $modalTitle.html("编辑项目");
                $modalForm.modal();
            });
        });
    </script>
	</body>
</html>