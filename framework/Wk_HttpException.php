<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/4/25
 * Time: 下午4:33
 */

class Wk_HttpException extends Wk_Exception{
    public $statusCode;
    function __construct($status, $message=null, $code=0) {
        $this->statusCode=$status;
        parent::__construct($message,$code);
    }
    public function getStatus() {
        return $this->statusCode;
    }
} 