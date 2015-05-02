<?php

class AuthPlugin extends Yaf_Plugin_Abstract
{
    /**
     * @var string The currently called controller name
     */
    private $controllerName;
    /**
     * @var string The currently called action alias name, which is the return of ActionPlugin
     */
    private $actionAliasName;

    /**
     * The main authentication process
     *
     * @param Yaf_Request_Abstract|Yaf_Request_Simple|Yaf_Request_Http $request
     * @param Yaf_Response_Abstract $response
     */
    public function dispatchLoopStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
        $this->controllerName = strtolower($request->getControllerName());
        $this->actionAliasName = strtolower($request->getActionName());

        if(!($this->controllerName == 'index' && in_array(['index', 'register'], $this->actionAliasName))) {
            if(!Yaf_Session::getInstance()->has('login_uid')) {
                header("Location:/");
                die();
            }
        }
    }
}