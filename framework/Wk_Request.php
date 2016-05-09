<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/4/25
 * Time: 下午3:29
 */

class Wk_Request {
    public static $get;

    public static $post;

    public static $request;

    private static $_mobileDetect;
    private static $_isMobile;
    private static $_isAjax;

    private static function mobileDetect() {
        if (!isset(self::$_mobileDetect)) {
            self::$_mobileDetect = new Mobile_Detect();
        }
        return self::$_mobileDetect;
    }

    /**
     * @return bool
     */
    public static function isMobile() {
        if (!isset(self::$_isMobile)) {
            self::$_isMobile = self::mobileDetect()->isMobile();
        }
        return self::$_isMobile;
    }

    /**
     * @return bool
     */
    public static function isAjax() {
        if (!isset(self::$_isAjax)) {
            self::$_isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        }
        return self::$_isAjax;
    }

    public static function redirect($uri) {
        if(!headers_sent()) {
            header('Location: ' . $uri);
        }
        die;
    }

    /**
     * @return string
     */
    public static function getClientIp() {
        if (getenv('HTTP_CLIENT_IP')) {
            $clientIp = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $clientIp = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('X-FORWARDED-FOR')) {
            $clientIp = getenv('X-FORWARDED-FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $clientIp = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $clientIp = getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
            $clientIp = getenv('HTTP_FORWARDED');
        } elseif (getenv('REMOTE_ADDR')) {
            $clientIp = getenv('REMOTE_ADDR');
        } else {
            $clientIp = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        }
        return $clientIp;
    }

    public static function startSession($domain) {
        if (session_status() == PHP_SESSION_NONE) {
            $session_name = session_name();
            if (isset($_REQUEST[$session_name])) {
                session_id($_REQUEST[$session_name]);
                $domain = '';
            }
            ini_set('session.gc_probability', 1);
            ini_set('session.gc_divisor', 100);
            ini_set('session.cookie_lifetime', 2592000);
            ini_set('session.gc_maxlifetime', 2592000);
            ini_set('session.cookie_domain', $domain);
            //$sessionHandler = new K_MemcachedSessionHandler();
            $sessionHandler = new Wk_RedisSessionHandler();
            /** @noinspection PhpParamsInspection */
            session_set_save_handler($sessionHandler);
            session_start();
        }
    }

    private static function getRequestStringParam($method, $param, $defaultValue = null, $allowEmpty = true) {
        $value = empty(self::${$method}[$param]) ? $defaultValue : trim(self::${$method}[$param]);
        if (!$allowEmpty && empty($value))
            throw new Wk_Exception($param . ' is empty', -1);
        return !empty($value) ? $value : $defaultValue;
    }

    /**
     * @param string $param
     * @param null $defaultValue
     * @param bool $allowEmpty
     * @throws K_Exception
     * @return null|string
     */
    public static function getGetString($param, $defaultValue = null, $allowEmpty = true) {
        return self::getRequestStringParam('get', $param, $defaultValue, $allowEmpty);
    }

    /**
     * @param string $param
     * @param null $defaultValue
     * @param bool $allowEmpty
     * @throws K_Exception
     * @return null|string
     */
    public static function getPostString($param, $defaultValue = null, $allowEmpty = true) {
        return self::getRequestStringParam('post', $param, $defaultValue, $allowEmpty);
    }

    /**
     * @param string $param
     * @param null $defaultValue
     * @param bool $allowEmpty
     * @throws K_Exception
     * @return null|string
     */
    public static function getRequestString($param, $defaultValue = null, $allowEmpty = true) {
        return self::getRequestStringParam('request', $param, $defaultValue, $allowEmpty);
    }

    private static function getRequestNumberParam($method, $type, $param, $defaultValue = null, $allowEmpty = true) {
        $value = !isset(self::${$method}[$param]) ? $defaultValue : trim(self::${$method}[$param]);
        if (!$allowEmpty && $value == '') throw new Wk_Exception($param . ' is empty', -1);
        if (!empty($value) && !is_numeric($value)) throw new Wk_Exception($param . ' is not a number', -1);
        $numFunc = $type . 'val';
        $value = $numFunc($value);
        return $value;
    }

    /**
     * @param string $param
     * @param null $defaultValue
     * @param bool $allowEmpty
     * @throws K_Exception
     * @return int|null
     */
    public static function getGetInt($param, $defaultValue = null, $allowEmpty = true) {
        return self::getRequestNumberParam('get', 'int', $param, $defaultValue, $allowEmpty);
    }


    /**
     * @param string $param
     * @param null $defaultValue
     * @param bool $allowEmpty
     * @throws K_Exception
     * @return int|null
     */
    public static function getPostInt($param, $defaultValue = null, $allowEmpty = true) {
        return self::getRequestNumberParam('post', 'int', $param, $defaultValue, $allowEmpty);
    }

    /**
     * @param string $param
     * @param null $defaultValue
     * @param bool $allowEmpty
     * @throws K_Exception
     * @return int|null
     */
    public static function getRequestInt($param, $defaultValue = null, $allowEmpty = true) {
        return self::getRequestNumberParam('request', 'int', $param, $defaultValue, $allowEmpty);
    }

    /**
     * @param string $param
     * @param null $defaultValue
     * @param bool $allowEmpty
     * @throws K_Exception
     * @return float|string
     */
    public static function getGetFloat($param, $defaultValue = null, $allowEmpty = true) {
        return self::getRequestNumberParam('get', 'float', $param, $defaultValue, $allowEmpty);
    }

    /**
     * @param string $param
     * @param null $defaultValue
     * @param bool $allowEmpty
     * @throws K_Exception
     * @return float|string
     */
    public static function getPostFloat($param, $defaultValue = null, $allowEmpty = true) {
        return self::getRequestNumberParam('post', 'float', $param, $defaultValue, $allowEmpty);
    }

    /**
     * @param string $param
     * @param null $defaultValue
     * @param bool $allowEmpty
     * @throws K_Exception
     * @return float|string
     */
    public static function getRequestFloat($param, $defaultValue = null, $allowEmpty = true) {
        return self::getRequestNumberParam('request', 'float', $param, $defaultValue, $allowEmpty);
    }

    /**
     * @return int
     */
    public static function getTime() {
        if (!(Wk::app() instanceof WebApp)) {
            return time();
        }
        return $_SERVER['REQUEST_TIME'];
    }

} 