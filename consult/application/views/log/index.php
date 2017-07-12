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
                        <h5>操作日志</h5>
                    </div>
                    <div class="ibox-content">
                        <form action="<?php echo site_url('log/index'); ?>" method="get" class="logForm">
                            <div class="row">
                                <div class="col-sm-3 m-b-xs">
                                    <select name="type" class="input-sm form-control input-s-sm inline">
                                        <option value="0">请选择日志类型</option>
                                        <?php foreach ($log_type as $key=>$val): ?>
                                            <option value="<?php echo $key; ?>" <?php echo $key == $type ? 'selected' : ''; ?>><?php echo $val; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <div class="input-group">
                                        <input type="text" name="keyword" placeholder="请输入关键词" class="input-sm form-control" value="<?php echo $keyword; ?>"> <span class="input-group-btn">
                                            <button type="submit" class="btn btn-sm btn-primary"> 搜索</button> </span>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <table class="table table-striped table-bordered table-hover " id="editable">
                            <thead>
                                <tr>
                                    <th>日志ID</th>
                                    <th>控制器/方法</th>
                                    <th>类型</th>
                                    <th>标题</th>
                                    <th width="35%">内容</th>
                                    <th>操作人</th>
                                    <th>操作人IP</th>
                                    <th>操作时间</th>
                                    <th>结果</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
								if (!empty($list)):
									foreach ($list as $v): 
								?>
									<tr>
										<td><?php echo $v['id']; ?></td>
                                        <td><?php echo $v['module_name'].'/'.$v['action_name']; ?></td>
										<td><?php echo get_log_type($v['type']); ?></td>
										<td><?php echo $v['title']; ?></td>
										<td><?php echo $v['content']; ?></td>
										<td><?php echo $v['uname']; ?></td>
										<td><?php echo $v['ip']; ?></td>
										<td><?php echo $v['add_time']; ?></td>
                                        <td>
                                            <?php if ($v['status'] == 1): ?>
                                                <span class="label label-primary">成功</span>
                                            <?php else: ?>
                                                <span class="label label-danger">失败</span>
                                            <?php endif;?>
                                        </td>
									</tr>
                                <?php endforeach; else: ?>
									<tr>
										<td align="center" colspan="7">没有查询到符合条件的数据...</td>
									</tr>
								<?php endif; ?>
                            </tbody>
                        </table>
						<?php if (!empty($list)): ?>
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
						<?php endif; ?>
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
            $(".svn-rollback").click(function() {
                var logId = $(this).attr("log-id"),
                    revisionId = $(this).attr("revision-id");

                layer.confirm("你确认回滚到<b style='color: red;'>"+revisionId+"</b>版本？", {
                    btn: ['确定','取消'] //按钮
                }, function(){
                    var old_revision = $("select[name='old_revision']").val(),
                        new_revision = $("select[name='new_revision']").val();
                    if (old_revision == '' || new_revision == '') {
                        alert("请选择版本");
                    }
                    var postData = {
                        log_id : logId
                    };
                    $.post("<?php echo site_url('online/rollback');?>", postData, function(res) {
                        if (res.status == 1 || res.status == 2) {
                            layer.msg(res.msg, {icon: 1}, function() {
                                window.location.reload();
                            });
                        } else {
                            layer.msg(res.msg, {icon: 2});
                        }
                    }, 'json');
                }, function(){

                });
            });

            $(".logForm").find("select[name='type']").change(function() {
                $(".logForm").submit();
            });
        });
    </script>
	</body>
</html>