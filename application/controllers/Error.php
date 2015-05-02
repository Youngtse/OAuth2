<?php

/**
 * Created by PhpStorm.
 * User: Yanggen
 * Date: 15/4/14
 * Time: 上午11:12
 */
class ErrorController extends BaseController
{
    public function indexAction()
    {
        $e = $this->getRequest()->getQuery('e');
        if ($e == 'isHR') {
            $e = '账号不是微招聘HR账号，请更换账号后重试';
        } else if ($e == 'isA') {
            $e = '微招聘HR账号未激活，请更换账号后重试';
        }
        $this->getView()->assign('e', $e);
    }

    public function errorAction($exception)
    {
        $this->disableView();
        echo 'error';
        throw $exception;
    }
}
