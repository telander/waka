<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/4/25
 * Time: 下午3:16
 */

class Wk {
    public static $config;

    private static $componentStack = [];

    public static function app() {
        return self::$componentStack['app'];
    }


    public static function createWebApp($config, $startTime) {
        self::$config = $config;
        self::$componentStack['app'] = new WebApp();
        return self::$componentStack['app'];
    }

    public static function logger() {
        self::$componentStack['logger'] = new Wk_Log(self::$config['log']);
        return self::$componentStack['logger'];
    }

    public static function db($db = "default") {
        $hash = 'wk_db_' . $db;
        if (!isset(self::$componentStack[$hash])) {
            if (empty(self::$config['db']) || empty(self::$config['db'][$db])) {
                Wk::logger()->err('no db found');
                throw new Wk_Exception('', -1);
            }
            self::$componentStack[$hash] = new Wk_MySQLi(self::$config['db'][$db]);
        }
        return self::$componentStack[$hash];
    }

    public static function redis($redis = "default") {
        $hash = 'wk_redis_' . $redis;
        if (!isset(self::$componentStack[$hash])) {
            if (empty(self::$config['redis']) || empty(self::$config['redis'][$redis])) {
                return new Wk_Redis();
            }
            $host = self::$config['redis'][$redis]['host'];
            $port = self::$config['redis'][$redis]['port'];
            $_redis = new Wk_Redis();
            try {
                $success = $_redis->pconnect($host,$port);
            } catch (Exception $e) {
                Wk::logger()->err($e);
                return $_redis;
            }
            if (!$success) {
                Wk::logger()->err("connect to redis $host:$port return false: ".$_redis->getLastError());
                return $_redis;
            }
            self::$componentStack[$hash] = $_redis;
        }
        return self::$componentStack[$hash];
    }
} 