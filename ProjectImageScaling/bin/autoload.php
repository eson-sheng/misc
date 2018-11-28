<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2018/11/28
 * Time: 下午4:05
 */

/*自动加载*/
spl_autoload_register(function($class){
    require str_replace('\\', DIRECTORY_SEPARATOR, ltrim($class, '\\')).'.php';
});