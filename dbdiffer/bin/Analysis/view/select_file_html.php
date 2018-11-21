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
    <div>
        <h3>快速单独查看或对比数据变化：</h3>
        <form id="alone_from" action="index.php" method="GET" enctype="multipart/form-data">
            <textarea name="alone_sql" id="alone_sql" cols="125" rows="10"
                      placeholder="eg:
INSERT INTO `product_order` VALUES ('4028218166dcfcf40166de4b27eb000c','system','2018-11-04 18:35:59',0,3000.00,14.50,NULL,14.50,NULL,'PP1810121746916',0,'J181104183559334817','20181104','J181104183559334817','PJK2018110418355900011','PP1810121746916','2018-11-04 18:35:59',1,NULL,'4028218166da2a890166da2b6b9500a4',3000,1,'PP1810121746916',1.00000000,1,'ff80808162272ed1016227340b250002',0,'system','2018-11-04 18:35:59',0,'支付成功',1,'6210193310200514239','秦尉寒',NULL,0.0600,NULL);
"></textarea>
            <br>
            <input type="submit" id="submit" name="mod_show" value="单独查看">
            <input type="submit" id="submit" name="mod_contrast" value="对比数据">
            <input type="reset" id="reset" value="重置">
        </form>
    </div>
</div>
</body>
</html>