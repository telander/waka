<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/4/25
 * Time: 下午3:47
 */

class SimpleRouterParser implements Wk_RouterInterface{
    public function parse() {
        $uri = explode("?", $_SERVER["REQUEST_URI"])[0];
//        ajax接口
        if(strpos($uri, "/ajax") === 0) {
            $reqUriArr = explode('/', $uri);
            $controller = empty($reqUriArr[2]) ? 'index' : $reqUriArr[2];
            $action = empty($reqUriArr[3]) ? 'index' : $reqUriArr[3];

            return [$controller, $action];
        }
        $isAdmin = false;
        if(strpos($uri, '/admin') === 0) {
            $isAdmin = true;
        }
//        管理员页面
        if ($isAdmin) {
            $templateurl = include(APP_ROOT . '/configs/admin.templateurl.config.php');
            if(!empty($templateurl)) {
                foreach ($templateurl as $pattern => $template) {
                    $matches = self::urlMatch($pattern, $uri);
                    if ($matches !== false) {
                        foreach ($matches as $key => $value) {
                            self::setParam($key, $value);
                        }
                        $_GET['__config__'] = $template;
                        return ['adminPage','_template'];
                    }
                }
            }
            return null;
        }
//        页面
        $templateurl = include(APP_ROOT . '/configs/templateurl.config.php');
        if(!empty($templateurl)) {
            foreach ($templateurl as $pattern => $template) {
                $matches = self::urlMatch($pattern, $uri);
                if ($matches !== false) {
                    foreach ($matches as $key => $value) {
                        self::setParam($key, $value);
                    }
                    $_GET['__config__'] = $template;
                    return ['page','_template'];
                }
            }
        }
    }

    private static function urlMatch($pattern, $uri) {
        $uri = explode('?', $uri)[0];
        if (substr($uri, -1) === '/') {
            $uri = substr($uri, 0, strlen($uri) - 1);
        }
        if (substr($pattern, -1) === '/') {
            $pattern = substr($pattern, 0, strlen($pattern) - 1);
        }
        if ($uri == $pattern) return [];
        $uriPathArr = explode('/', $uri);
        $uriPathDepth = count($uriPathArr);
        if ($uriPathDepth > 5) return false;
        $patternPathArr = explode('/', $pattern);
        $patternPathDepth = count($patternPathArr);
        if ($patternPathDepth !== $uriPathDepth) return false;

        $params = [];
        for ($i = 0; $i < $patternPathDepth; $i++) {
            $uriPath = $uriPathArr[$i];
            $patternPath = $patternPathArr[$i];
            if (strtolower($uriPath) == strtolower($patternPath)) {
                continue;
            } else {
                if (strpos($patternPath, ':') === 0) {
                    $pKey = substr($patternPath, 1);
                    if (substr($patternPath, -3) === '{d}') {
                        if (!is_numeric($uriPath)) {
                            return false;
                        }
                        $pKey = substr($patternPath, 1, -3);
                    }
                    $params[$pKey] = $uriPath;
                    continue;
                } else {
                    return false;
                }
            }
        }
        return $params;
    }

    private function setParam($key, $value) {
        $_GET[$key] = $value;
        $_REQUEST[$key] = $value;
    }
} 