<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>PHP发送邮件</title>
</head>
<body>
    <div id="main" style="width:1200px;margin:80px auto;">
        <form id="form" action="" method="POST" enctype="multipart/form-data">
            <h3>PHP发送邮件</h3>
            <div class="group">
                <label for="to">收件人</label>
                <input type="email" name="to" id="to" required placeholder="收件人邮箱地址" value="">
            </div>
            <div class="group">
                <label for="subject">主题</label>
                <input type="text" name="subject" id="subject" required placeholder="邮件主题名称" value="">
            </div>
            <div>
                <textarea name="msg" id="msg" cols="80" rows="10" placeholder="想写的什么..."></textarea>
            </div>
            <input type="submit" id="submit" value="提交">
            <input type="reset" id="reset" value="重置">
        </form>
    </div>
</body>
<script type="text/javascript" src="./link/js/jquery.min.js"></script>
<script type="text/javascript" src="./link/js/index.js"></script>
</html>