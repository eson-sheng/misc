<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2018/11/5
 * Time: 上午10:41
 */

return [
    'dsn' => 'mysql:host=localhost;dbname=lalala;',
    'database' => 'lalala',
    'username' => 'root',
    'password' => 'root123.',
    'charset' => 'utf8',
    'sql_file_path' => 'dump',
    'out_path' => 'out',
    'mysql_cache' => 'cache',
    'GBK' => strpos(strtolower(PHP_OS), 'win') === 0,
];