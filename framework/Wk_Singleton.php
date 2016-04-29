<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/4/25
 * Time: 下午4:21
 */

class Wk_Singleton {
    private static $instanceStack = [];

    protected function __construct() {
    }

    /**
     * @return $this
     */
    final public static function getInstance() {
        $class = get_called_class();
        if (!isset(self::$instanceStack[$class])) {
            self::$instanceStack[$class] = new $class();
        }
        return self::$instanceStack[$class];
    }

    final private function __clone() {
    }
} 