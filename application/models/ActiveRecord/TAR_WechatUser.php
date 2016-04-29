<?php
class TAR_WechatUser extends Wk_ActiveRecord {

    public function __construct(array $sqlRow = null) {
        if (isset($sqlRow)) {
            $this->nickname = $sqlRow['nickname'];
            $this->openId = $sqlRow['open_id'];
            $this->unionId = $sqlRow['union_id'];
            $this->subscribe = isset($sqlRow['subscribe']) ? intval($sqlRow['subscribe']) : null;
            $this->sex = isset($sqlRow['sex']) ? intval($sqlRow['sex']) : null;
            $this->city = $sqlRow['city'];
            $this->province = $sqlRow['province'];
            $this->country = $sqlRow['country'];
            $this->headImgUrl = $sqlRow['head_img_url'];
            $this->subscribeTime = isset($sqlRow['subscribe_time']) ? intval($sqlRow['subscribe_time']) : null;
            $this->latitude = isset($sqlRow['latitude']) ? doubleval($sqlRow['latitude']) : null;
            $this->longitude = isset($sqlRow['longitude']) ? doubleval($sqlRow['longitude']) : null;
            $this->accuracy = isset($sqlRow['accuracy']) ? doubleval($sqlRow['accuracy']) : null;
        }
    }

    static protected function getTableName() {
        return 'waka_wechat_user';
    }

    static protected function getColNames() {
        return [
            'nickname' => ['name' => 'nickname', 'type' => 's'],
            'open_id' => ['name' => 'openId', 'type' => 's'],
            'union_id' => ['name' => 'unionId', 'type' => 's'],
            'subscribe' => ['name' => 'subscribe', 'type' => 'i'],
            'sex' => ['name' => 'sex', 'type' => 'i'],
            'city' => ['name' => 'city', 'type' => 's'],
            'province' => ['name' => 'province', 'type' => 's'],
            'country' => ['name' => 'country', 'type' => 's'],
            'head_img_url' => ['name' => 'headImgUrl', 'type' => 's'],
            'subscribe_time' => ['name' => 'subscribeTime', 'type' => 'i'],
            'latitude' => ['name' => 'latitude', 'type' => 'd'],
            'longitude' => ['name' => 'longitude', 'type' => 'd'],
            'accuracy' => ['name' => 'accuracy', 'type' => 'd'],
        ];
    }

    /**
     * @var string
     */
    public $nickname;

    /**
     * @var string
     */
    public $openId = '';

    /**
     * @var string
     */
    public $unionId = '';

    /**
     * @var integer
     */
    public $subscribe;

    /**
     * @var integer
     */
    public $sex;

    /**
     * @var string
     */
    public $city;

    /**
     * @var string
     */
    public $province;

    /**
     * @var string
     */
    public $country;

    /**
     * @var string
     */
    public $headImgUrl;

    /**
     * @var integer
     */
    public $subscribeTime;

    /**
     * @var double
     */
    public $latitude;

    /**
     * @var double
     */
    public $longitude;

    /**
     * @var double
     */
    public $accuracy;

}