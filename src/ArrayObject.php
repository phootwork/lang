<?php
namespace phootwork\lang;

class ArrayObject implements \ArrayAccess, \Countable, \IteratorAggregate, \Serializable, Arrayable {

	protected $array;

	public function __construct($contents = []) {
		$this->array = $contents;
	}

	public function __clone() {
		return new ArrayObject($this->array);
	}

	/**
	 * Counts the array
	 *
	 * @return int the amount of items
	 */
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
	 * Resets the array
	 *
	 * @return $this
	 */
	public function clear() {
		$this->array = [];
		return $this;
	}

	/**
	 * Checks whether this array is empty
	 *
	 * @return boolean
	 */
	public function isEmpty() {
		return $this->count() === 0;
	}

	//
	//
	// MUTATIONS
	//
	//

	/**
	 * Push one or more elements onto the end of array
	 *
	 * @param mixed $_ values
	 * @return $this
	 */
	public function push() {
		// @TODO php7 $this->append(...$values);
		// das ist doch behindi!
		foreach (func_get_args() as $v) {
			array_push($this->array, $v);
		}
		return $this;
	}

	/**
	 * Append one or more elements onto the end of array
	 *
	 * @param mixed $_ values
	 * @return $this
	 */
	public function append() {
		// das ist doch behindi!
		foreach (func_get_args() as $v) {
			array_push($this->array, $v);
		}
		return $this;
	}

	/**
	 * Adds an element to that array
	 *
	 * @param mixed $element
	 * @param int $index
	 * @return $this
	 */
	public function add($element, $index = null) {
		if ($index === null) {
			$this->array[$this->count()] = $element;
		} else {
			array_splice($this->array, $index, 0, $element);
		}

		return $this;
	}

	/**
	 * Adds all elements to the array
	 *
	 * @param array|\Iterator $array
	 * @return $this
	 */
	public function addAll($array) {
		foreach ($array as $element) {
			$this->add($element);
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
	 * Prepend one or more elements to the beginning of the array
	 *
	 * @param mixed $_ values
	 * @return $this
	 */
	public function unshift() {
		// @TODO php7 $this->prepend(...$values);
		// das ist doch auch behindi!
		foreach (func_get_args() as $v) {
			array_unshift($this->array, $v);
		}
		return $this;
	}

	/**
	 * Prepend one or more elements to the beginning of the array
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
	 * Removes an element from the list
	 *
	 * @param mixed $element
	 * @return $this
	 */
	public function remove($element) {
		$index = array_search($element, $this->array, true);
		if ($index !== null) {
			unset($this->array[$index]);
		}

		return $this;
	}

	/**
	 * Removes all elements from the list
	 *
	 * @param array|\Iterator $array
	 * @return $this
	 */
	public function removeAll($array) {
		foreach ($array as $element) {
			$this->remove($element);
		}

		return $this;
	}

	/**
	 * Remove a portion of the array and replace it with something else
	 *
	 * @param int $offset If offset is positive then the start of removed portion is at that offset from the beginning of the input array. If offset is negative then it starts that far from the end of the input array.
	 * @param int $length If length is omitted, removes everything from offset to the end of the array. If length is specified and is positive, then that many elements will be removed. If length is specified and is negative then the end of the removed portion will be that many elements from the end of the array. If length is specified and is zero, no elements will be removed.
	 * @param array $replacement If replacement array is specified, then the removed elements are replaced with elements from this array. If offset and length are such that nothing is removed, then the elements from the replacement array are inserted in the place specified by the offset. Note that keys in replacement array are not preserved. If replacement is just one element it is not necessary to put array() around it, unless the element is an array itself, an object or NULL.
	 * @return $this
	 */
	public function splice($offset, $length = null, $replacement = []) {
		$length = $length === null ? $this->count() : $length;
		array_splice($this->array, $offset, $length, $replacement);
		return $this;
	}

	//
	//
	// SORTING
	//
	//

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
	 * @param array $array the array on which is operated on
	 * @param Comparator|callable $cmp the compare function
	 * @param callable $usort the sort function for user passed $cmd
	 * @param callable $sort the default sort function
	 */
	protected function doSort(&$array, $cmp, callable $usort, callable $sort) {
		if (is_callable($cmp)) {
			$usort($array, $cmp);
		} else if ($cmp instanceof Comparator) {
			$usort($array, function ($a, $b) use ($cmp) {
				return $cmp->compare($a, $b);
			});
		} else {
			$sort($array);
		}
	}

	/**
	 * Reverses the order of all elements
	 *
	 * @return $this
	 */
	public function reverse() {
		$this->array = array_reverse($this->array);
		return $this;
	}

	//
	//
	// SEARCH
	//
	//

	/**
	 * Tests whether all elements in the array pass the test implemented by the provided function.
	 *
	 * Returns <code>true</code> for an empty array.
	 *
	 * @param callable $callback
	 * @return boolean
	 */
	public function every(callable $callback) {
		$match = true;
		foreach ($this->array as $element) {
			$match = $match && $callback($element);
		}

		return $match;
	}

	/**
	 * Tests whether at least one element in the array passes the test implemented by the provided function.
	 *
	 * Returns <code>false</code> for an empty array.
	 *
	 * @param callable $callback
	 * @return boolean
	 */
	public function some(callable $callback) {
		$match = false;
		foreach ($this->array as $element) {
			$match = $match || $callback($element);
		}

		return $match;
	}

	/**
	 * Searches the array for query using the callback function on each element
	 *
	 * The callback function takes one or two parameters:
	 *
	 *     function ($element [, $query]) {}
	 *
	 * The callback must return a boolean
	 *
	 * @param mixed $query (optional)
	 * @param callable $callback
	 * @return boolean
	 */
	public function search() {
		if (func_num_args() == 1) {
			$callback = func_get_arg(0);
		} else {
			$query = func_get_arg(0);
			$callback = func_get_arg(1);
		}

		foreach ($this->array as $element) {
			$return = func_num_args() == 1 ? $callback($element) : $callback($element, $query);

			if ($return) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Returns the element at the given index (or nothing if the index isn't present)
	 *
	 * @param int $index
	 * @return mixed
	 */
	public function get($index) {
		if (isset($this->array[$index])) {
			return $this->array[$index];
		}
	}

	/**
	 * Searches the array with a given callback and returns the first element if found.
	 *
	 * The callback function takes one or two parameters:
	 *
	 *     function ($element [, $query]) {}
	 *
	 * The callback must return a boolean
	 *
	 * @param mixed $query OPTIONAL the provided query
	 * @param callable $callback the callback function
	 * @return mixed|null the found element or null if it hasn't been found
	 */
	public function find() {
		if (func_num_args() == 1) {
			$callback = func_get_arg(0);
		} else {
			$query = func_get_arg(0);
			$callback = func_get_arg(1);
		}

		foreach ($this->array as $element) {
			$return = func_num_args() == 1 ? $callback($element) : $callback($element, $query);

			if ($return) {
				return $element;
			}
		}

		return null;
	}

	/**
	 * Searches the array with a given callback and returns the last element if found.
	 *
	 * The callback function takes one or two parameters:
	 *
	 *     function ($element [, $query]) {}
	 *
	 * The callback must return a boolean
	 *
	 * @param mixed $query OPTIONAL the provided query
	 * @param callable $callback the callback function
	 * @return mixed|null the found element or null if it hasn't been found
	 */
	public function findLast() {
		if (func_num_args() == 1) {
			$callback = func_get_arg(0);
		} else {
			$query = func_get_arg(0);
			$callback = func_get_arg(1);
		}

		$reverse = array_reverse($this->array, true);
		foreach ($reverse as $element) {
			$return = func_num_args() == 1 ? $callback($element) : $callback($element, $query);

			if ($return) {
				return $element;
			}
		}

		return null;
	}

	/**
	 * Searches the array with a given callback and returns all matching elements.
	 *
	 * The callback function takes one or two parameters:
	 *
	 *     function ($element [, $query]) {}
	 *
	 * The callback must return a boolean
	 *
	 * @param mixed $query OPTIONAL the provided query
	 * @param callable $callback the callback function
	 * @return mixed|null the found element or null if it hasn't been found
	 */
	public function findAll() {
		if (func_num_args() == 1) {
			$callback = func_get_arg(0);
		} else {
			$query = func_get_arg(0);
			$callback = func_get_arg(1);
		}

		$array = [];
		foreach ($this->array as $k => $element) {
			$return = func_num_args() == 1 ? $callback($element) : $callback($element, $query);

			if ($return) {
				$array[$k] = $element;
			}
		}

		return new static($array);
	}

	/**
	 * Returns the index of the given element or false if the element can't be found
	 *
	 * @param mixed $element
	 * @return int the index for the given element
	 */
	public function indexOf($element) {
		return array_search($element, $this->array, true);
	}

	/**
	 * Searches the array with a given callback and returns the index for the first element if found.
	 *
	 * The callback function takes one or two parameters:
	 *
	 *     function ($element [, $query]) {}
	 *
	 * The callback must return a boolean
	 *
	 * @param mixed $query OPTIONAL the provided query
	 * @param callable $callback the callback function
	 * @return int|null the index or null if it hasn't been found
	 */
	public function findIndex() {
		if (func_num_args() == 1) {
			$callback = func_get_arg(0);
		} else {
			$query = func_get_arg(0);
			$callback = func_get_arg(1);
		}

		$index = func_num_args() == 1 ? $this->find($callback) : $this->find($query, $callback);
		if ($index !== null) {
			$index = $this->indexOf($index);
		}

		return $index;
	}

	/**
	 * Searches the array with a given callback and returns the index for the last element if found.
	 *
	 * The callback function takes one or two parameters:
	 *
	 *     function ($element [, $query]) {}
	 *
	 * The callback must return a boolean
	 *
	 * @param mixed $query OPTIONAL the provided query
	 * @param callable $callback the callback function
	 * @return int|null the index or null if it hasn't been found
	 */
	public function findLastIndex() {
		if (func_num_args() == 1) {
			$callback = func_get_arg(0);
		} else {
			$query = func_get_arg(0);
			$callback = func_get_arg(1);
		}

		$index = func_num_args() == 1 ? $this->findLast($callback) : $this->findLast($query, $callback);
		if ($index !== null) {
			$index = $this->indexOf($index);
		}

		return $index;
	}

	/**
	 * Checks whether the given element is in this array
	 *
	 * @param mixed $element
	 * @return boolean
	 */
	public function contains($element) {
		return in_array($element, $this->array, true);
	}

	//
	//
	// SUGAR
	//
	//

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
	 * Extract a slice of the array
	 *
	 * @param int $offset
	 * @param int $length
	 * @param boolean $preserveKeys
	 * @return ArrayObject
	 */
	public function slice($offset, $length = null, $preserveKeys = false) {
		return new ArrayObject(array_slice($this->array, $offset, $length, $preserveKeys));
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
	 * Iterates the array and calls the callback function with the current item as parameter
	 *
	 * @param callable $callback
	 */
	public function each(callable $callback) {
		foreach ($this->array as $item) {
			$callback($item);
		}
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

	//
	//
	// INTERNALS
	//
	//

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