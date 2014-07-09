<?php

/**
 * @class			Set
 * @namespace		Freya\Helpers
 * @description
 * 
 * A class that implements the PHP interfaces for ArrayAccess, Countable, and IteratorAggregate.
 * Set objects can be treated as an array for the purpose of accessing and setting variables,
 * foreach() loops and functions like isset(), empty(), and count().
 * 
 * @methods
 * 
 * Set::all()					Returns an array of all data in the set
 * Set::clear()					Empties the set of all data
 * Set::has($key)				Returns (bool) based on whether $key exists in the set
 * Set::keys()					Returns an array of all keys in the set data
 * Set::remove($key)			Unsets a value from the set data corresponding to $key
 * Set::replace($items)			Sets multiple key => value pairs, as defined by the supplied $items array
 * Set::singleton($key, $value)	Used to ensure that objects in memory remain globally unique
 */

namespace Freya\Helpers;

class Set implements \ArrayAccess, \Countable, \IteratorAggregate {
	protected $data = array();

	public function __construct($items = array()) {
		$this->replace($items);
	}

	/**
	 * Custom getter and setter functions
	 */
	public function get($key, $default = null) {
		if ($this->has($key)) {
			return $this->data[$this->normalizeKey($key)];
		}

		return $default;
	}

	public function set($key, $value) {
		$this->data[$this->normalizeKey($key)] = $value;
	}

	/**
	 * Helper functions
	 */
	public function all() {
		return $this->data;
	}

	public function clear() {
		$this->data = array();
	}

	public function has($key) {
		return array_key_exists($this->normalizeKey($key), $this->data);
	}

	public function keys() {
		return array_keys($this->data);
	}

	public function remove($key) {
		unset($this->data[$this->normalizeKey($key)]);
	}
	
	public function replace($items) {
		foreach ($items as $key => $value) {
			$this->set($key, $value);
		}
	}
	
	/**
	 * Normalize Key
	 * This function is designed to be overloaded by child classes as needed
	 */
	protected function normalizeKey($key) {
		return $key;
	}
	
	/**
	 * Magic Function Overloading
	 */
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

	/**
	 * ArrayAccess interface implementation
	 */
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

	/**
	 * Countable interface implementation
	 */
	public function count() {
		return count($this->data);
	}

	/**
	 * IteratorAggregate interface implementation
	 */
	public function getIterator() {
		return new \ArrayIterator($this->data);
	}
}
