<?php
class TAR_City extends Wk_ActiveRecord {

    public function __construct(array $sqlRow = null) {
        if (isset($sqlRow)) {
            $this->id = isset($sqlRow['id']) ? intval($sqlRow['id']) : null;
            $this->name = $sqlRow['name'];
            $this->province = $sqlRow['province'];
            $this->status = isset($sqlRow['status']) ? intval($sqlRow['status']) : null;
            $this->createTime = $sqlRow['create_time'];
            $this->lat = isset($sqlRow['lat']) ? doubleval($sqlRow['lat']) : null;
            $this->lng = isset($sqlRow['lng']) ? doubleval($sqlRow['lng']) : null;
        }
    }

    static protected function getTableName() {
        return 'waka_city';
    }

    static protected function getColNames() {
        return [
            'name' => ['name' => 'name', 'type' => 's'],
            'province' => ['name' => 'province', 'type' => 's'],
            'status' => ['name' => 'status', 'type' => 'i'],
            'create_time' => ['name' => 'createTime', 'type' => 's'],
            'lat' => ['name' => 'lat', 'type' => 'd'],
            'lng' => ['name' => 'lng', 'type' => 'd'],
        ];
    }

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $province;

    /**
     * @var integer
     */
    public $status = 0;

    /**
     * @var string
     */
    public $createTime;

    /**
     * @var double
     */
    public $lat = 0;

    /**
     * @var double
     */
    public $lng = 0;

}