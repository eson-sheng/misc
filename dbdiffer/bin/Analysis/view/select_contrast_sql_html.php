<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>请选择需要对比的sql数据</title>
    <style>
        span{
            display: block;
            font-family: PingFangSC-Regular;
            font-size: 14px;
            color: #482929;
            border: double 1px pink;
            border-radius: 10px;
            margin: 5px;
            letter-spacing: 0;
            line-height: 20px;
            overflow:hidden;
            text-overflow:ellipsis;
            white-space: normal;
            display:-webkit-box;
            -webkit-box-orient:vertical;
            -webkit-line-clamp:2;/*规定最多显示两行*/
        }
    </style>
</head>
<body>
<div style="width:1200px;margin:80px auto;">
    <div>
        <h3>请选择需要对比的sql数据：</h3>
        <form id="form" action="index.php" method="GET" enctype="multipart/form-data">
            <div>
                <h4>请选择对比数据A类：</h4>
                <?= $rs_a; ?>
            </div>
            <div>
                <h4>请选择对比数据B类：</h4>
                <?= $rs_b; ?>
            </div>
            <input type="hidden" name="contrast" id="contrast" required value="contrast">
            <br>
            <input type="submit" id="submit" value="提交">
            <input type="reset" id="reset" value="重置">
        </form>
    </div>
</div>
</body>
<script type="text/javascript" src="../BitTools/public_link/js/jquery-1.7.2.js"></script>
<script type="text/javascript">
    $(function () {
        /*两个自动选择开始对比*/
        var input = $("input[name='contrast_a']");
        var params = "";
        var tmp = [];
        if (input.length == 2) {
            input.each(function (i) {
                tmp[i] = $(this).val();
            });
            params += "contrast_a=" +
                encodeURIComponent(tmp[0]) +
                "&contrast_b=" + encodeURIComponent(tmp[1]) +
                "&contrast=contrast";
            location.href = 'index.php?' + params;
        }
    });
</script>
</html>