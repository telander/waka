<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/5/3
 * Time: 下午1:53
 */

class Wk_User extends Wk_Entity{
    /**
     * 用户ID
     * @var int
     */
    public $userid;

    /**
     * 手机号
     * @var string
     */
    public $mobile;

    /**
     * 常居城市
     * @var string
     */
    public $city;

    /**
     * 常居省份
     * @var string
     */
    public $province;

    /**
     * 头像
     * @var string
     */
    public $avatarUrl;

    /**
     * 昵称
     * @var string
     */
    public $nickname;

    /**
     * 用户TOKEN
     * @var string
     */
    public $token;
} 