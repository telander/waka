<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/4/25
 * Time: 下午4:17
 */

class Wk_RedisSessionHandler implements SessionHandlerInterface {

    private $savePath;
    private $sessionName;
    private static $lifetime = 0;

    public function open($savePath, $sessionName)
    {
        $this->savePath = $savePath;
        $this->sessionName = $sessionName;
        // if (!is_dir($this->savePath)) {
        //     mkdir($this->savePath, 0777);
        // }
        self::$lifetime = intval(ini_get('session.gc_maxlifetime'));

        return true;
    }

    public function close()
    {
        return true;
    }

    public function read($id)
    {
        $redKey = 'wk:session:'.$id;
        return Wk::redis()->get($redKey);
    }

    public function write($id, $data)
    {
        $redKey = 'wk:session:'.$id;
//        return Wk::redis()->set($redKey, $data, self::$lifetime);
        return Wk::redis()->setex($redKey, self::$lifetime, $data);
    }

    public function destroy($id)
    {
        $redKey = 'wk:session:'.$id;
        $n = Wk::redis()->del($redKey);
        if ($n == 1) return true;
        return false;
    }

    public function gc($maxlifetime)
    {
        return true;
    }
} 