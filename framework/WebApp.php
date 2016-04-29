<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/4/25
 * Time: 下午3:18
 */

class WebApp extends AbstractApp{

    public $startTime;

    public $controller;

    public $action;

    public function run() {
        Wk_Request::$get = $_GET;
        Wk_Request::$post = $_POST;
        Wk_Request::$request = $_REQUEST;
        $routerParser = Wk::$config['router'];
        $router = new $routerParser;
        list($controllerName, $actionName) = $router->parse();
        if (empty($controllerName) || empty($actionName)) {
            $router = new Wk_DefaultRouterParser();
            list($controllerName, $actionName) = $router->parse();
        }
        self::route($controllerName, $actionName);
        die;
    }

    public function stop() {
        Wk::logger()->s();
        die;
    }

    private function route($controllerName, $actionName) {
        $controllerClass = ucfirst($controllerName . 'Controller');
        if (class_exists($controllerClass)) {
            /** @var K_Controller $runC */
            $runC = new $controllerClass($actionName);
            $this->controller = $runC;
            $runC->run($actionName);
        } else {
            if (Wk_Request::isAjax()) {
            } else {
                Wk_Request::redirect('/');
            }
        }
        $this->stop();
    }
}