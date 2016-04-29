<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/4/25
 * Time: 下午4:02
 */

abstract class Wk_Controller {
    public $controllerName;
    public $actionName;

    /**
     * @return bool
     */
    public function beforeAction() {
        return true;
    }

    abstract public function run($actionName);

} 