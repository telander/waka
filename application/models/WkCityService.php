<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/4/26
 * Time: 下午3:27
 */

class WkCityService extends Wk_Service{
    /**
     * @return Wk_City_List
     */
    public function getWkCityList() {
        $cityListRKey = "wk:city:getCityList";
        $res = unserialize(Wk::redis()->get($cityListRKey));
        if(!$res) {
            $cities = TAR_City::findAll("where status in (0, 1) order by status asc");
            $res = new Wk_City_List();
            $res->list = [];
            foreach ($cities as $city) {
                $item = new Wk_City();
                $item->id = $city->id;
                $item->name = $city->name;
                $item->province = $city->province;
                $item->status = $city->status;
                $item->lat = $city->lat;
                $item->lng = $city->lng;
                $res->list[] = $item;
            }
            Wk::redis()->set($cityListRKey, serialize($res), 20*60);
        }
        return $res;
    }
} 