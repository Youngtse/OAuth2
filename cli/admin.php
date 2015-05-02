<?php
/**
 * Created by PhpStorm.
 * User: Yanggen
 * Date: 15/4/22
 * Time: 下午6:33
 */

// 初始化时区
date_default_timezone_set('Asia/Shanghai');

// 初始化常量
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../'));
defined('LIB_PATH') || define('LIB_PATH', realpath(APPLICATION_PATH . '/library'));

// 初始化装载类
Yaf_Loader::import(LIB_PATH . '/LocalAutoLoader.php');
LocalAutoLoader::register();

// 初始化config
$config = new Yaf_Config_Ini(APPLICATION_PATH . '/config/config.ini', 'production');
Yaf_Registry::set('config', $config);

$app = new Yaf_Application(APPLICATION_PATH . '/config/application.ini');
$app->getDispatcher()->dispatch(new Yaf_Request_Simple("CLI", "Index", "Index", "register"));