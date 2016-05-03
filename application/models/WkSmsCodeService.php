<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/5/3
 * Time: 下午5:35
 */

class WkSmsCodeService extends Wk_Service{

    const CACHEKEY = "wk:smsCodeService:frequency?mobile=";
    const CACHE_RANDSTR_KEY = "wk:smsCodeService:mobile=";

    public static function sendCode($mobile, $userid = 0) {
        $key = self::CACHEKEY . $mobile;
        $value = Wk::redis()->get($key);
        if($value !== false) {
            throw new Wk_Exception("", TErrorConstants::E_SMS_CODE_TOO_FREQUENCY);
        }
        if(WAKA_ENV == "production")
            $randstr = mt_rand(10000, 99999);
        else
            $randstr = "12345";

//      to do 发短信

        Wk::redis()->set($key, $randstr, 60);
        $mobileVerify = new TAR_MobileVerify();
        $mobileVerify->mobile = $mobile;
        $mobileVerify->userid = 0;
        $mobileVerify->randstr = $randstr;
        $mobileVerify->state = 0;
        $mobileVerify->expire = date("Y-m-d H:i:s", time() + 5 * 60);
        $mobileVerify->save();
    }

    public static function verifyCode($mobile, $code) {
        $mobileVerify = TAR_MobileVerify::findOne("where mobile=? and randstr = ? and expire >= ? order by id desc", "sss", $mobile, $code, date("Y-m-d H:i:s"));
        if(isset($mobileVerify) && !empty($mobileVerify)) {
            return true;
        }
        return false;
    }
} 