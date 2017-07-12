<?php

/**
 * Class Set_Data
 */
class Set_Data implements ArrayAccess, Countable, IteratorAggregate, Serializable {
	private $data = array();

	public function __construct($data = array()) {
        if (!empty($data))
            $this->set($data);
	}

	public function get($key) {
		return isset($this->data[$key]) ? $this->data[$key] : NULL;
	}

	public function set($key, $value  = NULL) {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else
            $this->data[$key] = $value;
    }

	public function all() {
		return $this->data;
	}

	public function keys() {
        return array_keys($this->data);
    }

	public function has($key) {
        return array_key_exists($key, $this->data);
    }

	public function remove($key) {
        unset($this->data[$key]);
    }

	public function __get($key) {
        return $this->get($key);
    }

    public function __set($key, $value) {
        $this->set($key, $value);
    }

    public function __isset($key) {
        return $this->has($key);
    }

    public function __unset($key) {
        return $this->remove($key);
    }

	public function clear() {
        $this->data = array();
    }

	public function offsetExists($offset) {
        return $this->has($offset);
    }

    public function offsetGet($offset) {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value) {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset) {
        $this->remove($offset);
    }

	public function count() {
        return count($this->data);
    }

	public function getIterator() {
        return new ArrayIterator($this->data);
    }

    public function serialize() {
        return serialize($this->data);
    }

    public function unserialize($serialized) {
        return unserialize($this->data);
    }
}