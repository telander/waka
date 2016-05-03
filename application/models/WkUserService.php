<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/4/25
 * Time: 下午6:10
 */

class WkUserService extends Wk_Service{

    const PASSWORD_SALT = "waka_win";

    /**
     * 根据token登录
     * @param $token
     * @return Wk_User
     */
    public function getUserByToken($token) {
        $tokenInfo = TAR_Usertoken::findOne("where token = ? and is_deleted = 0", "s", $token);
        if(!isset($tokenInfo) || empty($tokenInfo)) {
            return null;
        }
        $user = TAR_User::findById($tokenInfo->userId);
        $retUser = new Wk_User();
        $retUser->mobile = $user->mobile;
        $retUser->city = $user->city;
        $retUser->province = $user->province;
        $retUser->avatarUrl = $user->headImgUrl;
        $retUser->nickname = $user->nickname;
        $retUser->userid = $user->id;
        $retUser->token = $token;
        return $retUser;
    }

    /**
     * 设置登录cookie
     * @param  $user
     */
    public function setLoginCookie($user) {
        $_COOKIE['WAKAUID'] = md5($user->userid);
        setcookie('WAKAUID', $_COOKIE['WAKAUID'], 0, '/', TZLS_DOMAIN);
    }

    /**
     * 手机号注册
     * @param $mobile
     * @param $password
     * @param $rePassword
     * @return int
     * @throws Wk_Exception
     */
    public function submitRegisterByMobile($mobile, $password, $rePassword) {
        if(mb_strlen($password) < 8) {
            throw new Wk_Exception("", -1);
        }
        if($password != $rePassword) {
            throw new Wk_Exception("", TErrorConstants::E_REGISTER_WRONG_REPEAT_PASSWORD);
        }
        if(preg_match('/^1[0-9]{10}$/', $mobile) === false) {
            throw new Wk_Exception("", TErrorConstants::E_REGISTER_WRONG_MOBILE);
        }
        $exist = TAR_User::count("where mobile = ?", "s", $mobile) > 0;
        if($exist) {
            throw new Wk_Exception("", TErrorConstants::E_REGISTER_USER_EXIST);
        }
        $user = new TAR_User();
        $user->mobile = $mobile;
        $user->password = md5(self::PASSWORD_SALT . md5($password . self::PASSWORD_SALT));
        $user->createTime = date("Y-m-d H:i:s");
//        手机号渠道注册
        $user->source = 0;
        $user->save();
        return $user->id;
    }

    /**
     * 手机号登录
     * @param $mobile
     * @param $password
     * @return Wk_User
     * @throws Wk_Exception
     */
    public function submitLoginWithMobile($mobile, $password) {
        $signPassword = md5(self::PASSWORD_SALT . md5($password . self::PASSWORD_SALT));
        $user = TAR_User::findOne("where mobile=? and password=?", "ss", $mobile, $signPassword);
        if(!isset($user)) {
            throw new Wk_Exception("", TErrorConstants::E_LOGIN_FAIL);
        }
        $firstLogin = false;
        if(empty($user->lastLoginTime)) {
            $firstLogin = true;
        }
        $user->lastLoginTime = date("Y-m-d H:i:s");
        $user->lastLoginIp = WkUtils::getClientIp();

        $token = new TAR_Usertoken();
        $token->token = md5(md5($user->id . time() . mt_srand())  . self::PASSWORD_SALT);
        $token->userId = $user->id;
        $token->expire = date("Y-m-d H:i:s", time() + 86400 * 14);
        $token->logintype = "mobile";
        $token->save();
        $user->save();
        $retUser = new Wk_User();
        $retUser->mobile = $user->mobile;
        $retUser->city = $user->city;
        $retUser->province = $user->province;
        $retUser->avatarUrl = $user->headImgUrl;
        $retUser->nickname = $user->nickname;
        $retUser->userid = $user->id;
        $retUser->token = $token->token;
        return $retUser;
    }

    /**
     * 微信登录
     * @param $openId
     * @return Wk_User
     * @throws Wk_Exception
     */
    public function submitLoginWithWechat($openId) {
        $firstLogin = false;
        $wechatUserInfo = TAR_WechatUser::findOne("where open_id = ?", "s", $openId);
        if(!isset($wechatUserInfo) || empty($wechatUserInfo)) {
            throw new Wk_Exception("", TErrorConstants::E_SNS_LOGIN);
        }
        $user = TAR_User::findOne("where open_id = ?", "s", $openId);
        if(!isset($user) || empty($user)) {
            $firstLogin = true;
            $user = new TAR_User();
        }

        $user->headImgUrl = $wechatUserInfo->headImgUrl;
        $user->nickname = $wechatUserInfo->nickname;
        $user->createTime = date("Y-m-d H:i:s");
        $user->mobile = "";
//        微信渠道注册
        $user->source = 1;
        $user->city = $wechatUserInfo->city;
        $user->province = $wechatUserInfo->province;
        $user->lastLoginIp = WkUtils::getClientIp();
        $user->lastLoginTime = date("Y-m-d H:i:s");
        $user->save();

        $tokenstring = md5(md5($user->id . $user->openId . date("Y-m")) . self::PASSWORD_SALT);
        $token = TAR_Usertoken::findOne("where token = ? and is_deleted = 0", "s", $tokenstring);
        if(!isset($token) || empty($token)) {
            $token = new TAR_Usertoken();
        }
        $token->token = $tokenstring;
        $token->userId = $user->id;
        $token->expire = "0000-00-00 00:00:00";
        $token->logintype = "wechat";
        $token->save();

        $retUser = new Wk_User();
        $retUser->mobile = $user->mobile;
        $retUser->city = $user->city;
        $retUser->province = $user->province;
        $retUser->avatarUrl = $user->headImgUrl;
        $retUser->nickname = $user->nickname;
        $retUser->userid = $user->id;
        $retUser->token = $token->token;
        return $retUser;
    }

    public function logout($token) {
        $tokenInfo = TAR_Usertoken::findOne("where token = ? and is_deleted = 0", "s", $token);
        if(isset($tokenInfo)) {
            $tokenInfo->delete();
        }
    }

    public function bindMobile($mobile, Wk_User $curUser, $hasBind = false) {
        $user = TAR_User::findById($curUser->userid);
        if(!isset($user) || empty($user)) {
            throw new Wk_Exception("", -1);
        }
        if(!$hasBind) {
            $user->mobile = $mobile;
            $user->save();
        }
        $curUser->mobile = $mobile;
        return $curUser;
    }

} 