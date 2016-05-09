<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/5/3
 * Time: 下午6:22
 */

class SnsApiController  extends ApiController{
    /**
     * 获取open_id
     * @return array
     * @throws Wk_Exception
     */
    public function getWeChatOpenIdAction() {
        $code = Wk_Request::getRequestString('code',null,false);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.Wk::$config['wechat']['WX_AKEY'].'&secret='.Wk::$config['wechat']['WX_SKEY'].'&code='.$code.'&grant_type=authorization_code';
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
        $expires_in = $data->expires_in;
        $refresh_token = $data->refresh_token;
        $openid = $data->openid;
        $scope = $data->scope;
        $_SESSION['weChatOpenId'] = $openid;
        return ['openid'=>$openid];
    }

    /**
     * 获取微信openid，用户信息(snsapi_userinfo授权)
     * @apiMethod get|post
     * @apiParam string code 微信授权code
     * @return array
     * @throws Wk_Exception
     */
    public function getWeChatOpenIdUserInfoAction() {
        $code = Wk_Request::getRequestString('code',null,false);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.Wk::$config['wechat']['WX_AKEY'].'&secret='.Wk::$config['wechat']['WX_SKEY'].'&code='.$code.'&grant_type=authorization_code';
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
        $expires_in = $data->expires_in;
        $refresh_token = $data->refresh_token;
        $openid = $data->openid;
        $scope = $data->scope;
        $unionid = $data->unionid;

        $url2 = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid."&lang=zh_CN";
        curl_setopt($ch, CURLOPT_URL, $url2);
        $data2 = curl_exec($ch);
        curl_close($ch);
        if($data2 === false) {
            throw new Wk_Exception('', TErrorConstants::E_SNS_LOGIN);
        }
        $data2 = json_decode($data2);
        if(!empty($data2->errcode)) {
            throw new Wk_Exception('', TErrorConstants::E_SNS_LOGIN);
        }
        if(empty($data2->openid)) {
            throw new Wk_Exception('', TErrorConstants::E_SNS_LOGIN);
        }

        $user = new TAR_WechatUser();

        $user->nickname = $data2->nickname;
        $user->sex = $data2->sex;
        $user->province = $data2->province;
        $user->city = $data2->city;
        $user->country = $data2->country;
        $user->headImgUrl = $data2->headimgurl;
        $user->openId = $data2->openid;
        $user->unionId = $data2->unionid;

        $tempUser = WkWxExternalService::getInstance()->getUserInfoByOpenId($openid);
        $user->subscribe = $tempUser['subscribe'];
        if($user->subscribe == 1) {
            $user->unionId = $tempUser['unionId'];
            $user->subscribeTime = $tempUser['subscribeTime'];
        }

        $_SESSION['tmpAvatar'] = $user->headImgUrl;

        WkWechatUserService::saveWeChatUser($user);
        return ['openid'=>$openid, 'weChatUserInfo' => $user, 'auth' => 'userinfo'];
    }

    /**
     * 获取微信用户信息（有openid，如果用户未关注，获取不到别的信息，因此会弹出授权页面)
     * @apiMethod get|post
     * @apiParam string openid 微信openid
     * @return array
     * @throws Wk_Exception
     */
    public function getWeChatUserInfoByOpenIdAction() {
        $openid = Wk_Request::getRequestString('openid',null,false);
        $weChatUserInfo = WkWeChatUserService::getWeChatUserByOpenId($openid);
        if(!isset($weChatUserInfo) || empty($weChatUserInfo) || empty($weChatUserInfo->unionId)) {
            // 调用用户基本信息接口
            $tempUser = WkWxExternalService::getInstance()->getUserInfoByOpenId($openid);
            // 如果已经关注了
            if($tempUser['subscribe'] == 1) {
                $weChatUserInfo = new TAR_WechatUser();
                $weChatUserInfo->unionId = $tempUser['unionId'];
                $weChatUserInfo->openId = $tempUser['openId'];
                $weChatUserInfo->subscribe = $tempUser['subscribe'];
                $weChatUserInfo->subscribeTime = date("Y-m-d H:i:s", $tempUser['subscribeTime']);
                $weChatUserInfo->sex = $tempUser['sex'];
                $weChatUserInfo->country = $tempUser['country'];
                $weChatUserInfo->province = $tempUser['province'];
                $weChatUserInfo->city = $tempUser['city'];
                $weChatUserInfo->nickname = $tempUser['nickname'];
                $weChatUserInfo->headImgUrl = $tempUser['headImgUrl'];
                // 创建该weChat用户
                WkWeChatUserService::saveWeChatUser($weChatUserInfo);
            }
            else {
//                do nothing
            }
        }
        else {
            // 如果该用户存在，也去调用一次，更新subscribe状态
            // 调用用户基本信息接口
            $tempUser = WkWxExternalService::getInstance()->getUserInfoByOpenId($openid);
            if($weChatUserInfo->subscribe != $tempUser['subscribe']) {
                $weChatUserInfo->subscribe = $tempUser['subscribe'];
                WkWeChatUserService::saveWeChatUser($weChatUserInfo);
            }
        }
        return ['weChatUserInfo' => $weChatUserInfo];
    }


    /**
     * 通过跳转方式获得微信基本授权，只能获得OpenId
     * @apiMethod get|post
     * @apiParam string retUrl 微信回调URL
     */
    public function getWxOAuth2Redirect_BaseAction() {
        $returl = Wk_Request::getRequestString("retUrl");
        $redirectUrl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".Wk::$config['wechat']['WX_AKEY']."&redirect_uri=".urlencode($returl)."&response_type=code&scope=snsapi_base&state=base#wechat_redirect";
        Wk_Request::redirect($redirectUrl);
    }

    /**
     * 通过跳转方式获得微信基本授权，可获得用户信息，但是会在微信里弹授权确认框
     * @apiMethod get|post
     * @apiParam string retUrl 微信回调URL
     * @apiParam string [state=userinfo] 获取的信息
     */
    public function getWxOAuth2Redirect_UserInfoAction() {
        $returl = Wk_Request::getRequestString("retUrl");
        // state可以传openId, 防止用户不通过授权，这样依然可以拿到用户的一些信息。
        $state = Wk_Request::getRequestString("state" ,"userinfo");
        $redirectUrl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".Wk::$config['wechat']['WX_AKEY']."&redirect_uri=".urlencode($returl)."&response_type=code&scope=snsapi_userinfo&state=".$state."#wechat_redirect";
        Wk_Request::redirect($redirectUrl);
    }
} 