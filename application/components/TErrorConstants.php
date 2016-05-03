<?php
class TErrorConstants {
    const E_SNS_LOGIN =5030;
    const E_LOGIN =5001;
    const E_NOT_LOGIN =5002;

//    Register & Login
    const E_REGISTER_WRONG_REPEAT_PASSWORD = 50001;
    const E_REGISTER_WRONG_MOBILE = 50002;
    const E_REGISTER_USER_EXIST = 50003;
    const E_LOGIN_FAIL = 50004;
    const E_SMS_CODE_TOO_FREQUENCY = 50005;

    private static $errorMsg = [
        self::E_SNS_LOGIN=>'第三方授权失败，请稍后重试！',
        self::E_LOGIN => "您已经登录",
        self::E_NOT_LOGIN => "您还没有登录，请先登录",
        self::E_REGISTER_WRONG_REPEAT_PASSWORD => "两次输入的密码不一致，请重新输入",
        self::E_REGISTER_WRONG_MOBILE => "您输入的手机号不合法（暂时只支持中国大陆手机号）",
        self::E_REGISTER_USER_EXIST => "该手机号已经注册过，请直接登录",
        self::E_LOGIN_FAIL => "未注册或您输入的密码不正确",
        self::E_SMS_CODE_TOO_FREQUENCY => "一分钟内不能频繁获取验证码，请稍等"
    ];

    public static function getErrorMsg($code) {
        if(!empty(self::$errorMsg[$code])) return self::$errorMsg[$code];
        return '当前访问人数太多啦，请耐心等待并重试';
    }
}