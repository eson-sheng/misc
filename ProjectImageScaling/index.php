<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2018/11/28
 * Time: 下午3:54
 */
require_once __DIR__ . "/bin/autoload.php";

/*配置文件加载*/
$config = require __DIR__ . '/config/web.php';
if (is_file(__DIR__ . '/config/local_web.php')) {
    $local_web = require __DIR__ . '/config/local_web.php';
    $config = array_merge($config, $local_web);
}

use Yewu\Scaling;

$index = new Scaling($config);
$index->index();