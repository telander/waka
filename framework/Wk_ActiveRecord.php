<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/4/25
 * Time: 下午4:56
 */

abstract class Wk_ActiveRecord extends Wk_Entity {

    /**
     * @var integer
     */
    public $id;

    abstract static protected function getTableName();

    abstract static protected function getColNames();

    /**
     * @param string $condition
     * @return $this
     * @throws Wk_Exception
     */
    final static public function findOne($condition = '') {
        $class = get_called_class();
        $tableName = call_user_func_array([$class, 'getTableName'], []);
        $sql = 'select * from ' . $tableName . ' ' . $condition;
        $args = array_slice(func_get_args(), 1);
        array_unshift($args, $sql);
        $res = call_user_func_array([Wk::db(), 'fetchOne'], $args);
        if (isset($res)) {
            return new $class($res);
        } else {
            return null;
        }
    }

    /**
     * @param string $condition
     * @return $this[]
     * @throws Wk_Exception
     */
    final static public function findAll($condition = '') {
        $class = get_called_class();
        $tableName = call_user_func_array([$class, 'getTableName'], []);
        $sql = 'select * from ' . $tableName . ' ' . $condition;
        $args = array_slice(func_get_args(), 1);
        array_unshift($args, $sql);
        $res = call_user_func_array([Wk::db(), 'fetchAll'], $args);
        if (isset($res) && !empty($res)) {
            $ret = [];
            foreach ($res as $sqlRow) {
                $ret[] = new $class($sqlRow);
            }
            return $ret;
        } else {
            return [];
        }
    }

    /**
     * @param string $condition
     * @return array
     * @throws Wk_Exception
     */
    final static public function findAllArr($condition = '') {
        $class = get_called_class();
        $tableName = call_user_func_array([$class, 'getTableName'], []);
        $sql = 'select * from ' . $tableName . ' ' . $condition;
        $args = array_slice(func_get_args(), 1);
        array_unshift($args, $sql);
        $res = call_user_func_array([Wk::db(), 'fetchAll'], $args);
        if (isset($res) && !empty($res)) {
            $ret = [];
            foreach ($res as $sqlRow) {
                $ret[] = $sqlRow;
            }
            return $ret;
        } else {
            return [];
        }
    }

    /**
     * @param int $pageNo
     * @param int $pageSize
     * @param string $condition
     * @return $this[]
     */
    final static public function findPage($pageNo, $pageSize, $condition = '') {
        $class = get_called_class();
        $tableName = call_user_func_array([$class, 'getTableName'], []);
        $offset = intval(($pageNo - 1) * $pageSize);
        $limit = intval($pageSize);
        $sql = 'select * from ' . $tableName . ' ' . $condition . ' limit ' . $offset . ',' . $limit;
        $args = array_slice(func_get_args(), 3);
        array_unshift($args, $sql);
        $res = call_user_func_array([Wk::db(), 'fetchAll'], $args);
        if (isset($res) && !empty($res)) {
            $ret = [];
            foreach ($res as $sqlRow) {
                $ret[] = new $class($sqlRow);
            }
            return $ret;
        } else {
            return [];
        }
    }

    /**
     * @param $id
     * @return $this
     */
    final static public function findById($id) {
        return self::findOne('where id = ?', 'i', $id);
    }

    /**
     * @param $condition
     * @return int
     * @throws Wk_Exception
     */
    final static public function count($condition) {
        $class = get_called_class();
        $tableName = call_user_func_array([$class, 'getTableName'], []);
        $sql = 'select count(*) from ' . $tableName . ' ' . $condition;
        $args = array_slice(func_get_args(), 1);
        array_unshift($args, $sql);
        $res = call_user_func_array([Wk::db(), 'count'], $args);
        return $res;
    }


    /**
     * usage:
     *  count2(['userId'=>1,'createTime'=>date('Y-m-d H:i:s', time())]);
     *
     * @param $condition
     * @return int
     * @throws Wk_Exception
     */
    final static public function count2($condition) {
        $class = get_called_class();
        $tableName = call_user_func_array([$class, 'getTableName'], []);
        $sql = 'select count(*) from ' . $tableName . ' ';

        $args = func_get_args();
        $types = '';
        $inspectArgs = $args[0];
        foreach($inspectArgs as $arg) {
            if(is_numeric($arg)) {
                $types .= 'i';
            } else {
                $types .= 's';
            }
        }
        $sql .= 'WHERE '. implode('=? AND ', array_keys($args[0])).'=?';
        array_unshift($inspectArgs, $types);
        array_unshift($inspectArgs, $sql);
        $res = call_user_func_array([Wk::db(), 'count'], $inspectArgs);
        return $res;
    }

    /**
     * @throws Wk_Exception
     */
    final public function insert() {
        if (isset($this->id)) {
            Wk::logger()->err('obj already inserted');
            throw new Wk_Exception('', -1);
        }
        $cols = $this->getColNames();
        $useCols = [];
        $args = [];
        $types = '';
        foreach ($cols as $col => $attr) {
            $useCols[] = $col;
            $types .= $attr['type'];
            $args[] = $this->{$attr['name']};
        }
        $sql = 'insert into ' . $this->getTableName() . '(' . implode(',', $useCols) . ') values (' . implode(',', array_fill(0, count($useCols), '?')) . ')';
        array_unshift($args, $types);
        array_unshift($args, $sql);
        if (call_user_func_array([Wk::db(), 'execute'], $args)) {
            $this->id = Wk::db()->getInsertId();
        } else {
            Wk::logger()->err('db error');
            throw new Wk_Exception('', -1);
        }
    }

    /**
     * @param array $partial
     * @throws Wk_Exception
     */
    final public function update(array $partial = []) {
        if (!isset($this->id)) {
            Wk::logger()->err('obj not inserted');
            throw new Wk_Exception('', -1);
        }
        $useCols = [];
        $args = [];
        $types = '';
        $cols = $this->getColNames();
        $partialKeys = array_keys($partial);
        foreach ($cols as $col => $attr) {
            if (!empty($partial) && !in_array($attr['name'], $partialKeys)) {
                continue;
            }
            $useCols[] = $col . '=?';
            $types .= $attr['type'];
            if (!empty($partial)) {
                $args[] = $partial[$attr['name']];
                $this->{$attr['name']} = $partial[$attr['name']];
            } else {
                $args[] = $this->{$attr['name']};
            }
        }
        $sql = 'update ' . $this->getTableName() . ' set ' . implode(',', $useCols) . ' where id=?';
        $args[] = $this->id;
        $types .= 'i';
        array_unshift($args, $types);
        array_unshift($args, $sql);
        if (!call_user_func_array([Wk::db(), 'execute'], $args)) {
            Wk::logger()->err('db error');
            throw new Wk_Exception('', -1);
        }
    }

    /**
     * @throws Wk_Exception
     */
    final public function save() {
        //$now = date('Y-m-d H:i:s');
        if (!empty(Wk::app()->user) && property_exists($this, 'updateUser')) {
            $this->updateUser = Wk::app()->user->userid;
        }
        if (property_exists($this, 'updateTime')) {
            $this->updateTime = date('Y-m-d H:i:s');
        }
        if (isset($this->id)) {
            $this->update();
        } else {
            if (!empty(Wk::app()->user) && property_exists($this, 'createUser')) {
                $this->createUser = Wk::app()->user->userid;
            }
            if (property_exists($this, 'createTime')) {
                $this->createTime = date('Y-m-d H:i:s');
            }
            $this->insert();
        }
    }

    /**
     * @throws Wk_Exception
     */
    final public function delete() {
        if (!isset($this->id)) {
            Wk::logger()->err('obj not inserted');
            throw new Wk_Exception('', -1);
        }
        if (property_exists($this, 'isDeleted')) {
            $this->isDeleted = 1;
            $this->save();
        } else {
            $sql = 'delete from ' . $this->getTableName() . ' where id=?';
            if (Wk::db()->execute($sql, 'i', $this->id)) {
                unset($this->id);
            } else {
                Wk::logger()->err('db error');
                throw new Wk_Exception('', -1);
            }
        }
    }

} 
