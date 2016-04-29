<?php
class TAR_User extends Wk_ActiveRecord {

    public function __construct(array $sqlRow = null) {
        if (isset($sqlRow)) {
            $this->id = isset($sqlRow['id']) ? intval($sqlRow['id']) : null;
            $this->status = isset($sqlRow['status']) ? intval($sqlRow['status']) : null;
            $this->mobile = $sqlRow['mobile'];
            $this->password = $sqlRow['password'];
            $this->source = isset($sqlRow['source']) ? intval($sqlRow['source']) : null;
            $this->openId = $sqlRow['open_id'];
            $this->lastLoginIp = $sqlRow['last_login_ip'];
            $this->lastLoginTime = $sqlRow['last_login_time'];
            $this->nickname = $sqlRow['nickname'];
            $this->city = $sqlRow['city'];
            $this->province = $sqlRow['province'];
            $this->headImgUrl = $sqlRow['head_img_url'];
            $this->createTime = $sqlRow['create_time'];
            $this->courtsId = $sqlRow['courts_id'];
            $this->isDelete = isset($sqlRow['is_delete']) ? intval($sqlRow['is_delete']) : null;
        }
    }

    static protected function getTableName() {
        return 'waka_user';
    }

    static protected function getColNames() {
        return [
            'status' => ['name' => 'status', 'type' => 'i'],
            'mobile' => ['name' => 'mobile', 'type' => 's'],
            'password' => ['name' => 'password', 'type' => 's'],
            'source' => ['name' => 'source', 'type' => 'i'],
            'open_id' => ['name' => 'openId', 'type' => 's'],
            'last_login_ip' => ['name' => 'lastLoginIp', 'type' => 's'],
            'last_login_time' => ['name' => 'lastLoginTime', 'type' => 's'],
            'nickname' => ['name' => 'nickname', 'type' => 's'],
            'city' => ['name' => 'city', 'type' => 's'],
            'province' => ['name' => 'province', 'type' => 's'],
            'head_img_url' => ['name' => 'headImgUrl', 'type' => 's'],
            'create_time' => ['name' => 'createTime', 'type' => 's'],
            'courts_id' => ['name' => 'courtsId', 'type' => 's'],
            'is_delete' => ['name' => 'isDelete', 'type' => 'i'],
        ];
    }

    /**
     * @var integer
     */
    public $status = 0;

    /**
     * @var string
     */
    public $mobile = '';

    /**
     * @var string
     */
    public $password;

    /**
     * @var integer
     */
    public $source = 1;

    /**
     * @var string
     */
    public $openId;

    /**
     * @var string
     */
    public $lastLoginIp;

    /**
     * @var string
     */
    public $lastLoginTime;

    /**
     * @var string
     */
    public $nickname;

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
    public $headImgUrl;

    /**
     * @var string
     */
    public $createTime;

    /**
     * @var string
     */
    public $courtsId;

    /**
     * @var integer
     */
    public $isDelete = 0;

}