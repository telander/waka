<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/4/25
 * Time: 下午3:19
 */

abstract class AbstractApp {

    /**
     * @var Wk_WebUser
     */
    public $user;

    final public function __construct() {
        if(!empty(Wk::$config['loadPath'])) {
            set_include_path(get_include_path() . PATH_SEPARATOR . implode(PATH_SEPARATOR, Wk::$config['loadPath']));
        }

        spl_autoload_register([$this, '_autoload']);
    }

    abstract public function run();

    public function stop() {
        die;
    }

    private function _autoload($class) {
        @include_once $class . ".php";
    }
} 