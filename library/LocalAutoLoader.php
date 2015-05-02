<?php

class LocalAutoLoader
{
    public static $map = [

    ];

    public static function register()
    {
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register(array(new self(), 'autoload'));
    }

    /**
     * Handles autoloading of classes.
     *
     * @param  string $class A class name.
     *
     * @return boolean Returns true if the class has been loaded
     */
    public function autoload($class)
    {
        $scriptPath = null;
        $scriptName = $class;

        if (isset(static::$map[$class])) {
            $pathInfo = static::$map[$class];
            $scriptPath = sprintf('%s%s', $pathInfo[0], $pathInfo[1]);
            $scriptName = '';
        } else if (strrpos($class, 'Util') == strlen($class) - 4 || strrpos($class, 'Utils') == strlen($class) - 5) {
            $scriptPath = LIB_PATH . '/utils';
        }

        if ($scriptPath) {
            if ($scriptName) {
                Yaf_Loader::import("{$scriptPath}/{$scriptName}.php");
            } else {
                Yaf_Loader::import($scriptPath);
            }
        }
    }
}

