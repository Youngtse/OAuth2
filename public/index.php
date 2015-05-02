<?php

// 初始化时区
date_default_timezone_set('Asia/Shanghai');
error_reporting(E_ALL);

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
$app->bootstrap()->run();