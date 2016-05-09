<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/4/25
 * Time: 下午3:38
 */

class Wk_Log {

    public function __construct(array $config) {
        $this->_statPath = empty($config['static_path']) ? null : $config['static_path'];
        $this->_infoPath = empty($config['info_path']) ? null : $config['info_path'];
        $this->_errPath = empty($config['err_path']) ? null : $config['err_path'];
        $this->_rotate = empty($config['rotate']) ? false : $config['rotate'];
        $this->_emailFrom = empty($config['emailFrom']) ? null : $config['emailFrom'];
        $this->_emailTo = empty($config['emailTo']) ? null : $config['emailTo'];
        $this->_emailSubject = empty($config['emailSubject']) ? null : $config['emailSubject'];
    }


    public function s() {
        if (empty($file) && empty($this->_statPath)) return;
        if (empty($file)) $file = $this->_statPath;
        if ($this->_rotate) {
            $file .= '.' . date('Ymd');
        }
        $fp = @fopen($file, 'a');
        @flock($fp, LOCK_EX);
        @fwrite($fp, self::formatSMessage());
        @flock($fp, LOCK_UN);
        @fclose($fp);
    }

    public function err($msg, $file = null) {
        if (!empty($this->_emailFrom) && !empty($this->_emailTo)
            && !empty($this->_emailSubject) && $msg instanceof Exception) {
            if (!($msg instanceof Wk_Exception)) {
                $body = print_r($msg, true);
                $subject = "=?UTF-8?B?" . base64_encode($this->_emailSubject) . "?=";
                $header = "MIME-Version: 1.0" . "\r\n";
                $header .= "Content-type:text/html;charset=utf-8" . "\r\n";
                $header .= 'From: <' . $this->_emailFrom . '>' . "\r\n";
                mail($this->_emailTo, $subject, $body, $header);
            }
            $msg = $msg->getMessage() . $msg->getTraceAsString();
        }
        if ($msg instanceof Exception) {
            $msg = $msg->getMessage() . $msg->getTraceAsString();
        }
        if (empty($file) && empty($this->_errPath)) return;
        if (empty($file)) $file = $this->_errPath;
        if ($this->_rotate) {
            $file .= '.' . date('Ymd');
        }
        error_log(self::formatLogMessage($msg, 'error', true), 3, $file);
    }

    public function log() {

    }

    private static function formatLogMessage($msg, $level, $detail) {
        $prefix = ' [' . $level . ']';
        $prefix .= date('Y-m-d H:i:s ', Wk_Request::getTime());
        $user = Wk::app()->user;
        if (isset($user) && !empty($user->userid) && !empty($user->utoken)) {
            $prefix .= '[userid:' . $user->userid . ']';
            $prefix .= '[usertoken:' . $user->utoken . ']';
        }
        // if(isset($_COOKIE["PHPSESSID"])) {
        //     $prefix .= '[PHPSESSID:'.$_COOKIE["PHPSESSID"].']';
        // }
        if ($detail) {
            $prefix .= $_SERVER['REQUEST_URI'];
            //$prefix .= self::getServerVariable('SERVER_SIGNATURE');
            //$prefix .= self::getServerVariable('QUERY_STRING');
            if (!empty($_POST)) {
                $prefix .= '[POST:';
                foreach ($_POST as $key => $value) {
                    $prefix .= $key . '=' . urlencode($value) . '&';
                }
                $prefix .= ']';
            }
            $prefix .= $_SERVER['HTTP_USER_AGENT'];
            $prefix .= $_SERVER['REMOTE_ADDR'];
            $prefix .= empty($_SERVER['HTTP_REFERER']) ? "" : $_SERVER['HTTP_REFERER'];
            if (isset($_FILES) && !empty($_FILES)) {
                $prefix .= "\n<file>\n" . print_r($_FILES, true) . "\n</file>\n";
            }
        }
        return $prefix . ' ' . $msg . "\n";
    }

    private static function formatSMessage() {
        $prefix = date('Y-m-d H:i:s ', Wk_Request::getTime());
        $user = Wk::app()->user;
        if (isset($user) && !empty($user->userid) && !empty($user->utoken)) {
            $prefix .= '[userid:' . $user->userid . ']';
            $prefix .= '[usertoken:' . $user->utoken . ']';
        }
        if (session_status() == PHP_SESSION_ACTIVE) {
            $prefix .= '[SESSION_ID:' . session_id() . ']';
        }
        if (!empty($_COOKIE['SERVERID'])) {
            $prefix .= '[SERVERID:' . $_COOKIE['SERVERID'] . ']';
        }
        $prefix .= $_SERVER['REQUEST_URI'];

        $referStr = '[REFER:';
        $hasRefer = false;
        $locStr = '[LOC:';
        $hasLoc = false;

        if (!empty($_SERVER['REQUEST_URI'])) {
            $urlArr = parse_url($_SERVER['REQUEST_URI']);
            //$prefix .= "[PATH:{$urlArr['path']}]";
            $getParams = [];
            parse_str($urlArr['query'], $getParams);
            if (!empty($getParams)) {
                //$prefix .= '[GET:';
                foreach ($getParams as $key => $value) {
//                    if (!in_array($key, ['v','vc','vd','token','timestr','sign','lang'])) {
//                        $prefix .= "$key=$value&";
//                    }
                }
                //$prefix .= ']';
            }
        }
        $prefix .= " ";
//        if (isset(K::app()->getController()) && !empty(K::app()->getController()->appParam)) {
//            $prefix .= '[APP_PARAM:';
//            foreach (K::app()->getController()->appParam as $key => $value) {
//                $prefix .= "$key=$value&";
//            }
//            $prefix .= ']';
//        }
        //$prefix .= self::getServerVariable('SERVER_SIGNATURE');
        //$prefix .= self::getServerVariable('REQUEST_URI');
        //$prefix .= self::getServerVariable('QUERY_STRING');
        if (!empty($_POST)) {
            $prefix .= '[POST:';
            foreach ($_POST as $key => $value) {
                if (in_array($key, ['refer','id1','id2'])) {
                    $hasRefer = true;
                    $referStr .= "$key=$value&";
                    continue;
                }
                $prefix .= $key . '=' . urlencode($value) . '&';
            }
            $prefix .= ']';
        }
        $referStr .= ']';
        $locStr .= ']';
        if ($hasRefer) {
            $prefix .= $referStr;
        }
        if ($hasLoc) {
            $prefix .= $locStr;
        }
        $prefix .= $_SERVER['HTTP_USER_AGENT'];
        $prefix .= $_SERVER['REMOTE_ADDR'];
        $prefix .= $_SERVER['HTTP_REFERER'];
//        $prefix .= '[NETCOUNT:db('.Wk::db()->getExecuteCnt().'),mc('.K::mcd()->getUseCnt().'),redis('.K::redis()->getUseCnt().'),tbapi('.TB_BaseSrv::getUseCnt().'),trapi('.TRoad_BaseSrv::getUseCnt().'),solr('.K::solr()->getUseCnt().')]';
        if (isset($_SERVER["REQUEST_TIME_FLOAT"])) {
            $prefix .= '[WAITING:' . (round(Wk::app()->startTime - $_SERVER["REQUEST_TIME_FLOAT"], 8)*1000) . 'ms]';
            $prefix .= '[DURATION:' . (round(microtime(true) - Wk::app()->startTime, 8)*1000) . 'ms]';
        }
        return $prefix . "\n";
    }
} 