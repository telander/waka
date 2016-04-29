<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/4/25
 * Time: 下午6:10
 */

class WkUserService extends Wk_Service{

    const PASSWORD_SALT = "waka_win";

    public function getUserByToken($token) {

    }

    /**
     * @param  $user
     */
    public function setLoginCookie($user) {
        $_COOKIE['WAKAUID'] = md5($user->userid);
        setcookie('WAKAUID', $_COOKIE['WAKAUID'], 0, '/', TZLS_DOMAIN);
    }

    public function submitLoginWithMobile($mobile, $password) {

    }

} 