<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/4/26
 * Time: 下午3:36
 */

class CityApiController extends ApiController{

    public function access() {

    }

    /**
     * 获取waka城市列表
     * @return Wk_City_List
     */
    public function getCityListAction() {
        $citylist = WkCityService::getInstance()->getWkCityList();
        return $citylist;
    }
} 