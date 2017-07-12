<?php 
error_reporting(7);
session_start(); 
//$_SESSION["check_number"]; 
//昨晚看到了chianren上的验证码效果，就考虑了一下，用PHP的GD库完成了类似功能 
//先成生背景，再把生成的验证码放上去 
$img_width=80;    //先定义图片的长、宽 
$img_height=30; 
if(intval($_REQUEST['h']) >= 10)
{
	$img_height = intval($_REQUEST['h']);
}
if(intval($_REQUEST['w']) >= 40)
{
	$img_width = intval($_REQUEST['w']);
}
$nmsg = "";
if($_GET["act"]== "init"){ 
    //srand(microtime() * 100000);//PHP420后，srand不是必须的 
    for($Tmpa=0;$Tmpa<4;$Tmpa++){ 
        $nmsg.=dechex(rand(0,15)); 
    }//by sports98 


    $_SESSION["check_number"] = $nmsg; 

    //echo $_SESSION["check_number"];
    //$HTTP_SESSION_VARS[check_number] = strval(mt_rand("1111","9999"));    //生成4位的随机数，放入session中 
    //谁能做下补充，可以同时生成字母和数字啊？？----由sports98完成了 

    $aimg = imageCreate($img_width,$img_height);    //生成图片 
    ImageColorAllocate($aimg, 255,255,255);            //图片底色，ImageColorAllocate第1次定义颜色PHP就认为是底色了 
    $black = ImageColorAllocate($aimg, 255,255,255);        //定义需要的黑色 
    ImageRectangle($aimg,0,0,$img_width-1,$img_height-1,$black);//先成一黑色的矩形把图片包围 

    //下面该生成雪花背景了，其实就是在图片上生成一些符号 
    for ($i=1; $i<=100; $i++) {    //先用100个做测试 
        imageString($aimg,1,mt_rand(1,$img_width),mt_rand(1,$img_height),"*",imageColorAllocate($aimg,mt_rand(200,255),mt_rand(200,255),mt_rand(200,255))); 
        //哈，看到了吧，其实也不是雪花，就是生成＊号而已。为了使它们看起来"杂乱无章、5颜6色"，就得在1个1个生成它们的时候，让它们的位置、颜色，甚至大小都用随机数，rand()或mt_rand都可以完成。 
    } 

    //上面生成了背景，现在就该把已经生成的随机数放上来了。道理和上面差不多，随机数1个1个地放，同时让他们的位置、大小、颜色都用成随机数~~ 
    //为了区别于背景，这里的颜色不超过200，上面的不小于200 
    for ($i=0;$i<strlen($_SESSION["check_number"]);$i++){ 
        //imageString($aimg, mt_rand(3,5),$i*$img_height/4+mt_rand(1,10),mt_rand(1,$img_width/2),
	   	imageString($aimg, 5,$i*$img_width/4+mt_rand(1,10),mt_rand(1,$img_height/2),
		$_SESSION["check_number"][$i],imageColorAllocate($aimg,mt_rand(0,100),mt_rand(0,150),mt_rand(0,200))); 
    } 
    Header("Content-type: image/png");    //告诉浏览器，下面的数据是图片，而不要按文字显示 
    ImagePng($aimg);                    //生成png格式。。。嘿嘿效果蛮像回事的嘛。。。 
    ImageDestroy($aimg); 
}

?>