<?php
/**
 * Created by PhpStorm.
 * User: Yanggen
 * Date: 15/4/8
 * Time: 上午11:14
 */
class Bootstrap extends Yaf_Bootstrap_Abstract
{
    protected function _initRoute(Yaf_Dispatcher $dispatcher)
    {
        $router = $dispatcher->getRouter();
        $router->addRoute('token', new Yaf_Route_Rewrite('token$', array('controller' => 'index', 'action' => 'token')));
        $router->addRoute('register', new Yaf_Route_Rewrite('register$', array('controller' => 'index', 'action' => 'register')));
    }

    protected function _initPlugin(Yaf_Dispatcher $dispatcher)
    {
        $dispatcher->registerPlugin(new ActionPlugin());
//        $dispatcher->registerPlugin(new AuthPlugin());
    }
}