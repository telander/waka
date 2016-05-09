<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/5/8
 * Time: 下午10:40
 */

class Wk_AdminUser extends Wk_Entity{
    /**
     * 用户ID
     * @var int
     */
    public $id;

    /**
     * 手机号
     * @var string
     */
    public $mobile;

    /**
     * 昵称
     * @var string
     */
    public $nickname;
}