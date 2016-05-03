<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/5/3
 * Time: 下午3:59
 */

class WkWechatUserService {
    /**
     * @param $openId
     * @return TAR_WechatUser
     */
    public static function getWeChatUserByOpenId($openId) {
        $mcdKey = 'm:weChatUserService?openId='.$openId;
        $user = Wk::redis()->get($mcdKey);
        if($user === false) {
            $user = TAR_WechatUser::findOne("where open_id = ?", "s", $openId);
            if(isset($user)) {
                Wk::redis()->set($mcdKey, serialize($user), 60*60*24);
            }
        }
        else {
            $user = unserialize($user);
        }
        return $user;
    }

    public static function saveWeChatUser($user) {
        $oldUser = self::getWeChatUserByOpenId($user->openId);
        if(isset($oldUser)) {
            $oldUser->unionId = $user->unionId;
            $oldUser->nickname = $user->nickname;
            $oldUser->sex = $user->sex;
            $oldUser->city = $user->city;
            $oldUser->province = $user->province;
            $oldUser->country = $user->country;
            $oldUser->subscribeTime = $user->subscribeTime;
            $oldUser->subscribe = $user->subscribe;
            $oldUser->headImgUrl = $user->headImgUrl;
            $oldUser->openId = $user->openId;

            $oldUser->save();
            $mcdKey = 'm:weChatUserService?openId='.$user->openId;
            Wk::redis()->delete($mcdKey);
            return true;
        } else {
            $oldUser = new TAR_WechatUser();
            $oldUser->unionId = $user->unionId;
            $oldUser->nickname = $user->nickname;
            $oldUser->sex = $user->sex;
            $oldUser->city = $user->city;
            $oldUser->province = $user->province;
            $oldUser->country = $user->country;
            $oldUser->subscribeTime = $user->subscribeTime;
            $oldUser->subscribe = $user->subscribe;
            $oldUser->headImgUrl = $user->headImgUrl;
            $oldUser->openId = $user->openId;

            $oldUser->save();
            return true;
        }
        return false;
    }
}