<?php
class TAR_Usertoken extends Wk_ActiveRecord {

    public function __construct(array $sqlRow = null) {
        if (isset($sqlRow)) {
            $this->id = isset($sqlRow['id']) ? intval($sqlRow['id']) : null;
            $this->userId = isset($sqlRow['user_id']) ? intval($sqlRow['user_id']) : null;
            $this->token = $sqlRow['token'];
            $this->expire = $sqlRow['expire'];
            $this->logintype = $sqlRow['logintype'];
            $this->isDeleted = isset($sqlRow['is_deleted']) ? intval($sqlRow['is_deleted']) : null;
        }
    }

    static protected function getTableName() {
        return 'waka_usertoken';
    }

    static protected function getColNames() {
        return [
            'user_id' => ['name' => 'userId', 'type' => 'i'],
            'token' => ['name' => 'token', 'type' => 's'],
            'expire' => ['name' => 'expire', 'type' => 's'],
            'logintype' => ['name' => 'logintype', 'type' => 's'],
            'is_deleted' => ['name' => 'isDeleted', 'type' => 'i'],
        ];
    }

    /**
     * @var integer
     */
    public $userId;

    /**
     * @var string
     */
    public $token;

    /**
     * @var string
     */
    public $expire;

    /**
     * @var string
     */
    public $logintype;

    /**
     * @var integer
     */
    public $isDeleted = 0;

}