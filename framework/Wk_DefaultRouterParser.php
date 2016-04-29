<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/4/25
 * Time: 下午3:59
 */

class Wk_DefaultRouterParser implements Wk_RouterInterface{
    public function parse() {
        $reqUriArr = explode('/', explode('?', $_SERVER['REQUEST_URI'])[0]);
        $n = count($reqUriArr);
        if(empty($reqUriArr[$n-1])) {
            $n = $n - 1;
        }
        if ($n === 2) {
            $controller = empty($reqUriArr[1]) ? 'index' : $reqUriArr[1];
            if(class_exists(ucfirst($controller.'Controller'))) {
                $action = 'index';
            } else {
                $controller = 'index';
                $action = empty($reqUriArr[1]) ? 'index' : $reqUriArr[1];
            }
            return [$controller, $action];
        }
        if ($n === 3) {
            $controller = empty($reqUriArr[1]) ? 'index' : $reqUriArr[1];
            $action = empty($reqUriArr[2]) ? 'index' : $reqUriArr[2];
            return [$controller, $action];
        }
        if ($n%2 === 1) {
            $controller = empty($reqUriArr[1]) ? 'index' : $reqUriArr[1];
            $action = empty($reqUriArr[2]) ? 'index' : $reqUriArr[2];
            for($i = 3; $i < $n; $i+=2) {
                self::setParam($reqUriArr[$i], $reqUriArr[$i+1]);
            }
            return [$controller, $action];
        }
        return ['kError','err404'];
    }

    private static function setParam($key, $value) {
        $_GET[$key] = $value;
        $_REQUEST[$key] = $value;
    }
} 