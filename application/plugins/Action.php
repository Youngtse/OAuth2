<?php

class ActionPlugin extends Yaf_Plugin_Abstract
{
    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
        $aliasName = $request->getActionName();
        $name = str_replace('-', '', $aliasName);
        $request->setActionName($name);

        $aliasController = $request->getControllerName();
//        $name = str_replace('-','',$aliasController);
        $list = explode('-', $aliasController);
        if ($list && count($list))
        {
            foreach ($list as &$item)
            {
                $item = ucfirst($item);
            }
            $request->setControllerName(implode('', $list));
        }


        $request->setParam('action_alias_name', $aliasName);
    }
}