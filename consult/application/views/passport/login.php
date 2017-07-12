<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title><?php echo $sys_name; ?> - 登录</title>
    <meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="cache-control" content="no-cache">
	<meta http-equiv="expires" content="0">
    <link href="<?php echo $static_prefix; ?>css/bootstrap.min.css?v=<?php echo $static_version; ?>" rel="stylesheet">
    <link href="<?php echo $static_prefix; ?>css/font-awesome.css?v=<?php echo $static_version; ?>" rel="stylesheet">
    <link href="<?php echo $static_prefix; ?>css/animate.css?v=<?php echo $static_version; ?>" rel="stylesheet">
    <link href="<?php echo $static_prefix; ?>css/style.css?v=<?php echo $static_version; ?>" rel="stylesheet">
    <link href="<?php echo $static_prefix; ?>css/login.css?v=<?php echo $static_version; ?>" rel="stylesheet">
    <!--[if lt IE 9]>
    <meta http-equiv="refresh" content="0;<?php echo site_url('passport/upgrade');?>" />
    <![endif]-->
	<script src="<?php echo $static_prefix; ?>js/jquery.min.js?v=<?php echo $static_version; ?>"></script>
    <script src="<?php echo $static_prefix; ?>js/bootstrap.min.js?v=<?php echo $static_version; ?>"></script>
	<script src="<?php echo $static_prefix; ?>js/plugins/layer/layer.min.js?v=<?php echo $static_version; ?>"></script>
    <script>
        if (window.top !== window.self) {
            window.top.location = window.location;
        }
    </script>
    <style>
        .captcha{
            margin-left: 2px;
        }
        .captcha i{
            display: block;
            float: left;
            vertical-align: bottom;
            margin-top: 16px;
            cursor: pointer;
        }
        .captcha input{
            width:80px;
            margin-right: 5px;
            float:left;
        }
        .clear{
            clear: both;
        }
        .m-b{
            margin-bottom: 0;;
        }
        .yzm{
            width: 90px;
			height: 30px;
        }
        </style>
</head>
<body class="signin">
    <div class="signinpanel">
        <div class="row">
            <div class="col-sm-12">
                <form method="post" id="loginForm">
                    <h4 class="no-margins"><?php echo $sys_name; ?></h4>
                    <!--<p class="m-t-md">登录到<?php echo $sys_name; ?></p>-->
                    <input type="text" class="form-control uname" name="account" placeholder="用户名" required/>
                    <input type="password" class="form-control pword m-b" name="password" placeholder="密码" required/>
                    <div class="row captcha">
                        <input type="text" class="form-control" name="captcha" placeholder="验证码" required/>
                        <i>
                           <img src="<?php echo site_url('/yzm/authcode_gen_img')?>" url="<?php echo site_url('/yzm/authcode_gen_img')?>" class="yzm"/>
                        </i>
                        <div class="clear"></div>
                    </div>
                    <button class="btn btn-success btn-block" type="submit">登录</button>
                </form>
            </div>
        </div>
        <div class="signup-footer">
            <div class="pull-left">
                
            </div>
        </div>
    </div>
	<script>
        function readAsDataURL(file){

        }
        $(function () {
			var $loginForm = $("#loginForm");
			$loginForm.submit(function (e) {
				if (e) {
					e.preventDefault();
				}
				var account = $loginForm.find("[name='account']").val(),
                    password = $loginForm.find("[name='password']").val(),
                    captcha = $loginForm.find("[name='captcha']").val();
				if (account == "") {
					parent.layer.msg('请填写登录名！');
					return false;
				}
				if (password == "") {
					parent.layer.msg('请填写登录密码！');
					return false;
				}
                if (captcha == "") {
                    parent.layer.msg('请填写验证码！');
                    return false;
                }
				$.post("<?php echo site_url('/passport/login');?>", $loginForm.serialize(), function (res) {
					if (res.status == 1) {
						location.href = "<?php echo site_url('welcome');?>";
					} else {
						$(".captcha").find("img").click();
						parent.layer.msg(res.msg);
					}
				}, "json");
			});

            $(".captcha").delegate("img", "click", function() {
                $(this).attr("src", $(this).attr("url")+"?r="+Math.random()*10000);
            });
		});
	</script>
</body>
</html>
