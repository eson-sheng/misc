<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>请选择需要对比的sql文件</title>
</head>
<body>
<div style="width:1200px;margin:80px auto;">
    <div>
        <h3>数据库快照操作：</h3>
        <code>
mysqldump --skip-extended-insert --skip-dump-date -uroot -p[password] -P[port] [database_name] > [file_name].sql
        </code>
    </div>
    <div>
        <h3>对比快照之间数据的变化：</h3>
        <form id="form" action="index.php" method="GET" enctype="multipart/form-data">
            <select name="file_a" id="file_a">
                <option value="">==请选择==</option>
                <?= $html_option; ?>
            </select>
            <select name="file_b" id="file_b">
                <option value="">==请选择==</option>
                <?= $html_option; ?>
            </select>
            <input type="submit" id="submit" value="提交">
            <input type="reset" id="reset" value="重置">
        </form>
    </div>
</div>
</body>
<!--<script type="text/javascript">-->
<!--    window.onload = function () {-->
<!--        document.getElementById('submit').onclick = function (event) {-->
<!--            event.preventDefault();-->
<!--            var xmlhttp, params, obj, form;-->
<!--            params = [];-->
<!--            form = document.getElementById("form");-->
<!--            obj = document.querySelectorAll('input,select');-->
<!--            for (var i = obj.length - 1; i >= 0; i--) {-->
<!--                params += encodeURIComponent(obj[i].name) + '=' + encodeURIComponent(obj[i].value) + '&';-->
<!--            }-->
<!--            // console.log(params);-->
<!--            xmlhttp = new XMLHttpRequest();-->
<!--            xmlhttp.open("POST", form.action, true);-->
<!--            xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");-->
<!--            xmlhttp.send(params);-->
<!--            xmlhttp.onreadystatechange = function () {-->
<!--                var data = xmlhttp.responseText;-->
<!--                var json = eval("(" + data + ")");-->
<!--                alert(json.error);-->
<!--            }-->
<!--        }-->
<!--    }-->
<!--</script>-->
</html>