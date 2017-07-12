<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="cache-control" content="no-cache">
	<meta http-equiv="expires" content="0">
    <title><?php echo $sys_name; ?></title>
	<link href="<?php echo $static_prefix; ?>css/bootstrap.min.css?v=<?php echo $static_version; ?>" rel="stylesheet">
    <link href="<?php echo $static_prefix; ?>css/font-awesome.min.css?v=<?php echo $static_version; ?>" rel="stylesheet">
	<link href="<?php echo $static_prefix; ?>css/plugins/iCheck/custom.css?v=<?php echo $static_version; ?>" rel="stylesheet">
    <link href="<?php echo $static_prefix; ?>css/animate.min.css?v=<?php echo $static_version; ?>" rel="stylesheet">
    <link href="<?php echo $static_prefix; ?>css/style.min.css?v=<?php echo $static_version; ?>" rel="stylesheet">
</head>
<body class="gray-bg">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>上线单列表</h5>
                </div>
                <div class="ibox-content">
                    <form action="<?php echo site_url('online/send_online'); ?>" method="get" class="online-form">
                        <div class="row">
                            <div class="col-sm-3 m-b-xs">
                                <select name="cid" class="input-sm form-control input-s-sm inline">
                                    <option value="">所有项目</option>
                                    <?php foreach($channel_list as $c): ?>
                                        <option value="<?php echo $c['cid']; ?>" <?php echo !empty($cid) && $cid == $c['cid'] ? 'selected' : '';?>><?php echo $c['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <input type="text" name="keyword" placeholder="请输入关键词" class="input-sm form-control" value="<?php  ?>"> <span class="input-group-btn">
                                    <button type="submit" class="btn btn-sm btn-primary"> 搜索</button> </span>
                                </div>
                            </div>
                        </div>
                    </form>
                    <table class="table table-striped table-bordered table-hover " id="editable">
                        <thead>
                        <tr>
                            <th>上线ID</th>
                            <th>项目名</th>
                            <th>上线单标题</th>
                            <th width="15%">产品需求</th>
                            <th width="15%">测试注意点</th>
                            <th>送测版本号</th>
                            <th>更新文件数</th>
                            <th>创建人</th>
                            <th>创建时间</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if ($list): foreach ($list as $v): ?>
                            <tr>
                                <td><?php echo $v['id']; ?></td>
                                <td><?php echo $channel_list[$v['cid']]['name']; ?></td>
                                <td><?php echo $v['title']; ?></td>
                                <td><?php echo $v['demand']; ?></td>
                                <td><?php echo $v['test_note']; ?></td>
                                <td><?php echo $v['end']; ?></td>
                                <td><span class="badge badge-primary"><?php echo count(json_decode($v['files'], TRUE)); ?></span></td>
                                <td><?php echo $v['uname']; ?></td>
                                <td><?php echo $v['apply_time']; ?></td>
                                <td>
                                    <?php echo online_cn_status($v['status']); ?>
                                </td>
                                <td>
									<input type="hidden" name="files" value='<?php echo $v['files']; ?>' />
                                    <button type="button" class="btn btn-outline btn-default look-files" online-id="<?php echo $v['id']; ?>" cid="<?php echo $v['cid']; ?>">查看文件</button>
                                    <?php if ($v['status'] == 5 && $submit_online_pri): ?>
                                        <button type="button" class="btn btn-outline btn-info set-send-online" online-id="<?php echo $v['id']; ?>">代码上线</button>
                                    <?php elseif ($v['status'] == 6 && $rollback_pri && $last_online_id == $v['id']): ?>
                                        <button type="button" class="btn btn-outline btn-warning set-online-rollback" online-id="<?php echo $v['id']; ?>">代码回滚</button>
                                    <?php elseif ($v['status'] == 7): ?>

                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr>
                                <td colspan="100" align="center">没有查询到符合条件的数据...</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                    <?php if ($list): ?>
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

    <div class="modal fade file-modal-form">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <h3 class="m-t-none m-b">更新文件列表</h3>
                    <table class="table table-striped table-bordered table-hover table-files">
                        <thead>
                        <tr>
                            <th>状态</th>
                            <th>文件类型</th>
                            <th>文件名</th>
							<th>操作</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
					<input type="hidden" name="cid" value="" />
                </div>
            </div>
        </div>
    </div>

	<div class="modal fade diff-modal" style="z-index:2051!important">
        <div class="modal-dialog">
            <div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title">文件对比</h4>
				</div>
                <div class="modal-body">
					
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
				</div>
            </div>
        </div>
    </div>

    <script src="<?php echo $static_prefix; ?>js/jquery.min.js?v=<?php echo $static_version; ?>"></script>
    <script src="<?php echo $static_prefix; ?>js/bootstrap.min.js?v=<?php echo $static_version; ?>"></script>
    <script src="<?php echo $static_prefix; ?>js/content.min.js?v=<?php echo $static_version; ?>"></script>
    <script src="<?php echo $static_prefix; ?>js/plugins/validate/jquery.validate.min.js?v=<?php echo $static_version; ?>"></script>
    <script src="<?php echo $static_prefix; ?>js/plugins/validate/messages_zh.min.js?v=<?php echo $static_version; ?>"></script>
	<script src="<?php echo $static_prefix; ?>js/plugins/iCheck/icheck.min.js?v=<?php echo $static_version; ?>"></script>
    <script src="<?php echo $static_prefix; ?>js/plugins/layer/layer.min.js?v=<?php echo $static_version; ?>"></script>
    <script src="<?php echo $static_prefix; ?>js/plugins/layer/extend/layer.ext.js?v=<?php echo $static_version; ?>"></script>
    <script>
		$(document).ready(function() {
            $(".set-send-online").click(function () {
                var postData = {online_id : $(this).attr("online-id")};
                parent.layer.confirm("你确认要执行上线命令？？？", {
                    icon: 3,
                    btn: ['确定','取消']
                }, function(){
                    $.post("<?php echo site_url('online/submit_online');?>", postData, function(res) {
                        parent.layer.close();
                        if (res.status == 1) {
                            parent.layer.msg(res.msg, {icon: 1, time: 3000}, function() {
                                window.location.reload();
                            });
                        } else {
                            parent.layer.msg(res.msg, {icon: 2});
                        }
                    }, 'json');
                }, function(){
                    parent.layer.close();
                });
            });

			$(".set-online-rollback").click(function () {
                var postData = {online_id : $(this).attr("online-id")};
                parent.layer.confirm("你确认要执行回滚命令？？？", {
                    icon: 3,
                    btn: ['确定','取消']
                }, function(){
                    $.post("<?php echo site_url('online/submit_online_rollback');?>", postData, function(res) {
                        parent.layer.close();
                        if (res.status == 1) {
                            parent.layer.msg(res.msg, {icon: 1, time: 3000}, function() {
                                window.location.reload();
                            });
                        } else {
                            parent.layer.msg(res.msg, {icon: 2});
                        }
                    }, 'json');
                }, function(){
                    parent.layer.close();
                });
            });

            $("select[name='cid']").change(function() {
                $(".online-form").submit();
            });

			$(".look-files").on("click", function() {
                var filesObj = $.parseJSON($(this).parent().find(":hidden").val());
                var htmlStr = "";
                for (var k in filesObj) {
                    htmlStr += "<tr>";
                    htmlStr += "<td>"+filesObj[k].item+"</td>";
                    htmlStr += "<td>"+filesObj[k].kind+"</td>";
                    htmlStr += "<td>"+filesObj[k].name+"</td>";
					if (filesObj[k].item == 'modified')
					{
						htmlStr += "<td><?php if ($diff_pri): ?><a href='javascript:void(0);' class='btn btn-outline btn-success diff-btn' revision='"+filesObj[k].revision+"' dir='"+filesObj[k].name+"'>文件对比</a><?php endif; ?></td>";
					} else {
						htmlStr += "<td></td>";
					}
                    htmlStr += "</tr>";
                }
                $(".table-files").find("tbody").html(htmlStr);
				$(".file-modal-form").find("input[name='cid']").val($(this).attr("cid"));
                $(".file-modal-form").modal();
            });

			$(".table-files").delegate(".diff-btn", "click", function () {
				var cid = $(".file-modal-form").find("input[name='cid']").val(),
					revision = $(this).attr("revision"),
					dir = $(this).attr("dir");
					$.post("<?php echo site_url('online/diff');?>", {cid:cid,revision:revision,dir:dir}, function(res) {
                        if (res.status == 1) {
                            $(".diff-modal").find(".modal-body").html(res.data.summarize);
							$(".diff-modal").modal();
                        }
                    }, 'json');
				
			});
		});
	</script>
</body>
</html>