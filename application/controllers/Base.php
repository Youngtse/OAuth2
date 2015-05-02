<?php
/**
 * Created by PhpStorm.
 * User: Yanggen
 * Date: 15/4/8
 * Time: 上午11:08
 */
class BaseController extends Yaf_Controller_Abstract
{
    protected $_session;

    public function init()
    {
        $this->_session = Yaf_Session::getInstance();
    }

    /**
     * 禁用view引擎
     */
    public function disableView($disableLayout = true)
    {
        if ($disableLayout) {
            $this->disableLayout();
        }
        Yaf_Dispatcher::getInstance()->disableView();
    }

    /**
     * 禁用layout排版
     */
    public function disableLayout()
    {
        Yaf_Registry::set('disableLayout', 1);
    }

    protected function getLegalParam($tag, $legalType, $legalList = [], $default = null, $clearXss = true)
    {
        //检查是否是post请求
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') == 0) {
            $param = $this->getRequest()->getPost($tag, $default);
        } else {
            $param = $this->getRequest()->get($tag, $default);
        }
        if ($param !== null) {
            switch ($legalType) {
                case 'id': {
                    if (preg_match('/^\d{1,20}$/', strval($param))) {
                        $val = intval($param);
                        return $val > 0 ? $val : false;
                    }
                    break;
                }
                case 'time': {
                    return intval($param);
                    break;
                }
                case 'int': {
                    if (!is_numeric($param)) {
                        break;
                    }
                    if ($param >= -2147483648 && $param <= 2147483647) {
                        $val = intval($param);
                    } else {
                        $val = $param * 1;
                    }

                    if (count($legalList) == 2) {
                        if ($val >= $legalList[0] && $val <= $legalList[1])
                            return $val;
                    } else
                        return $val;
                    break;
                }
                case 'float': {
                    if (!is_numeric($param)) {
                        break;
                    }
                    $var = floatval($param);
                    return $var;
                    break;
                }
                case 'str': {
                    $val = trim(strval($param));
//                    $this->verifyXss($val);
                    if($clearXss){
                        $val = $this->clearXSS($val);
                    }
                    if (count($legalList) == 2) {
                        if (($val) >= $legalList[0] && ($val) <= $legalList[1])
                            return $val;
                    } else
                        return $val;
                    break;
                }
                case 'boolean': {
                    if ($param == 'true') {
                        return true;
                    }
                    if ($param == 'false' || $param == 0) {
                        return false;
                    }
                    if ($param > 0) {
                        return true;
                    }
                    return false;
                }
                case 'trim_spec_str': {
                    $val = trim(strval($param));
                    if (!preg_match("/['.,:;*?~`!@#$%^&+=)(<>{}]|\\]|\\[|\\/|\\\\|\"|\\|/", $val)) {
                        if (count($legalList) == 2) {
                            if (strlen($val) >= $legalList[0] && strlen($val) <= $legalList[1])
                                return $val;
                        } else
                            return $val;
                    }
                    break;
                }
                case 'enum': {
                    if (in_array($param, $legalList)) {
                        return $param;
                    }
                    break;
                }
                case 'array': {
                    if (count($legalList) > 0)
                        return explode($legalList[0], strval($param));
                    else {
                        if (empty($param))
                            return array();
                        return explode(',', strval($param));
                    }

                    break;
                }
                case 'json': {
                    return json_decode(strval($param), true);
                    break;
                }
                case 'raw': {
                    return $param;
                    break;
                }
                default:
                    break;
            }
        }
        if ($default != null) {
            return $default;
        }
        return false;
    }

    public function clearXSS($string){
        return strip_tags($string);
    }
}