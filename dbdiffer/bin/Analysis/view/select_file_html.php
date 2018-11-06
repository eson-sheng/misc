<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>请选择需要对比的sql文件</title>
</head>
<body>
<div>
    <form id="form" action="index.php" method="POST" enctype="multipart/form-data">
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