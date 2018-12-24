<?php
$name='田馥甄';
$tel='12345678900';
$address='巡回演唱会PLUS成都站';
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <title>PHP发送邮件</title>
    <style type="text/css">
        *{
            margin: 0;
            padding: 0;
        }
        .content{
            width: 60%;
            margin: 0 auto;
            border: 3px solid pink;
            border-radius: 5%;
            font-size: 16px;
        }
    </style>
</head>
<body>
<div class='content'>
    <h1>田馥甄-2017巡回演唱会PLUS成都站</h1>
    <p>姓名： <?php echo $name; ?>  </p>
    <p>电话： <?php echo $tel; ?> </p>
    <p>地址： <?php echo $address; ?> </p>
    <p>点击大麦网<a href="https://www.damai.cn/">https://www.damai.cn/</a></p>
</div>
</body>
</html>
