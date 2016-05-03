<?php
class TAR_MobileVerify extends Wk_ActiveRecord {

    public function __construct(array $sqlRow = null) {
        if (isset($sqlRow)) {
            $this->id = isset($sqlRow['id']) ? intval($sqlRow['id']) : null;
            $this->randstr = $sqlRow['randstr'];
            $this->userid = isset($sqlRow['userid']) ? intval($sqlRow['userid']) : null;
            $this->phonecode = isset($sqlRow['phonecode']) ? intval($sqlRow['phonecode']) : null;
            $this->mobile = $sqlRow['mobile'];
            $this->expire = $sqlRow['expire'];
            $this->timestamp = $sqlRow['timestamp'];
            $this->state = isset($sqlRow['state']) ? intval($sqlRow['state']) : null;
        }
    }

    static protected function getTableName() {
        return 'waka_mobile_verify';
    }

    static protected function getColNames() {
        return [
            'randstr' => ['name' => 'randstr', 'type' => 's'],
            'userid' => ['name' => 'userid', 'type' => 'i'],
            'phonecode' => ['name' => 'phonecode', 'type' => 'i'],
            'mobile' => ['name' => 'mobile', 'type' => 's'],
            'expire' => ['name' => 'expire', 'type' => 's'],
            'timestamp' => ['name' => 'timestamp', 'type' => 's'],
            'state' => ['name' => 'state', 'type' => 'i'],
        ];
    }

    /**
     * @var string
     */
    public $randstr;

    /**
     * @var integer
     */
    public $userid;

    /**
     * @var integer
     */
    public $phonecode = 86;

    /**
     * @var string
     */
    public $mobile;

    /**
     * @var string
     */
    public $expire;

    /**
     * @var string
     */
    public $timestamp;

    /**
     * @var integer
     */
    public $state;

}