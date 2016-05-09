<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/5/6
 * Time: 下午5:25
 */

class AdminUserApiController extends AdminApiController{

    public function access() {
        return [
            "@" => ['submitAdminLogout'],
            "?" => ["submitAdminLogin", "submitAdminRegister"],
        ];
    }

    /**
     * 管理员登录
     * @apiMethod post
     * @apiParam string mobile 手机号
     * @apiParam string password 密码
     * @return Wk_AdminUser
     */
    public function submitAdminLoginAction() {
        $mobile = Wk_Request::getRequestString("mobile", null, false);
        $password = Wk_Request::getRequestString("password", null, false);

        $user = WkAdminUserService::getInstance()->submitAdminUserLogin($mobile, $password);

        if (session_status() == PHP_SESSION_ACTIVE) {
            $_SESSION['user'] = $user;
        }

        return $user;
    }

    /**
     * 管理员退出登录
     */
    public function submitAdminLogoutAction() {
        if(session_status() == PHP_SESSION_ACTIVE) {
            unset($_SESSION['user']);
        }
    }


    /**
     * 管理员注册(内部接口)
     * @apiMethod post
     * @apiParam string mobile 手机号
     * @apiParam string password 密码
     * @apiParam string rePassword 密码确认
     * @return array
     * @throws Wk_Exception
     */
    public function submitAdminRegisterAction() {
        $mobile = Wk_Request::getRequestString("mobile", null, false);
        $password = Wk_Request::getRequestString("password", null, false);
        $rePassword = Wk_Request::getRequestString("rePassword", null, false);

        if($password != $rePassword) {
            throw new Wk_Exception("管理员注册两次密码不一致，请重新输入", -1);
        }

        $userid = WkAdminUserService::getInstance()->submitAdminUserRegister($mobile, $password);
        return ['userid' => $userid];
    }
} 