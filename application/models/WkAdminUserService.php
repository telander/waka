<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/5/6
 * Time: 下午5:32
 */

class WkAdminUserService extends Wk_Service {
    const ADMIN_PASSWORD_SALT = "waka_admin_wawaka";


    /**
     * @param $mobile
     * @param $password
     * @return Wk_AdminUser
     * @throws Wk_Exception
     */
    public function submitAdminUserLogin($mobile, $password) {
        $password = md5(md5(self::ADMIN_PASSWORD_SALT . $password) . self::ADMIN_PASSWORD_SALT);

        $user = TAR_AdminUser::findOne("where mobile = ? and password = ? and is_deleted = 0", "ss", $mobile, $password);

        if(!isset($user) && empty($user)) {
            throw new Wk_Exception("管理员账号不存在", -1);
        }

        $user->lastLoginTime = date("Y-m-d H:i:s");
        $user->lastLoginIp = WkUtils::getClientIp();
        $user->save();

        $retUser = new Wk_AdminUser();
        $retUser->id = $user->id;
        $retUser->mobile = $user->mobile;
        $retUser->nickname = "nick" . $user->mobile;

        return $retUser;
    }

    public function submitAdminUserRegister($mobile, $password) {
        $password = md5(md5(self::ADMIN_PASSWORD_SALT . $password) . self::ADMIN_PASSWORD_SALT);
        $user = TAR_AdminUser::findOne("where mobile = ? and is_deleted = 0", "s", $mobile);
        if(isset($user) && !empty($user)) {
            throw new Wk_Exception("对不起，该手机已经注册过", -1);
        }
        $user = new TAR_AdminUser();
        $user->mobile = $mobile;
        $user->password = $password;
        $user->createTime = date("Y-m-d H:i:s");
        $user->save();
        return $user->id;
    }
}