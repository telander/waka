<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/5/3
 * Time: 下午5:56
 */

class SmsCodeApiController extends ApiController {
    /**
     * 发送手机验证码
     * @throws Wk_Exception
     */
    public function sendAction() {
        $mobile = Wk_Request::getGetString("mobile", null, false);
        WkSmsCodeService::getInstance()->sendCode($mobile, isset($this->curUser)?$this->curUser->userid:0);
    }
} 