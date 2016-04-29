<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/4/25
 * Time: 下午3:35
 */

date_default_timezone_set('Asia/Shanghai');
define('APP_ROOT', __DIR__ . '/../application');

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__);


spl_autoload_register(function($class){
    @include $class . '.php';
});

function catchFatalError() {
    $error = error_get_last();
    if (empty($error)) return;
    $ignore = E_WARNING | E_NOTICE | E_USER_WARNING | E_USER_NOTICE | E_STRICT | E_DEPRECATED | E_USER_DEPRECATED;
    if (($error['type'] & $ignore) == 0) {
        // handle the error - but DO NOT THROW ANY EXCEPTION HERE.
        Wk::logger()->err('Fatal error: ' . print_r($error, true));
        die;
    }
}