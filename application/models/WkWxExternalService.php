<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/5/3
 * Time: 下午3:41
 */

class WkWxExternalService extends Wk_Singleton{

    const WX_ACCESS_TOKEN_MC_KEY = "wx:1093190221_access_token";
    const WX_JS_TICKET_KEY = "wx:1093190221_js_ticket";

    private $token;
    private $wx_akey;
    private $wx_skey;

    public function __construct() {
        $this->token = Wk::$config['wechat']['TOKEN'];
        $this->wx_akey = Wk::$config['wechat']['WX_AKEY'];
        $this->wx_skey = Wk::$config['wechat']['WX_SKEY'];
    }

    public function checkSignature($signature, $timestamp, $nonce) {
        $token = $this->token;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    public function getBaseAccessToken($forceMc = false) {
        // access_token有访问限制（1天2000次），接口过期时间7200秒，因此设置缓存
        $access_token = Wk::redis()->get(self::WX_ACCESS_TOKEN_MC_KEY);
        Wk::logger()->log("access_token in Mcd: " . $access_token);
        if($access_token == false || $forceMc == true) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->wx_akey.'&secret='.$this->wx_skey;
            curl_setopt($ch, CURLOPT_URL, $url);
            $data = curl_exec($ch);
            if($data === false) {
                throw new Wk_Exception('', TErrorConstants::E_SNS_LOGIN);
            }
            $data = json_decode($data);
            if(!empty($data->errcode)) {
                throw new Wk_Exception('', TErrorConstants::E_SNS_LOGIN);
            }
            if(empty($data->access_token)) {
                throw new Wk_Exception('', TErrorConstants::E_SNS_LOGIN);
            }
            $access_token = $data->access_token;
            Wk::logger()->log("access_token in request: " . $access_token);
            Wk::redis()->set(self::WX_ACCESS_TOKEN_MC_KEY, $access_token, 3600);
        }
        return $access_token;
    }

    // 获取用户基本信息(UnionID机制)
    public function getUserInfoByOpenId($openid) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $access_token = self::getBaseAccessToken();
        $url2 = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        curl_setopt($ch, CURLOPT_URL, $url2);
        $data2 = curl_exec($ch);
        curl_close($ch);
        if($data2 === false) {
            throw new Wk_Exception('', TErrorConstants::E_SNS_LOGIN);
        }
        $data2 = json_decode($data2);

        if(isset($data2->errcode) && $data2->errcode == 40001) {
            $access_token = self::getBaseAccessToken(true);
            $url2 = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_URL, $url2);
            $data2 = curl_exec($ch);
            curl_close($ch);
            if($data2 === false) {
                throw new Wk_Exception('', TErrorConstants::E_SNS_LOGIN);
            }
            $data2 = json_decode($data2);
        }

        if(!empty($data2->errcode)) {
            throw new Wk_Exception('', TErrorConstants::E_SNS_LOGIN);
        }
        if(empty($data2->openid)) {
            throw new Wk_Exception('', TErrorConstants::E_SNS_LOGIN);
        }
        if($data2->subscribe == 0) {
            return ['subscribe' => $data2->subscribe, 'openId' => $data2->openid];
        }
        else {
            return [
                'subscribe' => $data2->subscribe,
                'openId' => $data2->openid,
                'nickname' => $data2->nickname,
                'sex' => $data2->sex,
                'province' => $data2->province,
                'city' => $data2->city,
                'country' => $data2->country,
                'subscribeTime' => $data2->subscribe_time,
                'unionId' => isset($data2->unionid) ? $data2->unionid : 'waka_' . $data2->openid,
                'headImgUrl' => $data2->headimgurl
            ];
        }
    }

    public static function subscribe($fromUsername, $toUsername) {
        $userInfo = self::getUserInfoByOpenId($fromUsername);

        $curUser = WkWechatUserService::getWeChatUserByOpenId($fromUsername);
        if(!isset($curUser) || empty($curUser)) {
            $curUser = new \stdClass();
        }
        if($userInfo['subscribe'] == 1) {
            $curUser->subscribe = 1;
            $curUser->subscribeTime = date("Y-m-d H:i:s", $userInfo['subscribeTime']);
            $curUser->openId = $userInfo['openId'];
            $curUser->unionId = $userInfo['unionId'];
            $curUser->country = $userInfo['country'];
            $curUser->province = $userInfo['province'];
            $curUser->city = $userInfo['city'];
            $curUser->headImgUrl = $userInfo['headImgUrl'];
            $curUser->sex = $userInfo['sex'];
            $curUser->nickname = $userInfo['nickname'];
            WkWechatUserService::saveWeChatUser($curUser);
        }
//        $reply = TAdminWeChatService::getInstance()->getSubscribeReply();
//        to do
        if(!isset($reply)) {
            $content = "";
        }
        else {
            $content = $reply->content;
        }
        $text = self::printTexts($fromUsername, $toUsername, $content);
        return $text;
    }

    public static function unsubscribe($fromUsername) {
        // do something
        $curUser = WkWechatUserService::getWeChatUserByOpenId($fromUsername);
        if(isset($curUser)) {
            $curUser->subscribe = 0;
            WkWechatUserService::saveWeChatUser($curUser);
        }
    }

    private static function printNews($fromUsername, $toUsername, $content, $title, $picurl, $url) {
        $newsTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[%s]]></MsgType>
        <ArticleCount>1</ArticleCount>
        <Articles>
        <item>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
        </item>
        </Articles>
        </xml>";
        $time = time();
        $resultStr = sprintf($newsTpl, $fromUsername, $toUsername, $time, "news", $title, $content, $picurl, $url);
        return $resultStr;
    }

    private static function printTexts($fromUsername, $toUsername, $content) {
        $textTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[%s]]></MsgType>
        <Content><![CDATA[%s]]></Content>
        <FuncFlag>0</FuncFlag>
        </xml>";
        $time = time();
        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, "text", $content);
        return $resultStr;
    }

    private static function printTransferCustomerService($fromUsername, $toUsername, $createTime) {
        $textTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[transfer_customer_service]]></MsgType>
        </xml>";
        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $createTime);
        Wk::logger()->log($resultStr);
        return $resultStr;
    }

    private static function printTransferCertainCustomerService($fromUsername, $toUsername, $createTime, $kfAccount) {
        $textTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[transfer_customer_service]]></MsgType>
        <TransInfo>
            <KfAccount>%s</KfAccount>
        </TransInfo>
        </xml>";
        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $createTime, $kfAccount);
        Wk::logger()->log($resultStr);
        return $resultStr;
    }
}
