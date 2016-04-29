<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/4/25
 * Time: 下午4:18
 */

class Wk_Redis extends Redis{
    private $_useCnt = 0;

    /**
     * @param string $key
     * @return bool|string
     */
    public function get($key) {
        try {
            $this->_useCnt++;
            return parent::get($key);
        } catch (Exception $e) {
            Wk::logger()->err($e->getMessage());
        }
        return false;
    }

    /**
     * @param $keys
     * @return array|bool
     */
    public function getMulti($keys) {
        try {
            $this->_useCnt++;
            return parent::getMultiple($keys);
        } catch (Exception $e) {
            Wk::logger()->err($e->getMessage());
        }
        return false;
    }

    /**
     * @param string $key
     * @return int
     */
    public function incr($key) {
        try {
            $this->_useCnt++;
            return parent::incr($key);
        } catch (Exception $e) {
            Wk::logger()->err($e->getMessage());
        }
        return 0;
    }

    /**
     * @param string $key
     * @param string $value = 1
     * @return int
     */
    public function incrBy($key, $value=1) {
        try {
            $this->_useCnt++;
            return parent::incrBy($key, $value);
        } catch (Exception $e) {
            Wk::logger()->err($e->getMessage());
        }
        return 0;
    }


    /**
     * @param string $key
     * @param int $ttl
     * @return bool
     */
    public function expire($key, $ttl) {
        $this->_useCnt++;
        return $this->_wrapBoolReturn(function() use ($key, $ttl){
            return parent::expire($key, $ttl);
        });
    }

    /**
     * @param $key1
     * @param null $key2
     * @param null $key3
     * @return bool|void
     */
    public function delete($key1, $key2 = null, $key3 = null) {
        try {
            $this->_useCnt++;
            parent::delete($key1, $key2, $key3);
        } catch (Exception $e) {
            Wk::logger()->err($e->getMessage());
        }
    }

    /**
     * @param array|int $key1
     * @param null $key2
     * @param null $key3
     * @return int
     */
    public function del($key1, $key2 = null, $key3 = null) {
        try {
            $this->_useCnt++;
            return parent::del($key1, $key2, $key3);
        } catch (Exception $e) {
            Wk::logger()->err($e->getMessage());
        }
        return 0;
    }

    /**
     * @param string $key
     * @param string $value
     * @param int $timeout
     * @return bool
     */
    public function set($key, $value, $timeout = 0) {
        $this->_useCnt++;
        return $this->_wrapBoolReturn(function() use ($key, $value, $timeout){
            return parent::set($key, $value, $timeout);
        });
    }

    /**
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function setnx($key, $value) {
        $this->_useCnt++;
        return $this->_wrapBoolReturn(function() use ($key, $value) {
            return parent::setnx($key, $value);
        });
    }

    /**
     * @param string $key
     * @return int
     */
    public function decr($key) {
        try {
            $this->_useCnt++;
            return parent::decr($key);
        } catch (Exception $e) {
            Wk::logger()->err($e->getMessage());
        }
        return 0;
    }

    /**
     * @param string $key
     * @param int $ttl
     * @param string $value
     * @return bool
     */
    public function setex($key, $ttl, $value) {
        $this->_useCnt++;
        return $this->_wrapBoolReturn(function() use ($key, $ttl, $value) {
            return parent::setex($key, $ttl, $value);
        });
    }

    /**
     * @param string $key
     * @param string $value
     * @return int
     */
    public function lPush($key, $value){
        $this->_useCnt++;
        return $this->_wrapBoolReturn(function() use ($key, $value) {
            return parent::lPush($key, $value);
        });
    }

    /**
     * @param string $key
     * @return string
     */
    public function lPop($key){
        $this->_useCnt++;
        return $this->_wrapBoolReturn(function() use ($key) {
            return parent::lPop($key);
        });
    }

    /**
     * @param string $key
     * @return int
     */
    public function ttl($key){
        $this->_useCnt++;
        return $this->_wrapBoolReturn(function() use ($key) {
            return parent::ttl($key);
        });
    }

    /**
     * @param $function
     * @return bool
     */
    private function _wrapBoolReturn($function) {
        try {
            $success = $function();
            if (!$success) {
                $errMsg = self::getLastError();
                if (!isset($errMsg)) $errMsg = 'unknown redis error';
                try {
                    throw new Exception($errMsg);
                } catch (Exception $e) {
                    Wk::logger()->err($e);
                }
            }
            return $success;
        } catch (Exception $e) {
            Wk::logger()->err($e);
        }
        return false;
    }

    public function getUseCnt() {
        return $this->_useCnt;
    }
} 