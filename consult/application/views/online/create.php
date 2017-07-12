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
	<link href="<?php echo $static_prefix; ?>css/plugins/iCheck/custom.css?v=<?php echo $static_version; ?>" rel="stylesheet">
    <link href="<?php echo $static_prefix; ?>css/animate.min.css?v=<?php echo $static_version; ?>" rel="stylesheet">
    <link href="<?php echo $static_prefix; ?>css/style.min.css?v=<?php echo $static_version; ?>" rel="stylesheet">
    <style>
        .diff-info{
            font-size:14px;
            line-height: 180%;
        }
        .change-list-div{
            max-height: 400px;
        }
    </style>
</head>
<body class="gray-bg">
<form class="form-horizontal m-t submit-apply" method="get" action="<?php echo $url;?>">
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>创建申请上线单</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">项目名：</label>
                            <div class="col-sm-8">
                                <b><?php echo $channel_info['name'];?></b>
                            </div>
                        </div>
						<div class="form-group">
							<label class="col-sm-3 control-label">上线单标题：</label>
							<div class="col-sm-8">
								<input type="text" name="title" class="form-control" placeholder="上线单标题">
							</div>
						</div>
						<div class="form-group" style="display: none;">
							<label class="col-sm-3 control-label">当前版本号：</label>
							<div class="col-sm-8 input-append">
								<b><?php echo $data['revision']; ?></b>
								<input type="hidden" name="old_revision" value="<?php echo $data['revision']; ?>" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">版本信息：</label>
							<div class="col-sm-8 input-group">
								<select name="new_revision" class="form-control">
                                    <option value="" author="0">--请选择版本-</option>
									<?php foreach($data['server_log'] as $d): ?>
										<option value="<?php echo $d['revision']; ?>" author="<?php echo $d['author']; ?>" <?php echo (!empty($svn_account) && $svn_account != $d['author']) ? 'style="display:none;"':'';?>><?php echo $d['revision'].'#'.$d['author'].'#'.date('Y-m-d H:i:s', $d['date']).'#'.$d['msg']; ?></option>
									<?php endforeach; ?>
								</select>
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-primary show-all-revision">全部</button>
                                </span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">产品需求：</label>
							<div class="col-sm-8">
								<textarea name="demand" class="form-control" placeholder="产品需求" rows="5"></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">测试注意点：</label>
							<div class="col-sm-8">
								<textarea name="test_note" class="form-control" placeholder="测试注意点" rows="5"></textarea>
							</div>
						</div>
                    </div>
                </div>
            </div>
        </div>

		<div class="row">
			<div class="col-sm-12">
				<div class="ibox-title">
					<h5>变更文件列表</h5>
				</div>
				<div class="ibox-content">
					<div class="table-responsive change-list-div">

						<table class="table table-striped change-list">
							<thead>
								<tr>
									<th>状态</th>
									<th>文件类型</th>
									<th>文件名</th>
									<th><input type='checkbox' name='allCheck' value='1' checked /></th>
								</tr>
							</thead>
							<tbody class="change-list-tbody">
								<tr><td colspan="3" align="center"></td></tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="col-sm-12 ibox-content text-center">
				<input type="hidden" name="cid" value="<?php echo $cid; ?>" />
				<input type="hidden" name="type" value="create" />
				<input class="btn btn-large btn-block btn-primary" type="submit" value="创建上线单">
			</div>
		</div>
    </div>
    </form>
    <script src="<?php echo $static_prefix; ?>js/jquery.min.js?v=<?php echo $static_version; ?>"></script>
    <script src="<?php echo $static_prefix; ?>js/bootstrap.min.js?v=<?php echo $static_version; ?>"></script>
    <script src="<?php echo $static_prefix; ?>js/content.min.js?v=<?php echo $static_version; ?>"></script>
    <script src="<?php echo $static_prefix; ?>js/plugins/validate/jquery.validate.min.js?v=<?php echo $static_version; ?>"></script>
    <script src="<?php echo $static_prefix; ?>js/plugins/validate/messages_zh.min.js?v=<?php echo $static_version; ?>"></script>
	<script src="<?php echo $static_prefix; ?>js/plugins/iCheck/icheck.min.js?v=<?php echo $static_version; ?>"></script>
    <script src="<?php echo $static_prefix; ?>js/plugins/layer/layer.min.js?v=<?php echo $static_version; ?>"></script>
    <script src="<?php echo $static_prefix; ?>js/plugins/layer/extend/layer.ext.js?v=<?php echo $static_version; ?>"></script>

    <script>
        var cid = "<?php echo $cid; ?>";
        var revision = "<?php echo !empty($data['revision']) ? $data['revision'] : ''; ?>";
		$(document).ready(function() {
            var $submitApply = $(".submit-apply");
            $submitApply.submit(function(e) {
                if (e) {
                    e.preventDefault();
                }
                var old_revision = $("input[name='old_revision']"),
                    new_revision = $("select[name='new_revision']"),
                    items = $("input[name='item[]']"),
                    title = $("input[name='title']"),
                    demand = $("textarea[name='demand']"),
					test_note = $("textarea[name='test_note']");

                if (title.val() == '') {
					title.focus();
                    parent.layer.msg('请填写上线单标题~~~',{icon: 2});
                    return false;
                }

                if (new_revision.val() == '') {
                    parent.layer.msg('请选择版本~~~',{icon: 2});
                    return false;
                }

                if (demand.val() == '') {
					demand.focus();
                    parent.layer.msg('请填写产品需求~~~',{icon: 2});
                    return false;
                }

				if (test_note.val() == '') {
					test_note.focus();
                    parent.layer.msg('请填写测试注意点~~~',{icon: 2});
                    return false;
                }

                if (items.length == 0) {
                    parent.layer.msg('没有变更文件~~~',{icon: 2});
                    return false;
                }
				
				var isChecked = false;
				$(".change-list-tbody").find(":checkbox").each(function () {
					if ($(this).prop("checked") == true)
					{
						isChecked = true;
					}
				});
				if (isChecked == false) {
					parent.layer.msg('请选择更新文件~~~',{icon: 2});
                    return false;
				}

                layer.confirm("你确认要创建申请单？", {
                    btn: ['确定','取消'] //按钮
                }, function(){
                    $.post("<?php echo $url;?>", $submitApply.serialize(), function(res) {
                        if (res.status == 1) {
                            layer.msg(res.msg, {icon: 1, time: 4000}, function() {
                                window.location.href = "<?php echo site_url('online/apply_list'); ?>";
                            });
                        } else {
                            layer.msg(res.msg, {icon: 2});
                        }
                    }, 'json');
                }, function(){

                });
            });

			$("input[name='allCheck']").on("click", function () {
				var obj = $(this);
				$(".change-list-tbody").find(":checkbox").each(function () {
					if (obj.prop("checked") == true)
					{
						$(this).parent().addClass("checked");
						$(this).prop("checked", true);
					} else {
						$(this).parent().removeClass("checked");
						$(this).prop("checked", false);
					}
				});
			});

            var diff_func = function() {
                var new_revision = $("select[name='new_revision']").val();
                if (new_revision == '') {
                    $(".change-list tbody").html("<tr><td colspan='100' align='center'>请选择版本...<td></tr>");
                    return false;
                }
                $(".change-list tbody").html("<tr><td colspan='100' align='center'>数据加载中...<td></tr>");
                var postData = {
                    type : 'diff',
                    new_revision : new_revision,
                    cid : cid
                };
                $.post("<?php echo $url;?>", postData, function(res) {
                    if (res.status == 1) {
                        var html = "<tr><td colspan='100' align='center'>没有查询到符合条件的数据...<td></tr>",
                            data = res.data;
                        if (data.length > 0) {
                            html = "";
                            for (var d in data) {
                                html += "<tr>";
                                var item = data[d].item != undefined && data[d].item != "null" ? data[d].item : "";
                                var kind = data[d].kind != undefined && data[d].kind != "null" ? data[d].kind : "";
                                var name = data[d].name != undefined && data[d].name != "null" ? data[d].name : "";
                                var old_revision = data[d].old_revision != undefined && data[d].old_revision != "null" ? data[d].old_revision : "";
                                html += "<td><input type='hidden' name='item[]' value='"+item+"'>"+item+"</td>";
                                html += "<td><input type='hidden' name='kind[]' value='"+kind+"'>"+kind+"</td>";
                                html += "<td><input type='hidden' name='name[]' value='"+name+"'>"+name+"</td>";
                                html += "<td><input type='checkbox' name='check[]' class='i-checks' value='"+name+"' checked><input type='hidden' name='revision[]' value='"+old_revision+"'></td>";
                                html += "</tr>";
                            }
                        }
                        $(".change-list tbody").html(html);
                        $(".i-checks").iCheck({checkboxClass:"icheckbox_square-green",radioClass:"iradio_square-green"});
                    } else {
                        layer.msg(res.msg, {icon: 2});
                    }
                }, 'json');
            }
            $("select[name='old_revision'],select[name='new_revision']").on("change", diff_func);
            if (cid) {
                diff_func();
            }

            $submitApply.delegate(".show-all-revision", "click", function() {
                $("select[name='new_revision']").children("option").show();
                $("select[name='new_revision']").children("option[author='0']").prop("selected",true).change();
                $(this).html("仅自己").removeClass("show-all-revision").addClass("show-self-revision").removeClass("btn-primary").addClass("btn-info");
            });
            $submitApply.delegate(".show-self-revision", "click", function() {
                $("select[name='new_revision']").children("option").hide();
                $("select[name='new_revision']").children("option[author='0']").prop("selected",true).change();
                $("select[name='new_revision']").children("option[author='<?php echo $svn_account; ?>']").show();
                $(this).html("全部").removeClass("show-self-revision").addClass("show-all-revision").addClass("btn-primary").removeClass("btn-info");
            });
		});
	</script>
</body>
</html>