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
        <form id="form" action="index.php" method="GET" enctype="multipart/form-data">
            <h3>配置选择：</h3>
            <select name="cache" id="cache">
                <option value="">==请选择==</option>
                <?= $html_option_cache; ?>
            </select>
            <input type="file" id="cache_file" value="" multiple="multiple" />
            <h3>对比快照之间数据的变化：</h3>
            <p>
                <input type="file" id="dump_file" value="" multiple="multiple"/>
            </p>
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
    <div>
        <h3>快速单独查看或对比数据变化：</h3>
        <form id="alone_from" action="index.php" method="GET" enctype="multipart/form-data">
            <textarea name="alone_sql" id="alone_sql" cols="125" rows="10"
                      placeholder="eg:
INSERT INTO `product_order` VALUES ('4028218166dcfcf40166de4b27eb000c','system','2018-11-04 18:35:59',0,3000.00,14.50,NULL,14.50,NULL,'PP1810121746916',0,'J181104183559334817','20181104','J181104183559334817','PJK2018110418355900011','PP1810121746916','2018-11-04 18:35:59',1,NULL,'4028218166da2a890166da2b6b9500a4',3000,1,'PP1810121746916',1.00000000,1,'ff80808162272ed1016227340b250002',0,'system','2018-11-04 18:35:59',0,'支付成功',1,'6210193310200514239','秦尉寒',NULL,0.0600,NULL);
"></textarea>
            <br>
            <input type="submit" id="submit_mod_show" name="mod_show" value="单独查看">
            <input type="submit" id="submit_mod_contrast" name="mod_contrast" value="对比数据">
            <input type="reset" id="reset" value="重置">
        </form>
    </div>
</div>
</body>
<script type="text/javascript" src="./js/pi.js"></script>
<script type="text/javascript" src="./js/config.js"></script>
<script type="text/javascript" src="./js/jquery.min.js"></script>
<script type="text/javascript">
$(function(){
    config = createConfig("save_input", [
        "file_a",
        "file_b",
        "alone_sql",
        "cache"
    ]);

    $("#submit").click(function(){
        config.saveCtrls();
    });
    
    $("#submit_mod_show").click(function(){
        config.saveCtrls();
    });
    
    $("#submit_mod_contrast").click(function(){
        config.saveCtrls();
    });

    $("#cache_file").change(function () {

        var formData = new FormData();
        var files = $(this)[0].files;
        $(files).each(function () {
            formData.append('cache_file[]', this);
        });

        $.ajax({
            url: '',
            dataType:"json",
            type: 'POST',
            cache: false,
            data: formData,
            processData: false,
            contentType: false
        }).done(function (res) {
            $(res).each(function(i){
                console.log(res[i]);
                if (!res[i].status) {
                    alert("上传失败！");
                }
            });
            /*判断上传是一个时候，直接比较*/
            if (res.length === 1) {
                console.log(files[0].name);
                $("#cache").append('' +
                    '<option selected="selected" value="cache/' + files[0].name + '">cache/' + files[0].name + '</option>'
                );
                // $("#submit").click();
                return true;
            }
            /*刷新本页面*/
            history.go(0);
        }).fail(function (res) {
            alert("上传失败：" + res.data);
        });
    });

    $("#dump_file").change(function () {

        var formData = new FormData();
        var files = $(this)[0].files;
        $(files).each(function () {
            formData.append('dump_file[]', this);
        });

        $.ajax({
            url: '',
            dataType:"json",
            type: 'POST',
            cache: false,
            data: formData,
            processData: false,
            contentType: false
        }).done(function (res) {
            $(res).each(function(i){
                console.log(res[i]);
                if (!res[i].status) {
                    alert("上传失败！");
                }
            });
            /*刷新本页面*/
            history.go(0);
        }).fail(function (res) {
            alert("上传失败：" + res.data);
        });

    });

});
</script>
</html>