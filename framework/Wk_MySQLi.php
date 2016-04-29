<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/4/25
 * Time: 下午4:06
 */

class Wk_MySQLi {
    private $_db = null;
    private $affected_rows = 0;

    private $_executeCnt = 0;

    public function __construct($config) {
        $this->_db = mysqli_init();
        if (!$this->_db) {
            Wk::logger()->err('mysqli_init failed');
            throw new Wk_Exception('',-1);
        }

        if (!$this->_db->options(MYSQLI_OPT_CONNECT_TIMEOUT, 1)) {
            Wk::logger()->err('Setting MYSQLI_OPT_CONNECT_TIMEOUT failed');
            throw new Wk_Exception('',-1);
        }

        if (!$this->_db->real_connect($config['host'], $config['username'], $config['password'], $config['db'], $config['port'])) {
            Wk::logger()->err('Connect Error (' . mysqli_connect_errno() . ') '
                . mysqli_connect_error());
            throw new Wk_Exception('',-1);
        }
        // $this->_db = new mysqli($dbConfig['host'], $dbConfig['username'], $dbConfig['password'], $dbConfig['db'], $dbConfig['port']);
        if (mysqli_connect_errno()) {
            Wk::logger()->err('connect to db failed: '.mysqli_connect_error().'('.mysqli_connect_errno().')');
            throw new Wk_Exception('',-1);
            //return false;
        } else {
            $this->_db->set_charset('utf8mb4');
        }
    }

    function __destruct() {
        if(isset($this->_db)) {
            $this->_db->close();
        }
    }

    private function fetchRes($stmt) {
        $ret = [];
        if(method_exists($stmt, 'get_result')) {
            $result = $stmt->get_result();
            if($result !== false) {
                while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $ret[] = $row;
                }
            }
        } else {
            $stmt->store_result();
            $meta = $stmt->result_metadata();
            $fields = [];
            $row = [];
            while ($field = $meta->fetch_field()) {
                $fields[] = &$row[$field->name];
            }
            call_user_func_array([$stmt, 'bind_result'], $fields);
            while ($stmt->fetch()) {
                $c = [];
                foreach($row as $key => $val) {
                    $c[$key] = $val;
                }
                $ret[] = $c;
            }
            $stmt->free_result();
        }
        return $ret;
    }

    /**
     * @param $query
     * @return int
     */
    public function count($query) {
        $ret = call_user_func_array([$this,"fetchOne"],func_get_args());
        if(!isset($ret)) {
            return 0;
        }
        $ret = array_values($ret);
        if(count($ret) > 1) {
            return 0;
        }
        if(!is_numeric($ret[0])) {
            return 0;
        }
        return intval($ret[0]);
    }

    public function fetchOne($query) {
        $ret = null;
        if($stmt = $this->_db->prepare($query)) {
            try{
                $args = func_get_args();
                $argsCount = count($args);
                if($argsCount > 1) {
                    $args = array_slice(func_get_args(), 1);
                    $refArr = [];
                    $n = count($args);
                    $refArr[] = $args[0];
                    for($i = 1; $i < $n; $i++) {
                        $refArr[] = &$args[$i];
                    }
                }
                if($argsCount == 1 || call_user_func_array([$stmt, "bind_param"], $refArr)) {
                    $this->_executeCnt++;
                    $startTime = microtime(true);
                    if($stmt->execute()) {
                        Wk::logger()->log('[SQL:'.$query.'][DURATION:'.(microtime(true)-$startTime).']');
                        $result = $this->fetchRes($stmt);
                        if(count($result) > 0) {
                            $ret = $result[0];
                        }
                    }else {
                        throw new Exception($stmt->error, $stmt->errno);
                    }
                }
                $stmt->close();
            } catch (Exception $e) {
                $stmt->close();
                Wk::logger()->err($e);
            }
        }
        return $ret;
    }

    public function fetchAll($query) {
        $ret = [];
        if($stmt = $this->_db->prepare($query)) {
            try{
                $args = func_get_args();
                $argsCount = count($args);
                if($argsCount > 1) {
                    $args = array_slice(func_get_args(), 1);
                    $refArr = [];
                    $n = count($args);
                    $refArr[] = $args[0];
                    for($i = 1; $i < $n; $i++) {
                        $refArr[] = &$args[$i];
                    }
                }
                if($argsCount == 1 || call_user_func_array([$stmt, "bind_param"], $refArr)) {
                    $this->_executeCnt++;
                    $startTime = microtime(true);
                    if($stmt->execute()) {
                        Wk::logger()->log('[SQL:'.$query.'][DURATION:'.(microtime(true)-$startTime).']');
                        $ret = $this->fetchRes($stmt);
                    }else{
                        throw new Exception($stmt->error, $stmt->errno);
                    }
                }
                $stmt->close();
            } catch (Exception $e) {
                $stmt->close();
                Wk::logger()->err($e);
            }
        }
        return $ret;
    }

    public function execute($query) {
        $ret = false;
        $this->affected_rows = 0;
        if($stmt = $this->_db->prepare($query)) {
            try{
                $args = func_get_args();
                $argsCount = count($args);
                if($argsCount > 1) {
                    $args = array_slice(func_get_args(), 1);
                    $refArr = [];
                    $n = count($args);
                    $refArr[] = $args[0];
                    for($i = 1; $i < $n; $i++) {
                        $refArr[] = &$args[$i];
                    }
                }
                if($argsCount == 1 || call_user_func_array([$stmt, "bind_param"], $refArr)) {
                    $this->_executeCnt++;
                    $startTime = microtime(true);
                    if($stmt->execute()) {
                        Wk::logger()->log('[SQL:'.$query.'][DURATION:'.(microtime(true)-$startTime).']');
                        $this->affected_rows = $stmt->affected_rows;
                        $ret = true;
                    }else {
                        throw new Exception($stmt->error, $stmt->errno);
                    }
                }
                $stmt->close();
            } catch (Exception $e) {
                $stmt->close();
                Wk::logger()->err($e);
            }
        }else{
            Wk::logger()->err($this->_db->error);
        }
        return $ret;
    }

    public function batchExecute($sqlArr) {
        $this->_db->autocommit(false);
        $ret = true;
        foreach ($sqlArr as $sql) {
            if($stmt = $this->_db->prepare($sql[0])) {
                try{
                    $args = $sql;
                    $argsCount = count($args);
                    if($argsCount > 1) {
                        $args = array_slice($sql, 1);
                        $refArr = [];
                        $n = count($args);
                        $refArr[] = $args[0];
                        for($i = 1; $i < $n; $i++) {
                            $refArr[] = &$args[$i];
                        }
                    }
                    if($argsCount == 1 || call_user_func_array([$stmt, "bind_param"], $refArr)) {
                        $this->_executeCnt++;
                        $startTime = microtime(true);
                        if(!$stmt->execute()) {
                            Wk::logger()->log('[SQL:'.$sql[0].'][DURATION:'.(microtime(true)-$startTime).']');
                            throw new Exception($stmt->error, $stmt->errno);
                        }
                    } else {
                        $ret = false;
                    }
                    $stmt->close();
                } catch (Exception $e) {
                    $ret = false;
                    $stmt->close();
                    Wk::logger()->err($e);
                }
            } else {
                $ret = false;
            }
        }
        if($ret) {
            $this->_db->commit();
        } else {
            $this->_db->rollback();
        }
        $this->_db->autocommit(true);
        return $ret;
    }

    public function startTransaction() {
        if(!$this->_db->autocommit(false)) {
            Wk::logger()->err($this->_db->error);
            throw new Wk_Exception('',-1);
        }
    }

    public function commit() {
        if(!$this->_db->commit()) {
            Wk::logger()->err($this->_db->error);
            throw new Wk_Exception("",-1);
        }
        $this->_db->autocommit(true);
    }

    public function rollback() {
        if(!$this->_db->rollback()) {
            Wk::logger()->err($this->error);
        }
        $this->_db->autocommit(true);
    }

    public function getInsertId() {
        return $this->_db->insert_id;
    }

    public function getAffectedRows() {
        return $this->affected_rows;
    }

    public function getExecuteCnt() {
        return $this->_executeCnt;
    }

    public function realEscapeString($string) {
        return $this->_db->real_escape_string($string);
    }
} 