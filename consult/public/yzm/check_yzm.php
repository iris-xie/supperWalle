<?
error_reporting(7);
session_start();
include 'securimage.php';

function authcode_check_code($code)
{
	$img = new Securimage();
	$valid = $img->check($code);
	return $valid;
}

if(!authcode_check_code($_POST['number']))
{
	
	echo("<script>
           window.alert('验证码错误!');
           window.location.href='".$_SERVER['HTTP_REFERER']."';
         </script>"); 
	@exit;
}

?> 