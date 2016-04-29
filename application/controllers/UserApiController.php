<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/4/26
 * Time: 下午3:17
 */

class UserApiController extends ApiController{
    public function access() {
        return [
            "@" => [],
            "?" => ["submitLogin", "submitRegister"],
        ];
    }

    public function submitLoginAction() {
        $mobile = Wk_Request::getGetString("mobile", null, false);
        $password = Wk_Request::getGetString("password", null, false);
        return WkUserService::getInstance()->submitLoginWithMobile($mobile, $password);
    }

    public function submitRegisterAction() {
        $mobile = Wk_Request::getGetString("mobile", null, false);
        $password = Wk_Request::getGetString("password", null, false);
        $rePassword = Wk_Request::getGetString("rePassword", null, false);
//        手机验证码
        $code = Wk_Request::getGetString("code", null, false);
        return WkUserService::getInstance()->submitRegisterWithMobile($mobile, $password, $rePassword, $code);
    }
}