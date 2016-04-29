<?php
/**
 * Created by PhpStorm.
 * User: jill
 * Date: 16/4/25
 * Time: 下午4:54
 */

class Wk_Entity implements JsonSerializable, ArrayAccess {

    public function jsonSerialize() {
        $res = [];
        $props = get_object_vars($this);
        foreach ($props as $prop => $value) {
            if (strpos($prop, '_') === 0) continue;
            if (isset($value)) {
                $res[$prop] = $value;
            }
        }
        return $res;
    }

    public function __set($name, $value) {
        Wk::logger()->err("property $name not exists in " . get_called_class());
        throw new Wk_Exception("property $name not exists in " . get_called_class(), -1);
    }

    public function __get($name) {
        Wk::logger()->err("property $name not exists in " . get_called_class());
        throw new Wk_Exception("property $name not exists in " . get_called_class(), -1);
    }

    final public function genFromObj($obj) {
        $props = get_object_vars($this);
        foreach ($props as $key => $value) {
            if (isset($obj->{$key})) {
                $this->{$key} = $obj->{$key};
            }
        }
    }

    final public function genFromArray(array $arr) {
        $props = get_object_vars($this);
        foreach ($props as $key => $value) {
            if (isset($arr[$key])) {
                $this->{$key} = $arr[$key];
            }
        }
    }

    public function map(array $propMap) {
        $props = get_object_vars($this);
        foreach ($props as $key => $value) {
            if(!in_array($key, $propMap)) {
                unset($this->{$key});
            }
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset) {
        return property_exists($this, $offset);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @throws Wk_Exception
     */
    public function offsetGet($offset) {
        if ($this->offsetExists($offset)) {
            return $this->{$offset};
        }
        Wk::logger()->err("property $offset not exists in " . get_called_class());
        throw new Wk_Exception("property $offset not exists in " . get_called_class(), -1);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @throws Wk_Exception
     */
    public function offsetSet($offset, $value) {
        if ($this->offsetExists($offset)) {
            $this->{$offset} = $value;
        }
        Wk::logger()->err("property $offset not exists in " . get_called_class());
        throw new Wk_Exception("property $offset not exists in " . get_called_class(), -1);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset) {
        if ($this->offsetExists($offset)) {
            $this->{$offset} = null;
        }
    }

} 