<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/4/26
 * Time: 下午3:30
 */

class Wk_City extends Wk_Entity{
    /**
     * 城市ID
     * @var int
     */
    public $id;

    /**
     * 城市名
     * @var string
     */
    public $name;

    /**
     * 省
     * @var string
     */
    public $province;

    /**
     * 状态：0开通，1待开通
     * @var int
     */
    public $status;

    /**
     * lat
     * @var float
     */
    public $lat;

    /**
     * lng
     * @var float
     */
    public $lng;
} 