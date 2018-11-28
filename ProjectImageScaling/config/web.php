<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2018/11/28
 * Time: 下午4:06
 */

return [
    'GBK' => strpos(strtolower(PHP_OS), 'win') === 0,
    'img_src' => './tmp/img', //原始图片目录
    'img_backup' => './tmp/backup', //备份图片目录
    'img_src_out' => './tmp/out_img', //缩放图片目录
];