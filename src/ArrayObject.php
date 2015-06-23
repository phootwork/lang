<?php
namespace phootwork\lang;

class ArrayObject implements \ArrayAccess, \Countable, \IteratorAggregate, \Serializable, Arrayable {

	private $array;

	public function __construct($contents = []) {
		$this->array = $contents;
	}

	public function count() {
		return count($this->array);
	}
	
	public function getIterator() {
		return new \ArrayIterator($this->array);
	}
	
	public function serialize() {
		return serialize($this->array);
	}
	
	public function unserialize($serialized) {
		$this->array = unserialize($serialized);
		
		return $this;
	}
	
	/**
	 * Push one or more elements onto the end of array
	 * 
	 * @param mixed $_ values
	 * @return $this
	 */
	public function push() {
		// das ist doch behindi!
		foreach (func_get_args() as $v) {
			array_push($this->array, $v);
		}
		return $this;
	}

	/**
	 * Pop the element off the end of array
	 * 
	 * @return mixed the popped element
	 */
	public function pop() {
		return array_pop($this->array);
	}
	
	/**
	 * Prepend one or more elements to the beginning of an array
	 * 
	 * @param mixed $_ values
	 * @return $this
	 */
	public function prepend() {
		// das ist doch auch behindi!
		foreach (func_get_args() as $v) {
			array_unshift($this->array, $v);
		}
		return $this;
	}

	/**
	 * Shift an element off the beginning of array
	 * 
	 * @return mixed the shifted element
	 */
	public function shift() {
		return array_shift($this->array);
	}

	/**
	 * Sorts the array
	 *
	 * @param Comparator|callable $cmp
	 * @return $this
	 */
	public function sort($cmp = null) {
		$this->doSort($this->array, $cmp, 'usort', 'sort');
	
		return $this;
	}
	
	/**
	 * Sorts the array and persisting key-value pairs
	 *
	 * @param Comparator|callable $cmp
	 * @return $this
	 */
	public function sortAssoc($cmp = null) {
		$this->doSort($this->array, $cmp, 'uasort', 'asort');
	
		return $this;
	}
	
	/**
	 * Sorts the array by keys
	 *
	 * @param Comparator|callable $cmp
	 * @return $this
	 */
	public function sortKeys($cmp = null) {
		$this->doSort($this->array, $cmp, 'uksort', 'ksort');
	
		return $this;
	}
	
	/**
	 * Internal sort function
	 *
	 * @param array $collection the collection on which is operated on
	 * @param Comparator|callable $cmp the compare function
	 * @param callable $usort the sort function for user passed $cmd
	 * @param callable $sort the default sort function
	 */
	protected function doSort(&$collection, $cmp, callable $usort, callable $sort) {
		if (is_callable($cmp)) {
			$usort($collection, $cmp);
		} else if ($cmp instanceof Comparator) {
			$usort($collection, function($a, $b) use ($cmp) {
				return $cmp->compare($a, $b);
			});
		} else {
			$sort($collection);
		}
	}

	/**
	 * Joins the array with a string
	 *
	 * @param string $glue Defaults to an empty string.
	 * @return Text
	 * 		Returns a string containing a string representation of all the array elements in the
	 * 		same order, with the glue string between each element.
	 */
	public function join($glue = '') {
		return new Text(implode($this->array, $glue));
	}

	/**
	 * Applies the callback to the elements of the given arrays
	 * 
	 * @param callable $callback Callback function to run for each element in each array. 
	 * @return ArrayObject
	 */
	public function map(callable $callback) {
		return new ArrayObject(array_map($callback, $this->array));
	}
	
	/**
	 * Filters elements of an array using a callback function
	 * 
	 * @param callable $callback The callback function to use
	 * 		If no callback is supplied, all entries of array equal to false will be removed.
	 * @return ArrayObject
	 */
	public function filter(callable $callback) {
		return new ArrayObject(array_filter($this->array, $callback));
	}
	
	/**
	 * Iteratively reduce the array to a single value using a callback function
	 * 
	 * @param callable $callback callback function
	 * 		`mixed callback (mixed $carry , mixed $item)`
	 * 		$carry - Holds the return value of the previous iteration; in the case of the first iteration it instead holds the value of initial.
	 * 		$item - Holds the value of the current iteration.  
	 * @param mixed $initial If the optional initial is available, it will be used at the beginning of the process, or as a final result in case the array is empty.
	 * @return mixed
	 */
	public function reduce(callable $callback, $initial = null) {
		return array_reduce($this->array, $callback, $initial);
	}

	/**
	 * Merges in other values
	 * 
	 * @param mixed ... Variable list of arrays to merge.
	 * @return ArrayObject $this 
	 */
	public function merge() {
		$this->array = array_merge($this->array, func_get_args());
		return $this;
	}
	
	/**
	 * Returns the keys of the array
	 * 
	 * @return ArrayObject the keys
	 */
	public function keys() {
		return new ArrayObject(array_keys($this->array));
	}
	
	/**
	 * Returns the values of the array
	 * 
	 * @return ArrayObject the values
	 */
	public function values() {
		return new ArrayObject(array_values($this->array));
	}
	
	/**
	 * Flips keys and values
	 * 
	 * @return ArrayObject $this
	 */
	public function flip() {
		$this->array = array_flip($this->array);
		return $this;
	}
	
	/**
	 * Returns the php array type
	 * 
	 * @return array
	 */
	public function toArray() {
		return $this->array;
	}
	
	/**
	 * @internal
	 */
	public function offsetSet($offset, $value) {
		if (!is_null($offset)) {
			$this->array[$offset] = $value;
		}
	}
	
	/**
	 * @internal
	 */
	public function offsetExists($offset) {
		return isset($this->array[$offset]);
	}
	
	/**
	 * @internal
	 */
	public function offsetUnset($offset) {
		unset($this->array[$offset]);
	}
	
	/**
	 * @internal
	 */
	public function offsetGet($offset) {
		return isset($this->array[$offset]) ? $this->array[$offset] : null;
	}
	
}