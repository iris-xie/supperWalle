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
    <link href="<?php echo $static_prefix; ?>css/font-awesome.min.css?v=<?php echo $static_version; ?>"
          rel="stylesheet">
    <link
        href="<?php echo $static_prefix; ?>css/plugins/dataTables/dataTables.bootstrap.css?v=<?php echo $static_version; ?>"
        rel="stylesheet">
    <link href="<?php echo $static_prefix; ?>css/animate.min.css?v=<?php echo $static_version; ?>" rel="stylesheet">
    <link href="<?php echo $static_prefix; ?>css/style.min.css?v=<?php echo $static_version; ?>" rel="stylesheet">

    <link href="<?php echo $static_prefix; ?>css/plugins/iCheck/custom.css" rel="stylesheet">

    <link href="<?php echo $static_prefix; ?>css/plugins/chosen/chosen.css" rel="stylesheet">

    <link href="<?php echo $static_prefix; ?>css/plugins/colorpicker/css/bootstrap-colorpicker.min.css"
          rel="stylesheet">

    <link href="<?php echo $static_prefix; ?>css/plugins/cropper/cropper.min.css" rel="stylesheet">

    <link href="<?php echo $static_prefix; ?>css/plugins/switchery/switchery.css" rel="stylesheet">

    <link href="<?php echo $static_prefix; ?>css/plugins/jasny/jasny-bootstrap.min.css" rel="stylesheet">

    <link href="<?php echo $static_prefix; ?>css/plugins/nouslider/jquery.nouislider.css" rel="stylesheet">

    <link href="<?php echo $static_prefix; ?>css/plugins/datapicker/datepicker3.css" rel="stylesheet">

    <link href="<?php echo $static_prefix; ?>css/plugins/ionRangeSlider/ion.rangeSlider.css" rel="stylesheet">
    <link href="<?php echo $static_prefix; ?>css/plugins/ionRangeSlider/ion.rangeSlider.skinFlat.css" rel="stylesheet">
    <link href="<?php echo $static_prefix; ?>css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css"
          rel="stylesheet">
    <link href="<?php echo $static_prefix; ?>css/plugins/clockpicker/clockpicker.css" rel="stylesheet">
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?php echo $user['name']; ?> 权限管理</h5>
                </div>
                <div class="ibox-content">
					<fieldset>
						<h3>
							模块权限
						</h3>
						<?php foreach ($module_config as $module): ?>
							<?php if ($module['parent_id'] == 0): ?>
									<div class="row">
									<div class="col-sm-6">
									<div class="checkbox checkbox-primary">
										<input type="checkbox" class="module" name="module" id="module<?=$module['id']?>" value="<?= $module['id'] ?>" <?php echo (in_array($module['id'],$my_module_rights)||in_array(1,$my_module_rights)) ?  'checked' : '' ?>/>
										<label for="module<?=$module['id']?>">
											<?= $module['name']; ?>
										</label>
									</div>
									</div>
									</div>
								<?php foreach ($module_config as $action): ?>
									<?php if ($action['parent_id'] == $module['id']): ?>
										<div class="checkbox  checkbox-circle  checkbox-danger checkbox-inline">
											<input type="checkbox" class="action" id="action<?=$action['id']?>" value="<?=$action['id']?>" name="action" <?php echo in_array($action['id'],$my_action_rights) ? 'checked' : '' ?> />
											<label for="action<?=$action['id']?>"><?php echo $action['name']; ?></label>
										</div>
									<?php endif; ?>
								<?php endforeach; ?>
							<?php endif; ?>
						<?php endforeach; ?>
						<br/>
                        <br/>
                        <h3>
                            项目权限
                        </h3>
                        <?php foreach ($channel_list as $channel): ?>
                            <div class="checkbox  checkbox-circle  checkbox-danger checkbox-inline">
                                <input type="checkbox" class="channel" id="action<?=$channel['cid']?>" value="<?=$channel['cid']?>" name="channel" <?php echo in_array($channel['cid'], $my_project_rights) ? 'checked' : '' ?> />
                                <label for="action<?=$channel['cid']?>"><?php echo $channel['name']; ?></label>
                            </div>
                        <?php endforeach; ?>
                        <br/>
						<div class="row" style="margin-top: 10px">
                            <?php if ($set_acl_pri): ?>
							    <button class="btn btn-primary col-md-1 col-md-offset-1" id="confirm">确认</button>
                            <?php endif; ?>
                            <a class="btn btn-white col-md-1 col-md-offset-1" href="<?php echo site_url('user/index'); ?>">取消</a>
						</div>
					</fieldset>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="<?php echo $static_prefix; ?>js/jquery.min.js?v=<?php echo $static_version; ?>"></script>
<script>
    $(function() {
        var uid = "<?php echo $user['uid']; ?>";
        $("#confirm").on('click', function () {
            var module = [],
                action = [],
                project = [];

            $("input[name='module']:checked").each(
                function () {
                    module.push($(this).val());
                }
            );
            $("input[name='action']:checked").each(
                function () {
                    action.push($(this).val());
                }
            );
            $("input[name='channel']:checked").each(
                function () {
                    project.push($(this).val());
                }
            );
			
			parent.layer.confirm("确定要提交吗？", {
				icon: 3,
				btn: ['确定','取消']
			}, function() {
                $.get("<?php echo site_url('user/set_acl');?>", 'uid=' + uid + '&module=' + module + '&action=' + action + '&project=' + project, function (res) {
                    if (res.status == 1) {
                        parent.layer.msg(res.msg, {icon: 1, time:2000}, function(){
                            window.location.href = "<?php echo site_url('user/index');?>";
                        });
                    } else {
                        parent.layer.msg(res.msg, {icon: 2});
                    }
                }, "json");
            }, function(){
				parent.layer.close();
            });

		});
	});
</script>