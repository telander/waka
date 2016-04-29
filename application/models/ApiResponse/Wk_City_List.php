<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/4/26
 * Time: 下午3:32
 */

class Wk_City_List extends Wk_Entity{
    /**
     * 城市列表
     * @var Wk_City[]
     */
    public $list;

    /**
     * 开通城市数
     * @var int
     */
    public $openingCount;

    /**
     * 即将开通城市数
     * @var int
     */
    public $openSoonCount;
} 