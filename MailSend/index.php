<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2018-12-21
 * Time: 10:48
 */

require_once __DIR__ . "/bin/phpmail/Email.php";

/*配置文件加载*/
$config = require __DIR__ . '/config/web.php';
if (is_file(__DIR__ . '/config/local_web.php')) {
    $local_web = require __DIR__ . '/config/local_web.php';
    $config = array_merge($config, $local_web);
}

$index = new Email($config);
$index->index();