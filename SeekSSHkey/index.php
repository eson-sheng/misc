<?php
/*数组配置*/
$config = [
    'dsn' => 'mysql:host=localhost;dbname=gogs;',
    'username' => 'root',
    'password' => 'root123.',
    'charset' => 'utf8',
];
/*本地配置*/
if (is_file(__DIR__ . '/local_config.php')) {
    $local_web = require __DIR__ . '/local_config.php';
    $config = array_merge($config, $local_web);
}
/*实例化PDO数据库*/
$_db = new PDO($config['dsn'], $config['username'], $config['password']);
$_db->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
$_db->exec('SET NAMES ' . $config['charset']);
// echo $_db->getAttribute(constant("PDO::ATTR_SERVER_VERSION"));
?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>通过SSHkey寻找gogs用户账号</title>
    </head>
    <body>
    <div style="width:1200px;margin:80px auto;">
        <div>
            <h3>请输入ssh公钥匹配gogs用户账号：</h3>
            <form id="form" action="index.php" method="POST" enctype="multipart/form-data">
                <div>
                    <textarea name="sshkey" id="sshkey" cols="120" rows="10" placeholder="请输入ssh公钥"></textarea>
                </div>
                <input type="submit" id="submit" value="提交">
                <input type="reset" id="reset" value="重置">
            </form>
        </div>
    </div>
    </body>
    </html>
<?php
if (!empty($_POST['sshkey'])) {
    $ssh_key = trim($_POST['sshkey']);
    $sql = "
    SELECT 
        u.name 
    FROM 
        public_key AS p,user AS u 
    WHERE 
        p.owner_id = u.id AND p.content = ? ;
    ";
    $query = $_db->prepare($sql);
    $query->execute(array($ssh_key));
    $ret = $query->fetch();
    echo "<script>alert('{$ret[0]}');</script>";
}
?>