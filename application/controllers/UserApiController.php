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
            "@" => ["bindMobile", "submitLogout"],
            "?" => ["submitLogin", "submitWxLogin", "submitRegister", ],
        ];
    }

    /**
     * 手机号登录
     * @return Wk_User
     * @throws Wk_Exception
     */
    public function submitLoginAction() {
        $mobile = Wk_Request::getGetString("mobile", null, false);
        $password = Wk_Request::getGetString("password", null, false);
        $user = WkUserService::getInstance()->submitLoginWithMobile($mobile, $password);
        if (session_status() == PHP_SESSION_ACTIVE) {
            $appParam = [];
            $appParam['token'] = $user->token;
            $_SESSION['appParam'] = $appParam;
        }
        return $user;
    }

    /**
     * 微信登录
     * @return Wk_User
     * @throws Wk_Exception
     */
    public function submitWxLoginAction() {
        $openId = Wk_Request::getRequestString("openId", null, false);
        $user = WkUserService::getInstance()->submitLoginWithWechat($openId);
        if (session_status() == PHP_SESSION_ACTIVE) {
            $appParam = [];
            $appParam['token'] = $user->token;
            $_SESSION['appParam'] = $appParam;
        }
        return $user;
    }

    /**
     * 手机号注册
     * @return int
     * @throws Wk_Exception
     */
    public function submitRegisterAction() {
        $mobile = Wk_Request::getGetString("mobile", null, false);
        $password = Wk_Request::getGetString("password", null, false);
        $rePassword = Wk_Request::getGetString("rePassword", null, false);
//        手机验证码
        $code = Wk_Request::getGetString("code", null, false);

//        验证码
        if(!WkSmsCodeService::getInstance()->verifyCode($mobile, $code)) {
            throw new Wk_Exception("请输入正确的验证码", -1);
        }
        return WkUserService::getInstance()->submitRegisterByMobile($mobile, $password, $rePassword);
    }

    /**
     * 登出（移动端手机号登录用户）
     */
    public function submitLogoutAction() {
        WkUserService::getInstance()->logout($this->curToken);
        if (session_status() == PHP_SESSION_ACTIVE) {
            unset($_SESSION['appParam']);
        }
    }

    /**
     * 用户绑定手机号（微信登录）
     * @return Wk_User
     * @throws Wk_Exception
     */
    public function bindMobileAction() {
        $mobile = Wk_Request::getGetString("mobile", null, false);
        $code = Wk_Request::getGetString("code", null, false);

        if(!WkSmsCodeService::getInstance()->verifyCode($mobile, $code)) {
            throw new Wk_Exception("请输入正确的验证码", -1);
        }
        return WkUserService::getInstance()->bindMobile($mobile, $this->curUser, false);
    }
}