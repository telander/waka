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
        $code = Wk_Request::getStringReq('code',null,false);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.K::$config['wechat']['WX_AKEY'].'&secret='.K::$config['wechat']['WX_SKEY'].'&code='.$code.'&grant_type=authorization_code';
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
} 