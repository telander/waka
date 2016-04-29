<?php
class TErrorConstants {
    const E_SNS_LOGIN =5030;
    const E_LOGIN =5001;
    const E_NOT_LOGIN =5002;

    private static $errorMsg = [
        self::E_SNS_LOGIN=>'第三方授权失败，请稍后重试！',
        self::E_LOGIN => "您已经登录",
        self::E_NOT_LOGIN => "您还没有登录，请先登录",
    ];

    public static function getErrorMsg($code) {
        if(!empty(self::$errorMsg[$code])) return self::$errorMsg[$code];
        return '当前访问人数太多啦，请耐心等待并重试';
    }
}