<?php declare(strict_types=1);
/**
 * This file is part of the Phootwork package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT License
 * @copyright Thomas Gossmann
 */

namespace phootwork\lang;

use phootwork\lang\parts\AccessorsPart;
use phootwork\lang\parts\AddAllPart;
use phootwork\lang\parts\AddPart;
use phootwork\lang\parts\EachPart;
use phootwork\lang\parts\IndexFindersPart;
use phootwork\lang\parts\PopPart;
use phootwork\lang\parts\ReducePart;
use phootwork\lang\parts\RemovePart;
use phootwork\lang\parts\ReversePart;
use phootwork\lang\parts\SortAssocPart;

class ArrayObject extends AbstractArray implements \ArrayAccess, \Countable, \IteratorAggregate, \Serializable, Arrayable {

	use AccessorsPart;
	use AddAllPart;
	use AddPart;
	use EachPart;
	use IndexFindersPart;
	use PopPart;
	use ReducePart;
	use RemovePart;
	use ReversePart;
	use SortAssocPart;

	public function __construct(array $contents = []) {
		$this->array = $contents;
	}

	public function getIterator(): \ArrayIterator {
		return new \ArrayIterator($this->array);
	}

	public function serialize(): string {
		return serialize($this->array);
	}

	/**
	 * @psalm-suppress ImplementedReturnTypeMismatch
	 * @psalm-suppress InvalidReturnType The return type should be `void` for consistency with \Serializable interface,
	 *                                   but we want fluid interface.
	 */
	public function unserialize($serialized): self {
		$this->array = unserialize($serialized);

		return $this;
	}

	/**
	 * Resets the array
	 *
	 * @return $this
	 */
	public function clear(): self {
		$this->array = [];
		return $this;
	}

	//
	//
	// MUTATIONS
	//
	//

	/**
	 * Append one or more elements onto the end of array
	 *
	 * @param array $elements
	 * @return $this
	 */
	public function append(...$elements): self {
		// das ist doch behindi!
		foreach ($elements as $element) {
			array_push($this->array, $element);
		}

		return $this;
	}

	/**
	 * Prepend one or more elements to the beginning of the array
	 *
	 * @param array $elements
	 * @return $this
	 */
	public function prepend(...$elements): self {
		// das ist doch auch behindi!
		foreach ($elements as $element) {
			array_unshift($this->array, $element);
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
	 * Remove a portion of the array and replace it with something else
	 *
	 * @param int $offset If offset is positive then the start of removed portion is at that offset from the beginning of the input array. If offset is negative then it starts that far from the end of the input array.
	 * @param int $length If length is omitted, removes everything from offset to the end of the array. If length is specified and is positive, then that many elements will be removed. If length is specified and is negative then the end of the removed portion will be that many elements from the end of the array. If length is specified and is zero, no elements will be removed.
	 * @param array $replacement If replacement array is specified, then the removed elements are replaced with elements from this array. If offset and length are such that nothing is removed, then the elements from the replacement array are inserted in the place specified by the offset. Note that keys in replacement array are not preserved. If replacement is just one element it is not necessary to put array() around it, unless the element is an array itself, an object or NULL.
	 * @return $this
	 */
	public function splice(int $offset, ?int $length = null, array $replacement = []): self {
		$length = $length === null ? $this->count() : $length;
		array_splice($this->array, $offset, $length, $replacement);

		return $this;
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
	public function join(string $glue = ''): Text {
		return new Text(implode($glue, $this->array));
	}

	/**
	 * Extract a slice of the array
	 *
	 * @param int $offset
	 * @param int $length
	 * @param boolean $preserveKeys
	 * @return ArrayObject
	 */
	public function slice(int $offset, ?int $length = null, bool $preserveKeys = false): ArrayObject {
		return new ArrayObject(array_slice($this->array, $offset, $length, $preserveKeys));
	}

	/**
	 * Merges in other values
	 *
	 * @param mixed ... Variable list of arrays to merge.
	 * @return ArrayObject $this
	 */
	public function merge(): self {
		$this->array = array_merge($this->array, func_get_args());
		return $this;
	}

	/**
	 * Returns the keys of the array
	 *
	 * @return ArrayObject the keys
	 */
	public function keys(): ArrayObject {
		return new ArrayObject(array_keys($this->array));
	}

	/**
	 * Returns the values of the array
	 *
	 * @return ArrayObject the values
	 */
	public function values(): ArrayObject {
		return new ArrayObject(array_values($this->array));
	}

	/**
	 * Flips keys and values
	 *
	 * @return ArrayObject $this
	 */
	public function flip(): self {
		$this->array = array_flip($this->array);
		return $this;
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
		return $this->array[$offset] ?? null;
	}
}
