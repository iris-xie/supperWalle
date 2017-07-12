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
</head>
<body class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>项目列表</h5>
                    </div>
				<div class="ibox-content text-center p-md">
					<div class="row">
						<?php foreach ($channel_list as $val): ?>
							<a href="<?php echo site_url('online/create').'?cid='.$val['cid']; ?>" class="btn btn-w-m btn-white"><?php echo $val['name']; ?></a>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
    </div>
    <script src="<?php echo $static_prefix; ?>js/jquery.min.js?v=<?php echo $static_version; ?>"></script>
    <script src="<?php echo $static_prefix; ?>js/bootstrap.min.js?v=<?php echo $static_version; ?>"></script>
	</body>
</html>