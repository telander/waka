<?php
class TAR_AdminUser extends Wk_ActiveRecord {

    public function __construct(array $sqlRow = null) {
        if (isset($sqlRow)) {
            $this->id = isset($sqlRow['id']) ? intval($sqlRow['id']) : null;
            $this->mobile = $sqlRow['mobile'];
            $this->password = $sqlRow['password'];
            $this->lastLoginIp = $sqlRow['last_login_ip'];
            $this->lastLoginTime = $sqlRow['last_login_time'];
            $this->nickname = $sqlRow['nickname'];
            $this->createTime = $sqlRow['create_time'];
            $this->isDeleted = isset($sqlRow['is_deleted']) ? intval($sqlRow['is_deleted']) : null;
        }
    }

    static protected function getTableName() {
        return 'waka_admin_user';
    }

    static protected function getColNames() {
        return [
            'mobile' => ['name' => 'mobile', 'type' => 's'],
            'password' => ['name' => 'password', 'type' => 's'],
            'last_login_ip' => ['name' => 'lastLoginIp', 'type' => 's'],
            'last_login_time' => ['name' => 'lastLoginTime', 'type' => 's'],
            'nickname' => ['name' => 'nickname', 'type' => 's'],
            'create_time' => ['name' => 'createTime', 'type' => 's'],
            'is_deleted' => ['name' => 'isDeleted', 'type' => 'i'],
        ];
    }

    /**
     * @var string
     */
    public $mobile = '';

    /**
     * @var string
     */
    public $password;

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
    public $createTime;

    /**
     * @var integer
     */
    public $isDeleted = 0;

}